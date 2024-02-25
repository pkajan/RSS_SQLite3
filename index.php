<?php
$filename = "settings.json";
if (file_exists($filename)) {


    $json_data  = json_decode(file_get_contents($filename), true);
    $dbFileName = $json_data['dbFileName'];
    $tableName  = $json_data['tableName'];
    $linkURL    = $json_data['linkURL'];
    $pageTitle  = $json_data['pageTitle'];
    $db         = new SQLite3($dbFileName);

    $db->exec("create table if not exists $tableName(id INTEGER PRIMARY KEY UNIQUE, title VARCHAR (250) NOT NULL, link VARCHAR (2500) NOT NULL, pubDate DATETIME NOT NULL)");

    header("Content-type: text/xml");
    echo "<?xml version='1.0' encoding='UTF-8'?>
            <rss version='2.0'>
            <channel>
            <title>$pageTitle</title>
            <description>Torrents links to download</description>
            <link>$linkURL</link>
            <item>
            <title>NULL</title>
            <link></link>
            <pubDate>Mon, 01 Jan 2020 00:00:00 +0200</pubDate>
            </item>";

    $tmpArray = [];

    $res = $db->query("SELECT * FROM $tableName");
    while ($data = $res->fetchArray()) {
        $title   = htmlspecialchars($data["title"]);
        $link    = ($data["link"]);
        $pubDate = htmlspecialchars($data["pubDate"]);

        $string = "<item>
        <title>$title</title>
        <link>$link</link>
        <pubDate>$pubDate</pubDate>
    </item>";
        array_unshift($tmpArray, $string);
    }
    echo implode(" ", $tmpArray);
    echo "</channel></rss>";
} else {
    echo "The file $filename does not exist";
    echo '<pre>
    {
        "dbFileName": "database.db",
        "tableName": "webfeed",
        "linkURL": "https://myhosting.eu",
        "pageTitle": "my RSS",
        "pwd_hash": "sha256_hash_of_your_password"
    }</pre>';
}
