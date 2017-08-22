<?php
    require_once ("database.php");
    
    class Customer
    {
        var $customerId;
        var $userId;
        var $firstName;
        var $lastName;
        var $drinkerTypeId;
        var $eaterTypeId;
        var $professionId;
        var $happiness;
        var $stinginess;
        var $active;
        var $allowance;
        
        function __construct($_userId, $customerId = null)
        {
            $this->userId = $_userId;
            
            if($this->CustomerExists($customerId))
            {
                $this->customerId = $customerId;
        	    $this->ReadCustomer($customerId);
        	    
            }
            else
            {
                $this->customerId = $this->GetNewCustomerId();
        		$this->firstName = $this->GetRandomFirstName();
        		$this->lastName = $this->GetRandomLastName();
        		$this->drinkerTypeId = $this->GetRandomDrinkerType();
        		$this->eaterTypeId = $this->GetRandomEaterType();
        		$this->professionId = $this->GetRandomProfession();
        		$this->happiness = rand (1, 10);
        		$this->stinginess = rand (1, 3);
        		$this->active = true;
            }
            
            $this->allowance = $this->GetAllowance();
        }
        function GetAllowance()
        {
            $db = Database::getInstance();
            
            $sql = "SELECT * FROM professions WHERE id={$this->professionId}";
            $result = $db->query($sql)->fetch_assoc();
            
            return $result['allowance_for_food'];
        }
        
        function BuyDish($price)
        {
            $this->allowance -= $price;
        }
        
        function GetNewCustomerId()
        {
            $db = Database::getInstance();
            
            $sql = "SELECT * FROM customers ORDER BY customer_id DESC";
            $result = $db->query($sql)->fetch_assoc();
            
            if (!empty($result['customer_id']))
                return $result['customer_id'] + 1;
            else
                return 1;
        }
        
        function CustomerExists($id)
        {
            $db = Database::getInstance();
            
            $sql = "SELECT * FROM customers WHERE customer_id='${id}'";
            $customer = $db->query($sql)->fetch_assoc();
            
            if (!empty($customer))
                return true;
            else
                return false;
        }
        
        function ReadCustomer($id)
        {
            $db = Database::getInstance();
            
            $sql = "SELECT * FROM customers WHERE customer_id='${id}'";
            $customer = $db->query($sql)->fetch_assoc();
            
            $this->firstName = $customer['first_name'];
    		$this->lastName = $customer['last_name'];
    		$this->drinkerTypeId = $customer['drinker_type_id'];
    		$this->eaterTypeId = $customer['eater_type_id'];
    		$this->professionId = $customer['profession_id'];
    		$this->happiness = $customer['happiness'];
    		$this->stinginess = $customer['stinginess'];
    		$this->active = $customer['active'];
        }
    	
		function RecordCustomer()
    	{
    		$db = Database::getInstance();
    
    		$sql = "INSERT INTO customers (user_id, first_name, last_name, drinker_type_id, eater_type_id, profession_id, happiness, stinginess, active) VALUES ('{$this->userId}', '{$this->firstName}', '{$this->lastName}', '{$this->drinkerTypeId}', '{$this->eaterTypeId}', '{$this->professionId}', '{$this->happiness}', '{$this->stinginess}', '{$this->active}')";
    		$db->query($sql);
    	}
    	
		function UpdateHappiness($newHappiness)
    	{
    		$db = Database::getInstance();
    		
    		$sql = "UPDATE customers SET happiness={$newHappiness} WHERE customer_id={$this->customerId}";
    		$db->query($sql);
    		
    		if($newHappiness <= -1)
    		{
    		    $sql = "UPDATE customers SET active=0 WHERE customer_id={$this->customerId}";
    		    $db->query($sql);
    		}
    		
    	}
    	
    	function CustomerReturns()
    	{
    	    $target = rand(1, 9);
    	    
    	    if($target <= $this->happiness)
    	    {
    	        return true;
    	    }
    	    else
    	    {
    	        return false;
    	    }
    	}
    	
    	function GetDishPreferences()
    	{
    		$preferences = array();
    		$recipes = $this->GetRecipes();
    		$ingredientPreferences = $this->GetIngredientPreferences();
    		
    		foreach($recipes as $currentRecipe)
    		{
    			$preference = 0;
    			if($currentRecipe['poultry_qty'] > 0)
    			{
    				$preference += $currentRecipe['poultry_qty'] + $ingredientPreferences['chicken_pref'];
    			}
    			if($currentRecipe['pork_qty'] > 0)
    			{
    				$preference += $currentRecipe['pork_qty'] + $ingredientPreferences['pork_pref'];
    			}
    			if($currentRecipe['carrot_qty'] > 0)
    			{
    				$preference += $currentRecipe['carrot_qty'] + $ingredientPreferences['carrot_pref'];
    			}
    			if($currentRecipe['potato_qty'] > 0)
    			{
    				$preference += $currentRecipe['potato_qty'] + $ingredientPreferences['potato_pref'];
    			}
    			
    			$preferences[$currentRecipe['name']] = $preference;
    		}
    		
    		arsort($preferences);
    		
    		return $preferences;
    	}
    	
    	function GetIngredientPreferences()
    	{
    	    $db = Database::getInstance();
    	    
    	    $sql = "SELECT * FROM eatertypes WHERE id={$this->eaterTypeId}";
    	    return $db->query($sql)->fetch_assoc();
    	}
        		
    	function GetRecipes()
    	{
    		$db = Database::getInstance();
    
    		$sql = "SELECT * FROM recipes";
    		return $db->query($sql);
    	}
    	
    	function GetDrinkPreferences()
    	{
    	    $db = Database::getInstance();
    	    $sql = "SELECT * FROM drinkertypes WHERE id={$this->drinkerTypeId}";
    	    return $db->query($sql)->fetch_assoc();
    	}

        function GetRandomFirstName()
    	{
    		$db = Database::getInstance();
    		
    		$sql = "SELECT * FROM firstnames";
    		$names = $db->query($sql);
    		
    		$nameId =  rand(1 , mysqli_num_rows($names));
    		
    		$sql = "SELECT first_name FROM firstnames WHERE (id = '$nameId')";
    		$name = $db->query($sql);
    		
    		$result = $name->fetch_assoc();
    		
    		return $result['first_name'];
    	}
    	
    	function GetRandomLastName()
    	{
    		$db = Database::getInstance();
    		
    		$sql = "SELECT * FROM lastnames";
    		$names = $db->query($sql);
    		
    		$nameId =  rand(1 , mysqli_num_rows($names));
    		
    		$sql = "SELECT last_name FROM lastnames WHERE (id = '$nameId')";
    		$name = $db->query($sql)->fetch_assoc();
    		
    		return $name['last_name'];
    	}
    	
    	function GetRandomDrinkerType()
    	{
    		$db = Database::getInstance();
    		
    		$sql = "SELECT * FROM drinkertypes";
    		$drinkerTypes = $db->query($sql);
    		
    		return rand(1 , mysqli_num_rows($drinkerTypes));
    	}
    	
    	function GetRandomEaterType()
    	{
    		$db = Database::getInstance();
    		
    		$sql = "SELECT * FROM eatertypes";
    		$eaterTypes = $db->query($sql);
    		
    		return rand(1 , mysqli_num_rows($eaterTypes));
    	}
    	
    	function GetRandomProfession()
    	{
    		$db = Database::getInstance();
    		
    		$sql = "SELECT * FROM professions";
    		$professions = $db->query($sql);
    		
    		return rand(1 , mysqli_num_rows($professions));
    	}
    }