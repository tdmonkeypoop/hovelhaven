<?php
	session_start();

	require("database.php");
	
	if (empty($_SESSION["userId"]))
	{
		header("location: ../index.php");
	}
	
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		$currentGame = GetCurrentGame($_SESSION["userId"]);
		
		$inputArray = array();
		$currentGame["unit_ale_price"]			= htmlspecialchars(stripslashes(trim($_POST["unitAlePrice"])));
		$currentGame["unit_wine_price"] 		= htmlspecialchars(stripslashes(trim($_POST["unitWineprice"])));
		$currentGame["Chicken Wings_price"]		= htmlspecialchars(stripslashes(trim($_POST["chickenWingsPrice"])));
		$currentGame["Pigs in the Coop_price"]	= htmlspecialchars(stripslashes(trim($_POST["pigsInTheCoopPrice"])));
		$currentGame["Homestyle Chicken_price"]	= htmlspecialchars(stripslashes(trim($_POST["homestyleChickenPrice"])));
		$currentGame["Chicken Hash_price"]		= htmlspecialchars(stripslashes(trim($_POST["chickenHashPrice"])));
		$currentGame["Chicken Pot Pie_price"]	= htmlspecialchars(stripslashes(trim($_POST["chickenPotPiePrice"])));
		$currentGame["Pork Chops_price"]		= htmlspecialchars(stripslashes(trim($_POST["porkChopsPrice"])));
		$currentGame["Homestyle Pork_price"]	= htmlspecialchars(stripslashes(trim($_POST["homestylePorkPrice"])));
		$currentGame["Pork Hash_price"]			= htmlspecialchars(stripslashes(trim($_POST["porkHashPrice"])));
		$currentGame["Stew_price"]				= htmlspecialchars(stripslashes(trim($_POST["stewPrice"])));
		$currentGame["Carrot Broth_price"]		= htmlspecialchars(stripslashes(trim($_POST["carrotBrothPrice"])));
		$currentGame["Steamed Veggies_price"]	= htmlspecialchars(stripslashes(trim($_POST["steamedVeggiesPrice"])));
		$currentGame["Mashed Potatoes_price"]	= htmlspecialchars(stripslashes(trim($_POST["mashedPotatoesPrice"])));
		$inputArray["orderale"]					= htmlspecialchars(stripslashes(trim($_POST["orderale"])));
		$inputArray["orderwine"] 				= htmlspecialchars(stripslashes(trim($_POST["orderwine"])));
		$inputArray["orderchicken"]				= htmlspecialchars(stripslashes(trim($_POST["orderchicken"])));
		$inputArray["orderpig"]					= htmlspecialchars(stripslashes(trim($_POST["orderpig"])));
		$inputArray["ordercarrot"]				= htmlspecialchars(stripslashes(trim($_POST["ordercarrot"])));
		$inputArray["orderpotato"]				= htmlspecialchars(stripslashes(trim($_POST["orderpotato"])));
		
		//GetReturningCustomers
		//GetNewCustomers
		//RunDaysTotals
		//ReceiveOrders(if Monday)
		//RecordCustomers
		RecordDay();
		
		header("location: ../tavern.php");
	}
	
	function PickDaysCustomers()
	{
		global $currentGame;
		
		$numberOfCustomers	= CalculateNumberOfCustomers();
		$aleProfitPercent	= ($currentGame["unit_ale_price"] / GetItemCostByName("unit_ale")) - 1.25;
		$wineProfitPercent	= ($currentGame["unit_wine_price"] / GetItemCostByName("unit_wine")) - 1.25;
		
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
					if ($currentGame["unit_ale"] > 0)
					{
						$stingyFactor = $aleProfitPercent * $customer['stinginess'];
						
						if ($stingyFactor < 0)
							$stingyFactor = 0;
						
						$customer['happiness'] -= $stingyFactor + 1;
						
						if ($customer['happiness'] >= -5)
						{
							$customerTotalAle++;
							$currentGame = GiveCustomerAle($currentGame);	
							RecordLedger($currentGame["user_id"], $currentGame["tavern_date"], $customerResponse . "ale ". $currentGame['unit_ale'] . " remain.");
						}
						else if ($customer['happiness'] < -5 && $customerTotalWine == 0 && $customerTotalAle == 0)
						{
							RecordLedger($currentGame["user_id"], $currentGame["tavern_date"], $customerResponse . "left angry (ale price)");
						}
						else
						{
							RecordLedger($currentGame["user_id"], $currentGame["tavern_date"],  $customerResponse . "left happy");
						}
					}
					else 
					{
						$customer['happiness'] -= 3;
						RecordLedger($currentGame["user_id"], $currentGame["tavern_date"],  $customerResponse . "angry for ale");
					}
				}
				else
				{
					if ($currentGame["unit_wine"] > 0)
					{
						$stingyFactor = $wineProfitPercent * $customer['stinginess'];
						
						if ($stingyFactor < 0)
							$stingyFactor = 0;
							
						$customer['happiness'] -= $stingyFactor + 1;
						
						if ($customer['happiness'] >= -5)
						{
							$customerTotalWine++;
							$currentGame = GiveCustomerWine($currentGame);	
							RecordLedger($currentGame["unit_id"], $currentGame["tavern_date"], $customerResponse . "wine " . $currentGame['unit_wine'] . " remain");
						}
						else if ($customer['happiness'] < -5 && $customerTotalWine == 0 && $customerTotalAle == 0)
						{
							RecordLedger($currentGame["unit_id"], $currentGame["tavern_date"], $customerResponse . "left angry (wine price)");
						}
						else
						{
							RecordLedger($currentGame["unit_id"], $currentGame["tavern_date"],  $customerResponse . "left happy");
						}
					}
					else 
					{
						$customer['happiness'] -= 3;
						RecordLedger($currentGame["unit_id"], $currentGame["tavern_date"],  $customerResponse . "angry for wine");
					}
				}
			}
		}
	}
	
	function GiveCustomerAle()
	{
		global $currentGame;
		
		$currentGame['unit_ale']--;
		$currentGame['current_money'] += $currentGame['unit_ale_price'];
	}
	
	function GiveCustomerWine()
	{
		global $currentGame;
		
		$currentGame['unit_wine']--;
		$currentGame['current_money'] += $currentGame['glass_wine_price'];
	}
	
	function CreateCustomer()
	{
		/*$uniqueCustomers = GetCustomerTypes();
			
		$newCustomer = GetCustomerById(rand(1, $uniqueCustomers));
		$newCustomer['happiness'] = rand (1, 10);
		$newCustomer['stinginess'] = rand (1, 3);
		
		return $newCustomer;*/
	}
	
	function CalculateNumberOfCustomers()
	{
		return 0;//rand(3, 10);
	}
	
	function GetCustomerTypes()
	{
		//$db = Database::getInstance();
		
		//$sql = "SELECT id FROM customers";
		//$result = $db->query($sql);

		//return mysqli_num_rows($result);
	}
	
	function GetCustomerById($id)
	{
		//$db = Database::getInstance();
		
		//$sql = "SELECT * FROM customers WHERE id = '$id'";
		//$customer = $db->query($sql);

		//$row = $customer->fetch_assoc();
		
		//return $row;
	}
	
	function CheckForShipments($newOrders)
	{
		//need to add bulk_orders to games table
		global $currentGame;
		
		if ($newAleOrder > 0)
		{
			while($currentGame["current_money"] >= GetItemCostByName("bulk_ale") && $newAleOrder > 0)
			{
				$currentGame["current_money"] -= GetItemCostByName("bulk_ale");
				$currentGame["bulk_ale"]++;
				$newAleOrder--;
			}
			
		}
		
		if ($newWineOrder > 0)
		{
			while($currentGame["current_money"] >= GetItemCostByName("bulk_wine") && $newWineOrder > 0)
			{
				$currentGame["current_money"] -= GetItemCostByName("bulk_wine");
				$currentGame["bulk_wine"]++;
				$newWineOrder--;
			}
			
		}
	}
	
	function GetItemCostByName($name)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT unit_cost FROM items WHERE unit_name = '$name'";
		$result = $db->query($sql);

		$row = $result->fetch_assoc();
		
		return $row["unit_cost"];
	}
	
	function RecordDay()
	{
		global $currentGame;
		
		$db = Database::getInstance();
		
		$currentGame["tavern_date"]++;
		
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


	
	function RecordLedger($userId, $tavernDate, $record)
	{
		$db = Database::getInstance();

		$sql = "INSERT INTO ledgers VALUES ('$userId', '$tavernDate', '$record')";
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
	
	function NewGame($userId)
	{
	    $db = Database::getInstance();

		$sql = "INSERT INTO games (user_id) VALUES ('$userId')";
		$db->query($sql);
		
		return $userId;
	}
	
	function GetGameByDate($userId, $date)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM games WHERE (user_id = '$userId') AND (tavern_date = '$date')";
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
	
	function GetUsername($userId)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT username FROM users WHERE id = '$userId'";
		$result = $db->query($sql);

		$row = $result->fetch_assoc();
		
		return $row['username'];
	}
		
	function FormatDate($days)
	{
		$numberOfYears = (int)($days / 360)+1;
		$numberOfMonths = (int)(($days % 360) / 30)+1;
		$numberOfDays = (int)(($days % 360) % 30)+1;
		
		return "Year: " . $numberOfYears . " Month: " . $numberOfMonths . " Day: " . (int)$numberOfDays;
	}
	
	
	function GetItems()
	{
		$db = Database::getInstance();

		$sql = "SELECT * FROM items";
		$items = $db->query($sql);

		return $items;
	}
	
	function GetDaysLedger($userId, $tavernDate)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT record FROM ledgers WHERE (user_id = '$userId') AND (tavern_date = '$tavern_date')";
		$ledger = $db->query($sql);

		return $ledger;
		
	}
	
	function OpenCases()
	{
		global $currentGame;
		
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM items";
		$items = $db->query($sql);
		
		foreach($items as $item)
		{
			if($currentGame[$item['unit_name']] == 0 && $currentGame[$item['bulk_name']] > 0)
			{
				$currentGame[$item['unit_name']]--;
				$currentGame[$itme["unit_name"]] += $item['bulk_qty'];
			}			
		}
	}