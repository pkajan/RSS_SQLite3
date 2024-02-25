<?php
if (!empty($_POST)) {
    $fileName = $_POST["fileName"];

    // Use unlink() function to delete a file 
    if (!unlink("uploads/" . $fileName)) {
        echo ("$fileName cannot be deleted due to an error");
    } else {
        echo ("$fileName has been deleted");
    }


    //DEBUG
    /*$filenameR = 'DEBUG_data.txt';
    $file = fopen($filenameR, 'a');
    $queryString = "RemovedFile: {$fileName}\n";
    fwrite($file, $queryString);*/
}
