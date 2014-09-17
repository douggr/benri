<?php
/*
 * base/zf-rest
 *
 * @link https://svn.locness.com.br/svn/base/trunk/zf-rest for the canonical source repository
 * @version 1.0.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/**
 * {@inheritdoc}
 */
class ZfRest_Model_Row_UserToGroup extends ZfRest_Db_Row
{
    /**
     * {@inheritdoc}
     */
    public function normalizeInput($input)
    {
        // user_id  int(11)
        // group_id int(11)

        if (isset($input->user_id)) {
            $this->user_id = $input->user_id;
        }

        if (isset($input->group_id)) {
            $this->group_id = $input->group_id;
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
        if (!isset($this->user_id)) {
            $this->pushError('user_id', 'missing_field', 'ERR.MISSING_FIELD');
        }

        if (!isset($this->group_id)) {
            $this->pushError('group_id', 'missing_field', 'ERR.MISSING_FIELD');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _update()
    {
    }

    /**
     * Setter for `user_id`
     * @return mixed
     */
    final protected function setUser($value)
    {
        return $this->user_id = $value;
    }

    /**
     * Setter for `user_id`
     * @return mixed
     */
    final protected function setUserId($value)
    {
        return $this->_getValueForId($value);
    }

    /**
     * Setter for `group_id`
     * @return mixed
     */
    final protected function setGroup($value)
    {
        return $this->group_id = $value;
    }

    /**
     * Setter for `group_id`
     * @return mixed
     */
    final protected function setGroupId($value)
    {
        return $this->_getValueForId($value);
    }
}
