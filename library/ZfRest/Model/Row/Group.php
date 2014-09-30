<?php
/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 1.1.3
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/**
 * {@inheritdoc}
 */
class ZfRest_Model_Row_Group extends ZfRest_Db_Row
{
    /**
     * {@inheritdoc}
     */
    public function normalizeInput($input)
    {
        // name        varchar(200)
        // description text
        // entity_id   int(11)

        if (isset($input->name)) {
            $this->name = $input->name;
        }

        if (isset($input->description)) {
            $this->description = $input->description;
        }

        if (isset($input->entity_id)) {
            $this->entity_id = $input->entity_id;
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
        if (!isset($this->name)) {
            $this->pushError('name', 'missing_field', 'ERR.MISSING_FIELD');
        }

        if (!isset($this->entity_id)) {
            $this->pushError('entity_id', 'missing_field', 'ERR.MISSING_FIELD');
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
        return trim($value);
    }

    /**
     * Getter for `description`
     * @return mixed
     */
    final protected function getDescription()
    {
        return ZfRest_Util_String::escape($this->description);
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
     * Setter for `admin`
     * @return mixed
     */
    final protected function setAdmin($value)
    {
        return $this->admin;
    }

    /**
     * Setter for `entity_id`
     * @return mixed
     */
    final protected function setEntity($value)
    {
        $this->entity_id = $value;
    }

    /**
     * Setter for `entity_id`
     * @return mixed
     */
    final protected function setEntityId($value)
    {
        return $this->_getValueForId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUsers()
    {
        return ZfRest_Model_UserToGroup::loadUsers($this->id);
    }
}
