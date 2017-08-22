<?php
    session_start();
?>

<html>
    <body>
        New Population: <?=$_SESSION["newPopulation"]?><br>
        New Acres: <?=$_SESSION["newAcres"]?><br>
        <form action="/Scripts/generate-city.php" method="POST">
            <label>Population: </label><input type="text" style="width:80px" value="" name="newPopulation"><br><br>
            <button type="submit">Generate City</button>
        </form>
    </body>    
</html>