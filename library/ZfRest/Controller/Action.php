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
abstract class ZfRest_Controller_Action extends ZfRest_Controller_Abstract
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
    }

    /**
     * {@inheritdoc}
     */
    public function postDispatch()
    {
        $request     = $this->getRequest();
        $contentType = 'application/json';

        if ($this->view instanceof Zend_View_Interface) {
            $contentType      = 'text/html';
            $isXmlHttpRequest = $request->isXmlHttpRequest();

            if ($isXmlHttpRequest) {
                $this->_helper
                    ->layout
                    ->disableLayout();

                if (!$request->isPjaxRequest()) {
                    $this->view
                        ->assign([
                            'pjaxTemplate' => $this->getViewScript(),
                        ]);

                    $this->_helper
                        ->ViewRenderer
                        ->setNoController(true);

                    $main = "{$request->getParam('controller')}/{$request->getParam('action')}";
                    $this->_helper
                        ->viewRenderer($this->_mainTemplate ?: $main);
                }
            } else {
                $this->_helper
                    ->layout
                    ->setLayout($this->_layout);
            }

            $this->view
                ->assign([
                    'messages'  => $this->_messages,
                    'title'     => $this->_pageTitle,
                ]);
        }

        $this->getResponse()
            ->setHeader('Content-Type', "{$contentType}; charset=utf-8");
    }
}
