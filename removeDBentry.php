<?php
require_once("functions.php");

// 1. Ochrana pred neoprávneným prístupom
if (!isLoggedIn()) {
  http_response_code(403);
  echo "Unauthorized access";
  exit;
}

verifyCSRFToken();

if (!empty($_POST)) {
  $entryID = $_POST['id'] ?? '';

  /* removing from db */
  if (isset($entryID) and is_numeric($entryID)) {
    $stmt = getDB()->prepare("DELETE FROM $tableName WHERE id = :id");
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
