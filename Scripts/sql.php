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

		$sql = "INSERT INTO games (gameid, userid, currentdate, currentmoney, mug_ale, glass_wine, common_meal, fine_meal, chicken, pork_chop, carrot, potato, barrel_wine, keg_ale, full_chicken, pig, carrot_bag, potato_sack, ale_price, wine_price, common_meal_price, fine_meal_price) values ('$userId', '$userId', 0, 10.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.05, .20, .8, 2.2)";
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
		
		$sql = "SELECT * FROM games WHERE gameid = '$gameId' ORDER BY currentdate DESC";
		$result = $conn->query($sql);

		$row = $result->fetch_assoc();
		
		return $row;
	}
	
	function EndTurn($currentGame)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		
		foreach($currentGame as $key => $value)
		{
			$keys[] = $key;
			$values[] = $value;
		}
		$keysImploded = implode(", ", $keys);
		$valuesImploded = implode(" , ", $values);
		
		$sql = "INSERT INTO games (" . $keysImploded . ") VALUES (" . $valuesImploded . ")";
    
		$conn->query($sql);
		
		$sql = "INSERT INTO test VALUES ('" . $sql ."')";
		$conn->query($sql);
	}
	
	function FormatDate($days)
	{
		$numberOfYears = (int)($days / 360)+1;
		$numberOfMonths = (int)(($days % 360) / 30)+1;
		$numberOfDays = (int)(($days % 360) % 30)+1;
		
		return "Year: " . $numberOfYears . " Month: " . $numberOfMonths . " Day: " . (int)$numberOfDays;
	}
	
	function GetItemCostByName($name)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		
		$sql = "SELECT cost FROM items WHERE name = '$name'";
		$result = $conn->query($sql);

		$row = $result->fetch_assoc();
		
		return $row["cost"];
	}
	
	function GetItemQtyByName($name)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		
		$sql = "SELECT qty FROM items WHERE name = '$name'";
		$result = $conn->query($sql);

		$row = $result->fetch_assoc();
		
		return $row["qty"];
	}
	
	function GetCustomerTypes()
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		
		$sql = "SELECT id FROM customers";
		$result = $conn->query($sql);

		return mysqli_num_rows($result);
	}
	
	function GetCustomerById($id)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		
		$sql = "SELECT * FROM customers WHERE id = '$id'";
		$result = $conn->query($sql);

		$row = $result->fetch_assoc();
		
		return $row;
	}
	
	function RecordLedger($gameId, $gameDate, $record)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

		$sql = "INSERT INTO ledgers VALUES ('$gameId', '$gameDate', '$record')";
		$conn->query($sql);
	}
	
	function GetDaysLedger($gameId, $gameDate)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		
		$sql = "SELECT record FROM ledgers WHERE (gameid = '$gameId') AND (date = '$gameDate')";
		$result = $conn->query($sql);

		return $result;
		
	}