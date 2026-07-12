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

mihiway_handle_syslang_post($cor);

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
?>

<html>
    <head>
        <script src="theme.js"></script>
        <link rel="stylesheet" href="theme.css">
    </head>
    <body>
        <?php mihiway_render_header($cor, ['rootPrefix' => '', 'showMenu' => true, 'showLogo' => false, 'showAccount' => true, 'accountName' => $myAccName]); ?>
        <h1><?php echo $cor->getLan('image_credits'); ?></h1>
        <div class="content">
            <ul>
                <li>Moon: <a href="https://www.flaticon.com/free-icons/dark" title="dark icons">Dark icon created by adriansyah - Flaticon</a></li>
                <li>Stars: <a href="https://www.flaticon.com/free-icons/sparkle" title="sparkle icons">Sparkle icon created by kornkun - Flaticon</a></li>
                <li>Sun: <a href="https://www.flaticon.com/free-icons/light-mode" title="light mode icons">Light mode icon created by Fantasyou - Flaticon</a></li>
            </ul>
        </div>
        <?php mihiway_render_footer($cor, ['rootPrefix' => '']); ?>
    </body>
</html>