<?php
require_once("functions.php");

// 1. Načítanie nastavení
$json_data = json_decode(file_get_contents("settings.json"), TRUE);

if (isLoggedIn()) {
    $linkURL = htmlspecialchars($json_data['linkURL'] ?? '', ENT_QUOTES, 'UTF-8');
    $directory = __DIR__ . '/uploads';

    if (is_dir($directory)) {
        $scanned_directory = array_diff(scandir($directory, SCANDIR_SORT_DESCENDING), array('..', '.'));

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
