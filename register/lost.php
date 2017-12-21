<?php
require_once('../database.php');
require_once('../functions.php');

if(isset($_POST['username'])){
	$username = $_POST['username'];
	$data =[];
	if(!isset($_POST['g-recaptcha-response'])){
		$data['log'] = 5;
		header('Content-Type: application/json');
		echo json_encode($data);
		exit();
	}else{
		$res = $_POST['g-recaptcha-response'];
		$secret= 'Captcha secret';
		$ip = $_SERVER['REMOTE_ADDR'];
		$url = "https://www.google.com/recaptcha/api/siteverify";
		$dat = "secret=$secret&response=$res&remoteip=$ip";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dat);
		curl_setopt($ch, CURLOPT_POST, true);
		$result = json_decode(curl_exec($ch));
		if($result->success == false){
			$data['log'] = 5;
			header('Content-Type: application/json');
			echo json_encode($data);
			exit();
		}
	}
	if(!call('get_accounts','["'.$username.'"]')){// Validating Username
		$data['log'] = 0;
		header('Content-Type: application/json');
		echo json_encode($data);
		exit();
	}
	$stmt = $conn->prepare("SELECT EXISTS(SELECT * FROM `users` WHERE `user` = ?)");
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	foreach($row as $x){}
	if($x == 1){
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user` = ?");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$mail = $row['email'];
		$pw = $row['pw'];
		$msg = 'Hello '.$username.'<br>Someone or you requested to receive password.<br>Password: '.$pw.'<br><hr>This is an automatic email, don\'t reply to this email, we will not receive.';
		
		$email = urlencode($mail);
		$subject = urlencode('Your Requested Password');
		$message = urlencode($msg);
		$url="http://Email Server/?email=$email&message=$message&subject=$subject";
		$file = file_get_contents($url);
		if($file == 1){
			$data['log'] = 1;
			header('Content-Type: application/json');
			echo json_encode($data);
			exit();	
		}else{
			$data['log'] = 2;
			header('Content-Type: application/json');
			echo json_encode($data);
			exit();	
		}
	}else{
		$data['log'] = 4;
		header('Content-Type: application/json');
		echo json_encode($data);
		exit();
	}
}


require_once('../header.php');
?>
<script>
function lostpass(){
	$('.btn').attr('disabled','true');
	var user = document.getElementById('username').value;
	var captcha;
	if(grecaptcha.getResponse()){
		captcha = grecaptcha.getResponse();
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var y= JSON.parse(this.responseText);
				if(y['log'] == 1){
					document.getElementById('lresult').innerHTML = '<div class="alert alert-info"><strong>Success!</strong> Check your email inbox and <b>specially spam</b> box or try again after 15 minutes.</div>';
				}else if(y['log'] == 2){
					document.getElementById('lresult').innerHTML = '<div class="alert alert-info">Email server error! if you don\'t received any email, please report.</div>';
					setTimeout(function(){location.reload();},5000);
				}else if(y['log'] == 4){
					document.getElementById('lresult').innerHTML = '<div class="alert alert-danger">Your Account is not Registered</div>';
					setTimeout(function(){location.reload();},5000);
				}else if(y['log'] == 5){
					document.getElementById('lresult').innerHTML = '<div class="alert alert-danger">Recaptcha is not valid.</div>';
					setTimeout(function(){location.reload();},5000);
				}else if(y['log'] == 0){
					document.getElementById('lresult').innerHTML = '<div class="alert alert-danger">Username is not Valid!</div>';
					setTimeout(function(){location.reload();},5000);
				}else{
					document.getElementById('lresult').innerHTML = '<div class="alert alert-danger">Error! report.</div>';
					setTimeout(function(){location.reload();},5000);
				}	
			}
		};
		xmlhttp.open("POST", "lost.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("username="+user+"&g-recaptcha-response="+captcha);
	}else{
		document.getElementById('lresult').innerHTML = '<div class="alert alert-danger">Please Submit Recaptcha.</div>';
		$('.btn').removeAttr('disabled');
	}
	
	return 1;
}
</script>
<div class="content">
	<div class="col-md-3"></div>
	<div class="col-md-6">
		<div class="card">
			<div class="content">
				<h4>Lost Password:</h4>
				<form onsubmit="lostpass();return false;">
					<label>Username without @:</label>
					<input class="form-control" type="text" placeholder="Username" name="username" id="username" required>
					<div style="margin-top:5px;" class="g-recaptcha" data-sitekey="6LdgyjQUAAAAAOaxn89zmS4RmVgCrbJhkYa7THgV"></div>
					<script src="https://www.google.com/recaptcha/api.js"></script>
					<input type="submit" style="margin-top:5px;" value="Submit" class="btn btn-primary">
				</form>
				<div style="margin-top:5px;" id="lresult"></div>
			</div>
		</div>
	</div>
	<div class="col-md-3"></div>
</div>

<?php
require('../footer.php');
?>