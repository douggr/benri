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
use ZfRest\Model\Exception\UserToGroup as Exception;

/**
 * {@inheritdoc}
 */
class UserToGroup extends Db\Table
{
    /**
     * {@inheritdoc}
     */
    protected $_rowClass = 'ZfRest\Model\Row\UserToGroup';

    /**
     * {@inheritdoc}
     */
    protected $_name = 'user_to_group';

    /**
     * {@inheritdoc}
     */
    protected $_primary = ['user_id', 'group_id'];

    /**
     * {@inheritdoc}
     */
    public static function loadGroups($userId)
    {
        $table  = new Group();
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(['gr' => 'group'])
            ->join(['ug' => 'user_to_group'], 'gr.id = ug.group_id', [])
            ->where('ug.user_id = ?', $userId);

        return $table->fetchAll($select);
    }

    /**
     * {@inheritdoc}
     */
    public static function loadUsers($groupId)
    {
        $table  = new User();
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(['us' => 'user'])
            ->join(['ug' => 'user_to_group'], 'us.id = ug.user_id', [])
            ->where('ug.group_id = ?', $groupId);

        return $table->fetchAll($select);
    }
}
