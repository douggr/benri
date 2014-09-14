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

namespace ZfRest\Model;

use ZfRest\Db;
use ZfRest\Model\Exception\Entity as Exception;

/**
 * {@inheritdoc}
 */
class Entity extends Db\Table
{
    /**
     * {@inheritdoc}
     */
    protected $_rowClass = 'ZfRest\Model\Row\Entity';

    /**
     * {@inheritdoc}
     */
    protected $_name = 'entity';

    /**
     * {@inheritdoc}
     */
    protected $_primary = ['id', 'locale_id'];

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

        if (!$user || !$user->admin) {
            $select->where('visibility = ?', 'PUBLIC');
        }

        $select->limitPage($pageSize->currentPage, $pageSize->pageSize);

        return $table->fetchAll($select);
    }
}
