<?php
    session_start();
    require_once("database.php");
    
    $newPopulation = 0;
    
    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $newPopulation = htmlspecialchars(stripslashes(trim($_POST["newPopulation"])));
    }
    
    $_SESSION["newAcres"] = GetAcres();
    $_SESSION["newPopulation"] = $newPopulation;
    
    function GetAcres()
    {
        global $newPopulation;
    
        $acresPerCapita = 2.34;
        
        return $acresPerCapita * $newPopulation;
    }
    
    header("location: ../city.php");