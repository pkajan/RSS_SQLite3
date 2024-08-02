<?php
require_once("functions.php");
$SUCCESS = FALSE;
if (isset($_COOKIE["member_login"]) && $_COOKIE["member_login"] == sha256($json_data['pwd_hash'])) {
  $SUCCESS = TRUE;
}
if ($SUCCESS) {
  $json_data = json_decode(file_get_contents("settings.json"), TRUE);
  $dbFileName = $json_data['dbFileName'];
  $db = new SQLite3($dbFileName);
  $tableName = $json_data['tableName'];
  $linkURL = htmlspecialchars($json_data['linkURL']);
  $pageTitle = htmlspecialchars($json_data['pageTitle']);

  $res = $db->query("SELECT * FROM $tableName");

  $tmpArray = [];

  while ($data = $res->fetchArray()) {
    $id = htmlspecialchars($data["id"]);
    $title = htmlspecialchars($data["title"]);
    $link = htmlspecialchars($data["link"]);
    $pubDate = htmlspecialchars($data["pubDate"]);

    $string = "\n<div class='row card-panel hoverable wordWrap valign-wrapper padding3 gap3 blue-grey darken-2'>\n" .
      "<div class='col s1 padding3 horizontal-center'>{$id}</div>\n" .
      "<div class='col s5 padding3 tooltip truncate bold' title='{$title}'>{$title}</div>\n" .
      "<div class='col s3 padding3 truncate tooltip' title='{$link}'>{$link}</div>\n" .
      "<div class='col s2 padding3 truncate horizontal-center' title='{$pubDate}'>{$pubDate}</div>\n" .
      "<div class='col s1 padding3 horizontal-center'><a href='#' class='removeDBentry waves-effect waves-light btn filled bold' value='{$data["id"]}'>Delete</a></div></div>\n\n";
    array_unshift($tmpArray, $string);
  }
  echo implode(" ", $tmpArray);
}
