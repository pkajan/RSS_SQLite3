<?php
function removeWeirdThings($string) {
    return preg_replace("/[^a-zA-Z0-9\.\-]+/", "", $string);
}

function sha256($string) {
    return hash('sha256', $string);
}

function dbquery($string, $db) {
    $db->exec("$string");
}

function add_to_db($title, $link, $db) {
    global $tableName;
    if (!empty($title) and !empty($link)) {
        $title   = htmlspecialchars($title);
        $link    = htmlspecialchars($link);
        $pubDate = htmlspecialchars(date("D, j M Y G:i:s TP"));
        $textquery = "INSERT INTO $tableName (title, link, pubDate) VALUES('$title', '$link', '$pubDate')";
        dbquery($textquery, $db);
    }
}

$json_data  = json_decode(file_get_contents("settings.json"), true);
$dbFileName = $json_data['dbFileName'];
$tableName  = $json_data['tableName'];
$linkURL = $json_data['linkURL'];
$db         = new SQLite3($dbFileName);

$arr_file_types = ['torrent', 'mackousko'];
$target_dir = "uploads";
$actualtime = date("Y-m-d--H-i-s");

if (isset($_COOKIE["member_login"]) && $_COOKIE["member_login"] == sha256($json_data['pwd_hash'])) {
    if (!empty($_POST["title"]) && !empty($_POST["link"])) {
        $title = htmlspecialchars($_POST["title"], ENT_QUOTES, 'UTF-8');
        $link  = htmlspecialchars($_POST["link"], ENT_QUOTES, 'UTF-8');;
        add_to_db($title, $link, $db);
        echo $title . " addeded.";
        return;
    }

    if (!(in_array(pathinfo($_FILES["file"]["name"])['extension'], $arr_file_types))) {
        echo "Sorry, only torrent files are allowed.";
        return;
    }

    if ($_FILES["file"]["size"] > 15728640) { //just to be sure, someone doesnt inject javascript
        echo "Sorry, your file is too large.";
        return;
    }

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777);
    }

    $target_file  = removeWeirdThings(basename($actualtime . "-" . $_FILES["file"]["name"]));
    $target_dir = $target_dir . '/';

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $target_file)) {
        echo "The file " . htmlspecialchars(basename($_FILES["file"]["name"]), ENT_QUOTES, 'UTF-8') . " has been uploaded";
        $title = str_replace(".torrent", "", basename($_FILES["file"]["name"]));
        $link  = "$linkURL/$target_dir" . $target_file;
        add_to_db($title, $link, $db);
    } else {
        echo htmlspecialchars($target_dir . $target_file, ENT_QUOTES, 'UTF-8') . "<br>";
        echo "Sorry, there was an error uploading your file.";
    }
} else {
    echo "No :P";
}
