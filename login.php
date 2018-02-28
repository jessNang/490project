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
#connecting to database
include("account.php");
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$con = mysqli_connect($hostname, $username, $password, "users") or die (mysqli_error());

#checks to see if the username and password the user entered is correct
#if correct -> logs user in, starts a session, and brings them to the welcome page
#else if incorrect -> displays that the password or username they entered is incorrect
if(isset($_POST["submit"])){
	$user=mysqli_real_escape_string($con, $_POST['user']);
	$pass=mysqli_real_escape_string($con, $_POST['password']);
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
        $request['type'] = "login";
        $request['username'] = $user;
        $request['password'] = $pass;
        $response = $client->send_request($request);

        if($response == true){
		$query=mysqli_query($con,"SELECT * FROM login where name='".$user."'");
		$numrows=mysqli_num_rows($query);
		if($numrows!=0){
			while($row=mysqli_fetch_assoc($query)){
				$dbusername=$row['name'];
				$dbpassword=$row['passwd'];
				$dbemail=$row['email'];
			}
			if(($user == $dbusername) && (password_verify($pass,$dbpassword))){
				session_start();
				$_SESSION['sess_user']=$user;
				$_SESSION['sess_email']=$dbemail;
				//redirect browser
				header("Location:welcome.php");
			}	
		} 
		else {
			echo "Invalid username or password!";
		}
	}
}
?>

</body>
</html>
