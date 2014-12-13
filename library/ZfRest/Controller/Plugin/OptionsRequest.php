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
 * OPTIONS request method represents a request for information about the
 * communication options available on the request/response chain identified
 * by the Request-URI. This method allows the client to determine the options
 * and/or requirements associated with a resource, or the capabilities of a
 * server, without implying a resource action or initiating a resource
 * retrieval.
 */
class ZfRest_Controller_Plugin_OptionsRequest extends Zend_Controller_Plugin_Abstract
{
    /**
     * Send an empty response and exit
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        if ($request->isOptions()) {
            $this->getResponse()
                ->sendResponse();

            exit -200;
        }
    }
}
