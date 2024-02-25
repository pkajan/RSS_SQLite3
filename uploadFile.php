<?php
function dbquery($string, $db) {
  $db->exec("$string");
}

function removeWeirdThings($string) {
  return preg_replace("/[^a-zA-Z0-9\.\-]+/", "_", $string);
}

$actualtime = date("Y-m-d--H-i-s");
$target_dir = "uploads/";
$baseFilename = basename($_FILES["uploaded_file"]["name"]);
$normalizeName = removeWeirdThings("{$actualtime}-{$baseFilename}");
$target_file = "{$target_dir}{$normalizeName}";
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


// Check if file already exists
if (file_exists($target_file)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["uploaded_file"]["size"] > 500000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
if ($imageFileType != "torrent" && $imageFileType != "mackousko") {
  echo "Sorry, only torrent files are allowed.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk != 0) {
  if (move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $target_file)) {
    echo $normalizeName;
  }
  else {
    echo "Sorry, there was an error uploading your file.";
  }
}


//DEBUG 
/*  $filenameR = 'DEBUG_data.txt';
  $file = fopen($filenameR, 'a');
  $queryString = "File: {$path}\n";
  fwrite($file, $queryString);*/
//}

