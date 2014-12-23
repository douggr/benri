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
 * Validates the presence of the User-Agent header.
 */
class ZfRest_Controller_Plugin_RequireUserAgentHeader extends Zend_Controller_Plugin_Abstract
{
    static private $_errMessage = [
        'Request forbidden by administrative rules.',
        'Please make sure your request has a User-Agent header.'
    ];

    /**
     * Validates the current request.
     *
     * All requests MUST include a valid User-Agent header. Requests with no
     * User-Agent header will be rejected.
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        if (!$request->getHeader('User-Agent')) {
            $this->getResponse()
                ->setHttpResponseCode(403)
                ->setHeader('Content-Type', 'text/plain; charset=utf-8')
                ->setBody(implode("\n", self::$_errMessage))
                ->sendResponse();

            exit -403;
        }
    }
}
