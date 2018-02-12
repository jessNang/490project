<?php
include("account.php");
mysqli_connect($hostname, $username, $password, "users") or die (mysqli_error());
print "connected";
?>
