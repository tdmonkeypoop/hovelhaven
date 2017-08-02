<?php
    require_once ("Scripts/config.php");
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD);
    
    $sql = "DROP DATABASE IF EXISTS tavern";
    $conn->query($sql);
    
    $sql = "CREATE DATABASE tavern";
    $conn->query($sql);
    
    $sql = "USE tavern";
    $conn->query($sql);
    
    $sql = "CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY NOT NULL, username TEXT NOT NULL, password TEXT NOT NULL, gameid INT)";
    $conn->query($sql);
    /*
        Users
            id
            username
            password
            gameid
    */

    $sql = "CREATE TABLE ledgers (gameid INT, date TEXT NOT NULL, itemid INT NOT NULL, sold BOOL NOT NULL, qty INT NOT NULL)";
    $conn->query($sql);
    /*
        ledgers
            gameid
            date
            itemid
            sold
            qty
    */

    $sql = "CREATE TABLE games (gameid INT PRIMARY KEY AUTO_INCREMENT, userid INT NOT NULL, currentdate INT NOT NULL, currentmoney DECIMAL(11, 2), mug_ale INT, glass_wine INT, common_meal INT, fine_meal INT, chicken INT, pork_chop INT, carrot INT, potato INT, barrel_wine INT, keg_ale INT, full_chicken INT, pig INT, carrot_bag INT, potato_sack INT, ale_price DECIMAL(6,2), wine_price DECIMAL(6,2), common_meal_price DECIMAL(6,2), fine_meal_price DECIMAL(6,2))";
    $conn->query($sql);
    /*
        games
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
            ale_price
            wine_price
            common_meal_price
            fine_meal_price
    */
    
    $sql = "CREATE TABLE items (id INT PRIMARY KEY AUTO_INCREMENT, name TEXT NOT NULL, cost DECIMAL(6,2) NOT NULL, qty INT)";
    $conn->query($sql);
    
    /*
        items
            id
            name
            cost
            qty
    */
    $sql = "INSERT INTO items VALUES(1, 'mug_ale', 0.04, 1)";
    $conn->query($sql);
    $sql = "INSERT INTO items VALUES(2, 'glass_wine', 0.20, 1)";
    $conn->query($sql);
    $sql = "INSERT INTO items VALUES(3, 'common_meal', 0.30, 1)";
    $conn->query($sql);
    $sql = "INSERT INTO items VALUES(4, 'fine_meal', 0.70, 1)";
    $conn->query($sql);
    $sql = "INSERT INTO items VALUES(5, 'chicken', 0.20, 1)";
    $conn->query($sql);
    $sql = "INSERT INTO items VALUES(6, 'pork_chop', 0.3, 1)";
    $conn->query($sql);
    $sql = "INSERT INTO items VALUES(7, 'carrot', 0.05, 1)";
    $conn->query($sql);
    $sql = "INSERT INTO items VALUES(8, 'potato', 0.03, 1)";
    $conn->query($sql);
    $sql = "INSERT INTO items VALUES(9, 'barrel_wine', 5.00, 200)";
    $conn->query($sql);
    $sql = "INSERT INTO items VALUES(10, 'keg_ale', 10, 100)";
    $conn->query($sql);
    $sql = "INSERT INTO items VALUES(11, 'full_chicken', 1, 4)";
    $conn->query($sql);
    $sql = "INSERT INTO items VALUES(12, 'pig', 3.1, 8)";
    $conn->query($sql);
    $sql = "INSERT INTO items VALUES(13, 'carrot_bag', 1, 12)";
    $conn->query($sql);
    $sql = "INSERT INTO items VALUES(14, 'potato_sack', 1, 20)";
    $conn->query($sql);
    
    
    $sql = "CREATE TABLE customers (id INT PRIMARY KEY AUTO_INCREMENT, name TEXT NOT NULL, ale_pref INT, wine_pref INT, chicken_pref INT, pork_chop_pref INT, carrot_pref INT, potato_pref INT)";
    $conn->query($sql);
    /*
        customers
            id
            name
            ale_pref
            wine_pref
            chicken_pref
            pork_pref
            carrot_pref
            potato_pref
    */
    
    $sql = "INSERT INTO customers VALUES(1, 'common', 2, 1, 1, 1, 5, 5)";
    $conn->query($sql);
    $sql = "INSERT INTO customers VALUES(2, 'wealthy', 1, 3, 5, 5, 1, 1)";
    $conn->query($sql);
    $sql = "INSERT INTO customers VALUES(3, 'Drunk', 5, 1, 0, 0, 0, 0)";
    $conn->query($sql);