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
class ZfRest_Model_User extends ZfRest_Db_Table
{
    /**
     * {@inheritdoc}
     */
    protected $_rowClass = 'ZfRest_Model_Row_User';

    /**
     * {@inheritdoc}
     */
    protected $_name = 'user';

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

        if ($sort && $model->offsetExists($sort)) {
            $select->order("$sort $order");
        } else {
            $select->order("created_at $order");
        }

        $select->limitPage($pageSize->currentPage, $pageSize->pageSize);

        return $table->fetchAll($select);
    }

    /**
     * {@inheritdoc}
     */
    public static function loadWithPermissions($token, $context)
    {
        $entity = intval($context);
        $table  = new static();

        // Find users matching 3 criterias + the given token:
        //  - if the user has access within the given context;
        //  - if the user is a site admin within the given context;
        //  - if the user is a system admin
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(['us' => 'user'])
            ->join(['ue' => 'user_to_entity'], 'us.id = ue.user_id', [])
            ->join(['en' => 'entity'], 'ue.entity_id = en.id', ['en.id as entity'])
            ->where('us.token = ?', $token)
            ->where("en.id = ? OR us.admin = true", $entity);

        $model = $table->fetchRow($select->limit(1))
;
        if (!$model) {
            // user not found OR user can't access the given context
            return null;
        }

        if ($model->admin) {
            // can access everything…
            $collection = [$model];

        } else {
            // filter the user against the groups he belongs to…
            $select
                ->join(['ug' => 'user_to_group'], 'us.id = ug.user_id', [])
                ->join(['gr' => 'group'], 'ug.group_id = gr.id', ['gr.id as gid', 'gr.admin as gadmin'])
                ->where('gr.entity_id = ?', $entity);

            $collection = $table->fetchAll($select);
        }

        $permissions = [];

        foreach ($collection as $model) {
            if ($model->admin) {
                break;
            }

            if (!isset($permissions[$model->entity])) {
                $permissions[$model->entity] = [];
            }

            $permissions[$model->entity][] = [$model->gid, intval($model->gadmin)];
        }

        $model->permissions = $permissions;

        return $model;
    }
}
