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
						<input type="hidden" name="source" value="kb_table1.php"/>
						<div class="card green button submit" style="width:21%;margin-left:1%;">
							<p>Refresh DB &#8635;</p>
						</div>
					</form>
					<form method="post" action="empty_db.php">
						<input type="hidden" name="source" value="kb_table1.php"/>
						<div class="card red button empty-db submit" style="width:21%;margin-left:1%;">
							<p>Empty DB &#8999;</p>
						</div>
					</form>	
				</div>
				<div class="clear"></div>
				
				<div class="container">
					<h2 style="color:#ffffff;margin-bottom:0px;">&#9655; KB Table 1</h2>
					<div class="card" style="width:100%;padding:0;">
						<table>
							<tbody>
							<?php
								
								$query = "SELECT * FROM kb_table_1 ORDER BY fact_index";
								$result = mysqli_query($con, $query);
								if(!$result)
									 die("Table not found");
								else{
									if($result->num_rows > 0){
							?>
								<tr>
									<td width="">Index</td>
									<td width="">Fact</td>
									<td width="">Basic or Derived</td>
									<td width="">Level</td>
									<td width="">List of rules where fact appeared in LHS</td>
									<td width="">List of rules where fact appeared in RHS</td>
									<td width="" class="no-border">This fact is derived from</td>
								</tr>
							<?php	
										while($row = mysqli_fetch_array($result))
										{ ?>
										  <tr>
													<td style="text-align:center"><?php echo (!empty($row['fact_index']) ? $row['fact_index'] : '-'); ?></td>
													<td><?php echo (!empty($row['fact']) ? $row['fact'] : '-'); ?></td>
													<td><?php echo (!empty($row['is_derived']) ? 'Derived' : 'Basic'); ?></td>
													<td style="text-align:center"><?php echo (!empty($row['level']) ? $row['level'] : '-'); ?></td>
													<td><?php echo (!empty($row['rules_in_lhs']) ? $row['rules_in_lhs'] : '-'); ?></td>
													<td><?php echo (!empty($row['rules_in_rhs']) ? $row['rules_in_rhs'] : '-'); ?></td>
													<td><?php echo (!empty($row['derived_from']) ? $row['derived_from'] : '-'); ?></td>
												</tr>
							<?php		}
									}
									else
										echo "<tr><td width=\"100%\">KB Table 1 is empty</td></tr>";
								}
							
							?>
								
							</tbody>
						</table>
					</div>	
				</div>
			<?php include_once('footer.inc.php'); ?>