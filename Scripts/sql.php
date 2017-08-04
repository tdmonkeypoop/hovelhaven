<?php
	require ("database.php");
	
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
	
	function UserExists($username)
	{
		$db = Database::getInstance();
		$sql = "SELECT password FROM users where username = '$username'";
		$result = $db->query($sql);

		if ($result ->num_rows > 0)
		{
			return true;
		}
		
		return false;
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
	
	function GetUsername($id)
	{
		$db = Database::getInstance();
		$sql = "SELECT username FROM users where id = '$id'";
		$result = $db->query($sql);

		if ($result ->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			return $row["username"];
		}
		
		return null;
	}
	
	function GetUserGameId($userId)
	{
	    $db = Database::getInstance();
		$sql = "SELECT gameid FROM users where id = '$userId'";
		$result = $db->query($sql);

		if ($result ->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			return $row["gameid"];
		}
		
		return null;
	}
	
	function NewUser($username, $password)
	{
		$db = Database::getInstance();

		$encryptedPassword = md5($password . "monKey");
		$sql = "INSERT INTO users (username, password) values ('$username','$encryptedPassword')";
		$db->query($sql);
	}
	
	function NewGame($userId)
	{
	    $db = Database::getInstance();

		$sql = "INSERT INTO games (gameid, userid, currentdate, currentmoney, mug_ale, glass_wine, common_meal, fine_meal, chicken, pork_chop, carrot, potato, barrel_wine, keg_ale, full_chicken, pig, carrot_bag, potato_sack, mug_ale_price, glass_wine_price, common_meal_price, fine_meal_price) values ('$userId', '$userId', 0, 10.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.05, .20, .8, 2.2)";
		$db->query($sql);
		
		$sql = "SELECT gameid FROM games where userid = '$userId'";
		$result = $db->query($sql);

		$row = $result->fetch_assoc();
		$gameId = $row["gameid"];
		
		$sql = "UPDATE users SET gameid = '$gameId' WHERE id='$userId'";
		$result = $db->query($sql);
		
		return $gameId;
	}
	
	function GetCurrentGame($gameId)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM games WHERE gameid = '$gameId' ORDER BY currentdate DESC";
		$result = $db->query($sql);

		$row = $result->fetch_assoc();
		
		return $row;
	}
	
	function GetGameByDate($gameId, $date)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM games WHERE (gameid = '$gameId') AND (currentdate = '$date')";
		$result = $db->query($sql);

		if(!empty($result))
		{
			$row = $result->fetch_assoc();
		
			return $row;
		}
		else
		{
			return null;
		}
	}
	
	function EndTurn($currentGame)
	{
		$db = Database::getInstance();
		
		foreach($currentGame as $key => $value)
		{
			$keys[] = $key;
			$values[] = $value;
		}
		$keysImploded = implode(", ", $keys);
		$valuesImploded = implode(" , ", $values);
		
		$sql = "INSERT INTO games (" . $keysImploded . ") VALUES (" . $valuesImploded . ")";
    
		$db->query($sql);
		
		$sql = "INSERT INTO test VALUES ('" . $sql ."')";
		$db->query($sql);
	}
	
	function FormatDate($days)
	{
		$numberOfYears = (int)($days / 360)+1;
		$numberOfMonths = (int)(($days % 360) / 30)+1;
		$numberOfDays = (int)(($days % 360) % 30)+1;
		
		return "Year: " . $numberOfYears . " Month: " . $numberOfMonths . " Day: " . (int)$numberOfDays;
	}

	function RecordLedger($gameId, $gameDate, $record)
	{
		$db = Database::getInstance();

		$sql = "INSERT INTO ledgers VALUES ('$gameId', '$gameDate', '$record')";
		$db->query($sql);
	}
	
	function GetDaysLedger($gameId, $gameDate)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT record FROM ledgers WHERE (gameid = '$gameId') AND (date = '$gameDate')";
		$result = $db->query($sql);

		return $result;
		
	}
	
		function GetItemCostByName($name)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT cost FROM items WHERE name = '$name'";
		$result = $db->query($sql);

		$row = $result->fetch_assoc();
		
		return $row["cost"];
	}
	
	
	
	
	
	
