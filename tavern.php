<?php
	session_start();
	require_once("Scripts/sql.php");
	
	if (!empty($_SESSION["userId"]))
	{
		$_SESSION["gameId"] = GetUserGameId($_SESSION["userId"]);
	}
	else
	{
		header("location: index.php");
	}
	
	if (empty($_SESSION["gameId"]))
	{
		$_SESSION["gameId"] = NewGame($_SESSION["userId"]);
	}
	
	$currentGame = GetCurrentGame($_SESSION["gameId"]);
?>

<html>
	<head>
		<title>Pathfinder's Hovel</title>
		<link href='Styles/reset.css' rel='stylesheet' type='text/css'>
		<link href='Styles/tavern.css' rel='stylesheet' type='text/css'>
	</head>
	<body class="container">
	    <!--Backgrounds-->
	    <div class="logo"></div>
	    <div class="header"><div style="float: right;padding-top: 30px; padding-right: 30px"><a href="/Scripts/logout.php"><button class="logout-button">Logout</button></a></div></div>
	    <div class="tabs"></div>
	    <div class="dead-space"></div>
	    <div class="main"></div>
	    <div class="summary"></div>
	    <div class="footer"></div>
	    
	    <!--Content-->
	    <div class="logo middled centered"><img src="/Images/logo.jpg" alt="Logo" width="100%" height="100%"></div>
	    <div class="header middled centered">
	    	<h1><?=GetUsername($currentGame["userid"])?>'s Tavern</h1>
    	</div>
	    <div class="summary">
	        <div class="summary-item justify-right">Coming Soon</div>
	        <div class="summary-item justify-right">User Id: <?php echo $_SESSION["userId"]; ?></div>
	        <div class="summary-item justify-right">Game Id: <?php echo $_SESSION["gameId"]; ?></div>
	        <div class="summary-item justify-right"></div>
	        <div class="summary-item justify-right"></div>
	    </div>
	    
	    <div class="tabs bottomed">
	        <div class="tab-item centered middled">Summary (Soon)</div>
	        <div class="tab-item centered middled">Ledgers (Soon)</div>
	        <div class="tab-item centered middled">Expenses (Soon)</div>
	        <div class="tab-item centered middled">Inventory (Soon)</div>
	        <div class="tab-item centered middled">Employees (Soon)</div>
	        <div class="tab-item centered middled">Customers (Soon)</div>
	    </div>
	    
	    <div class="main card-container">
	    	<!--Backgrounds-->
	    	<div class="summary-card-title card-title"></div>
	    	<div class="prices-card-title card-title"></div>
	    	<div class="expenses-card-title card-title"></div>
	    	<div class="ledger-card-title card-title"></div>
	    	<div class="summary-card card"></div>
	    	<div class="prices-card card"></div>
	    	<div class="expenses-card card"></div>
	    	<div class="ledger-card card"></div>
	    	<div class="summary-card-total card-total card"></div>
	    	<div class="prices-card-total card-total card"></div>
	    	<div class="expenses-card-total card-total card"></div>
	    	<div class="start-next-day card-total card"></div>
	    	
	    	<!--Content-->
	    	<!--Titles-->
	    		<div class="summary-card-title card-title middled centered">SUMMARY</div>
	    		<div class="prices-card-title card-title middled centered">PRICES</div>
	    		<div class="expenses-card-title card-title middled centered">Inventory</div>
	    		<div class="ledger-card-title card-title middled centered">LEDGER</div>
	    	<!--Cards-->
		    	<div class="summary-card card">
		    		Current Day: <?=FormatDate($currentGame["currentdate"])?><br>
		    		Total Cash: $<?=$currentGame["currentmoney"]?><br>
		    	</div>
		    	<div class="prices-card card">
				
		    	</div>
		    	<div class="expenses-card card">
		    		Mugs of Ale: <?=$currentGame["mug_ale"]?><br>
		    		Glasses of Wine: <?=$currentGame["glass_wine"]?><br>
		    		Common Meals: <?=$currentGame["common_meal"]?><br>
		    		Fine Meals: <?=$currentGame["fine_meal"]?><br>
		    		Poultry: <?=$currentGame["chicken"]?><br>
		    		Pork Chops: <?=$currentGame["pork_chop"]?><br>
		    		Carrots: <?=$currentGame["carrot"]?><br>
		    		Potatos: <?=$currentGame["potato"]?><br>
		    		Barrels of Wine: <?=$currentGame["barrel_wine"]?><br>
		    		Kegs of Ale: <?=$currentGame["keg_ale"]?><br>
		    		Full Chickens: <?=$currentGame["full_chicken"]?><br>
		    		Pigs: <?=$currentGame["pig"]?><br>
		    		Carrot Bags: <?=$currentGame["carrot_bag"]?><br>
		    		Potato Sacks: <?=$currentGame["potato_sack"]?><br>
		    	</div>
		    	<div class="ledger-card card">
		    	</div>
		    <!--Totals-->
		    	<div class="summary-card-total card-total middled justify-right"></div>
		    	<div class="prices-card-total card-total middled justify-right"></div>
		    	<div class="expenses-card-total card-total middled justify-right"></div>
		    	<a href="/Scripts/end-turn.php" class="start-next-day"><div class="start-next-day card-total card middled centered">End Day</div></a>
	    </div>
	    
	    <div class="footer justify-right middled">Contact Us... But Don't Really we don't care. - No Really We Don't Care</div>
	</body>
</html>