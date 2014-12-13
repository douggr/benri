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
class ZfRest_Auth
{
    const FAILURE                       = Zend_Auth_Result::FAILURE;
    const FAILURE_IDENTITY_NOT_FOUND    = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
    const FAILURE_IDENTITY_AMBIGUOUS    = Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
    const FAILURE_CREDENTIAL_INVALID    = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
    const FAILURE_UNCATEGORIZED         = Zend_Auth_Result::FAILURE_UNCATEGORIZED;

    /**
     * @var ZfRest_Auth
     */
    protected static $instance = null;

    /**
     * Returns an instance of ZfRest_Auth
     *
     * @return ZfRest_Auth
     */
    final public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Authenticates against the supplied adapter
     *
     * @param string username
     * @param string password in row format
     * @return ZfRest_Auth
     */
    public static function authenticate($username, $password)
    {
        if ('' === trim($username) || '' === trim($password)) {
            throw new ZfRest_Auth_Exception('ERR.IDENTITY_AMBIGUOUS', self::FAILURE_IDENTITY_AMBIGUOUS);
        }

        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $usedColumn = 'email';
        } else {
            $usedColumn = 'username';
        }

        $user = ZfRest_Model_User::locate($usedColumn, $username);

        if (null === $user) {
            throw new ZfRest_Auth_Exception('ERR.IDENTITY_NOT_FOUND', self::FAILURE_IDENTITY_NOT_FOUND);
        }

        if (!ZfRest_Util_String::verifyPassword($password, $user->password)) {
            throw new ZfRest_Auth_Exception('ERR.CREDENTIAL_INVALID', self::FAILURE_CREDENTIAL_INVALID);
        }

        $token              = ZfRest_Util_String::password(static::getAccessToken($user));
        $user->access_token = $token;
        $user->save();

        return [
            'token_type'    => 'bearer',
            'access_token'  => $token
        ];
    }

    /**
     * @param Object|ZfRest_Model_User
     * @return string
     */
    public static function getAccessToken($user)
    {
        return base64_encode("{$user->api_key}:{$user->api_secret}");
    }

    /**
     * Singleton pattern implementation makes "new" unavailable
     *
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Singleton pattern implementation makes "clone" unavailable
     *
     * @return void
     */
    private function __clone()
    {
    }
}
