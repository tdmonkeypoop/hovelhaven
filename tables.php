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
    $sql = "CREATE TABLE users 
    (
        id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
        username TEXT NOT NULL,
        password TEXT NOT NULL
    )";
    
    $db->query($sql);
    
    /*************************************
     * games
     *************************************/
    $sql = "CREATE TABLE games
    (
        user_id INT,
        tavern_date INT DEFAULT 0,
        current_money INT DEFAULT 250,
        unit_ale INT DEFAULT 0,
        bulk_ale INT DEFAULT 0,
        bulk_ale_on_order INT DEFAULT 0,
        unit_wine INT DEFAULT 0,
        bulk_wine INT DEFAULT 0,
        bulk_wine_on_order INT DEFAULT 0,
        unit_poultry INT DEFAULT 0,
        bulk_poultry INT DEFAULT 0,
        bulk_poultry_on_order INT DEFAULT 0,
        unit_pork INT DEFAULT 0,
        bulk_pork INT DEFAULT 0,
        bulk_pork_on_order INT DEFAULT 0,
        unit_carrot INT DEFAULT 0,
        bulk_carrot INT DEFAULT 0,
        bulk_carrot_on_order INT DEFAULT 0,
        unit_potato INT DEFAULT 0,
        bulk_potato INT DEFAULT 0,
        bulk_potato_on_order INT DEFAULT 0,
        unit_ale_price INT DEFAULT 4,
        unit_wine_price INT DEFAULT 8,
        `Chicken Wings_price` INT DEFAULT 6,
        `Pigs in the Coop_price` INT DEFAULT 14,
        `Homestyle Chicken_price` INT DEFAULT 7,
        `Chicken Hash_price` INT DEFAULT 7,
        `Chicken Pot Pie_price` INT DEFAULT 8,
        `Pork Chops_price` INT DEFAULT 8,
        `Homestyle Pork_price` INT DEFAULT 9,
        `Pork Hash_price` INT DEFAULT 9,
        `Stew_price` INT DEFAULT 10,
        `Carrot Broth_price` INT DEFAULT 1,
        `Steamed Veggies_price` INT DEFAULT 2,
        `Mashed Potatoes_price` INT DEFAULT 1
        )";
    
    $db->query($sql);
    
    /*************************************
     * ledgers
     *************************************/
    $sql = "CREATE TABLE ledgers
    (
        user_id INT,
        tavern_date INT NOT NULL,
        record TEXT
    )";
    
    $db->query($sql);
    
    /*************************************
     * items
     *************************************/
    $sql = "CREATE TABLE items
    (
        id INT PRIMARY KEY AUTO_INCREMENT,
        unit_name TEXT,
        unit_tag TEXT,
        unit_cost INT,
        bulk_name TEXT,
        bulk_tag TEXT,
        bulk_cost INT,
        bulk_qty INT
    )";
    
    $db->query($sql);
    
    $items = array();
    $items[] = [1, "'Mug of Ale'",   "'unit_ale'",    4, "'Keg of Ale'",  "'bulk_ale'",      116,    29];
    $items[] = [2, "'Glass of Wine'",  "'unit_wine'",    8, "'Bottle of Wine'",  "'bulk_wine'",     48,     48];
    $items[] = [3, "'Poultry'",  "'unit_poultry'", 6, "'Hen'",  "'bulk_poultry'",  144,     24];
    $items[] = [4, "'Pork'",  "'unit_pork'",    8, "'Pig'",  "'bulk_pork'",     176,    22];
    $items[] = [5, "'Carrot'",  "'unit_carrot'",  1, "'Bag of Carrots'",  "'bulk_carrot'",   10,     10];
    $items[] = [6, "'Potato'",  "'unit_potato'",  1, "'Sack of Potatoes'",  "'bulk_potato'",   8,      8];
    
    $itemsImploded = array();
    for($i = 0; $i < count($items); $i++)
    {
        $itemsImploded[] = "(" . implode(',', $items[$i]) . ")";
    }

    $sql = "INSERT INTO items VALUES" . implode(',',$itemsImploded);
    $db->query($sql);
    
    /*************************************
     * recipes
     *************************************/
    $sql = "CREATE TABLE recipes
    (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name TEXT NOT NULL,
        poultry_qty INT,
        pork_qty INT,
        carrot_qty INT,
        potato_qty INT
    )";
    
    $db->query($sql);
    
    $recipes[] = [1,  "'Chicken Wings'",     1, 0, 0, 0];
    $recipes[] = [2,  "'Pigs in the Coop'",  1, 1, 0, 0];
    $recipes[] = [3,  "'Homestyle Chicken'", 1, 0, 1, 0];
    $recipes[] = [4,  "'Chicken Hash'",      1, 0, 0, 1];
    $recipes[] = [5,  "'Chicken Pot Pie'",   1, 0, 1, 1];
    $recipes[] = [6,  "'Pork Chops'",        0, 1, 0, 0];
    $recipes[] = [7,  "'Homestyle Pork'",    0, 1, 1, 0];
    $recipes[] = [8,  "'Pork Hash'",         0, 1, 0, 1];
    $recipes[] = [9,  "'Stew'",              0, 1, 1, 1];
    $recipes[] = [10, "'Carrot Broth'",      0, 0, 1, 0];
    $recipes[] = [11, "'Steamed Veggies'",   0, 0, 1, 1];
    $recipes[] = [12, "'Mashed Potatoes'",   0, 0, 0, 1];
    
    $recipesImploded = array();
    for($i = 0; $i < count($recipes); $i++)
    {
        $recipesImploded[] = "(" . implode(',', $recipes[$i]) . ")";
    }

    $sql = "INSERT INTO recipes VALUES" . implode(',',$recipesImploded);
    $db->query($sql);
     
     /*************************************
     * customers
     *************************************/
    $sql = 'CREATE TABLE customers 
    (
        customer_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        first_name TEXT,
        last_name TEXT,
        drinker_type_id INT,
        eater_type_id INT,
        profession_id INT,
        happiness INT,
        stinginess INT,
        active BOOL
    )';
    
    $db->query($sql);
    
    /*************************************
     * eatertypes
     *************************************/
    $sql = "CREATE TABLE eatertypes 
    (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name TEXT NOT NULL,
        chicken_pref INT,
        pork_pref INT,
        carrot_pref INT,
        potato_pref INT
    )";
    
    $db->query($sql);
    
    $eaterTypes = array();
    $eaterTypes[] = [1, '"Vegetarian"',      -10, -10, 1, 1];
    $eaterTypes[] = [2, '"Meat Lover"',      1, 1, -1, -1];
    $eaterTypes[] = [3, '"Muslim"',          1, -10, 1, 1];
    $eaterTypes[] = [4, '"Porker"',          -1, 1, 0, 1];
    $eaterTypes[] = [5, '"Rabbit"',          -1, -1, 1, 0];
    
    $eaterTypesImploded = array();
    for($i = 0; $i < count($eaterTypes); $i++)
    {
        $eaterTypesImploded[] = "(" . implode(',', $eaterTypes[$i]) . ")";
    }
    
    $sql = "INSERT INTO eatertypes VALUES" . implode(',',$eaterTypesImploded);
    $db->query($sql);
    
    /*************************************
     * drinkertypes
     *************************************/
    $sql = "CREATE TABLE drinkertypes 
    (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name TEXT NOT NULL,
        ale_pref INT,
        wine_pref INT
    )";
    
    $db->query($sql);
    
    $drinkerTypes = array();
    $drinkerTypes[] = [1, '"Drunk"',          5, 1];
    $drinkerTypes[] = [2, '"Oenophiliac"',    1, 5];
    $drinkerTypes[] = [3, '"Standard"',       1, 1];
    
    $drinkerTypesImploded = array();
    for($i = 0; $i < count($drinkerTypes); $i++)
    {
        $drinkerTypesImploded[] = "(" . implode(',', $drinkerTypes[$i]) . ")";
    }
    
    $sql = "INSERT INTO drinkertypes VALUES" . implode(',',$drinkerTypesImploded);
    $db->query($sql);
    
    /*************************************
     * professions
     *************************************/
    $sql = "CREATE TABLE professions 
    (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name TEXT NOT NULL,
        daily_wage INT,
        allowance_for_food INT,
        religious INT
    )";
    
    $db->query($sql);
    
    $professions = array();
    $professions[] = [1, '"Laborer"', 9, 15, 5];
    
    $professionsImploded = array();
    for($i = 0; $i < count($professions); $i++)
    {
        $professionsImploded[] = "(" . implode(',', $professions[$i]) . ")";
    }
    
    $sql = "INSERT INTO professions VALUES" . implode(',',$professionsImploded);
    $db->query($sql);
    
    /*************************************
     * calender
     *************************************/
    $sql = "CREATE TABLE calendar
    (
        date INT,
        name TEXT NOT NULL,
        religious BOOL
    )";

    $db->query($sql);

    $calendar = array();
    
    //Weekends and "Sabbath"
    for($i = 6; $i <= 360; $i = $i + 6)
    {
        $calendar[] = [$i - 1,  "'Weekend'",                0];
        $calendar[] = [$i,      "'Helm\'s Day'",            1];    
    }
    
    //Holidays
    $calendar[] = [1,           "'New Year'",               0];
    $calendar[] = [16,          "'Martin Luther King'",     0];
    $calendar[] = [44,          "'Valentine\'s Day'",       0];
    $calendar[] = [148,         "'Memorial Day'",           0];
    $calendar[] = [184,         "'Independance Day'",       0];
    $calendar[] = [244,         "'Labor Day'",              0];
    $calendar[] = [279,         "'Columbus Day'",           0];
    $calendar[] = [310,         "'Veterans Day'",           0];
    $calendar[] = [321,         "'Thanksgiving Eve'",       0];
    $calendar[] = [322,         "'Thanksgiving Day'",       0];
    $calendar[] = [355,         "'Christmas Eve'",          1];
    $calendar[] = [356,         "'Christmas Day'",          1];
    
    $calendarImploded = array();
    for($i = 0; $i < count($calendar); $i++)
    {
        $calendarImploded[] = "(" . implode(',', $calendar[$i]) . ")";
    }
    
    $sql = "INSERT INTO calendar VALUES" . implode(',',$calendarImploded);
    $db->query($sql);
    
    /*************************************
     * firstnames
     *************************************/
    $sql = "CREATE TABLE firstnames 
    (
        id INT PRIMARY KEY AUTO_INCREMENT,
        first_name TEXT
    )";
    
    $db->query($sql);
    
    $firstNames = array();
    $firstNames[] = "('Abrielle')";
    $firstNames[] = "('Adair')";
    $firstNames[] = "('Adara')";
    $firstNames[] = "('Adriel')";
    $firstNames[] = "('Aiyana')";
    $firstNames[] = "('Alissa')";
    $firstNames[] = "('Alixandra')";
    $firstNames[] = "('Altair')";
    $firstNames[] = "('Amara')";
    $firstNames[] = "('Anatola')";
    $firstNames[] = "('Anya')";
    $firstNames[] = "('Arcadia')";
    $firstNames[] = "('Ariadne')";
    $firstNames[] = "('Arianwen')";
    $firstNames[] = "('Aurelia')";
    $firstNames[] = "('Aurelian')";
    $firstNames[] = "('Aurelius')";
    $firstNames[] = "('Avalon')";
    $firstNames[] = "('Acalia')";
    $firstNames[] = "('Alaire')";
    $firstNames[] = "('Auristela')";
    $firstNames[] = "('Bastian')";
    $firstNames[] = "('Breena')";
    $firstNames[] = "('Brielle')";
    $firstNames[] = "('Briallan')";
    $firstNames[] = "('Briseis')";
    $firstNames[] = "('Cambria')";
    $firstNames[] = "('Cara')";
    $firstNames[] = "('Carys')";
    $firstNames[] = "('Caspian')";
    $firstNames[] = "('Cassia')";
    $firstNames[] = "('Cassiel')";
    $firstNames[] = "('Cassiopeia')";
    $firstNames[] = "('Cassius')";
    $firstNames[] = "('Chaniel')";
    $firstNames[] = "('Cora')";
    $firstNames[] = "('Corbin')";
    $firstNames[] = "('Cyprian')";
    $firstNames[] = "('Daire')";
    $firstNames[] = "('Darius')";
    $firstNames[] = "('Destin')";
    $firstNames[] = "('Drake')";
    $firstNames[] = "('Drystan')";
    $firstNames[] = "('Dagen')";
    $firstNames[] = "('Devlin')";
    $firstNames[] = "('Devlyn')";
    $firstNames[] = "('Eira')";
    $firstNames[] = "('Eirian')";
    $firstNames[] = "('Elysia')";
    $firstNames[] = "('Eoin')";
    $firstNames[] = "('Evadne')";
    $firstNames[] = "('Eliron')";
    $firstNames[] = "('Evanth')";
    $firstNames[] = "('Fineas')";
    $firstNames[] = "('Finian')";
    $firstNames[] = "('Fyodor')";
    $firstNames[] = "('Gareth')";
    $firstNames[] = "('Gavriel')";
    $firstNames[] = "('Griffin')";
    $firstNames[] = "('Guinevere')";
    $firstNames[] = "('Gaerwn')";
    $firstNames[] = "('Ginerva')";
    $firstNames[] = "('Hadriel')";
    $firstNames[] = "('Hannelore')";
    $firstNames[] = "('Hermione')";
    $firstNames[] = "('Hesperos')";
    $firstNames[] = "('Iagan')";
    $firstNames[] = "('Ianthe')";
    $firstNames[] = "('Ignacia')";
    $firstNames[] = "('Ignatius')";
    $firstNames[] = "('Iseult')";
    $firstNames[] = "('Isolde')";
    $firstNames[] = "('Jessalyn')";
    $firstNames[] = "('Kara')";
    $firstNames[] = "('Kerensa')";
    $firstNames[] = "('Korbin')";
    $firstNames[] = "('Kyler')";
    $firstNames[] = "('Kyra')";
    $firstNames[] = "('Katriel')";
    $firstNames[] = "('Kyrielle')";
    $firstNames[] = "('Leala')";
    $firstNames[] = "('Leila')";
    $firstNames[] = "('Lilith')";
    $firstNames[] = "('Liora')";
    $firstNames[] = "('Lucien')";
    $firstNames[] = "('Lyra')";
    $firstNames[] = "('Leira')";
    $firstNames[] = "('Liriene')";
    $firstNames[] = "('Liron')";
    $firstNames[] = "('Maia')";
    $firstNames[] = "('Marius')";
    $firstNames[] = "('Mathieu')";
    $firstNames[] = "('Mireille')";
    $firstNames[] = "('Mireya')";
    $firstNames[] = "('Maylea')";
    $firstNames[] = "('Meira')";
    $firstNames[] = "('Natania')";
    $firstNames[] = "('Nerys')";
    $firstNames[] = "('Nuriel')";
    $firstNames[] = "('Nyssa')";
    $firstNames[] = "('Neirin')";
    $firstNames[] = "('Nyfain')";
    $firstNames[] = "('Oisin')";
    $firstNames[] = "('Oralie')";
    $firstNames[] = "('Orion')";
    $firstNames[] = "('Orpheus')";
    $firstNames[] = "('Ozara')";
    $firstNames[] = "('Oleisa')";
    $firstNames[] = "('Orinthea')";
    $firstNames[] = "('Peregrine')";
    $firstNames[] = "('Persephone')";
    $firstNames[] = "('Perseus')";
    $firstNames[] = "('Petronela')";
    $firstNames[] = "('Phelan')";
    $firstNames[] = "('Pryderi')";
    $firstNames[] = "('Pyralia')";
    $firstNames[] = "('Pyralis')";
    $firstNames[] = "('Qadira')";
    $firstNames[] = "('Quintessa')";
    $firstNames[] = "('Quinevere')";
    $firstNames[] = "('Raisa')";
    $firstNames[] = "('Remus')";
    $firstNames[] = "('Rhyan')";
    $firstNames[] = "('Rhydderch')";
    $firstNames[] = "('Riona')";
    $firstNames[] = "('Renfrew')";
    $firstNames[] = "('Saoirse')";
    $firstNames[] = "('Sarai')";
    $firstNames[] = "('Sebastian')";
    $firstNames[] = "('Seraphim')";
    $firstNames[] = "('Seraphina')";
    $firstNames[] = "('Sirius')";
    $firstNames[] = "('Sorcha')";
    $firstNames[] = "('Saira')";
    $firstNames[] = "('Sarielle')";
    $firstNames[] = "('Serian')";
    $firstNames[] = "('Séverin')";
    $firstNames[] = "('Tavish')";
    $firstNames[] = "('Tearlach')";
    $firstNames[] = "('Terra')";
    $firstNames[] = "('Thalia')";
    $firstNames[] = "('Thaniel')";
    $firstNames[] = "('Theia')";
    $firstNames[] = "('Torian')";
    $firstNames[] = "('Torin')";
    $firstNames[] = "('Tressa')";
    $firstNames[] = "('Tristana')";
    $firstNames[] = "('Uriela')";
    $firstNames[] = "('Urien')";
    $firstNames[] = "('Ulyssia')";
    $firstNames[] = "('Vanora')";
    $firstNames[] = "('Vespera')";
    $firstNames[] = "('Vasilis')";
    $firstNames[] = "('Xanthus')";
    $firstNames[] = "('Xara')";
    $firstNames[] = "('Xylia')";
    $firstNames[] = "('Yadira')";
    $firstNames[] = "('Yseult')";
    $firstNames[] = "('Yakira')";
    $firstNames[] = "('Yeira')";
    $firstNames[] = "('Yeriel')";
    $firstNames[] = "('Yestin')";
    $firstNames[] = "('Zaira')";
    $firstNames[] = "('Zephyr')";
    $firstNames[] = "('Zora')";
    $firstNames[] = "('Zorion')";
    $firstNames[] = "('Zaniel')";
    $firstNames[] = "('Zarek')";
    
    $sql = "INSERT INTO firstnames (first_name) VALUES" . implode(',', $firstNames);
    $db->query($sql);
    
    /*************************************
     * lastnames
     *************************************/
    $sql = "CREATE TABLE lastnames
    (
        id INT PRIMARY KEY AUTO_INCREMENT,
        last_name TEXT
    )";
    
    $db->query($sql);
    
    $lastNames[] = "('Thoraded')";
    $lastNames[] = "('Gilar')";
    $lastNames[] = "('Balkral')";
    $lastNames[] = "('Baern')";
    $lastNames[] = "('Agamm')";
    $lastNames[] = "('Toror')";
    $lastNames[] = "('Gegkas')";
    $lastNames[] = "('Thorar')";
    $lastNames[] = "('Bofan')";
    $lastNames[] = "('Yedorn')";
    $lastNames[] = "('Vonlar')";
    $lastNames[] = "('Ogar')";
    $lastNames[] = "('Thorbar')";
    $lastNames[] = "('Renag')";
    $lastNames[] = "('Mogain')";
    $lastNames[] = "('Rogi')";
    $lastNames[] = "('Garn')";
    $lastNames[] = "('Taldam')";
    $lastNames[] = "('Farur')";
    $lastNames[] = "('Matlo')";
    $lastNames[] = "('Renar')";
    $lastNames[] = "('Banir')";
    $lastNames[] = "('Falthal')";
    $lastNames[] = "('Ril')";
    $lastNames[] = "('Boroag')";
    $lastNames[] = "('Valdtor')";
    $lastNames[] = "('Keldar')";
    $lastNames[] = "('Galgrat')";
    $lastNames[] = "('Gnok')";
    $lastNames[] = "('Gilerl')";
    $lastNames[] = "('Danseg')";
    $lastNames[] = "('Gehgrim')";
    $lastNames[] = "('Valdar')";
    $lastNames[] = "('Nalrak')";
    $lastNames[] = "('Taig')";
    $lastNames[] = "('Vardar')";
    $lastNames[] = "('Thir')";
    $lastNames[] = "('Khondlim')";
    $lastNames[] = "('Barungrim')";
    $lastNames[] = "('Gimur')";
    $lastNames[] = "('Rigur')";
    $lastNames[] = "('Hathur')";
    $lastNames[] = "('Duerthal')";
    $lastNames[] = "('Varan')";
    $lastNames[] = "('Madik')";
    $lastNames[] = "('Gorlel')";
    $lastNames[] = "('Kaldersun')";
    $lastNames[] = "('Galar')";
    $lastNames[] = "('Thorur')";
    $lastNames[] = "('Durel')";
    $lastNames[] = "('Donur')";
    $lastNames[] = "('Ovtak')";
    $lastNames[] = "('Arof')";
    $lastNames[] = "('Maegan')";
    $lastNames[] = "('Torlo')";
    $lastNames[] = "('Galar')";
    $lastNames[] = "('Yerdel')";
    $lastNames[] = "('Badar')";
    $lastNames[] = "('Gladgar')";
    $lastNames[] = "('Del')";
    $lastNames[] = "('Orag')";
    $lastNames[] = "('Belkhan')";
    $lastNames[] = "('Azrimm')";
    $lastNames[] = "('Gordrin')";
    $lastNames[] = "('Fultut')";
    $lastNames[] = "('Berdo')";
    $lastNames[] = "('Havdar')";
    $lastNames[] = "('Halvic')";
    $lastNames[] = "('Whurthal')";
    $lastNames[] = "('Bazgen')";
    $lastNames[] = "('Kilor')";
    $lastNames[] = "('Babtur')";
    $lastNames[] = "('Baergdas')";
    $lastNames[] = "('Barunak')";
    $lastNames[] = "('Zuth')";
    $lastNames[] = "('Thirdan')";
    $lastNames[] = "('Manar')";
    $lastNames[] = "('Cael')";
    $lastNames[] = "('Maedok')";
    $lastNames[] = "('Baerggar')";
    $lastNames[] = "('Balseg')";
    $lastNames[] = "('Yab')";
    $lastNames[] = "('Barunhof')";
    $lastNames[] = "('Damir')";
    $lastNames[] = "('Garnlo')";
    $lastNames[] = "('Galli')";
    $lastNames[] = "('Garnut')";
    $lastNames[] = "('Nalgan')";
    $lastNames[] = "('Babdok')";
    $lastNames[] = "('Donir')";
    $lastNames[] = "('Thirir')";
    $lastNames[] = "('Torgag')";
    $lastNames[] = "('Ril')";
    $lastNames[] = "('Belur')";
    $lastNames[] = "('Gomglad')";
    $lastNames[] = "('Rogi')";
    $lastNames[] = "('Gomggug')";
    $lastNames[] = "('Baern')";
    $lastNames[] = "('Gimthic')";
    $lastNames[] = "('Yerlak')";
    
    $sql = "INSERT INTO lastnames (last_name) VALUES" . implode(',', $lastNames);
    $db->query($sql);
    