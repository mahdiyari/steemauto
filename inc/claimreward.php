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
				Here you can Enable or Disable claiming rewards automatically to your account balance.<br><br>
				<strong>More info:</strong> Usually you should redeem your steemit curation and author rewards by clicking on a button in your wallet in steemit.com<br>
				by this tool you don't need to click on that button manually! it will do that job for you automatically.<br>
				this tool will check your account every 5 minutes and will transfer your rewards to your balance.<br>
				and such as always, your balance is safe! (because of using posting authority)<br><br>
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
