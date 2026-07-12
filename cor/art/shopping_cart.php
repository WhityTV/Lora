<?php

require_once __DIR__ . '/../lan/lan.php';
require_once __DIR__ . '/../inc/header.php';

class ShoppingCart extends Lan {
    public function __construct() {
    parent::__construct();
    }
}

$shoppingCart = new ShoppingCart();

mihiway_handle_syslang_post($shoppingCart);

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if (!isset($_SESSION['userId'])) {
    header('Location: ../log/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <script src="../theme.js"></script>
        <link rel="stylesheet" href="../theme.css">
    </head>
    <body>
        <?php mihiway_render_header($shoppingCart, ['rootPrefix' => '../', 'showMenu' => true, 'showLogo' => false, 'showAccount' => true]); ?>
        <h1><?php echo $shoppingCart->getLan('shopping_cart'); ?></h1>
        <?php mihiway_render_footer($shoppingCart, ['rootPrefix' => '../']); ?>
    </body>
</html>