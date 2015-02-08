<?php
/**
 * douggr/benri
 *
 * @license http://opensource.org/license/MIT
 * @link    https://github.com/douggr/benri
 * @version 1.0.0
 */

/**
 * Reference concrete class that extends Zend_Db_Table_Rowset_Abstract.
 * Developers may also create their own classes that extend the abstract class.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
