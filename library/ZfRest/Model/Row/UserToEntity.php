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

/**
 * {@inheritdoc}
 */
class UserToEntity extends Db\Row
{
    /**
     * {@inheritdoc}
     */
    public function normalizeInput($input)
    {
        // user_id   int(11)
        // entity_id int(11)

        if (isset($input->user_id)) {
            $this->user_id = $input->user_id;
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
        if (!isset($this->user_id)) {
            $this->pushError('user_id', 'missing_field', 'ERR.MISSING_FIELD');
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
}
