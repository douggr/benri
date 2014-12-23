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
class ZfRest_Auth extends Zend_Auth
{
    /**
     * {@inheritdoc}
     */
    public function authenticate(Zend_Auth_Adapter_Interface $adapter)
    {
        $result = $adapter->authenticate();

        /**
         * ZF-7546 - prevent multiple succesive calls from storing inconsistent results
         * Ensure storage has clean state
         */
        if ($this->hasIdentity()) {
            $this->clearIdentity();
        }

        if ($result->isValid()) {
            $identity = $adapter->getResultRowObject([
                'access_token',
                'admin',
                'api_key',
                'created_at',
                'email',
                'id',
                'updated_at',
                'username'
            ]);

            $this->getStorage()->write($identity);
        }

        return $result;
    }


    /**
     * {@inheritdoc}
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }
}
