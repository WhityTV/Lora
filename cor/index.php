<?php

require_once __DIR__ . '/lan/lan.php';

class Cor extends Lan {
    public function __construct() {
    }
    
    public function cor() {
        echo '<html><h1>' . $this->getLan('home') . '</h1></html>';
    }
}

$cor = new Cor();
$cor->cor();