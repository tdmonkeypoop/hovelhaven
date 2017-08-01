<?php
	session_start();
	
    require_once ("sql.php");
    
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$username = trim($_POST["uname"]);
		$username = stripslashes($username);
		$username = htmlspecialchars($username); 
		$password = trim($_POST["psw"]);
		$password = stripslashes($password);
		$password = htmlspecialchars($password);
	}
	
	if (!ValidUsername($username))
	{
		$_SESSION["error"] = "Username is invalid; Must be at lease 2 characters!";
		header("location: ../index.php");
	}
	elseif (!ValidPassword($password))
	{
		$_SESSION["error"] = "Password is invalid; Must be at lease 2 characters!";
		header("location: ../index.php");
	}
	elseif (VerifyUser($username, $password))
	{
		$_SESSION["userId"] = GetUserId($username);
		header("location: ../tavern.php");
	}
	else
	{
		$_SESSION["error"] = "Username/Password combo invalid!";
		header("location: ../index.php");
	}
