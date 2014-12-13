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
class ZfRest_Auth extends Zend_Auth
{
    /**
     * {@inheritdoc}
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }
}
