<?php
	session_start();

	//require_once("database.php");
	require_once("customer.php");
	
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
		$inputArray["bulk_ale"]					= htmlspecialchars(stripslashes(trim($_POST["bulk_ale"])));
		$inputArray["bulk_wine"]				= htmlspecialchars(stripslashes(trim($_POST["bulk_wine"])));
		$inputArray["bulk_poultry"]				= htmlspecialchars(stripslashes(trim($_POST["bulk_poultry"])));
		$inputArray["bulk_pork"]				= htmlspecialchars(stripslashes(trim($_POST["bulk_pork"])));
		$inputArray["bulk_carrot"]				= htmlspecialchars(stripslashes(trim($_POST["bulk_carrot"])));
		$inputArray["bulk_potato"]				= htmlspecialchars(stripslashes(trim($_POST["bulk_potato"])));
		
		$customers = array();
		
		ReceiveOrders();
		GetReturningCustomers();
		GetNewCustomers();
		ServeCustomers();
		RecordDay();
		
		$_SESSION['numberOfCustomersToday'] = count($customers);
		
		header("location: ../tavern.php");
	}
	
	function ReceiveOrders()
	{
		global $currentGame;
		global $inputArray;
		
		foreach($inputArray as $itemName=>$qtyToOrder)
		{
			$priceOfBulkItem = GetBulkItemCostByName($itemName);
			
			while($qtyToOrder > 0)
			{
				if ($currentGame['current_money'] >= $priceOfBulkItem)
				{
					$currentGame['current_money'] -= $priceOfBulkItem;
					$currentGame["{$itemName}_on_order"] += 1;
				}
				
				$qtyToOrder--;
			}
		}
		
		if($currentGame['tavern_date'] % 6 == 0)
		{
			foreach($currentGame as $key=>$value)
			{
				$columnName = split("_", $key);
				if($columnName[0] == "bulk"  && count($columnName) == 2)
				{
					$currentGame[$key] += $currentGame["{$key}_on_order"];
					$currentGame["{$key}_on_order"] = 0;
				}
			}
		}
	}
	
	function GetRowById($tableName, $id)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM {$tableName} WHERE id = '{$id}'";
		return $db->query($sql)->fetch_assoc();
	}
	
	function GetReturningCustomers()
	{
		global $customers;
		global $currentGame;
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM customers WHERE active=1 AND user_id={$_SESSION['userId']}";
		$returningCustomers = $db->query($sql);

		if (!empty($returningCustomers))
		{
			foreach($returningCustomers as $currentCustomer)
			{
				$loadedCustomer = new Customer($_SESSION['userId'], $currentCustomer['customer_id']);
				
				if(count($customers) < 30)
				{
					if ($loadedCustomer->CustomerReturns())
					{
						$customers[] = $loadedCustomer;
					}
					else
					{
						$loadedCustomer->UpdateHappiness($loadedCustomer->happiness - 1);
					}
				}
				$loadedCustomer->UpdateHappiness($loadedCustomer->happiness - 1);
			}
		}
	}
	
	function GetNewCustomers()
	{
		global $currentGame;
		global $customers;
		$db = Database::getInstance();
		
		for ($i = count($customers); $i < 30; $i++)
		{
			$newCustomer = new Customer($_SESSION['userId']);
			$newCustomer->RecordCustomer();
			
			if ($newCustomer->CustomerReturns())
			{
				$customers[] = $newCustomer;
			}
		}
	}
	
	function ServeCustomers()
	{
		global $customers;
		global $currentGame;

		foreach($customers as $currentCustomer)
		{
			$dishPreferences = $currentCustomer->GetDishPreferences();
			$fed = false;
			$attempts = 0;
			$record = "I didn't make it into the if statement";
			
			while($currentCustomer->happiness > 0 && !$fed && $attempts < 5)
			{
				$dish = PlaceOrder($dishPreferences);
				
				$canAfford = CanAfford($dish, $currentCustomer);
				$haveIngredients = HaveIngredients($dish); 

				if ($canAfford && $haveIngredients)
				{
					$currentCustomer->happiness += GetHappinesModifierFromSalesTemperment($dish, $currentCustomer);
					$currentCustomer->UpdateHappiness($currentCustomer->happiness);
					
					if($currentCustomer->happiness >= 0)
					{
						FeedDish($currentCustomer, $dish);
						$record = "{$currentCustomer->customerId} bought {$dish} and left happy, $currentCustomer->happiness";
						$fed = true;
					}
				}
				elseif($canAfford)
				{
					$record = "{$currentCustomer->customerId} can Afford but {$dish} is not on the menu";
					//$record = "{$currentCustomer->customerId}-{$currentCustomer->firstName} Happy: {$currentCustomer->happiness} unhappy you lack ingredients for {$dish}";
				}
				elseif($haveIngredients)
				{
					$happinessModifier = GetHappinesModifierFromSalesTemperment($dish, $currentCustomer);
					$currentCustomer->happiness += $happinessModifier;
					$currentCustomer->UpdateHappiness($currentCustomer->happiness);
					
					$record = "Customer {$currentCustomer->customerId} couldn't afford {$dish}, happiness updated by {$happinessModifier}";
					//$record = "{$currentCustomer->customerId}-{$currentCustomer->firstName} Happy: {$currentCustomer->happiness} storms out over price of {$dish}";
				}
				else
				{
					$attempts--;
				}
				
				if($attempts ==	5)
				{
					$currentCustomer->happiness = $currentCustomer->happiness - 2;
					$currentCustomer->UpdateHappiness($currentCustomer->happiness);
					
					$record = "{$currentCustomer->customerId} left after 5 attempts";
				}
				$attempts++;
				RecordLedger($_SESSION["userId"], $currentGame["tavern_date"], $record);
			}
		}
	}
	
	function GetHappinesModifierFromSalesTemperment($dishName, $customer)
	{
		$dishPrice = GetDishPriceByName($dishName);
		$basePrice = GetDishBasePriceByName($dishName);
		$profitMargin = $dishPrice / $basePrice;
		
		$marginModifier = profitMargin - 1.25;
		$happinessModifier = $marginModifier * $customer->stinginess;
		
		return $happinessModifier;
	}
	
	function FeedDish($currentCustomer, $dishName)
	{
		global $currentGame;
		
		$ingredients = GetRecipeByName($dishName);
		
		$totalIngredients = 0;
		$dishPrice = GetDishPriceByName($dishName);

		
		foreach($ingredients as $columnName=>$qty)
		{
			$ingredient = split("_", $columnName);
			
			if(count($ingredient) > 1 && $ingredient[1] == "qty")
			{
				$bulkQtyString = "bulk_{$ingredient[0]}";
				$unitQtyString = "unit_{$ingredient[0]}";
				
				$currentGame[$unitQtyString] -= $qty;
				$totalIngredients += $qty;
				
				if($currentGame[$unitQtyString] < 0)
				{
					OpenCases();
				}
			}
		}
		
		$currentGame['current_money'] += $dishPrice;
		$currentCustomer->allowance -= $dishPrice;
		$currentCustomer->happiness += $totalIngredients;
		$currentCustomer->UpdateHappiness($currentCustomer->happiness);
	}
	
	function CanAfford($dishName, $customer)
	{
		$dishPrice = GetDishPriceByName($dishName);
		
		if($dishPrice <= $customer->allowance)
			return true;
		else
			return false;
	}
	
	function GetDishPriceByName($dishName)
	{
		global $currentGame;
		
		$columnName = "{$dishName}_price";
		
		return $currentGame[$columnName];
	}
	
	function GetDishBasePriceByName($dishName)
	{
		$ingredients = GetRecipeByName($dishName);
		
		$totalPrice = 0;
		
		foreach($ingredients as $columnName=>$qty)
		{
			$ingredient = split("_", $columnName);
			
			if(count($ingredient) > 1 && $ingredient[1] == "qty")
			{
				$totalPrice += GetIngredientBasePrice("unit_{$ingredient[0]}")*$qty;
			}
		}
		
		return $totalPrice;
	}
	
	function GetIngredientBasePrice($ingredient)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM items WHERE unit_tag='$ingredient'";
		$ingredientRow = $db->query($sql)->fetch_assoc();
		
		return $ingredientRow['unit_cost'];
	}
	
	function HaveIngredients($dishName)
	{
		global $currentGame;
		
		$ingredients = GetRecipeByName($dishName);

		foreach($ingredients as $columnName=>$qty)
		{
			if(!empty($qty))
			{
				$ingredient = split("_", $columnName);
				
				if(count($ingredient) > 1 && $ingredient[1] == "qty")
				{
					$unitQtyString = "unit_{$ingredient[0]}";
					$bulkQtyString = "bulk_{$ingredient[0]}";
					if($currentGame[$unitQtyString] < $qty && $currentGame["$bulkQtyString"] < 1)
					{
						return false;
					}
				}
			}
		}
		
		return true;
	}
	
	function PlaceOrder($arrayOfItems)
	{
		$totalValue = 0;
		
		foreach($arrayOfItems as $itemName=>$itemPref)
		{
			if($itemPref > 0)
				$totalValue += $itemPref;
		}
		
		$choiceValue = rand(1, $totalValue);
		
		$choice = 0;
		foreach($arrayOfItems as $itemName=>$itemPref)
		{
			$choice += $itemPref;
			
			if($choice>=$choiceValue)
			{
				return $itemName;
			}
		}
		
		return null;
	}
	
	function ServeDish($currentCustomer, $dishes)
	{
		global $currentGame;
		
		$served = false;
		
		while(!$served)
		{
			$dishName = $dishes[rand(0, count($dishes))];
			
			if(HaveIngredients($dishName))
			{
				
			}
		}
	}

	
	function GetRecipeByName($dishName)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT * FROM recipes WHERE name='${dishName}'";
		return $db->query($sql)->fetch_assoc();
	}
	
	function GetItemCostByName($name)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT unit_cost FROM items WHERE unit_tag = '$name'";
		$result = $db->query($sql);

		$row = $result->fetch_assoc();
		
		return $row["unit_cost"];
	}
	
	function GetBulkItemCostByName($name)
	{
		$db = Database::getInstance();
		
		$sql = "SELECT bulk_cost FROM items WHERE bulk_tag = '$name'";
		$result = $db->query($sql);

		$row = $result->fetch_assoc();
		
		return $row["bulk_cost"];
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
			if($currentGame[$item["unit_tag"]] <= 0 && $currentGame[$item["bulk_tag"]] > 0)
			{
				$currentGame[$item["bulk_tag"]] -= 1;
				$currentGame[$item["unit_tag"]] += $item["bulk_qty"];
			}
			
		}
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
	
