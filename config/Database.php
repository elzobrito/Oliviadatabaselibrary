<?php

namespace OliviaDatabaseConfig;

use OliviaDatabaseLibrary\ADatabase;

class Database extends ADatabase
{
    private static $databases = [
        'mysql' => [
            'host' => '127.0.0.1',
            'port' => '',
            'database' => 'obs_escolar',
            'user' => 'root',
            'password' => '',
            'driver' => 'mysql',
            'ssl' => false
        ]
    ];

    public static function getDB($name = null)
    {
        if (!array_key_exists($name, self::$databases)) {
            throw new \Exception("Banco de dados inválido");
        }

        return self::$databases[$name];
    }
}
