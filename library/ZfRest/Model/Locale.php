<?php
/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 1.1.3
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/**
 * {@inheritdoc}
 */
class ZfRest_Model_Locale extends ZfRest_Db_Table
{
    /**
     * {@inheritdoc}
     */
    protected $_rowClass = 'ZfRest_Model_Row_Locale';

    /**
     * {@inheritdoc}
     */
    protected $_name = 'locale';

    /**
     * {@inheritdoc}
     */
    protected $_primary = ['id'];

    /**
     * Locales buffer
     *
     * @var array
     */
    private static $_buffer = [];

    /**
     * {@inheritdoc}
     */
    public static function getLocale($locale)
    {
        if (is_object($locale)) {
            $locale = $locale->id;
            $column = 'id';

        } elseif (is_array($locale)) {
            $locale = $locale['id'];
            $column = 'id';

        } elseif (preg_match('/[^\d]+/', $locale)) {
            $column = 'code';
            $locale = str_replace('_', '-', $locale);

        } else {
            $locale = intval($locale);
            $column = 'id';

        }

        $data = implode('', [$column, $locale]);

        if (!array_key_exists($data, self::$_buffer)) {
            $model = static::locate($column, $locale);

            if ($model && $model->active) {
                self::$_buffer[$data] = $model;
            } else {
                self::$_buffer[$data] = null;
            }
        }

        return self::$_buffer[$data];
    }
}
