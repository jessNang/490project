<?php
#forums page
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
	<meta name="viewport" content="width=device-width, initial-scale=">
	<title>Forum Page</title>
	<link rel="stylesheet" href="discover.css">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="slicknav.css">
    	<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    	<script src="jquery.slicknav.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<!--<script type="text/javascript">
		$(document).ready(function(){
			$('#nav_menu').slicknav({prependTo:"#mobile_menu"});
	    	});
    	</script>-->
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
			<li><form>
				<input type="search" name="search" placeholder="Search movies...">
                                <a class="fa fa-search"></a>

			</form></li>
			<li><a href="profile.php"><?=$_SESSION['sess_user'];?></a></li>
			<li><a href="logout.php">Logout</a></li>
		</ul>
        </nav>
	</nav>

  	<br/>
	<h3 align="center">Forum Page</h3>
	<br/>

	<div class="container">
		<form method="POST" id="comment_form">
			<div class="form-group">
				<input type="text" name="comment_name" id="comment_name" class="form-control" placeholder="Enter Name" />
			</div>
			<div class="form-group">
				<textarea name="comment_content" id="comment_content" class="form-control" placeholder="Enter Message" rows="5" ></textarea>
			</div>
			<div class="form-group">
				<input type="hidden" name="comment_id" id="comment_id" value="0" />
				<input type="submit" name="submit" id="submit" class="btn btn-info" value="Submit" />
			</div>
		</form>
		<span id="comment_message"></span>
		<br />
		<div id="display_comment"></div>
	</div>
</body>
</html>

<script>
	$(document).ready(function(){
		$('#comment_form').on('submit', function(event){
			event.preventDefault();
			var form_data = $(this).serialize();
			$.ajax({
				url:"addComment.php",
				method:"POST",
				data:form_data,
				dataType:"JSON",
				success:function(data){
					if(data.error != ''){
						 $('#comment_form')[0].reset();
						 $('#comment_message').html(data.error);
						 $('#comment_id').val('0');
						 load_comment();
					}
				}	
			})
		});

		load_comment();

		function load_comment(){
			$.ajax({
				url:"getComment.php",
				method:"POST",
				success:function(data){
					$('#display_comment').html(data);
				}
			})
		 }

		$(document).on('click', '.reply', function(){
			var comment_id = $(this).attr("id");
			$('#comment_id').val(comment_id);
			$('#comment_name').focus();
		});
	
	});
	</script>

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
