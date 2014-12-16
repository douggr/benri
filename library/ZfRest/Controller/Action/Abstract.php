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
abstract class ZfRest_Controller_Action_Abstract extends Zend_Rest_Controller
{
    /**
     * {@inheritdoc}
     */
    protected $_messages = [];

    /**
     * Used for deleting resources.
     */
    public function deleteAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);
    }

    /**
     * Used for retrieving resources.
     */
    public function getAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);
    }

    /**
     * Issued against any resource to get just the HTTP header info.
     */
    final public function headAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(204);
    }

    /**
     * Used for retrieving resources.
     */
    public function indexAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);
    }

    /**
     * Used for updating resources with partial JSON data. A PATCH request may
     * accept one or more of the attributes to update the resource. PATCH is
     * a relatively new and uncommon HTTP verb, so resource endpoints also
     * accept PUT requests.
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
        $this->getResponse()
            ->setHttpResponseCode(405);
    }

    /**
     * Used for replacing resources or collections. For PUT requests with no
     * body attribute, be sure to set the Content-Length header to zero.
     */
    public function putAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);
    }

    /**
     * Retrieve a plugin or plugins by class.
     *
     * @param  string $class
     * @return false|Zend_Controller_Plugin_Abstract|array
     */
    final protected function _getPlugin($class)
    {
        return Zend_Controller_Front::getInstance()
            ->getPlugin($class);
    }

    /**
     * Retrieve all registered plugins.
     *
     * @return array
     */
    final protected function _getPlugins()
    {
        return Zend_Controller_Front::getInstance()
            ->getPlugins();
    }

    /**
     * {@inheritdoc}
     */
    final protected function _pushMessage($message, $type = 'error', array $interpolateParams = [])
    {
        $this->_messages[] = [
            'message'   => vsprintf($message, $interpolateParams),
            'type'      => $type
        ];

        return $this;
    }

    /**
     * Register a plugin.
     *
     * @param  string|Zend_Controller_Plugin_Abstract $plugin
     * @param  int $stackIndex stack index for plugin
     * @return ZfRest_Controller_Action
     */
    final protected function _registerPlugin($plugin, $stackIndex = null)
    {
        if (is_string($plugin)) {
            $plugin = new $plugin;
        }

        Zend_Controller_Front::getInstance()
            ->registerPlugin($plugin, $stackIndex);

        return $this;
    }
}
