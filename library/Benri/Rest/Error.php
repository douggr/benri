<?php

/**
 * {@inheritdoc}
 */
class Benri_Rest_Error extends Benri_Rest_Controller
{
    /**
     * @internal
     */
    public function errorAction()
    {
        $error      = $this->_getParam('error_handler');
        $field      = implode('/', [
            $error->request->getParam('module'),
            $error->request->getParam('controller'),
            $error->request->getParam('action'),
        ]);

        $this->_pushError(
            $field,
            static::ERROR_UNKNOWN,
            'We\'re having a really bad day.',
            $error->exception->getMessage()
        );
    }
}
