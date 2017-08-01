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
	
	/*Run the end turn scripts required
	
	OpenShop(); //SetBaseline
    ServeCustomers(); //Actually Sell Stuff
    CloseShop();
    AccessDamage();
    OrderGoods();
    CountInventory();*/
	
	$currentGame = GetCurrentGame($_SESSION["gameId"]);
	
	PickDaysCustomers();
	UpdateCustomers();
	CheckForShipments();
	$currentGame = EndDay($currentGame);
	
	
	
	
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
	
	header("location: ../tavern.php");