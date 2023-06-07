<?php

namespace OliviaDatabaseLibrary;

interface Model
{
    public function getTable();
    public function update($fields, $wheres, $values);
    public function save($fields = null, $valores = null);
    public function delete($id = null, $primaryKey = null);
    public function find($fields, $wheres, $values, $join = null, $group = null, $order = null, $having = null, $limit = null);
    public function all($fields = null, $order = null, $limit = null);
    public function count($wheres = null, $values = null, $join = null, $group = null, $order = null, $having = null, $limit = null);
    public function findForId($id, $primaryKey = null, $fields = null, $join = null, $order = null, $limit = null);
    public function values();
    public function request_cripto();
    public function request($fields = null);
    public function fill();
}
