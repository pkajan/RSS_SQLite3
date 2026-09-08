<?php
require_once("functions.php");


$LoginLogoutBTN = "LOGIN";
if (isLoggedIn()) {
    $LoginLogoutBTN = "LOGOUT";
}

?>
<script>
    function submitLoginLogout() {
        var isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        var postData = {};

        if (isLoggedIn) {
            // Pri odhlásení posielame explicitne action
            postData = {
                action: 'logout'
            };
        } else {
            var passwordInput = document.getElementById("LOGINpassword");
            if (!passwordInput.value) {
                passwordInput.reportValidity();
                return;
            }
            postData = {
                LOGINpassword: passwordInput.value
            };
        }

        $.post("login.php", postData)
            .done(function(response) {
                if (response.trim() === "true" || response.trim() === "Odhlasenie") {
                    location.reload();
                } else {
                    alert("Chybné heslo!");
                }
            })
            .fail(function(xhr, status, error) {
                console.error("(submitLoginLogout) Error:", error);
            });
    }
</script>

<div class="row">
    <div class="col s4 offset-s7">
        <?php if (!isLoggedIn()): ?>
            <form id="loginForm" class="col s12" onsubmit="event.preventDefault(); submitLoginLogout();">
                <div class="row padding10">
                    <div class="input-field outlined col s12" style="margin: 0 4px;">
                        <input id="LOGINpassword" class="validate" type="password" placeholder=" " required>
                        <label for="LOGINpassword">Password</label>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
    <div class="col s1 center-vertical">
        <a class="waves-effect waves-light btn bold filled loginLogout" id="LOGINsubmit" onclick="submitLoginLogout()">
            <?php echo $LoginLogoutBTN ?>
        </a>
    </div>
</div>