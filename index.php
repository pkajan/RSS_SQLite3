<?php
require_once("functions.php");

$filename = "settings.json";
if (file_exists($filename)) {

    $json_data  = json_decode(file_get_contents($filename), true);
    $tableName  = $json_data['tableName'] ?? '';
    $linkURL    = htmlspecialchars($json_data['linkURL'] ?? '', ENT_QUOTES, 'UTF-8');
    $pageTitle  = htmlspecialchars($json_data['pageTitle'] ?? '', ENT_QUOTES, 'UTF-8');

    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
        http_response_code(400);
        echo "Invalid table name";
        exit;
    }

    // Vytvorenie tabuľky ak neexistuje
    getDB()->exec("CREATE TABLE IF NOT EXISTS {$tableName} (id INTEGER PRIMARY KEY UNIQUE, title VARCHAR (250) NOT NULL, link VARCHAR (2500) NOT NULL, pubDate DATETIME NOT NULL)");

    header("Content-Type: text/xml; charset=utf-8");
    echo "<?xml version='1.0' encoding='UTF-8'?>\n";
    echo "<rss version='2.0'>\n";
    echo "<channel>\n";
    echo "<title>{$pageTitle}</title>\n";
    echo "<description>Torrents links to download</description>\n";
    echo "<link>{$linkURL}</link>\n";
    echo "<item>\n";
    echo "  <title>NULL</title>\n";
    echo "  <link></link>\n";
    echo "  <pubDate>Mon, 01 Jan 2020 00:00:00 +0200</pubDate>\n";
    echo "</item>\n";

    // Záznamy načítame v obrátenom poradí priamo z DB (O(1) réžia v PHP)
    $res = getDB()->query("SELECT * FROM {$tableName} ORDER BY id DESC");

    while ($data = $res->fetchArray(SQLITE3_ASSOC)) {
        $title   = htmlspecialchars($data["title"], ENT_QUOTES, 'UTF-8');
        $link    = htmlspecialchars($data["link"], ENT_QUOTES, 'UTF-8');
        $pubDate = htmlspecialchars($data["pubDate"], ENT_QUOTES, 'UTF-8');

        echo "<item>\n";
        echo "  <title>{$title}</title>\n";
        echo "  <link>{$link}</link>\n";
        echo "  <pubDate>{$pubDate}</pubDate>\n";
        echo "</item>\n";
    }

    echo "</channel>\n</rss>";
} else {
    echo "The file {$filename} does not exist";
    echo '<pre>
    {
        "dbFileName": "TESTdatabase.db",
        "tableName": "TESTwebfeed",
        "linkURL": "https://myhosting.eu",
        "pageTitle": "TESTmy RSS",
        "pwd_hash": "hash_of_your_password"
    }</pre>';
}
