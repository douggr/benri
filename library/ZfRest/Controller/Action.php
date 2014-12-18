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
abstract class ZfRest_Controller_Action extends ZfRest_Controller_Action_Abstract
{
    /**
     * Layout used by this controller
     * @var string
     */
    protected $_layout = 'default/layout';

    /**
     * @var string
     */
    protected $_mainTemplate;

    /**
     * @var string
     */
    protected $_pageTitle = null;

    /**
     * @var string
     */
    protected $_pjaxTemplate;

    /**
     * {@inheritdoc}
     */
    public function disableLayout()
    {
        $this->_helper
            ->layout()
            ->disableLayout();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function indexAction()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->_helper
            ->layout
            ->setLayout($this->_layout);

        $request = $this->getRequest();
        $action  = $request->getParam('action');

        if (!in_array($action, ['delete', 'index', 'get', 'patch', 'post', 'put'])) {
            if ($request->isGet()) {
                $action = $request->getParam('id') ? 'get' : 'index';
            } else {
                $action = strtolower($request->getMethod());
            }

            $request->setParam('action', $action);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postDispatch()
    {
        $request     = $this->getRequest();
        $contentType = 'application/json';

        if ($this->view instanceof Zend_View_Interface) {
            // allow the programmer to use any partial view located in
            // '/views/scripts/components'.
            $this->view->addScriptPath(APPLICATION_PATH . '/views/scripts/components');

            $contentType = 'text/html';

            $this->view
                ->assign([
                    'controller'    => $this->getParam('controller'),
                    'identity'      => ZfRest_Auth::getInstance()->getIdentity(),
                    'messages'      => $this->_messages,
                    'module'        => $this->getParam('module'),
                    'pageTitle'     => $this->_pageTitle,
                ]);

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
                    $this->view
                        ->assign([
                            'pjaxTemplate' => "{$pjaxTemplate}.phtml",
                        ]);

                    $this->_helper
                        ->viewRenderer($this->_mainTemplate);
                }
            }
        }

        $this->getResponse()
            ->setHeader('Content-Type', "{$contentType}; charset=utf-8");
    }
}
