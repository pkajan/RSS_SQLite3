<?php
require_once("functions.php");

if (isLoggedIn()) {
    $directory = __DIR__ . '/uploads';

    if (is_dir($directory)) {
        $scanned_directory = array_filter(scandir($directory, SCANDIR_SORT_DESCENDING), function ($file) { return strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'torrent';    });

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
