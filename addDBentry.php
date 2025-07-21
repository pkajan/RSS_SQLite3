<?php
function dbquery($entryName, $entryLink, $pubDate, $db, $table) {
    $stmt = $db->prepare("INSERT INTO $table (title, link, pubDate) VALUES (:title, :link, :pubDate)");
    $stmt->bindValue(':title', $entryName, SQLITE3_TEXT);
    $stmt->bindValue(':link', $entryLink, SQLITE3_TEXT);
    $stmt->bindValue(':pubDate', $pubDate, SQLITE3_TEXT);
    $stmt->execute();
}

if (!empty($_POST)) {
    $entryName = $_POST["entryName"];
    $entryLink = $_POST["entryLink"];
    $json_data = json_decode(file_get_contents("settings.json"), TRUE);
    $dbFileName = $json_data['dbFileName'];
    $dbTableName = $json_data['tableName'];
    $db = new SQLite3($dbFileName);

    /* add db */
    $pubDate = date("D, j M Y G:i:s TP");

    dbquery($entryName, $entryLink, $pubDate, $db, $dbTableName);

    echo "Entry added: " . htmlspecialchars($entryName) . "\nMagnet: " . htmlspecialchars($entryLink);

    //DEBUG 
    /*$filenameR = 'DEBUG_data.txt';
    $file = fopen($filenameR, 'a');
    $queryString = "AddedDBentry: {$entryName} | {$entryLink}\n";
    fwrite($file, $queryString);
    fclose($file);*/
}
?>
