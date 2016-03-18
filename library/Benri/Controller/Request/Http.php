<?php

/**
 * HTTP request object for use with Benri_Controller family.
 *
 * @link http://framework.zend.com/manual/1.12/en/zend.controller.request.html Zend_Controller_Request_Http
 */
class Benri_Controller_Request_Http extends Zend_Controller_Request_Http
{
    /**
     * Retrieve a parameter.
     *
     * Retrieves a parameter from the instance. Priority is in the order of
     * userland parameters (see {@link setParam()}), $_GET, $_POST. If a
     * parameter matching the $key is not found, null is returned.
     *
     * If the $key is an alias, the actual key aliased will be used.
     *
     * @param mixed $key
     * @param mixed $default Default value to use if key not found
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        $param = parent::getParam($key, $default);

        if (is_string($param)) {
            return trim($param);
        }

        return $param;
    }

    /**
     * Returns the REQUEST_METHOD header.
     *
     * @return string
     */
    public function getMethod()
    {
        return strtoupper(parent::getMethod());
    }

    /**
     * Returns the URL which this request was made to.
     *
     * @return string
     */
    final public function getCompleteUri()
    {
        return "{$this->getScheme()}://{$this->getHttpHost()}{$this->getRequestUri()}";
    }

    /**
     * Was the request made by GET?
     *
     * @return bool
     */
    public function isGet()
    {
        return 'GET' === $this->getMethod();
    }

    /**
     * Was the request made by DELETE?
     *
     * @return bool
     */
    public function isDelete()
    {
        return 'DELETE' === $this->getMethod();
    }

    /**
     * Was the request made by HEAD?
     *
     * @return bool
     */
    public function isHead()
    {
        return 'HEAD' === $this->getMethod();
    }

    /**
     * Was the request made by OPTIONS?
     *
     * @return bool
     */
    public function isOptions()
    {
        return 'OPTIONS' === $this->getMethod();
    }

    /**
     * Was the request made by PATCH?
     *
     * @return bool
     */
    public function isPatch()
    {
        return 'PATCH' === $this->getMethod();
    }

    /**
     * Is the request a Javascript XMLHttpRequest and has the PJAX header?
     *
     * Should work with jQuery, Prototype, possibly others.
     *
     * @return bool
     */
    public function isPjaxRequest()
    {
        return $this->isXmlHttpRequest() && $this->getHeader('X-PJAX');
    }

    /**
     * Was the request made by POST?
     *
     * @return bool
     */
    public function isPost()
    {
        return 'POST' === $this->getMethod();
    }

    /**
     * Was the request made by PUT?
     *
     * @return bool
     */
    public function isPut()
    {
        return 'PUT' === $this->getMethod();
    }
}
