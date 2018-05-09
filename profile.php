<?php
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
	<title>Profile</title>
	<link rel="stylesheet" href="rate.css">
	<link rel="stylesheet" href="profile.css">
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
                        <li><a href="nowplaying.php">Now Playing</a></li>
                        <li><a href="upcoming.php">Upcoming</a></li>
                        <li><a href="classics.php">Classics</a></li>
                        <li><a href="discover.php">Discover</a></li>
			<li><a href="showtimes.php">Showtimes</a></li>
			<li><a href="forum.php">Forum</a></li>
                        <li><form>
                        	<input type="search" name="search" placeholder="Search movies...">
                                <a class="fa fa-search"></a>
			</form></li>
                        <li><a href="profile.php"><?=$_SESSION['sess_user'];?></a></li>
                        <li><a href="logout.php">Logout</a></li>
                </ul>
        </nav>
	</nav>
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include('doPingDmz.php');

$iniFile="";
doPing();

if((isset($_REQUEST['search']))&&($_REQUEST['search']!="")){
        $title=$_REQUEST['search'];
        $category = array();
        array_push($category, $title);
        $client = new rabbitMQClient($iniFile,"testServer");

        $request = array();
        $request['type'] = "find";
        $request['params'] = $category;
        $request['page'] = "";
        $response = $client->send_request($request);
        $movieTitle;
        if($response == true){
                foreach($movie as $key => $value){
                        if($key=="poster_path"){
#                               echo "<table style='width:100%'><td><img src='https://image.tmdb.org/t/p/w342".$value."'></td>"; 
                                 #echo "<img src='https://image.tmdb.org/t/p/w342".$value."'>";     
                                echo "<img src='https://image.tmdb.org/t/p/w342".$value."' height='150'>";
                                #echo "<br>";
                        }
                }
                foreach($response['data'] as $key => $value){
                        if($key=="title"){
                                #echo "<td>$value<br><br>";
                                echo "$value<br><br>";
                                $movieTitle=$value;
                        }
                }
                echo "Rating: ";
        ?>
        <form>
        <fieldset class="starability-growRotate">
                <input type="radio" id="rate5" name="rating" value="5" />
                <label for="rate5" title="Terrible">5 stars</label>

                <input type="radio" id="rate4" name="rating" value="4" />
                <label for="rate4" title="Not good">4 stars</label>

                <input type="radio" id="rate3" name="rating" value="3" />
                <label for="rate3" title="Average">3 stars</label>

                <input type="radio" id="rate2" name="rating" value="2" />
                <label for="rate2" title="Very good">2 stars</label>

                <input type="radio" id="rate1" name="rating" value="1" />
                <label for="rate1" title="Amazing">1 star</label>
        </fieldset>
        </form>
        <?php
                foreach($response['data'] as $key => $value){
                        if($key=="release_date"){
                                echo "Release Date: $value<br><br>";
                        }
                }
                foreach($response['data'] as $key => $value){
                        if($key=="genre_ids"){
                                echo "Genre: ";
                                foreach($value as $innerRow => $val){
                                        echo "$val ";
                                }
                                echo "<br><br>";
                        }
                }
                foreach($response['data'] as $key => $value){
                        if($key=="overview"){
                                #echo "Overview: $value<br></td></tr></table><br>";
                                 echo "Overview: $value<br><br>";
                        }
                }
        }
        echo "<a href='movieRecommend.php?movie=".$movieTitle."'>Similar Movies</a><br>";
}
else{
?>
	<div class="card">
		<div class="imgBox"><img src="image3.jpg" height="400px" width="300px"></div>
		<div class="details">
			<div class="content">
				<h3><?=$_SESSION['sess_user'];?><br><span><?=$_SESSION['sess_email'];?></span></h3>
				<ul>
					<li><a href="https://www.facebook.com/"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
					<li><a href="https://twitter.com/?lang=en"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                        <li><a href="https://plus.google.com"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>
                                        <li><a href="https://www.instagram.com/"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>

				</ul>
			</div>
		<div>
	</div>
</body>
</html>
<?php
}
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


