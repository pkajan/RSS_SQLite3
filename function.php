<?php

function removeWeirdThings($string) {
    return preg_replace("/[^a-zA-Z0-9\.\-]+/", "_", $string);
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
