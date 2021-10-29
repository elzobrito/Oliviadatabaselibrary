<?php

namespace OliviaDatabaseConfig;

use OliviaDatabaseLibrary\ADatabase;

class Database extends ADatabase
{
    /*
    * Exemplo de uso do database
    */

    public static function getDB($driver = null)
    {
        $db = null;
        switch ($driver) {
            case 'mysql':
                $db = [
                    'host' => '',
                    'port' => '',
                    'database' => '',
                    'user' => '',
                    'password' => '',
                    'driver' => ''
                ];
                break;
            default:
                $db = [
                    'host' => '',
                    'port' => '',
                    'database' => '',
                    'user' => '',
                    'password' => '',
                    'driver' => ''
                ];
                break;
        }
        return $db;
    }
}
