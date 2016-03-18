<?php

/**
 * Used to implement Action Controllers for use with the Front Controller.
 *
 * @link Benri_Controller_Abstract.html Benri_Controller_Abstract
 */
class Benri_Rest_Controller_Action extends Benri_Controller_Action_Abstract
{
    /**
     * Request data.
     * @var StdClass
     */
    protected $_input;

    /**
     * Response data.
     * @var mixed
     */
    protected $_data;


    /**
     * Initialize object.
     */
    public function init()
    {
        parent::init();

        Zend_Controller_Front::getInstance()
            ->registerPlugin(new Zend_Controller_Plugin_PutHandler())
            ->registerPlugin(new Benri_Controller_Plugin_CORS())
            ->registerPlugin(new Benri_Controller_Plugin_RequireUserAgentHeader())
            ->registerPlugin(new Benri_Controller_Plugin_OptionsRequest());

        try {
            $this->getHelper('layout')->disableLayout();
        } catch (Zend_Controller_Action_Exception $e) {
            // If the Layout helper isn't enabled, just ignore and continue.
        }

        $this->getHelper('viewRenderer')->setNoRender(true);

        $this->_input = new StdClass();
    }

    /**
     * Post-dispatch routines.
     *
     * Common usages for `postDispatch()` include rendering content in a
     * sitewide template, link url correction, setting headers, etc.
     */
    public function postDispatch()
    {
        $respond  = false;
        $response = (object) [];

        if ($this->_data) {
            $response->data = $this->_data;
        }

        if ($this->_errors) {
            $response->errors = $this->_errors;
        }

        if ($this->_messages) {
            $response->messages = $this->_messages;
        }

        $this
            ->getResponse()
            ->setHeader('Content-Type', 'application/json; charset=utf-8', true)
            ->setBody(json_encode($response, JSON_NUMERIC_CHECK | JSON_HEX_AMP));
    }

    /**
     * Pre-dispatch routines.
     */
    public function preDispatch()
    {
        $error   = null;
        $request = $this->getRequest();

        if (!$request->isGet() && !$request->isHead()) {
            // read data from the request body.
            $this->_input = json_decode($request->getRawBody());

            if (JSON_ERROR_NONE === json_last_error()) {
                return;
            }

            // Sending invalid JSON will result in a `400 Bad Request` response.
            $this
                ->getResponse()
                ->setHttpResponseCode(400)
                ->setHeader('Content-Type', 'text/plain; charset=utf-8', true)
                ->setBody(json_last_error_msg())
                ->sendResponse();

            exit(400);
        }
    }

    /**
     * General method to save models (Benri_Db_Table_Row).
     *
     * @param Benri_Db_Table_Row
     * @return Benri_Controller_Rest
     */
    protected function _saveModel(Benri_Db_Table_Row &$model, $data = null)
    {
        return parent::_saveModel($model, $data ?: $this->_input);
    }
}
