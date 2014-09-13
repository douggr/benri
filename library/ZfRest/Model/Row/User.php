<?php
/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 1.0.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

namespace ZfRest\Model\Row;

use ZfRest\Db;
use ZfRest\Util\String;
use ZfRest\Util\DateTime;

/**
 * {@inheritdoc}
 */
class User extends Db\Row
{
    /**
     * {@inheritdoc}
     */
    public function isVisible($viewer)
    {
        if (is_object($viewer)) {
            return
                    // explicitly set…
                'PUBLIC' === $this->visibility

                ||  // or admins…
                $viewer->admin

                ||  // or the user himself.
                $viewer->id == $this->id;
        } else {
            return 'PUBLIC' === $this->visibility;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeInput($input)
    {
        // email        varchar(200)
        // first_name   varchar(200)
        // last_name    varchar(200)
        // username     varchar(200)
        // password     char(60)
        // admin        tinyint(1)
        // status       char(1)
        // visibility   enum('PUBLIC','PRIVATE')
        // token        char(60)
        // token_max_ts timestamp
        // api_key      char(32)
        // api_secret   char(60)

        if (isset($input->email)) {
            $this->email = $input->email;
        }

        if (isset($input->first_name)) {
            $this->first_name = $input->first_name;
        }

        if (isset($input->last_name)) {
            $this->last_name = $input->last_name;
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

        if (isset($input->status)) {
            $this->status = $input->status;
        }

        if (isset($input->visibility)) {
            $this->visibility = $input->visibility;
        }

        if (isset($input->token)) {
            $this->token = $input->token;
        }

        if (isset($input->token_max_ts)) {
            $this->token_max_ts = $input->token_max_ts;
        }

        if (isset($input->api_key)) {
            $this->api_key = $input->api_key;
        }

        if (isset($input->api_secret)) {
            $this->api_secret = $input->api_secret;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _insert()
    {
        // every `user` MUST HAVE a password…
        if ('' === trim($this->password)) {
            $this->pushError('password', 'invalid', 'ERR.PASSWORD_REQUIRED');
        }

        // an api_key…
        $this->api_key      = String::random(32);

        // an api_secret…
        $this->api_secret   = String::random(60);

        // a token…
        $this->token        = String::random(60);

        // and a valid token_max_ts…
        $this->token_max_ts = '+1week';

        $this->token_max_ts
            ->setFormat('Y:m:d H:i:s');
    }

    /**
     * {@inheritdoc}
     */
    protected function _save()
    {
        if (!isset($this->email)) {
            $this->pushError('email', 'missing_field', 'ERR.MISSING_FIELD');
        }

        if (!isset($this->password)) {
            $this->pushError('password', 'missing_field', 'ERR.MISSING_FIELD');
        }

        if (!isset($this->admin)) {
            $this->pushError('admin', 'missing_field', 'ERR.MISSING_FIELD');
        }

        if (!isset($this->token)) {
            $this->pushError('token', 'missing_field', 'ERR.MISSING_FIELD');
        }

        if (!isset($this->api_key)) {
            $this->pushError('api_key', 'missing_field', 'ERR.MISSING_FIELD');
        }

        if (!isset($this->api_secret)) {
            $this->pushError('api_secret', 'missing_field', 'ERR.MISSING_FIELD');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _update()
    {
    }

    /**
     * Setter for `email`
     * @return mixed
     */
    final protected function setEmail($value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->pushError('email', 'invalid', 'ERR.EMAIL_INVALID', $value);

        } elseif (!$this->username) {
            $this->username = trim($value);
            return $this->username;

        } else {
            return trim($value);

        }
    }

    /**
     * Setter for `first_name`
     * @return mixed
     */
    final protected function setFirstName($value)
    {
        return trim($value);
    }

    /**
     * Setter for `last_name`
     * @return mixed
     */
    final protected function setLastName($value)
    {
        return trim($value);
    }

    /**
     * Setter for `first_name`
     * @return mixed
     */
    final protected function setUsername($value)
    {
        return trim($value);
    }

    /**
     * Setter for `password`
     * @return mixed
     */
    final protected function setPassword($value)
    {
        return String::password($value);
    }

    /**
     * Setter for `admin`
     * @return mixed
     */
    final protected function setAdmin($value)
    {
        return intval($value);
    }

    /**
     * Setter for `status`
     * @return mixed
     */
    final protected function setStatus($value)
    {
        return trim($value);
    }

    /**
     * Setter for `visibility`
     * @return mixed
     */
    final protected function setVisibility($value)
    {
        $value = strtoupper(trim($value));

        if ('PUBLIC' !== $value && 'PRIVATE' !== $value) {
            $this->pushError('visibility', 'invalid', 'ERR.VISIBILITY_INVALID');
        } else {
            return $value;
        }
    }

    /**
     * Setter for `token`
     * @return mixed
     */
    final protected function setToken($value)
    {
        return trim($value);
    }

    /**
     * Setter for `token_max_ts`
     * @return mixed
     */
    final protected function setTokenMaxTs($value)
    {
        return new DateTime($value);
    }

    /**
     * Setter for `api_key`
     * @return mixed
     */
    final protected function setApiKey($value)
    {
        return trim($value);
    }

    /**
     * Setter for `api_secret`
     * @return mixed
     */
    final protected function setApiSecret($value)
    {
        return trim($value);
    }
}
