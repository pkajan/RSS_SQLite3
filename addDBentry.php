<?php
require_once("functions.php");

// 1. Kontrola prihlásenia
if (!isLoggedIn()) {
    http_response_code(403);
    echo "Unauthorized access";
    exit;
}

verifyCSRFToken();

function dbqueryAdd($entryName, $entryLink, $pubDate, $tableName) {
    $stmt = getDB()->prepare("INSERT INTO {$tableName} (title, link, pubDate) VALUES (:title, :link, :pubDate)");
    $stmt->bindValue(':title', $entryName, SQLITE3_TEXT);
    $stmt->bindValue(':link', $entryLink, SQLITE3_TEXT);
    $stmt->bindValue(':pubDate', $pubDate, SQLITE3_TEXT);

    return $stmt->execute();
}

if (!empty($_POST["entryName"]) && !empty($_POST["entryLink"])) {
    $entryName = mb_substr(trim($_POST["entryName"]), 0, 500, 'UTF-8');
    $entryLink = mb_substr(trim($_POST["entryLink"]), 0, 4500, 'UTF-8');

    $pubDate = date(DATE_RSS);

    if (dbqueryAdd($entryName, $entryLink, $pubDate, $tableName)) {
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