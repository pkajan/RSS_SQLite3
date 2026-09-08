<?php
require_once("functions.php");

if (!empty($_POST)) {
    $action = $_POST['action'] ?? '';

    // Spoločné bezpečnostné nastavenia pre cookies (pridaná path)
    $cookieOptions = [
        'path'     => '/',    // Platnosť pre celú doménu
        'secure'   => true,   // Len cez HTTPS
        'httponly' => true,   // Zákaz prístupu cez JS (ochrana pred XSS)
        'samesite' => 'Lax'   // Ochrana pred CSRF
    ];

    // Overenie prihlásenia (porovnávame string s stringom)
    $isLoggedIn = isset($_COOKIE["member_login"]) &&
        $pwdHashControl !== '' &&
        hash_equals($_COOKIE["member_login"], md5($pwdHashControl));

    // Explicitné odhlásenie
    if ($action === 'logout') {
        $cookieOptions['expires'] = time() - 3600;
        setcookie('member_login', '', $cookieOptions);
        unset($_COOKIE['member_login']);
        echo "Odhlasenie";
        exit;
    }

    // PRIHLÁSENIE
    if (!empty($_POST["LOGINpassword"])) {
        $inputPassword = $_POST["LOGINpassword"];

        if ($pwdHashControl !== '' && password_verify($inputPassword, $pwdHashControl)) {
            $cookieOptions['expires'] = time() + (30 * 86400); // 30 dní

            setcookie('member_login', md5($pwdHashControl), $cookieOptions);
            echo "true";
        } else {
            echo "Invalid Login";
        }
    }
}
