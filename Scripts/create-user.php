<?php
	session_start();
	
    require_once ("sql.php");
    
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$username = trim($_POST["uname"]);
		$username = stripslashes($username);
		$username = htmlspecialchars($username); 
		$password = trim($_POST["psw1"]);
		$password = stripslashes($password);
		$password = htmlspecialchars($password);
		$repassword = trim($_POST["psw2"]);
		$repassword = stripslashes($repassword);
		$repassword = htmlspecialchars($repassword);
	}
	
	if (!ValidUsername($username))
	{
		$_SESSION["createUserError"] = "Username is invalid; Must be at lease 2 characters!";
		header("location: ../create-user.php");
	}
	elseif ($password != $repassword)
	{
	    $_SESSION["createUserError"] = "Password do not match, please check passwords!";
	    header("location: ../create-user.php");
	}
	elseif (!ValidPassword($password))
	{
		$_SESSION["createUserError"] = "Password is invalid; Must be at lease 2 characters!";
		header("location: ../create-user.php");
	}
	elseif (UserExists($username))
	{
		$_SESSION["createUserError"] = "Sorry username " . $username . " is already taken!";
		header("location: ../create-user.php");
	}
	else
	{
		NewUser($username, $password);
		header("location: ../index.php");
	}