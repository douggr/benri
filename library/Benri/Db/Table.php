<?php
/**
 * The Benri_Db_Table class is an object-oriented interface to
 * database tables. It provides methods for many common operations on tables.
 *
 * @link http://framework.zend.com/manual/1.12/en/zend.db.table.html Zend_Db_Table
 */
class Benri_Db_Table extends Zend_Db_Table
{
    /**
     * The configuration entry to use when `multidb` is registered.
     *
     * @var string
     */
    protected $_connection;

    /**
     * The primary key column or columns.
     *
     * A compound key should be declared as an array. You may declare a
     * single-column primary key as a string. Prefer arrays.
     *
     * @var mixed
     */
    protected $_primary = array('id');

    /**
     * Classname for row
     *
     * @var string
     * @see Benri_Db_Table_Row
     */
    protected $_rowClass = 'Benri_Db_Table_Row';

    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'Benri_Db_Table_Rowset';

    /**
     * Fetches all rows.
     *
     * @param int $pageNumber An SQL LIMIT offset
     * @param int $pageSize An SQL LIMIT count
     * @param string|array $order An SQL ORDER clause
     * @return Benri_Db_Table_Row The row results
     */
    public static function all($pageNumber = 0, $pageSize = 10, $order = null)
    {
        $table = new static();

        return $table->fetchAll(null, $order, $pageSize, $pageNumber);
    }

    /**
     * Fetches a new blank row (not from the database).
     *
     * @param array $data Data to populate in the new row
     * @return Benri_Db_Table_Row
     */
    public static function create($data = array())
    {
        $table = new static();
        return $table->createRow((array) $data);
    }

    /**
     * Fetches one row in an object of type Benri_Db_Table_Row, or returns
     * null if no row matches the specified criteria.
     *
     * @param string $column The sql `where` clause
     * @param mixed $value The value to use against the `where` clause
     * @return Benri_Db_Table_Row or null The row results, or null if no row
     *  found
     */
    public static function locate($column, $value)
    {
        $table  = new static();
        $select = $table->select()
            ->where("{$table->getAdapter()->quoteIdentifier($column)} = ?", $value)
            ->limit(1);

        return $table->fetchRow($select);
    }


    /**
     * Create table rows in bulk mode.
     *
     * @param array $batch and array of column-value pairs.
     * @return int The number of affected rows.
     * @throws Zend_Db_Adapter_Exception
     */
    public static function bulkInsert(array $batch)
    {
        $table = new static();

        if (1 === sizeof($batch)) {
            return $table->insert(array_shift($batch));
        }

        $adapter    = $table->getAdapter();
        $counter    = 0;
        $sqlBinds   = [];
        $values     = [];

        //
        // Do some voodoo here...
        foreach ($batch as $i => $row) {
            $placeholders = [];

            foreach ($row as $column => $value) {
                $counter++;

                if ($adapter->supportsParameters('positional')) {
                    $placeholders[] = '?';
                    $values[]       = $value;

                } elseif ($adapter->supportsParameters('named')) {
                    $name           = ":col{$i}{$counter}";
                    $placeholders[] = $name;
                    $values[$name]  = $value;

                } else {
                    throw new Zend_Db_Adapter_Exception(sprintf(
                            '%s doesn\'t support positional or named binding',
                            get_class($table)
                        ));
                }
            }

            //
            // and more blacky magic over here...
            $sqlBinds[] = '(' . implode(',', $placeholders) . ')';
        }

        //
        // extract column names...
        $columns = array_keys($row);

        //
        // and quoteIdentifier() them.
        array_walk($columns, function (&$index) use ($adapter) {
                $index = $adapter->quoteIdentifier($index, true);
            });

        //
        // Shit, shit, shit! F U ZF.
        $spec = $adapter->quoteIdentifier(
                ($table->_schema ? "{$table->_schema}." : '') . $table->_name
            );

        //
        // Build the SQL using the placeholders...
        $sql = sprintf(
                'INSERT INTO %s (%s) VALUES %s',
                $spec,                  // fully table name
                implode(',', $columns), // column names
                implode(',', $sqlBinds) // placeholders
            );

        // Ready?
        $stmt = $adapter->prepare($sql);

        //
        // Fight!
        $stmt->execute($values);

        //
        // aaaaaaand voilá!
        return $stmt->rowCount();
    }


    /**
     * Returns an instance of a Zend_Db_Table_Select object.
     *
     * @param bool $withFromPart Whether or not to include the from part of
     *  the select based on the table
     * @return Zend_Db_Table_Select
     * @see http://framework.zend.com/manual/1.12/en/zend.db.select.html Zend_Db_Select
     */
    public function select($withFromPart = parent::SELECT_WITHOUT_FROM_PART)
    {
        return parent::select($withFromPart)
            ->setIntegrityCheck(false);
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
     * Create dynamic finders.
     *
     * Dynamic finders ease your life in a way to do queries quickly without
     * having to event instantiate a `Benri_Db_Table` class.
     *
     * <code>
     * // `where name = ?, "douggr"`
     * Person::whereNameEq('douggr');
     *
     * // `where phone IS NULL`
     * Person::wherePhoneIsNull();
     *
     * // `where id IN (?), $values`
     * SomeModel::whereIdIn(1, 2, 3, 4);
     * </code>
     *
     * @internal
     * @return Benri_Db_Table_Rowset
     * @throws Zend_Db_Table_Exception if invalid query
     */
    public static function __callStatic($method, array $args = [])
    {
        preg_match("/where(?P<column>[a-zA-Z]+)(?P<operator>Lt|Le|Gt|Ge|Eq|Ne|In|IsNull|IsNotNull)+/", $method, $matches);

        if (!$matches) {
            throw new Zend_Db_Table_Exception(sprintf(
                'Call to undefined method %s::%s()', get_called_class(), $method
            ));
        }

        //
        // PHP's black magic
        extract($matches);

        $table   = new static();
        $select  = $table->select();
        $options = [];
        $column  = $table->getAdapter()
            ->quoteIdentifier(Benri_Util_String::dasherize($column));

        foreach ($args as $key => $arg) {
            if (is_array($arg)) {
                $options = $arg;
                unset($args[$key]);
            }
        }

        foreach ($options as $method => $value) {
            call_user_func_array([$select, $method], (array) $value);
        }

        if ('IsNull' === $operator) {
            return $table->fetchAll($select->where("{$column} IS NULL"));
        }

        if ('IsNotNull' === $operator) {
            return $table->fetchAll($select->where("{$column} IS NOT NULL"));
        }

        switch ($operator) {
            case 'Lt':
                $operator = "< ?";
                break;

            case 'Le':
                $operator = '<= ?';
                break;

            case 'Gt':
                $operator = '> ?';
                break;

            case 'Ge':
                $operator = '>= ?';
                break;

            case 'Eq':
                $operator = '= ?';
                break;

            case 'Ne':
                $operator = '<> ?';
                break;

            case 'In':
                $operator = 'IN (?)';
                break;
        }

        return $table->fetchAll($select->where("{$column} {$operator}", $args));
    }


    /**
     * Initialize database adapter.
     *
     * @throws Zend_Db_Table_Exception
     */
    protected function _setupDatabaseAdapter()
    {
        if (Zend_Registry::isRegistered('multidb')) {
            $this->_setAdapter(Zend_Registry::get('multidb')->getDb($this->_connection));
        } else {
            parent::_setupDatabaseAdapter();
        }
    }
}
