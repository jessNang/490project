<?php
$user = "";
$email = "";

//connect to the database
$db = mysqli_connect('localhost', 'root', 'IT490password', 'users');

//if the registration submit button is clicked
if (isset($_POST['sub'])) {
	$user = mysqli_real_escape_string($_POST['user']);
	$pass1 = mysqli_real_escape_string($_POST['passwd']);
	$pass2 = mysqli_real_escape_string($_POST['confPassword']);
	$email = mysqli_real_escape_string($_POST['email']);
}

$psswd = md5($pass1); //encrypt the password before storing it for security purposes

$sql = "INSERT INTO login (name, passwd, email) VALUES ('$user', '$psswd', '$email')";

mysqli_query($db, $sql);
?>
