<?php
	session_start();
	
	if (!empty($_SESSION["userId"]))
	{
		header("location: tavern.php");
	}
?>

<html>
  <head>
    <title>Login Screen</title>
    <link href='/Styles/reset.css' rel='stylesheet' type='text/css'>
    <link href='/Styles/style.css' rel='stylesheet' type='text/css'>
  </head>
  <body>
  	<a href="/Scripts/logout.php"><button class="logout-button">Logout</button></a>
  	<div class="header-ribbon">Tavern - Login</div>
    <div id="id01" class="modal">
		  <!-- Modal Content -->
		  <form class="modal-content" action="/Scripts/login.php" method="POST">
		    <div class="container">
		      	<label><b>Username</b></label>
		      	<input type="text" placeholder="Enter Username" name="uname" required>
				    <label><b>Password</b></label>
      			<input type="password" placeholder="Enter Password" name="psw" required>
      			<?php
      			if (!empty($_SESSION["error"]))
	      			{
	      				echo "<div class='errorMessage'>".$_SESSION['error']."</div>";
	      			}
      			?>
		      <button type="submit">Login</button>		      
		    </div>

		    <div class="container" style="background-color:#f1f1f1">
		      <a href="create-user.php"><button type="button"  class="createbtn">Create Account</button></a>
		    </div>
		  </form>
		</div>
  </body>
</html>