<?php
    require_once ("Scripts/config.php");
    require_once ("Scripts/database.php");
    
    $db = Database::getInstance();
    
    /*************************************
     * Destroy and Rebuild DB
     *************************************/
    $sql = "DROP DATABASE IF EXISTS tavern";
    $db->query($sql);
    
    $sql = "CREATE DATABASE tavern";
    $db->query($sql);
    
    $sql = "USE tavern";
    $db->query($sql);
    
    /*************************************
     * users
     *************************************/
    $sql = "CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY NOT NULL, username TEXT NOT NULL, password TEXT NOT NULL, gameid INT)";
    $db->query($sql);
    /*
        id
        username
        password
        gameid
    */

    /*************************************
     * ledgers
     *************************************/
    $sql = "CREATE TABLE ledgers (gameid INT, date INT NOT NULL, record TEXT)";
    $db->query($sql);
    /*
        gameid
        date
        record
    */

    /*************************************
     * games
     *************************************/
    $sql = "CREATE TABLE games (gameid INT, userid INT NOT NULL, currentdate INT NOT NULL, currentmoney DECIMAL(11, 2), mug_ale INT, glass_wine INT, common_meal INT, fine_meal INT, chicken INT, pork_chop INT, carrot INT, potato INT, barrel_wine INT, keg_ale INT, full_chicken INT, pig INT, carrot_bag INT, potato_sack INT, mug_ale_price DECIMAL(6,2), glass_wine_price DECIMAL(6,2), common_meal_price DECIMAL(6,2), fine_meal_price DECIMAL(6,2))";
    $db->query($sql);
    /*
        gameid
        userid
        currentdate
        currentmoney
        mug_ale
        glass_wine
        common_meal
        fine_meal
        chicken
        pork_chop
        carrot
        potato
        barrel_wine
        keg_ale
        full_chicken
        pig
        carrot_bag
        potato_sack
        mug_ale_price
        glass_wine_price
        common_meal_price
        fine_meal_price
    */
    
    /*************************************
     * items
     *************************************/
    $sql = "CREATE TABLE items (id INT PRIMARY KEY AUTO_INCREMENT, name TEXT NOT NULL, cost DECIMAL(6,2) NOT NULL, qty INT)";
    $db->query($sql);
    /*
        id
        name
        cost
        qty
    */
    
    $items = array();
    $items[] = [1,  '"mug_ale"',      1,      1];
    $items[] = [2,  '"glass_wine"',   2,      1];
    $items[] = [3,  '"common_meal"',  0.30,   1];
    $items[] = [4,  '"fine_meal"',    0.70,   1];
    $items[] = [5,  '"chicken"',      0.20,   1];
    $items[] = [6,  '"pork_chop"',    0.3,    1];
    $items[] = [7,  '"carrot"',       0.05,   1];
    $items[] = [8,  '"potato"',       0.03,   1];
    $items[] = [9,  '"barrel_wine"',  100,    50];
    $items[] = [10, '"keg_ale"',      100,    100];
    $items[] = [11, '"full_chicken"', 1,      4];
    $items[] = [12, '"pig"',          3.1,    8];
    $items[] = [13, '"carrot_bag"',   1,      12];
    $items[] = [14, '"potato_sack"',  1,      20];
    
    $itemsImploded = array();
    for($i = 0; $i < count($items); $i++)
    {
        $itemsImploded[] = "(" . implode(',', $items[$i]) . ")";
    }

    $sql = "INSERT INTO items VALUES" . implode(',',$itemsImploded);
    $db->query($sql);


    /*************************************
     * customers
     *************************************/
    $sql = "CREATE TABLE customers (id INT PRIMARY KEY AUTO_INCREMENT, name TEXT NOT NULL, ale_pref INT, wine_pref INT, chicken_pref INT, pork_chop_pref INT, carrot_pref INT, potato_pref INT)";
    $db->query($sql);
    /*
        id
        name
        ale_pref
        wine_pref
        chicken_pref
        pork_pref
        carrot_pref
        potato_pref
    */

    $customers[] = [1, '"common"',    2, 1, 1, 1, 5, 5];
    $customers[] = [2, '"wealthy"',   1, 3, 5, 5, 1, 1];
    $customers[] = [3, '"drunk"',     5, 1, 0, 0, 0, 0];
    
    $customersImploded = array();
    for($i = 0; $i < count($customers); $i++)
    {
        $customersImploded[] = "(" . implode(',', $customers[$i]) . ")";
    }
    
    $sql = "INSERT INTO customers VALUES" . implode(',',$customersImploded);
    $db->query($sql);
    
    /*************************************
     * calender
     *************************************/
    $sql = "CREATE TABLE calender (date INT, name TEXT NOT NULL, religious BOOL)";
    $db->query($sql);

    $calender[] = [1, "'newYear'", 0];
    $calender[] = [5, "'weekend'", 0];
    $calender[] = [6, "'Helms Day'", 1];
    $calender[] = [6, "'dragons day'", 0];
    $calender[] = [11, "'weekend'", 0];
    $calender[] = [12, "'Helms Day'", 1];
    $calender[] = [17, "'weekend'", 0];
    $calender[] = [18, "'Helms Day'", 1];
    $calender[] = [23, "'weekend'", 0];
    $calender[] = [24, "'Helms Day'", 1];
    $calender[] = [29, "'weekend'", 0];
    $calender[] = [30, "'Helms Day'", 1];
    
    $calenderImploded = array();
    for($i = 0; $i < count($calender); $i++)
    {
        $calenderImploded[] = "(" . implode(',', $calender[$i]) . ")";
    }
    
    $sql = "INSERT INTO calender VALUES" . implode(',',$calenderImploded);
    $db->query($sql);