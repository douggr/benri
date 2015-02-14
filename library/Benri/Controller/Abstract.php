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
     * @var bool
     */
    private $_layoutEnabled = true;

    /**
     * Class constructor
     *
     * The request and response objects should be registered with the
     * controller, as should be any additional optional arguments; these will be
     * available via {@link getRequest()}, {@link getResponse()}, and
     * {@link getInvokeArgs()}, respectively.
     *
     * When overriding the constructor, please consider this usage as a best
     * practice and ensure that each is registered appropriately; the easiest
     * way to do so is to simply call parent::__construct($request, $response,
     * $invokeArgs).
     *
     * After the request, response, and invokeArgs are set, the
     * {@link $_helper helper broker} is initialized.
     *
     * Finally, {@link init()} is called as the final action of
     * instantiation, and may be safely overridden to perform initialization
     * tasks; as a general rule, override {@link init()} instead of the
     * constructor to customize an action controller's instantiation.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs Any additional invocation arguments
     * @return void
     */
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->_layoutEnabled = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getPluginResource('layout');

        if (!$this->_layoutEnabled) {
            Zend_Controller_Front::getInstance()
                ->setParam('noViewRenderer', true);;
        }

        parent::__construct($request, $response, $invokeArgs);
    }

    /**
     * Used for deleting resources.
     *
     * @return void
     */
    public function deleteAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(404);
    }

    /**
     * Disable the view layout.
     *
     * @return Benri_Controller_Action
     */
    protected function disableLayout()
    {
        if ($this->_layoutEnabled()) {
            $this->_helper
                ->layout()
                ->disableLayout();
        }

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
            ->setHttpResponseCode(404);
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
            ->setHttpResponseCode(404);
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
            ->setHttpResponseCode(404);
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
            ->setHttpResponseCode(404);
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
     * @param string $message
     * @param string $type Type of the message (info, warning, error, etc)
     * @param array $interpolateParams Params to interpolate within $message
     * @return Benri_Controller_Abstract
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
     * @param int $stackIndex stack index for plugin
     * @return Benri_Controller_Abstract
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
     * @return Benri_Controller_Abstract
     */
    protected function _pushError($resource, $code, $title, $message = '')
    {
        $this->_errors[] = array(
            'code'      => $code,
            'message'   => $message,
            'resource'  => $resource,
            'title'     => $title
        );

        return $this;
    }

    /**
     * @return bool
     */
    final public function _layoutEnabled()
    {
        return $this->_layoutEnabled;
    }
}
