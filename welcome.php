<?php
#starts session and connects to the user
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
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

</head>
<body>
	<nav>
		<ul class="main_menu">
			<li><a href="welcome.php">Home</a></li>
			<li><a href="#">Upcoming</a></li>
			<li><a href="#">Classics</a></li>
			<li><a href="#">Comedy</a></li>
			<li><a href="#">Family</a></li>
			<li><a href="#">Horror</a></li>
			<li><form>
				<input type="search" placeholder"search...">
				<a href="#" class="fa fa-search"></a>
			</form></li>
			<li><a href="profile.php"><?=$_SESSION['sess_user'];?></a></li>
			<li><a href="logout.php">Logout</a></li>
		</ul>
	</nav>
	</div>
	<h2>Welcome to JLEOMD, <?=$_SESSION['sess_user'];?>!</h2>
</body>
</html>
<?php


//Expire the session if user is inactive for 30 minutes or more.
$expireAfter = 30;
 
//Check to see if our "last action" session variable has been set.
if(isset($_SESSION['last_action'])){
    
    //Figure out how many seconds have passed since the user was last active.
    $secondsInactive = time() - $_SESSION['last_action'];
    
    //Convert our minutes into seconds.
    $expireAfterSeconds = $expireAfter * 60;
    
    //Check to see if they have been inactive for too long.
    if($secondsInactive >= $expireAfterSeconds){
        //User has been inactive for too long. Kill their session.
        session_unset();
        session_destroy();
	header("location:login.php");
    }
    
}
 
//Assign the current timestamp as the user's latest activity
$_SESSION['last_action'] = time();
}
?>
