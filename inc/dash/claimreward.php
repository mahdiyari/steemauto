<?php
$result = $conn->query("SELECT `claimreward` FROM `users` WHERE `user`='$name'");
foreach($result as $x){
	foreach($x as $x){}
}
if($x == 1){
	$claimreward = 1;
}else{
	$claimreward = 0;
}
?>

	<div class="content"> <!-- Content -->
		<div class="col-md-3"></div>
		<div style="" class="col-md-6">
			<div class="card">
				<div class="content">
					<h3>Welcome <? echo $name; ?>,</h3><br>
					Here you can enable or disable SteemAuto from automatically claiming your rewards.<br><br>
					<strong>More info:</strong> Usually you redeem your Steemit curation and author rewards by clicking on a button in your wallet on steemit.com<br>
					This handy tool means you don't need to click on that button manually, since it does that job for you automatically!<br>
					Every 15 minutes this tool will check your account and will transfer your pending rewards to your balance.<br>
					This tool is safe to use because SteemAuto only has access to your posting authority. That means it can only claim your rewards and cannot access any of the funds in your wallet.<br><br>
					<strong>Status:</strong> 
					<? if($claimreward==0){ ?>
					<span style="color:red;">Disabled</span>
					<? }else{ ?>
					<span style="color:green;">Enabled</span> 
					<? } ?>
					<br>
					<? if($claimreward == 1){ ?>
					<button style="margin-top:5px;" class="btn btn-danger" onclick="disableclaimreward();">Click to Disable</button>
					<? }else{ ?>
					<button style="margin-top:5px;" class="btn btn-success" onclick="enableclaimreward();">Click to Enable</button>
					<? } ?>
				</div>
			</div>
		</div>
		<div class="col-md-3"></div>
	</div> <!-- /Content -->
