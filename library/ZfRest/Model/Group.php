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
use ZfRest\Model\Exception\Group as Exception;

/**
 * {@inheritdoc}
 */
class Group extends Db\Table
{
    /**
     * {@inheritdoc}
     */
    protected $_rowClass = 'ZfRest\Model\Row\Group';

    /**
     * {@inheritdoc}
     */
    protected $_name = 'group';

    /**
     * {@inheritdoc}
     */
    protected $_primary = ['id', 'locale_id'];

    /**
     * {@inheritdoc}
     */
    public function loadUsers()
    {
        return UserToGroup::loadUsers($this->id);
    }
}
