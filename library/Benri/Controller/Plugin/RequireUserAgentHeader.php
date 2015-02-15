<?php
/**
 * Validates the presence of the User-Agent header.
 *
 * @link http://framework.zend.com/manual/1.12/en/zend.controller.plugins.html Zend_Controller_Plugin_Abstract
 */
class Benri_Controller_Plugin_RequireUserAgentHeader extends Zend_Controller_Plugin_Abstract
{
    /**
     * @internal
     * @var array
     */
    static private $_errMessage = array(
        'Request forbidden by administrative rules.',
        'Please make sure your request has a User-Agent header.'
    );

    /**
     * Validates the current request.
     *
     * All requests MUST include a valid User-Agent header. Requests with no
     * User-Agent header will be rejected.
     *
     * @internal
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     * @see http://framework.zend.com/manual/1.12/en/zend.controller.request.html Zend_Controller_Request_Abstract
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        if (!$request->getHeader('User-Agent')) {
            $this->getResponse()
                ->setHttpResponseCode(403)
                ->setHeader('Content-Type', 'text/plain; charset=utf-8')
                ->setBody(implode("\n", self::$_errMessage))
                ->sendResponse();

            exit(403);
        }
    }
}
