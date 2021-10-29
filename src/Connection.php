<?php

namespace OliviaDatabaseLibrary;

use \PDO;
use \PDOStatement;
use \Exception;
use \PDOException;

/**
 * Class Connection
 * @package elzobrito
 */
class Connection
{
    /**
     * @var PDO
     */
    private $pdo = null;
    /**
     * @var array
     */
    private $options = [];

    /**
     * Connection constructor.
     * @param array $options
     * @throws Exception
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return PDO
     */
    protected function connect()
    {
        if (!$this->pdo) {
            try {
                $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "UTF8"',
                    // PDO::MYSQL_ATTR_SSL_CA => __DIR__ . DIRECTORY_SEPARATOR . 'BaltimoreCyberTrustRoot.crt.pem'
                );
                $this->pdo = new PDO($this->dsn(), $this->options['user'], $this->options['password'], $options);
                // set the PDO error mode to exception
                // $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                $this->status = "Falha ao tentar conectar: \n" . $e->getMessage();
            }
        }
        return $this->pdo;
    }

    /**
     * @return string
     */
    protected function dsn()
    {
        return "{$this->options['driver']}:host={$this->options['host']};port={$this->options['port']};dbname={$this->options['database']}";
    }

    /**
     * @param $sql
     * @return PDOStatement
     */
    private final function statement($sql)
    {
        try {
            return $this->connect()->prepare($sql);
        } catch (PDOException $e) {
            $this->status = "Falha ao tentar conectar: \n" . $e->getMessage();
        }
    }

    /**
     * @param string $sql
     * @param array $values
     * @return string
     */
    protected final function executeInsert($sql, array $values)
    {
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $this->connect()->lastInsertId();
        }

        return null;
    }

    /**
     * @param string $sql
     * @param array $values
     * @return array
     */
    protected final function executeSelect($sql, array $values)
    {
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $statement->fetchAll(PDO::FETCH_OBJ);
        }

        return null;
    }

    /**
     * @param string $sql
     * @param array $values
     * @return int
     */
    protected final function executeUpdate($sql, array $values)
    {
        return $this->execute($sql, $values);
    }

    /**
     * @param string $sql
     * @param array $values
     * @return int
     */
    protected final function executeDelete($sql, array $values)
    {
        return $this->execute($sql, $values);
    }

    /**
     * @param $sql
     * @param array $values
     * @return int|null
     */
    protected final function execute($sql, array $values)
    {
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $statement->rowCount();
        }

        return null;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
