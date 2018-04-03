<div class="content"> <!-- 1 -->
	<div class="row" style="margin:0 !important"> <!-- 2 -->
		<div class="col-md-3"></div>
		<div class="col-md-6"> <!-- 3 -->
			<div class="card"> <!-- 4 -->
				<div class="content"> <!-- 5 -->
					<h3>Welcome <? echo $name; ?>,</h3><br>
					This page lets you upvote an user's comments automatically.<br>
					Each user will get only one upvote on each post they comment on and a maximum of two upvotes per day (UTC), even if they write more comments.<br>
					Remember to configure the upvote weight and wait time.<br>
					<center><a class="btn btn-success" style="margin-top:8px;" onclick="$('#addusertolist').toggle(500);">Add a User to the List</a></center>
					<form style="display:none;" id="addusertolist" onsubmit="addusertolist();return false;">
					<hr>
					<h4>Fill the form and Click on Submit:</h4>
						<label>Commenter's Steemit Username without @:</label>
						<input id="username" placeholder="For example: mahdiyari" name="username" type="text" class="form-control" required/>
						<label>Upvote Weight (%):</label>
						<input id="weight" placeholder="For example: 50" name="weight" type="number" step="0.01" min="0.01" max="100" class="form-control" required/>
						<label>Wait time (minutes):</label>
						<input id="aftertime" placeholder="For example: 20" name="aftertime" type="number" step="1" min="0" max="30" class="form-control" required/>
						<input style="margin-top:10px;"value="Submit" type="submit" class="btn btn-primary">
					</form>
					<script>
					document.getElementById('username').value='';
					document.getElementById('aftertime').value='';
					document.getElementById('weight').value='';
					</script>
				</div> <!-- /5 -->
			</div> <!-- /4 -->
		</div> <!-- /3 -->
		<div class="col-md-3"></div>
	</div> <!-- /2 -->
	<?
	$stmt = $conn->prepare("SELECT EXISTS(SELECT * FROM `commentupvote` WHERE `user`=?)");
	$stmt->bind_param('s', $name);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	foreach($row as $x){}
	if($x == 1){
		?>
		<div class="row" style="margin:0 !important"> <!-- 2 -->
			<div class="col-md-12"> <!-- 3 -->
				<div class="card"> <!-- 4 -->
					<div class="content"> <!-- 5 -->
						<h3 style="border-bottom:1px solid #000; padding-bottom:10px;">You Are following:</h3>
						<div style="max-height:600px; overflow:auto;" class="table-responsive-vertical shadow-z-1"> <!-- 8 -->
							<!-- Table starts here -->
							<table id="table" class="table table-hover table-mc-light-blue">
							  <thead>
								<tr>
								  <th>#</th>
								  <th>Username</th>
								  <th>Weight</th>
								  <th>Wait Time</th>
								  <th>Status</th>
								  <th>Action</th>
								</tr>
							  </thead>
							  <tbody>
									<?
									$result = $conn->query("SELECT * FROM `commentupvote` WHERE `user`='$name'");
									$k = 1;
									foreach($result as $n){
										if($n['enable'] == 1){
											$fc = 1;
											$status = '<b style="color:green;">Enabled</b>';
										}else{
											$fc=0;
											$status = '<b style="color:red;">Disabled <abbr data-toggle="tooltip" title="if it is Auto Disabled, Voting Weight is Too Small. Increase Voting Weight to Enable.">?</abbr></b>';
										}
									?>
									<tr class="tr1">
									  <td data-title="ID"><? echo $k; ?></td>
									  <td data-title="Name"><a href="https://steemit.com/@<? echo $n['commenter']; ?>" target="_blank">@<? echo $n['commenter']; ?></a></td>
									  <td data-title="Status"><? echo ($n['weight']/100).'%'; ?></td>
									  <td data-title="Status"><? echo $n['aftermin']; ?> min</td>
									  <td data-title="Status"><? echo $status; ?></td>
									  
									  <td data-title="Status">
									  <button onclick="showset('<? echo $k; ?>');" class="btn btn-primary">Settings</button>
									  <button onclick="if(confirm('Are you sure?')){removeuserfromlist('<? echo $n['commenter']; ?>');};" class="btn btn-danger">REMOVE</button>
									  </td> 
									</tr>
									<!-- Settings -->
									<div class="row" style="margin:0 !important;">
										<div class="col-md-3"></div>
										<div style="text-align:left; display:none; padding:20px;" id="set<? echo $k; ?>" class="col-md-6">
											<form onsubmit="commentupvotesettings('<? echo $n['commenter']; ?>');return false;">
												<label>Settings for Commenter: <a href="https://steemit.com/@<? echo $n['commenter']; ?>" target="_blank">@<? echo $n['commenter']; ?></a></label>
												<br><label>Weight:</label>
												<input id="weight<? echo $n['commenter']; ?>" placeholder="Voting Weight" name="weight" type="number" class="form-control" value="<? echo $n['weight']/100; ?>" step="0.01" min="0.01" max="100" required/>
												<label>Time to wait before voting:</label>
												<input id="aftermin<? echo $n['commenter']; ?>" value="<? echo $n['aftermin']; ?>" placeholder="Upvoting After X Minutes." name="aftermin" type="number" class="form-control" step="1" min="0" max="30" required/>
												<li style="margin-top:5px; margin-bottom:5px;" class="list-group-item">
													Enabled:
													<div class="material-switch pull-right">
														<input id="enable<? echo $n['commenter']; ?>" name="enable" class="enable" type="checkbox" <? if($fc == 1){echo 'checked';} ?>/>
														<label for="enable<? echo $n['commenter']; ?>" id="enable" class="label-success"></label>
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
								?>
							</tbody>
							</table>
						</div>
					</div> <!-- /5 -->
				</div> <!-- /4 -->
			</div> <!-- /3 -->
		</div> <!-- /2 -->
	<?
	}
	?>
</div>
	