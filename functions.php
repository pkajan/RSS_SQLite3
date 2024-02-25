<?php

//Calculates the SHA-256 hash of a string
function sha256($string) {
  return hash('sha256', $string);
}

$json_data = json_decode(file_get_contents("settings.json"), TRUE);
$dbFileName = $json_data['dbFileName'];
$db = new SQLite3($dbFileName);
$tableName = $json_data['tableName'];
$linkURL = $json_data['linkURL'];
$reloadTimeInMilliseconds = $json_data['reloadTimeInMilliseconds'];
