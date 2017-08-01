<?php
	require_once ("config.php");
	
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

	function VerifyUser($username, $password)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

		$encryptedPassword = md5($password . "monKey");
		$sql = "SELECT password FROM users where username = '$username'";
		$result = $conn->query($sql);

		if ($result ->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			if ($encryptedPassword == $row["password"])
			{
				return true;
			}
		}
		
		return false;
	}
	
	function UserExists($username)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		$sql = "SELECT password FROM users where username = '$username'";
		$result = $conn->query($sql);

		if ($result ->num_rows > 0)
		{
			return true;
		}
		
		return false;
	}
	
	function GetUserId($username)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		$sql = "SELECT id FROM users where username = '$username'";
		$result = $conn->query($sql);

		if ($result ->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			return $row["id"];
		}
		
		return null;
	}
	
	function GetUsername($id)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		$sql = "SELECT username FROM users where id = '$id'";
		$result = $conn->query($sql);

		if ($result ->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			return $row["username"];
		}
		
		return null;
	}
	
	function GetUserGameId($userId)
	{
	    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		$sql = "SELECT gameid FROM users where id = '$userId'";
		$result = $conn->query($sql);

		if ($result ->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			return $row["gameid"];
		}
		
		return null;
	}
	
	function NewUser($username, $password)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

		$encryptedPassword = md5($password . "monKey");
		$sql = "INSERT INTO users (username, password) values ('$username','$encryptedPassword')";
		$conn->query($sql);
	}
	
	function NewGame($userId)
	{
	    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

		$sql = "INSERT INTO games (userid, currentdate, currentmoney, mug_ale, glass_wine, common_meal, fine_meal, chicken, pork_chop, carrot, potato, barrel_wine, keg_ale, full_chicken, pig, carrot_bag, potato_sack) values ('$userId', '0y12m30d', 10.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)";
		$conn->query($sql);
		
		$sql = "SELECT gameid FROM games where userid = '$userId'";
		$result = $conn->query($sql);

		$row = $result->fetch_assoc();
		$gameId = $row["gameid"];
		
		$sql = "UPDATE users SET gameid = '$gameId' WHERE id='$userId'";
		$result = $conn->query($sql);
		
		return $gameId;
	}
	
	function GetCurrentGame($gameId)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		
		$sql = "SELECT * FROM games WHERE gameid = '$gameId'";
		$result = $conn->query($sql);

		$row = $result->fetch_assoc();
		
		return $row;
	}
	
	function EndTurn($currentGame)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		
		$sql = "UPDATE games Set ";
		$gameId = $currentGame['gameid'];
		$variableArray = array();
		foreach($currentGame as $key => $value)
		{
			$variableArray[] = $key . " = '" . $value . "'";
		}
		
		$sql .= implode(",", $variableArray);
		$sql .= " WHERE gameid = '$gameId'";
		$conn->query($sql);
	}
	
	function FormatDate($days)
	{
		$numberOfYears = ($days / 360)+1;
		$numberOfMonths = (($days % 360) / 30)+1;
		$numberOfDays = (($days % 360) % 30)+1;
		
		return "Day " . (int)$numberOfDays . " of Month " . (int)$numberOfMonths . " of Year " . (int)$numberOfYears;
	}