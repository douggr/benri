<?php

/**
 * {@inheritdoc}
 */
class Benri_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Initialize rest routes.
     */
    protected function _initRestRoute()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->setResponse(new Benri_Controller_Response_Http());
        $front->setRequest(new Benri_Controller_Request_Http());

        $front->getRouter()
            ->addRoute('benri-application', new Zend_Rest_Route($front));
    }


    /**
     * Initialize the database resource.
     */
    protected function _initDbResource()
    {
        $registry = $this->getPluginResource('db');
        if (!$registry) {
            return;
        }

        //
        // options in configs/application
        $options = $registry->getOptions();

        if (array_key_exists('dsn', $options) && '' !== $options['dsn']) {
            $options['params'] = array_replace(
                $options['params'],
                $this->_parseDsn($options['dsn'])
            );
        }

        $registry->setOptions($options);
    }


    /**
     * Initialize multidb resources.
     */
    protected function _initMultiDbResources()
    {
        $registry = $this->getPluginResource('multidb');
        if (!$registry) {
            return;
        }

        //
        // options in configs/application
        $options = $registry->getOptions();

        foreach ($options as &$connection) {
            if ('db://' === substr($connection['dbname'], 0, 5)) {
                $connection = array_replace(
                    $connection,
                    $this->_parseDsn($connection['dbname'])
                );
            }
        }

        Zend_Registry::set('multidb', $registry->setOptions($options));
    }


    /**
     * Parse a DSN string and return return its components.
     *
     * If you want to use DSN, you **must** provide a valid AND complete DSN.
     *
     * Also, you are responsible to set both adapter and charset options, so
     * a valid DSN would looks something like this (DSNs MUST begin with `db://`):
     *      db://root:root@127.0.0.1/my_db or
     *      db://root:root@localhost/db_name
     *
     * @param string $dsn
     * @return array
     */
    private function _parseDsn($dsn)
    {
        $dsn = parse_url($dsn);
        $cfg = [];

        //
        // Some drivers (a.k.a. PDO_PGSQL) complains if the port is set
        // without a value, even NULL
        if (isset($dsn['port'])) {
            $cfg['port'] = $dsn['port'];
        }

        return $cfg + [
            'dbname'    => isset($dsn['path']) ? trim($dsn['path'], '/') : null,
            'host'      => isset($dsn['host']) ? $dsn['host']            : null,
            'password'  => isset($dsn['pass']) ? $dsn['pass']            : null,
            'username'  => isset($dsn['user']) ? $dsn['user']            : null,
        ];
    }
}
