<?php
require_once("functions.php");

// 1. Ochrana pred neoprávneným prístupom
if (!isLoggedIn()) {
    http_response_code(403);
    echo "Unauthorized access";
    exit;
}

if (!empty($_POST["fileName"])) {
    $fileName = basename($_POST["fileName"]);
    $fileName = preg_replace('/[^\p{L}\p{N}._\(\) \-–—]/u', '', $fileName);

    // Dynamic fallback: ak priečinok neexistuje, vytvorí sa alebo načítava cez absolutnú cestu
    $uploadsFolder = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($uploadsFolder)) {
        mkdir($uploadsFolder, 0755, true);
    }

    $uploadsDir = realpath("uploads");
    $filePath = $uploadsDir . DIRECTORY_SEPARATOR . $fileName;

    // Bezpečnostná kontrola:
    // - $filePath nesmie byť false (súbor existuje)
    // - $filePath musí začínať cestou $uploadsDir (ochrana pred Path Traversal)
    // - objekt musí byť súbor, nie adresár
    if ($filePath !== false && is_file($filePath) && str_starts_with($filePath, $uploadsDir . DIRECTORY_SEPARATOR)) {
        if (unlink($filePath)) {
            echo htmlspecialchars($fileName) . " has been deleted";
        } else {
            echo "Cannot delete " . htmlspecialchars($fileName) . " due to an error";
        }
    } else {
        echo "File does not exist or invalid path: " . htmlspecialchars($fileName);
    }


    //DEBUG
    /*$filenameR = 'DEBUG_data.txt';
    $file = fopen($filenameR, 'a');
    $queryString = "RemovedFile: {$fileName}\n";
    fwrite($file, $queryString);*/
}
