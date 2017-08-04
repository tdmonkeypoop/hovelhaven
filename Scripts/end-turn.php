<?php
	session_start();

	require_once("sql.php");
	
	if (empty($_SESSION["userId"]))
	{
		header("location: ../login.php");
	}
	
	$currentGame = GetCurrentGame($_SESSION["gameId"]);
	
	$inputArray = array();
	$inputArray["aleprice"]			= htmlspecialchars(stripslashes(trim($_POST["aleprice"])));
	$inputArray["wineprice"] 		= htmlspecialchars(stripslashes(trim($_POST["wineprice"])));
	$inputArray["commonmealprice"]	= htmlspecialchars(stripslashes(trim($_POST["commonmealprice"])));
	$inputArray["finemealprice"] 	= htmlspecialchars(stripslashes(trim($_POST["finemealprice"])));
	$inputArray["orderale"]			= htmlspecialchars(stripslashes(trim($_POST["orderale"])));
	$inputArray["orderwine"] 		= htmlspecialchars(stripslashes(trim($_POST["orderwine"])));
	$inputArray["orderchicken"]		= htmlspecialchars(stripslashes(trim($_POST["orderchicken"])));
	$inputArray["orderpig"]			= htmlspecialchars(stripslashes(trim($_POST["orderpig"])));
	$inputArray["ordercarrot"]		= htmlspecialchars(stripslashes(trim($_POST["ordercarrot"])));
	$inputArray["orderpotato"]		= htmlspecialchars(stripslashes(trim($_POST["orderpotato"])));
	
	if(!empty($inputArray["aleprice"]))
		$currentGame["ale_price"]=$inputArray["aleprice"];
	if(!empty($inputArray["wineprice"]))
		$currentGame["wine_price"]=$inputArray["wineprice"];
	
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
		$numberOfCustomers = rand(3, 10);
		
		$uniqueCustomers = GetCustomerTypes();
		
		for ($i = 0; $i < $numberOfCustomers; $i++)
		{
			$customerId = rand(1, $uniqueCustomers);
			
			$customer = GetCustomerById($customerId);
			$customerHappiness = rand (1, 10);
			$customerStinginess = rand (1, 3);
			
			while ($customerHappiness > 0)
			{
				$drinkChance = rand (1, ($customer["ale_pref"] + $customer["wine_pref"]));
				
				$drinkChoice = rand (1, $drinkChance);
				$currentGame = OpenCases($currentGame);
				
				if ($drinkChoice <= $customer["ale_pref"])
				{
					if ($currentGame["mug_ale"] > 0)
					{
						$profitPercent = $currentGame["ale_price"] / GetItemCostByName("mug_ale") - .25;
						$stingyFactor = $profitPercent * $customerStinginess;
						
						$customerHappiness -= $stingyFactor + 1;
						
						if ($customerHappiness >= -1)
						{
							$currentGame["mug_ale"] -= 1;
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"], $i ."-" . $customer["name"] . "(H,S)-(" . $customerHappiness . "," . $customerStinginess . ") bought mug of ale");
							$currentGame["currentmoney"] += $currentGame["ale_price"];
						}
						else if ($customerHappiness < -5)
						{
							$customerHappiness = -1;
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"], $i ."-" . $customer["name"] . "(H,S)-(" . $customerHappiness . "," . $customerStinginess . ") angered by ale price");
						}
						else
						{
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"],  $i ."-" . $customer["name"] . "(H,S)-(" . $customerHappiness . "," . $customerStinginess . ") left happy");
						}
					}
					else 
					{
						$customerHappiness -= 3;
						RecordLedger($currentGame["gameid"], $currentGame["currentdate"],  $i ."-" . $customer["name"] . "(H,S)-(" . $customerHappiness . "," . $customerStinginess . ") angry for ale");
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
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"],  $i ."-" . $customer["name"] . "(H,S)-(" . $customerHappiness . "," . $customerStinginess . ") bought glass of wine");
							$currentGame["currentmoney"] += $currentGame["wine_price"];
						}
						else if ($customerHappiness < -5)
						{
							$customerHappiness = -1;
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"],  $i ."-" . $customer["name"] . "(H,S)-(" . $customerHappiness . "," . $customerStinginess . ") angered by wine price");
						}
						else
						{
							RecordLedger($currentGame["gameid"], $currentGame["currentdate"],  $i ."-" . $customer["name"] . "(H,S)-(" . $customerHappiness . "," . $customerStinginess . ") left happy");
						}
					}
					else 
					{
						$customerHappiness -= 3;
						RecordLedger($currentGame["gameid"], $currentGame["currentdate"],  $i ."-" . $customer["name"] . "(H,S)-(" . $customerHappiness . "," . $customerStinginess . ") angry for wine");
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
