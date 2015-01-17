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
 * @link http://framework.zend.com/manual/1.12/en/zend.controller.front.html Zend_Controller_Front
 * @link http://framework.zend.com/manual/1.12/en/zend.controller.action.html Zend_Controller_Action
 */
abstract class Benri_Controller_Abstract extends Zend_Rest_Controller
{
    /**
     * This means a required resource does not exist.
     */
    const ERROR_MISSING         = 'missing';

    /**
     * This means a required field on a resource has not been set.
     */
    const ERROR_MISSING_FIELD   = 'missing_field';

    /**
     * This means the formatting of a field is invalid. The documentation for
     * that resource should be able to give you more specific information.
     */
    const ERROR_INVALID         = 'invalid';

    /**
     * This means another resource has the same value as this field. This can
     * happen in resources that must have some unique key (such as Label or
     * Locale names).
     */
    const ERROR_ALREADY_EXISTS  = 'already_exists';

    /**
     * This means an uncommon error.
     */
    const ERROR_UNCATEGORIZED   = 'uncategorized';

    /**
     * For the rare case an exception occurred and we couldn't recover.
     */
    const ERROR_UNKNOWN         = 'unknown';

    /**
     * @var array
     */
    protected $_errors = array();

    /**
     * @var array
     */
    protected $_messages = array();

    /**
     * Benri_Controller_Request_Http object wrapping the request environment.
     *
     * @var Benri_Controller_Request_Http
     */
    protected $_request = null;

    /**
     * Zend_Controller_Response_Abstract object wrapping the response.
     *
     * @var Benri_Controller_Response_Http
     */
    protected $_response = null;

    /**
     * Used for deleting resources.
     *
     * @return void
     */
    public function deleteAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);
    }

    /**
     * Disable the view layout.
     *
     * @return Benri_Controller_Action
     */
    protected function disableLayout()
    {
        $this->_helper
            ->layout()
            ->disableLayout();

        return $this;
    }

    /**
     * Used for retrieving resources.
     *
     * @return void
     */
    public function getAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);
    }

    /**
     * Issued against any resource to get just the HTTP header info.
     *
     * @return void
     */
    final public function headAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(204);
    }

    /**
     * Used for retrieving resources.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);
    }

    /**
     * Used for updating resources with partial JSON data.
     *
     * A PATCH request may accept one or more of the attributes to update the
     * resource. PATCH is a relatively new and uncommon HTTP verb, so resource
     * endpoints also accept PUT requests.
     *
     * @return void
     */
    final public function patchAction()
    {
        return $this->putAction();
    }

    /**
     * Used for creating resources, or performing custom actions.
     *
     * @return void
     */
    public function postAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);
    }

    /**
     * Used for replacing resources or collections.
     *
     * For PUT requests with no body attribute, be sure to set the
     * `Content-Length` header to zero.
     *
     * @return void
     */
    public function putAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(405);
    }

    /**
     * Retrieve a plugin or plugins by class.
     *
     * @param string $class
     * @return mixed `false` if no one plugin is loaded,
     *  `Zend_Controller_Plugin_Abstract` if then given $class is registered
     *  as a plugin or `Zend_Controller_Plugin_Abstract[]` if $class is null
     */
    final protected function _getPlugin($class)
    {
        return Zend_Controller_Front::getInstance()
            ->getPlugin($class);
    }

    /**
     * Retrieve all registered plugins.
     *
     * @return array An array of `Zend_Controller_Plugin_Abstract`
     */
    final protected function _getPlugins()
    {
        return Zend_Controller_Front::getInstance()
            ->getPlugins();
    }

    /**
     * Push a message, allowing it to be shown to clients.
     *
     * @return Benri_Controller_Action_Abstract
     */
    protected function _pushMessage($message, $type = 'error', array $interpolateParams = array())
    {
        $this->_messages[] = array(
            'message'   => vsprintf($message, $interpolateParams),
            'type'      => $type
        );

        return $this;
    }

    /**
     * Register a plugin.
     *
     * @param mixed $plugin string or Zend_Controller_Plugin_Abstract
     * @param integer $stackIndex stack index for plugin
     * @return Benri_Controller_Action_Abstract
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

    /**
     * All error objects have field and code properties so that your client
     * can tell what the problem is.
     *
     * If resources have custom validation errors, they should be documented
     * with the resource.
     *
     * @param string $field The erroneous field or column
     * @param string $code One of the ERROR_* codes contants
     * @param string $title A title for this error
     * @param string $message A friendly message
     * @return Benri_Controller_Action_Abstract
     */
    protected function _pushError($resource, $field, $title, $message = '')
    {
        $this->getResponse()
            ->setHttpResponseCode(422);

        $this->_errors[] = array(
            'field'     => $field,
            'message'   => $message,
            'resource'  => $resource,
            'title'     => $title
        );

        return $this;
    }
}
