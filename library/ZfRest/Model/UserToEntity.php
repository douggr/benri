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
class ZfRest_Model_UserToEntity extends ZfRest_Db_Table
{
    /**
     * {@inheritdoc}
     */
    protected $_rowClass = 'ZfRest_Model_Row_UserToEntity';

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
        $table  = new ZfRest_Model_Entity();
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
        $table  = new ZfRest_Model_User();
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(['us' => 'user'])
            ->join(['ue' => 'user_to_entity'], 'us.id = ue.user_id', [])
            ->where('ue.entity_id = ?', $entityId);

        return $table->fetchAll($select);
    }
}
