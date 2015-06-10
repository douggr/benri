<?php
/**
 * Contains an individual row of a Benri_Db_Table object.
 *
 * This is a class that contains an individual row of a Benri_Db_Table object.
 * When you run a query against a Table class, the result is returned in a set
 * of Benri_Db_Table_Row objects. You can also use this object to create new
 * rows and add them to the database table.
 *
 * @link http://framework.zend.com/manual/1.12/en/zend.db.table.row.html Zend_Db_Table_Row
 */
class Benri_Db_Table_Row extends Zend_Db_Table_Row
{
    /**
     * This means a required resource does not exist.
     */
    const ERROR_MISSING         = 'missing';

    /**
     * This means a required field on a resource has not been set.
     */
    const ERROR_MISSING_FIELD   = 'missing_field';

    /**
     * This means the formatting of a field is invalid. The documentation for
     * that resource should be able to give you more specific information.
     */
    const ERROR_INVALID         = 'invalid';

    /**
     * This means another resource has the same value as this field. This can
     * happen in resources that must have some unique key (such as Label or
     * Locale names).
     */
    const ERROR_ALREADY_EXISTS  = 'already_exists';

    /**
     * This means an uncommon error.
     */
    const ERROR_UNCATEGORIZED   = 'uncategorized';

    /**
     * For the rare case an exception occurred and we couldn't recover.
     */
    const ERROR_UNKNOWN         = 'unknown';

    /**
     * Hold the errors while saving this object.
     *
     * @var array
     */
    private $_errors = array();

    /**
     * Returns the errors found while saving this object.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Returns true if the given column was modified since this object was
     * loaded from the database.
     *
     * @param string $column
     * @return bool
     */
    public function isDirty($column)
    {
        return array_key_exists($column, $this->_modifiedFields);
    }

    /**
     * Returns true if this is a new record on the database.
     *
     * @return bool
     */
    public function isNewRecord()
    {
        return empty($this->_cleanData);
    }

    /**
     *
     * @param $input
     * @return Benri_Db_Table_Row
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
     * Reset the value to the given column to its defaults.
     *
     * @param string $column
     */
    final public function reset($column)
    {
        if ($this->isDirty($column)) {
            $this->_data[$column] = $this->_cleanData[$column];
            unset($this->_modifiedFields[$column]);
        }
    }

    /**
     * Saves the properties to the database.
     *
     * This performs an intelligent insert/update, and reloads the properties
     * with fresh data from the table on success.
     *
     * @return mixed The primary key value(s), as an associative array if the
     *  key is compound, or a scalar if the key is single-column
     */
    final public function save()
    {
        if (true === $this->_readOnly) {
            throw new Zend_Db_Table_Row_Exception('This row has been marked read-only.');
        }

        /**
         * Allows pre-save logic to be applied to any row.
         *
         * Zend_Db_Table_Row only uses to do it on _insert OR _update,
         * here we can use the very same rules to be applied in both methods.
         */
        $this->_save();

        if (count($this->_errors)) {
            throw new Zend_Db_Table_Row_Exception('This row contain errors.');
        }

        foreach ($this->_data as $column => &$value) {
            if ($value instanceof DateTime) {
                // Should replace with Benri_Util_DateTime.
                if (!($value instanceof Benri_Util_DateTime)) {
                    $value = new Benri_Util_DateTime($value->format('U'));
                }

                $value->setFormat('Y-m-d H:i:s');
            }
        }

        if ($this->isNewRecord()) {
            if ($this->offsetExists('created_at')) {
                $this->created_at = new Benri_Util_DateTime();
                $this->created_at->setFormat('Y-m-d H:i:s');
            }
        }

        if ($this->offsetExists('updated_at')) {
            $this->updated_at = new Benri_Util_DateTime();
            $this->updated_at->setFormat('Y-m-d H:i:s');
        }

        /// Saves the properties to the database
        parent::save();

        /// Run post-SAVE logic
        $this->_postSave();

        /// chain
        return $this;
    }

    /**
     * Convert this object to a JSON string.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray(), JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK);
    }

    /**
     * @return mixed The primary key value(s), as an associative array if the
     *  key is compound, or a scalar if the key is single-column
     */
    final protected function _doInsert()
    {
        /// A read-only row cannot be saved.
        if ($this->_readOnly === true) {
            throw new Zend_Db_Table_Row_Exception('This row has been marked read-only');
        }

        /// Run pre-INSERT logic
        $this->_insert();

        if (count($this->_errors)) {
            throw new Zend_Db_Table_Row_Exception('This row contain errors.');
        }

        return parent::_doInsert();
    }

    /**
     * @return mixed The primary key value(s), as an associative array if the
     *  key is compound, or a scalar if the key is single-column
     */
    final protected function _doUpdate()
    {
        /// A read-only row cannot be saved.
        if ($this->_readOnly === true) {
            throw new Zend_Db_Table_Row_Exception('This row has been marked read-only');
        }

        /// Run pre-UPDATE logic
        $this->_update();

        if (count($this->_errors)) {
            throw new Zend_Db_Table_Row_Exception('This row contain errors.');
        }

        return parent::_doUpdate();
    }

    /**
     * Allows post-save logic to be applied to row.
     *
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
     * @param string $title A title for this error
     * @param string $message A friendly message
     * @return Benri_Db_Table_Row
     */
    protected function _pushError($field, $code, $title, $message = '')
    {
        $this->_errors[] = array(
            'field'     => $field,
            'message'   => $message,
            'code'      => $code,
            'title'     => $title
        );

        return $this;
    }

    /**
     * Allows pre-save logic to be applied to row.
     *
     * @throws Zend_Db_Table_Row_Exception
     */
    protected function _save()
    {
    }

    /**
     * Constructor.
     *
     * Supported params for $config are:-
     * - table       = class name or object of type Zend_Db_Table_Abstract
     * - data        = values of columns in this row.
     *
     * @param array $config OPTIONAL Array of user-specified config options
     * @throws Zend_Db_Table_Row_Exception
     */
    final public function __construct(array $config = array())
    {
        if (isset($config['table']) && $config['table'] instanceof Zend_Db_Table_Abstract) {
            $this->_table = $config['table'];
            $this->_tableClass = get_class($this->_table);
        } elseif ($this->_tableClass !== null) {
            $this->_table = $this->_getTableFromString($this->_tableClass);
        }

        if (isset($config['data'])) {
            if (!is_array($config['data'])) {
                throw new Zend_Db_Table_Row_Exception('Data must be an array.');
            }

            $this->setFromArray($this->_data = $config['data']);
            $this->_modifiedFields = array();
        }

        if (isset($config['stored']) && $config['stored'] === true) {
            $this->_cleanData = $this->_data;
        }

        if (isset($config['readOnly']) && $config['readOnly'] === true) {
            $this->setReadOnly(true);
        }

        // Retrieve primary keys from table schema
        if (($table = $this->_getTable())) {
            $info = $table->info();
            $this->_primary = (array) $info['primary'];
        }

        $this->init();
    }

    /**
     * @internal
     */
    public function __set($columnName, $value)
    {
        $setter = Benri_Util_String::camelize($columnName, true);

        if (method_exists($this, "set{$setter}")) {
            $value = call_user_func_array(array($this, "set{$setter}"), array($value));
        }

        return parent::__set($columnName, $value);
    }
}
