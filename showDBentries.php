<?php
require_once("functions.php");

if (isLoggedIn()) {
    $json_data = json_decode(file_get_contents("settings.json"), TRUE);
    $tableName = $json_data['tableName'] ?? '';
    $linkURL = htmlspecialchars($json_data['linkURL'] ?? '', ENT_QUOTES, 'UTF-8');
    $pageTitle = htmlspecialchars($json_data['pageTitle'] ?? '', ENT_QUOTES, 'UTF-8');

    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
        http_response_code(400);
        echo "Invalid table name";
        exit;
    }

    // Záznamy načítame v obrátenom poradí priamo z DB (O(1) réžia v PHP)
    $res = getDB()->query("SELECT * FROM {$tableName} ORDER BY id DESC");

    $output = "";

    while ($data = $res->fetchArray(SQLITE3_ASSOC)) {
        $id = htmlspecialchars($data["id"], ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars($data["title"], ENT_QUOTES, 'UTF-8');
        $link = htmlspecialchars($data["link"], ENT_QUOTES, 'UTF-8');
        $pubDate = htmlspecialchars($data["pubDate"], ENT_QUOTES, 'UTF-8');

        $output .= "\n<div class='row card-panel hoverable wordWrap valign-wrapper padding3 gap3 blue-grey darken-2'>\n" .
            "<div class='col s1 padding3 horizontal-center'>{$id}</div>\n" .
            "<div class='col s5 padding3 tooltip truncate bold' title='{$title}'>{$title}</div>\n" .
            "<div class='col s3 padding3 truncate tooltip' title='{$link}'>{$link}</div>\n" .
            "<div class='col s2 padding3 truncate horizontal-center' title='{$pubDate}'>{$pubDate}</div>\n" .
            "<div class='col s1 padding3 horizontal-center'><a href='#' class='removeDBentry waves-effect waves-light btn filled bold' data-id='{$id}'>Delete</a></div></div>\n\n";
    }

    echo $output;
}
