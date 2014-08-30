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

//Get rule name and enter rule name in KB Table 2
$total_rules = get_total_number_of_rules($con);

//Process Each Rule
for($i=1;$i<=$total_rules;$i++) 
{
	$rule_index = 'R'.$i;
	$rule_short = get_short_rule($con, $rule_index);
	$rule = get_rule_name($con, $rule_index);
	
	enter_rule_in_kb2($con, $rule_index); //get rule name and enter rule name in KB Table 2
	
	//display error if rule doesn't start with if 
	if( !( ((substr($rule_short, 0, 2) == 'IF') || (substr($rule_short, 0, 2) == 'if')) && ((substr($rule, 0, 2) == 'IF') || (substr($rule, 0, 2) == 'if')) ) ){
		die("Rule $rule_index : \"$rule\" doesn\'t start with \'if\'");
		continue;
	}	
	if( !( (strpos($rule_short,'THEN') !== false) || (strpos($rule_short,'then') !== false) ) ){
		die("Rule $rule_index : \"$rule\" doesn\'t have \'then\' keyword");
		continue;
	}
	
	$lhs_facts = array();
	$lhs_facts = extract_lhs_facts_from_rule($con, $rule_index);
	
	foreach ($lhs_facts as $lhs_fact) {
		$fact_index = $lhs_fact;
		if(!check_fact_in_kb1($con, $lhs_fact))
		{
			$fact_index = $lhs_fact;
			$fact = get_fact_name_by_index($con, $fact_index);
			if(is_fact_derived($con, $fact_index))
				$is_derived = 1;
			else
				$is_derived = 0;
				
			$lhs_rules = get_rules_which_has_this_fact_in_lhs($con, $fact_index);	
			$lhs_rules_string = "";
			foreach($lhs_rules as $lhs_rule){
				$lhs_rules_string = $lhs_rules_string." ".$lhs_rule;
			
			}
			
			$query1 = "INSERT INTO kb_table_1 (fact_index, fact, is_derived, rules_in_lhs) VALUES ('$fact_index', '$fact', '$is_derived', '$lhs_rules_string')";
			
			$result1 = mysqli_query($con,$query1);	
		}
		
		$fact_index = $lhs_fact;
		if(is_fact_derived($con, $fact_index)){
			$rhs_rules = get_rules_which_has_this_fact_in_rhs($con, $fact_index);	
			foreach($rhs_rules as $rhs_rule){
				enter_rule_in_kb2($con, $rhs_rule);
				$leads_to_next_rule = "";
				$result1 = mysqli_query($con,"SELECT * FROM kb_table_2 WHERE rule_index='$rhs_rule'");	
				if($result1->num_rows > 0){
					while($row = mysqli_fetch_array($result1)){
						$leads_to_next_rule = $row['leads_to_next_rule'];
					}
				}
				$leads_to_next_rule = $leads_to_next_rule." ".$rule_index;	
				$result2 = mysqli_query($con,"UPDATE kb_table_2 SET leads_to_next_rule='$leads_to_next_rule' WHERE rule_index='$rhs_rule'");
				
			}	
		}
		
		/********************Check following statements again in this for loop ***********/
		/*$dependent_facts = "";
		$result1 = mysqli_query($con,"SELECT * FROM kb_table_2 WHERE rule_index='$rule_index'");	
		if($result1->num_rows > 0){
			while($row = mysqli_fetch_array($result1)){
				$dependent_facts = $row['dependent_facts'];
			}
		}
		
		$dependent_facts = $dependent_facts." ".$fact_index;	
		//$dependent_facts = $dependent_facts.".".$fact_index;	
		//I am not sure that above statement should be used or not
		mysqli_query($con,"UPDATE kb_table_2 SET dependent_facts='$dependent_facts' WHERE rule_index='$rule_index'");*/
		
		$dependent_facts = extract_dependent_facts_logical_expression($con, $rule_index);
		mysqli_query($con,"UPDATE kb_table_2 SET dependent_facts='$dependent_facts' WHERE rule_index='$rule_index'");
	}
	
	$rhs_facts = array();
	$rhs_facts = extract_rhs_facts_from_rule($con, $rule_index);
	
	foreach ($rhs_facts as $rhs_fact) {
		$fact_index = $rhs_fact;
		if(!check_fact_in_kb1($con, $fact_index)){
			$fact_index = $rhs_fact;
			$fact = get_fact_name_by_index($con, $fact_index);
			if(is_fact_derived($con, $fact_index))
				$is_derived = 1;
			else
				$is_derived = 0;
				
			$lhs_rules = get_rules_which_has_this_fact_in_lhs($con, $fact_index);	
			$lhs_rules_string = "";
			foreach($lhs_rules as $lhs_rule){
				$lhs_rules_string = $lhs_rules_string." ".$lhs_rule;
			
			}
			
			$rhs_rules = get_rules_which_has_this_fact_in_rhs($con, $fact_index);	
			$rhs_rules_string = "";
			foreach($rhs_rules as $rhs_rule){
				$rhs_rules_string = $rhs_rules_string." ".$rhs_rule;
			}
			$query1 = "INSERT INTO kb_table_1 (fact_index, fact, is_derived, rules_in_lhs, rules_in_rhs) VALUES ('$fact_index', '$fact', '$is_derived', '$lhs_rules_string', '$rhs_rules_string')";

			$result1 = mysqli_query($con,$query1);
			if(!$result1)
				die("error on line 143");
				
			
			
		}
		
		$result3 = mysqli_query($con,"UPDATE kb_table_2 SET derived_fact='$fact_index' WHERE rule_index='$rule_index'");
		
		$lhs_rules = get_rules_which_has_this_fact_in_lhs($con, $fact_index);	
		foreach($lhs_rules as $lhs_rule){
			//echo "<br>lhs_rule : $lhs_rule";
			enter_rule_in_kb2($con, $lhs_rule);
			$comes_from_previous_rule = $rule_index;
			$result1 = mysqli_query($con,"SELECT * FROM kb_table_2 WHERE rule_index='$lhs_rule'");	
			if($result1->num_rows > 0){
				while($row = mysqli_fetch_array($result1)){
					$comes_from_previous_rule = $comes_from_previous_rule." ".$row['comes_from_previous_rule'];
				}
			}else{
				die('error');
			}
			$result2 = mysqli_query($con,"UPDATE kb_table_2 SET comes_from_previous_rule='$comes_from_previous_rule' WHERE rule_index='$lhs_rule'");
		}
		
		$result = mysqli_query($con,"SELECT * FROM kb_table_2 WHERE rule_index='$rule_index'");
		if(!$result)
			die("KB Table 2 not found");
		else{
			if($result->num_rows > 0){
				while($row = mysqli_fetch_array($result)){
					$dependent_facts = $row['dependent_facts'];
					$derived_fact = $row['derived_fact'];
					$derived_from = $dependent_facts;
					$result1 = mysqli_query($con,"SELECT derived_from FROM kb_table_1 WHERE fact_index='$fact_index'");
					if($result1 -> num_rows > 0){
						while($row = mysqli_fetch_array($result1)){
							if(!empty($row['derived_from']))
								$dependent_facts = $dependent_facts."+".$row['derived_from'];
							mysqli_query($con,"UPDATE kb_table_1 SET derived_from='$dependent_facts' WHERE fact_index='$fact_index'");	
						}
					}
				/*	
					$dependent_facts = extract_dependent_facts_logical_expression($con, $rule_index);
					
					mysqli_query($con,"UPDATE kb_table_1 SET derived_from='$dependent_facts' WHERE fact_index='$fact_index'");	*/
				}
			}
		}
	}
	
}

//For each row in KB Table 1
$result = mysqli_query($con,"SELECT * FROM kb_table_1");
if(!$result)
	die("Table not found");
else{
	if($result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
			$fact_index = $row['fact_index'];
			if($row['is_derived'] == NULL){
				$result2 = mysqli_query($con,"UPDATE kb_table_1 SET is_derived='0', level='0', rules_in_rhs='', derived_from='' WHERE fact_index='$fact_index'");
			}
			else{
				if($row['rules_in_lhs'] == NULL){
					$result2 = mysqli_query($con,"UPDATE kb_table_1 SET rules_in_lhs='' WHERE fact_index='$fact_index'");
					//Compute and update column ‘Level’ {HOW???} --- this part is Pending 
				}
			}
		}
	}
}

//For each row in KB Table 2
$result = mysqli_query($con,"SELECT * FROM kb_table_2");
if(!$result)
	die("Table not found");
else{
	if($result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
			$rule_index = $row['rule_index'];
			if($row['comes_from_previous_rule'] == NULL){
				$result2 = mysqli_query($con,"UPDATE kb_table_2 SET comes_from_previous_rule='' WHERE rule_index='$rule_index'");
			}
			if($row['leads_to_next_rule'] == NULL){
				$result2 = mysqli_query($con,"UPDATE kb_table_2 SET leads_to_next_rule='' WHERE rule_index='$rule_index'");
			}
			
		}
	}
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

/*************************************************************
******Get total number of rules*******************************
**************************************************************/
function get_total_number_of_rules($con)
{
	$result = mysqli_query($con,"SELECT * FROM tbl_rules");
	if(!$result)
		 die("Table not found");
	else{
		return $result->num_rows;
	}
}

/*************************************************************
******Get rule name by index*******************************
**************************************************************/
function get_fact_name_by_index($con, $fact_index)
{
	$result = mysqli_query($con,"SELECT fact FROM tbl_facts WHERE fact_index='$fact_index'");
	if(!$result)
		 die("Table not found");
	else{
		if($result->num_rows == 1){
			while($row = mysqli_fetch_array($result)){
				$fact = $row['fact'];
			}
			return $fact;
		}
		else
			die("fact $fact_index doesn\'t exist");
	}
}

/*************************************************************
******Get rule name by index*******************************
**************************************************************/
function get_rule_name($con, $rule_index)
{
	$result = mysqli_query($con,"SELECT rule FROM tbl_rules WHERE rule_index='$rule_index'");
	if(!$result)
		 die("Table not found");
	else{
		if($result->num_rows == 1){
			while($row = mysqli_fetch_array($result)){
				$rule_name = $row['rule'];
			}
			return $rule_name;
		}
		else
			die("rule $rule_index doesn\'t exist");
	}
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

/**************************************************************
******get rule name and enter rule name in KB Table 2**********
***************************************************************/
function enter_rule_in_kb2($con, $rule_index)
{
	if(!check_rule_in_kb2($con, $rule_index)){
		
		$result = mysqli_query($con,"SELECT * FROM tbl_rules WHERE rule_index='$rule_index'");
		if(!$result)
			 die("Table not found");
		else{
			if($result->num_rows > 0){
				while($row = mysqli_fetch_array($result)){
					$rule_name = $row['rule'];
				}
				
				if(!check_rule_in_kb2($con, $rule_index))
					$result2 = mysqli_query($con,"INSERT INTO kb_table_2 (rule_index) VALUES ('$rule_index')");
				
			}
		}
	}	
}

/**************************************************************
******check if fact exists in KB Table 1***********************
***************************************************************/
function check_fact_in_kb1($con, $fact_index)
{
	$result = mysqli_query($con,"SELECT * FROM kb_table_1 WHERE fact_index='$fact_index'");
	if(!$result)
		 die("Table not found");
	else{
		if($result->num_rows > 0)
			return TRUE;
		else
			return FALSE;
	}
}
/**************************************************************
******check if rule exists in KB Table 2***********************
***************************************************************/
function check_rule_in_kb2($con, $rule_index)
{
	$result = mysqli_query($con,"SELECT * FROM kb_table_2 WHERE rule_index='$rule_index'");
	if(!$result)
		 die("Table not found");
	else{
		if($result->num_rows > 0)
			return TRUE;
		else
			return FALSE;
	}
}

/*******************************************************************************
******Extract dependent facts logical expression for rule***********************
********************************************************************************/
function extract_dependent_facts_logical_expression($con, $rule_index)
{
	$short_rule = get_short_rule($con, $rule_index);
	
	$token = strtok($short_rule, "+");
	$facts = array();
	$i = 0;
	$not_value = false;
	
	while ($token != false)
    {
		if($token == "THEN" || $token == "then")
			break;
		
		/*if( !($token == "IF" || $token == "if" || $token == "OR" ||  $token == "or" || $token == "AND" || $token == "and" || $token == "NOT" || $token == "not" || $token == "(" || $token == ")") )
			$facts[$i] = $token; 	
		$token = strtok("+");
		$i++;*/
		
		if($not_value == TRUE){
			$facts[$i] = $facts[$i].$token; 
			$not_value = false;
			$i++;	
		}
		else{
			if($token == "NOT" || $token == "not"){
				$not_value = TRUE;
				$facts[$i] = '!'; 	
			}	
			else{	
				if( !($token == "IF" || $token == "if" || $token == "OR" ||  $token == "or" || $token == "AND" || $token == "and" || $token == "THEN" || $token == "then" || $token == "(" || $token == ")") )
					$facts[$i] = $token; 
				$i++;
			}
		}
		$token = strtok("+");
    }
	
	$dependent_facts_logical_expression = "";
	foreach($facts as $fact){
		if(empty($dependent_facts_logical_expression))
			$dependent_facts_logical_expression = $fact;
		else
			$dependent_facts_logical_expression = $dependent_facts_logical_expression.".".$fact;
	}
	
	return $dependent_facts_logical_expression;
}

/*****************************************************************************
******Extract derived facts logical expression for rule***********************
******************************************************************************/
function extract_derived_facts_logical_expression($con, $rule_index)
{
	$short_rule = get_short_rule($con, $rule_index);
	
	$token = strtok($short_rule, "+");
	$facts = array();
	$i = 0;
	$temp = FALSE;
	$not_value = false;
  
	while ($token != false)
    {
		if($temp == FALSE){
			if($token == "THEN" || $token == "then")
				$temp = TRUE;
		}
		else{
			if($not_value == TRUE){
				$facts[$i] = $facts[$i].$token; 
				$not_value = false;
				$i++;	
			}
			else{
				if($token == "NOT" || $token == "not"){
					$not_value = TRUE;
					$facts[$i] = '!'; 	
				}	
				else{	
					if( !($token == "IF" || $token == "if" || $token == "OR" ||  $token == "or" || $token == "AND" || $token == "and" || $token == "THEN" || $token == "then" || $token == "(" || $token == ")") )
						$facts[$i] = $token; 
					$i++;
				}
			}	
		}
		$token = strtok("+");
    }
	
	$derived_facts_logical_expression = "";
	foreach($facts as $fact){
		if(empty($derived_facts_logical_expression))
			$derived_facts_logical_expression = $fact;
		else
			$derived_facts_logical_expression = $derived_facts_logical_expression.".".$fact;
	}
	
	return $dependent_facts_logical_expression;
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
					//echo "<br>extract_rhs_facts_from_rule: ".$rhs_fact;
					if($fact_index == $rhs_fact){
						$status = TRUE;		
						//echo "<br>this RHS fact: ".$rhs_fact." is derived";
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
					//echo "<br>extract_rhs_facts_from_rule:".$rhs_fact;
					if($fact_index == $rhs_fact){
						//echo "<br>extract_rhs_facts_from_rule:".$rhs_fact;
						$rhs_rules[$i] = $rule_index;
						$i++;		
					}	
				}
			}
		}
	}
	
	return $rhs_rules;
}

/**********************Functions for debugging purpose****************************/
function print_lhs_and_rhs_facts_in_a_rule($con, $rule_index)
{
	$lhs_facts = array();
	$lhs_facts = extract_lhs_facts_from_rule($con, $rule_index);
	
	$rhs_facts = array();
	$rhs_facts = extract_rhs_facts_from_rule($con, $rule_index);
	
	echo "<br><h3>for $rule_index :</h3>";
	echo "<h4> LHS facts are:</h4>";
	foreach ($lhs_facts as $lhs_fact) {
		echo $lhs_fact."<br>";
	}
	echo "<h4> RHS facts are:</h4>";
	foreach ($rhs_facts as $rhs_fact) {
		echo $rhs_fact."<br>";
	}
}	
	
	
	
mysqli_close($con);
?>