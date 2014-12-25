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
class ZfRest_Controller_Rest extends ZfRest_Controller_Action_Abstract
{
    /**
     * Request data
     */
    protected $_input;

    /**
     * Response data
     */
    private $_data;

    /**
     * {@inheritdoc}
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
        $this->_data    = [
            'messages'  => [],
            'data'      => null
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function postDispatch()
    {
        $this->_data['messages'] = $this->_messages;

        if (count($this->_errors)) {
            $this->_data['errors'] = $this->_errors;
        }

        $pretty = $this->getRequest()
            ->getParam('pretty');

        if (null !== $pretty) {
            $jsonOptions = JSON_NUMERIC_CHECK | JSON_HEX_AMP | JSON_PRETTY_PRINT;
        } else {
            $jsonOptions = JSON_NUMERIC_CHECK | JSON_HEX_AMP;
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->setBody(json_encode($this->_data, $jsonOptions));
    }

    /**
     * {@inheritdoc}
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
     * General method to save models (ZfRest_Db_Table_Row)
     *
     * @param ZfRest_Db_Table_Row
     * @return ZfRest_Controller_Rest
     */
    protected function _saveModel(ZfRest_Db_Table_Row &$model)
    {
        try {
            $model->normalizeInput($this->_input)
                ->save();

            $this->_pushMessage('Horray!', 'success');
        } catch (Zend_Db_Table_Row_Exception $ex) {
            foreach ($model->getErrors() as $error) {
                extract($error) && $this->_pushError($resource, $field, $title, $message);
            }

            $this->_pushMessage($ex->getMessage(), 'danger');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _setResponseData($data)
    {
        $this->_data['data'] = $data;

        return $this;
    }
}
