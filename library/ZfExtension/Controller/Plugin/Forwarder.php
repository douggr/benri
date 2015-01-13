<?php
/**
 * douggr/zf-extension
 *
 * @license http://opensource.org/license/MIT
 * @link    https://github.com/douggr/zf-extension
 * @version 2.1.0
 */

/**
 * Emulates hierarquical inheritance for routes.
 *
 * @link http://framework.zend.com/manual/1.12/en/zend.controller.plugins.html Zend_Controller_Plugin_Abstract
 */
class ZfExtension_Controller_Plugin_Forwarder extends Zend_Controller_Plugin_Abstract
{
    /**
     * @internal
     * @var integer
     */
    private static $_forwardStack = -1;

    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @internal
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     * @see http://framework.zend.com/manual/1.12/en/zend.controller.request.html Zend_Controller_Request_Abstract
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $action = $request->getParam('action');

        while ($forwardingController = $request->getParam($controller = sprintf('children-%02d', ++self::$_forwardStack))) {
            $idKey = "{$controller}-id";

            if (!$request->isGet() || $request->getParam($idKey)) {
                $action = strtolower($request->getMethod());
            }

            $request
                // Set up the parent controller and id
                ->setParam('parent', $request->getParam('controller'))
                ->setParam('parentId', $request->getParam('id'))

                // Set up the current controller and id
                ->setParam('controller', $forwardingController)
                ->setParam('id', $request->getParam($idKey))

                // shift the current entry from the router
                ->setParam($controller, null)
                ->setParam($idKey, null)

                // set up the new Controller and Action
                ->setControllerName($forwardingController)
                ->setActionName($action)

                // do not dispatch until we loop through all controllers
                ->setDispatched(false);
        }
    }
}
