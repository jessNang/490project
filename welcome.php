<?php
session_start();
if(!isset($_SESSION["sess_user"])){
	header("location:login.php");
} else {
?>
<!doctype html>
<html>
<head>
<title>Welcome</title>
</head>
<body>
<h2>Welcome to JLEOMD, <?=$_SESSION['sess_user'];?>! <a href="logout.php">Click here to logout</a></h2>
</body>
</html>
<?php
}
?>
