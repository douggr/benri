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
class ZfRest_Model_Entity extends ZfRest_Db_Table
{
    /**
     * {@inheritdoc}
     */
    protected $_rowClass = 'ZfRest_Model_Row_Entity';

    /**
     * {@inheritdoc}
     */
    protected $_name = 'entity';

    /**
     * {@inheritdoc}
     */
    protected $_primary = ['id'];

    /**
     * {@inheritdoc}
     */
    public static function all($pageSize, $sort = null, $order = 'desc')
    {
        $user   = static::getAuthUser();
        $table  = new static();
        $model  = $table::create();
        $select = $table->select();

        if ($model->offsetExists('entity_id')) {
            $select->where('entity_id = ?', static::getContext());
        }

        if ($sort && $model->offsetExists($sort)) {
            $select->order("$sort $order");
        } else {
            $select->order("created_at $order");
        }

        if (!$user || ($user && !$user->admin)) {
            $select->where('visibility = ?', 'PUBLIC');
        }

        $select->limitPage($pageSize->currentPage, $pageSize->pageSize);

        return $table->fetchAll($select);
    }
}
