<?php

if(isset($_GET['p']) && is_numeric($_GET['p']) && $_GET['p'] >0){
	$page = $_GET['p'];
}else{
	$page =0;
}
$mysqlpage = 20*$page;

if(isset($_GET['fan']) && $_GET['fan'] != ''){
	$searchfan = 1;
}else{
	$searchfan = 0;
}
?>
<div class="content"> <!-- Content -->
<? if($searchfan == 1){	?>
		<div class="row" style="margin:0 !important"> <!-- 2 -->
			<div class="col-md-3"></div>
				<div class="col-md-6"> <!-- 3 -->
					<div class="card"> <!-- 4 -->
						<div class="content"> <!-- 5 -->
							<h3>Searching for fan: </h3><br>
							<? 
							$stmt = $conn->prepare("SELECT EXISTS(SELECT * FROM `fans` WHERE `fan`=?)");
							$searchedfan = $_GET['fan'];
							$stmt->bind_param('s', $searchedfan);
							$stmt->execute();
							$result = $stmt->get_result();
							$row = $result->fetch_assoc();
							foreach($row as $exists){}
							if($exists == 1){
								$stmt = $conn->prepare("SELECT * FROM `fans` WHERE `fan`=?");
								$stmt->bind_param('s', $searchedfan);
								$stmt->execute();
								$result = $stmt->get_result();
								$row = $result->fetch_assoc();
								$resultt = $conn->query("SELECT EXISTS(SELECT * FROM `fanbase` WHERE `follower` = '$name' AND `fan`='$searchedfan')");
								foreach($resultt as $y){
									foreach($y as $y){}
								}
								if($y == 1){
									$alreadyfollowed = 1;
								}else{
									$alreadyfollowed = 0;
								}
								
								?>
								<strong>Fan name:</strong><span> <? echo htmlspecialchars($searchedfan); ?></span><br>
								<strong>Followers:</strong><span> <? echo $row['followers']; ?> (<a href="/dash.php?i=15&id=2&user=<? echo htmlspecialchars($searchedfan); ?>">Show enable followers</a>)</span><br><br>
								<? if($alreadyfollowed){ ?>
									<button onclick="if(confirm('Are you sure?')){unfollow1('<? echo $row['fan']; ?>');};" class="btn btn-danger" <? if($row['fan'] == $name){echo 'disabled="disabled"';} ?>>UNFOLLOW</button>
									<button onclick="showset('1');" class="btn btn-primary">Settings</button>
									<? 
									$resultt = $conn->query("SELECT * FROM `fanbase` WHERE `follower` = '$name' AND `fan`='$searchedfan'");
									foreach($resultt as $n){}
									?>
									<!-- Settings -->
									<div class="row" style="margin:0 !important;">
										<div style="text-align:left; display:none; padding:20px;" id="set1" class="col-md-6">
											<form onsubmit="settings1('<? echo $row['fan']; ?>'); return false;">
												<label>Settings for Fan: <a href="https://steemit.com/@<? echo $row['fan']; ?>" target="_blank">@<? echo $row['fan']; ?></a></label>
												<label>Weight: Default Weight is 100%. leave it empty to be default.</label>
												<input id="weight<? echo $row['fan']; ?>" placeholder="Voting Weight" value="<? echo $n['weight']/100; ?>" name="weight" type="number" class="form-control" step="0.01" min="0" max="100">
												<label>Time to wait before voting. Default Time is 0 minutes.</label>
												<input id="aftermin<? echo $row['fan']; ?>" placeholder="Upvoting After X Minutes." value="<? echo $n['aftermin']; ?>" name="aftermin" type="number" class="form-control" step="1" min="0" max="30">
												<li style="margin-top:5px; margin-bottom:5px;" class="list-group-item">
													Enabled:
													<div class="material-switch pull-right">
														<input id="enable<? echo $row['fan']; ?>" name="enable" class="enable" type="checkbox" <? if($n['enable'] == 1){echo 'checked';} ?>/>
														<label for="enable<? echo $row['fan']; ?>" id="enable" class="label-success"></label>
													</div>
												</li>
												<input style="margin-top:10px;"value="Save Settings" type="submit" class="btn btn-primary">
											</form>
										</div>
										<div class="col-md-3"></div>
									</div>
									<!-- /Settings -->	
										
								<? }else{ ?>
										<button onclick="if(confirm('Are you sure?')){follow1('<? echo $row['fan']; ?>');};" class="btn btn-primary" <? if($row['fan'] == $name){echo 'disabled="disabled"';} ?>>FOLLOW</button>
								<? } ?>
								
								
								
								<?	
							}else{ ?>
								<p style="color:red;">Can't find. First, someone should follow that fan.</p>
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

	<div class="row" style="margin:0 !important;">
		<div class="col-md-3"></div>
			<div class="col-md-6">
				<div class="card">
					<div class="content">
						<h3>Welcome <? echo $name; ?>,</h3><br>
						Here you can see a list of the most popular authors and upvote them.<br>						Follow someone to auto upvote that user's posts.
						<form style="display:;" id="become" onsubmit="follow2(); return false;">
							<label>Username:</label>
							<input id="userx" placeholder="For example: mahdiyari" name="username" type="text" class="form-control" required>
							<input style="margin-top:10px;"value="Follow" type="submit" class="btn btn-primary">
						</form>
					</div>
				</div>
			</div>
		<div class="col-md-3"></div>
	</div>

	<div class="row" style="margin:0 !important;"> <!-- Row -->
		<div class="col-md-12"> <!-- Col-12 -->
			<div class="card"><!-- card -->
				<div class="content"><!-- content -->
					<h3 style="border-bottom:1px solid #000; padding-bottom:10px;">You Are following:</h3>
					<div style="max-height:600px; overflow:auto;" class="table-responsive-vertical shadow-z-1">
						<? 
						$result = $conn->query("SELECT EXISTS(SELECT * FROM `fans`)");
						foreach($result as $x){
							foreach($x as $x){}
						}
						if($x == 0){
							echo 'None';
						}else{
							$result = $conn->query("SELECT EXISTS(SELECT * FROM `fanbase` WHERE `follower`= '$name')");
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
											<th>Followers</th>
											<th>Weight</th>
											<th>Wait Time</th>
											<th>Status</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										
										<?
										$result = $conn->query("SELECT * FROM `fanbase` WHERE `follower` = '$name'");
										$k = 1;
										$enb;
										foreach($result as $n){
											$nn = $n['fan'];
											if($n['enable'] == 1){
												$status = '<b style="color:green;">Enabled</b>';
												$enb = 1;
											}else{
												$status = '<b style="color:red;">Disabled <abbr data-toggle="tooltip" title="if it is Auto Disabled, Voting Weight is Too Small. Increase Voting Weight to Enable.">?</abbr></b>';
												$enb = 0;
											}
											$result = $conn->query("SELECT * FROM `fans` WHERE `fan` = '$nn'");
											foreach($result as $b){
												?>

												<tr class="tr1">
													<td data-title="ID"><? echo $k; ?></td>
													<td data-title="Name"><a href="/dash.php?i=2&fan=<? echo $b['fan']; ?>" target="_blank">@<? echo $b['fan']; ?></a></td>
													<td data-title="Status"><? echo $b['followers']; ?></td>
													<td data-title="Status"><? echo $n['weight']/100; ?>%</td>
													<td data-title="Status"><? echo $n['aftermin']; ?> min</td>
													<td data-title="Status"><? echo $status ?></td>
													<td data-title="Status">
														<button onclick="showset('<? echo $k; ?>');" class="btn btn-primary">Settings</button>
														<button onclick="if(confirm('Are you sure?')){unfollow1('<? echo $b['fan']; ?>');};" class="btn btn-danger">UNFOLLOW</button>
													</td> 
												</tr>
								
												<!-- Settings -->
												<div class="row" style="margin:0 !important;">
													<div class="col-md-3"></div>
													<div style="text-align:left; display:none; padding:20px;" id="set<? echo $k; ?>" class="col-md-6">
														<form onsubmit="settings1('<? echo $b['fan']; ?>'); return false;">
															<label>Settings for Fan: <a href="https://steemit.com/@<? echo $b['fan']; ?>" target="_blank">@<? echo $b['fan']; ?></a></label>
															<label>Weight: Default Weight is 100%. leave it empty to be default.</label>
															<input id="weight<? echo $b['fan']; ?>" placeholder="Voting Weight" value="<? echo $n['weight']/100; ?>" name="weight" type="number" class="form-control" step="0.01" min="0" max="100">
															<label>Time to wait before voting. Default Time is 0 minutes.</label>
															<input id="aftermin<? echo $b['fan']; ?>" placeholder="Upvoting After X Minutes." value="<? echo $n['aftermin']; ?>" name="aftermin" type="number" class="form-control" step="1" min="0" max="30">
															<li style="margin-top:5px; margin-bottom:5px;" class="list-group-item">
																Enabled:
																<div class="material-switch pull-right">
																	<input id="enable<? echo $b['fan']; ?>" name="enable" class="enable" type="checkbox" <? if($enb == 1){echo 'checked';} ?>/>
																	<label for="enable<? echo $b['fan']; ?>" id="enable" class="label-success"></label>
																</div>
															</li>
															<input style="margin-top:10px;"value="Save Settings" type="submit" class="btn btn-primary">
														</form>
													</div>
													<div class="col-md-3"></div>
												</div>
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
					</div> 
				</div><!-- /contact -->
			</div><!-- /card -->
		</div><!-- /Col-12 -->
	</div><!-- /Row -->	
	
	
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<div class="card">
				<div class="content">
					<h3>Search for a fan:</h3>
					<hr style="margin:10px;">
					<form action="/dash.php" class="form" method="GET">
						<label for="fan">Fan name:</label><input class="form-control" id="fan" placeholder="steemauto" name="fan" type="text" required/>
						<input name="i" type="number" value="2" style="display:none;" required>
						<input style="margin-top:7px;" class="btn btn-primary" value="Search" type="submit"/>
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-3"></div>
	</div>
	
	
	<!-- -->	
	<div class="row" style="margin:0 !important;"> <!-- Row -->
		<div class="col-md-12"> <!-- Col-12 -->
			<div class="card"><!-- card -->
				<div class="content"><!-- content -->
					<h3 style="border-bottom:1px solid #000; padding-bottom:10px;">Top Fans:</h3>
					<? 
					$result = $conn->query("SELECT EXISTS(SELECT * FROM `fans` ORDER BY `fans`.`followers` DESC LIMIT $mysqlpage,20)");
					foreach($result as $x){
						foreach($x as $x){}
					}
					if($x == 0){
						echo 'None';
					}else{
						$result = $conn->query("SELECT EXISTS(SELECT * FROM `fanbase` WHERE `follower` = '$name')");
						foreach($result as $y){
							foreach($y as $y){}
						}
						if($y == 1){
							$result = $conn->query("SELECT `fan` FROM `fanbase` WHERE `follower` = '$name'");
							$t = 0;
							foreach($result as $y){
								foreach($y as $y){
									$uze[$t]=$y;
									$t =+ 1;
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
										<th>Followers</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?
									$i = $mysqlpage+1;
									$result = $conn->query("SELECT * FROM `fans` ORDER BY `fans`.`followers` DESC LIMIT $mysqlpage,20");
									foreach($result as $x){
										$s = 0;
										if($t>0){
											foreach($uze as $u){
												if($u == $x['fan']){
													$s = 1;
												}
											}
										}
										?>
										<tr class="tr2">
											<td data-title="ID"><? echo $i; ?></td>
											<td data-title="Name"><a href="/dash.php?i=2&fan=<? echo $x['fan']; ?>" target="_blank">@<? echo $x['fan']; ?></a></td>
											<td data-title="Status"><? echo $x['followers']; ?></td>
											<? if($x['fan']!=$name && $s ==0){ ?>
												<td data-title="Status">
													<button onclick="if(confirm('Are you sure?')){follow1('<? echo $x['fan']; ?>');};" class="btn btn-primary">FOLLOW</button>
												</td> 
											<? }elseif($s == 1){ ?>
												<td data-title="Status">
													<button onclick="if(confirm('Are you sure?')){unfollow1('<? echo $x['fan']; ?>');};" class="btn btn-danger">UNFOLLOW</button>
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
								<? if($page>0){ ?> <a class="btn btn-primary" href="/dash.php?i=2">First page</a>
									<a class="btn btn-primary" href="/dash.php?i=2&p=<? echo $page-1; ?>">Previous page</a> <? } ?>
								<a class="btn btn-primary" href="/dash.php?i=2&p=<? echo $page+1; ?>">Next page</a>
							</div>
						</div>

				 <? } ?>


				</div><!-- /contact -->
			</div><!-- /card -->
		</div><!-- /Col-12 -->
	</div><!-- /Row -->
	<? } ?>
</div> <!-- /Content -->
