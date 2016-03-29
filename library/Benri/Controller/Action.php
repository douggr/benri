<?php

/**
 * Used to implement Action Controllers for use with the Front Controller.
 *
 * @link Benri_Controller_Abstract.html Benri_Controller_Abstract
 */
abstract class Benri_Controller_Action extends Benri_Controller_Action_Abstract
{
    /**
     * Layout used by this controller.
     * @var string
     */
    protected $_layout;

    /**
     * A title for an action.
     * @var string
     */
    protected $_title = null;


    /**
     * Initialize object.
     */
    public function init()
    {
        parent::init();

        if ($this->_layout) {
            $this->getHelper('layout')->setLayout($this->_layout);
        }
    }

    /**
     * Post-dispatch routines.
     *
     * Common usages for `postDispatch()` include rendering content in a
     * sitewide template, link url correction, setting headers, etc.
     */
    public function postDispatch()
    {
        if ($this->view instanceof Zend_View_Interface) {
            // Common variables used in all views.
            $this->view->assign([
                'errors'    => $this->_errors,
                'messages'  => $this->_messages,
                'title'     => $this->_title,
            ]);

            // XMLHttpRequest requests should not render the entire layout,
            // only the correct templates related to the action.
            if ($this->getRequest()->isXmlHttpRequest()) {
                $this->getHelper('layout')->disableLayout();
            }

            $this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8', true);
        }
    }
}
