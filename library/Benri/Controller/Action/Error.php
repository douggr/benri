<?php

/**
 * Provides a plugin for handling exceptions thrown by the application,
 * including those resulting from missing controllers or actions.
 *
 * @link http://framework.zend.com/manual/1.12/en/zend.controller.plugins.html#zend.controller.plugins.standard.errorhandler Zend_Controller_Plugin_ErrorHandler
 */
class Benri_Controller_Action_Error extends Benri_Controller_Action_Abstract
{
    /**
     * @internal
     */
    public function errorAction()
    {
    }

    /**
     * @internal
     */
    public function notFoundAction()
    {
    }

    /**
     * @internal
     */
    public function init()
    {
    }

    /**
     * @internal
     */
    public function postDispatch()
    {
        $this->_report();

        if ($this->getRequest()->isXMLHttpRequest()) {
            $this->getHelper('json')->sendJson([
                'data'      => null,
                'errors'    => $this->_errors,
                'messages'  => $this->_messages,
            ]);
        }

        if ($error = $this->getParam('error_handler')) {
            $this->view->assign([
                'exception' => $error->exception,
                'request'   => $error->request,
            ])
        }

        $this->view->assign([
            'errors'    => $this->_errors,
            'messages'  => $this->_messages,
        ]);

        $this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * @internal
     */
    public function preDispatch()
    {
        $this->getResponse()->setHttpResponseCode(500);

        if (!$error = $this->getParam('error_handler')) {
            return;
        }

        $code     = $error->exception->getCode();
        $callback = Benri_Util_String
            ::camelize($this->getResponse()->getMessageFromCode($code));

        $this->_pushError(null, $code, $error->exception->getMessage());

        if ($callback) {
            $this->getResponse()->setHttpResponseCode($code);

            if (method_exists($this, "{$callback}Action")) {
                return $this->forward($callback);
            }
        }

        switch ($error->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getRequest()->setActionName('not-found');
                $this->getResponse()->setHttpResponseCode(404);
                break;
        }
    }

    /**
     * @internal
     */
    protected function _report()
    {
    }
}
