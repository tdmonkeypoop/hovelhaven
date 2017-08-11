<?php
	session_start();
	require_once("Scripts/end-turn.php");
	
	if (empty($_SESSION["userId"]))
	{
		header("location: index.php");
	}
	
	if (empty(GetCurrentGame($_SESSION["userId"])))
	{
		NewGame($_SESSION["userId"]);
	}
	
	$currentGame = GetCurrentGame($_SESSION["userId"]);
	$yesterdayGame = GetGameByDate($_SESSION["userId"], $currentGame["tavern_date"] - 1);
	$items = GetItems();
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
	    	<h1><?=GetUsername($currentGame["user_id"])?>'s Tavern</h1>
    	</div>
	    <div class="summary">
	        <div class="summary-item justify-right">Coming Soon</div>
	        <div class="summary-item justify-right">User Id: <?= $_SESSION["userId"]; ?></div>
	        <div class="summary-item justify-right">Yesterday's Date: <?=$yesterdayGame["tavern_date"]?></div>
	        <div class="summary-item justify-right">Current Date: <?=$currentGame["tavern_date"]?></div>
	    </div>
	    
	    <div class="tabs bottomed">
	        <div class="tab-item centered middled">Summary (Soon)</div>
	        <div class="tab-item centered middled">Ledgers (Soon)</div>
	        <div class="tab-item centered middled">Expenses (Soon)</div>
	        <div class="tab-item centered middled">Inventory (Soon)</div>
	        <div class="tab-item centered middled">Employees (Soon)</div>
	        <div class="tab-item centered middled">Customers (Soon)</div>
	    </div>
	    
	    <form class="main card-container" action="/Scripts/end-turn.php" method="POST">
	    <!--<div class="main card-container">-->
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
	    		Current Day:<br><?=FormatDate($currentGame["tavern_date"])?><br><br>
	    		Total Cash: $<?=$currentGame["current_money"]?><br><br>
	    		&lt;-- Yesterday --><br>
	    		Total Earned: $<?= $currentGame["current_money"] - $yesterdayGame["current_money"]?><br>
	    		Ale sold: <?= $yesterdayGame["unit_ale"] - $currentGame["unit_ale"] ?><br>
	    		Wine sold: <?= $yesterdayGame["unit_wine"] - $currentGame["unit_wine"] ?><br>
	    		Poultry sold: <br>
	    		Pork sold: <br>
	    		Carrot sold: <br>
	    		Potato sold: <br>
	    		Kegs opened: <br>
	    		Bottles opened: <br>
	    		Hens slaughtered: <br>
	    		Pigs slaughtered: <br>
	    		Bags opened: <br>
	    		Sacks opened: <br>
	    	</div>
	    	<div class="prices-card card" style="line-height: 90%;">
			<label>Mug of Ale: <?= $currentGame["unit_ale_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["unit_ale_price"]?>" name="unitAlePrice"><br><br>
			<label>Glass of Wine: <?= $currentGame["unit_wine_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["unit_wine_price"]?>" name="unitWineprice"><br><br>
			<label>Chicken Wings: <?= $currentGame["Chicken Wings_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["Chicken Wings_price"]?>" name="chickenWingsPrice"><br><br>
			<label>Pigs in the Coop: <?= $currentGame["Pigs in the Coop_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["Pigs in the Coop_price"]?>" name="pigsInTheCoopPrice"><br><br>
			<label>Homestyle Chicken: <?= $currentGame["Homestyle Chicken_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["Homestyle Chicken_price"]?>" name="homestyleChickenPrice"><br><br>
			<label>Chicken Hash: <?= $currentGame["Chicken Hash_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["Chicken Hash_price"]?>" name="chickenHashPrice"><br><br>
			<label>Chicken Pot Pie: <?= $currentGame["Chicken Pot Pie_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["Chicken Pot Pie_price"]?>" name="chickenPotPiePrice"><br><br>
			<label>Pork Chops: <?= $currentGame["Pork Chops_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["Pork Chops_price"]?>" name="porkChopsPrice"><br><br>
			<label>Homestyle Pork: <?= $currentGame["Homestyle Pork_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["Homestyle Pork_price"]?>" name="homestylePorkPrice"><br><br>
			<label>Pork Hash: <?= $currentGame["Pork Hash_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["Pork Hash_price"]?>" name="porkHashPrice"><br><br>
			<label>Stew: <?= $currentGame["Stew_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["Stew_price"]?>" name="stewPrice"><br><br>
			<label>Carrot Brother: <?= $currentGame["Carrot Broth_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["Carrot Broth_price"]?>" name="carrotBrothPrice"><br><br>
			<label>Steamed Veggies: <?= $currentGame["Steamed Veggies_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["Steamed Veggies_price"]?>" name="steamedVeggiesPrice"><br><br>
			<label>Mashed Potatoes: <?= $currentGame["Mashed Potatoes_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["Mashed Potatoes_price"]?>" name="mashedPotatoesPrice"><br><br>
			&lt;-- ORDER --><br>
			<?php foreach($items as $item): ?>
			<label><?=$item['bulk_name']?>: <?= $item['bulk_cost'] ?>cp</label><input type="text" style="width:40px; float:right" placeholder="0" name="<?= $item['bulk_tag'] ?>"><br><br>
			<?php endforeach; ?>
			</div>
	    	<div class="expenses-card card">
				<?php foreach($items as $item): ?>
					<?=$item["unit_name"]?>: <?=$currentGame[$item["unit_tag"]]?><br>
					<?=$item["bulk_name"]?>: <?=$currentGame[$item["bulk_tag"]]?><br>
				<?php endforeach; ?>
	    	</div>
	    	<div class="ledger-card card">
	    		<?php
	    		$yesterdaysDate = $currentGame["tavern_date"] - 1;
	    		$yesterdaysLedger = GetDaysLedger($currentGame["user_id"], $yesterdaysDate);
	    		$i = 1;
	    		foreach($yesterdaysLedger as $currentLedger): ?>
   					<?=$i . ":" . $currentLedger["record"] ?><br>
   				<?php 
   				$i++;
   				endforeach; ?>
	    	</div>
		    <!--Totals-->
	    	<div class="summary-card-total card-total middled justify-right"></div>
	    	<div class="prices-card-total card-total middled justify-right"></div>
	    	<div class="expenses-card-total card-total middled justify-right"></div>
	    	<div class="start-next-day card-total card middled centered"><button type="submit">End Day</button></div>
	    
	    </form>
	    <div class="footer justify-right middled">Contact Us... But Don't Really we don't care. - No Really We Don't Care</div>
	</body>
</html>