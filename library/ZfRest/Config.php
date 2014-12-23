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
class ZfRest_Config
{
    /**
     * {@inheritdoc}
     */
    static private $_config;

    /**
     * {@inheritdoc}
     */
    public function get($index)
    {
        if (!self::$_config) {
            self::$_config = Zend_Controller_Front::getInstance()
                ->getInvokeArg('bootstrap');
        }

        return self::$_config->getOption($index);
    }
}
