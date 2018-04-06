<?php
#upcoming page 2
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
        <title>Upcoming Page 2</title>
		<link rel="stylesheet" href="rate.css">
        <link rel="stylesheet" href="welcome.css">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

</head>
<body>
		<!-- Navigation bar -->
        <nav>
                <ul class="main_menu">
                        <li><a href="welcome.php">Home</a></li>
                        <li><a href="nowplaying.php">Now Playing</a><li>
                        <li><a href="upcoming.php">Upcoming</a></li>
                        <li><a href="classics.php">Classics</a></li>
                        <li><a href="discover.php">Discover</a></li>
                        <li><form method="post">
                        	<input type="search" name="search" placeholder="Search movies...">
                                <a class="fa fa-search"></a>
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

//if user typed movie title into search bar
if((isset($_REQUEST['search']))&&($_REQUEST['search']!="")){
	$title=$_REQUEST['search'];
	$category = array();
	array_push($category, $title);
	$client = new rabbitMQClient("dmz.ini","testServer");

	//api request array for movie user searched
	$request = array();
	$request['type'] = "find";
	$request['params'] = $category;
	$request['page'] = "";
	$response = $client->send_request($request);
	$movieTitle;
	
	//Printing api results
	if($response == true){
        	foreach($movie as $key => $value){
					//Movie poster 
                	if($key=="poster_path"){
							#echo "<table style='width:100%'><td><img src='https://image.tmdb.org/t/p/w342".$value."'></td>"; 
                       		#echo "<img src='https://image.tmdb.org/t/p/w342".$value."'>";     
                    		echo "<img src='https://image.tmdb.org/t/p/w342".$value."' height='150'>";
                        	#echo "<br>";
                	}
        	}
			//Movie title
        	foreach($response['data'] as $key => $value){
                	if($key=="title"){
                        	#echo "<td>$value<br><br>";
                      	 	echo "$value<br><br>";
                        	$movieTitle=$value;
                	}
        	}
			
			//Movie rating
        	echo "Rating: ";
        ?>
        <form>
		<!--Rating stars -->
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
			//Movie's release date
        	foreach($response['data'] as $key => $value){
                	if($key=="release_date"){
                        	echo "Release Date: $value<br><br>";
                	}
        	}
			
			//Generes associated with the movie
        	foreach($response['data'] as $key => $value){
                	if($key=="genre_ids"){
                        	echo "Genre: ";
                        	foreach($value as $innerRow => $val){
                                	echo "$val ";
                        	}
                        	echo "<br><br>";
                	}
        	}
			
			//Overview for the movie
        	foreach($response['data'] as $key => $value){
                	if($key=="overview"){
                        	#echo "Overview: $value<br></td></tr></table><br>";
                      		 echo "Overview: $value<br><br>";
                	}
        	}
	}
	
	//Link to find similar movies to the current movie
	echo "<a href='movieRecommend.php?movie=".$movieTitle."'>Similar Movies</a><br>";
}

else{
	$client = new rabbitMQClient("dmz.ini","testServer");
	
	//upcoming movies page 2 api request
	$request = array();
	$request['type'] = "upcoming";
	$request['page'] = "2";
	$response = $client->send_request($request);

	//printing out the api results
	if($response == true){
		foreach($response['data'] as $movie){
			echo "<br>";
			
			//movie poster
			foreach($movie as $key => $value){
                        	if($key=="poster_path"){
                                	echo "<img src='https://image.tmdb.org/t/p/w300".$value."' height='150'>";
                                	echo "<br>";
                        	}
			}
			
			//movie title
			foreach($movie as $key => $value){
                        	if($key=="title"){
									//link to page with detailed info about the movie
                               		echo "<a href='movieFind.php?category=".$value."'>$value</a><br>";
                        	}
				//movie's release date
				if($key=="release_date"){
					echo "Release Date: $value<br>";
				}
			}
		}
	}
}
?>
	<footer>
		<p>
		<a href="upcoming.php">1 &nbsp;</a><br>";
		<a href="upcoming2.php">2 &nbsp;</a><br>";
		<a href="upcoming3.php">3 &nbsp;</a><br>";
		</p>
	</footer>

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