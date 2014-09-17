<?php
/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 1.0.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/**
 * {@inheritdoc}
 */
class ZfRest_Model_Group extends ZfRest_Db_Table
{
    /**
     * {@inheritdoc}
     */
    protected $_rowClass = 'ZfRest_Model_Row_Group';

    /**
     * {@inheritdoc}
     */
    protected $_name = 'group';

    /**
     * {@inheritdoc}
     */
    protected $_primary = ['id', 'entity_id'];

    /**
     * {@inheritdoc}
     */
    public static function all($pageSize, $sort = null, $order = 'desc')
    {
        $user   = static::getAuthUser();
        $table  = new static();

        if (!$user || !$user->isSiteAdmin()) {
            return $table;
        }

        $model  = $table::create();
        $select = $table->select()
            ->where('entity_id = ?', static::getContext());

        if ($sort && $model->offsetExists($sort)) {
            $select->order("$sort $order");
        } else {
            $select->order("created_at $order");
        }

        $select->limitPage($pageSize->currentPage, $pageSize->pageSize);

        return $table->fetchAll($select);
    }
}
