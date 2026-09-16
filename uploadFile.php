<?php
require_once("functions.php");

function removeWeirdThings($string) {
  return preg_replace("/[^a-zA-Z0-9\.\-]+/", "_", $string);
}

// Nastavenie hlavičky pre JSON odpoveď
header('Content-Type: application/json; charset=utf-8');

$response = [
  'success' => false,
  'files'   => [],
  'errors'  => []
];

if (!isLoggedIn()) {
  http_response_code(401);
  $response['errors'][] = 'Unauthorized access.';
  echo json_encode($response);
  exit;
}

if (!isset($_FILES["uploaded_file"])) {
  http_response_code(400);
  $response['errors'][] = 'No files uploaded.';
  echo json_encode($response);
  exit;
}

// 1. Ošetrenie existencie adresára uploads/
$target_dir = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;
if (!is_dir($target_dir)) {
  mkdir($target_dir, 0755, true);
}

$uploadedFileData = $_FILES["uploaded_file"];
$finfo = new finfo(FILEINFO_MIME_TYPE);

$allowedMimeTypes = [
  'application/x-bittorrent',
  'application/x-torrent',
  'application/octet-stream'
];

$savedFiles = [];

// Normalizácia spracovania (pre jedno aj viacero polí)
$fileNames = is_array($uploadedFileData["name"]) ? $uploadedFileData["name"] : [$uploadedFileData["name"]];

foreach ($fileNames as $counter => $fileName) {
  if (empty($fileName)) {
    continue;
  }

  $errorCode = is_array($uploadedFileData["error"]) ? $uploadedFileData["error"][$counter] : $uploadedFileData["error"];
  if ($errorCode !== UPLOAD_ERR_OK) {
    $response['errors'][] = "File '{$fileName}' upload error code: {$errorCode}";
    continue;
  }

  $tmpFilePath = is_array($uploadedFileData["tmp_name"]) ? $uploadedFileData["tmp_name"][$counter] : $uploadedFileData["tmp_name"];
  $fileSize    = is_array($uploadedFileData["size"]) ? $uploadedFileData["size"][$counter] : $uploadedFileData["size"];

  $actualtime    = date("Y-m-d--H-i-s");
  $baseFilename  = basename($fileName);
  $normalizeName = removeWeirdThings("{$actualtime}-{$baseFilename}");
  $target_file   = $target_dir . $normalizeName;
  
  $fileErrors = [];

  // A. Kontrola existencie
  if (file_exists($target_file)) {
    $fileErrors[] = "File '{$fileName}' already exists.";
  }

  // B. Kontrola veľkosti
  if (isset($maxFileSizeLimit) && $fileSize > $maxFileSizeLimit) {
    $fileErrors[] = "File '{$fileName}' is too large ({$fileSize} bytes).";
  }

  // C. Kontrola prípony
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
  if ($imageFileType !== "torrent" && $imageFileType !== "mackousko") {
    $fileErrors[] = "File '{$fileName}' has invalid extension (.{$imageFileType}).";
  }

  // D. Kontrola MIME typu
  $detectedMimeType = $finfo->file($tmpFilePath);
  if (!in_array($detectedMimeType, $allowedMimeTypes, true)) {
    $fileErrors[] = "File '{$fileName}' has invalid MIME type ({$detectedMimeType}).";
  }

  // E. Kontrola nebezpečného PHP kódu
  $fileContentHeader = file_get_contents($tmpFilePath, false, null, 0, 2048);
  if (preg_match('/<\?php|<\?=/i', $fileContentHeader)) {
    $fileErrors[] = "File '{$fileName}' contains dangerous executable code.";
  }

  // Uloženie súboru ak neboli nájdené chyby
  if (empty($fileErrors)) {
    if (move_uploaded_file($tmpFilePath, $target_file)) {
      $savedFiles[] = $normalizeName;
      $response['files'][] = [
        'original_name'  => $fileName,
        'normalized_name' => $normalizeName,
        'status'          => 'uploaded'
      ];
    } else {
      $response['errors'][] = "Failed to move uploaded file '{$fileName}'.";
    }
  } else {
    $response['errors'] = array_merge($response['errors'], $fileErrors);
  }
}

// Nastavenie hlavného stavu úspešnosti
if (!empty($savedFiles)) {
  $response['success'] = true;
}

echo json_encode($response);
