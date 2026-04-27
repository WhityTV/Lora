<?php
require_once __DIR__ . '/../lan/lan.php';

class ForgotPassword extends Lan {
    public function __construct() {
        parent::__construct();
    }
}

$forgotPassword = new ForgotPassword();
$statusMsg = '';
$errors = [];

if (isset($_POST['syslang']) && in_array($_POST['syslang'], ['EN', 'DE'])) {
    $forgotPassword->setSysLan($_POST['syslang']);
    header("Refresh:0");
}

// UI states: mail -> code -> password.
$step = $_SESSION['reset_step'] ?? 'mail';

// Resolve step from session with password step taking precedence.
if (isset($_SESSION['reset_verified']) && $_SESSION['reset_verified'] === true) {
    $step = 'password';
} elseif (isset($_SESSION['reset_mail']) || isset($_SESSION['reset_user_id'])) {
    $step = 'code';
}

// Step 1: Generate and send reset code to the requested mail.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_code'])) {
    $mail = trim($_POST['mail']);
    $safemail = $forgotPassword->esc($mail);

    $userResult = $forgotPassword->qry("SELECT id, disabled FROM al_usr WHERE mail = '{$safemail}' LIMIT 1;");
    $userRow = ($userResult && $userResult->num_rows > 0) ? mysqli_fetch_assoc($userResult) : null;
    $userExists = ($userRow !== null);
    $userDisabled = $userExists && (($userRow['disabled'] ?? 'N') === 'Y');

    if (!$userExists || $userDisabled) {
        $statusMsg = $forgotPassword->getLan('mail_not_existent');
        $step = 'mail';
    } else {
        $code = strtoupper(bin2hex(random_bytes(3)));
        $tokenHash = hash('sha256', $code);

        // Invalidate all previous tokens for this mail so only the latest is valid.
        $forgotPassword->qry("DELETE FROM password_reset_tokens WHERE mail = '{$safemail}';");

        // Store token hash with a 15-minute expiry window.
        $forgotPassword->qry("INSERT INTO password_reset_tokens (mail, token_hash, expires_at, used) VALUES ('{$safemail}', '{$tokenHash}', DATE_ADD(NOW(), INTERVAL 15 MINUTE), 'N');");

        $subject = $forgotPassword->getLan('pass_reset_code');
        $message = '<html><body>';
        $message .= '<p>' . $forgotPassword->getLan('reset_pass_msg') . '</p>';
        $message .= '<p> <b>' . $code . '</b></p>';
        $message .= '</body></html>';
        $headers = "From: brightymightywhity@gmail.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $mailSent = mail($mail, $subject, $message, $headers);

        if ($mailSent) {
            $statusMsg = $forgotPassword->getLan('reset_code_sent');
            $_SESSION['reset_mail'] = $mail;
            $_SESSION['reset_user_id'] = (int) $userRow['id'];
            $_SESSION['reset_step'] = 'code';
            unset($_SESSION['reset_verified']);
            $step = 'code';
        } else {
            $statusMsg = $forgotPassword->getLan('mail_not_existent');
            error_log(
                'mail() failed in forgot_password.php | mail=' . $mail .
                ' | SMTP=' . (string) ini_get('SMTP') .
                ' | smtp_port=' . (string) ini_get('smtp_port') .
                ' | sendmail_from=' . (string) ini_get('sendmail_from')
            );
            $step = 'mail';
        }
    }
}

// Go back to mail step when user wants to request a new code.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_code'])) {
    unset($_SESSION['reset_mail'], $_SESSION['reset_user_id'], $_SESSION['reset_verified'], $_SESSION['reset_step']);
    session_write_close();
    header('Location: forgot_password.php');
    exit;
}

// Cancel the reset flow entirely and return to login.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reset'])) {
    $safemail = $forgotPassword->esc($_SESSION['reset_mail'] ?? '');
    if ($safemail !== '') {
        $forgotPassword->qry("DELETE FROM password_reset_tokens WHERE mail = '{$safemail}' AND (used = 'N');");
    }

    unset($_SESSION['reset_mail'], $_SESSION['reset_user_id'], $_SESSION['reset_verified'], $_SESSION['reset_step']);
    session_write_close();
    header('Location: login.php');
    exit;
}

// Step 2: Verify user entered code against the latest valid DB token.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
    $inputCode = strtoupper(trim($_POST['code'] ?? ''));
    $safemail = $forgotPassword->esc($_SESSION['reset_mail'] ?? '');
    $resetUserId = isset($_SESSION['reset_user_id']) ? (int) $_SESSION['reset_user_id'] : 0;

    if ($resetUserId > 0) {
        $userStateResult = $forgotPassword->qry("SELECT disabled FROM al_usr WHERE id = {$resetUserId} LIMIT 1;");
        $userStateRow = ($userStateResult && $userStateResult->num_rows > 0) ? mysqli_fetch_assoc($userStateResult) : null;
        if ($userStateRow && (($userStateRow['disabled'] ?? 'N') === 'Y')) {
            $statusMsg = $forgotPassword->getLan('account_disabled');
            $_SESSION['reset_step'] = 'mail';
            $step = 'mail';
        }
    }

    if ($step !== 'mail') {

        // Fetch only the latest token that is not expired and not yet used.
        $result = $forgotPassword->qry(
            "SELECT id, token_hash FROM password_reset_tokens
            WHERE mail = '{$safemail}'
            AND expires_at > NOW()
            AND (used = 'N')
            ORDER BY created_at DESC LIMIT 1;"
        );
        $row = ($result && $result->num_rows > 0) ? mysqli_fetch_assoc($result) : null;

        if ($row && hash_equals($row['token_hash'], hash('sha256', $inputCode))) {
            // Consume token immediately after successful code entry.
            $tokenId = (int) $row['id'];
            $forgotPassword->qry("UPDATE password_reset_tokens SET used = 'Y' WHERE id = {$tokenId} AND (used = 'N');");

            if ($resetUserId > 0) {
                $forgotPassword->qry("UPDATE al_usr SET login_attempts = 0 WHERE id = {$resetUserId};");
            }

            $_SESSION['reset_verified'] = true;
            $_SESSION['reset_step'] = 'password';
            $step = 'password';
            $statusMsg = '';
            session_regenerate_id(true);
            session_write_close();
            header('Location: forgot_password.php');
            exit;
        } else {
            if ($resetUserId > 0) {
                $attemptsResult = $forgotPassword->qry("SELECT login_attempts FROM al_usr WHERE id = {$resetUserId} LIMIT 1;");
                $attemptsRow = ($attemptsResult && $attemptsResult->num_rows > 0) ? mysqli_fetch_assoc($attemptsResult) : null;
                $attempts = (int) ($attemptsRow['login_attempts'] ?? 0) + 1;

                if ($attempts >= 3) {
                    $forgotPassword->qry("UPDATE al_usr SET login_attempts = {$attempts}, disabled = 'Y' WHERE id = {$resetUserId};");
                    $statusMsg = $forgotPassword->getLan('account_disabled');
                    $_SESSION['reset_step'] = 'mail';
                    $step = 'mail';
                } else {
                    $forgotPassword->qry("UPDATE al_usr SET login_attempts = {$attempts} WHERE id = {$resetUserId};");
                    $statusMsg = $forgotPassword->getLan('code_invalid');
                    $_SESSION['reset_step'] = 'code';
                    $step = 'code';
                }
            } else {
                $statusMsg = $forgotPassword->getLan('code_invalid');
                $_SESSION['reset_step'] = 'code';
                $step = 'code';
            }
        }
    }
}

// Step 3: Save new password only after successful code verification.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_password'])) {
    $errors = [];
    $password = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $resetmail = $_SESSION['reset_mail'] ?? '';
    $resetUserId = isset($_SESSION['reset_user_id']) ? (int) $_SESSION['reset_user_id'] : 0;
    $isVerified = isset($_SESSION['reset_verified']) && $_SESSION['reset_verified'] === true;

    if (!$isVerified) {
        $statusMsg = $forgotPassword->getLan('code_invalid');
        $_SESSION['reset_step'] = 'mail';
        $step = 'mail';
    } else {
        if ($password === '' || $confirmPassword === '') {
            $errors[] = $forgotPassword->getLan('pass_not_empty');
        }

        if ($password !== $confirmPassword) {
            $errors[] = $forgotPassword->getLan('pass_mismatch');
        }

        if (strlen($password) < 15) {
            $errors[] = $forgotPassword->getLan('pass_too_short');
        }

        if ($forgotPassword->hasWeakRepetitionPattern($password)) {
            $errors[] = $forgotPassword->getLan('pass_weak_pattern');
        }

        // Passord rules disabled due to NIST recommendation to not enforce specific complexity requirements, but can be re-enabled if desired.

        // if (!preg_match('/\p{Lu}/u', $password)) {
        //     $errors[] = $forgotPassword->getLan('pass_uppercase');
        // }

        // if (!preg_match('/\p{Ll}/u', $password)) {
        //     $errors[] = $forgotPassword->getLan('pass_lowercase');
        // }

        // if (!preg_match('/\d/', $password)) {
        //     $errors[] = $forgotPassword->getLan('pass_number');
        // }

        // if (!preg_match('/[^\p{L}\d\s]/u', $password)) {
        //     $errors[] = $forgotPassword->getLan('pass_special_char');
        // }

        $safemail = $forgotPassword->esc($resetmail);
        $userResult = null;
        if ($resetUserId > 0) {
            $userResult = $forgotPassword->qry("SELECT id, firstname, lastname, username, mail FROM al_usr WHERE id = {$resetUserId} LIMIT 1;");
        }

        // Fallback: resolve account by mail when no valid user id exists in session.
        if ((!$userResult || $userResult->num_rows === 0) && $safemail !== '') {
            $userResult = $forgotPassword->qry("SELECT id, firstname, lastname, username, mail FROM al_usr WHERE mail = '{$safemail}' LIMIT 1;");
        }

        $userRow = ($userResult && $userResult->num_rows > 0) ? mysqli_fetch_assoc($userResult) : [];
        if (empty($userRow) || !isset($userRow['id'])) {
            $errors[] = $forgotPassword->getLan('code_invalid');
        } else {
            $resetUserId = (int) $userRow['id'];
        }

        $personalValues = [
            $userRow['firstname'] ?? '',
            $userRow['lastname'] ?? '',
            $userRow['username'] ?? '',
            $userRow['mail'] ?? $resetmail,
        ];
        foreach ($personalValues as $personalValue) {
            if ($personalValue !== '' && stripos($password, $personalValue) !== false) {
                $errors[] = $forgotPassword->getLan('pass_contains_personal_info');
                break;
            }
        }
    
        if ($forgotPassword->isCommonOrPwnedBlackListedPassword($password)) {
            $errors[] = $forgotPassword->getLan('pass_blacklisted');

        }

        if (empty($errors)) {
            $passwordHash = password_hash($password, PASSWORD_ARGON2ID);
            $safeHash = $forgotPassword->esc($passwordHash);
            $updateResult = $forgotPassword->qry("UPDATE al_usr SET password = '{$safeHash}' WHERE id = {$resetUserId};");
            if ($updateResult === false) {
                $errors[] = $forgotPassword->getLan('login_failed');
                $_SESSION['reset_step'] = 'password';
                $step = 'password';
            }

            if ($updateResult !== false) {
                // Read back and verify the saved hash to ensure the reset actually persisted.
                $verifyResult = $forgotPassword->qry("SELECT password FROM al_usr WHERE id = {$resetUserId} LIMIT 1;");
                $verifyRow = ($verifyResult && $verifyResult->num_rows > 0) ? mysqli_fetch_assoc($verifyResult) : null;
                $storedHash = $verifyRow['password'] ?? '';
                if ($storedHash === '' || !password_verify($password, $storedHash)) {
                    $errors[] = $forgotPassword->getLan('login_failed');
                    $_SESSION['reset_step'] = 'password';
                    $step = 'password';
                }
            }

            if ($updateResult !== false && empty($errors)) {
                // Send confirmation mail after successful password reset.
                $confirmSubject = $forgotPassword->getLan('reset_pass');
                $confirmMessage = '<html><body>';
                $confirmMessage .= '<p>' . $forgotPassword->getLan('pass_reset_success') . '</p>';
                $confirmMessage .= '</body></html>';
                $confirmHeaders = "From: brightymightywhity@gmail.com\r\n";
                $confirmHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";
                mail($resetmail, $confirmSubject, $confirmMessage, $confirmHeaders);

                unset($_SESSION['reset_mail'], $_SESSION['reset_user_id'], $_SESSION['reset_verified']);
                header('Location: login.php');
                exit;
            }
        } else {
            $_SESSION['reset_step'] = 'password';
            $step = 'password';
        }
    }
}

$pageTitle = $step === 'password' ? $forgotPassword->getLan('reset_pass') : $forgotPassword->getLan('forgot_pass');
?>

<html>
    <head>
        <title>
            <?php
            echo $pageTitle;
            ?>
        </title>
        <link rel="stylesheet" href="login.css">
    </head>
    <body>
        <div class="language_buttons">
            <form method="post">
                <?php
                    $syslan = $forgotPassword->getSysLan();
                    if ($syslan == "DE") {
                        echo '<button type="submit" name="syslang" value="EN"><img src="../../icons/UK.png" alt="EN" width="25" height="25"></button>';
                    } elseif ($syslan == "EN") {
                        echo '<button type="submit" name="syslang" value="DE"><img src="../../icons/DE.png" alt="DE" width="25" height="25"></button>';
                    }
                ?>
            </form>
        </div>
        <h1>
        <?php
        echo $pageTitle;
        ?>
        </h1>

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

        <?php
        if (!empty($errors)) {
        ?>
            <?php
            foreach ($errors as $error) {
            ?>
                <p>
                    <?php
                    echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
                    ?>
                </p>
        <?php
            }
        }
        ?>

        <?php
        // Render form based on current step. 
        if ($step === 'mail') {
        ?>
            <form method="post">
                <label for="mail">
                    <?php echo $forgotPassword->getLan('mail');
                    ?>
                </label><br>
                <input type="mail" id="mail" name="mail" required value="
                    <?php 
                    echo htmlspecialchars($_POST['mail'] ?? '', ENT_QUOTES, 'UTF-8'); 
                    ?>
                ">
                <p>
                    <input type="submit" name="send_code" value="<?php echo $forgotPassword->getLan('receive_code'); ?>">
                    <input type="submit" name="cancel_reset" value="<?php echo $forgotPassword->getLan('cancel'); ?>" formnovalidate>
                </p>
            </form>
        <?php
        } elseif ($step === 'code') {
        ?>
            <form method="post">
                <p>
                    <input type="submit" name="resend_code" value="<?php echo $forgotPassword->getLan('resend_code'); ?>" formnovalidate>
                </p>
                <label for="code">
                    <?php
                    echo $forgotPassword->getLan('pass_reset_code');
                    ?>
                </label><br>
                <input type="text" id="code" name="code" required>
                <p>
                    <input type="submit" name="verify_code" value="<?php echo $forgotPassword->getLan('submit'); ?>">
                    <input type="submit" name="cancel_reset" value="<?php echo $forgotPassword->getLan('cancel'); ?>" formnovalidate>
                </p>
            </form>
        <?php
        } elseif ($step === 'password') {
        ?>
            <form method="post">
                <label for="new_password">
                    <?php
                    echo $forgotPassword->getLan('new_pass');
                    ?>
                </label><br>
                <input type="password" id="new_password" name="new_password" autocomplete="new-password" required><br>
                <label for="confirm_password">
                    <?php
                    echo $forgotPassword->getLan('confirm_pass');
                    ?>
                </label><br>
                <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" required>
                <p>
                    <input type="submit" name="save_password" value="<?php echo $forgotPassword->getLan('register'); ?>">
                    <input type="submit" name="cancel_reset" value="<?php echo $forgotPassword->getLan('cancel'); ?>" formnovalidate>
                </p>
            </form>
        <?php
        }
        ?>
    </body>
</html>