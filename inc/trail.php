<?php

if(isset($_GET['p']) && is_numeric($_GET['p']) && $_GET['p'] >0){
	$page = $_GET['p'];
}else{
	$page =0;
}
$mysqlpage = 20*$page;

if(isset($_GET['trail']) && $_GET['trail'] != ''){
	$searchtrail = 1;
}else{
	$searchtrail = 0;
}
?>


<div class="content"> <!-- 1 -->

<? 	if($searchtrail == 1){	?>

		<div class="row" style="margin:0 !important"> <!-- 2 -->
			<div class="col-md-3"></div>
				<div class="col-md-6"> <!-- 3 -->
					<div class="card"> <!-- 4 -->
						<div class="content"> <!-- 5 -->
							<h3>Searching for trail: </h3><br>
							<? 
							$stmt = $conn->prepare("SELECT EXISTS(SELECT * FROM `trailers` WHERE `user`=?)");
							$searchedtrail = $_GET['trail'];
							$stmt->bind_param('s', $searchedtrail);
							$stmt->execute();
							$result = $stmt->get_result();
							$row = $result->fetch_assoc();
							foreach($row as $exists){}
							if($exists == 1){
								$stmt = $conn->prepare("SELECT * FROM `trailers` WHERE `user`=?");
								$stmt->bind_param('s', $searchedtrail);
								$stmt->execute();
								$result = $stmt->get_result();
								$row = $result->fetch_assoc();
								$resultt = $conn->query("SELECT EXISTS(SELECT * FROM `followers` WHERE `follower` = '$name' AND `trailer`='$searchedtrail')");
								foreach($resultt as $y){
									foreach($y as $y){}
								}
								if($y == 1){
									$alreadyfollowed = 1;
								}else{
									$alreadyfollowed = 0;
								}
								
								?>
								<strong>Trail name:</strong><span> <? echo htmlspecialchars($searchedtrail); ?></span><br>
								<strong>Description:</strong><span> <? echo htmlspecialchars($row['description']); ?></span><br>
								<strong>Followers:</strong><span> <? echo $row['followers']; ?> (<a href="/dash.php?i=15&id=1&user=<? echo htmlspecialchars($searchedtrail); ?>">Show enable followers</a>)</span><br><br>
								<? if($alreadyfollowed){ ?>
									<button onclick="if(confirm('Are you sure?')){unfollow('<? echo $row['user']; ?>');};" class="btn btn-danger">UNFOLLOW</button>
									<button onclick="showset('1');" class="btn btn-primary">Settings</button>
									<? 
									$resultt = $conn->query("SELECT * FROM `followers` WHERE `follower` = '$name' AND `trailer`='$searchedtrail'");
									foreach($resultt as $n){}
									?>
									<!-- Settings -->
								<div class="row" style="margin:0 !important;">
									<div style="text-align:left; display:none; padding:20px;" id="set1" class="col-md-12">
										<form onsubmit="settings('<? echo $row['user']; ?>'); return false;">
												<label>Settings for Trailer: <a href="https://steemit.com/@<? echo $row['user']; ?>" target="_blank">@<? echo $row['user']; ?></a></label>
												<div id="setweight<? echo $row['user']; ?>" <? if($n['fcurator'] == 1){echo 'style="display:none;"';} ?>><label>Weight: Default Weight is 100%. leave it empty to be default.</label>
											  <input id="weight<? echo $row['user']; ?>" placeholder="Voting Weight" name="weight" type="number" class="form-control" value="<? echo $n['weight']/100; ?>" step="0.01" min="0" max="100">
											  </div>
											 <li style="margin-top:5px; margin-bottom:5px;" class="list-group-item">
												Follow Curator Weight:
												<div class="material-switch pull-right">
													<input id="fcurator<? echo $row['user']; ?>" name="fcurator" class="fcurator" type="checkbox" <? if($n['fcurator'] == 1){echo 'checked';} ?>/>
													<label for="fcurator<? echo $row['user']; ?>" id="fcurator1" class="label-primary"></label>
												</div>
											</li>
											  <label>Time to wait before voting. Default Time is 0 minutes.</label>
											  <input id="aftermin<? echo $row['user']; ?>" value="<? echo $n['aftermin']; ?>" placeholder="Upvoting After X Minutes." name="aftermin" type="number" class="form-control" step="1" min="0" max="30">
												<li style="margin-top:5px; margin-bottom:5px;" class="list-group-item">
												Enabled:
													<div class="material-switch pull-right">
														<input id="enable<? echo $row['user']; ?>" name="enable" class="enable" type="checkbox" <? if($n['enable'] == 1){echo 'checked';} ?>/>
														<label for="enable<? echo $row['user']; ?>" id="enable" class="label-success"></label>
													</div>
												</li>
											  <input style="margin-top:10px;"value="Save Settings" type="submit" class="btn btn-primary">
										 </form>
									 </div>
								</div>
								<script>
									$(document).ready(function() {
										if(document.getElementById('fcurator<? echo $row['user']; ?>').checked){
											  $('#setweight<? echo $row['user']; ?>').hide(500);
										  }else{
											   $('#setweight<? echo $row['user']; ?>').show(500);
										  } 
										$('#fcurator<? echo $row['user']; ?>').change(function() {
											if(document.getElementById('fcurator<? echo $row['user']; ?>').checked){
												  $('#setweight<? echo $row['user']; ?>').hide(500);
											  }else{
												   $('#setweight<? echo $row['user']; ?>').show(500);
											  }      
										});
									});
								</script>
								<!-- /Settings -->
									
								<? }else{ ?>
									<button onclick="if(confirm('Are you sure?')){follow('<? echo $row['user']; ?>');};" class="btn btn-primary">FOLLOW</button>
								<? } ?>
								
								
								
								<?	
							}else{ ?>
								<p style="color:red;">Can't find. That Trail should register on steemauto and become a trail.</p>
							<?
							}
							?>
						</div> <!-- /5 -->
					</div> <!-- /4 -->
				</div> <!-- /3 -->
			<div class="col-md-3"></div>
		</div> <!-- /2 -->
		
		<?
	}else{ ?>
<div class="row" style="margin:0 !important"> <!-- 2 -->
<div class="col-md-3"></div>
<div class="col-md-6"> <!-- 3 -->
<div class="card"> <!-- 4 -->
<div class="content"> <!-- 5 -->
<h3>Welcome <? echo $name; ?>,</h3><br>
Here you can see a List of Curation Trailers and follow them.<br>
By following a Trail, you will upvote automatically each posts that trail will upvote.<br>
or, you can: <a style="margin:5px;" class="btn btn-success" onclick="showbecome();">become/edit your Trailer</a>
<form style="display:none;" id="become" onsubmit="become(); return false;">
<label>Short Description:(max 100 character)</label>
<textarea id="description" placeholder="For example: I'm voting only Good posts." name="description" type="text" class="form-control" required>
</textarea>
<input style="margin-top:10px;"value="Submit" type="submit" class="btn btn-primary">
</form>
</div> <!-- /5 -->
</div> <!-- /4 -->
</div> <!-- /3 -->
<div class="col-md-3"></div>
</div> <!-- /2 -->

<div class="row" style="margin:0 !important"> <!-- 6 -->

	<div class="col-md-12"> <!-- 7 -->
		<div class="card"> <!-- card -->
			<div class="content"> <!-- content -->


				<h3 style="border-bottom:1px solid #000; padding-bottom:10px;">You Are following:</h3>

				<div style="max-height:600px; overflow:auto;" class="table-responsive-vertical shadow-z-1"> <!-- 8 -->

					<? 
					$result = $conn->query("SELECT EXISTS(SELECT * FROM `trailers`)");
					foreach($result as $x){
						foreach($x as $x){}
					}
					if($x == 0){
						echo 'None';
					}else{
						$result = $conn->query("SELECT EXISTS(SELECT * FROM `followers` WHERE `follower`= '$name')");
						foreach($result as $y){
							foreach($y as $y){}
						}
						if($y == 1){
							?>


					<!-- Table starts here -->
						<table id="table" class="table table-hover table-mc-light-blue">
						  <thead>
							<tr>
							  <th>#</th>
							  <th>Username</th>
							  <th>Description</th>
							  <th>Followers</th>
							  <th>Weight</th>
							  <th>Wait Time</th>
							  <th>Status</th>
							  <th>Action</th>
							</tr>
						  </thead>
						  <tbody>
								
								<?
								$result = $conn->query("SELECT * FROM `followers` WHERE `follower` = '$name'");
								$k = 1;
								$enb;
								foreach($result as $n){
									$nn = $n['trailer'];
									$result = $conn->query("SELECT * FROM `trailers` WHERE `user` = '$nn'");
									foreach($result as $b){
										if($n['fcurator'] == 1){
											$w = 'Auto <abbr data-toggle="tooltip" title="You will Follow Curator Upvote Weight.">?</abbr>';
											$fc = 1;
											if($n['enable'] == 0){
												$status = '<b style="color:red;">Disabled <abbr data-toggle="tooltip" title="if it is Auto Disabled, You don\'t have enough Steem Power to follow Curator upvote Weight, enter a weight manually to Enable.">?</abbr></b>';
												$enb =0;
											}else{
												$status = '<b style="color:green;">Enabled</b>';
												$enb =1;
											}
										}else{
											$w = ($n['weight']/100).'%';
											$fc = 0;
											if($n['enable'] == 0){
												$status = '<b style="color:red;">Disabled <abbr data-toggle="tooltip" title="if it is Auto Disabled, Voting Weight is Too Small. Increase Voting Weight to Enable.">?</abbr></b>';
												$enb =0;
											}else{
												$status = '<b style="color:green;">Enabled</b>';
												$enb =1;
											}
										}
							?>

								<tr class="tr1">
									<td data-title="ID"><? echo $k; ?></td>
									<td data-title="Name"><a href="/dash.php?i=1&trail=<? echo $b['user']; ?>" target="_blank">@<? echo $b['user']; ?></a></td>
									<td data-title="Link"><? echo substr(strip_tags($b['description']),0,100); ?></td>
									<td data-title="Status"><? echo $b['followers']; ?></td>
									<td data-title="Status"><? echo $w; ?></td>
									<td data-title="Status"><? echo $n['aftermin']; ?> min</td>
									<td data-title="Status"><? echo $status; ?></td>

									<td data-title="Status">
									<button onclick="showset('<? echo $k; ?>');" class="btn btn-primary">Settings</button>
									<button onclick="if(confirm('Are you sure?')){unfollow('<? echo $b['user']; ?>');};" class="btn btn-danger">UNFOLLOW</button>
									</td> 
								</tr>	
								<!-- Settings -->
								<div class="row" style="margin:0 !important;">
									<div class="col-md-3"></div>
									<div style="text-align:left; display:none; padding:20px;" id="set<? echo $k; ?>" class="col-md-6">
										<form onsubmit="settings('<? echo $b['user']; ?>'); return false;">
												<label>Settings for Trailer: <a href="https://steemit.com/@<? echo $b['user']; ?>" target="_blank">@<? echo $b['user']; ?></a></label>
												<div id="setweight<? echo $b['user']; ?>" <? if($fc == 1){echo 'style="display:none;"';} ?>><label>Weight: Default Weight is 100%. leave it empty to be default.</label>
											  <input id="weight<? echo $b['user']; ?>" placeholder="Voting Weight" name="weight" type="number" class="form-control" value="<? echo $n['weight']/100; ?>" step="0.01" min="0" max="100">
											  </div>
											 <li style="margin-top:5px; margin-bottom:5px;" class="list-group-item">
												Follow Curator Weight:
												<div class="material-switch pull-right">
													<input id="fcurator<? echo $b['user']; ?>" name="fcurator" class="fcurator" type="checkbox" <? if($fc == 1){echo 'checked';} ?>/>
													<label for="fcurator<? echo $b['user']; ?>" id="fcurator1" class="label-primary"></label>
												</div>
											</li>
											  <label>Time to wait before voting. Default Time is 0 minutes.</label>
											  <input id="aftermin<? echo $b['user']; ?>" value="<? echo $n['aftermin']; ?>" placeholder="Upvoting After X Minutes." name="aftermin" type="number" class="form-control" step="1" min="0" max="30">
												<li style="margin-top:5px; margin-bottom:5px;" class="list-group-item">
												Enabled:
													<div class="material-switch pull-right">
														<input id="enable<? echo $b['user']; ?>" name="enable" class="enable" type="checkbox" <? if($enb == 1){echo 'checked';} ?>/>
														<label for="enable<? echo $b['user']; ?>" id="enable" class="label-success"></label>
													</div>
												</li>
											  <input style="margin-top:10px;"value="Save Settings" type="submit" class="btn btn-primary">
										 </form>
									 </div>
									<div class="col-md-3"></div>
								</div>
								<script>
									$(document).ready(function() {
										if(document.getElementById('fcurator<? echo $b['user']; ?>').checked){
											  $('#setweight<? echo $b['user']; ?>').hide(500);
										  }else{
											   $('#setweight<? echo $b['user']; ?>').show(500);
										  } 
										$('#fcurator<? echo $b['user']; ?>').change(function() {
											if(document.getElementById('fcurator<? echo $b['user']; ?>').checked){
												  $('#setweight<? echo $b['user']; ?>').hide(500);
											  }else{
												   $('#setweight<? echo $b['user']; ?>').show(500);
											  }      
										});
									});
								</script>
								<!-- /Settings -->		

								<?
								$k += 1;	
								}
								}
								?>
							</tbody>
						</table>
						<?
						}else{
							echo 'None.';
						}
					}
					?>
				</div> <!-- /8 -->
			</div> <!-- /content -->
		</div> <!-- /card -->
	</div> <!-- /7 -->
</div> <!-- /6 -->

<div class="row">
	<div class="col-md-3"></div>
	<div class="col-md-6">
		<div class="card">
			<div class="content">
				<h3>Search for a trail:</h3>
				<hr style="margin:10px;">
				<form action="/dash.php" class="form" method="GET">
					<label for="trail">Trail name:</label><input class="form-control" id="trail" placeholder="steemauto" name="trail" type="text" required/>
					<input name="i" type="number" value="1" style="display:none;" required>
					<input style="margin-top:7px;" class="btn btn-primary" name="submit" value="Search" type="submit"/>
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-3"></div>
</div>

<div class="row" style="margin:0 !important"> <!-- 9 -->
	<div class="col-md-12"> <!-- 10 -->
		<div class="card"> <!-- card -->
			<div class="content"> <!-- content -->
				<!-- -->	
				<h3 style="border-bottom:1px solid #000; padding-bottom:10px;">Top Trailers: </h3>

				<? 
				$result = $conn->query("SELECT EXISTS(SELECT * FROM `trailers` ORDER BY `trailers`.`followers` DESC LIMIT $mysqlpage,20)");
				foreach($result as $x){
					foreach($x as $x){}
				}
				if($x == 0){
					echo 'None';
				}else{
					$result = $conn->query("SELECT EXISTS(SELECT * FROM `followers` WHERE `follower` = '$name')");
					foreach($result as $y){
						foreach($y as $y){}
					}
					$rrr = 0;
					if($y == 1){
						$result = $conn->query("SELECT `trailer` FROM `followers` WHERE `follower` = '$name'");
						$r = 0;
						foreach($result as $y){
							foreach($y as $y){
								$uze[$r]=$y;
								$r = $r+ 1;
								$rrr = 1;
							}
						}
					}
					?>

				<div style="max-height:600px; overflow:auto;" class="table-responsive-vertical shadow-z-1">
				  <!-- Table starts here -->
				  
					<table id="table" class="table table-hover table-mc-light-blue">
						<thead>
							<tr>
								<th>#</th>
								<th>Username</th>
								<th>Description</th>
								<th>Followers</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $mysqlpage+1;
							$result = $conn->query("SELECT * FROM `trailers` ORDER BY `trailers`.`followers` DESC LIMIT $mysqlpage,20");
								foreach($result as $x){
									$s = 0;
									if($rrr = 1){
										foreach($uze as $u){
											if($u == $x['user']){
												$s = 1;
											}
										}
									}
							?>
							<tr class="tr2">
								<td data-title="ID"><? echo $i; ?></td>
								<td data-title="Name"><a href="/dash.php?i=1&trail=<? echo $x['user']; ?>" target="_blank">@<? echo $x['user']; ?></a></td>
								<td data-title="Link"><? echo substr(strip_tags($x['description']),0,100); ?></td>
								<td data-title="Status"><? echo $x['followers']; ?></td>
								<? if($x['user']!=$name && $s ==0){ ?>
								<td data-title="Status">
									<button onclick="if(confirm('Are you sure?')){follow('<? echo $x['user']; ?>');};" class="btn btn-primary">FOLLOW</button>
								</td> 
								<? }elseif($s == 1){ ?>
								<td data-title="Status">
									<button onclick="if(confirm('Are you sure?')){unfollow('<? echo $x['user']; ?>');};" class="btn btn-danger">UNFOLLOW</button>
								</td> 
								<? }else{ ?>
								<td data-title="Status">

								</td>
								<? } ?>
							</tr>
									<?
									$i += 1;
								}
								?>
							  </tbody>
						</table>
						<div class="col-md-12" style="text-align:center;">
							<? if($page>0){ ?> <a class="btn btn-primary" href="/dash.php?i=1">First page</a>
								<a class="btn btn-primary" href="/dash.php?i=1&p=<? echo $page-1; ?>">Previous page</a> <? } ?>
							<a class="btn btn-primary" href="/dash.php?i=1&p=<? echo $page+1; ?>">Next page</a>
						</div>
					</div>
				<? } ?>
			</div><!-- /content -->
		</div><!-- /card -->
	</div><!-- /10 -->

</div><!-- /9 -->

	<? } ?>
</div><!-- /1 -->
