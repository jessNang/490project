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
		<a href="login.php">Have an account? Login Here</a>
        </form>
	</div>       

<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include('doPingDb.php');

$iniFile="";
doPing();


if(isset($_POST["submit"])){
	$user=$_POST['user'];
	$pass=$_POST['password'];
	$email=$_POST['email'];
	
	$client = new rabbitMQClient($iniFile,"testServer");
	
	$request = array();
	$request['type'] = "register";
	$request['username'] = $user;
	$request['password'] = $pass;
	$request['email'] = $email;
	$response = $client->send_request($request);

	if($response['valid'] === true){
		echo "Account successfully created";
	}
	else{
		echo "That username already exists! Please try again with another!";
	}
}

?>
</body>
</html>
