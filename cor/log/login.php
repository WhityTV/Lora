<?php
require_once __DIR__ . '/../lan/lan.php';

class Login extends Lan {
    public function __construct() {
        parent::__construct();
    }
    public function getUsrname() {
        return $this->getLan('usrname');
    }
    public function getPass() {
        return $this->getLan('pass');
    }
}

$login = new Login();
?>

<html>
    <head>
        <link rel="stylesheet" href="login.css">
    </head>
    <body>
        <h1>Login</h1>
        <form>
            <label for="username">
                <?php
                    echo($login->getUsrname());
                ?>
            </label><br>
            <input type="text" id="username" name="username"><br>
            <label for="password">
                <?php
                    echo($login->getPass());
                ?>
            </label><br>
            <input type="password" id="password" name="password"><br><br>
            <input type="submit" value="Login">
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
