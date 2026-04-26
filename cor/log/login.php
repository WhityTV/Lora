<?php
require_once __DIR__ . '/../lan/lan.php';

class Login extends Lan {
    public function __construct() {
        parent::__construct();

    }
}

$login = new Login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $safeUsername = $login->esc($username);
    $result = $login->qry("SELECT password FROM al_usr WHERE username = '{$safeUsername}';");
    $password_hash = '';

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $password_hash = $row['password'] ?? '';
    }

    if ($password_hash !== '' && password_verify($password, $password_hash)) {
        header("Location: ../index.php");
        exit;
    } else {
        echo $login->getLan('login_failed');
    }
}
?>

<html>
    <head>
        <link rel="stylesheet" href="login.css">
    </head>
    <body>
        <h1>Login</h1>
        <form method="post">
            <label for="username">
                <?php
                    echo($login->getLan('usrname'));
                ?>
            </label><br>
            <input type="text" id="username" name="username" autocomplete="username"><br>
            <label for="password">
                <?php
                    echo($login->getLan('pass'));
                ?>
            </label><br>
            <input type="password" id="password" name="password" autocomplete="current-password"><br><br>
            <input type="submit" value="<?php echo $login->getLan('login'); ?>">
            <p>
                <a href="forgot_password.php"><?php echo $login->getLan('forgot_pass'); ?></a>
                |
                <a href="../reg/register.php"><?php echo $login->getLan('register'); ?></a>
            </p>
        </form>
        <div class="language_buttons">
            <form method="post">
                <?php
                    $syslan = $login->getSysLan();
                    if ($syslan == "DE") {
                        echo '<button type="submit" name="syslang" value="EN"><img src="../../icons/UK.png" alt="EN" width="25" height="25"></button>';
                    } elseif ($syslan == "EN") {
                        echo '<button type="submit" name="syslang" value="DE"><img src="../../icons/DE.png" alt="DE" width="25" height="25"></button>';
                    }
                ?>
                <?php
                    if (isset($_POST['syslang']) && in_array($_POST['syslang'], ['EN', 'DE'])) {
                        $login->setSysLan($_POST['syslang']);
                        header("Refresh:0");
                    }
                ?>
            </form>
        </div>
    </body>
</html>
