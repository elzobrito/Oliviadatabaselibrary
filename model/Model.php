<?php

namespace OliviaDatabaseModel;

use OliviaDatabaseConfig\Database;
use OliviaDatabaseLibrary\QueryBuilder;
use OliviaDatabaseLibrary\Model as iModel;

class Model implements iModel
{
    protected $table;
    protected $drive;
    protected $fillable = array();
    protected $atributos = array();

    /**
     * @var array
     */
    private $clausules = [];

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    function __call($name, $arguments)
    {
        $clausule = $arguments[0];
        if (count($arguments) > 1) {
            $clausule = $arguments;
        }
        $this->clausules[strtolower($name)] = $clausule;

        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function update($fields, $wheres, $values)
    {
        return (new QueryBuilder(Database::getDB($this->drive)))
            ->table($this->table)
            ->fields($fields)
            ->where($wheres)
            ->update($values);
    }
    public function save($fields = [], $valores = [])
    {
        return (new QueryBuilder(Database::getDB($this->drive)))
            ->table($this->table)
            ->fields(($fields ?: $this->fill()))
            ->insert(($valores ?: $this->values()));
    }

    public function delete($id = null, $primaryKey = 'id')
    {

        return (new QueryBuilder(Database::getDB($this->drive)))
            ->table($this->table)
            ->where([$primaryKey . ' = ?'])
            ->delete([$id ?? isset($this->id)]);
    }


    public function deleteFull($id = null, $primaryKey = null, $wheres = null, $values = null, $join = null, $group = null, $having = null, $limit = null)
    {
        if ($id || isset($this->id)) {
            $value = $id ?? isset($this->id);
            $qb = new QueryBuilder(Database::getDB($this->drive));
            return $qb
                ->table($this->table)
                ->where($wheres ?? [($primaryKey ?? 'id') . ' = ?'])
                ->join($join)
                ->group($group)
                ->having($having)
                ->limit($limit)
                ->delete($values ?? [$value]);
        }
    }

    public function find($fields, $wheres, $values, $join = null, $group = null, $order = null, $having = null, $limit = null)
    {
        return (new QueryBuilder(Database::getDB($this->drive)))
            ->table($this->table)
            ->fields($fields ?? $this->atributos)
            ->where($wheres)
            ->join($join)
            ->group($group)
            ->order($order)
            ->having($having)
            ->limit($limit)
            ->select($values);
    }


    public function all($fields = null, $order = null, $limit = null, $join = null, $group = null)
    {
        return (new QueryBuilder(Database::getDB($this->drive)))
            ->table($this->table)
            ->fields($fields ?? $this->atributos)
            ->order($order)
            ->limit($limit)
            ->join($join)
            ->group($group)
            ->select();
    }

    public function count($wheres = null, $values = null, $join = null, $group = null, $order = null, $having = null, $limit = null)
    {
        return (new QueryBuilder(Database::getDB($this->drive)))
            ->table($this->table)
            ->fields(['count(*) as total'])
            ->where($wheres)
            ->join($join)
            ->group($group)
            ->order($order)
            ->having($having)
            ->limit($limit)
            ->select($values);
    }

    public function findForId($id, $primaryKey = null, $fields = null, $join = null, $order = null, $limit = null)
    {
        return (new QueryBuilder(Database::getDB($this->drive)))
            ->table($this->table)
            ->fields($fields ?? $this->atributos)
            ->where([($primaryKey ?? 'id') . ' = ?'])
            ->join($join)
            ->order($order)
            ->limit($limit)
            ->select([$id]);
    }

    public function values()
    {
        $filds = [];
        $array = [];
        $filds = $this->fill();
        foreach ($this as $key => $value) {
            if (in_array($key, $filds))
                $array[] = $value;
        }
        return $array;
    }


    public function request_cripto()
    {
        $val_temp = null;
        foreach ($this->fillable as $key => $value)
            if (substr($value, 0, 2) == 'id') {
                if (isset($_REQUEST[$value]))
                    if (!is_numeric($_REQUEST[$value])) {
                        $val_temp = $this->decryptIt($_REQUEST[$value]);
                        if (is_numeric($val_temp)) {
                            $this->$value = $val_temp;
                        } else {
                            $this->$value = $_REQUEST[$value];
                        }
                    } else {
                        $this->$value = $_REQUEST[$value];
                    }
            } else {
                $this->$value = $_REQUEST[$value];
            }
    }

    public function request($fields = null)
    {
        $campos = $fields ?? $this->fillable;
        foreach ($campos as $key => $value)
            if (isset($_REQUEST[$value]))
                $this->$value = $_REQUEST[$value];
    }

    public function fill()
    {
        $array = [];
        foreach ($this->fillable as $key => $value) {
            $array[] = $value;
        }
        return $array;
    }
}
