<?php

/**
 * {@inheritdoc}
 */
class Benri_Rest_Controller_Action_Error extends Benri_Rest_Controller_Action_Error
{
    /**
     * @internal
     */
    public function postDispatch()
    {
        $this->_report();

        $this->getHelper('json')->sendJson([
            'data'      => null,
            'errors'    => $this->_errors,
            'messages'  => $this->_messages,
        ]);
    }
}
