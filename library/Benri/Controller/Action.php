<?php
/**
 * douggr/benri
 *
 * @license http://opensource.org/license/MIT
 * @link    https://github.com/douggr/benri
 * @version 1.0.0
 */

/**
 * Used to implement Action Controllers for use with the Front Controller.
 *
 * @link Benri_Controller_Abstract.html Benri_Controller_Abstract
 */
abstract class Benri_Controller_Action extends Benri_Controller_Abstract
{
    /**
     * Layout used by this controller.
     *
     * @var string
     */
    protected $_layout = 'default/layout';

    /**
     * Used to override default templates. If this is set, the controller will
     * ignore controller template and use $_mainTemplate.
     *
     * @var string
     */
    protected $_mainTemplate;

    /**
     * A title for an action.
     *
     * @var string
     */
    protected $_pageTitle = null;

    /**
     * Used to override default templates. If this is set, the controller will
     * ignore controller template and use $_mainTemplate together with
     * $_pjaxTemplate.
     *
     * @var string
     */
    protected $_pjaxTemplate;

    /**
     * Used as the index page.
     *
     * @return void
     */
    public function indexAction()
    {
    }

    /**
     * Initialize object.
     *
     * @return void
     */
    public function init()
    {
        $this->_helper
            ->layout
            ->setLayout($this->_layout);

        $request = $this->getRequest();
        $action  = $request->getParam('action');

        if (!in_array($action, array('delete', 'index', 'get', 'patch', 'post', 'put'))) {
            if ($request->isGet()) {
                $action = $request->getParam('id') ? 'get' : 'index';
            } else {
                $action = strtolower($request->getMethod());
            }

            $request->setParam('action', $action);
        }
    }

    /**
     * Post-dispatch routines.
     *
     * Common usages for `postDispatch()` include rendering content in a
     * sitewide template, link url correction, setting headers, etc.
     *
     * @return void
     */
    public function postDispatch()
    {
        $request     = $this->getRequest();
        $contentType = 'application/json';

        if ($this->view instanceof Zend_View_Interface) {
            // allow the programmer to use any partial view located in
            // '/views/scripts/components'.
            $this->view
                ->addScriptPath(APPLICATION_PATH . '/views/scripts/components');

            $contentType = 'text/html';

            // Common variables used in all views.
            $this->view
                ->assign(array(
                    'controller'    => $this->getParam('controller'),
                    'identity'      => Benri_Auth::getInstance()->getIdentity(),
                    'messages'      => $this->_messages,
                    'module'        => $this->getParam('module'),
                    'now'           => new Benri_Util_DateTime(),
                    'pageTitle'     => $this->_pageTitle
                ));

            // XMLHttpRequest requests should not render the entire layout,
            // only the correct templates related to the action.
            if ($request->isXmlHttpRequest()) {
                $this->disableLayout();
            }

            if ($this->_mainTemplate) {
                $this->_helper
                    ->ViewRenderer
                    ->setNoController(true);

                $pjaxTemplate = "{$this->getParam('controller')}/{$this->getParam('action')}";

                if ($request->isPjaxRequest()) {
                    $this->_helper
                        ->viewRenderer($pjaxTemplate);
                    
                } else {
                    $this->view->pjaxTemplate = "{$pjaxTemplate}.phtml";

                    $this->_helper
                        ->viewRenderer($this->_mainTemplate);
                }
            }
        }

        $this->getResponse()
            ->setHeader('Content-Type', "{$contentType}; charset=utf-8");
    }
}
