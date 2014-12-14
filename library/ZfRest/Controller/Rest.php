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
class ZfRest_Controller_Rest extends ZfRest_Controller_Abstract
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

        $this->_helper
            ->layout()
            ->disableLayout();

        $this->_helper
            ->viewRenderer
            ->setNoRender(true);

        $this->_input   = new StdClass();
        $this->_data    = [
            'controller'    => $this->getParam('controller'),
            'identity'      => ZfRest_Auth::getInstance()->getIdentity(),
            'messages'      => [],
            'module'        => $this->getParam('module'),
            'data'          => null
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function postDispatch()
    {
        $this->_data['messages'][] = $this->_messages;

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
        $error   = null;
        $request = $this->getRequest();

        // we don't need this good guy no anymore…
        unset($_POST);

        if (!$request->isGet() && !$request->isHead()) {
            // … we read data from the request body.
            $this->_input   = json_decode(file_get_contents('php://input'));
            $jsonError      = json_last_error();

            if (JSON_ERROR_NONE !== $jsonError) {
                switch ($jsonError) {
                    case JSON_ERROR_DEPTH:
                        $error = "Problems parsing JSON data.\nThe maximum stack depth has been exceeded.";
                        break;

                    case JSON_ERROR_STATE_MISMATCH:
                        $error = "Invalid or malformed JSON.";
                        break;

                    case JSON_ERROR_CTRL_CHAR:
                        $error = "Problems parsing JSON data.\nControl character error, possibly incorrectly encoded.";
                        break;

                    case JSON_ERROR_SYNTAX:
                        $error = "Syntax error, malformed JSON.";
                        break;

                    case JSON_ERROR_UTF8:
                        $error = "Problems parsing JSON data.\nMalformed UTF-8 characters, possibly incorrectly encoded.";
                        break;

                    case JSON_ERROR_RECURSION:
                        $error = "Problems parsing JSON data.\nOne or more recursive references in the value to be encoded.";
                        break;

                    case JSON_ERROR_INF_OR_NAN:
                        $error = "Problems parsing JSON data.\nOne or more NAN or INF values in the value to be encoded.";
                        break;

                    case JSON_ERROR_UNSUPPORTED_TYPE:
                        $error = "Problems parsing JSON data.\nA value of a type that cannot be encoded was given.";
                        break;
                }

                $this->getResponse()
                    ->setHttpResponseCode(403)
                    ->setHeader('Content-Type', 'text/plain; charset=utf-8')
                    ->setBody($error)
                    ->sendResponse();

                exit -403;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _setResponseData($data)
    {
        $this->_data['data'] = $data;
    }
}
