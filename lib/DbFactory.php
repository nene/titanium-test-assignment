<?php

/**
 * Creates PDO database object with settings from INI file with DB
 * connection settings.
 */
class DbFactory
{
    /**
     * Creates the PDO object.
     *
     * @param {string} $filename  Path to INI file.
     * @return {PDO} DB connection object.
     * @throws {Exception} When connection can't be established or INI file missing.
     */
    static function createFrom($filename)
    {
        $config = @parse_ini_file($filename);
        return new PDO("mysql:host={$config['host']};dbname={$config['dbname']}", $config['user'], $config['pass']);
    }
}
