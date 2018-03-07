<html>
<head>
        <meta charset="utf-8">
        <link href="login.css" type="text/css" rel="stylesheet">
        <title>Login</title>
</head>
<body>
        <div class="container">
        <h1>JLEOMDB Sign-In</h1><br>
        <form action="" method="POST">
                <p>Username</p>
                <input type="text" name="user" id="user" placeholder="Enter username">
                
                <p>Password</p>
                <input type="password" name="password" id="password"  placeholder="Enter password">
                
                <input type="submit" name="submit" value="Login">
                <a href="register.php">New User? Register here</a>
        </form>
        </div>

<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if(isset($_POST["submit"])){
	$user=$_POST['user'];
	$pass=$_POST['password'];
	$client = new rabbitMQClient("db.ini","testServer");
        
	$request = array();
        $request['type'] = "login";
        $request['username'] = $user;
        $request['password'] = $pass;
        $response = $client->send_request($request);
	
	if($response['valid'] === true){
		session_start();
                $_SESSION['sess_user']=$response['userName'];
		$_SESSION['sess_email']=$response['em'];
                //redirect browser
                header("Location:welcome.php");
	}
	else{
		echo "Invalid username or password";
	}

}
?>

</body>
</html>
