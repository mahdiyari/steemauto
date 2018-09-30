<?php

require_once('inc/dep/func.php');
require_once('inc/conf/db.php');
require_once('inc/dep/login_register.php');
date_default_timezone_set('UTC');
$a = 0;
$active = 1;
if(isset($_GET['i'])){
	$i = $_GET['i'];
	if($i == 1){ //// Curation Trail
		$a =1;
		$active = 2;
	}elseif($i ==2){ //// Fan Base
		$a =2;
		$active = 3;
	}elseif($i ==3){// Following a Trailer
		$a =3;
	}elseif($i ==4){// Unfollowing a Trailer
		$a =4;
	}elseif($i ==5){// Settings For a Trailer
		$a =5;
	}elseif($i ==6){ // Becoming a Trailer
		$a =6;
	}elseif($i ==7){// Following a Fan
		$a =7;
	}elseif($i ==8){// Unfollowing a Fan
		$a =8;
	}elseif($i ==9){// Following a Fan by submitting form
		$a =9;
	}elseif($i ==10){// Settings For a Fan.
		$a =10;
	}elseif($i ==11){ //// Add Scheduled Posts
		$a =11;
		$active = 4;
	}elseif($i ==12){ // Delete Scheduled Posts
		$a =12;
	}elseif($i ==13){ // Comment Auto Upvote
		$a =13;
		$active = 5;
	}elseif($i ==14){ // Comment Auto Upvote settings
		$a =14;
	}elseif($i ==15){ // list of trailers and fanbase
		$a =15;
	}elseif($i ==133){ // Remove from comment list
		$a =133;
	}elseif($i ==16){ // Claim Rewards
		$a =16;
		$active = 6;
	}else{
		$a = 0;
		$active = 1;
	}
}

require_once('inc/temp/head.php');
require_once('inc/dep/func.php');
if($log == 0){
	echo 'You should login.<script type="text/javascript">window.location.href = "/register";</script>';
	header("Location: /register");
	exit();
}
$x = json_decode(call('get_accounts','["'.$name.'"]'));
$y = $x->result[0]->posting->account_auths;
$auth = 0;
foreach($y as $y){
    if($y[0] == 'steemauto'){
        $auth = 1;
		break;
    }
}
//changing power limit
if(isset($_POST['powerlimit']) && is_numeric($_POST['powerlimit']) && $_POST['powerlimit'] >= 1 && $_POST['powerlimit'] <= 99){
	$submittedpowerlimit = $_POST['powerlimit'];
	$stmt = $conn->prepare("UPDATE `users` SET `limit_power`=? WHERE `user`=?");
	$stmt->bind_param('ss', $submittedpowerlimit,$name);
	$stmt->execute();
	echo '<script>setTimeout(function(){$.notify({icon: "pe-7s-check",message: "Successfully saved."},{type: "success",timer: 6000});},1000);</script>';
}

include('inc/dash/js.php'); // some css + js functions

if($auth == 0){ ?>

<div class="content">
<div class="card">
<div class="content">
<center>
<h5 style="color:red;">Please leave Steemauto if you don't understand how it works or what it does. You could harm your Steem account if you change settings that you do not understand.</h5>
<h3>Welcome <? echo $name; ?>,</h3>
<br>Please add @steemauto to your account's posting auths using one of following apps:<br>
<br><a class="btn btn-success" href="https://steemconnect.com/authorize/@steemauto/?redirect_uri=https://steemauto.com/dash.php">SteemConnect (recommended)</a> or <a class="btn btn-success" href="https://steemauto.com/auth/">SteemAuto</a>
<br><br>Both are secure.
<br>If you don't add @steemauto to your posting auths you will not be able to use our site.
</center>
</div>
</div>
</div>

<? }else{ ?>
<? if($a ==0){ //Dashboard

$result = $conn->query("SELECT `current_power`, `limit_power` FROM `users` WHERE `user`='$name'");
foreach($result as $x){
	$powernow = $x['current_power'];
	$powerlimit = $x['limit_power'];
	if($powernow == 0){
		$powernow1 = 'Updating... (can take 5 minutes)';
	}
}
?>


<!-- dashboard menu -->
<div class="row" style="margin-top:50px;">
	<div class="col-md-3"></div>
	<center>
		<div class="col-md-6">
			<div class="content">
				<div class="card">
					<div class="content">
						<h5 style="color:red;">Please leave Steemauto if you don't understand how it works or what it does. You could harm your Steem account if you change settings that you do not understand.</h5>
						<h3>Welcome <? echo $name; ?>,</h3>

						<br>Please Choose One:<br>
						<a href="#settings" onclick="document.getElementById('settings').scrollIntoView();" class="btn btn-warning">Settings</a><br><br>
						<a href="dash.php?i=1" class="btn btn-primary">Curation Trail</a>
						<a href="dash.php?i=2" class="btn btn-primary">Fanbase</a><br>
						<a style="margin-top:5px;" href="dash.php?i=13" class="btn btn-primary">Upvote Comments</a>
						<a style="margin-top:5px;" href="dash.php?i=11" class="btn btn-primary">Schedule Posts</a><br>
						<a style="margin-top:5px;" href="dash.php?i=16" class="btn btn-primary">Claim Rewards</a><br><hr>
						<p>You can remove SteemAuto's access from your account by using SteemConnect</p>
						<a href="https://steemconnect.com/revoke/@steemauto" class="btn btn-danger">Unauthorize (Leave SteemAuto)</a>
					</div>
				</div>
			</div>
		</div>
	</center>
	<div class="col-md-3"></div>
</div>

<!-- settings -->
<div class="row" style="margin-bottom:5px; padding-bottom:5px;">
	<div class="col-md-3"></div>
	<div class="col-md-6">
		<div class="content">
			<div class="card" id="settings">
				<div class="content">
					<center>
						<h4 style="border-bottom:1px solid #000; padding-bottom:10px;">Settings</h4>
					</center>
					<strong>Upvoting status:</strong>
						<span id="upvoting_status"></span>
					<br>
					<strong>Voting power:</strong>
						<span id="voting_power"></span>
					<br>
					<script>
						function callNode(method, params, cb){
							const body = JSON.stringify({
								"jsonrpc": "2.0",
								"method": method,
								"params": [params],
								"id": 1
							})
							const xmlhttp = new XMLHttpRequest()
							xmlhttp.onreadystatechange = function() {
								if (this.readyState == 4 && this.status == 200) {
									cb(JSON.parse(this.responseText).result)
								}
							}
							xmlhttp.open('POST', 'https://api.steemit.com', true)
							xmlhttp.setRequestHeader('Content-type', 'application/json')
							xmlhttp.send(body)

							return 1
						}
						callNode('get_accounts',['<? echo $name; ?>'], function (result){
							var userAcc = result[0]
							var us = document.getElementById('upvoting_status')
							var vp = document.getElementById('voting_power')
							var delegated = parseInt(userAcc.delegated_vesting_shares.replace('VESTS', '')) // VESTS
							var received = parseInt(userAcc.received_vesting_shares.replace('VESTS', '')) // VESTS
							var vesting = parseInt(userAcc.vesting_shares.replace('VESTS', '')) // VESTS
							var withdrawRate = 0
							if (parseInt(userAcc.vesting_withdraw_rate.replace('VESTS', '')) > 0) {
								withdrawRate = Math.min(
									parseInt(userAcc.vesting_withdraw_rate.replace('VESTS', '')),
									parseInt((userAcc.to_withdraw - userAcc.withdrawn) / 1000000)
								)
							}
							var totalvest = vesting + received - delegated - withdrawRate
							var maxMana = Number(totalvest * Math.pow(10, 6))
							var delta = Date.now() / 1000 - userAcc.voting_manabar.last_update_time
							var current_mana = Number(userAcc.voting_manabar.current_mana) + (delta * maxMana / 432000)
							var percentage = Math.round(current_mana / maxMana * 10000)
							if (!isFinite(percentage)) percentage = 0
							if (percentage > 10000) percentage = 10000
							else if (percentage < 0) percentage = 0
							var percent = (percentage / 100).toFixed(2)
							vp.innerHTML = percent + '%'
							if (percent < <? echo $powerlimit; ?>) {
								us.innerHTML = 'Paused'
								us.style.color = 'red'
							} else {
								us.innerHTML = 'Normal'
								us.style.color = 'green'
							}
						})
						
					</script>
					<strong>Limit on Mana:</strong><span> <? echo $powerlimit; ?>% <a onclick="$('#limitpower').toggle(500)">(Click to edit)</a></span><br>
					<form id="limitpower" style="display:none;" onsubmit="if(!confirm('Are you sure?')) return false;" method="post">
						<label for="powerlimit">Mana limitation (%):</label>
						<input id="powerlimit" name="powerlimit" class="form-control" type="number" min="1" max="99" step="0.01" required>
						<input style="margin-top:5px;" type="submit" value="submit" class="btn btn-primary">
					</form><br>
					<p>All your upvotes will be paused if your Mana is lower than the Mana limitation.</p>
					<p>Read more about Mana in the Steemit FAQ.</p>
					<p>You can check your Mana here: <a href="https://steemd.com/@<? echo $name; ?>">https://steemd.com/@<? echo $name; ?></a></p>

				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3"></div>
</div>

<?
}elseif($a == 1){ //Curation Trail
	include('inc/dash/trail.php');
}elseif($a == 2){ //Fanbase
	include('inc/dash/fanbase.php');
}elseif($a == 11){ // Schedule Posts
	include('inc/dash/scheduled.php');
}elseif($a == 13){ // Comment upvotes
	include('inc/dash/commentupvote.php');
}elseif($a == 15){ // List of trailers and fanbase followers
	include('inc/dash/list.php');
}elseif($a == 16){ // Claim Rewards
	include('inc/dash/claimreward.php');
}else{
	header("Location: /");
}


}

require('inc/temp/footer.php');
?>
