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
class ZfRest_Model_Row_Entity extends ZfRest_Db_Row
{
    private static $_adminGroup = null;

    /**
     * {@inheritdoc}
     */
    public function hasPublicProfile()
    {
        return 'PUBLIC' === $this->visibility;
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeInput($input)
    {
        // name        varchar(200)
        // slug        varchar(200)
        // description varchar(200)
        // location    varchar(100)
        // url         varchar(200)
        // email       varchar(100)
        // visibility  enum('PUBLIC','PRIVATE')

        if (isset($input->name)) {
            $this->name = $input->name;
        }

        if (isset($input->description)) {
            $this->description = $input->description;
        }

        if (isset($input->location)) {
            $this->location = $input->location;
        }

        if (isset($input->url)) {
            $this->url = $input->url;
        }

        if (isset($input->email)) {
            $this->email = $input->email;
        }

        if (isset($input->visibility)) {
            $this->visibility = $input->visibility;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _insert()
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function _save()
    {
        if ('' === trim($this->name)) {
            $this->pushError('name', 'missing_field', 'ERR.MISSING_FIELD');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _update()
    {
    }

    /**
     * Setter for `name`
     * @return mixed
     */
    final protected function setName($value)
    {
        $this->slug = $value;
        return trim($value);
    }

    /**
     * Setter for `slug`
     * @return mixed
     */
    final protected function setSlug($value)
    {
        return ZfRest_Util_String::slugfy($value);
    }

    /**
     * Setter for `description`
     * @return mixed
     */
    final protected function setDescription($value)
    {
        return trim($value);
    }

    /**
     * Setter for `location`
     * @return mixed
     */
    final protected function setLocation($value)
    {
        return trim($value);
    }

    /**
     * Setter for `url`
     * @return mixed
     */
    final protected function setUrl($value)
    {
        return trim($value);
    }

    /**
     * Setter for `email`
     * @return mixed
     */
    final protected function setEmail($value)
    {
        $value = trim($value);

        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->pushError('email', 'invalid', 'ERR.EMAIL_INVALID');
        } else {
            return $value;
        }
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
     * {@inheritdoc}
     */
    public function loadUsers()
    {
        return ZfRest_Model_UserToEntity::loadUsers($this->id);
    }
}
