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
$new_fact = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{

   if (empty($_POST["new_fact"]))
     {$nameErr = "Fact is required";header('location:index.php');}
   else
    {
		$new_fact = test_input($_POST["new_fact"]);
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
	
if(!check_fact_in_db($con, $new_fact)){
	add_fact_in_db($con, $new_fact);
	header('location:index.php');
}
else{
	die("Fact \"$new_fact\" already exists in DB");
}

function test_input($data)
{
	if(($data == "") || ($data == " ") || ($data == "  ") || empty($data)){	
		die('Now its working??');	
		header('location:index.php');
	}
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return $data;
}

function check_fact_in_db($con, $fact)
{
	$query = "SELECT fact FROM tbl_facts WHERE fact='$fact'";
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

function add_fact_in_db($con, $fact)
{
	$temp = get_toal_facts_in_db($con) + 1;
	$fact_index = "F".$temp;
	$result = mysqli_query($con,"INSERT INTO tbl_facts (fact_index, fact) VALUES ('$fact_index', '$fact')");
	if(!$result)
		return FALSE;
	else
		return TRUE;
}

function get_toal_facts_in_db($con)
{
	$result = mysqli_query($con,"SELECT * FROM tbl_facts");
	if(!$result)
		 die("Table not found");
	else{
		return $result->num_rows;
	}
		
}


mysqli_close($con);
?>