<?php
require_once("functions.php");

if (isLoggedIn()) {
    $directory = __DIR__ . '/uploads';

    if (is_dir($directory)) {
        // 1. Načítanie súborov priamo so zoradením od najnovšieho pomocou SCANDIR_SORT_DESCENDING
        $scanned_directory = array_filter(scandir($directory, SCANDIR_SORT_DESCENDING), function ($file) use ($directory) {
            return is_file($directory . '/' . $file) && strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'torrent';
        });

        // 2. Úprava poradia podľa času úpravy
        usort($scanned_directory, function ($a, $b) use ($directory, $uploadOrder) {
            $timeA = filemtime($directory . '/' . $a);
            $timeB = filemtime($directory . '/' . $b);

            return strcasecmp($uploadOrder, 'ASC') === 0 ? $timeA <=> $timeB : $timeB <=> $timeA;
        });

        $scannedDirContent = "";
        foreach ($scanned_directory as $value) {
            // Bezpečný zobrazený text
            $safeFileName = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            // URL kódovaný názov pre href
            $urlEncodedFileName = rawurlencode($value);

            $scannedDirContent .= "<div class='row horizontal-center card-panel hoverable blue-grey darken-3 padding5'>\n" .
                "<div class='col s11 wordWrap align-left'><a href='{$linkURL}/uploads/{$urlEncodedFileName}'>{$safeFileName}</a></div>\n" .
                "<div class='col s1 valign-wrapper'><a href='#' class='removeLink fontSizeLarge padding-left2' data-filename='{$safeFileName}'>❌</a></div>\n" .
                "</div>\n\n";
        }

        if (!empty($scanned_directory)) {
            echo $scannedDirContent;
        }
    }
}
