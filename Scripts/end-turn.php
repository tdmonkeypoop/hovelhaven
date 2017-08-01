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
	
	$currentGame = BuyOneOfEverything($currentGame);
	$currentGame = ReceiveOrders($currentGame, $newAleOrder, $newWineOrder);
	PickDaysCustomers();
	UpdateCustomers();
	CheckForShipments();
	$currentGame = EndDay($currentGame);
	
	header("location: ../tavern.php");

	function BuyOneOfEverything($currentGame)
	{
		if($currentGame["mug_ale"] == 0 && $currentGame["keg_ale"] > 0)
		{
			$currentGame["keg_ale"]--;
			$currentGame["mug_ale"] = GetItemQtyByName("keg_ale");
		}
		
		if($currentGame["mug_ale"] > 0)
		{
			$currentGame["mug_ale"]--;
			$currentGame["currentmoney"] += $currentGame["ale_price"];
		}
		
		if($currentGame["glass_wine"] == 0 && $currentGame["barrel_wine"] > 0)
		{
			$currentGame["barrel_wine"]--;
			$currentGame["glass_wine"] = GetItemQtyByName("barrel_wine");
		}
		
		if($currentGame["glass_wine"] > 0)
		{
			$currentGame["glass_wine"]--;
			$currentGame["currentmoney"] += $currentGame["wine_price"];
		}
		return $currentGame;
	}
	
	function ReceiveOrders($currentGame, $newAleOrder, $newWineOrder)
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

	function PickDaysCustomers()
	{
		//Pick Customers that will visit
	}
	
	function UpdateCustomers()
	{
		//Did Customers like what you offered?
		//Did you not have what customer requested?
	}
	
	function CheckForShipments()
	{
		//Shipments arrive at end of day
	}
	
	function EndDay($currentGame)
	{
		
		$currentGame["currentdate"]++; 
	
		EndTurn($currentGame);
		
		return $currentGame;
	}
