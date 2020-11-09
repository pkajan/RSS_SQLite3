<?php
setcookie("member_login", "", time() -5000, NULL, NULL, true, true);
echo "Logout";
