<?php
	session_start();
	require_once("sql.php");
	
	if (!empty($_SESSION["userId"]))
	{
	}
	else
	{
		header("location: ../login.php");
	}
	
	$currentGame = GetCurrentGame($_SESSION["gameId"]);
	
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$newAlePrice = trim($_POST["aleprice"]);
		$newAlePrice = stripslashes($newAlePrice);
		$newAlePrice = htmlspecialchars($newAlePrice);
		
		$newWinePrice = trim($_POST["wineprice"]);
		$newWinePrice = stripslashes($newWinePrice);
		$newWinePrice = htmlspecialchars($newWinePrice);
		
		$newCommonMealPrice = trim($_POST["commonmealprice"]);
		$newCommonMealPrice = stripslashes($newCommonMealPrice);
		$newCommonMealPrice = htmlspecialchars($newCommonMealPrice);
		
		$newFineMealPrice = trim($_POST["finemealprice"]);
		$newFineMealPrice = stripslashes($newFineMealPrice);
		$newFineMealPrice = htmlspecialchars($newFineMealPrice);
		
		$newAleOrder = trim($_POST["orderale"]);
		$newAleOrder = stripslashes($newAleOrder);
		$newAleOrder = htmlspecialchars($newAleOrder);
		
		$newWineOrder = trim($_POST["orderwine"]);
		$newWineOrder = stripslashes($newWineOrder);
		$newWineOrder = htmlspecialchars($newWineOrder);
		
		$newChickenOrder = trim($_POST["orderchicken"]);
		$newChickenOrder = stripslashes($newChickenOrder);
		$newChickenOrder = htmlspecialchars($newChickenOrder);
		
		$newPigOrder = trim($_POST["orderpig"]);
		$newPigOrder = stripslashes($newPigOrder);
		$newPigOrder = htmlspecialchars($newPigOrder);
		
		$newCarrotOrder = trim($_POST["ordercarrot"]);
		$newCarrotOrder = stripslashes($newCarrotOrder);
		$newCarrotOrder = htmlspecialchars($newCarrotOrder);
		
		$newPotatoOrder = trim($_POST["orderpotato"]);
		$newPotatoOrder = stripslashes($newPotatoOrder);
		$newPotatoOrder = htmlspecialchars($newPotatoOrder);
	}
	
	if($newAlePrice > 0)
		$currentGame["ale_price"]=$newAlePrice;
	if($newWinePrice > 0)
		$currentGame["wine_price"]=$newWinePrice;
	if($newCommonMealPrice > 0)
		$currentGame["common_meal_price"]=$newCommonMealPrice;
	if($newFineMealPrice > 0)
		$currentGame["fine_meal_price"]=$newFineMealPrice;
	
	$currentGame = PickDaysCustomers($currentGame);
	$currentGame = CheckForShipments($currentGame, $newAleOrder, $newWineOrder);
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
		$numberOfCustomers = rand(3, 10);
		
		$uniqueCustomers = GetCustomerTypes();
		
		for ($i = 0; $i < $numberOfCustomers; $i++)
		{
			$currentGame = OpenCases($currentGame);
			$customerId = rand(1, $uniqueCustomers);
			
			$customer = GetCustomerById($customerId);
			$customerHappiness = rand (1, 10);
			$customerStinginess = rand (1, 10);
			
			while ($customerHappiness > 0)
			{
				$drinkChance = rand (1, ($customer["ale_pref"] + $customer["wine_pref"]));
				
				$drinkChoice = rand (1, $drinkChance);
				
				if ($drinkChoice <= $customer["ale_pref"])
				{
					if ($currentGame["mug_ale"] > 0)
					{
						$profitPercent = $currentGame["ale_price"] / GetItemCostByName("mug_ale") - .25;
						$stingyFactor = $profitPercent * $customerStinginess;
						
						$customerHappiness -= $stingyFactor + 1;
						
						if ($customerHappiness >= 0)
						{
							$currentGame["mug_ale"] -= 1;
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"], "Sold ale to " . $customer["name"]);
							$currentGame["currentmoney"] += $currentGame["ale_price"];
						}
						else if ($customerHappiness < -5)
						{
							$customerHappiness = -1;
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"], $customer["name"] . " angered by ale price.");
						}
						else
						{
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"], $customer["name"] . " left happiness - " . $customerHappiness);
						}
					}
					else 
					{
						$customerHappiness -= 3;
						RecordLedger($currentGame["gameid"], $currentGame["currentdate"], $customer["name"] . " angry for ale.");
					}
				}
				else
				{
					if ($currentGame["glass_wine"] > 0)
					{
						
						$profitPercent =  $currentGame["wine_price"] / GetItemCostByName("glass_wine") - .25;
						$stingyFactor = $profitPercent * $customerStinginess;
						
						$customerHappiness -= $stingyFactor + 1;
						
						if ($customerHappiness >= 0)
						{
							$currentGame["glass_wine"] -= 1;
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"], "Sold wine to " . $customer["name"]);
							$currentGame["currentmoney"] += $currentGame["wine_price"];
						}
						else if ($customerHappiness < -5)
						{
							$customerHappiness = -1;
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"], $customer["name"] . " angered by ale price.");
						}
						else
						{
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"], $customer["name"] . " left happiness - " . $customerHappiness);
						}
					}
					else 
					{
						$customerHappiness -= 3;
						RecordLedger($currentGame["gameid"], $currentGame["currentdate"], $customer["name"] . " angry for wine.");
					}
				}
			}
		}
		
		return $currentGame;
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
