<?php
#destorying the session when the user logs out of JLEOMDB
session_start();
unset($_SESSION['sess_user']);
unset($_SESSION['sess_email']);
session_destroy();
header("location:login.php");
?>
