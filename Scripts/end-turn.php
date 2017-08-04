<?php
	session_start();

	require_once("sql.php");
	require_once("database.php");
	
	if (empty($_SESSION["userId"]))
	{
		header("location: ../login.php");
	}
	
	$currentGame = GetCurrentGame($_SESSION["gameId"]);
	
	$inputArray = array();
	$currentGame["mug_ale_price"]		= htmlspecialchars(stripslashes(trim($_POST["aleprice"])));
	$currentGame["glass_wine_price"] 	= htmlspecialchars(stripslashes(trim($_POST["wineprice"])));
	$currentGame["common_meal_price"]	= htmlspecialchars(stripslashes(trim($_POST["commonmealprice"])));
	$currentGame["fine_meal_price"] 	= htmlspecialchars(stripslashes(trim($_POST["finemealprice"])));
	$inputArray["orderale"]				= htmlspecialchars(stripslashes(trim($_POST["orderale"])));
	$inputArray["orderwine"] 			= htmlspecialchars(stripslashes(trim($_POST["orderwine"])));
	$inputArray["orderchicken"]			= htmlspecialchars(stripslashes(trim($_POST["orderchicken"])));
	$inputArray["orderpig"]				= htmlspecialchars(stripslashes(trim($_POST["orderpig"])));
	$inputArray["ordercarrot"]			= htmlspecialchars(stripslashes(trim($_POST["ordercarrot"])));
	$inputArray["orderpotato"]			= htmlspecialchars(stripslashes(trim($_POST["orderpotato"])));
	
	$currentGame = PickDaysCustomers($currentGame);
	$currentGame = CheckForShipments($currentGame, $inputArray["orderale"], $inputArray["orderwine"]);
	EndDay($currentGame);
	
	header("location: ../tavern.php");

	function OpenCases($currentGame)
	{
		if($currentGame["mug_ale"] == 0 && $currentGame["keg_ale"] > 0)
		{
			$currentGame["keg_ale"]--;
			$currentGame["mug_ale"] = GetItemQtyByName("keg_ale");
		}
		
		if($currentGame["glass_wine"] == 0 && $currentGame["barrel_wine"] > 0)
		{
			$currentGame["barrel_wine"]--;
			$currentGame["glass_wine"] = GetItemQtyByName("barrel_wine");
		}
		
		return $currentGame;
	}

	function PickDaysCustomers($currentGame)
	{
		$numberOfCustomers	= CalculateNumberOfCustomers();
		$aleProfitPercent	= ($currentGame["mug_ale_price"] / GetItemCostByName("mug_ale")) - 1.25;
		$wineProfitPercent	= ($currentGame["glass_wine_price"] / GetItemCostByName("glass_wine")) - 1.25;
		
		for ($i = 0; $i < $numberOfCustomers; $i++)
		{
			$customer = CreateCustomer();
	
			$customerTotalResponse =  $i ."-" . $customer["name"] . " drank ";
			$customerTotalWine = 0;
			$customerTotalAle = 0;
			
			while ($customer['happiness'] > 0)
			{
				$customerResponse =  $i ."-" . $customer["name"] . "(H,S)-(" . $customer['happiness'] . "," . $customer['stinginess'] . ") ";
		
				$drinkChance = rand (1, ($customer['ale_pref'] + $customer['wine_pref']));
				
				$drinkChoice = rand (1, $drinkChance);
				
				$currentGame = OpenCases($currentGame);
				
				if ($drinkChoice <= $customer["ale_pref"])
				{
					if ($currentGame["mug_ale"] > 0)
					{
						$stingyFactor = $aleProfitPercent * $customer['stinginess'];
						
						if ($stingyFactor < 0)
							$stingyFactor = 0;
						
						$customer['happiness'] -= $stingyFactor + 1;
						
						if ($customer['happiness'] >= -5)
						{
							$customerTotalAle++;
							$currentGame = GiveCustomerAle($currentGame);	
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"], $customerResponse . "ale ". $currentGame['mug_ale'] . " remain.");
						}
						else if ($customer['happiness'] < -5 && $customerTotalWine == 0 && $customerTotalAle == 0)
						{
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"], $customerResponse . "left angry (ale price)");
						}
						else
						{
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"],  $customerResponse . "left happy");
						}
					}
					else 
					{
						$customer['happiness'] -= 3;
						RecordLedger($currentGame["gameid"], $currentGame["currentdate"],  $customerResponse . "angry for ale");
					}
				}
				else
				{
					if ($currentGame["glass_wine"] > 0)
					{
						$stingyFactor = $wineProfitPercent * $customer['stinginess'];
						
						if ($stingyFactor < 0)
							$stingyFactor = 0;
							
						$customer['happiness'] -= $stingyFactor + 1;
						
						if ($customer['happiness'] >= -5)
						{
							$customerTotalWine++;
							$currentGame = GiveCustomerWine($currentGame);	
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"], $customerResponse . "wine " . $currentGame['glass_wine'] . " remain");
						}
						else if ($customer['happiness'] < -5 && $customerTotalWine == 0 && $customerTotalAle == 0)
						{
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"], $customerResponse . "left angry (wine price)");
						}
						else
						{
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"],  $customerResponse . "left happy");
						}
					}
					else 
					{
						$customer['happiness'] -= 3;
						RecordLedger($currentGame["gameid"], $currentGame["currentdate"],  $customerResponse . "angry for wine");
					}
				}
			}
		}
		
		return $currentGame;
	}
	
	function GiveCustomerAle($currentGame)
	{
		$currentGame['mug_ale']--;
		$currentGame['currentmoney'] += $currentGame['mug_ale_price'];
		
		return $currentGame;
	}
	
	function GiveCustomerWine($currentGame)
	{
		$currentGame['glass_wine']--;
		$currentGame['currentmoney'] += $currentGame['glass_wine_price'];
		
		return $currentGame;
	}
	
	function CreateCustomer()
	{
		$uniqueCustomers = GetCustomerTypes();
			
		$newCustomer = GetCustomerById(rand(1, $uniqueCustomers));
		$newCustomer['happiness'] = rand (1, 10);
		$newCustomer['stinginess'] = rand (1, 3);
		
		return $newCustomer;
	}
	
	function CalculateNumberOfCustomers()
	{
		return rand(3, 10);
	}
	
	function GetCustomerTypes()
	{
		$db = Database::getInstance();
		
		$sql = "SELECT id FROM customers";
		$result = $db->query($sql);

		return mysqli_num_rows($result);
	}
	
	function GetCustomerById($id)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM customers WHERE id = '$id'";
		$customer = $db->query($sql);

		$row = $customer->fetch_assoc();
		
		return $row;
	}
	
	function CheckForShipments($currentGame, $newAleOrder, $newWineOrder)
	{
		if ($newAleOrder > 0)
		{
			while($currentGame["currentmoney"] >= GetItemCostByName("keg_ale") && $newAleOrder > 0)
			{
				$currentGame["currentmoney"] -= GetItemCostByName("keg_ale");
				$currentGame["keg_ale"]++;
				$newAleOrder--;
			}
			
		}
		
		if ($newWineOrder > 0)
		{
			while($currentGame["currentmoney"] >= GetItemCostByName("barrel_wine") && $newWineOrder > 0)
			{
				$currentGame["currentmoney"] -= GetItemCostByName("barrel_wine");
				$currentGame["barrel_wine"]++;
				$newWineOrder--;
			}
			
		}
		return $currentGame;
	}
	
	function EndDay($currentGame)
	{
		
		$currentGame["currentdate"]++; 
	
		EndTurn($currentGame);
	}


	
	function GetItemQtyByName($name)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT qty FROM items WHERE name = '$name'";
		$result = $db->query($sql);

		$row = $result->fetch_assoc();
		
		return $row["qty"];
	}