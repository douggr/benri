<?php
/**
 * douggr/zf-rest
 *
 * @license http://opensource.org/license/MIT
 * @link    https://github.com/douggr/zf-rest
 * @version 2.1.0
 */

/**
 * {@inheritdoc}
 */
class ZfRest_Model_Row_User extends ZfRest_Db_Table_Row
{
    /**
     * Setter for access_token.
     *
     * @return string
     */
    public function setAccessToken($value)
    {
        return base64_encode("{$this->api_key}:{$this->api_secret}");
    }

    /**
     * Setter for created_at.
     *
     * @return ZfRest_Util_DateTime
     */
    public function setCreatedAt($value)
    {
        return new ZfRest_Util_DateTime($value);
    }

    /**
     * Setter for email.
     *
     * @return string
     */
    public function setEmail($value)
    {
        return trim($value);
    }

    /**
     * Setter for username.
     *
     * @return string
     */
    public function setUsername($value)
    {
        if (preg_match('/^[^a-z]/i', $value)) {
            // username's must begin with a letter.
            $this->_pushError('user', 'username', self::ERROR_INVALID, 'Username must begin with a letter.');
        }

        if (preg_match('/[^\w+]/i', $value)) {
            // username's must begin with a letter.
            $this->_pushError(
                'user',
                'username',
                self::ERROR_INVALID,
                'Username may only contain alphanumeric characters or dashes and must begin with a letter.'
            );
        }

        return trim($value);
    }

    /**
     * Setter for created_at.
     *
     * @return ZfRest_Util_DateTime
     */
    public function setUpdatedAt($value)
    {
        return new ZfRest_Util_DateTime($value);
    }

    /**
     * Returns the column/value data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_replace($this->_data, array(
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ));
    }

    /**
     * Allows pre-insert logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _insert()
    {
        // every user MUST HAVE an api_key…
        $this->_setApiKey();

        // an api_secret…
        $this->_setApiSecret();

        // a token…
        $this->token = ZfRest_Util_String::random(60);

        // and a valid password.
        $this->_setPassword();

        return $this;
    }

    /**
     * Allows pre-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _update()
    {
        if ($this->isDirty('api_key')) {
            $this->_setApiKey();
        }

        if ($this->isDirty('api_secret')) {
            $this->_setApiSecret();
        }

        if ($this->isDirty('password')) {
            if ('' === trim($this->password)) {
                $this->reset('password');
            } else {
                $this->_setPassword();
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _save()
    {
        if ('' === trim($this->email)) {
            $this->_pushError('user', 'email', static::ERROR_MISSING_FIELD, 'Email is mandatory');
        }

        if ('' === trim($this->username)) {
            $this->_pushError('user', 'username', static::ERROR_MISSING_FIELD, 'Username is mandatory');
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->_pushError('user', 'email', static::ERROR_INVALID, 'This email is invalid');
        }

        if (!$this->_checkUniqueness('email')) {
            $this->_pushError('user', 'email', static::ERROR_ALREADY_EXISTS, 'Email is already taken');
        }

        if (!$this->_checkUniqueness('username')) {
            $this->_pushError('user', 'username', static::ERROR_ALREADY_EXISTS, 'Username is already taken');
        }

        // … and always change there.
        $this->updated_at   = new ZfRest_Util_DateTime();
        $this->access_token = null;

        return $this;
    }

    /**
     * Setter fot api_key.
     *
     * @return string
     */
    protected function _setApiKey()
    {
        return $this->api_key = md5(ZfRest_Util_String::random(60));
    }

    /**
     * Setter for api_secret.
     *
     * @return string
     */
    protected function _setApiSecret()
    {
        return $this->api_secret = ZfRest_Util_String::random(60);
    }

    /**
     * Setter for password.
     *
     * @return string
     */
    protected function _setPassword()
    {
        if ('' === trim($this->password)) {
            $this->_pushError('user', 'password', static::ERROR_MISSING_FIELD, 'Password is mandatory');
        }

        if (7 > strlen($this->password)) {
            $this->_pushError('user', 'password', static::ERROR_INVALID, 'Password is too short (minimum is 7 characters)');
        }

        return $this->password = ZfRest_Util_String::password($this->password);
    }
}
