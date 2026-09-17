<?php

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('X-Content-Type-Options: nosniff');
header("Content-Type: text/xml; charset=utf-8");

require_once("functions.php");

$filename = "settings.json";
if (file_exists($filename)) {
    echo "<?xml version='1.0' encoding='UTF-8'?>\n";
    echo "<rss version='2.0'>\n";
    echo "<channel>\n";
    echo "<lastBuildDate>" . date(DATE_RSS) . "</lastBuildDate>\n";
    echo "<title>{$pageTitle}</title>\n";
    echo "<description>Torrents links to download</description>\n";
    echo "<link>{$linkURL}</link>\n";

    // Záznamy načítame v obrátenom poradí priamo z DB
    $res = getDB()->query("SELECT * FROM {$tableName} ORDER BY id DESC LIMIT {$maxEntryLimit}");
    $hasItems = false;

    while ($data = $res->fetchArray(SQLITE3_ASSOC)) {
        $hasItems = true;
        $itemID   = htmlspecialchars($data["id"], ENT_QUOTES, 'UTF-8');
        $title   = htmlspecialchars($data["title"], ENT_QUOTES, 'UTF-8');
        $link    = htmlspecialchars($data["link"], ENT_QUOTES, 'UTF-8');
        $pubDate = htmlspecialchars($data["pubDate"], ENT_QUOTES, 'UTF-8');

        echo "<item>\n";
        echo "  <title>{$title}</title>\n";
        echo "  <link>{$link}</link>\n";
        echo "  <guid>$itemID--" . md5($link) . "</guid>\n";
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