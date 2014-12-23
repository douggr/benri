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
class ZfRest_Auth_Adapter_DbTable extends Zend_Auth_Adapter_DbTable
{
    /**
     * $_tableName - the table name to check
     *
     * @var string
     */
    protected $_tableName = 'user';

    /**
     * $_identityColumn - the column to use as the identity
     *
     * @var string
     */
    protected $_identityColumn = 'username';

    /**
     * $_credentialColumns - columns to be used as the credentials
     *
     * @var string
     */
    protected $_credentialColumn = 'password';

    /**
     * getDbAdapter() - returns the database adapter.
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDbAdapter()
    {
        return $this->_zendDb;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentity($identity)
    {
        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $this->setIdentityColumn('email');
        } else {
            $this->setIdentityColumn('username');
        }

        return parent::setIdentity($identity);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmbiguityIdentity($flag)
    {
        $this->_ambiguityIdentity = false;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate()
    {
        $this->_authenticateSetup();

        $dbSelect   = $this->_authenticateCreateSelect();
        $identity   = $this->_authenticateQuerySelect($dbSelect);
        $authResult = $this->_authenticateValidateResultSet($identity);

        if ($authResult instanceof Zend_Auth_Result) {
            return $authResult;
        }

        return $this->_authenticateValidateResult(array_shift($identity));
    }

    /**
     * {@inheritdoc}
     */
    protected function _authenticateCreateSelect()
    {
        $dbSelect = clone $this->getDbSelect();
        $dbSelect->from($this->_tableName)
            ->where($this->_zendDb->quoteIdentifier($this->_identityColumn, true) . ' = ?', $this->_identity)
            ->limit(1);

        return $dbSelect;
    }

    /**
     * {@inheritdoc}
     */
    protected function _authenticateValidateResult($resultIdentity)
    {
        if (!ZfRest_Util_String::verifyPassword($this->_credential, $resultIdentity[$this->_credentialColumn])) {
            $code             = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;;
            $message          = 'Supplied credential is invalid.';
        } else {
            $this->_resultRow = $resultIdentity;
            $code             = Zend_Auth_Result::SUCCESS;
            $message          = 'Authentication successful.';
        }

        $this->_authenticateResultInfo['code']          = $code;
        $this->_authenticateResultInfo['messages'][]    = $message;

        return $this->_authenticateCreateAuthResult();
    }
}
