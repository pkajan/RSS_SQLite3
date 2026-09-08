<?php
require_once("functions.php");

function removeWeirdThings($string) {
  return preg_replace("/[^a-zA-Z0-9\.\-]+/", "_", $string);
}

if (isLoggedIn()) {
  if (!isset($_FILES["uploaded_file"])) {
    exit;
  }

  // 1. Ošetrenie existencie adresára uploads/
  $target_dir = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;
  if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
  }

  $uploadedFileData = $_FILES["uploaded_file"];
  $counter = 0;
  $normalizeNameS = "";

  // Inicializácia finfo pre detekciu MIME typu
  $finfo = new finfo(FILEINFO_MIME_TYPE);

  // Povolené MIME typy pre torrenty a binárne dátové súbory
  $allowedMimeTypes = [
    'application/x-bittorrent',
    'application/x-torrent',
    'application/octet-stream'
  ];

  if (is_array($uploadedFileData["name"])) {
    foreach ($uploadedFileData["name"] as $counter => $fileName) {
      if (empty($fileName) || $uploadedFileData["error"][$counter] !== UPLOAD_ERR_OK) {
        continue;
      }

      $tmpFilePath = $uploadedFileData["tmp_name"][$counter];
      $actualtime = date("Y-m-d--H-i-s");
      $baseFilename = basename($fileName);
      $normalizeName = removeWeirdThings("{$actualtime}-{$baseFilename}");
      $target_file = $target_dir . $normalizeName;
      $uploadOk = 1;

      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

      // A. Kontrola existencie
      if (file_exists($target_file)) {
        echo "Sorry, file already exists.\n";
        $uploadOk = 0;
      }

      // B. Kontrola veľkosti
      $fileSize = $uploadedFileData["size"][$counter];
      if ($fileSize > $maxFileSizeLimit) {
        echo "Sorry, your file is too large. -{$fileSize}-\n";
        $uploadOk = 0;
      }

      // C. Kontrola prípony
      if ($imageFileType !== "torrent" && $imageFileType !== "mackousko") {
        echo "Sorry, only torrent files are allowed. -{$imageFileType}-\n";
        $uploadOk = 0;
      }

      // D. Kontrola reálneho MIME typu pomocou finfo
      $detectedMimeType = $finfo->file($tmpFilePath);
      if (!in_array($detectedMimeType, $allowedMimeTypes, true)) {
        echo "Sorry, invalid file content/MIME type: -{$detectedMimeType}-\n";
        $uploadOk = 0;
      }

      // E. Bezpečnostná kontrola: Zákaz spustiteľného PHP kódu v obsahu súboru
      $fileContentHeader = file_get_contents($tmpFilePath, false, null, 0, 2048);
      if (preg_match('/<\?php|<\?=/i', $fileContentHeader)) {
        echo "Sorry, file contains invalid or dangerous executable code.\n";
        $uploadOk = 0;
      }

      // Uloženie súboru
      if ($uploadOk !== 0) {
        if (move_uploaded_file($tmpFilePath, $target_file)) {
          $normalizeNameS .= $normalizeName . ";;;";
        } else {
          echo "Sorry, there was an error uploading your file.\n";
        }
      }
    }
    //DEBUG
    /*  $filenameR = 'DEBUG_data.txt';
		$file = fopen($filenameR, 'a');
		$queryString = "File: ". implode(",",$value)."\n";
		fwrite($file, $queryString);
	*/
  }

  echo $normalizeNameS;
}
