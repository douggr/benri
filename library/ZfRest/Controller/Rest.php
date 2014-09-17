<?php
/*
 * base/zf-rest
 *
 * @link https://svn.locness.com.br/svn/base/trunk/zf-rest for the canonical source repository
 * @version 1.0.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/**
 * {@inheritdoc}
 *
 * All API requests MUST include a valid User-Agent header. Requests with no
 * User-Agent header will be rejected.
 */
class ZfRest_Controller_Rest extends Zend_Rest_Controller
{
    use ZfRest_Controller_Auth;

    /**
     * Request data
     */
    protected $input;

    /**
     * Response data
     */
    protected $data;

    /**
     * {@inheritdoc}
     */
    private $_errors = [];

    /**
     * @var integer
     */
    private static $_forwardStack = 0;

    /**
     * @var integer
     */
    private static $_parents = -1;

    /**
     * Used for deleting resources.
     */
    public function deleteAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);
    }

    /**
     * Used for retrieving resources.
     */
    public function getAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);
    }

    /**
     * Issued against any resource to get just the HTTP header info.
     */
    final public function headAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(204);
    }

    /**
     * Used for retrieving resources.
     */
    public function indexAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);
    }

    /**
     * {@inheritdoc}
     */
    final public function init()
    {
        $this
            ->getResponse()
            // {{{ BEGIN CORS
            ->setHeader('Access-Control-Allow-Origin', '*', true)
            ->setHeader('Access-Control-Allow-Credentials', 'true', true)
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE', true)
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Preferred-Locale, X-Context, Authorization', true)
            // END CORS }}}

            ->setHeader('Access-Control-Max-Age', '1728000', true)
            ->setHeader('X-Preferred-Locale', $this->getPreferredLocale(), true)
            ->setHeader('X-Context', $this->getContext(), true)
            ->setHeader('Vary', 'Accept-Encoding', true)
            ->setHeader('Content-Type', 'application/json; charset=utf-8', true);

        $this->setAuth($this->getRequest()->getHeader('Authorization'))
            ->setContext($this->getContext());

        ZfRest_Db_Table::setAuthUser($this->getCurrentUser());
        ZfRest_Db_Table::setContext($this->getContext());
        ZfRest_Db_Table::setPreferredLocale($this->getPreferredLocale());

        $this
            ->_helper
            ->viewRenderer
            ->setNoRender(true);

        $this->input = new \StdClass();
    }

    /**
     * Used for updating resources with partial JSON data. A PATCH request may
     * accept one or more of the attributes to update the resource. PATCH is
     * a relatively new and uncommon HTTP verb, so resource endpoints also
     * accept PUT requests.
     */
    final public function patchAction()
    {
        return $this->putAction();
    }

    /**
     * Used for creating resources, or performing custom actions.
     */
    public function postAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);

    }

    /**
     * {@inheritdoc}
     */
    public function postDispatch()
    {
        if (0 !== sizeof($this->_errors)) {
            $this->data = ['errors' => []];

            foreach ($this->_errors as $error) {
                $message = $error['message'];
                unset($error['message']);

                $this->data['errors'][] = (object) [
                    'message'   => $message,
                    'details'   => $error
                ];
            }

            $this->getResponse()
                ->setHttpResponseCode(422);
        }

        if (null !== $this->data) {
            $pretty = $this->getRequest()
                ->getParam('pretty');

            if (null !== $pretty) {
                $jsonOptions = JSON_NUMERIC_CHECK | JSON_HEX_AMP | JSON_PRETTY_PRINT;
            } else {
                $jsonOptions = JSON_NUMERIC_CHECK | JSON_HEX_AMP;
            }

            $this->data = json_encode($this->data, $jsonOptions);
        } else {
            $this->getResponse()
                ->setHttpResponseCode(404);
        }

        return $this->getResponse()
            ->setBody($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function preDispatch()
    {
        $this->validateRequest();

        $request = $this->getRequest();

        if ($request->isOptions()) {
            $this->_skipAction(204);
        }

        // we don't need this good guy no anymore…
        unset($_POST);

        if (!$request->isGet() && !$request->isHead()) {

            // … so we read data from the request body.
            $this->input = json_decode(file_get_contents('php://input'));
            $jsonError = json_last_error();

            if (JSON_ERROR_NONE !== $jsonError) {
                switch ($jsonError) {
                    case JSON_ERROR_DEPTH:
                        $message = 'ERR.JSON_DEPTH';
                        break;

                    case JSON_ERROR_STATE_MISMATCH:
                        $message = 'ERR.JSON_STATE_MISMATCH';
                        break;

                    case JSON_ERROR_CTRL_CHAR:
                        $message = 'ERR.JSON_CTRL_CHAR';
                        break;

                    case JSON_ERROR_SYNTAX:
                        $message = 'ERR.JSON_SYNTAX';
                        break;

                    case JSON_ERROR_UTF8:
                        $message = 'ERR.JSON_UTF8';
                        break;

                    case JSON_ERROR_RECURSION:
                        $message = 'ERR.JSON_RECURSION';
                        break;

                    case JSON_ERROR_INF_OR_NAN:
                        $message = 'ERR.JSON_INF_OR_NAN';
                        break;

                    case JSON_ERROR_UNSUPPORTED_TYPE:
                        $message = 'ERR.JSON_UNSUPPORTED_TYPE';
                        break;
                }

                $this->_skipAction(400, $message);
            }
        }

        $needIdToContinue = !!($request->isPut() || $request->isPatch() || $request->isDelete());

        if ($needIdToContinue && (!isset($this->input->id) && !$request->getParam('id'))) {
            return $this->_skipAction(422, 'ERR.ID_REQUIRED');
        }

        // emulates hierarquical inheritance :)
        // /:controller/:id/another-controller/:another-id/:anot...
        // {{{
        $action     = $request->getParam('action');
        $controller = sprintf('children-%02d', self::$_forwardStack++);

        while ($forwardingController = $request->getParam($controller)) {
            if (!$request->isGet() || $request->getParam("{$controller}-id")) {
                $action = strtolower($request->getMethod());
            }

            self::$_parents++;
            $request->setParam($controller, null);

            $this->forward(
                $action,
                $forwardingController,
                'v1',
                $request->getParams()
            );
        }
        // }}}
    }

    /**
     * Tells the application which of the registered translation tables to use
     * for translation at initial startup.
     *
     * @return string
     */
    final public function getPreferredLocale()
    {
        $locale = $this->getRequest()
            ->getHeader('X-Preferred-Locale');

        if (!$locale) {
            $locale = Zend_Registry::get('Zend_Locale');
        }

        return str_replace('-', '_', $locale);
    }

    /**
     * 
     */
    final protected function getContext()
    {
        return $this->getRequest()
            ->getHeader('X-Context') ?: 1;
    }

    /**
     * Used for replacing resources or collections. For PUT requests with no
     * body attribute, be sure to set the Content-Length header to zero.
     */
    public function putAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);

    }

    /**
     * All error objects have field and code properties so that your client
     * can tell what the problem is. These are the possible validation error
     * codes:
     *  - missing: This means a resource does not exist.
     *  - missing_field: This means a required field on a resource has not
     *      been set.
     *  - invalid: This means the formatting of a field is invalid. The
     *      documentation for that resource should be able to give you more
     *      specific information.
     *  - already_exists: This means another resource has the same value as
     *      this field. This can happen in resources that must have some unique
     *      key (such as Label or Locale names).
     *  - uncategorized: This means an uncommon error.
     *  - unknown: For the rare case an exception occurred and we couldn't
     *      recover.
     *
     * If resources have custom validation errors, they will be documented
     * with the resource.
     */
    protected function pushError($field, $code, $message = '', array $interpolateParams = [])
    {
        $message = $this->_($message, $interpolateParams);

        $this->_errors[] = [
            'field'     => $field,
            'code'      => $code,
            'message'   => $message,
        ];
    }

    /**
     * There are three possible types of client errors on API calls that
     * receive request bodies:
     *
     *  - Sending invalid JSON will result in a 400 Bad Request
     *      response (@see preDispatch()).
     *  - Requests with no User-Agent header will result in a 400 Bad Request
     *      response.
     *  - Sending invalid fields will result in a 422 Unprocessable Entity
     *      response (per controller).
     */
    protected function validateRequest()
    {
        $hasUserAgent = $this->getRequest()
            ->getHeader('User-Agent');

        if (!$hasUserAgent) {
            $this->_skipAction(400, 'ERR.USER_AGENT_REQUIRED');
        }
    }

    /**
     * Translate the given message
     *
     * @return string
     */
    final protected function _($message, array $params = [])
    {
        $message = Zend_Registry::get('Zend_Translate')
            ->_($message, $this->getPreferredLocale());

        return vsprintf($message, $params);
    }

    /**
     * Don't execute the action, sending the response before.
     *
     * @exit
     */
    final protected function _skipAction($code, $message = null)
    {
        if (is_string($message)) {
            $message = (object) [
                'message'   => $this->_($message),
                'code'      => 0,
                'details'   => []
            ];
        }

        $this
            ->getResponse()
            ->setHttpResponseCode($code)
            ->setBody(json_encode($message))
            ->sendResponse();

        // As we gather here today, we bid farewell…
        exit -$code;
    }

    /**
     * Returns an object containing the page size and current page used in
     * lists.
     *
     * @return StdClass
     */
    final protected function _getPageSize()
    {
        $defaults = [
            'defaultPageSize' => 20,
            'maxPageSize'     => 1000
        ];

        $paginationConfig = $this->getInvokeArg('bootstrap')
            ->getOption('pagination');

        if (!$paginationConfig) {
            $paginationConfig = $defaults;
        } else {
            $paginationConfig = array_replace($defaults, $paginationConfig);
        }

        $pageSize    = $this->getRequest()->getParam('limit') ?: $paginationConfig['defaultPageSize'];
        $currentPage = $this->getRequest()->getParam('page')  ?: 1;

        if ($pageSize > $paginationConfig['maxPageSize']) {
            $pageSize = $paginationConfig['maxPageSize'];
        }

        return (object) [
            'pageSize'      => $pageSize,
            'currentPage'   => $currentPage,
        ];
    }

    /**
     * Allows pre-save logic to be applied to models.
     */
    protected function _saveModel($model)
    {
        $model->normalizeInput($this->input);

        try {
            $model->save();
            $this->data = $model->toArray();

        } catch (ZfRest_Db_Exception $e) {
            $errors = $model->getErrors();

            if (false !== $errors) {
                foreach ($errors as $error) {
                    call_user_func_array([$this, 'pushError'], $error);
                }
            }

        } catch (Exception $e) {
            $this->pushError('general', 'unknown', $e->getMessage());

        } finally {
            // log…
        }
    }
}
