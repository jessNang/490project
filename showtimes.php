<?php
#Showtimes
#starts session and connects to the user
session_start();
if(!isset($_SESSION["sess_user"])){
        header("location:login.php");
} else {
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=">
    <title>Showtimes</title>
    <link rel="stylesheet" href="discover.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="slicknav.css">
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="jquery.slicknav.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#nav_menu').slicknav({prependTo:"#mobile_menu"});
	    });
    </script>
</head>
<body>
	<!-- Navigation bar -->
    <nav id="mobile_menu">
	<nav id="nav_menu">
        <ul class="main_menu">
            <li><a href="welcome.php">Home</a></li>
            <li><a href="nowplaying.php">Now Playing</a><li>
            <li><a href="upcoming.php">Upcoming</a></li>
            <li><a href="classics.php">Classics</a></li>
            <li><a href="discover.php">Discover</a></li>
			<li><a href="showtimes.php">Showtimes</a></li>
			<li><a href="forum.php">Forum</a></li>
            <li><form method="post">
                <input type="search" name="search" placeholder="Search movies...">
                <a class="fa fa-search"></a>
			</form></li>
            <li><a href="profile.php"><?=$_SESSION['sess_user'];?></a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
	</nav>

	<div class="container">
        <form action="" method="POST">
            <p>Movie</p>
            <input type="text" name="movieName" id="movieName" placeholder="Movie Name" onblur="getLocation()">
			<p>Radius</p>
			<input type="text" name="radius" id="radius" placeholder="Radius: 5, 10, or 20">
			<input type="submit" name="submit" value="Showtimes">
		</form>
    </div>
	<p id="demo"></p>

<script type="text/javascript">
	var x = document.getElementById("demo");
	function getLocation(){
		if (navigator.geolocation){
			navigator.geolocation.getCurrentPosition(showPosition);
		}
		else{ 
			x.innerHTML = "Geolocation is not supported by this browser.";
		}
	}

	function showPosition(position){
		var latitude = position.coords.latitude;
		var longitude = position.coords.longitude;
	}
</script>

<?php 
$_SESSION['latitude'] = '<script>document.write(latitude)</script>';
//echo $_SESSION['latitude']; 
$_SESSION['longitude'] = '<script>document.write(longitude)</script>';
//echo $_SESSION['longitude']; 

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if(isset($_POST["submit"])){
    $movieName=$_POST['movieName'];
	$radius=$_POST['radius'];
	
	$client = new rabbitMQClient("dmz.ini","testServer");
	
	//passing user info array to be inserted into database
	$request = array();
	$request['type'] = "showtimes";
	$request['movie'] = $movieName;
	$request['radius'] = $radius;
	$request['latitude'] = $_SESSION['latitude'];
	$request['longitude'] = $_SESSION['longitude'];
	
	$response = $client->send_request($request);
}
?>
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

