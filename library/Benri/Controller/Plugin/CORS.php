<?php
/**
 * douggr/benri
 *
 * @license http://opensource.org/license/MIT
 * @link    https://github.com/douggr/benri
 * @version 1.0.0
 */

/**
 * Remove the same origin restriction from API calls.
 *
 * @link http://framework.zend.com/manual/1.12/en/zend.controller.plugins.html Zend_Controller_Plugin_Abstract
 */
class Benri_Controller_Plugin_CORS extends Zend_Controller_Plugin_Abstract
{
    /**
     * Indicates whether a resource can be shared based by returning the value
     * of the Origin request header, "*", or "null" in the response.
     *
     * @var string
     */
    private $_origin = '*';

    /**
     * Indicates whether the response to request can be exposed when the omit
     * credentials flag is unset. When part of the response to a preflight
     * request it indicates that the actual request can include user
     * credentials.
     *
     * @var boolean
     */
    private $_credentials = true;

    /**
     * Indicates, as part of the response to a preflight request, which methods
     * can be used during the actual request.
     *
     * @var array
     */
    private $_methods = array(
        'DELETE',
        'GET',
        'OPTIONS',
        'PATCH',
        'POST',
        'PUT'
    );

    /**
     * Indicates, as part of the response to a preflight request, which header
     * field names can be used during the actual request.
     *
     * @var array
     */
    private $_headers = array(
        'Authorization',
        'Content-Type',
    );

    /**
     * Indicates how long the results of a preflight request can be cached in
     * a preflight result cache.
     *
     * @var integer
     */
    private $_maxAge = 86400;

    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * @internal
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     * @see http://framework.zend.com/manual/1.12/en/zend.controller.request.html Zend_Controller_Request_Abstract
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $methods  = implode(', ', array_unique($this->_methods));
        $headers  = implode(', ', array_unique($this->_headers));

        if ($this->_credentials) {
            header('Access-Control-Allow-Credentials: true', true);
        }

        header("Access-Control-Allow-Origin: {$this->_origin}", true);
        header("Access-Control-Allow-Methods: {$methods}", true);
        header("Access-Control-Allow-Headers: {$headers}", true);
        header("Access-Control-Max-Age: {$this->_maxAge}", true);
        header('X-XSS-Protection: 1; mode=block', true);
        header('X-Frame-Options: SAMEORIGIN', true);
    }

    /**
     * @param boolean $credentials
     * @return Benri_Controller_Plugin_CORS
     */
    public function setCredentials($credentials = true)
    {
        $this->_credentials = (boolean) $credentials;

        return $this;
    }

    /**
     * @param string $origin
     * @return Benri_Controller_Plugin_CORS
     */
    public function setOrigin($origin = '*')
    {
        $this->_origin = $origin;

        return $this;
    }

    /**
     * @param integer $deltaSeconds
     * @return Benri_Controller_Plugin_CORS
     */
    public function setMaxAge($deltaSeconds = 86400)
    {
        $this->_maxAge = $deltaSeconds;

        return $this;
    }

    /**
     * Set a header to use within 'Access-Control-Allow-Headers' header.
     *
     * @param string $name
     * @return Benri_Controller_Plugin_CORS
     */
    public function setHeader($name)
    {
        $this->_headers[] = $name;

        return $this;
    }

    /**
     * Clear the specified header from 'Access-Control-Allow-Headers' index.
     *
     * @param string $name The header to clear
     * @return Benri_Controller_Plugin_CORS
     */
    public function clearHeader($name)
    {
        return $this->unsetFromArray($name, $this->_headers);
    }

    /**
     * Set a method to use within 'Access-Control-Allow-Methods' header.
     *
     * @param string $name
     * @return Benri_Controller_Plugin_CORS
     */
    public function setMethod($name)
    {
        $this->_methods[] = $name;

        return $this;
    }

    /**
     * Clear the specified method from 'Access-Control-Allow-Methods' index.
     *
     * @param string $name
     * @return Benri_Controller_Plugin_CORS
     */
    public function clearMethod($name)
    {
        return $this->unsetFromArray($name, $this->_methods);
    }

    /**
     * 
     */
    private function unsetFromArray($name, array &$array)
    {
        if (!count($array)) {
            return $this;
        }

        foreach ($array as $index => $header) {
            if ($name === $header) {
                unset($array[$index]);
            }
        }

        return $this;
    }
}
