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
abstract class ZfRest_Db_Table_Abstract extends Zend_Db_Table
{
    /**
     * {@inheritdoc}
     */
    protected $_primary = ['id'];

    /**
     * {@inheritdoc}
     */
    protected $_rowClass = 'ZfRest_Db_Table_Row';

    /**
     * {@inheritdoc}
     */
    public static function all($currentPage = 1, $pageSize = 10, $sort = null, $order = 'desc')
    {
        $table  = new static();
        $model  = $table::create();
        $select = $table->select();

        if ($sort && $model->offsetExists($sort)) {
            $select->order("$sort $order");
        }

        $select->limitPage($currentPage, $pageSize);

        return $table->fetchAll($select);
    }

    /**
     * {@inheritdoc}
     */
    public static function create($data = [])
    {
        return (new static())
            ->createRow((array) $data);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function select($withFromPart = self::SELECT_WITHOUT_FROM_PART)
    {
        return parent::select($withFromPart)
            ->setIntegrityCheck(false);
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
    protected function _setupDatabaseAdapter()
    {
        $this->_setAdapter(Zend_Registry::get('multidb'));
    }
}
