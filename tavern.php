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
	        <div class="summary-item justify-right">User Id: <?php echo $_SESSION["userId"]; ?></div>
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
	    	</div>
	    	<div class="prices-card card">
			<label>Mug of Ale: <?= $currentGame["unit_ale_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["unit_ale_price"]?>cp" name="unitAlePrice"><br><br>
			<label>Glass of Wine: <?= $currentGame["unit_wine_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["unit_wine_price"]?>cp" name="unitWineprice"><br><br>
			<label>Chicken Wings: <?= $currentGame["Chicken Wings_price"]?>cp New:</label><input type="text" style="width:40px; float:right" value="<?= $currentGame["Chicken Wings_price"]?>cp" name="chickenWingsPrice"><br><br>
			<br><br>
			&lt;-- ORDER --><br><br>
			<?php
			$items = GetItems();
			foreach($items as $item): ?>
			<label><?=$item['bulk_name']?>: <?= $item['bulk_cost'] ?>cp</label><input type="text" style="width:40px; float:right" placeholder="0" name="<?= $item['bulk_name'] ?>"><br><br>
			<?php endforeach; ?>
			</div>
	    	<div class="expenses-card card">
				Mug of Ale: <?=$currentGame["unit_ale"]?><br>
	    		Glasses of Wine: <?=$currentGame["unit_wine"]?><br>
	    		Poultry: <?=$currentGame["unit_poultry"]?><br>
	    		Pork Chops: <?=$currentGame["unit_pork"]?><br>
	    		Carrots: <?=$currentGame["unit_carrots"]?><br>
	    		Potatos: <?=$currentGame["unit_potato"]?><br>
	    		Kegs of Ale: <?=$currentGame["bulk_ale"]?><br>
	    		Barrels of Wine: <?=$currentGame["bulk_wine"]?><br>
	    		Full Chickens: <?=$currentGame["bulk_poultry"]?><br>
	    		Pigs: <?=$currentGame["bulk_pork"]?><br>
	    		Carrot Bags: <?=$currentGame["bulk_carrot"]?><br>
	    		Potato Sacks: <?=$currentGame["bulk_potato"]?><br><br>
	    	</div>
	    	<div class="ledger-card card">
	    		<?php
	    		$yesterdaysDate = $currentGame["tavern_date"] - 1;
	    		$yesterdaysLedger = GetDaysLedger($currentGame["user_id"], $yesterdaysDate);
	    		foreach($yesterdaysLedger as $currentLedger): ?>
   					<?= $currentLedger["record"] ?><br>
   				<?php endforeach; ?>
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