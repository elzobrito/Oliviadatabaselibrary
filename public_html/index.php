<?php

namespace Publico;

require dirname(__DIR__) . '/vendor/autoload.php';

$c = new Configuracoes();
print_r($c->all());