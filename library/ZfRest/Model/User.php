<?php
/**
 * douggr/zf-rest
 *
 * @license http://opensource.org/license/MIT
 * @link    https://github.com/douggr/zf-rest
 * @version 2.1.0
 */

/**
 * Class for SQL table interface.
 *
 * {@inheritdoc}
 */
class ZfRest_Model_User extends ZfRest_Db_Table_Abstract
{
    /**
     * Classname for row.
     *
     * @var string
     * @link ZfRest_Model_Row_User.html ZfRest_Model_Row_User
     */
    protected $_rowClass = 'ZfRest_Model_Row_User';

    /**
     * The table name.
     *
     * @var string
     */
    protected $_name = 'user';

    /**
     * Find a user using various fields as ID.
     *
     * @param mixed $id
     * @return ZfRest_Model_User_Row or null if the Model couldn't be found
     * @see ZfRest_Db_Table_Abstract::locate()
     */
    public static function findUserById($id)
    {
        if ('new' === $id) {
            return static::create();
        }

        // used with authenticated users.
        if ('me' === $id) {
            $auth = ZfRest_Auth::getInstance();
            if ($auth->hasIdentity()) {
                return static::locate('id', $auth->getIdentity()->id);
            }
        }

        if (preg_match('/\d+/', $id)) {
            // $id is numeric
            $column = 'id';

        } elseif (filter_var($id, FILTER_VALIDATE_EMAIL)) {
            // $id is a valid email
            $column = 'email';

        } else {
            // $id isn't either an integer or an email address
            $column = 'username';

        }

        // @see ZfRest_Db_Table_Abstract::locate()
        $model = static::locate($column, $id);

        if (!$model) {
            return null;
        } else {
            return $model;
        }
    }
}
