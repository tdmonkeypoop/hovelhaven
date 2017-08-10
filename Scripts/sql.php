<?php
	require ("database.php");

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

	function RecordLedger($gameId, $gameDate, $record)
	{
		$db = Database::getInstance();

		$sql = "INSERT INTO ledgers VALUES ('$gameId', '$gameDate', '$record')";
		$db->query($sql);
	}
	
	function GetCurrentGame($userId)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM games WHERE user_id = '$userId' ORDER BY tavern_date DESC";
		$result = $db->query($sql);

		$row = $result->fetch_assoc();
		
		if (!empty($row))
		{
			return $row;
		}
		else
		{
			return null;
		}
	}
	