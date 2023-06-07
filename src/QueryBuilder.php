<?php

namespace OliviaDatabaseLibrary;

use InvalidArgumentException;
use OliviaCache\SessionCache;

class QueryBuilder extends Connection
{
    /**
     * As cláusulas armazenadas.
     *
     * @var array
     */
    private $clauses = [];

    /**
     * Chama um método para definir uma cláusula.
     *
     * @param string $name      O nome do método chamado.
     * @param array  $arguments Os argumentos passados para o método.
     *
     * @throws InvalidArgumentException Se o nome da cláusula for inválido.
     *
     * @return $this
     */
    public function __call($name, $arguments)
    {
        $validClauses = ['table', 'fields', 'join', 'pre', 'where', 'group', 'order', 'having', 'limit'];
        if (!in_array(strtolower($name), $validClauses)) {
            throw new InvalidArgumentException('Nome de cláusula inválido: ' . $name);
        }

        $clause = $arguments[0];
        if (count($arguments) > 1) {
            $clause = $arguments;
        }
        $this->clauses[strtolower($name)] = $clause;

        return $this;
    }

    /**
     * Construtor da classe QueryBuilder.
     *
     * @param array $options As opções de configuração do banco de dados.
     */
    public function __construct($options)
    {
        parent::__construct($options);
    }

    /**
     * Executa uma instrução INSERT.
     *
     * @param array $values Os valores a serem inseridos.
     *
     * @throws InvalidArgumentException Se as cláusulas 'table' e 'fields' não estiverem definidas.
     *
     * @return mixed O resultado da execução da instrução INSERT.
     */
    public function insert($values)
    {
        $this->validateClause('table');
        $this->validateClause('fields');

        $table = $this->clauses['table'];
        $fields = implode(', ', $this->clauses['fields']);
        $placeholders = implode(', ', array_fill(0, count($this->clauses['fields']), '?'));

        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";

        $cache = new SessionCache($table);
        $cache->delete();

        return $this->executeInsert($sql, $values);
    }

    /**
     * Executa uma instrução SELECT.
     *
     * @param array $values Os valores a serem usados nos placeholders da consulta.
     *
     * @throws InvalidArgumentException Se as cláusulas 'table' e 'fields' não estiverem definidas.
     *
     * @return mixed O resultado da execução da instrução SELECT.
     */
    public function select($values = [])
    {
        $this->validateClause('table');
        $this->validateClause('fields');

        $table = $this->clauses['table'];
        $fields = implode(', ', $this->clauses['fields']);
        $join = $this->clauses['join'] ?? '';
        $preSelect = $this->clauses['pre'] ?? '';

        $sql = $preSelect . ' SELECT ' . $fields . ' FROM ' . $table;

        if ($join) {
            $sql .= ' ' . $join;
        }

        foreach (['where', 'group', 'order', 'having', 'limit'] as $key) {
            if (isset($this->clauses[$key])) {
                $sql .= ' ' . $this->clauses[$key]['instruction'] . ' ';
                $sql .= implode($this->clauses[$key]['separator'], $this->clauses[$key]) . ' ';
            }
        }

        $cache = new SessionCache($table);
        $cacheKey = md5($sql . json_encode($values) . $table);

        $result = $cache->get($cacheKey);

        if ($result === null) {
            $result = $this->executeSelect($sql, $values);
            $cache->set($cacheKey, $result);
        }

        return $result;
    }

    /**
     * Executa uma instrução UPDATE.
     *
     * @param array $values  Os valores a serem atualizados.
     * @param array $filters Os filtros para a cláusula WHERE.
     *
     * @throws InvalidArgumentException Se as cláusulas 'table' e 'fields' não estiverem definidas.
     *
     * @return mixed O resultado da execução da instrução UPDATE.
     */
    public function update($values, $filters = [])
    {
        $this->validateClause('table');
        $this->validateClause('fields');

        $table = $this->clauses['table'];
        $join = $this->clauses['join'] ?? '';
        $sets = is_array($this->clauses['fields']) ? implode(', ', array_map(fn ($value) => $value . ' = ?', $this->clauses['fields'])) : $this->clauses['fields'];

        $whereClausules = $this->clauses['where'] ?? [];
        $whereArray = implode(' ', $whereClausules);
        $where = !empty($whereArray) ? ' WHERE ' . $whereArray : '';

        $sql = "UPDATE {$table} ";
        if ($join) {
            $sql .= "{$join} ";
        }
        $sql .= "SET {$sets}{$where}";

        $cache = new SessionCache($table);
        $cache->delete();

        return $this->executeUpdate($sql, array_merge($values, $filters));
    }

    /**
     * Executa uma instrução DELETE.
     *
     * @param array $filters Os filtros para a cláusula WHERE.
     *
     * @throws InvalidArgumentException Se a cláusula 'table' não estiver definida.
     *
     * @return mixed O resultado da execução da instrução DELETE.
     */
    public function delete($filters = [])
    {
        $this->validateClause('table');

        $table = $this->clauses['table'];
        $where = $this->clauses['where'] ?? null;

        $sql = ['DELETE FROM', $table];

        if (isset($this->clauses['join'])) {
            $sql[] = $this->clauses['join'];
        }

        if ($where !== null) {
            $sql[] = 'WHERE';
            if (is_array($where)) {
                $sql[] = implode(' ', $where);
            } else {
                $sql[] = $where;
            }
        }

        $sql = implode(' ', $sql);

        $cache = new SessionCache($table);
        $cache->delete();

        return $this->executeDelete($sql, $filters);
    }

    /**
     * Executa uma instrução DELETE completa.
     *
     * @param array $values  Os valores a serem utilizados na instrução DELETE.
     * @param array $filters Os filtros para a cláusula WHERE.
     *
     * @throws InvalidArgumentException Se a cláusula 'table' não estiver definida.
     *
     * @return mixed O resultado da execução da instrução DELETE completa.
     */
    public function deleteFull($values, $filters = [])
    {
        $this->validateClause('table');

        $table = $this->clauses['table'];
        $join = $this->clauses['join'] ?? '';

        $command = ['DELETE ' . $table . '.* FROM ' . $table, $join,];

        $clauses = [
            'where' => 'WHERE',
        ];

        foreach ($clauses as $key => $instruction) {
            if (isset($this->clauses[$key])) {
                $value = $this->clauses[$key];
                if (is_array($value)) {
                    $value = implode(' ', $value);
                }
                $command[] = $instruction . ' ' . $value;
            }
        }

        $sql = implode(' ', array_filter($command));

        $cache = new SessionCache($table);
        $cache->delete();

        return $this->executeDelete($sql, array_merge($values, $filters));
    }

    /**
     * Valida se uma cláusula está definida.
     *
     * @param string $name O nome da cláusula a ser validada.
     *
     * @throws InvalidArgumentException Se a cláusula não estiver definida.
     */
    private function validateClause($name)
    {
        if (!isset($this->clauses[$name])) {
            throw new InvalidArgumentException('A cláusula "' . $name . '" é obrigatória');
        }
    }
}