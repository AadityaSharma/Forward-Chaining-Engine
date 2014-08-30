<?php
	
include_once('generate_kb_tables.php');
	
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
	
?>