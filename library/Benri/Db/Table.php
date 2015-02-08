<?php
/**
 * douggr/benri
 *
 * @license http://opensource.org/license/MIT
 * @link    https://github.com/douggr/benri
 * @version 1.0.0
 */

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
    public static function all($pageNumber = 1, $pageSize = 10, $order = null)
    {
        $table = new static();

        return $table->fetchAll(null, $pageNumber, $pageSize, $order);
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
            ->where("{$column} = ?", $value)
            ->limit(1);

        return $table->fetchRow($select);
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
     * Initialize database adapter.
     *
     * @return void
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
