<div class="content"> <!-- Content -->

<? 
if(isset($_GET['user']) && $_GET['user'] !='' && isset($_GET['id']) && is_numeric($_GET['id'])){
	$followed = $_GET['user'];
	$i = $_GET['id'];
	
	if(call('get_accounts','["'.$followed.'"]')){
		?>
		<div class="row" style="margin:0 !important">
			<div class="col-md-3"></div>
			<div style="" class="col-md-6">
				<div class="card">
					<div class="content">
						<h3>Welcome <? echo $name; ?>,</h3><br>
						Here is a list of the SteemAuto users who are part of @<? echo $followed; ?>'s <span style="color:red;"><? if($i == 1){echo 'Curation Trail';}else{echo 'Fanbase';} ?></span>.					</div>
				</div>
				</div>
			<div class="col-md-3"></div>
		</div>
		<div class="row" style="margin:0 !important">
			<div style="" class="col-md-12">
				<div class="card">
					<div class="content">	
						<h3 style=" padding-bottom:10px;">Followers:</h3>
		
		<?
		if($i == 1){ //Curation Trail
			$i = 1;
			$result = $conn->query("SELECT EXISTS(SELECT * FROM `followers` WHERE `trailer`='$followed' AND `enable`=1)");
			foreach($result as $x){
				foreach($x as $x){}
			}
			if($x == 1){
				$k = 1;
				?>
				
				<div style="max-height:600px; overflow:auto;" class="table-responsive-vertical shadow-z-1">
				  <!-- Table starts here --> 
				<table id="table" class="table table-hover table-mc-light-blue">
				  <thead>
					<tr>
					  <th>#</th>
					  <th>Follower</th>
					  <th>Weight</th>
					</tr>
				  </thead>
				  <tbody>
				
				<?
				$result = $conn->query("SELECT * FROM `followers` WHERE `trailer`='$followed' AND `enable`=1");
				foreach($result as $c){
					if($c['fcurator'] == 1){
						$weight = 'Auto';
					}else{
						$weight = ($c['weight']/100).'%';
					}
					?>
					
					
					<tr class="tr2">
						<td data-title="ID"><? echo $k; ?></td>
						<td data-title="Name"><a href="https://steemit.com/@<? echo $c['follower']; ?>">@<? echo $c['follower']; ?></a></td>
						<td data-title="Status"><? echo $weight; ?></td>
					</tr>
					
					
					
					<?
					$k = $k+1;
				}
				?>
					</tbody>
				</table>
				</div>
				<?
				
			}else{ // no followers
				echo 'None.';
			}
		}else{ // Fanbase
			$i = 0;
			$result = $conn->query("SELECT EXISTS(SELECT * FROM `fanbase` WHERE `fan`='$followed' AND `enable`=1)");
			foreach($result as $x){
				foreach($x as $x){}
			}
			if($x == 1){
				$k = 1;
				?>
				
				<div style="max-height:600px; overflow:auto;" class="table-responsive-vertical shadow-z-1">
				  <!-- Table starts here --> 
				<table id="table" class="table table-hover table-mc-light-blue">
				  <thead>
					<tr>
					  <th>#</th>
					  <th>Follower</th>
					  <th>Weight</th>
					</tr>
				  </thead>
				  <tbody>
				
				<?
				$result = $conn->query("SELECT * FROM `fanbase` WHERE `fan`='$followed' AND `enable`=1");
				foreach($result as $c){
					?>
					
					
					<tr class="tr2">
						<td data-title="ID"><? echo $k; ?></td>
						<td data-title="Name"><a href="https://steemit.com/@<? echo $c['follower']; ?>">@<? echo $c['follower']; ?></a></td>
						<td data-title="Status"><? echo ($c['weight']/100).'%'; ?></td>
					</tr>
					
					
					
					<?
					$k = $k+1;
				}
				?>
					</tbody>
				</table>
				</div>
				<?
				
			}else{ // no followers
				echo 'None.';
			}
		}
		?>
			</div>
		</div>
		
		
</div>
</div>
		<?
	}
}
 ?>
	
	


			

			  
		
	
</div> <!-- /Content -->
