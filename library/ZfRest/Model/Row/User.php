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
class ZfRest_Model_Row_User extends ZfRest_Db_Table_Row
{
    /**
     * Setter for access_token
     * @return string
     */
    public function setAccessToken($value)
    {
        return base64_encode("{$this->api_key}:{$this->api_secret}");
    }

    /**
     * Setter for created_at
     * @return ZfRest_Util_DateTime
     */
    public function setCreatedAt($value)
    {
        return new ZfRest_Util_DateTime($value);
    }

    /**
     * Setter for email
     * @return string
     */
    public function setEmail($value)
    {
        return trim($value);
    }

    /**
     * Setter for email
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
     * Setter for created_at
     * @return ZfRest_Util_DateTime
     */
    public function setUpdatedAt($value)
    {
        return new ZfRest_Util_DateTime($value);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_replace($this->_data, [
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function _insert()
    {
        // every user MUST HAVE an api_key…
        $this->api_key      = ZfRest_Util_String::random(32);

        // an api_secret…
        $this->_setApiSecret();

        // a token…
        $this->token        = ZfRest_Util_String::random(60);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _update()
    {
        if ($this->isDirty('api_key')) {
            $this->api_key = ZfRest_Util_String::random(32);
        }

        if ($this->isDirty('api_secret')) {
            $this->_setApiSecret();
        }

        // Never change this…
        //$this->created_at = $this->_cleanData['created_at'];

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

        if ('' === trim($this->password)) {
            $this->_pushError('user', 'password', static::ERROR_MISSING_FIELD, 'Password is mandatory');
        }

        if (7 > strlen($this->password)) {
            $this->_pushError('user', 'password', static::ERROR_INVALID, 'Password is too short (minimum is 7 characters)');
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->_pushError('user', 'email', static::ERROR_INVALID, 'This email is invalid');
        }

        if ($this->isDirty('password')) {
            $this->password = ZfRest_Util_String::password($this->password);
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
     * Setter for api_secret
     * @return string
     */
    protected function _setApiSecret()
    {
        $this->api_secret = ZfRest_Util_String::password(ZfRest_Util_String::random(60));
    }
}
