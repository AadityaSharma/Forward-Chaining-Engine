<?php
	
include_once('conf.php');

$con = mysqli_connect($db_host,$db_username,$db_password,$db_database);

// Check connection
if (mysqli_connect_errno($con))
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  die('Unable to connect to Database');
}

//Empty KB Table 1 and KB Table 2
empty_kb_tables($con);

//Empty facts table & rules table
empty_facts_and_rules_table($con);
	
if ($_SERVER["REQUEST_METHOD"] == "POST")
{

   if (!empty($_POST["source"])){
		$link = test_input($_POST["source"]);
		header("location:$link");
	}
	else{
		header('location:index.php');
	}		
}
function test_input($data)
{
	if(($data == "") || ($data == " ") || ($data == "  ") || empty($data)){	
		//die('Now its working??');	
		header('location:index.php');
	}
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return $data;
}
	
/***********************************************************************
******Empty KB Table 1 and KB Table 2***********************************
************************************************************************/
function empty_kb_tables($con)
{
	$result1 = mysqli_query($con,"TRUNCATE kb_table_1");
	$result2 = mysqli_query($con,"TRUNCATE kb_table_2");
	if(!($result1 && $result2))
		 die("Tables not found");
}
/***********************************************************************
******Empty facts & rules table***********************************
************************************************************************/
function empty_facts_and_rules_table($con)
{
	$result1 = mysqli_query($con,"TRUNCATE tbl_facts");
	$result2 = mysqli_query($con,"TRUNCATE tbl_rules");
	if(!($result1 && $result2))
		 die("Tables not found");
}	
?>