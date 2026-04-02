<?php
require_once __DIR__ . '/../lan/lan.php';

class Register extends Lan {
    public function __construct() {
        parent::__construct();
    }
}

$register = new Register();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firstname'], $_POST['lastname'], $_POST['username'], $_POST['email'], $_POST['password'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $register->qry("INSERT INTO al_usr (firstname, lastname, username, mail, password) VALUES ('{$firstname}', '{$lastname}', '{$username}', '{$email}', '{$password}');");
    header("Location: ../log/login.php");
    exit;
}
?>

<html>
    <head>
        <link rel="stylesheet" href="register.css">
    </head>
    <body>
        <h1>Register</h1>
        <form method="post">
            <label for="firstname">
                <?php
                    echo($register->getLan('firstname'));
                ?>
            </label><br>
            <input type="text" id="firstname" name="firstname"><br>
            <label for="lastname">
                <?php
                    echo($register->getLan('lastname'));
                ?>
            </label><br>
            <input type="text" id="lastname" name="lastname"><br>
            <label for="username">
                <?php
                    echo($register->getLan('usrname'));
                ?>
            </label><br>
            <input type="text" id="username" name="username"><br>
            <label for="email">
                <?php
                    echo($register->getLan('email'));
                ?>
            </label><br>
            <input type="email" id="email" name="email"><br>
            <label for="password">
                <?php
                    echo($register->getLan('pass'));
                ?>
            </label><br>
            <input type="password" id="password" name="password"><br>
            <label for="confirm_password">
                <?php
                    echo($register->getLan('confirm_pass'));
                ?>
            </label><br>
            <input type="password" id="confirm_password" name="confirm_password">
            <p>
                <input type="submit" value="<?php echo $register->getLan('register'); ?>">
            </p>
        </form>
        <div class="language_buttons">
            <form method="post">
                <?php
                    $syslan = $register->getSysLan();
                    if ($syslan == "DE") {
                        echo '<button type="submit" name="syslang" value="EN"><img src="../../icons/UK.png" alt="EN" width="25" height="25"></button>';
                    } elseif ($syslan == "EN") {
                        echo '<button type="submit" name="syslang" value="DE"><img src="../../icons/DE.png" alt="DE" width="25" height="25"></button>';
                    }
                ?>
                <?php
                    if (isset($_POST['syslang']) && in_array($_POST['syslang'], ['EN', 'DE'])) {
                        $register->setSysLan($_POST['syslang']);
                        header("Refresh:0");
                    }
                ?>
            </form>
        </div>
    </body>
</html>