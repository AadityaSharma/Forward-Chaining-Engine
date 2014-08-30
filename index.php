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
						<input type="hidden" name="source" value="index.php"/>
						<div class="card green button submit" style="width:21%;margin-left:1%;">
							<p>Refresh DB &#8635;</p>
						</div>
					</form>
					<form method="post" action="empty_db.php">
						<input type="hidden" name="source" value="index.php"/>
						<div class="card red button empty-db submit" style="width:21%;margin-left:1%;">
							<p>Empty DB &#8999;</p>
						</div>
					</form>	
				</div>
				<div class="clear"></div>
				<div class="container">
					<form method="post" action="facts.exec.php">
						<div class="card" style="width:78%;">
							<input type="text" style="text-transform:lowercase;" required="required" name="new_fact" placeholder="Add a new fact"/>
						</div>
						<div class="card green button submit" style="width:21%;margin-left:1%;">
							<p>Add this fact &#9166;</p>
						</div>
					</form>	
				</div>
				
				<div class="container">
					<h2 style="color:#ffffff;margin-bottom:0px;">&#9655; Facts List</h2>
					<div class="card" style="width:100%;padding:0;">
						<table>
							<tbody>
							<?php
								
								$query = "SELECT fact_index, fact FROM tbl_facts";
								$result = mysqli_query($con, $query);
								if(!$result)
									 die("Table not found");
								else{
									if($result->num_rows > 0){
							?>
								<tr>
									<td width="10%">Index</td>
									<td width="90%" class="no-border">Fact Name</td>
								</tr>
							<?php	
										while($row = mysqli_fetch_array($result))
										{
										  echo "<tr>
													<td style=\"text-align:center;\">".$row['fact_index']."</td>
													<td class=\"no-border\">".$row['fact']."</td>
												</tr>";
										}
									}
									else
										echo "<tr><td width=\"100%\">No Facts in the database</td></tr>";
								}
							
							?>
							</tbody>
						</table>
					</div>	
				</div>
			<?php include_once('footer.inc.php'); ?>