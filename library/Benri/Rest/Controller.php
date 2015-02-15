<?php
/**
 * Used to implement Action Controllers for use with the Front Controller.
 *
 * @link Benri_Controller_Abstract.html Benri_Controller_Abstract
 */
class Benri_Rest_Controller extends Benri_Controller_Abstract
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
    protected $_data;

    /**
     * Initialize object.
     *
     * @return void
     */
    public function init()
    {
        $this->_registerPlugin(new Zend_Controller_Plugin_PutHandler());
        $this->_registerPlugin(new Benri_Controller_Plugin_CORS());
        $this->_registerPlugin(new Benri_Controller_Plugin_RequireUserAgentHeader());
        $this->_registerPlugin(new Benri_Controller_Plugin_OptionsRequest());

        try {
            $this->disableLayout();
        } catch (Zend_Controller_Action_Exception $e) {
            // If the Layout helper isn't enabled, just ignore and continue.
        }

        $this->_helper
            ->viewRenderer
            ->setNoRender(true);

        $this->_input   = new StdClass();
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
        $response = (object) array(
            'data'      => $this->_data,
            'errors'    => $this->_errors,
            'messages'  => $this->_messages,
        );

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json; charset=utf-8', true)
            ->setBody(json_encode($response, JSON_NUMERIC_CHECK | JSON_HEX_AMP));
    }

    /**
     * Pre-dispatch routines.
     *
     * @return void
     */
    public function preDispatch()
    {
        $error   = null;
        $request = $this->getRequest();

        if (!$request->isGet() && !$request->isHead()) {
            // … we read data from the request body.
            $this->_input = json_decode($request->getRawBody());

            /// Sending invalid JSON will result in a `400 Bad Request` response.
            if (JSON_ERROR_NONE !== json_last_error()) {
                $this->getResponse()
                    ->setHttpResponseCode(400)
                    ->setHeader('Content-Type', 'text/plain; charset=utf-8', true)
                    ->setBody(json_last_error_msg())
                    ->sendResponse();

                exit(400);
            }
        }
    }

    /**
     * General method to save models (Benri_Db_Table_Row).
     *
     * @param Benri_Db_Table_Row
     * @return Benri_Controller_Rest
     */
    protected function _saveModel(Benri_Db_Table_Row &$model)
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

            $this->_pushMessage($ex->getMessage(), 'error');
        }

        return $this;
    }
}
