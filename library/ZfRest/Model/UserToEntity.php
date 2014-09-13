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
use ZfRest\Model\Exception\UserToEntity as Exception;

/**
 * {@inheritdoc}
 */
class UserToEntity extends Db\Table
{
    /**
     * {@inheritdoc}
     */
    protected $_rowClass = 'ZfRest\Model\Row\UserToEntity';

    /**
     * {@inheritdoc}
     */
    protected $_name = 'user_to_entity';

    /**
     * {@inheritdoc}
     */
    protected $_primary = ['user_id', 'entity_id'];

    /**
     * {@inheritdoc}
     */
    public static function loadEntities($userId)
    {
        $table  = new Entity();
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(['en' => 'entity'])
            ->join(['ue' => 'user_to_entity'], 'en.id = ue.entity_id', [])
            ->where('ue.user_id = ?', $userId);

        return $table->fetchAll($select);
    }

    /**
     * {@inheritdoc}
     */
    public static function loadUsers($entityId)
    {
        $table  = new User();
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(['us' => 'user'])
            ->join(['ue' => 'user_to_entity'], 'us.id = ue.user_id', [])
            ->where('ue.entity_id = ?', $entityId);

        return $table->fetchAll($select);
    }
}
