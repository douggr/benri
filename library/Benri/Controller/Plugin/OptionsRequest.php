<?php
/**
 * OPTIONS request method represents a request for information about the
 * communication options available on the request/response chain identified
 * by the Request-URI. This method allows the client to determine the options
 * and/or requirements associated with a resource, or the capabilities of a
 * server, without implying a resource action or initiating a resource
 * retrieval.
 *
 * @link http://framework.zend.com/manual/1.12/en/zend.controller.plugins.html Zend_Controller_Plugin_Abstract
 */
class Benri_Controller_Plugin_OptionsRequest extends Zend_Controller_Plugin_Abstract
{
    /**
     * Send an empty response and exit
     *
     * @internal
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     * @see http://framework.zend.com/manual/1.12/en/zend.controller.request.html Zend_Controller_Request_Abstract
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        if ($request->isOptions()) {
            $this->getResponse()
                ->sendResponse();

            exit(0);
        }
    }
}
