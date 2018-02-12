<?php
#starts session and connects to the one from the login page
session_start();
if(!isset($_SESSION["sess_user"])){
	header("location:login.php");
} else {
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Welcome</title>
	<link rel="stylesheet" href="welcome.css">
</head>
<body>
	<nav>
		<ul class="main_menu">
			<li><a href="welcome.php">Home</a></li>
			<li><a href="#">Action</a></li>
			<li><a href="#">Adventure</a></li>
			<li><a href="#">Comedy</a></li>
			<li><a href="#">Family</a></li>
			<li><a href="#">Horror</a></li>
			<li><form>
				<input type="search" placeholder"search...">
				<a href="#" class="fa fa-search"></a>
			</form></li>
			<li><a href="logout.php">Logout</a></li>
		</ul>
	</nav>
	</div>
	<h2>Welcome to JLEOMD, <?=$_SESSION['sess_user'];?>!</h2>
</body>
</html>
<?php
}
?>
