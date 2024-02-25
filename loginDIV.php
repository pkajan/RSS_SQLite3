<?php
require_once("functions.php");

$SUCCESS = FALSE;
$LoginLogoutBTN = "LOGIN";
if (isset($_COOKIE["member_login"]) && $_COOKIE["member_login"] == sha256($json_data['pwd_hash'])) {
  $SUCCESS = TRUE;
  $LoginLogoutBTN = "LOGOUT";
}
$status = ($SUCCESS) ? "hidden" : "";
?>

<div class="row">
   <div class="col s6 offset-s5">
      <form id="loginForm" class="col s12">
         <div class="row padding10">
            <div id="" class="input-field outlined col s12" style="margin: 0 4px;" <?php echo $status; ?>>
               <input id="LOGINpassword" class="validate" type="password" placeholder=" " required>
               <label for="LOGINpassword">Password</label>
            </div>
         </div>
      </form>
   </div>
   <div class="col s1 center-vertical">
      <a class="waves-effect waves-light btn bold loginLogout" id="LOGINsubmit" onclick="submitLoginLogout()">
         <?php echo $LoginLogoutBTN ?>
      </a>
   </div>
</div>
