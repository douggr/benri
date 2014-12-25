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
class ZfRest_Model_User extends ZfRest_Db_Table_Abstract
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
     * Find a user using various fields as ID.
     *
     * @return ZfRest_Model_User_Row
     */
    public static function findUserById($id)
    {
        if ('new' === $id) {
            return static::create();
        }

        if ('me' === $id) {
            $auth = ZfRest_Auth::getInstance();
            if ($auth->hasIdentity()) {
                return static::locate('id', $auth->getIdentity()->id);
            }
        }

        if (preg_match('/\d+/', $id)) {
            $column = 'id';
        } elseif (filter_var($id, FILTER_VALIDATE_EMAIL)) {
            $column = 'email';
        } else {
            $column = 'username';
        }

        $model = static::locate($column, $id);

        if (!$model) {
            return null;
        } else {
            return $model;
        }
    }
}
