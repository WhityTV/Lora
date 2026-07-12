<?php

function mihiway_normalize_prefix(string $rootPrefix): string {
    $rootPrefix = trim($rootPrefix);
    if ($rootPrefix === '') {
        return '';
    }

    return rtrim($rootPrefix, '/') . '/';
}

function mihiway_icons_prefix(string $rootPrefix): string {
    return mihiway_normalize_prefix($rootPrefix) . '../';
}

function mihiway_icon_url(string $fileName): string {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/';
    $scriptName = str_replace('\\', '/', $scriptName);
    $projectRoot = preg_replace('~/cor(?:/.*)?$~', '', $scriptName);

    if (!is_string($projectRoot) || $projectRoot === '') {
        $projectRoot = '/';
    }

    return $projectRoot . '/icons/' . ltrim($fileName, '/');
}

function mihiway_handle_syslang_post(Lan $app): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['syslang'])) {
        return;
    }

    if (!in_array($_POST['syslang'], ['EN', 'DE'], true)) {
        return;
    }

    $app->setSysLan($_POST['syslang']);
    header('Location: ' . ($_SERVER['REQUEST_URI'] ?? ''));
    exit;
}

function mihiway_get_account_name(Lan $app): string {
    $accountName = 'Account name';

    if (!isset($_SESSION['userId'])) {
        return $accountName;
    }

    $userId = (int) $_SESSION['userId'];
    $result = $app->qry("SELECT username FROM al_usr WHERE id = {$userId} LIMIT 1;");
    if ($result && $result->num_rows > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row && isset($row['username']) && $row['username'] !== '') {
            $accountName = $row['username'];
        }
    }

    return $accountName;
}

function mihiway_render_header(Lan $app, array $options = []): void {
    $rootPrefix = mihiway_normalize_prefix($options['rootPrefix'] ?? '');
    $iconsPrefix = mihiway_icons_prefix($rootPrefix);
    $showMenu = $options['showMenu'] ?? true;
    $showLogo = $options['showLogo'] ?? false;
    $showAccount = $options['showAccount'] ?? true;
    $accountName = $options['accountName'] ?? mihiway_get_account_name($app);

    $headerClasses = ['site_header'];
    if ($showMenu) {
        $headerClasses[] = 'site_header--with-menu';
    }
    if ($showLogo) {
        $headerClasses[] = 'site_header--with-logo';
    }

    echo '<header class="' . htmlspecialchars(implode(' ', $headerClasses), ENT_QUOTES, 'UTF-8') . '">';

    if ($showLogo) {
        echo '<img class="logo" src="' . htmlspecialchars(mihiway_icon_url('mihiway.png'), ENT_QUOTES, 'UTF-8') . '" alt="mihiway Logo" width="270" height="175">';
    }

    echo '<div class="top_controls">';
    echo '<label class="theme_toggle" title="Dark/Light Mode">';
    echo '<input type="checkbox" id="theme-toggle-checkbox">';
    echo '<span class="slider">';
    echo '<span class="stars"><img src="' . htmlspecialchars(mihiway_icon_url('stars.png'), ENT_QUOTES, 'UTF-8') . '" alt="Stars" width="16" height="16"></span>';
    echo '<span class="moon"><img src="' . htmlspecialchars(mihiway_icon_url('moon.png'), ENT_QUOTES, 'UTF-8') . '" alt="Moon" width="16" height="16"></span>';
    echo '<span class="bubble" aria-hidden="true"></span>';
    echo '<span class="sun" aria-hidden="true"><img src="' . htmlspecialchars(mihiway_icon_url('sun.png'), ENT_QUOTES, 'UTF-8') . '" alt="" width="15" height="15"></span>';
    echo '</span>';
    echo '</label>';

    echo '<div class="language_buttons">';
    echo '<form method="post" class="language_inline_toggle">';
    $syslan = $app->getSysLan();
    if ($syslan === 'DE') {
        echo '<button type="submit" name="syslang" value="EN" class="lang-combo-btn" aria-label="Switch to English" title="Switch to English">';
        echo '<img src="' . htmlspecialchars(mihiway_icon_url('DE.png'), ENT_QUOTES, 'UTF-8') . '" class="lang-flag flag-de" alt="DE" width="25" height="25">';
        echo '<span class="language_inline_arrow" aria-hidden="true">&#8644;</span>';
        echo '<img src="' . htmlspecialchars(mihiway_icon_url('UK.png'), ENT_QUOTES, 'UTF-8') . '" class="lang-flag flag-en" alt="EN" width="25" height="25">';
        echo '</button>';
    } elseif ($syslan === 'EN') {
        echo '<button type="submit" name="syslang" value="DE" class="lang-combo-btn" aria-label="Switch to German" title="Switch to German">';
        echo '<img src="' . htmlspecialchars(mihiway_icon_url('UK.png'), ENT_QUOTES, 'UTF-8') . '" class="lang-flag flag-en" alt="EN" width="25" height="25">';
        echo '<span class="language_inline_arrow" aria-hidden="true">&#8644;</span>';
        echo '<img src="' . htmlspecialchars(mihiway_icon_url('DE.png'), ENT_QUOTES, 'UTF-8') . '" class="lang-flag flag-de" alt="DE" width="25" height="25">';
        echo '</button>';
    }
    echo '</form>';
    echo '</div>';
    echo '</div>';

    if ($showMenu) {
        echo '<nav class="site_menu">';
        echo '<div class="site_menu_left">';
        echo '<span class="art_cat_wrapper">';
        echo '<span class="art_cat_trigger"><strong>' . htmlspecialchars($app->getLan('art'), ENT_QUOTES, 'UTF-8') . ' | </strong></span>';
        echo '<div class="art_cat">';
        echo '<p>';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'ser/search.php?cat=electronic_and_tech', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('electronic_and_tech'), ENT_QUOTES, 'UTF-8') . '</strong></a> | ';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'ser/search.php?cat=books', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('books'), ENT_QUOTES, 'UTF-8') . '</strong></a> | ';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'ser/search.php?cat=movies_series_music_games', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('movies_series_music_games'), ENT_QUOTES, 'UTF-8') . '</strong></a> | ';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'ser/search.php?cat=home_garden_diy', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('home_garden_diy'), ENT_QUOTES, 'UTF-8') . '</strong></a> | ';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'ser/search.php?cat=toys', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('toys'), ENT_QUOTES, 'UTF-8') . '</strong></a> | ';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'ser/search.php?cat=clothes_shoes', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('clothes_shoes'), ENT_QUOTES, 'UTF-8') . '</strong></a> | ';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'ser/search.php?cat=accessories_watches', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('accessories_watches'), ENT_QUOTES, 'UTF-8') . '</strong></a> | ';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'ser/search.php?cat=sport', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('sport'), ENT_QUOTES, 'UTF-8') . '</strong></a> | ';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'ser/search.php?cat=vehicles', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('vehicles'), ENT_QUOTES, 'UTF-8') . '</strong></a> | ';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'ser/search.php?cat=other', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('other'), ENT_QUOTES, 'UTF-8') . '</strong></a>';
        echo '</p>';
        echo '</div>';
        echo '</span>';
        echo '<span class="art_cat_wrapper">';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'art/saved.php', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('saved_art'), ENT_QUOTES, 'UTF-8') . ' | </strong></a>';
        echo '</span>';
        echo '<span class="art_cat_wrapper">';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'art/last_art.php', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('last_art'), ENT_QUOTES, 'UTF-8') . ' | </strong></a>';
        echo '</span>';
        echo '<span class="order_history">';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'art/his.php', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('order_history'), ENT_QUOTES, 'UTF-8') . '</strong></a>';
        echo '</span>';
        echo '</div>';

        echo '<form class="search-form" method="get" action="' . htmlspecialchars($rootPrefix . 'ser/search.php', ENT_QUOTES, 'UTF-8') . '">';
        echo '<img class="search-icon" src="' . htmlspecialchars(mihiway_icon_url('search.png'), ENT_QUOTES, 'UTF-8') . '" alt="Search" width="15" height="15">';
        echo '<input type="text" name="search_query" placeholder="">';
        echo '<button type="submit">' . htmlspecialchars($app->getLan('search'), ENT_QUOTES, 'UTF-8') . '</button>';
        echo '</form>';

        echo '<div class="site_menu_right">';
        echo '<span class="seller_dash">';
        echo '<a href="' . htmlspecialchars($rootPrefix . 'art/seller_dash.php', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('seller_dash'), ENT_QUOTES, 'UTF-8') . ' | </strong></a>';
        echo '</span>';
        echo '<span class="shopping_cart">';
        echo '<img src="' . htmlspecialchars(mihiway_icon_url('shopping_cart.png'), ENT_QUOTES, 'UTF-8') . '" alt="Shopping Cart" width="25" height="25">';
        $shoppingCartItems = property_exists($app, 'shopping_cart_items') ? (int) $app->shopping_cart_items : 0;
        echo '<a href="' . htmlspecialchars($rootPrefix . 'art/shopping_cart.php', ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($app->getLan('shopping_cart') . ': ' . $shoppingCartItems, ENT_QUOTES, 'UTF-8') . ' | </strong></a>';
        echo '</span>';

        if ($showAccount) {
            echo '<span class="my_acc_wrapper">';
            echo '<span class="my_acc"><strong>' . htmlspecialchars($app->getLan('my_acc'), ENT_QUOTES, 'UTF-8') . '</strong><img src="' . htmlspecialchars(mihiway_icon_url('dropdown.png'), ENT_QUOTES, 'UTF-8') . '" alt="Dropdown symbol" width="20" height="20"></span>';
            echo '<div class="my_acc_menu">';
            if (isset($_SESSION['userId'])) {
                echo '<div class="my_acc_item my_acc_name">' . htmlspecialchars($accountName, ENT_QUOTES, 'UTF-8') . '</div>';
                echo '<a class="my_acc_item" href="' . htmlspecialchars($rootPrefix . 'set/settings.php', ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($app->getLan('settings'), ENT_QUOTES, 'UTF-8') . '</a>';
                echo '<a class="my_acc_item" href="' . htmlspecialchars($rootPrefix . 'set/help.php', ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($app->getLan('help'), ENT_QUOTES, 'UTF-8') . '</a>';
                echo '<a class="my_acc_item" href="' . htmlspecialchars($rootPrefix . 'log/logout.php', ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($app->getLan('log_out'), ENT_QUOTES, 'UTF-8') . '</a>';
            } else {
                echo '<a class="my_acc_item" href="' . htmlspecialchars($rootPrefix . 'log/login.php', ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($app->getLan('login'), ENT_QUOTES, 'UTF-8') . '</a>';
                echo '<a class="my_acc_item" href="' . htmlspecialchars($rootPrefix . 'reg/register.php', ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($app->getLan('register'), ENT_QUOTES, 'UTF-8') . '</a>';
            }
            echo '</div>';
            echo '</span>';
        }

        echo '</div>';
        echo '</nav>';
    }

    echo '</header>';
}

function mihiway_render_footer(Lan $app, array $options = []): void {
    $rootPrefix = mihiway_normalize_prefix($options['rootPrefix'] ?? '');

    echo '<footer>';
    echo '<p>';
    echo '<a href="' . htmlspecialchars($rootPrefix . 'set/help.php', ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($app->getLan('help'), ENT_QUOTES, 'UTF-8') . ' | </a>';
    echo '<a href="' . htmlspecialchars($rootPrefix . 'image_credits.php', ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($app->getLan('image_credits'), ENT_QUOTES, 'UTF-8') . ' | </a>';
    echo '<a href="' . htmlspecialchars($rootPrefix . 'contact.php', ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($app->getLan('contact'), ENT_QUOTES, 'UTF-8') . ' | </a>';
    echo '<a href="' . htmlspecialchars($rootPrefix . 'privacy_policy.php', ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($app->getLan('privacy_policy'), ENT_QUOTES, 'UTF-8') . ' | </a>';
    echo '<a href="' . htmlspecialchars($rootPrefix . 'terms_of_service.php', ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($app->getLan('terms_of_service'), ENT_QUOTES, 'UTF-8') . ' | </a>';
    echo '<a href="' . htmlspecialchars($rootPrefix . 'imprint.php', ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($app->getLan('imprint'), ENT_QUOTES, 'UTF-8') . ' | </a>';
    echo '&copy; 2026 mihiway. All rights reserved.';
    echo '</p>';
    echo '</footer>';
}