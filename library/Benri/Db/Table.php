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
     * Fetches all rows.
     *
     * @param integer $currentPage An SQL LIMIT offset
     * @param integer $pageSize An SQL LIMIT count
     * @param string|array $order An SQL ORDER clause
     * @return Benri_Db_Table_Row The row results
     */
    public static function all($currentPage = 1, $pageSize = 10, $order = null)
    {
        $table  = new static();
        $select = $table->select()
            ->order($order)
            ->limitPage($currentPage, $pageSize);

        return $table->fetchAll($select);
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
     * @param boolean $withFromPart Whether or not to include the from part of
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
}
