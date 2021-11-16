<?php
$filename = $_GET["filename"];

// Use unlink() function to delete a file 
if (!unlink("uploads/" . $filename)) {
    echo ("$filename cannot be deleted due to an error");
} else {
    echo ("$filename has been deleted");
}

header('Location: ' . "add.php");
