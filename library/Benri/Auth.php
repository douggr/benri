<?php
/**
 * douggr/benri
 *
 * @license http://opensource.org/license/MIT
 * @link    https://github.com/douggr/benri
 * @version 1.0.0
 */

/**
 * Provides an API for authentication and includes concrete authentication
 * adapters for common use case scenarios.
 *
 * @link http://framework.zend.com/manual/1.12/en/zend.auth.introduction.html Zend_Auth
 */
class Benri_Auth extends Zend_Auth
{
    /**
     * Authenticates against the supplied adapter.
     *
     * @param Zend_Auth_Adapter_Interface $adapter The adapter to use
     * @return Zend_Auth_Result
     * @see http://framework.zend.com/manual/1.12/en/zend.auth.introduction.html#zend.auth.introduction.adapters Zend_Auth_Adapter_Interface
     * @see http://framework.zend.com/manual/1.12/en/zend.auth.introduction.html#zend.auth.introduction.results Zend_Auth_Result
     */
    public function authenticate(Zend_Auth_Adapter_Interface $adapter)
    {
        // Authenticates against the supplied adapter.
        $result = $adapter->authenticate();

        /**
         * ZF-7546 - prevent multiple succesive calls from storing inconsistent
         * results.
         *
         * Ensure storage has clean state.
         */
        if ($this->hasIdentity()) {
            $this->clearIdentity();
        }

        if ($result->isValid()) {
            $this->getStorage()
                ->write($adapter->getResultRowObject());
        }

        return $result;
    }


    /**
     * Returns an instance of Benri_Auth.
     *
     * @return Benri_Auth
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }
}
