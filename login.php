<?php
require_once("functions.php");

$json_data = json_decode(file_get_contents("settings.json"), TRUE);
$LOGINpassword = NULL;
if (!empty($_POST)) {
  if (isset($_COOKIE["member_login"]) && $_COOKIE["member_login"] == sha256($json_data['pwd_hash'])) {
    //LOGOUT
    $arr_cookie_options = array(
      'expires' => time() * (-1),
      'secure' => TRUE,     // or false
      'httponly' => FALSE,    // or false
      'samesite' => 'None' // None || Lax  || Strict
    );
    setcookie('member_login', "", $arr_cookie_options);
    echo "Odhlasenie";
  }
  else {
    //LOGIN
    if (!empty($_POST["LOGINpassword"])) {
      $LOGINpassword = $_POST["LOGINpassword"];

      if ($json_data['pwd_hash'] == sha256($LOGINpassword)) {
        $arr_cookie_options = array(
          'expires' => time() + (10 * 365 * 24 * 60 * 60),
          'secure' => TRUE,     // or false
          'httponly' => FALSE,    // or false
          'samesite' => 'None' // None || Lax  || Strict
        );

        setcookie('member_login', sha256(sha256($LOGINpassword)), $arr_cookie_options);
        echo "true";
      }
      else {
        echo "Invalid Login";
      }
    }
  }


  //DEBUG
  /*$filenameR = 'DEBUG_data.txt';
  $file = fopen($filenameR, 'a');
  $queryString = "{$LOGINpassword}\n";
  fwrite($file, $queryString);*/
}
