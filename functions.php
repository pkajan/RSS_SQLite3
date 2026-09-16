<?php

$json_data = json_decode(file_get_contents("settings.json"), TRUE);
$dbFileName = $json_data['dbFileName'];
$tableName = $json_data['tableName'] ?? '';
$linkURL    = htmlspecialchars($json_data['linkURL'] ?? '', ENT_QUOTES, 'UTF-8');
$pageTitle  = htmlspecialchars($json_data['pageTitle'] ?? '', ENT_QUOTES, 'UTF-8');
$reloadTimeInMilliseconds = $json_data['reloadTimeInMilliseconds'];
$maxFileSizeLimit = $json_data['maxFileSizeLimit'];
$pwdHashControl = $json_data['pwd_hash'];

function getDB() {
    static $db = null;

    if ($db === null) {
        global $dbFileName;
        $db = new SQLite3($dbFileName);
        
        // Nastavenie pragma príkazov pre lepšiu konkurentnosť
        $db->exec('PRAGMA journal_mode=WAL;');
        $db->exec('PRAGMA busy_timeout=5000;');
    }

    return $db;
}

function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return !empty($_SESSION['is_logged_in']);
}
