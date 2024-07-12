<?php
function dbquery($string, $db) {
  $db->exec("$string");
}

function removeWeirdThings($string) {
  return preg_replace("/[^a-zA-Z0-9\.\-]+/", "_", $string);
}

$uploadedFileData = $_FILES["uploaded_file"];
$counter = 0;
$normalizeNameS = "";
foreach ($uploadedFileData["name"] as $value) {
  $actualtime = date("Y-m-d--H-i-s");
  $target_dir = "uploads/";
  $baseFilename = basename($uploadedFileData["name"][$counter]);
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
  $fileSize = $uploadedFileData["size"][$counter];
  if ($fileSize > 500000) {
    echo "Sorry, your file is too large. -$fileSize-\n";
    $uploadOk = 0;
  }

  // Allow certain file formats
  if ($imageFileType != "torrent" && $imageFileType != "mackousko") {
    echo "Sorry, only torrent files are allowed. -$imageFileType-\n";
    $uploadOk = 0;
  }

  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk != 0) {
    if (move_uploaded_file($uploadedFileData["tmp_name"][$counter], $target_file)) {
      //echo $normalizeName;
      $normalizeNameS .= $normalizeName . ";;;";
    }
    else {
      echo "Sorry, there was an error uploading your file.";
    }
  }

  $counter++;

  //DEBUG
/*  $filenameR = 'DEBUG_data.txt';
	$file = fopen($filenameR, 'a');
	$queryString = "File: ". implode(",",$value)."\n";
	fwrite($file, $queryString);
*/
}

echo $normalizeNameS;
