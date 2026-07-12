<?php
require_once __DIR__ . '/../lan/lan.php';
require_once __DIR__ . '/../inc/header.php';

class Login extends Lan {
    public function __construct() {
        parent::__construct();

    }
}

$login = new Login();
mihiway_handle_syslang_post($login);
$statusMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $safeUsername = $login->esc($username);
    $result = $login->qry("SELECT id, password, login_attempts, disabled FROM al_usr WHERE username = '{$safeUsername}' LIMIT 1;");
    $userRow = ($result && $result->num_rows > 0) ? mysqli_fetch_assoc($result) : null;

    if ($userRow && (($userRow['disabled'] ?? 'N') === 'Y')) {
        $statusMsg = $login->getLan('account_disabled');
    } elseif ($userRow && isset($userRow['password']) && password_verify($password, $userRow['password'])) {
        $userId = (int) $userRow['id'];
        $_SESSION['userId'] = $userId;
        $login->qry("UPDATE al_usr SET login_attempts = 0 WHERE id = {$userId};");
        header("Location: ../index.php");
        exit;
    } else {
        if ($userRow && isset($userRow['id'])) {
            $userId = (int) $userRow['id'];
            $attempts = (int) ($userRow['login_attempts'] ?? 0) + 1;

            if ($attempts >= 3) {
                $login->qry("UPDATE al_usr SET login_attempts = {$attempts}, disabled = 'Y' WHERE id = {$userId};");
                $statusMsg = $login->getLan('account_disabled');
            } else {
                $login->qry("UPDATE al_usr SET login_attempts = {$attempts} WHERE id = {$userId};");
                $statusMsg = $login->getLan('login_failed');
            }
        } else {
            $statusMsg = $login->getLan('login_failed');
        }
    }
}
?>

<html>
    <head>
        <script src="../theme.js"></script>
        <link rel="stylesheet" href="../theme.css">
        <link rel="stylesheet" href="login.css">
    </head>
    <body>
        <?php mihiway_render_header($login, ['rootPrefix' => '../', 'showMenu' => false, 'showLogo' => false, 'showAccount' => false]); ?>
        <h1>Login</h1>
        <?php
        if ($statusMsg !== '') {
        ?>
            <p>
                <?php
                echo htmlspecialchars($statusMsg, ENT_QUOTES, 'UTF-8');
                ?>
            </p>
        <?php
        }
        ?>
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
            <div class="form_actions">
                <input type="submit" value="<?php echo $login->getLan('login'); ?>">
                <p class="auth_links">
                <a href="forgot_password.php"><?php echo $login->getLan('forgot_pass'); ?></a>
                |
                <a href="../reg/register.php"><?php echo $login->getLan('register'); ?></a>
                </p>
            </div>
        </form>
        <?php mihiway_render_footer($login, ['rootPrefix' => '../']); ?>
    </body>
</html>
