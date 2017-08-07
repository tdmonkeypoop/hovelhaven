<?php
	session_start();
	
    require_once ("database.php");
    

	$previousURI = $_SESSION['CurrentURI'];
	$_SESSION['CurrentURI'] = $_SERVER['REQUEST_URI'];

	$previousURIPieces = explode('/', $previousURI);
	$previousURIPiecesCount = count($previousURIPieces) -1;
	$previousURI = $previousURIPieces[$previousURIPiecesCount];
    
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$username = htmlspecialchars(stripslashes(trim($_POST["uname"])));
		$password = htmlspecialchars(stripslashes(trim($_POST["psw"])));
		$repassword = htmlspecialchars(stripslashes(trim($_POST["psw2"])));
	}
	
	if(!empty($repassword))
	{
		if($password !== $repassword)
		{
			$_SESSION["error"] = "Passwords do not match!";
			header("location: ../${previousURI}");
			exit();
		}
	}
	
	if (!ValidUsername($username))
	{
		$_SESSION["error"] = "Username is invalid; Must be at lease 2 characters!";
		header("location: ../${previousURI}");
		exit();
	}
	elseif (!ValidPassword($password))
	{
		$_SESSION["error"] = "Password is invalid; Must be at lease 2 characters!";
		header("location: ../${previousURI}");
		exit();
	}
	elseif (VerifyUser($username, $password))
	{
		if($previousURI == "index.php")
		{
			$_SESSION["userId"] = GetUserId($username);
			header("location: ../tavern.php");
			exit();
		}
		else
		{
			$_SESSION["error"] = "Sorry username " . $username . " is already taken!";
			header("location: ../${previousURI}");
			exit();
		}
	}
	else
	{
		if($previousURI == "index.php")
		{
			$_SESSION["error"] = "Incorrect Username/Password Combo!";
			header("location: ../tavern.php");
			exit();
		}
		else
		{
			NewUser($username, $password);
			header("location: ../index.php");
			exit();
		}
	}
	
	function GetUserId($username)
	{
		$db = Database::getInstance();
		$sql = "SELECT id FROM users where username = '$username'";
		$result = $db->query($sql);

		if ($result ->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			return $row["id"];
		}
		
		return null;
	}
	
	function VerifyUser($username, $password)
	{
		$db = Database::getInstance();

    	$sql = "SELECT password FROM users where username = '$username' and password = md5('${password}monKey')";
		$valid_user = $db->query($sql);

		if ($valid_user->num_rows > 0)
		{
				return true;
    	}
    	else
    	{
				return false;
    	}
	}
	
	function ValidUsername($username)
	{
		if (strlen($username) > 2)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function ValidPassword($password)
	{
		if (strlen($password) > 2)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function NewUser($username, $password)
	{
		$db = Database::getInstance();

		$encryptedPassword = md5($password . "monKey");
		$sql = "INSERT INTO users (username, password) values ('$username','$encryptedPassword')";
		$db->query($sql);
	}

