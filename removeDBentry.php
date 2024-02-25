<?php
function dbquery($string, $db) {
  $db->exec("$string");
}

if (!empty($_POST)) {
  $entryID = $_POST["id"];
  $json_data = json_decode(file_get_contents("settings.json"), TRUE);
  $dbFileName = $json_data['dbFileName'];
  $dbTableName = $json_data['tableName'];
  $db = new SQLite3($dbFileName);
  /* removing from db */
  if (isset($entryID) and is_numeric($entryID)) {
    $sql_query = "DELETE FROM `rsstorrent`.`$dbTableName` WHERE `$dbTableName`.`id` = " . $entryID . ";";
    dbquery("DELETE FROM $dbTableName WHERE id =" . $entryID, $db);
    echo "Entry with ID {$entryID} was deleted";
  }


  //DEBUG 
  /*$filenameR = 'DEBUG_data.txt';
  $file = fopen($filenameR, 'a');
  $queryString = "RemovedDBentry: {$entryID}\n";
  fwrite($file, $queryString);*/
}
