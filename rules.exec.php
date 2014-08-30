<?php
include_once('conf.php');

$con = mysqli_connect($db_host,$db_username,$db_password,$db_database);

// Check connection
if (mysqli_connect_errno($con))
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  die('Unable to connect to Database');
}

// define variables and set to empty values
$new_rule = "";
$new_rule_short = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{

   if (empty($_POST["new_rule"]) || empty($_POST["new_rule_short"]))
     {$nameErr = "Rule is required";header('location:rules.php');}
   else
    {
		$new_rule = test_input($_POST["new_rule"]);
		$new_rule_short = test_input($_POST["new_rule_short"]);
	}
		
}
/*
if(($new_fact == "") || ($new_fact == " ") || ($new_fact == "  "))
	header('location:index.php');*/
/*	
if(($new_fact == "") || ($new_fact == " ") || ($new_fact == "  ") || empty($new_fact)){	
	die('Why isn\'t it working??');	
	header('location:index.php');
}	*/
	
if(!check_rule_in_db($con, $new_rule_short)){
	add_rule_in_db($con, $new_rule_short, $new_rule);
	header('location:rules.php');
}
else{
	die("Fact \"$new_rule\" already exists in DB");
}

function test_input($data)
{
	if(($data == "") || ($data == " ") || ($data == "  ") || empty($data)){	
		die('Now its working??');	
		header('location:rules.php');
	}
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return $data;
}

function check_rule_in_db($con, $new_rule_short)
{
	$query = "SELECT rule_short FROM tbl_rules WHERE rule='$new_rule_short'";
	$result = mysqli_query($con, $query);
	if(!$result)
		 die("Table not found");
	else{
		if($result->num_rows > 0)
			return TRUE;
		else
			return FALSE;
	}
		
}

function add_rule_in_db($con, $new_rule_short, $new_rule)
{
	$temp = get_toal_rules_in_db($con) + 1;
	$rule_index = "R".$temp;
	$result = mysqli_query($con,"INSERT INTO tbl_rules (rule_index, rule, rule_short) VALUES ('$rule_index', '$new_rule', '$new_rule_short')");
	if(!$result)
		return FALSE;
	else
		return TRUE;
}

function get_toal_rules_in_db($con)
{
	$result = mysqli_query($con,"SELECT * FROM tbl_rules");
	if(!$result)
		 die("Table not found");
	else{
		return $result->num_rows;
	}
		
}


mysqli_close($con);
?>