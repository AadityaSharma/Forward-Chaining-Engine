<?php

// Database Connection
include_once('conf.php');

$host="forwardchaining.db.11538578.hostedresource.com";
$uname="forwardchaining";
$pass="Nishant123456!";
$database = "forward_chaining"; 
/*
$connection=mysqli_connect($host,$uname,$pass); 

echo mysqli_error();

//or die("Database Connection Failed");
$selectdb=mysqli_select_db($database) or 
die("Database could not be selected"); 
$result=mysqli_select_db($database)
or die("database cannot be selected <br>");
*/

$con = mysqli_connect($db_host,$db_username,$db_password,$db_database);

// Check connection
if (mysqli_connect_errno($con))
{
  echo "Failed to connect to mysqli: " . mysqli_connect_error();
  die('Unable to connect to Database');
}

// Fetch Record from Database

$output = "";
$table = "kb_table_2"; // Enter Your Table Name 
$qry1 = "select * from $table ORDER BY rule_index ASC";
//$qry1 = "select * from $table ORDER BY cast(substr(rule_index, 1) as signed) integer ASC";
$sql = mysqli_query($con, $qry1);
$columns_total = mysqli_num_fields($sql);

// Get The Field Name
/*
for ($i = 0; $i < $columns_total; $i++) {
$heading = mysqli_field_name($sql, $i);
$output .= '"'.$heading.'",';
}
$output .="\n";
*/
// Get Records from the table

while ($row = mysqli_fetch_array($sql)) {
/*for ($i = 0; $i < $columns_total; $i++) {
if($i != 0)
	$output .='"'.$row["$i"].'",';
}*/

$row[2] = substr($row[2], 1);
$row[2] = str_replace(" ", "&", $row[2]);	


if($row[3] == NULL)
	$row[3] = 'Nil';
if($row[4] == NULL)
	$row[4] = 'Nil';	
if($row[5] == NULL)
	$row[5] = 'Nil';	

	
$output .= $row[1].','.$row[2].','.$row[3].','.$row[4].','.$row[5].',';
$output .="\n";
}

// Download the file

$filename = "data1.csv";
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);

echo $output;
exit;

?>