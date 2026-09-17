<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$json_data = json_decode(file_get_contents("settings.json"), TRUE);
$dbFileName = $json_data['dbFileName'];

if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $json_data['tableName'])) {
    http_response_code(400);
    echo "Invalid table name";
    exit;
} else {
    $tableName = $json_data['tableName'] ?? '';
}

// Vytvorenie tabuľky ak neexistuje
getDB()->exec("CREATE TABLE IF NOT EXISTS {$tableName} (id INTEGER PRIMARY KEY UNIQUE, title VARCHAR (500) NOT NULL, link VARCHAR (4500) NOT NULL, pubDate DATETIME NOT NULL)");

$linkURL    = htmlspecialchars($json_data['linkURL'] ?? '', ENT_QUOTES, 'UTF-8');
$pageTitle  = htmlspecialchars($json_data['pageTitle'] ?? '', ENT_QUOTES, 'UTF-8');
$reloadTimeInMilliseconds = intval($json_data['reloadTimeInMilliseconds'] ?? 10000);
$maxFileSizeLimit = intval($json_data['maxFileSizeLimit'] ?? 5000000);
$pwdHashControl = $json_data['pwd_hash'];
$maxEntryLimit = intval($json_data['maxEntryLimit'] ?? 100);


function getDB()
{
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
    return !empty($_SESSION['is_logged_in']);
}

function getCSRFToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCSRFToken(): void {
    $token = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['csrf_token']) ||
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        http_response_code(403);

        echo "CSRF ERROR\n";
        //echo "Session token: " . ($_SESSION['csrf_token'] ?? 'MISSING') . "\n";
        //echo "POST token: " . $token . "\n";

        exit;
    }
}
