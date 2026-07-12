<?php

require_once __DIR__ . '/../lan/lan.php';
require_once __DIR__ . '/../inc/header.php';

class Create extends Lan {
    public function __construct() {
    parent::__construct();
    }
}

$create = new Create();

mihiway_handle_syslang_post($create);

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
        <?php mihiway_render_header($create, ['rootPrefix' => '../', 'showMenu' => true, 'showLogo' => false, 'showAccount' => true]); ?>
        <h1><?php echo $create->getLan('create_art'); ?></h1>
        <?php mihiway_render_footer($create, ['rootPrefix' => '../']); ?>
    </body>
</html>