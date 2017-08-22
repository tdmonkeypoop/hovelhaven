<?php
    require("Scripts/customer.php");
    
    
    $newPerson = new Customer(1);
    if(!$newPerson->CustomerExists($newPerson->customerId))
    {
        $newPerson->RecordCustomer();
    }
?>
<html>
    <body>
        UserID: <?= $newPerson->userId ?><br>
        CustomerID: <?= $newPerson->customerId ?><br>
        Name: <?= $newPerson->firstName ?> <?=$newPerson->lastName?><br>
        DrinkerID: <?= $newPerson->drinkerTypeId ?><br>
        EaterID: <?= $newPerson->eaterTypeId ?><br>
        ProfessionId: <?= $newPerson->professionId ?><br>
        Happiness: <?= $newPerson->happiness ?> <br>
        Stinginess: <?= $newPerson->stinginess ?> <br>
        Active: <?= $newPerson->active ?> <br><br>
        
        !--Ingredience Preferences--!<br><br>
        <?php $ingrPrefs = $newPerson->GetIngredientPreferences();?>
        Chicken: <?= $ingrPrefs['chicken_pref'] ?><br>
        Pork: <?= $ingrPrefs['pork_pref'] ?><br>
        Carrot: <?= $ingrPrefs['carrot_pref'] ?><br>
        Potato: <?= $ingrPrefs['potato_pref'] ?><br><br>
        !--Dish Preferences--!<br><br>
        <?php $dishPrefs = $newPerson->GetDishPreferences();
        foreach($dishPrefs as $dishName => $dishPref):?>
        Prefers <?=$dishName?> with a preference of <?=$dishPref?><br>
        <?php endforeach;?>
        <br>!--Drink Preferences--!<br><br>
        <?php $drinkPrefs = $newPerson->GetDrinkPreferences(); ?>
        Ale: <?=$drinkPrefs['ale_pref'] ?><br>
        Wine: <?=$drinkPrefs['wine_pref'] ?><br>
    </body>
</html>