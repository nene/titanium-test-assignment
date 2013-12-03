<?php

/**
 * Creates PDO database object with settings from db-config.ini file.
 */
class DbFactory
{
    /**
     * Creates the PDO object.
     * @return {PDO}
     */
    static function create()
    {
        $config = parse_ini_file(dirname(dirname(__FILE__)) . "/db-config.ini");
        return new PDO("mysql:host={$config['host']};dbname={$config['dbname']}", $config['user'], $config['pass']);
    }
}
