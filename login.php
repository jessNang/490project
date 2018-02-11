<html>
<head>
        <meta charset="utf-8">
        <link href="https://fonts.googleapis.com/css?family=Libre+Barcode+39+Text" rel="stylesheet"> 
        <link href="https://fonts.googleapis.com/css?family=Julius+Sans+One" rel="stylesheet">
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
include("account.php");
$con = mysqli_connect($hostname, $username, $password, "users") or die (mysqli_error());

if(isset($_POST["submit"])){
	$user=mysqli_real_escape_string($con, $_POST['user']);
	$pass=mysqli_real_escape_string($con, $_POST['password']);

	$query=mysqli_query($con,"SELECT * FROM login where user='".$user."' AND password='".$pass."'");
	$numrows=mysqli_num_rows($query);

	if($numrows!=0){
		while($row=mysqli_fetch_assoc($query)){
			$dbusername=$row['user'];
			$dbpassword=$row['password'];
		}

		if($user == $dbusername && $pass == $dbpassword){
			session_start();
			$_SESSION['sess_user']=$user;

			//redirect browser
			header("Location:welcome.php");
		}
	} 
	else {
		echo "Invalid username or password!";
	}
}
?>

</body>
</html>
