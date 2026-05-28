<?php

require_once __DIR__ . '/lan/lan.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['syslang']) && in_array($_POST['syslang'], ['EN', 'DE'], true)) {
    $cor->setSysLan($_POST['syslang']);
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<html>
    <head>
        <link rel="stylesheet" href="index.css">
        <script src="jquery-4.0.js"></script>
        <script src="index.js"></script>
    </head>
    <body>
        <img class="logo" src="../icons/mihiway.png" alt="mihiway Logo" width="300" height="175">
        <div class="menu">
            <div class="menu-left">
                <span class="art_cat_wrapper">
                    <?php
                        echo('<span class="art_cat_trigger"><strong>' . $cor->getLan('art') . ' | </strong></span>');
                    ?>
                    <div class="art_cat">
                        <p>
                            <a href="ser/search.php?cat=electronic_and_tech"><strong><?php echo $cor->getLan('electronic_and_tech'); ?></strong></a> |
                            <a href="ser/search.php?cat=books"><strong><?php echo $cor->getLan('books'); ?></strong></a> |
                            <a href="ser/search.php?cat=movies_series_music_games"><strong><?php echo $cor->getLan('movies_series_music_games'); ?></strong></a> |
                            <a href="ser/search.php?cat=home_garden_diy"><strong><?php echo $cor->getLan('home_garden_diy'); ?></strong></a> |
                            <a href="ser/search.php?cat=toys"><strong><?php echo $cor->getLan('toys'); ?></strong></a> |
                            <a href="ser/search.php?cat=clothes_shoes"><strong><?php echo $cor->getLan('clothes_shoes'); ?></strong></a> |
                            <a href="ser/search.php?cat=accessories_watches"><strong><?php echo $cor->getLan('accessories_watches'); ?></strong></a> |
                            <a href="ser/search.php?cat=sport"><strong><?php echo $cor->getLan('sport'); ?></strong></a> |
                            <a href="ser/search.php?cat=vehicles"><strong><?php echo $cor->getLan('vehicles'); ?></strong></a> |
                            <a href="ser/search.php?cat=other"><strong><?php echo $cor->getLan('other'); ?></strong></a>
                        </p>
                    </div>
                </span>
                <a href="art/saved.php"><strong><?php echo $cor->getLan('saved_art'); ?> | </strong></a>
                <span class="art_cat_wrapper">
                    <a href="art/last_art.php"><strong><?php echo $cor->getLan('last_art'); ?></strong></a>
                </span>
            </div>

            <form class="search-form" method="get" action="ser/search.php">
                <img class="search-icon" src="../icons/search.png" alt="Search" width="15" height="15">
                <input type="text" name="search_query" placeholder="">
                <button type="submit"><?php echo $cor->getLan('search'); ?></button>
            </form>

            <div class="menu-right">
                <span class="order_history">
                    <a href="art/his.php"><strong><?php echo $cor->getLan('order_history'); ?> | </strong></a>
                </span>
                <span class="seller_dash">
                    <a href="art/seller_dash.php"><strong><?php echo $cor->getLan('seller_dash'); ?> | </strong></a>
                </span>
                <span class="shopping_cart">
                    <img src="../icons/shopping_cart.png" alt="Shopping Cart" width="25" height="25">
                    <a href="art/shopping_cart.php"><strong><?php echo $cor->getLan('shopping_cart') . ': ' . $cor->shopping_cart_items; ?> | </strong></a>
                </span>
                <span class="my_acc_wrapper">
                    <span class="my_acc">
                        <strong><?php echo $cor->getLan('my_acc'); ?></strong>
                        <img src="../icons/dropdown.png" alt="Dropdown symbol" width="20" height="20">
                    </span>
                    <div class="my_acc_menu">
                        <?php if (isset($_SESSION['userId'])) { ?>
                        <div class="my_acc_item my_acc_name"><?php echo htmlspecialchars($myAccName, ENT_QUOTES, 'UTF-8'); ?></div>
                        <a class="my_acc_item" href="set/settings.php"><?php echo $cor->getLan('settings'); ?></a>
                        <a class="my_acc_item" href="set/help.php"><?php echo $cor->getLan('help'); ?></a>
                        <a class="my_acc_item" href="log/logout.php"><?php echo $cor->getLan('log_out'); ?></a>
                        <?php } else { ?>
                        <a class="my_acc_item" href="log/login.php"><?php echo $cor->getLan('login'); ?></a>
                        <a class="my_acc_item" href="reg/register.php"><?php echo $cor->getLan('register'); ?></a>
                        <?php } ?>
                    </div>
                </span>
            </div>
        </div>

        <div class="top_controls">
            <label class="theme_toggle" title="Dark/Light Mode">
                <input type="checkbox" id="theme-toggle-checkbox">
                <span class="slider">
                    <span class="stars"><img src="../icons/stars.png" alt="Stars" width="16" height="16"></span>
                    <span class="moon"><img src="../icons/moon.png" alt="Moon" width="16" height="16"></span>
                    <span class="bubble" aria-hidden="true"></span>
                    <span class="sun" aria-hidden="true"></span>
                </span>
            </label>

            <div class="language_buttons">
                <form method="post">
                    <?php
                        $syslan = $cor->getSysLan();
                        if ($syslan == "DE") {
                            echo '<button type="submit" name="syslang" value="EN"><img src="../icons/UK.png" alt="EN" width="25" height="25"></button>';
                        } elseif ($syslan == "EN") {
                            echo '<button type="submit" name="syslang" value="DE"><img src="../icons/DE.png" alt="DE" width="25" height="25"></button>';
                        }
                    ?>
                </form>
            </div>
        </div>
    </body>
</html>