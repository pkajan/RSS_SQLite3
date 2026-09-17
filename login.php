<?php
session_start([
    'cookie_lifetime' => 30 * 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

require_once("functions.php");

verifyCSRFToken();

if (!empty($_POST)) {
    $action = $_POST['action'] ?? '';

    // ODHLÁSENIE
    if ($action === 'logout') {
        $_SESSION = [];
        session_destroy();
        echo "Odhlasenie";
        exit;
    }

    // PRIHLÁSENIE
    if (!empty($_POST["LOGINpassword"])) {
        $inputPassword = $_POST["LOGINpassword"];

        if ($pwdHashControl !== '' && password_verify($inputPassword, $pwdHashControl)) {
            session_regenerate_id(true); // Ochrana pred Session Fixation
            $_SESSION['is_logged_in'] = true;
            echo "true";
        } else {
            echo "Invalid Login";
            sleep(1);
        }
    }
}
