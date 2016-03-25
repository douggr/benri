<?php

/**
 * Used to implement Action Controllers for use with the Front Controller.
 *
 * @link http://framework.zend.com/manual/1.12/en/zend.controller.front.html Zend_Controller_Front
 * @link http://framework.zend.com/manual/1.12/en/zend.controller.action.html Zend_Controller_Action
 */
abstract class Benri_Controller_Action_Abstract extends Zend_Rest_Controller
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
     * Force the request action parameter.
     */
    public function init()
    {
        $request = $this->getRequest();

        // limit actions to HTTP common verbs
        if ($request->isGet()) {
            $action = $this->getParam('id') ? 'get' : 'index';
        } else {
            $action = $this->getParam('x-method', $request->getMethod());
        }

        $request
            ->setActionName($action)
            ->setDispatched(false)
            ->setParam('action', $action);
    }

    /**
     * Used for deleting resources.
     */
    public function deleteAction()
    {
        throw new Zend_Controller_Exception('Method Not Allowed', 405);
    }

    /**
     * Used for retrieving resources.
     */
    public function getAction()
    {
        throw new Zend_Controller_Exception('Method Not Allowed', 405);
    }

    /**
     * Issued against any resource to get just the HTTP header info.
     */
    final public function headAction()
    {
        throw new Zend_Controller_Exception('No Content', 204);
    }

    /**
     * Used for retrieving resources.
     */
    public function indexAction()
    {
        throw new Zend_Controller_Exception('Method Not Allowed', 405);
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
        throw new Zend_Controller_Exception('Method Not Allowed', 405);
    }

    /**
     * Used for replacing resources or collections.
     *
     * @NOTE For PUT requests with no body attribute, be sure to set the
     * `Content-Length` header to zero.
     */
    public function putAction()
    {
        throw new Zend_Controller_Exception('Method Not Allowed', 405);
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
     * @return self
     * @throws Zend_Db_Table_Row_Exception
     */
    protected function _saveModel(Benri_Db_Table_Row &$model, $data = null)
    {
        try {
            $model->normalizeInput($data)->save();
        } catch (Zend_Db_Table_Row_Exception $ex) {
            foreach ($model->getErrors() as $error) {
                $this->_pushError(
                    $error['field'],
                    $error['code'],
                    $error['title'],
                    $error['message']
                );
            }

            throw $ex;
        }

        return $this;
    }
}
