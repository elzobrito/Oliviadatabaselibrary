<?php
namespace Publico;

use OliviaDatabaseLibrary\Model\Model;

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
}
