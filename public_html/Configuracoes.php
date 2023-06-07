<?php

namespace OliviaDatabasePublico;

use OliviaDatabaseModel\Model;

class Configuracoes extends Model
{
    protected $table = 'configuracoes';
    protected $drive = 'mysql';

    protected $fillable = [
        'parametro',
        'valor',
        'status'
    ];

    protected $atributos = [
        'id',
        'parametro',
        'valor',
        'status',
        'created_at'
    ];

    public function __construct()
    {
        $this->filtros['id'] = [
            'type' => 'number',
            'validate' => 'required',
            'size' => '10',
        ];

        $this->filtros['parametro'] = [
            'type' => 'string',
            'validate' => 'required',
            'size' => '50',
        ];
    }
}
