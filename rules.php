		<?php include_once('header.inc.php'); ?>
		<script>
			$(document).ready(function(){
				var windowH = $(window).height();
				var wrapperH = $('#wrap').height();
				var margin = (windowH)*0.05;
				var wrapH = (windowH)*0.9;
				
				$('#wrap').css('min-height', (wrapH)+'px');  
				$('#main').css('min-height', (wrapH)+'px');  
				var mainH = $('#main').height();
				$('aside').css('min-height', (mainH)+'px');	
				
				$(window).resize(function(){
					var windowH = $(window).height();
					var wrapperH = $('#wrap').height();
					var margin = (windowH)*0.05;
					var wrapH = (windowH)*0.9;
					
					$('#wrap').css('min-height', (wrapH)+'px');  
					$('#main').css('min-height', (wrapH)+'px');  
					var mainH = $('#main').height();
					$('aside').css('min-height', (mainH)+'px');	
				})
				
				//Other things to be done
				$('form .submit').click(function() {
				  $(this).parent().submit();	
				});
				/*$('.empty-db').click(function() {
				  alert('This will delete all facts, rules & KB Tables from database. Do you want to proceed?');
				});*/
				
				var new_rule_short = $("#new_rule_short").val();
				var new_rule = $("#new_rule").val();
				
				//For Keys to add rules in input box
				$('.key').click(function() {
					if($(this).attr('ref') == "CLEAR"){
						new_rule = '';
						new_rule_short = '';
					}
					else{
						if(new_rule != ''){
							new_rule = new_rule + ' ' + $(this).text();
							new_rule_short = new_rule_short + '+' + $(this).attr('ref');
						}	
						else{
							new_rule = new_rule + $(this).text();
							new_rule_short = new_rule_short + $(this).attr('ref');
						}	
					}
					$("#new_rule").val(new_rule);	
					$("#new_rule_short").val(new_rule_short);	
				});
			})	
		</script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
		
		<section id="wrap">
			<?php include_once('aside.inc.php'); ?>
			<section id="main">
				<div class="container">
					<div class="card" style="width:56%;cursor:default;">
						<p>Welcome to KDIS Forward Chaining Inference Engine!</p>
					</div>
					<form method="post" action="refresh_db.php">
						<input type="hidden" name="source" value="rules.php"/>
						<div class="card green button submit" style="width:21%;margin-left:1%;">
							<p>Refresh DB &#8635;</p>
						</div>
					</form>
					<form method="post" action="empty_db.php">
						<input type="hidden" name="source" value="rules.php"/>
						<div class="card red button empty-db submit" style="width:21%;margin-left:1%;">
							<p>Empty DB &#8999;</p>
						</div>
					</form>	
				</div>
				<div class="clear"></div>
				<div class="container">
					<form method="post" action="rules.exec.php">
						<div class="card" style="width:78%;">
							<input type="text" id="new_rule" value="" required="required" name="new_rule" placeholder="Add a new rule using buttons shown below"/>
							<input type="hidden" id="new_rule_short" value="" name="new_rule_short"/>
						</div>
						<div class="card green button submit" style="width:21%;margin-left:1%;">
							<p>Add this rule &#9166;</p>
						</div>
					</form>	
				</div>
				<div class="container">
					<div class="key" ref="IF">IF</div>
					<div class="key" ref="(">(</div>
					<div class="key" ref=")">)</div>
					<div class="key" ref="OR">OR</div>
					<div class="key" ref="AND">AND</div>
					<div class="key" ref="NOT">NOT</div>
					<div class="key" ref="THEN">THEN</div>
					<div class="key" ref="CLEAR" style="background-color:rgb(173, 83, 79);">CLEAR &#8999;</div>
				</div>
				<div class="container" style="margin-bottom:15px;">
							<?php
								
								$query = "SELECT fact_index, fact FROM tbl_facts";
								$result = mysqli_query($con, $query);
								if(!$result)
									 die("Table not found");
								else{
									if($result->num_rows > 0){
								
										while($row = mysqli_fetch_array($result))
										{
											//echo "<div class=\"key\">".$row['fact_index']."</div>";
											echo "<div class=\"key\" ref=\"".$row['fact_index']."\" style=\"text-transform:lowercase;\">".$row['fact']."</div>";
										}
									}
									else
										echo "<div class=\"card\" style=\"width:100%\">No Facts in the database</div>";
								}
							
							?>
				</div>
				<div class="clear"></div>
				<div class="container">
					<h2 style="color:#ffffff;margin-bottom:0px;">&#9655; Rules List</h2>
					<div class="card" style="width:100%;padding:0;">
						<table>
							<tbody>
							<?php
								
								$query = "SELECT rule_index, rule FROM tbl_rules";
								$result = mysqli_query($con, $query);
								if(!$result)
									 die("Table not found");
								else{
									if($result->num_rows > 0){
							?>
								<tr>
									<td width="10%">Index</td>
									<td width="90%" class="no-border">Rule Name</td>
								</tr>
							<?php	
										while($row = mysqli_fetch_array($result))
										{
										  echo "<tr>
													<td style=\"text-align:center;\">".$row['rule_index']."</td>
													<td class=\"no-border\">".$row['rule']."</td>
												</tr>";
										}
									}
									else
										echo "<tr><td width=\"100%\">No rules in the database</td></tr>";
								}
							
							?>
							</tbody>
						</table>
					</div>	
				</div>
			<?php include_once('footer.inc.php'); ?>