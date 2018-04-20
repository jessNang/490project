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
        <title>Classics</title>
	<link rel="stylesheet" href="rate.css">
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
                        <li><form>
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

//movie search
if((isset($_REQUEST['search']))&&($_REQUEST['search']!="")){
        $title=$_REQUEST['search'];
        $category = array();
        array_push($category, $title);
        $client = new rabbitMQClient("dmz.ini","testServer");

        $request = array();
        $request['type'] = "find";
        $request['params'] = $category;
        $request['page'] = "";
        $response = $client->send_request($request);
        $movieTitle;

	//getting movie information
        if($response == true){
                foreach($movie as $key => $value){
			//movie poster
                        if($key=="poster_path"){
#                               echo "<table style='width:100%'><td><img src='https://image.tmdb.org/t/p/w342".$value."'></td>"; 
                                 #echo "<img src='https://image.tmdb.org/t/p/w342".$value."'>";     
                                echo "<img src='https://image.tmdb.org/t/p/w342".$value."' height='150'>";
                                #echo "<br>";
                        }
                }
		//movie title
                foreach($response['data'] as $key => $value){
                        if($key=="title"){
                                #echo "<td>$value<br><br>";
                                echo "$value<br><br>";
                                $movieTitle=$value;
                        }
                }
		//movie's rating
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
		//movie's release date
                foreach($response['data'] as $key => $value){
                        if($key=="release_date"){
                                echo "Release Date: $value<br><br>";
                        }
                }
		//genres of the movie
                foreach($response['data'] as $key => $value){
                        if($key=="genre_ids"){
                                echo "Genre: ";
                                foreach($value as $innerRow => $val){
                                        echo "$val ";
                                }
                                echo "<br><br>";
                        }
                }
		//movie overview
                foreach($response['data'] as $key => $value){
                        if($key=="overview"){
                                #echo "Overview: $value<br></td></tr></table><br>";
                                 echo "Overview: $value<br><br>";
                        }
                }
        }
	//link for finding similar movies
        echo "<a href='movieRecommend.php?movie=".$movieTitle."'>Similar Movies</a><br>";
}

//classic movies
else{
	$client = new rabbitMQClient("dmz.ini","testServer");
	
	//setting current movie page number
	$pageNumber=$_REQUEST['page'];
	if(($pageNumber != "") && ($pageNumber != "1")){
		$currentPage = intval($pageNumber);
	}
	else{
		$currentPage = 1;
	}

	$request = array();
	$request['type'] = "classics";
	$request['page'] = "$currentPage";
	$response = $client->send_request($request);

	//getting movie information
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
			//movie title and release date
			foreach($movie as $key => $value){
                        	if($key=="title"){
                                	echo "<a href='movieFind.php?category=".$value."'>$value</a><br>";
                        	}

				if($key=="release_date"){
					echo "Release Date: $value<br>";
				}
			}
		}
	}
}
?>
	<footer>
		<?php 
		//next and previous pages link		
		echo"<p>";
		if($currentPage != 1){
			$previous = $currentPage - 1;
			echo "<a href='classics.php?page=".$previous."'>Previous Page</a>";
			echo "&nbsp;&nbsp;&nbsp;";
			$next = $currentPage + 1;
			echo "<a href='classics.php?page=".$next."'>Next Page</a><br>";
		}
		if($currentPage == 1){
			$next = $currentPage + 1;
			echo "<a href='classics.php?page=".$next."'>Next Page</a><br>";
		}
		echo"</p>";
		?>
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

