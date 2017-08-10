<?php

	session_start();
	
	$previousURI = $_SESSION['CurrentURI'];
	$_SESSION['CurrentURI'] = $_SERVER['REQUEST_URI'];
?>

<html>
	<head>
		<title>Create User Form</title>
		<link href='/Styles/reset.css' rel='stylesheet' type='text/css'>
		<link href='/Styles/style.css' rel='stylesheet' type='text/css'>
	</head>
	<body>
		<a href="Scripts/logout.php"><button class="logout-button">Back</button></a>
  		<div class="header-ribbon">Tavern - Create New Tavern</div>
		<!-- The Modal -->
		<div id="id01" class="modal">
		  <!-- Modal Content -->
		  <form class="modal-content" action="Scripts/login.php" method="POST">
		    <div class="container">
		      	<label><b>Username</b></label>
		      	<input type="text" placeholder="Enter Username" name="uname" required>
				<label><b>Password</b></label>
      			<input type="password" placeholder="Enter Password" name="psw" required>
      			<label><b>Retype Password</b></label>
      			<input type="password" placeholder="Enter Password" name="psw2" required>
      			<?php
      				if (isset($_SESSION["error"]))
	      			{
	      				echo "<div class='errorMessage'>".$_SESSION['error']."</div>";
	      			}
	      			unset($_SESSION["error"]);
      			?>
		      <button type="submit">Create Account</button>		      
		    </div>
		  </form>
		</div>
	</body>
</html>