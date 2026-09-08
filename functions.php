<?php

$json_data = json_decode(file_get_contents("settings.json"), TRUE);
$dbFileName = $json_data['dbFileName'];
$tableName = $json_data['tableName'];
$linkURL = $json_data['linkURL'];
$reloadTimeInMilliseconds = $json_data['reloadTimeInMilliseconds'];
$maxFileSizeLimit = $json_data['maxFileSizeLimit'];
$pwdHashControl = $json_data['pwd_hash'];

function getDB() {
    static $db = null;

    if ($db === null) {
        global $dbFileName;
        $db = new SQLite3($dbFileName);
    }

    return $db;
}

function isLoggedIn() {
    global $pwdHashControl;
    if (isset($_COOKIE["member_login"]) && hash_equals($_COOKIE["member_login"], md5($pwdHashControl))) {
        return true;
    }
}
