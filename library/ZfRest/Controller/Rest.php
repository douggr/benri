<?php
/**
 * douggr/zf-rest
 *
 * @license http://opensource.org/license/MIT
 * @link    https://github.com/douggr/zf-rest
 * @version 2.1.0
 */

/**
 * Used to implement Action Controllers for use with the Front Controller.
 *
 * @link ZfRest_Controller_Action_Abstract.html ZfRest_Controller_Action_Abstract
 */
class ZfRest_Controller_Rest extends ZfRest_Controller_Action_Abstract
{
    /**
     * Request data.
     *
     * @var StdClass
     */
    protected $_input;

    /**
     * Response data.
     *
     * @var mixed
     */
    private $_data;

    /**
     * Initialize object.
     *
     * @return void
     */
    public function init()
    {
        $this->_registerPlugin(new ZfRest_Controller_Plugin_CORS());
        $this->_registerPlugin(new Zend_Controller_Plugin_PutHandler());

        try {
            $this->_helper
                ->layout()
                ->disableLayout();
        } catch (Zend_Controller_Action_Exception $e) {
            // If the Layout helper isn't enabled, just ignore and continue.
        }

        $this->_helper
            ->viewRenderer
            ->setNoRender(true);

        $this->_input   = new StdClass();
        $this->_data    = array(
            'messages'  => array(),
            'data'      => null
        );
    }

    /**
     * Post-dispatch routines.
     *
     * Common usages for `postDispatch()` include rendering content in a
     * sitewide template, link url correction, setting headers, etc.
     *
     * @return void
     */
    public function postDispatch()
    {
        $this->_data['messages'] = $this->_messages;

        if (count($this->_errors)) {
            $this->_data['errors'] = $this->_errors;
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->setBody(json_encode($this->_data, JSON_NUMERIC_CHECK | JSON_HEX_AMP));
    }

    /**
     * Pre-dispatch routines.
     *
     * @return void
     */
    public function preDispatch()
    {
        Zend_Controller_Front::getInstance()
            ->getPlugin('Zend_Controller_Plugin_ErrorHandler')
            ->setErrorHandlerModule('api');

        $error   = null;
        $request = $this->getRequest();

        // we don't need this good guy no anymore…
        unset($_POST);

        if (!$request->isGet() && !$request->isHead()) {
            // … we read data from the request body.
            $this->_input   = json_decode(file_get_contents('php://input'));

            /// Sending invalid JSON will result in a `400 Bad Request` response.
            if (JSON_ERROR_NONE !== json_last_error()) {
                $this->getResponse()
                    ->setHttpResponseCode(400)
                    ->setHeader('Content-Type', 'text/plain; charset=utf-8')
                    ->setBody(json_last_error_msg())
                    ->sendResponse();

                exit -422;
            }
        }
    }

    /**
     * General method to save models (ZfRest_Db_Table_Row).
     *
     * @param ZfRest_Db_Table_Row
     * @return ZfRest_Controller_Rest
     */
    protected function _saveModel(ZfRest_Db_Table_Row &$model)
    {
        try {
            $model->normalizeInput($this->_input)
                ->save();

        } catch (Zend_Db_Table_Row_Exception $ex) {
            foreach ($model->getErrors() as $error) {
                $this->_pushError(
                    $error['resource'],
                    $error['field'],
                    $error['title'],
                    $error['message']
                );
            }

            $this->_pushMessage($ex->getMessage(), 'danger');
        }

        return $this;
    }

    /**
     * Prepare the response.
     *
     * Response data in REST requests are send together with `messages` and
     * `errors`.
     *
     * @param mixed $data Data to send along with `messages` and `errors`
     * @return ZfRest_Controller_Rest
     */
    protected function _setResponseData($data)
    {
        $this->_data['data'] = $data;

        return $this;
    }
}
