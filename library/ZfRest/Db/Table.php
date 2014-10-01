<?php
/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 1.1.4
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/**
 * {@inheritdoc}
 */
class ZfRest_Db_Table extends Zend_Db_Table
{
    /**
     * {@inheritdoc}
     */
    protected $_primary = 'id';

    /**
     * {@inheritdoc}
     */
    protected $_rowClass = 'ZfRest_Db_Row';

    /**
     * @var ZfRest_Model_Row_User
     */
    private static $user;

    /**
     * @var string
     */
    private static $locale;

    /**
     * @var integer
     */
    private static $context;

    /**
     * {@inheritdoc}
     */
    public static function all($pageSize, $sort = null, $order = 'desc')
    {
        $table  = new static();
        $model  = $table::create();
        $select = $table->select();

        if ($model->offsetExists('entity_id')) {
            $select->where('entity_id = ?', static::getContext());
        }

        if ($sort && $model->offsetExists($sort)) {
            $select->order("$sort $order");
        }

        $select->limitPage($pageSize->currentPage, $pageSize->pageSize);

        return $table->fetchAll($select);
    }

    /**
     * {@inheritdoc}
     */
    public static function create($data = [])
    {
        return (new static())
            ->createRow((array) $data);
    }

    /**
     * {@inheritdoc}
     */
    final public static function getAuthUser()
    {
        return self::$user;
    }

    /**
     * {@inheritdoc}
     */
    public static function locate($column, $value)
    {
        $table  = new static();
        $select = $table->select()
            ->where("{$column} = ?", $value)
            ->limit(1);

        $result = $table->fetchAll($select);

        if ($result && $result->count()) {
            return $result[0];
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    final public static function setAuthUser($user)
    {
        self::$user = $user;
    }

    /**
     * {@inheritdoc}
     */
    final public static function getContext()
    {
        return self::$context;
    }

    /**
     * {@inheritdoc}
     */
    final public static function setContext($context)
    {
        self::$context = $context;
    }

    /**
     * {@inheritdoc}
     */
    final public static function getPreferredLocale()
    {
        return self::$locale;
    }

    /**
     * {@inheritdoc}
     */
    final public static function setPreferredLocale($locale)
    {
        self::$locale = $locale;
    }

    /**
     * Convert this object to a JSON string
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray(), JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK);
    }

    /**
     * @return void
     */
    protected function _setupDatabaseAdapter()
    {
        $this->_setAdapter(Zend_Registry::get('multidb'));
    }
}
