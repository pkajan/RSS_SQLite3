<?php
require_once("functions.php");
$SUCCESS = FALSE;
if (isset($_COOKIE["member_login"]) && $_COOKIE["member_login"] == sha256($json_data['pwd_hash'])) {
  $SUCCESS = TRUE;
}
if ($SUCCESS) {
  $json_data = json_decode(file_get_contents("settings.json"), TRUE);
  $linkURL = htmlspecialchars($json_data['linkURL']);
  $directory = getcwd() . '/uploads';
  $scanned_directory = array_diff(scandir($directory, SCANDIR_SORT_DESCENDING), array('..', '.'));

  $scannedDirContent = "";
  foreach ($scanned_directory as $value) {
    $scannedDirContent .= "<div class='row horizontal-center card-panel hoverable blue-grey darken-3 padding5'>\n" .
      "<div class='col s11 wordWrap align-left'><a href='{$linkURL}/uploads/{$value}'>{$value}</a></div>\n" .
      "<div class='col s1 valign-wrapper'><a href='#' class='removeLink' value='{$value}'>❌</a></div></div>\n\n";
  }
  if (count($scanned_directory) > 0) {
    echo $scannedDirContent;
  }
}

