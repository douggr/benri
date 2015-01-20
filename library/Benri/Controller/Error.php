<?php
/**
 * douggr/benri
 *
 * @license http://opensource.org/license/MIT
 * @link    https://github.com/douggr/benri
 * @version 1.0.0
 */

/**
 * Error handler.
 
 * @link Benri_Controller_Abstract.html Benri_Controller_Abstract
 */
class Error extends Benri_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                break;
        }

        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log('Exception data', $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $errors->request->getParams());
        }

        if ($this->isXmlHttpRequest()) {
            // This will match the response data just like in
            // Benri_Rest_Controller
            $response = array(
                'data'      => null,
                'errors'    => null,
                'messages'  => $errors->exception->getMessage(),
            );

            $this->getResponse()
                ->setHeader('Content-Type', 'application/json; charset=utf-8')
                ->setBody(json_encode($response, JSON_NUMERIC_CHECK | JSON_HEX_AMP));

        } else {
            if ($this->getInvokeArg('displayExceptions') == true) {
                // Some huuuuuuge objects
                $this->view->exception = $errors->exception;
                $this->view->request   = $errors->request;
            }

            $this->view->code    = $errors->exception->getCode();
            $this->view->message = $errors->exception->getMessage();

            $this->getResponse()
                ->setHeader('Content-Type', 'text/html; charset=utf-8');
        }
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');

        if (!$bootstrap->hasResource('Log')) {
            return false;
        }

        return $bootstrap->getResource('Log');
    }
}
