<?php

/*Checks to see if the user exists and if the password is correct*/
include ("connect.php");

//get values passed from the html login form
$userID = $_POST['user'];
$pass = $_POST['passwd'];

//for preventing sql injection
$userID = stripcslashes($userID);
$pass = stripcslashes($pass);
$userID = mysqli_real_escape_string($userID);
$pass = mysqli_real_escape_string($pass);

//connect to the server and selecting the database
mysqli_connect( $hostname, $username, $password, "users");


//query the database for the user
$result = mysqli_query("select * from login where name = '$userID' and passwd = '$pass'") or die("Failed to query database ".mysqli_error());
$row = mysqli_fetch_array($result);
if ($row['name'] == $userID && $row['passwd'] == $password ){
	echo "Login success!! Welcome ".$row['name'];
	echo '<a href="logout.html">Click here to log out</a>';
} 
else{
	echo "failed to login";
}
?>
