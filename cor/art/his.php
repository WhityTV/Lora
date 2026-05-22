<?php

require_once __DIR__ . '/../lan/lan.php';

class His extends Lan {
    public function __construct() {
    parent::__construct();
    }
}

$his = new His();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if (!isset($_SESSION['userId'])) {
    header('Location: ../log/login.php');
    exit;
}
?>

<html>
    <body>
        <h1><?php echo $his->getLan('order_history'); ?></h1>
    </body>
</html>