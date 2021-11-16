<!DOCTYPE html>
<html ondrop="upload_file(event)" ondragover="return false">

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Add new things into RSS</title>
    <link rel='stylesheet' href='styles.css'>
    <script src="jquery.min.js"></script>
    <script src='script.js'></script>
</head>

<body>
    <?php
    include('function.php');
    session_start();
    $json_data  = json_decode(file_get_contents("settings.json"), true);
    $dbFileName = $json_data['dbFileName'];
    $db         = new SQLite3($dbFileName);
    $tableName  = $json_data['tableName'];
    $linkURL    = htmlspecialchars($json_data['linkURL']);
    $pageTitle  = htmlspecialchars($json_data['pageTitle']);


    $db->exec("create table if not exists $tableName(id INTEGER PRIMARY KEY UNIQUE, title VARCHAR (250) NOT NULL, link VARCHAR (2500) NOT NULL, pubDate DATETIME NOT NULL)");
    /* will create empty table, if doesnt exist */

    //hash('sha256', $_POST["password"])
    if (!empty($_POST["password"])) {
        if ($json_data['pwd_hash'] == sha256($_POST["password"])) {
            setcookie("member_login", sha256(sha256($_POST["password"])), time() + (10 * 365 * 24 * 60 * 60), NULL, NULL, true, true);
            header("Refresh:0");
        } else {
            echo "Invalid Login";
        }
    }

    /* removing from db */
    if (isset($_POST["id"]) and is_numeric($_POST["id"])) {
        $sql_query = "DELETE FROM `rsstorrent`.`web_feed` WHERE `web_feed`.`id` = " . $_POST["id"] . ";";
        dbquery("DELETE FROM $tableName WHERE id =" . $_POST["id"], $db);
        header("Location: add.php");
        header("Refresh:0; url=add.php");
    }


    //add new entries
    ?>

    <table id="addtab" hidden>
        <tr>
            <td class="magnet">Title: <input id='title' type='text' name='title'><br />Link: <input id='link' type='text' name='link'><br />
                <input onclick="ajax_magnet();" class='tlacitko' type='button' value='Submit'>
            </td>
            <td>
                <div id="drop_file_zone" ondrop="upload_file(event)" ondragover="return false">
                    <div id="drag_upload_file">
                        <p>Drop file here</p>
                        <p><input type="button" value="Select File" onclick="file_explorer();"></p>
                        <input type="file" id="selectfile">
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <?php
    /* show db entries */
    if (isset($_COOKIE["member_login"]) && $_COOKIE["member_login"] == sha256($json_data['pwd_hash'])) {
        echo "<table class='vypis' border='1'>" . "<th>ID</th>" . "<th>Title</th>" . "<th>Link</th>" . "<th>Date</th>" . "<th>Delete</th>";

        $res = $db->query("SELECT * FROM $tableName");

        $tmpArray = [];

        while ($data = $res->fetchArray()) {
            $id      = htmlspecialchars($data["id"]);
            $title   = htmlspecialchars($data["title"]);
            $link    = htmlspecialchars($data["link"]);
            $pubDate = htmlspecialchars($data["pubDate"]);

            $string =  "<tr><td class='center idcol'>" . $id . "</td>\n" .
                "<td class='center titlecol'>" . $title . "</td>\n" .
                "<td class='linkcol' title='" . $link . "'>" . substr($data["link"], 0, 40) . "</td>\n" .
                "<td class='center datecol'>" . $pubDate . "</td>\n" .
                "<td class='center deletecol'><button type='submit' form='form1' onclick=\"sendID(" . $data["id"] . ")\" value='Submit'>Delete</button></td></tr>\n";
            array_unshift($tmpArray, $string);
        }
        echo implode(" ", $tmpArray);
        echo "</table>";

        echo "<form id='form1' method='post'><input type='text' id='id' name='id' value='notsetyet' hidden/></form>
                \n</body>\n</html>\n";
    } else {
        echo '<form action="" method="post" id="frmLogin">
                <div>Password: <input name="password" type="password" value="" class="input-field"></div> 
                <div><input type="submit" name="login" value="Login" class="form-submit-button"></div>      
                </form>';
    }

    ?>
    <input id="button" type="button" value="Logout" onclick="logout();">
    <div id="uploadsSide" hidden>
        <p id="uploadsFolder" hidden></p>
        <p id="filelist" class="bold stayatplace1" onclick="hideme('uploadsFolder'); return false;" title="Hide on click">Files (show):</p>
    </div>
    <?php
    if (isset($_COOKIE["member_login"]) && $_COOKIE["member_login"] == sha256($json_data['pwd_hash'])) {
        echo "<script>document.getElementById('addtab').removeAttribute('hidden');document.getElementById('uploadsSide').removeAttribute('hidden');</script>";


        $directory = getcwd() . '/uploads';
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
        $innerHTML = "<span id=\'linkz\'>";
        foreach ($scanned_directory as $value) {
            $innerHTML .=  "<a href=\"" . $linkURL . "\/uploads\/" . $value . "\">$value</a>&nbsp;<a class=\"nodeco\" href=\"delete.php?filename=" . $value . "\">❌</a><hr>";
        }

        if (count($scanned_directory) > 0) {
            echo "<script>document.getElementById('uploadsFolder').innerHTML = '" . $innerHTML . "</span>';</script>";
        }
    }
    ?>
</body>
</html>
