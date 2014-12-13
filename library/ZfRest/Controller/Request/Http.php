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
class ZfRest_Controller_Request_Http extends Zend_Controller_Request_Http
{
    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return strtoupper(parent::getMethod());
    }

    /**
     * {@inheritdoc}
     */
    final public function getCompleteUri()
    {
        return "{$this->getScheme()}://{$this->getHttpHost()}{$this->getRequestUri()}";
    }

    /**
     * {@inheritdoc}
     */
    public function isGet()
    {
        return 'GET' === $this->getMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function isDelete()
    {
        return 'DELETE' === $this->getMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function isHead()
    {
        return 'HEAD' === $this->getMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function isOptions()
    {
        return 'OPTIONS' === $this->getMethod();
    }

    /**
     * Was the request made by PATCH?
     *
     * @return boolean
     */
    public function isPatch()
    {
        return 'PATCH' === $this->getMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function isPost()
    {
        return 'POST' === $this->getMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function isPut()
    {
        return 'PUT' === $this->getMethod();
    }
}
