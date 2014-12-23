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
 * Emulates hierarquical inheritance for routes.
 */
class ZfRest_Controller_Plugin_Forwarder extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var integer
     */
    private static $_forwardStack = -1;

    /**
     * {@inheritdoc}
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
