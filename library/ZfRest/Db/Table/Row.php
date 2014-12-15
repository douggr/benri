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
class ZfRest_Db_Table_Row extends Zend_Db_Table_Row
{
    /// This means a required resource does not exist.
    const ERROR_MISSING         = 'missing';

    /// This means a required field on a resource has not been set.
    const ERROR_MISSING_FIELD   = 'missing_field';

    /// This means the formatting of a field is invalid. The documentation for
    /// that resource should be able to give you more specific information.
    const ERROR_INVALID         = 'invalid';

    /// This means another resource has the same value as this field. This can
    /// happen in resources that must have some unique key (such as Label or
    /// Locale names).
    const ERROR_ALREADY_EXISTS  = 'already_exists';

    /// This means an uncommon error.
    const ERROR_UNCATEGORIZED   = 'uncategorized';

    /// For the rare case an exception occurred and we couldn't recover.
    const ERROR_UNKNOWN         = 'unknown';

    /**
     * @var array
     */
    private $_errors = [];

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->_errors;
    }

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
    public function isNewRecord()
    {
        return empty($this->_cleanData);
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
    public function save()
    {
        if (true === $this->_readOnly) {
            throw new Zend_Db_Table_Row_Exception('This row has been marked read-only.');
        }

        /// Allows pre-save logic to be applied to any row.
        ///
        /// Zend_Db_Table_Row only uses to do it on _insert OR _update,
        /// here we can use the very same rules to be applied in both methods.
        $this->_save();

        if (count($this->_errors)) {
            throw new Zend_Db_Table_Row_Exception('This row contain errors.');
        }

        foreach ($this->_data as $column => &$value) {
            if ($value instanceof DateTime) {
                // Should replace with ZfRest_Util_DateTime.
                if (!($value instanceof ZfRest_Util_DateTime)) {
                    $value = new ZfRest_Util_DateTime($value->format('U'));
                }

                $value->setFormat('Y:m:d H:i:s');
            }
        }

        $identity = ZfRest_Auth::getInstance()->getIdentity();

        if ($identity && isset($identity->id)) {
            $createdBy = $identity->id;
        } else {
            $createdBy = 1;
        }

        if ($this->isNewRecord() && $this->offsetExists('created_by')) {
            if (!$this->created_by) {
                $this->created_by = $createdBy;
            }
        }

        if ($this->offsetExists('updated_by')) {
            if (!$this->updated_by) {
                $this->updated_by = $createdBy;
            }
        }

        /// Saves the properties to the database
        parent::save();

        /// Run post-SAVE logic
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
     * @internal
     */
    final protected function _getValueForId($value)
    {
        if ($value instanceof static || $value instanceof StdClass) {
            return $value->id;
        } else {
            return intval($value);
        }
    }

    /**
     * Allows post-save logic to be applied to row.
     *
     * @return void
     * @throws Zend_Db_Table_Row_Exception
     */
    protected function _postSave()
    {
    }

    /**
     * All error objects have field and code properties so that your client
     * can tell what the problem is.
     *
     * If resources have custom validation errors, they should be documented
     * with the resource.
     *
     * @param string $field The erroneous field or column
     * @param string $code One of the ERROR_* codes contants
     * @param string $message
     * @param array $interpolateParams Params to interpolate within the message
     * @return ZfRest_Db_Table_Abstract_Row
     */
    protected function _pushError($field, $code, $message = '', array $interpolateParams = [])
    {
        $this->_errors[] = [
            'field'     => $field,
            'code'      => $code,
            'message'   => vsprintf($message, $interpolateParams)
        ];

        return $this;
    }

    /**
     * Allows pre-save logic to be applied to row.
     *
     * @return void
     * @throws Zend_Db_Table_Row_Exception
     */
    protected function _save()
    {
    }

    /**
     * {@inheritdoc}
     * @internal
     */
    public function __call($method, array $args)
    {
        $columnName         = ZfRest_Util_String::dasherize(substr($method, 3));
        $isSetterOrGetter   = substr($method, 0, 3);
        $columnExists       = $this->offsetExists($columnName);

        if ($columnExists) {
            if ('get' === $isSetterOrGetter) {
                return $this->__get($columnName);
            } elseif ('set' === $isSetterOrGetter) {
                return $this->__set($columnName, $args[0]);
            }
        }

        return parent::__call($method, $args);
    }

    /**
     * {@inheritdoc}
     * @internal
     */
    public function __get($columnName)
    {
        $getter = ZfRest_Util_String::camelize($columnName, true);

        if (method_exists($this, "get{$getter}")) {
            return call_user_func_array([$this, "get{$getter}"], []);
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
}