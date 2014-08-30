<?php
include_once('conf.php');

$con = mysqli_connect($db_host,$db_username,$db_password,$db_database);

// Check connection
if (mysqli_connect_errno($con))
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  die('Unable to connect to Database');
}
/*
global $inferences;
global $facts;
global $rule_status;*/
$inferences = array();
$facts = array();
$rule_status = array();
$recursive_count = 0;
$facts = get_all_facts($con);
$rule_status = initialize_rule_status($con);

forward_chaining($con); //Call forward chaining Function

/****************************************************************************
*******************Process Forward Chaining Function*************************
*******************This Function Gets Executed First*************************
*****************************************************************************/
function forward_chaining($con)
{
	global $recursive_count;
	global $facts;
	global $inferences;
	global $rule_status;
	
	foreach($facts as $fact){
		echo "<br>loop started for ".$fact['fact_index']." which has truth value ".$fact['truth_value']."<ul>";

		if(is_fact_derived($con, $fact['fact_index'])){
			die('<br>Provided fact '.$fact['fact_index'].' is derived. Can\'t proceed further');
		}
		
		$lhs_rules = get_rules_which_has_this_fact_in_lhs($con, $fact['fact_index']);	
		
		foreach($lhs_rules as $lhs_rule){
			$rule_index = $lhs_rule;
			
			//echo "<br>---->rule status for $rule_index is: ".$rule_status[$rule_index];
			
			if($rule_status[$rule_index] == "not fired"){
				process_forward_chain_of_rules($con, $rule_index);
			}	
		}
		echo "</ul><br>loop ended for ".$fact['fact_index']."<br>";	
	}
	
	foreach($inferences as $inference){
		echo "<br>loop started for inference ".$fact['fact_index']." which has truth value ".$fact['truth_value']."<ul>";

		/*if(is_fact_derived($con, $inference['fact_index'])){
			die('<br>Provided fact '.$inference['fact_index'].' is derived. Can\'t proceed further');
		}*/
		
		$lhs_rules = get_rules_which_has_this_fact_in_lhs($con, $inference['fact_index']);	
		
		foreach($lhs_rules as $lhs_rule){
			$rule_index = $lhs_rule;
			
			//echo "<br>---->rule status for $rule_index is: ".$rule_status[$rule_index];
			
			if($rule_status[$rule_index] == "not fired"){
				echo "<h4>Processing process_forward_chain_of_rules in main function for $rule_index</h4>";
				process_forward_chain_of_rules($con, $rule_index);
			}	
		}
		echo "</ul><br>loop ended for inference ".$fact['fact_index']."<br>";	
	}
	

	if($recursive_count > 0){
		//Print all provided facts
		echo "<h4>All provided facts are:</h4>";
		foreach($facts as $fact){
			echo "<br>".$fact['fact_index'];
		}

		//Print all inferences
		echo "<h4>All Inferences are:</h4>";
		foreach($inferences as $inference){
			echo "<br>".$inference['fact_index'];
		}
		
		//Print all rule_status
		echo "<h4>All Rule Status are:</h4>";
		foreach($rule_status as $status){
			echo "<br>".$status;
		}
		
		exit();
	}
	else{
		echo "<br><h1>Main function calling second time</h1>";
		$recursive_count++;
		forward_chaining($con);
	}
}


/****************************************************************************
*******************Process Forward Chain of Rules****************************
*****************************************************************************/
//function process_forward_chain_of_rules($con, $rule_index, &$facts, &$inferences)
function process_forward_chain_of_rules($con, $rule_index)
{
	global $facts;
	global $inferences;
	global $rule_status;
	
	echo "<br><h5>----> Entered in process forward chaining for $rule_index</h5><ul>";
	
	$result = mysqli_query($con,"SELECT * FROM kb_table_2 WHERE rule_index='$rule_index'");
	if(!$result)
		die("KB Table 2 not found");
	else{
		if($result->num_rows == 1){
			//echo "<br>----> one rule is in db";
			while($row = mysqli_fetch_array($result)){
				$dependent_facts = $row['dependent_facts'];
				$derived_fact = $row['derived_fact'];
				//$dependent_facts_truth_value = eveluate_truth_value_of_dependent_facts($con, $dependent_facts, $facts, $inferences);
				$dependent_facts_truth_value = eveluate_truth_value_of_dependent_facts($con, $dependent_facts);
				echo "<br>~~truth value of dependent facts for $rule_index ($dependent_facts) is ".($dependent_facts_truth_value ? "TRUE" : "FALSE");
				if($dependent_facts_truth_value === TRUE){
					if($rule_status[$rule_index] == "not fired"){
						if(empty($inferences)){
							$inferences[0]['fact_index'] = $derived_fact;
							$inferences[0]['truth_value'] = TRUE;
						}
						else{
							$size = sizeof($inferences);
							$inferences[$size]['fact_index'] = $derived_fact;
							$inferences[$size]['truth_value'] = TRUE;
						}
						
						$rule_status[$rule_index] = "fired";
						echo "<h4>$dependent_facts has truth value ".($dependent_facts_truth_value ? "TRUE" : "FALSE")." Hence Derived fact $derived_fact is added to inferenced facts list</h4>";
						echo "<h4>And Rule status for $rule_index is marked as ".$rule_status[$rule_index]."</h4>";
						$result1 = mysqli_query($con,"SELECT * FROM kb_table_2 WHERE rule_index='$rule_index'");
						if(!$result1)
							die("KB Table 2 not found");
						else{
							if($result->num_rows == 1){
								$leads_to_next_rule = $row['leads_to_next_rule'];
								
								echo "<br>$rule_index leads_to_next_rule $leads_to_next_rule";
								
								if(!empty($leads_to_next_rule)){
									$token5 = strtok($leads_to_next_rule, " ");
									$next_rules = array();
									$temp_count = 0;
									while ($token5 != false)
									{
										$next_rules[$temp_count] = $token5;
										$token5 = strtok(" ");
										$temp_count++;
									}

									foreach($next_rules as $next_rule){
										echo "<br>leads_to_next_rule is $leads_to_next_rule and token5 is $next_rule";
										/*if(substr($token , 0, 1) == '!'){
											$strlen = strlen($token) - 1;
											$temp = substr($token , 1, $strlen);
											echo "<br>now processing process_forward_chain_of_rules for $temp";
											process_forward_chain_of_rules($con, $temp);
										}
										else{		
											if( ($token != "||") && ($token != "&&") && ($token != "(") && ($token != ")") ){
												echo "<br>now processing process_forward_chain_of_rules for $token";
												process_forward_chain_of_rules($con, $token);
											}	
										}*/	
										//echo "<br>now processing process_forward_chain_of_rules for $next_rule";
										process_forward_chain_of_rules($con, $next_rule);
									}	
								}	
							}	
						}	
						
					}
				}
				
			}
		}
	}
	echo "</ul><h5><---- Finished process forward chaining for $rule_index</h5>";

}

/*************************************************************************************
*******************Evaluate Truth Value of Dependent Facts****************************
**************************************************************************************/
//function eveluate_truth_value_of_dependent_facts($con, $dependent_facts, &$facts, &$inferences)
function eveluate_truth_value_of_dependent_facts($con, $dependent_facts)
{
	global $facts;
	global $inferences;
	global $rule_status;
	
	$string = $dependent_facts;
	
	if(mb_substr_count($string, "F") == 1){
		echo "<br>-->The logical expression has only one fact <ul>";	
		$token = $string;
		$match = FALSE;
		$temp_truth_value = FALSE;
				
		if(substr($token , 0, 1) == '!'){
			$strlen = strlen($token) - 1;
			$temp = substr($token , 1, $strlen);
				
			foreach($facts as $fact){
				if($temp == $fact['fact_index']){
					$temp_truth_value = $fact['truth_value'];
						
					if($temp_truth_value == TRUE)
						$temp_truth_value = FALSE;
					else
						$temp_truth_value = TRUE;
							
					$match = TRUE;
					
					//echo "<br>~~$temp matched with fact: ".$fact['fact_index']." and match is set as TRUE";	
				}
			}
			foreach($inferences as $inference){
				echo "<br>~~processing ".$inference['fact_index'].", checking if ".$temp." (temp) == ".$inference['fact_index']." (fact)";
				if($temp == $inference['fact_index']){
					$temp_truth_value = $inference['truth_value'];
					
					if($temp_truth_value == TRUE)
						$temp_truth_value = FALSE;
					else
						$temp_truth_value = TRUE;	
						
					$match = TRUE;
					echo "<br>~~$temp matched with inference: ".$inference['fact_index']." and match is set as TRUE";	
				}
			}
			
		}
		else{
			$temp = $token;
				
			foreach($facts as $fact){
				if($temp == $fact['fact_index']){
					$temp_truth_value = $fact['truth_value'];
						
					$match = TRUE;
					//echo "<br>~~$temp matched with fact: ".$fact['fact_index']." and match is set as TRUE";	
				}
			}
			foreach($inferences as $inference){
				echo "<br>~~processing ".$inference['fact_index'].", checking if ".$temp." (temp) == ".$inference['fact_index']." (inference)";
				if($temp == $inference['fact_index']){
					$temp_truth_value = $inference['truth_value'];	
						
					$match = TRUE;
					echo "<br>~~$temp matched with inference: ".$inference['fact_index']." and match is set as TRUE";	
				}
			}
		}
		echo"</ul>";
		if($match == FALSE){
			//echo "<h4>truth_value is returned FALSE</h4>";
			return FALSE;
		}	
		else{
			//echo "<h4>truth_value is returned true as $temp_truth_value</h4>";
			
			return $temp_truth_value;
		}
		
	}
	else{	
		echo "<br>~~ logical expression (dependent facts) $string ---> ";	
		$string = str_replace(".", " || ", $string);
		echo $string;
		echo " ---> ";	
		$string = str_replace("+", " && ", $string);
		echo $string;
		
		echo "<ul>";
		
		$token = strtok($string, " ");
		$match = FALSE;

		while ($token != false)
		{
			$temp_truth_value = FALSE;
			
			
			
			if((substr($token , 0, 2) != '||') && (substr($token , 0, 2) != '&&')){
			
				if(substr($token , 0, 1) == '!'){
					$strlen = strlen($token) - 1;
					$temp = substr($token , 1, $strlen);
					
					foreach($facts as $fact){
						if($temp == $fact['fact_index']){
							echo "<br>~~processing ".$fact['fact_index'].", checking if ".$temp." (temp) == ".$fact['fact_index']." (fact)";
							$temp_truth_value = $fact['truth_value'];
							
							if($temp_truth_value == TRUE)
								$temp_truth_value = FALSE;
							else
								$temp_truth_value = TRUE;
								
							$string = str_replace($temp, $temp_truth_value, $string);	
							$match = TRUE;
						}
					}
					foreach($inferences as $inference){
						echo "<br>~~processing ".$inference['fact_index'].", checking if ".$temp." (temp) == ".$inference['fact_index']." (inference)";
						if($temp == $inference['fact_index']){
							$temp_truth_value = $inference['truth_value'];
							
							if($temp_truth_value == TRUE)
								$temp_truth_value = FALSE;
							else
								$temp_truth_value = TRUE;
								
							$string = str_replace($temp, $temp_truth_value, $string);		
								
							$match = TRUE;
						}
					}
					
				}
				else{
					$temp = $token;
					//echo "<h4>temp is $temp</h4>";
					foreach($facts as $fact){
						//echo "<h4>processing ".$fact['fact_index'].", checking if ".$temp." (temp) == ".$fact['fact_index']." (fact)</h4>";
						if($temp == $fact['fact_index']){
						//echo "<h4>temp $temp matched with fact ".$fact['fact_index']."</h4>";		
							$temp_truth_value = $fact['truth_value'];
								
							$string = str_replace($temp, $temp_truth_value, $string);	
								
							$match = TRUE;
							//echo "<br>~~$temp matched with fact: ".$fact['fact_index']." and match is set as TRUE";	
						}
					}
					foreach($inferences as $inference){
						echo "<h4>processing ".$inference['fact_index'].", checking if ".$temp." (temp) == ".$inference['fact_index']." (fact)</h4>";
						if($temp == $inference['fact_index']){
							$temp_truth_value = $inference['truth_value'];
							
							$string = str_replace($temp, $temp_truth_value, $string);		
								
							$match = TRUE;
							echo "<br>~~$temp matched with inference: ".$inference['fact_index']." and match is set as TRUE";	
						}
					}
				}
			}
			
			$token = strtok(" ");
			
			if($match == FALSE){ 
				echo"<br>~~As match is false aborted from loop";
				break;
			}	
				
			$match = FALSE;	
		}
		echo"</ul>";
		if($match == FALSE){
			//echo "<h4>truth_value is returned FALSE</h4>";
			return FALSE;
		}	
		else{
			$truth_value = eval("return (".$string.");");
			
			//echo "<h4>truth_value is returned true as $truth_value</h4>";
			
			return $truth_value;
		}
	}	
}

/*************************************************************************
******************* Set all rule status as "not fired" *******************
**************************************************************************/
function initialize_rule_status($con)
{
	//$i=0;
	$rule_status = array();
	$result = mysqli_query($con,"SELECT * FROM tbl_rules");
	if(!$result)
		die('error: table doesn\'t exist');
	else{
		if($result->num_rows > 0){
			while($row = mysqli_fetch_array($result)){
				$temp = $row['rule_index'];
				$rule_status[$temp] = 'not fired';
				//$rule_status[$i][$temp] = 'not fired';
				//$i++;
			}
			return $rule_status;
		}
		else{
			return FALSE;
		}
	}	
}
/*************************************************************************
*******************Get all facts from Database****************************
**************************************************************************/
function get_all_facts($con)
{
	$facts = array( 
				array( 'fact_index' => 'F2',
						   'truth_value' => TRUE
                    ),
				array( 'fact_index' => 'F6',
						   'truth_value' => TRUE
                    ),	
				array( 'fact_index' => 'F4',
					  'truth_value' => TRUE
                    ),
				array( 'fact_index' => 'F5',
					  'truth_value' => TRUE
                    )	
             );
	return $facts;		 
}
/**************************************************************
******Determine whether fact is "basic" or "derived"***********************
***************************************************************/
function is_fact_derived($con, $fact_index)
{
	$status = FALSE;
	$rhs_facts = array();
	$i=0;
	
	$result = mysqli_query($con,"SELECT * FROM tbl_rules");
	if(!$result)
		 die("Table not found");
	else{
		if($result->num_rows > 0){
			while($row = mysqli_fetch_array($result)){
				$rule_short = $row['rule_short'];
				$rule_index = $row['rule_index'];
				$rhs_facts = extract_rhs_facts_from_rule($con, $rule_index);
				foreach ($rhs_facts as $rhs_fact) {
					if($fact_index == $rhs_fact){
						$status = TRUE;		
					}	
				}
			}
		}
	}
	
	return $status;
}
/**************************************************************
******Get rules which has a fact in LHS***********************
***************************************************************/
function get_rules_which_has_this_fact_in_lhs($con, $fact_index)
{
	$lhs_rules = array();
	$lhs_facts = array();
	
	$i=0;
	
	$result = mysqli_query($con,"SELECT * FROM tbl_rules");
	if(!$result)
		 die("Table not found");
	else{
		if($result->num_rows > 0){
			while($row = mysqli_fetch_array($result)){
				$rule_short = $row['rule_short'];
				$rule_index = $row['rule_index'];
				$lhs_facts = extract_lhs_facts_from_rule($con, $rule_index);
				foreach ($lhs_facts as $lhs_fact) {
					if($fact_index == $lhs_fact){
						$lhs_rules[$i] = $rule_index;
						$i++;		
					}	
				}
			}
		}
	}
	
	return $lhs_rules;
}
/**************************************************************
******Get rules which has a fact in RHS***********************
***************************************************************/
function get_rules_which_has_this_fact_in_rhs($con, $fact_index)
{
	$rhs_rules = array();
	$rhs_facts = array();
	
	$i=0;
	
	$result = mysqli_query($con,"SELECT * FROM tbl_rules");
	if(!$result)
		 die("Table not found");
	else{
		if($result->num_rows > 0){
			while($row = mysqli_fetch_array($result)){
				$rule_short = $row['rule_short'];
				$rule_index = $row['rule_index'];
				$rhs_facts = extract_rhs_facts_from_rule($con, $rule_index);
				foreach ($rhs_facts as $rhs_fact) {
					if($fact_index == $rhs_fact){
						$rhs_rules[$i] = $rule_index;
						$i++;		
					}	
				}
			}
		}
	}
	
	return $rhs_rules;
}
/**************************************************************
******Extract LHS facts from rule***********************
***************************************************************/
function extract_lhs_facts_from_rule($con, $rule_index)
{
        $short_rule = get_short_rule($con, $rule_index);
        
        $token = strtok($short_rule, "+");
        $facts = array();
        $i = 0;
  
        while ($token != false)
    {
                if($token == "THEN" || $token == "then")
                        break;
                
                if( !($token == "IF" || $token == "if" || $token == "OR" ||  $token == "or" || $token == "AND" || $token == "and" || $token == "NOT" || $token == "not" || $token == "(" || $token == ")") )
                        $facts[$i] = $token;    
                $token = strtok("+");
                $i++;
    }
        
        return $facts;
}

/**************************************************************
******Extract RHS facts from rule***********************
***************************************************************/
function extract_rhs_facts_from_rule($con, $rule_index)
{
        $short_rule = get_short_rule($con, $rule_index);
        
        $token = strtok($short_rule, "+");
        $facts = array();
        $i = 0;
        $temp = FALSE;
  
        while ($token != false)
    {
                if($temp == FALSE){
                        if($token == "THEN" || $token == "then")
                                $temp = TRUE;
                }
                else{
                        if( !($token == "IF" || $token == "if" || $token == "OR" ||  $token == "or" || $token == "AND" || $token == "and" || $token == "NOT" || $token == "not" || $token == "THEN" || $token == "then" || $token == "(" || $token == ")") )
                                $facts[$i] = $token; 
                        $i++;
                }
                $token = strtok("+");
    }
        
        return $facts;
}
/*************************************************************
******Get short rule by index*******************************
**************************************************************/
function get_short_rule($con, $rule_index)
{
	$result = mysqli_query($con,"SELECT rule_short FROM tbl_rules WHERE rule_index='$rule_index'");
	if(!$result)
		 die("Table not found");
	else{
		if($result->num_rows == 1){
			while($row = mysqli_fetch_array($result)){
				$rule_name = $row['rule_short'];
			}
			return $rule_name;
		}
		else
			die("rule $rule_index doesn\'t exist");
	}
}


mysqli_close($con);
?>