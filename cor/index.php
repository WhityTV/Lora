<?php

require_once __DIR__ . '/lan/lan.php';
require_once __DIR__ . '/inc/header.php';

class Cor extends Lan {
    public function __construct() {
        parent::__construct();
    }

    public $shopping_cart_items = 0;
}

$cor = new Cor();
$myAccName = 'Account name';

if (isset($_SESSION['userId'])) {
    $userId = (int) $_SESSION['userId'];
    $result = $cor->qry("SELECT username FROM al_usr WHERE id = {$userId} LIMIT 1;");
    if ($result && $result->num_rows > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row && isset($row['username']) && $row['username'] !== '') {
            $myAccName = $row['username'];
        }
    }
}

mihiway_handle_syslang_post($cor);
?>
<!DOCTYPE html>
<html>
    <head>
        <script src="theme.js"></script>
        <link rel="stylesheet" href="theme.css">
        <script src="jquery-4.0.js"></script>
    </head>
    <body class="homepage">
        <?php mihiway_render_header($cor, ['rootPrefix' => '', 'showMenu' => true, 'showLogo' => true, 'showAccount' => true, 'accountName' => $myAccName]); ?>
        <?php mihiway_render_footer($cor, ['rootPrefix' => '']); ?>
    </body>
</html>