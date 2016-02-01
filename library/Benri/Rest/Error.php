<?php

/**
 * {@inheritdoc}
 */
class Benri_Rest_Error extends Benri_Rest_Controller
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this
            ->disableLayout()
            ->getHelper('viewRenderer')
            ->setNoRender();
    }

    /**
     * {@inheritdoc}
     */
    public function preDispatch()
    {
        return $this;
    }

    /**
     * @internal
     */
    public function errorAction()
    {
        $error      = $this->getParam('error_handler');
        $field      = implode('/', [
            $error->request->getParam('module'),
            $error->request->getParam('controller'),
            $error->request->getParam('action'),
        ]);

        $this->_pushError(
            $field,
            $error->type,
            $error->exception->getMessage()
        );

        switch ($error->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                return $this->getResponse()->setHttpResponseCode(404);
        }

        $this->getResponse()->setHttpResponseCode(500);
    }
}
