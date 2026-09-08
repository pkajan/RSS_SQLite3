<?php
require_once("functions.php");

// 1. Kontrola prihlásenia
if (!isLoggedIn()) {
    http_response_code(403);
    echo "Unauthorized access";
    exit;
}

function dbqueryAdd($entryName, $entryLink, $pubDate, $tableName) {
    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
        http_response_code(400);
        echo "Invalid table name";
        exit;
    }

    $stmt = getDB()->prepare("INSERT INTO {$tableName} (title, link, pubDate) VALUES (:title, :link, :pubDate)");
    $stmt->bindValue(':title', $entryName, SQLITE3_TEXT);
    $stmt->bindValue(':link', $entryLink, SQLITE3_TEXT);
    $stmt->bindValue(':pubDate', $pubDate, SQLITE3_TEXT);

    return $stmt->execute();
}

if (!empty($_POST["entryName"]) && !empty($_POST["entryLink"])) {
    $entryName = trim($_POST["entryName"]);
    $entryLink = trim($_POST["entryLink"]);

    $json_data = json_decode(@file_get_contents("settings.json"), TRUE);
    $dbTableName = $json_data['tableName'] ?? '';

    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $dbTableName)) {
        http_response_code(400);
        echo "Invalid table name configuration";
        exit;
    }

    $pubDate = date("D, j M Y G:i:s TP");

    if (dbqueryAdd($entryName, $entryLink, $pubDate, $dbTableName)) {
        echo "Entry added: " . htmlspecialchars($entryName, ENT_QUOTES, 'UTF-8') .
            "\nMagnet: " . htmlspecialchars($entryLink, ENT_QUOTES, 'UTF-8');
    } else {
        http_response_code(500);
        echo "Failed to add entry to database";
    }
} else {
    http_response_code(400);
    echo "Missing required parameters";
}
