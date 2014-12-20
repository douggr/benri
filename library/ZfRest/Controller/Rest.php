<?php
/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 2.0.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/**
 * {@inheritdoc}
 */
class ZfRest_Controller_Rest extends ZfRest_Controller_Action_Abstract
{
    /// This means a required resource does not exist.
    const ERROR_MISSING         = 'missing';

    /// This means a required field on a resource has not been set.
    const ERROR_MISSING_FIELD   = 'missing_field';

    /// This means the formatting of a field is invalid. The documentation for
    /// that resource should be able to give you more specific information.
    const ERROR_INVALID         = 'invalid';

    /// This means another resource has the same value as this field. This can
    /// happen in resources that must have some unique key (such as Label or
    /// Locale names).
    const ERROR_ALREADY_EXISTS  = 'already_exists';

    /// This means an uncommon error.
    const ERROR_UNCATEGORIZED   = 'uncategorized';

    /// For the rare case an exception occurred and we couldn't recover.
    const ERROR_UNKNOWN         = 'unknown';

    /**
     * Request data
     */
    protected $_input;

    /**
     * Response data
     */
    private $_data;

    /**
     * @var array
     */
    private $_errors = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->_registerPlugin(new ZfRest_Controller_Plugin_CORS());
        $this->_registerPlugin(new Zend_Controller_Plugin_PutHandler());

        $this->_helper
            ->layout()
            ->disableLayout();

        $this->_helper
            ->viewRenderer
            ->setNoRender(true);

        $this->_input   = new StdClass();
        $this->_data    = [
            'messages'  => [],
            'data'      => null
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function postDispatch()
    {
        $this->_data['messages'] = $this->_messages;

        if (count($this->_errors)) {
            $this->_data['errors'] = $this->_errors;
        }

        $pretty = $this->getRequest()
            ->getParam('pretty');

        if (null !== $pretty) {
            $jsonOptions = JSON_NUMERIC_CHECK | JSON_HEX_AMP | JSON_PRETTY_PRINT;
        } else {
            $jsonOptions = JSON_NUMERIC_CHECK | JSON_HEX_AMP;
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->setBody(json_encode($this->_data, $jsonOptions));
    }

    /**
     * {@inheritdoc}
     */
    public function preDispatch()
    {
        Zend_Controller_Front::getInstance()
            ->getPlugin('Zend_Controller_Plugin_ErrorHandler')
            ->setErrorHandlerModule('api');

        $error   = null;
        $request = $this->getRequest();

        // we don't need this good guy no anymore…
        unset($_POST);

        if (!$request->isGet() && !$request->isHead()) {
            // … we read data from the request body.
            $this->_input   = json_decode(file_get_contents('php://input'));

            /// Sending invalid JSON will result in a `400 Bad Request` response.
            if (JSON_ERROR_NONE !== json_last_error()) {
                $this->getResponse()
                    ->setHttpResponseCode(400)
                    ->setHeader('Content-Type', 'text/plain; charset=utf-8')
                    ->setBody(json_last_error_msg())
                    ->sendResponse();

                exit -422;
            }
        }
    }

    /**
     * All error objects have field and code properties so that your client
     * can tell what the problem is.
     *
     * If resources have custom validation errors, they should be documented
     * with the resource.
     *
     * @param string $field The erroneous field or column
     * @param string $code One of the ERROR_* codes contants
     * @param string $message
     * @param array $interpolateParams Params to interpolate within the message
     * @return ZfRest_Controller_Rest
     */
    protected function _pushError($resource, $field, $title, $message = '')
    {
        $this->getResponse()
            ->setHttpResponseCode(422);

        $this->_errors[] = [
            'field'     => $field,
            'message'   => $message,
            'resource'  => $resource,
            'title'     => $title
        ];

        return $this;
    }

    /**
     * General method to save models (ZfRest_Db_Table_Row)
     *
     * @param ZfRest_Db_Table_Row
     * @return ZfRest_Controller_Rest
     */
    protected function _saveModel(ZfRest_Db_Table_Row &$model)
    {
        try {
            $model->normalizeInput($this->_input)
                ->save();

            $this->_pushMessage('Horray!', 'success');
        } catch (Zend_Db_Table_Row_Exception $ex) {
            foreach ($model->getErrors() as $error) {
                extract($error) && $this->_pushError($resource, $field, $title, $message);
            }

            $this->_pushMessage($ex->getMessage(), 'danger');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _setResponseData($data)
    {
        $this->_data['data'] = $data;

        return $this;
    }
}
