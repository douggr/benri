<?php
/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 1.1.4
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/**
 * {@inheritdoc}
 */
class ZfRest_Model_Row_User extends ZfRest_Db_Row
{
    /**
     * @var array
     */
    public $permissions = [];

    /**
     * {@inheritdoc}
     */
    public function normalizeInput($input)
    {
        // email         VARCHAR(200)
        // username      VARCHAR(200)
        // password      CHAR(60)
        // admin         BOOLEAN
        // token         CHAR(60)
        // access_token  CHAR(60)
        // api_key       CHAR(32)
        // api_secret    CHAR(60)

        if (isset($input->email)) {
            $this->email = $input->email;
        }

        if (isset($input->username)) {
            $this->username = $input->username;
        }

        if (isset($input->password)) {
            $this->password = $input->password;
        }

        if (isset($input->admin)) {
            $this->admin = $input->admin;
        }

        if (isset($input->access_token)) {
            $this->token = $input->access_token;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _insert()
    {
        // every user MUST HAVE a password…
        if ('' === trim($this->password)) {
            $this->pushError('password', 'invalid', 'ERR.PASSWORD_REQUIRED');
        }

        // an api_key…
        $this->api_key      = ZfRest_Util_String::random(32);

        // an api_secret…
        $this->api_secret   = ZfRest_Util_String::random(60);

        // a token…
        $this->token        = ZfRest_Util_String::random(60);

        // and an access token…
        $this->access_token = ZfRest_Util_String::random(60);
    }

    /**
     * {@inheritdoc}
     */
    protected function _save()
    {
        if ('' === trim($this->email)) {
            $this->pushError('email', 'missing_field', 'ERR.MISSING_FIELD');
        }

        if ('' === trim($this->password)) {
            $this->pushError('password', 'missing_field', 'ERR.MISSING_FIELD');
        }

        if ('' === trim($this->admin)) {
            $this->admin = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _update()
    {
    }

    /**
     * Setter for email
     * @return mixed
     */
    final protected function setEmail($value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->pushError('email', 'invalid', 'ERR.EMAIL_INVALID', $value);

        } elseif (!$this->username) {
            return $this->username = $value;

        } else {
            return trim($value);

        }
    }

    /**
     * Setter for first_name
     * @return mixed
     */
    final protected function setUsername($value)
    {
        return trim($value);
    }

    /**
     * Setter for password
     * @return mixed
     */
    final protected function setPassword($value)
    {
        return ZfRest_Util_String::password($value);
    }

    /**
     * Setter for admin
     * @return mixed
     */
    final protected function setAdmin($value)
    {
        return intval($value);
    }

    /**
     * Setter for token
     * @return mixed
     */
    final protected function setToken($value)
    {
        return trim($value);
    }

    /**
     * Setter for token
     * @return mixed
     */
    final protected function setAccessToken($value)
    {
        return trim($value);
    }

    /**
     * Setter for api_key
     * @return mixed
     */
    final protected function setApiKey($value)
    {
        return trim($value);
    }

    /**
     * Setter for api_secret
     * @return mixed
     */
    final protected function setApiSecret($value)
    {
        return trim($value);
    }

    /**
     * {@inheritdoc}
     */
    public function loadGroups()
    {
        return ZfRest_Model_UserToGroup::loadGroups($this->id);
    }

    /**
     * {@inheritdoc}
     */
    public function loadEntities()
    {
        return ZfRest_Model_UserToEntity::loadEntities($this->id);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $data                = parent::toArray();
        $data['permissions'] = $this->permissions;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    final public function isSiteAdmin()
    {
        $context = ZfRest_Db_Table::getContext();

        if ($this->admin) {
            return true;

        } elseif (!array_key_exists($context, $this->permissions)) {
            return false;

        } else {
            foreach ($this->permissions[$context] as $group) {
                if ($group[1]) {
                    return true;
                }
            }

            return false;
        }
    }
}
