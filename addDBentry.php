<?php
function dbquery($string, $db){
    $db->exec("$string");
}

if (!empty($_POST)) {
    $entryName = htmlspecialchars($_POST["entryName"]);
    $entryLink = htmlspecialchars($_POST["entryLink"]);
    $json_data = json_decode(file_get_contents("settings.json"), TRUE);
    $dbFileName = $json_data['dbFileName'];
    $dbTableName = $json_data['tableName'];
    $db = new SQLite3($dbFileName);

    /* add db */
    $pubDate = htmlspecialchars(date("D, j M Y G:i:s TP"));
    $textquery = "INSERT INTO $dbTableName (title, link, pubDate) VALUES('$entryName', '$entryLink', '$pubDate')";
    dbquery($textquery, $db);
    echo "Entry:{$entryName} | URL/MAGNET:{$entryLink} was added";


    //DEBUG 
    /*$filenameR = 'DEBUG_data.txt';
    $file = fopen($filenameR, 'a');
    $queryString = "AddedDBentry: {$entryName} | {$entryLink}\n";
    fwrite($file, $queryString);*/
}

