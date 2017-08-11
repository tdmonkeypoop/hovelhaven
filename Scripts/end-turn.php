<?php
	session_start();

	require("database.php");
	
	if (empty($_SESSION["userId"]))
	{
		header("location: ../index.php");
	}
	
	$currentGame = GetCurrentGame($_SESSION["userId"]);
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		
		
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
		
		$customers = array();
		
		GetReturningCustomers();
		GetNewCustomers();
		ServeCustomers();
		//ReceiveOrders(if Monday)
		//RecordCustomers
		RecordDay();
		
		header("location: ../tavern.php");
	}
	
	function GetReturningCustomers()
	{
		global $customers;
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM customers";
		$returningCustomers = $db->query($sql);

		if (!empty($returningCustomers))
		{
			foreach($returningCustomers as $currentCustomer)
			{
				if (CustomerReturns($currentCustomer))
				{
					$customers[] = $currentCustomer;
				}
			}
		}
	}
	
	function CustomerReturns($currentCustomer)
	{
		global $currentGame;
		
		$target = rand(1,10);
		//This means that the 31st returning customers will not be unhappy that there isn't a seat for him
		if ($target <= $currentCustomer['happiness'] || $currentCustomer['happiness'] == 10)
		{
			$record = "Return, " . $currentCustomer['customer_id'] . " - " .  $currentCustomer['first_name'] . " " . $currentCustomer['last_name'] . " Happy:" . $currentCustomer['happiness'] . " Drink:" . $currentCustomer['drinker_type_id'] . " Food:" . $currentCustomer['eater_type_id'] . " Profession:" . $currentCustomer['profession_id'] . " Stingy:" . $currentCustomer['stinginess'];
			RecordLedger($_SESSION["userId"], $currentGame["tavern_date"], $record);
			return true;
		}
		else
		{
			$currentCustomer['happiness'] -= 1;
			UpdateCustomer($currentCustomer);
			return false;
		}
	}
	
	function GetNewCustomers()
	{
		global $currentGame;
		global $customers;
		$db = Database::getInstance();
		
		
		$maxNumberOfCustomers = 30;
		
		for ($i = count($customers); $i <= $maxNumberOfCustomers; $i++)
		{
			$target = rand(1, 10);
			$newCustomer = CreateCustomer();
			RecordCustomer($newCustomer);
			
			if ($target < $newCustomer['happiness'])
			{
				$record = "New, " . $newCustomer['first_name'] . " " . $newCustomer['last_name'] . " Happy: " . $newCustomer['happiness'] . " Drink: " . $newCustomer['drinker_type_id'] . " Food: " . $newCustomer['eater_type_id'] . " Profession: " . $newCustomer['profession_id'] . " Stingy: " . $newCustomer['stinginess'];
				RecordLedger($_SESSION["userId"], $currentGame["tavern_date"], $record);
				$customers[] = $newCustomer;
			}
		}
	}
	
	function RecordCustomer($customer)
	{
		$db = Database::getInstance();

		$sql = "INSERT INTO customers (user_id, first_name, last_name, drinker_type_id, eater_type_id, profession_id, happiness, stinginess, active) VALUES ('" . $_SESSION['userId'] . "', '" .  $customer['first_name'] . "', '" . $customer['last_name'] . "', '" . $customer['drinker_type_id'] . "', '" . $customer['eater_type_id'] . "', '" . $customer['profession_id'] . "', '" . $customer['happiness'] . "', '" . $customer['stinginess'] . "', '1')";
		$db->query($sql);
	}
	
	function UpdateCustomer($customer)
	{
		$db = Database::getInstance();
		
		$sql = "UPDATE customers SET happiness=". $customer['happiness'] . " WHERE customer_id=" . $customer['customer_id'];
		$db->query($sql);
	}
	
	function ServeCustomers()
	{
		global $customers;
		global $currentGame;
		
		if (count($customers) > 30)
		{
			$customers = array_slice($customers, 0, 30);
		}
		
		foreach($customers as $currentCustomer)
		{
			$currentProfession = GetFromTableById("professions" , $currentCustomer['profession_id']);
			$currentDrinkerType = GetFromTableById("drinkertypes", $currentCustomer['drinker_type_id']);
			$currentEaterType = GetFromTableById("eatertypes", $currentCustomer['eater_type_id']);
			
			$dishPreferences = DetermineDishPreference($currentEaterType);
			
			foreach($dishPreferences as $dish => $pref)
			{
				$record = $currentCustomer['customer_id'] . " - " .  $currentCustomer['first_name'] . " has a preference of " . $pref . " towards dish " . $dish;
				RecordLedger($_SESSION["userId"], $currentGame["tavern_date"], $record);
			}
		}
	}
	
	function DetermineDishPreference($eaterType)
	{
		$preferences = array();
		$recipes = GetRecipes();
		
		foreach($recipes as $currentRecipe)
		{
			$preference = 0;
			if($currentRecipe['poultry_qty'] > 0  && $eaterType['chicken_pref'] > 0)
			{
				$preference = $currentRecipe['poultry_qty'] + $eaterType['chicken_pref'];
			}
			if($currentRecipe['pork_qty'] > 0  && $eaterType['pork_pref'] > 0)
			{
				$preference = $currentRecipe['pork_qty'] + $eaterType['pork_pref'];
			}
			if($currentRecipe['carrot_qty'] > 0  && $eaterType['carrot_pref'] > 0)
			{
				$preference = $currentRecipe['carrot_qty'] + $eaterType['carrot_pref'];
			}
			if($currentRecipe['potato_qty'] > 0  && $eaterType['potato_pref'] > 0)
			{
				$preference = $currentRecipe['potato_qty'] + $eaterType['potato_pref'];
			}
			
			$preferences[$currentRecipe['name']] = $preference;
		}
		
		arsort($preferences);
		
		return $preferences;
	}
	
	function GetRecipes()
	{
		$db = Database::getInstance();

		$sql = "SELECT * FROM recipes";
		$recipes = $db->query($sql);

		return $recipes;
	}
	
	function GetFromTableById($tableName, $professionId)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM ${tableName} WHERE id = '${professionId}'";
		$result = $db->query($sql);
		
		
		
		return $result->fetch_assoc();
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
		$newCustomer = array();
		$newCustomer["user_id"] = $_SESSION["userId"];
		$newCustomer["first_name"] = GetRandomFirstName();
		$newCustomer["last_name"] = GetRandomLastName();
		$newCustomer["drinker_type_id"] = GetRandomDrinkerType();
		$newCustomer["eater_type_id"] = GetRandomeaterType();
		$newCustomer["profession_id"] = GetRandomProfession();
		$newCustomer["happiness"] = rand (1, 10);
		$newCustomer["stinginess"] = rand (1, 3);
		$newCustomer["active"] = true;
		
		return $newCustomer;
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
		
		$sql = "SELECT unit_cost FROM items WHERE unit_tag = '$name'";
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
		$keysImploded = implode("`, `", $keys);
		$valuesImploded = implode(" , ", $values);
		
		$sql = "INSERT INTO games (`" . $keysImploded . "`) VALUES (" . $valuesImploded . ")";
    
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
		$numberOfDays = (int)(($days % 360) % 30);
		
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
		
		$sql = "SELECT record FROM ledgers WHERE (user_id = '$userId') AND (tavern_date = '$tavernDate')";
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
			if($currentGame[$item['unit_tag']] == 0 && $currentGame[$item['bulk_tag']] > 0)
			{
				$currentGame[$item['unit_tag']]--;
				$currentGame[$itme["unit_tag"]] += $item['bulk_qty'];
			}			
		}
	}
	
	function GetRandomFirstName()
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM firstnames";
		$names = $db->query($sql);
		
		$nameId =  rand(1 , mysqli_num_rows($names));
		
		$sql = "SELECT first_name FROM firstnames WHERE (id = '$nameId')";
		$name = $db->query($sql);
		
		$result = $name->fetch_assoc();
		
		return $result['first_name'];
	}
	
	function GetRandomLastName()
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM lastnames";
		$names = $db->query($sql);
		
		$nameId =  rand(1 , mysqli_num_rows($names));
		
		$sql = "SELECT last_name FROM lastnames WHERE (id = '$nameId')";
		$name = $db->query($sql);
		
		$result = $name->fetch_assoc();
		
		return $result['last_name'];
	}
	
	function GetRandomDrinkerType()
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM drinkertypes";
		$drinkerTypes = $db->query($sql);
		
		return rand(1 , mysqli_num_rows($drinkerTypes));
	}
	
	function GetRandomEaterType()
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM eatertypes";
		$eaterTypes = $db->query($sql);
		
		return rand(1 , mysqli_num_rows($eaterTypes));
	}
	
	function GetRandomProfession()
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM professions";
		$professions = $db->query($sql);
		
		return rand(1 , mysqli_num_rows($professions));
	}