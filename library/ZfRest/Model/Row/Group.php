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
use ZfRest\Model;
use ZfRest\Model\Group as Table;
use ZfRest\Model\Exception\Group as Exception;
use ZfRest\Util\String;

/**
 * {@inheritdoc}
 */
class Group extends Db\Row
{
    /**
     * {@inheritdoc}
     */
    public function normalizeInput($input)
    {
        // name        varchar(200)
        // description text
        // entity_id   int(11)
        // locale_id   int(11)

        if (isset($input->name)) {
            $this->name = $input->name;
        }

        if (isset($input->description)) {
            $this->description = $input->description;
        }

        if (isset($input->entity_id)) {
            $this->entity_id = $input->entity_id;
        }

        if (isset($input->locale)) {
            $this->locale_id = $input->locale;
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

        if (!isset($this->locale_id)) {
            $this->pushError('locale_id', 'missing_field', 'ERR.MISSING_FIELD');
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
        return String::escape($this->description);
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
     * Translate this object to another locale
     * @return mixed
     */
    public function translate($newLocale)
    {
        $clone = Table::locateWithinLocale($this->id, $newLocale);

        if (!$clone) {
            $clone             = Table::create();
            $clone->_data      = $this->toArray();
            $clone->locale_id  = $newLocale;
            $clone->created_by = Table::getAuthUser()->id;

            $clone->save();
        }

        return $clone;
    }

    /**
     * Setter for `locale_id`
     * @return mixed
     */
    final protected function setLocale($value)
    {
        return $this->locale_id = $value;
    }
}
