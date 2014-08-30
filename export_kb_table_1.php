<?php
//Disable error reporting
//error_reporting(0);

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
$table = "kb_table_1"; // Enter Your Table Name 
//$qry1 = "select * from $table ORDER BY fact_index ASC";
//$qry1 = "select * from $table ORDER BY cast(substr(fact_index, 1) as signed) integer ASC";
$qry1 = "select * from $table ORDER BY is_derived";
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
if($row[7] == NULL){
	$row[7] = 'Nil';
}else{		
	$row[7] = str_replace("+ ", "or", $row[7]);	
	$row[7] = substr($row[7], 1);
	$row[7] = str_replace(" F", "&F", $row[7]);	
	//$row[7] = preg_replace("/[0-9]/ ", "&", $row[7]);
	//$row[7] = str_replace(" ", "", $row[7]);
}

if($row[5] == NULL)
	$row[5] = 'Nil';
if($row[6] == NULL)
	$row[6] = 'Nil';	
	
if($row[3] == 0)
	$row[3] = 'Basic';
else{
	if($row[3] == 1)
		$row[3] = 'Derived';	
}
	
$row[4] = 0;	
	
$row[5] = substr($row[5], 1);	
$row[5] = str_replace(" ", "", $row[5]);	 
	
//$output .= $row[2].','.$row[1].','.$row[3].','.$row[4].','.$row[5].','.$row[6].','.$row[7].',';
//$output .="\n";
$output .= $row[2].','.$row[1].','.$row[3].','.$row[4].','.$row[5].','.$row[6].','.$row[7];
$output .="\n";
}

// Download the file

$filename = "data.csv";
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);

echo $output;

//header('location:index.php');
exit;

?>