<?php
require_once("functions.php");

if (!empty($_POST)) {
  $entryID = $_POST["id"];
  $json_data = json_decode(file_get_contents("settings.json"), TRUE);
  $dbFileName = $json_data['dbFileName'];
  $dbTableName = $json_data['tableName'];
  $db = new SQLite3($dbFileName);
  /* removing from db */
  if (isset($entryID) and is_numeric($entryID)) {
    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $dbTableName)) {
      http_response_code(400);
      echo "Invalid table name";
      exit;
    }
    $stmt = getDB()->prepare("DELETE FROM $dbTableName WHERE id = :id");
    $stmt->bindValue(':id', $entryID, SQLITE3_INTEGER);
    $stmt->execute();
    echo "Entry with ID {$entryID} was deleted";
  }


  //DEBUG 
  /*$filenameR = 'DEBUG_data.txt';
  $file = fopen($filenameR, 'a');
  $queryString = "RemovedDBentry: {$entryID}\n";
  fwrite($file, $queryString);*/
}
