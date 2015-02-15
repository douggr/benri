<?php
/**
 * Reference concrete class that extends Zend_Db_Table_Rowset_Abstract.
 *
 * @link http://framework.zend.com/manual/1.12/en/zend.db.table.rowset.html Zend_Db_Table_Rowset
 */
class Benri_Db_Table_Rowset extends Zend_Db_Table_Rowset
{
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
