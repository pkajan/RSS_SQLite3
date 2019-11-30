<!DOCTYPE html>
<html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <title>Add new things into RSS</title>
        <link rel='stylesheet' href='styles.css'>
        <script src='script.js'></script>
    </head>
    <body>

<?php
session_start();

$json_data  = json_decode(file_get_contents("settings.json"), true);
$dbFileName = $json_data['dbFileName'];
$tableName  = $json_data['tableName'];
$linkURL = $json_data['linkURL'];
$db         = new SQLite3($dbFileName);
$db->exec("CREATE TABLE $tableName(id INTEGER PRIMARY KEY UNIQUE, title VARCHAR (250) NOT NULL, link VARCHAR (2500) NOT NULL, pubDate DATETIME NOT NULL)");
/* will create empty table, if doesnt exist */

function dbquery($string)
{
    global $db;
    $db->exec("$string");
}

function add_to_db()
{
    global $tableName;
    if (!empty($_POST["title"]) and !empty($_POST["link"])) {
        $title   = htmlspecialchars($_POST["title"]);
        $link    = htmlspecialchars($_POST["link"]);
        $pubDate = htmlspecialchars(date('Y-m-d H:i:s')); //2018-08-01 00:00:00
        
        $textquery = "INSERT INTO $tableName (title, link, pubDate) VALUES('$title', '$link', '$pubDate')";
        dbquery($textquery);
        
        header("Location: add.php");
    }
}

if (!empty($_POST["password"])) {
    if ($json_data['pwd_hash'] == md5($_POST["password"])) {
        setcookie("member_login", md5(md5($_POST["password"])), time() + (10 * 365 * 24 * 60 * 60));
        header("Refresh:0");
    } else {
        echo "Invalid Login";
    }
}

if (isset($_COOKIE["member_login"]) && $_COOKIE["member_login"] == md5($json_data['pwd_hash'])) {
    add_to_db();
    
    echo "<form class='tlacitka' method='post' enctype='multipart/form-data'>" . "<table>" . "<tr>" . "<td class='magnet'>" . "Title: <input type='text' name='title'><br/>" . "Link: <input type='text' name='link'><br/>" . "<input class='tlacitko' type='submit' value='Submit'>" . "</td>" . "<td class='torrent'>" . "Select torrent to upload:" . "<input type='file' name='fileToUpload' id='fileToUpload'>" . "<input class='tlacitko' type='submit' value='Upload torrent' name='submiter'>" . "</td>" . "</tr></table></form>";
    
    /* upload */
    if (isset($_POST["submiter"])) {
        $target_dir = "uploads/";
        if (!file_exists('path/to/directory')) {
            mkdir($target_dir, 0777, true);
        }
        $target_file  = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk     = 1;
        $torrFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (isset($_POST["submiter"])) {
            // Check if file already exists
            if (file_exists($target_file)) {
                echo "<div class='center'>Sorry, file already exists.</div>";
                $uploadOk = 0;
            }
            // Check file size
            if ($_FILES["fileToUpload"]["size"] > 500000) {
                echo "<div class='center'>Sorry, your file is too large.</div>";
                $uploadOk = 0;
            }
            // Allow certain file formats
            if ($torrFileType != "torrent") {
                echo "<div class='center'>Sorry, only torrent files are allowed.</div>";
                $uploadOk = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "<div class='center'>Sorry, your file was not uploaded.</div>";
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    echo "<div class='center'>The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.</div>";
                    $_POST["title"] = basename($_FILES["fileToUpload"]["name"], ".torrent");
                    $_POST["link"]  = "$linkURL/uploads/" . basename($_FILES["fileToUpload"]["name"]);
                    add_to_db($string, $json_data, $mysqli);
                } else {
                    echo $target_file . "<br>";
                    echo "<div class='center'>Sorry, there was an error uploading your file.</div>";
                }
            }
        }
    }
    
    /* show db entries */
    echo "<table class='vypis' border='1'>" . "<th>ID</th>" . "<th>Title</th>" . "<th>Link</th>" . "<th>Date</th>" . "<th>Delete</th>";
    
    $res = $db->query("SELECT * FROM $tableName");
    
    while ($data = $res->fetchArray()) {
        $id      = htmlspecialchars($data["id"]);
        $title   = htmlspecialchars($data["title"]);
        $link    = htmlspecialchars($data["link"]);
        $pubDate = htmlspecialchars($data["pubDate"]);
        
        echo "<tr><td>" . $id . "</td>\n";
        echo "<td>" . $title . "</td>\n";
        echo "<td title='" . $link . "'>" . substr($data["link"], 0, 40) . "</td>\n";
        echo "<td>" . $pubDate . "</td>\n";
        echo "<td><button type='submit' form='form1' onclick=\"sendID(" . $data["id"] . ")\" value='Submit'>Delete</button></td></tr>\n";
    }
    echo "</table>";
    
    echo "<form id='form1'><input type='text' id='id' name='id' value='notsetyet' hidden/></form>
    \n</body>\n</html>\n";
    
    /* removing from db */
    if (isset($_GET["id"]) and is_numeric($_GET["id"])) {
        $sql_query = "DELETE FROM `rsstorrent`.`web_feed` WHERE `web_feed`.`id` = " . $_GET["id"] . ";";
        dbquery("DELETE FROM $tableName WHERE id =" . $_GET["id"]);
        header("Location: add.php");
        header("Refresh:0; url=add.php");
    }
} else {
    echo '<form action="" method="post" id="frmLogin">
        <div>Password: <input name="password" type="password" value="" class="input-field"> 
        <div><input type="submit" name="login" value="Login" class="form-submit-button"></div>      
    </form>';
}
