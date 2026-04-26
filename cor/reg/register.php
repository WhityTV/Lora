<?php
require_once __DIR__ . '/../lan/lan.php';

class Register extends Lan {
    public function __construct() {
        parent::__construct();
    }
}

$register = new Register();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firstname'], $_POST['lastname'], $_POST['username'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])) {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $passwordInput = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $errors = [];

    $allFieldsFilled = ($firstname !== '' && $lastname !== '' && $username !== '' && $email !== '' && $passwordInput !== '' && $confirmPassword !== '');
    if (!$allFieldsFilled) {
        $errors[] = $register->getLan('fill_all_fields');
    }

    if ($passwordInput !== $confirmPassword) {
        $errors[] = $register->getLan('pass_mismatch');
    }

    if (strlen($passwordInput) < 8) {
        $errors[] = $register->getLan('pass_too_short');
    }

    if (!preg_match('/\p{Lu}/u', $passwordInput)) {
        $errors[] = $register->getLan('pass_uppercase');
    }

    if (!preg_match('/\p{Ll}/u', $passwordInput)) {
        $errors[] = $register->getLan('pass_lowercase');
    }

    if (!preg_match('/\d/', $passwordInput)) {
        $errors[] = $register->getLan('pass_number');
    }

    if (!preg_match('/[^\p{L}\d\s]/u', $passwordInput)) {
        $errors[] = $register->getLan('pass_special_char');
    }

    $personalValues = [$firstname, $lastname, $username, $email];
    foreach ($personalValues as $personalValue) {
        if ($personalValue !== '' && stripos($passwordInput, $personalValue) !== false) {
            $errors[] = $register->getLan('pass_contains_personal_info');
            break;
        }
    }

    if (empty($errors)) {
        $password = password_hash($passwordInput, PASSWORD_ARGON2ID);
        $register->qry("INSERT INTO al_usr (firstname, lastname, username, mail, password) VALUES ('{$firstname}', '{$lastname}', '{$username}', '{$email}', '{$password}');");
        header("Location: ../log/login.php");
        exit;
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p>{$error}</p>";
        }
    }
}
?>

<html>
    <head>
        <link rel="stylesheet" href="register.css">
    </head>
    <body>
        <h1><?php echo $register->getLan('reg'); ?></h1>
        <form method="post">
            <label for="firstname">
                <?php
                    echo($register->getLan('firstname'));
                ?>
            </label><br>
            <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($_POST['firstname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><br>
            <label for="lastname">
                <?php
                    echo($register->getLan('lastname'));
                ?>
            </label><br>
            <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($_POST['lastname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><br>
            <label for="username">
                <?php
                    echo($register->getLan('usrname'));
                ?>
            </label><br>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><br>
            <label for="email">
                <?php
                    echo($register->getLan('email'));
                ?>
            </label><br>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><br>
            <label for="password">
                <?php
                    echo($register->getLan('pass'));
                ?>
            </label><br>
            <input type="password" id="password" name="password" autocomplete="new-password"><br>
            <label for="confirm_password">
                <?php
                    echo($register->getLan('confirm_pass'));
                ?>
            </label><br>
            <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password">
            <p>
                <input type="submit" value="<?php echo $register->getLan('register'); ?>">
                <input type="button" value="<?php echo $register->getLan('cancel'); ?>" onclick="window.location.href='../log/login.php';">
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