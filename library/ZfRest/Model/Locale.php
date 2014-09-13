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
use ZfRest\Model\Exception\Locale as Exception;

/**
 * {@inheritdoc}
 */
class Locale extends Db\Table
{
    /**
     * {@inheritdoc}
     */
    protected $_rowClass = 'ZfRest\Model\Row\Locale';

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
