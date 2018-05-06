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
        <title>Movie</title>
        <link rel="stylesheet" href="welcome.css">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

</head>
<body>
        <nav>
                <ul class="main_menu">
                        <li><a href="welcome.php">Home</a></li>
                        <li><a href="nowplaying.php">Now Playing</a><li>
                        <li><a href="upcoming.php">Upcoming</a></li>
                        <li><a href="classics.php">Classics</a></li>
                        <li><a href="discover.php">Discover</a></li>
			<li><a href="showtimes.php">Showtimes</a></li>
			<li><a href="forum.php">Forum</a></li>
                        <li><form>
				<input type="search" placeholder="Search movies...">
                                <a href="find.php" class="fa fa-search"></a>
			</form></li>
                        <li><a href="profile.php"><?=$_SESSION['sess_user'];?></a></li>
                        <li><a href="logout.php">Logout</a></li>
                </ul>
        </nav>
        </div>

<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if(isset($_POST['search'])){
	$movie=$_POST['search'];
	echo "Movie: $movie <br>";
	$category = array();
	array_push($category, $movie);
	print_r($category);
	$client = new rabbitMQClient("dmz.ini","testServer");

	$request = array();
	$request['type'] = "find";
	$request['params'] = $category;
	$request['page'] = "";
	$response = $client->send_request($request);
	$movieTitle;
	if($response == true){
		foreach($movie as $key => $value){
        		if($key=="poster_path"){
#               		echo "<table style='width:100%'><td><img src='https://image.tmdb.org/t/p/w342".$value."'></td>"; 
        			#echo "<img src='https://image.tmdb.org/t/p/w342".$value."'>";     
	            		echo "<img src='https://image.tmdb.org/t/p/w342".$value."'>";
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
