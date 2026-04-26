<?php

require_once __DIR__ . '/lan/lan.php';

class Cor extends Lan {
    public function __construct() {
    parent::__construct();
    }
}

$cor = new Cor();
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
            <span class = "art_cat_wrapper">
                <?php
                    echo('<span class="art_cat_trigger"><strong>' . $cor->getLan('art') . ' | </strong></span>');
                ?>
                <div class = "art_cat">
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
            <span class = "art_cat_wrapper">
                <strong class="last_art"><?php echo $cor->getLan('last_art'); ?></strong>

                <div class = "art_cat">
                    <?php include 'art/last._art.php'; ?>
                </div>
            </span>
        </div>


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
                <?php
                    if (isset($_POST['syslang']) && in_array($_POST['syslang'], ['EN', 'DE'])) {
                        $cor->setSysLan($_POST['syslang']);
                        header("Refresh:0");
                    }
                ?>
            </form>
        </div>
    </body>