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
class ZfRest_Db_Row extends Zend_Db_Table_Row
{
    /**
     * @var array
     */
    private $_errors = [];

    /**
     * {@inheritdoc}
     */
    public function isDirty($column)
    {
        return array_key_exists($column, $this->_modifiedFields);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return empty($this->_errors) ? false : $this->_errors;
    }

    /**
     * {@inheritdoc}
     */
    public function isNewRecord() {
        return empty($this->_cleanData);
    }

    /**
     * All error objects have field and code properties so that your client
     * can tell what the problem is. These are the possible validation error
     * codes:
     *  - missing: This means a resource does not exist.
     *  - missing_field: This means a required field on a resource has not
     *      been set.
     *  - invalid: This means the formatting of a field is invalid. The
     *      documentation for that resource should be able to give you more
     *      specific information.
     *  - already_exists: This means another resource has the same value as
     *      this field. This can happen in resources that must have some unique
     *      key (such as Label or Locale names).
     *  - uncategorized: This means an uncommon error.
     *  - unknown: For the rare case an exception occurred and we couldn't
     *      recover.
     *
     * If resources have custom validation errors, they will be documented
     * with the resource.
     */
    protected function pushError($field, $code, $message = '', $interpolateParams = [])
    {
        $this->_errors[] = [
            'field'     => $field,
            'code'      => $code,
            'message'   => $message,
            'params'    => (array) $interpolateParams,
        ];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        /// A read-only row cannot be saved.
        if ($this->_readOnly === true) {
            throw new Exception('ERR.READ_ONLY');
        }

        /// Allows pre-save logic to be applied to any row.
        /// Zend_Db_Table_Row only uses to do it on _insert OR _update,
        /// why Zend, why?
        ///
        /// Computers are bullshit!
        $this->_save();

        foreach ($this->_data as $column => &$value) {
            if ($value instanceof DateTime) {
                $value->setFormat('Y:m:d H:i:s');
            }
        }

        if (false !== $this->getErrors()) {
            throw new Exception('ERR.VALIDATION_INVALID');
        }

        $user = ZfRest_Db_Table::getAuthUser();
        if ($user) {
            $created_by = $user->id;
        } else {
            $created_by = 1;
        }

        if ($this->isNewRecord()) {
            if ($this->offsetExists('created_by')) {
                $this->created_by = $created_by;
            }

            if ($this->offsetExists('updated_by')) {
                $this->updated_by = $created_by;
            }
        } else {
            if ($this->offsetExists('updated_by')) {
                $this->updated_by = $created_by;
            }
        }

        ///
        parent::save();

        ///
        $this->_postSave();
    }

    /**
     * Convert this object to a JSON string
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray(), JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK);
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeInput($input)
    {
        foreach ($input as $column => $value) {
            if ($this->offsetExists($column)) {
                $this->__set($column, $value);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doInsert()
    {
        /// Run pre-INSERT logic
        $this->_insert();

        /// Holy shit
        if (false !== $this->getErrors()) {
            throw new Exception('ERR.VALIDATION_INVALID');
        }

        /// Execute the INSERT (this may throw an exception)
        $data = array_intersect_key($this->_data, $this->_modifiedFields);
        $primaryKey = $this->_getTable()->insert($data);

        /// Normalize the result to an array indexed by primary key column(s).
        /// The table insert() method may return a scalar.
        if (is_array($primaryKey)) {
            $newPrimaryKey = $primaryKey;

        } else {
            ///ZF-6167 Use tempPrimaryKey temporary to avoid that zend encoding fails.
            $tempPrimaryKey = (array) $this->_primary;
            $newPrimaryKey = array(current($tempPrimaryKey) => $primaryKey);
        }

        /// Save the new primary key value in _data.  The primary key may have
        /// been generated by a sequence or auto-increment mechanism, and this
        /// merge should be done before the _postInsert() method is run, so the
        /// new values are available for logging, etc.
        $this->_data = array_merge($this->_data, $newPrimaryKey);

        /// Run post-INSERT logic
        $this->_postInsert();

        /// Update the _cleanData to reflect that the data has been inserted.
        $this->_refresh();

        return $primaryKey;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doUpdate()
    {
        /// Get expressions for a WHERE clause based on the primary key value(s).
        $where = $this->_getWhereQuery(false);

        /// Run pre-UPDATE logic
        $this->_update();

        /// Holy shit
        if (false !== $this->getErrors()) {
            throw new Exception('ERR.VALIDATION_INVALID');
        }

        /// Compare the data to the modified fields array to discover
        /// which columns have been changed.
        $diffData = array_intersect_key($this->_data, $this->_modifiedFields);

        /// Were any of the changed columns part of the primary key?
        $pkDiffData = array_intersect_key($diffData, array_flip((array) $this->_primary));

        /// Execute cascading updates against dependent tables.
        /// Do this only if primary key value(s) were changed.
        if (count($pkDiffData) > 0) {
            $depTables = $this->_getTable()->getDependentTables();
            if (!empty($depTables)) {
                $pkNew = $this->_getPrimaryKey(true);
                $pkOld = $this->_getPrimaryKey(false);

                foreach ($depTables as $tableClass) {
                    $t = $this->_getTableFromString($tableClass);
                    $t->_cascadeUpdate($this->getTableClass(), $pkOld, $pkNew);
                }
            }
        }

        /// Execute the UPDATE (this may throw an exception)
        /// Do this only if data values were changed.
        /// Use the $diffData variable, so the UPDATE statement
        /// includes SET terms only for data values that changed.
        if (count($diffData) > 0) {
            $this->_getTable()->update($diffData, $where);
        }

        /// Run post-UPDATE logic.  Do this before the _refresh()
        /// so the _postUpdate() function can tell the difference
        /// between changed data and clean (pre-changed) data.
        $this->_postUpdate();

        /// Refresh the data just in case triggers in the RDBMS changed
        /// any columns.  Also this resets the _cleanData.
        $this->_refresh();

        ///
        /// Return the primary key value(s) as an array if the key is compound
        /// or a scalar if the key is a scalar.
        ///
        $primaryKey = $this->_getPrimaryKey(true);
        if (count($primaryKey) == 1) {
            return current($primaryKey);
        }

        return $primaryKey;
    }

    /**
     * Allows pre-save logic to be applied to row.
     *
     * @return void
     */
    protected function _save()
    {
    }

    /**
     * Allows post-save logic to be applied to row.
     *
     * @return void
     */
    protected function _postSave()
    {
    }

    /**
     * {@inheritdoc}
     * @internal
     */
    final protected function _getValueForId($value)
    {
        return $value instanceof static || $value instanceof \StdClass
            ? $value->id
            : intval($value);
    }

    /**
     * {@inheritdoc}
     * @internal
     */
    public function __call($method, array $args)
    {
        $column           = ZfRest_Util_String::dasherize(substr($method, 3));
        $isSetterOrGetter = substr($method, 0, 3);

        if ('get' === $isSetterOrGetter && $this->offsetExists($column)) {
            return $this->__get($column);

        } elseif ('set' === $isSetterOrGetter && $this->offsetExists($column)) {
            return $this->__set($column, $args[0]);

        } else {
            return parent::__call($method, $args);

        }
    }

    /**
     * {@inheritdoc}
     * @internal
     */
    public function __get($columnName)
    {
        $getter = ZfRest_Util_String::camelize($columnName, true);

        if (method_exists($this, "get{$getter}")) {
            return call_user_func_array([$this, "get{$getter}"], [$value]);

        } else {
            return parent::__get($columnName);

        }
    }

    /**
     * {@inheritdoc}
     * @internal
     */
    public function __set($columnName, $value)
    {
        $setter = ZfRest_Util_String::camelize($columnName, true);

        if (method_exists($this, "set{$setter}")) {
            $value = call_user_func_array([$this, "set{$setter}"], [$value]);
        }

        parent::__set($columnName, $value);
    }

    /**
     * Setter for `locale_id`
     * @return mixed
     */
    final protected function setLocaleId($value)
    {
        if ($this->offsetExists('locale_id')) {
            $locale = ZfRest_Model_Locale::getLocale($value);

            if (!$locale) {
                $this->pushError('locale_id', 'invalid', 'ERR.UNKNONWN_LOCALE', $value);
            } else {
                return $locale->id;
            }
        } else {
            return false;
        }
    }
}
