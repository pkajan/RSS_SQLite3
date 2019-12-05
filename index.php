<?php

$json_data  = json_decode(file_get_contents("settings.json"), true);
$dbFileName = $json_data['dbFileName'];
$tableName  = $json_data['tableName'];
$linkURL    = $json_data['linkURL'];
$pageTitle  = $json_data['pageTitle'];
$db         = new SQLite3($dbFileName);


header("Content-type: text/xml");
echo "<?xml version='1.0' encoding='UTF-8'?>
<rss version='2.0'>
<channel>
<title>$pageTitle</title>
<description>Torrents links to download</description>
<link>$linkURL</link>";

$tmpArray = [];

$res = $db->query("SELECT * FROM $tableName");
while ($data = $res->fetchArray()) {
    $title   = htmlspecialchars($data["title"]);
    $link    = htmlspecialchars($data["link"]);
    $pubDate = htmlspecialchars($data["pubDate"]);
    
    $string = "<item>
        <title>$title</title>
        <link>$link</link>
        <pubDate>$pubDate</pubDate>
    </item>";
    array_unshift($tmpArray, $string);
}
echo implode(" ",$tmpArray);
echo "</channel></rss>";
