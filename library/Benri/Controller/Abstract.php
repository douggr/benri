<?php

/**
 * Used to implement Action Controllers for use with the Front Controller.
 *
 * @link http://framework.zend.com/manual/1.12/en/zend.controller.front.html Zend_Controller_Front
 * @link http://framework.zend.com/manual/1.12/en/zend.controller.action.html Zend_Controller_Action
 */
abstract class Benri_Controller_Abstract extends Zend_Rest_Controller
{
    /**
     * @var array
     */
    protected $_errors = [];

    /**
     * @var array
     */
    protected $_messages = [];

    /**
     * Benri_Controller_Request_Http object wrapping the request environment.
     *
     * @var Benri_Controller_Request_Http
     */
    protected $_request = null;

    /**
     * Zend_Controller_Response_Abstract object wrapping the response.
     *
     * @var Benri_Controller_Response_Http
     */
    protected $_response = null;

    /**
     * Force the request action parameter.
     *
     * @see https://github.com/douggr/benri/issues/10
     */
    public function init()
    {
        $request = $this->getRequest();
        $action  = $request->getParam('action');

        // limit Actions to HTTP common verbs
        if (!in_array($action, ['delete', 'get', 'patch', 'post', 'put'], true)) {
            if ($request->isGet()) {
                $action = $request->getParam('id') ? 'get' : 'index';
            } else {
                $action = strtolower($request->getMethod());
            }

            $request->setParam('action', $action);
        }
    }

    /**
     * Used for deleting resources.
     */
    public function deleteAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(404);
    }

    /**
     * Disable the view layout.
     *
     * @return Benri_Controller_Action
     */
    protected function disableLayout()
    {
        $this->_helper
            ->layout()
            ->disableLayout();

        return $this;
    }

    /**
     * Used for retrieving resources.
     */
    public function getAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(404);
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
            ->setHttpResponseCode(404);
    }

    /**
     * Used for updating resources with partial JSON data.
     *
     * A PATCH request may accept one or more of the attributes to update the
     * resource. PATCH is a relatively new and uncommon HTTP verb, so resource
     * endpoints also accept PUT requests.
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
            ->setHttpResponseCode(404);
    }

    /**
     * Used for replacing resources or collections.
     *
     * @NOTE For PUT requests with no body attribute, be sure to set the
     * `Content-Length` header to zero.
     */
    public function putAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(404);
    }

    /**
     * Push a message, allowing it to be shown to clients.
     *
     * @param string $message
     * @param string $type Type of the message (info, warning, error, etc)
     * @param array $interpolateParams Params to interpolate within $message
     * @return self
     */
    protected function _pushMessage($message, $type = 'error', array $interpolateParams = [])
    {
        $this->_messages[] = [
            'message'   => vsprintf($message, $interpolateParams),
            'type'      => $type,
        ];

        return $this;
    }

    /**
     * All error objects have field and code properties so that your client
     * can tell what the problem is.
     *
     * If resources have custom validation errors, they should be documented
     * with the resource.
     *
     * @param string $resource The erroneous resource
     * @param string $code
     * @param string $title A title for the error
     * @param string $message A friendly message
     * @return self
     */
    protected function _pushError($resource, $code, $title, $message = '')
    {
        $this->_errors[] = [
            'code'      => $code,
            'message'   => $message,
            'resource'  => $resource,
            'title'     => $title,
        ];

        return $this;
    }

    /**
     * General method to save models (Benri_Db_Table_Row).
     *
     * @param Benri_Db_Table_Row
     * @param mixed Data to normalize and save into the model
     * @return Benri_Controller_Rest
     * @throws Zend_Db_Table_Row_Exception
     */
    protected function _saveModel(Benri_Db_Table_Row &$model, $data = null)
    {
        try {
            $model->normalizeInput($data)
                ->save();
        } catch (Zend_Db_Table_Row_Exception $ex) {
            foreach ($model->getErrors() as $error) {
                $this->_pushError(
                    $resource = $error['field'],
                    $code     = $error['code'],
                    $title    = $error['title'],
                    $message  = $error['message']
                );
            }

            throw $ex;
        }

        return $this;
    }
}
