<?php
	session_start();

	require("sql.php");
	//require("database.php");
	
	if (empty($_SESSION["userId"]))
	{
		header("location: ../index.php");
	}
	
	$currentGame = GetCurrentGame($_SESSION["userId"]);
	
	$inputArray = array();
	$currentGame["unit_ale_price"]		= htmlspecialchars(stripslashes(trim($_POST["unitAlePrice"])));
	$currentGame["unit_wine_price"] 	= htmlspecialchars(stripslashes(trim($_POST["unitWineprice"])));
	$currentGame["Chicken Wings_price"]	= htmlspecialchars(stripslashes(trim($_POST["chickenWingsPrice"])));
	$currentGame["Pigs in the Coop_price"]	= htmlspecialchars(stripslashes(trim($_POST["pigsInTheCoopPrice"])));
	$currentGame["Homestyle Chicken_price"]	= htmlspecialchars(stripslashes(trim($_POST["homestyleChickenPrice"])));
	$currentGame["Chicken Hash_price"]	= htmlspecialchars(stripslashes(trim($_POST["chickenHashPrice"])));
	$currentGame["Chicken Pot Pie_price"]	= htmlspecialchars(stripslashes(trim($_POST["chickenPotPiePrice"])));
	$currentGame["Pork Chops_price"]	= htmlspecialchars(stripslashes(trim($_POST["porkChopsPrice"])));
	$currentGame["Homestyle Pork_price"]	= htmlspecialchars(stripslashes(trim($_POST["homestylePorkPrice"])));
	$currentGame["Pork Hash_price"]	= htmlspecialchars(stripslashes(trim($_POST["porkHashPrice"])));
	$currentGame["Stew_price"]	= htmlspecialchars(stripslashes(trim($_POST["stewPrice"])));
	$currentGame["Carrot Broth_price"]	= htmlspecialchars(stripslashes(trim($_POST["carrotBrothPrice"])));
	$currentGame["Steamed Veggies_price"]	= htmlspecialchars(stripslashes(trim($_POST["steamedVeggiesPrice"])));
	$currentGame["Mashed Potatoes_price"]	= htmlspecialchars(stripslashes(trim($_POST["mashedPotatoesPrice"])));
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
		if($currentGame["unit_ale"] == 0 && $currentGame["bulk_ale"] > 0)
		{
			$currentGame["bulk_ale"]--;
			$currentGame["unit_ale"] = GetItemQtyByName("bulk_ale");
		}
		
		if($currentGame["unit_wine"] == 0 && $currentGame["bulk_wine"] > 0)
		{
			$currentGame["bulk_wine"]--;
			$currentGame["unit_wine"] = GetItemQtyByName("bulk_wine");
		}
		
		return $currentGame;
	}

	function PickDaysCustomers($currentGame)
	{
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
		
		return $currentGame;
	}
	
	function GiveCustomerAle($currentGame)
	{
		$currentGame['unit_ale']--;
		$currentGame['current_money'] += $currentGame['unit_ale_price'];
		
		return $currentGame;
	}
	
	function GiveCustomerWine($currentGame)
	{
		$currentGame['unit_wine']--;
		$currentGame['current_money'] += $currentGame['glass_wine_price'];
		
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
		return $currentGame;
	}
	
	function GetItemCostByName($name)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT cost FROM items WHERE name = '$name'";
		$result = $db->query($sql);

		$row = $result->fetch_assoc();
		
		return $row["cost"];
	}
	
	function EndDay($currentGame)
	{
		
		$currentGame["tavern_date"]++; 
	
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