<?php

/**
 * @link http://framework.zend.com/manual/1.12/en/zend.application.html Zend_Application
 */
class Benri_Application extends Zend_Application
{
    /**
     * Singleton instance.
     *
     * @var Benri_Application
     */
    protected static $_instance;


    /**
     * Returns an instance of Benri_Application.
     *
     * Singleton pattern implementation
     *
     * @return Benri_Application
     */
    public static function getInstance()
    {
        return self::$_instance;
    }


    /**
     * Create and returns an instance of Benri_Application.
     */
    public static function createInstance($environment, $options = null)
    {
        if (!self::$_instance) {
            self::$_instance = new static($environment, $options, false);
        }

        return self::$_instance;
    }
}
