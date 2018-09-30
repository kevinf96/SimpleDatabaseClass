<?php
	require("class/cache.class.php");
	require("class/database.class.php");

	phpFastCache::$storage = "auto";
	$Database = new Database("127.0.0.1","root","password","database");
	
	$updateQuery = $Database->query("UPDATE account SET points=5 WHERE id=5");
	if($updateQuery){
		echo "successfully updated account table";
	}
	else{
		echo "query failed";
	}
	
	$minMoney = 5;
	$selAccount = $Database->get("SELECT name FROM account WHERE money>:moneyVariable",array(":moneyVariable" => $minMoney)); //With cache
	$selAccount = $Database->get("SELECT name FROM account WHERE money>:moneyVariable",array(":moneyVariable" => $minMoney),false); //Without cache
	foreach($selAccount as &$getAccount){
		echo $getAccount->name;
	}
	
	$Database->delete();
	
?>