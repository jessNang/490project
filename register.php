<html>
<head>
        <meta charset="utf-8">
        <link href="register.css" type="text/css" rel="stylesheet">
        <title>Registration</title>
</head>

<script>
//function to make sure that the two password fields match
function checkPwd(){
        var password = document.getElementById("password");
        var confPassword = document.getElementById("confPassword");
        
        if(confPassword.value != password.value){
                document.getElementById("mismatch").style.display="block";
                confPassword.value="";
        }

        if(confPassword.value == password.value){
                document.getElementById("mismatch").style.display="none";
        }
}
</script>

<body>
        <div class="container">
        <h1>JLEOMDB Registration</h1><br>
        <form action="" method="POST">
                <p>Username</p>
                <input type="text" name="user" id="user" required="required" placeholder="Enter username">
                
                <p>Password</p>
                <input type="password" name="password" id="password" required="required" placeholder="Enter password" autocomplete="off">
                
                <p>Confirm Password</p>
                <input type="password" name="confPassword" id="confPassword" required="required" placeholder="Confirm your password" autocomplete="off" onblur="checkPwd()">
                
                <span style="display:none;font-family:'Julius Sans One', sans-serif;" id="mismatch">Password is Incorrect</span>

                <p>Email</p>
                <input type="text" name="email" id="email" required="required" placeholder="Enter email ID">
                
                <input type="submit" name="submit" value="Register">
        </form>
	</div>       

<?php
include("account.php");
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$con = mysqli_connect($hostname, $username, $password, "users") or die (mysqli_error());
if(isset($_POST["submit"])){
	$user=mysqli_real_escape_string($con, $_POST['user']);
	$pass=sha1(mysqli_real_escape_string($con, $_POST['password']));
#	$pass=password_hash((mysqli_real_escape_string($con, $_POST['password'])), PASSWORD_DEFAULT);

	$email=mysqli_real_escape_string($con, $_POST['email']);
	
	$client = new rabbitMQClient("authentication.ini","testServer");
	
	if (isset($argv[1]))
	{
  		$msg = $argv[1];
	}
	else
	{
  		$msg = "test message";
	}

	$request = array();
	$request['type'] = "Register";
	$request['username'] = $user;
	$request['password'] = $pass;
	$request['email'] = $email;
	$response = $client->send_request($request);
	
	if($response == true){
		$query=mysqli_query($con,"SELECT * FROM login where name='".$user."'");
		$numrows=mysqli_num_rows($query);
	
		#if the user isn't in the database add them
		if($numrows==0){
			$sql="INSERT INTO login(name, email, passwd) VALUES('$user','$email', '$pass')";

			$result=mysqli_query($con, $sql);
			if($result){
				echo "Account Successfully Created";
			} else {
				echo "Failure!";
			}
		#else display that the user already exists
		} else {
			echo "That username already exists! please try again with another.";
		}
	}
}
?>
</body>
</html>
