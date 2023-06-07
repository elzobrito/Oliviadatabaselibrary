<?php

use OliviaCache\SessionCache;

require_once  __DIR__ . '/vendor/autoload.php';

class index
{
    public function __construct()
    {
        $sessionCache = new SessionCache('chave');
        $sessionCache->set('nome','Elzo Brito');
        echo $sessionCache->get('nome');
    }
}
new index();