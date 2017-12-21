<?php
require_once('../database.php');
require_once('../functions.php');

if(isset($_POST['pw']) && isset($_POST['user']) && isset($_POST['email'])){ //Registering New Password
	$data =[];
	$pw = $_POST['pw'];
	$email = $_POST['email'];
	$name = $_POST['user'];
	
	if(!call('get_accounts','["'.$name.'"]')){// Validating Username
		$data['reg'] = 0;
		header('Content-Type: application/json');
		echo json_encode($data);
		exit();
	}
	
	if($pw =='' || $pw ==null || $user =='' || $user ==null){
		$data['reg'] = 0;
		header('Content-Type: application/json');
		echo json_encode($data);
		exit();
	}

	$stmt = $conn->prepare("SELECT EXISTS(SELECT * FROM `users` WHERE `user` = ?)");
	$stmt->bind_param('s', $name);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	foreach($row as $x){}
	if($x == 0){
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
				.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
				.'0123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$rand = '';
		foreach (array_rand($seed, 15) as $k) $rand .= $seed[$k];
		$memo = $rand;
		$stmt = $conn->prepare("INSERT INTO `users`(`user`,`memo`,`pw`,`email`) VALUES (?,?,?,?)");
		$stmt->bind_param('ssss', $name,$memo,$pw,$email);
		$stmt->execute();
		
		$data['reg'] = 2;
		$data['memo'] = $memo;
		header('Content-Type: application/json');
		echo json_encode($data);
		exit();
	}elseif($x == 1){
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user` = ?");
		$stmt->bind_param('s', $name);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$x = $row['enable'];
		if($x == 0){
			$p = $row['memo'];
			$url = "https://TX server/?user=steemauto";
			$file = file_get_contents($url);
			$file = json_decode($file);
			$z = 0;
			foreach($file as $k){
				if($k->memo == $p && $k->transaction == 'Receive  0.001 SBD from '.$name){
					$z =1;
					$stmt = $conn->prepare("UPDATE `users` SET `pw`=?,`enable`='1',`email`=? WHERE `user`=?");
					$stmt->bind_param('sss', $pw,$email,$name);
					$stmt->execute();
					$data['reg'] = 1;
					header('Content-Type: application/json');
					echo json_encode($data);
					exit();
				}
			}
			if($z == 0){
				$data['reg'] = 2;
				$data['memo'] = $p;
				header('Content-Type: application/json');
				echo json_encode($data);
				exit();
			}
		}else{
			$data['reg'] = 3;
			header('Content-Type: application/json');
			echo json_encode($data);
			exit();
		}
	}
}elseif(isset($_POST['lpw']) && isset($_POST['luser'])){ //Login to system
	$data =[];
	$pw = $_POST['lpw'];
	$name = $_POST['luser'];
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
	if(!call('get_accounts','["'.$name.'"]')){// Validating Username
		$data['log'] = 0;
		header('Content-Type: application/json');
		echo json_encode($data);
		exit();
	}
	if($pw =='' || $pw ==null || $user =='' || $user ==null){
		$data['log'] = 0;
		header('Content-Type: application/json');
		echo json_encode($data);
		exit();
	}

	$stmt = $conn->prepare("SELECT EXISTS(SELECT * FROM `users` WHERE `user` = ?)");
	$stmt->bind_param('s', $name);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	foreach($row as $x){}
	if($x == 1){
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user` = ?");
		$stmt->bind_param('s', $name);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$x = $row['enable'];
		if($x == 0){
			
			$p = $row['memo'];
			$url = "https://Transaction Server/?user=steemauto";
			$file = file_get_contents($url);
			$file = json_decode($file);
			$z = 0;
			foreach($file as $k){
				if($k->memo == $p && $k->transaction == 'Receive  0.001 SBD from '.$name){
					$z =1;
					$stmt = $conn->prepare("UPDATE `users` SET `enable`='1' WHERE `user`=?");
					$stmt->bind_param('s', $name);
					$stmt->execute();
					$x = $row['pw'];
					if($x == $pw){
						$data['log'] = 1;
						header('Content-Type: application/json');
						echo json_encode($data);
						setcookie('luser', $name, time() + (86400 * 1), "/"); // 86400 = 1 day
						setcookie('lpw', $pw, time() + (86400 * 1), "/"); // 86400 = 1 day
						exit();
					}
				}
			}
			if($z == 0){
				$data['log'] = 2;
				header('Content-Type: application/json');
				echo json_encode($data);
				exit();
			}

		}else{
			$x = $row['pw'];
			if($x == $pw){
				$data['log'] = 1;
				header('Content-Type: application/json');
				echo json_encode($data);
				setcookie('luser', $name, time() + (86400 * 1), "/"); // 86400 = 1 day
				setcookie('lpw', $pw, time() + (86400 * 1), "/"); // 86400 = 1 day
				exit();
			}else{
				$data['log'] = 4;
				header('Content-Type: application/json');
				echo json_encode($data);
				exit();
			}
		}
	}else{
		$data['log'] = 3;
		header('Content-Type: application/json');
		echo json_encode($data);
		exit();
	}

}
require_once('../header.php');
?>
<script>
function reg(){
	$('.btn').attr('disabled','true');
	var pw = document.getElementById('pw').value;
	var pw2 = document.getElementById('pw2').value;
	if(pw != pw2){
		document.getElementById('result').innerHTML = '<div class="alert alert-danger">Password Confirmation is not Correct.</div>';
		$('.btn').removeAttr('disabled');
	}else{
		var user = document.getElementById('username').value;
		var email = document.getElementById('email').value;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var y= JSON.parse(this.responseText);
				if(y['reg'] == 1){
					document.getElementById('result').innerHTML = '<div class="alert alert-success"><strong>Success!</strong> Now You can Login.</div>';
				}else if(y['reg'] == 2){
					document.getElementById('result').innerHTML = '<div class="alert alert-info">Please Send <code>0.001 SBD</code> to Account <code>@steemauto</code> with memo <code>'+y["memo"]+'</code> Then Try again.</div>';
				}else if(y['reg'] == 3){
					document.getElementById('result').innerHTML = '<div class="alert alert-danger">User Already Registered.</div>';
				}else if(y['reg'] == 0){
					document.getElementById('result').innerHTML = '<div class="alert alert-danger">Username is not Valid!</div>';
				}else{
					document.getElementById('result').innerHTML = '<div class="alert alert-danger">Error! report.</div>';
				}
				
				$('.btn').removeAttr('disabled');
			}
		};
		xmlhttp.open("POST", "index.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("pw="+pw+"&user="+user+"&email="+email);
	}
	
	return 1;
}

function log(){
	$('.btn').attr('disabled','true');
	var pw = document.getElementById('lpw').value;
	var user = document.getElementById('lusername').value;
	var captcha;
	if(grecaptcha.getResponse()){
		captcha = grecaptcha.getResponse();
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var y= JSON.parse(this.responseText);
				if(y['log'] == 1){
					document.getElementById('lresult').innerHTML = '<div class="alert alert-success"><strong>Success!</strong> Please Wait...</div>';
					window.location = "/dash.php";
				}else if(y['log'] == 2){
					document.getElementById('lresult').innerHTML = '<div class="alert alert-info">Your Account is not Registered.</div>';
					setTimeout(function(){location.reload();},2000);
				}else if(y['log'] == 3){
					document.getElementById('lresult').innerHTML = '<div class="alert alert-danger">Please First Register.</div>';
					setTimeout(function(){location.reload();},2000);
				}else if(y['log'] == 4){
					document.getElementById('lresult').innerHTML = '<div class="alert alert-danger">Check your username and password.</div>';
					setTimeout(function(){location.reload();},2000);
				}else if(y['log'] == 5){
					document.getElementById('lresult').innerHTML = '<div class="alert alert-danger">Recaptcha is not valid.</div>';
					setTimeout(function(){location.reload();},2000);
				}else if(y['log'] == 0){
					document.getElementById('lresult').innerHTML = '<div class="alert alert-danger">Username is not Valid!</div>';
					setTimeout(function(){location.reload();},2000);
				}else{
					document.getElementById('lresult').innerHTML = '<div class="alert alert-danger">Error! report.</div>';
					setTimeout(function(){location.reload();},2000);
				}
				
				$('.btn').removeAttr('disabled');
			}
		};
		xmlhttp.open("POST", "index.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("lpw="+pw+"&luser="+user+"&g-recaptcha-response="+captcha);
	}else{
		document.getElementById('lresult').innerHTML = '<div class="alert alert-danger">Please Submit Recaptcha.</div>';
		$('.btn').removeAttr('disabled');
	}
	
	return 1;
}
</script>
<style>
input{
	margin-top:5px;
}
</style>
<?
if($log == 0){ //Register And Login
	?>
	<div class="content">
	
	<div class="col-md-6"> <!-- Register -->
	<div class="card">
	<div class="content">
	<div style="border:1px solid #000; padding:20px; padding-top:0px; text-align:left;">
	<center><h2 style="margin-bottom:15px;border-bottom:1px solid #000; padding-bottom:5px;">Register</h2></center>
	<form onsubmit="reg(); return false;">
	<div class="form-group">
	<input placeholder="Steemit Username without @" id="username" type="text" name="username" class="form-control" required />
	<input placeholder="Email" id="email" type="email" name="email" class="form-control" required />
	<input placeholder="Password" id="pw" type="password" name="password" class="form-control" required />
	<input placeholder="Confirm Password" id="pw2" type="password" name="password2" class="form-control" required />
	<input style="margin-top:5px;" class="btn btn-primary" type="submit" value="Register" />
	</div>
	</form>
	</div>
	<br>
	<div id="result"></div>
	</div>
	</div>
	</div>
	
	<div class="col-md-6"> <!-- Login -->
	<div class="card">
	<div class="content">
	<div style="border:1px solid #000; padding:20px; padding-top:0px; text-align:left;">
	<center><h2 style="margin-bottom:15px;border-bottom:1px solid #000; padding-bottom:5px;">Login</h2></center>
	
	<form onsubmit="log(); return false;">
	<div class="form-group">
	<input placeholder="Steemit Username without @" id="lusername" type="text" name="username" class="form-control" required />
	<input placeholder="Password" id="lpw" type="password" name="password" class="form-control" required />
	<div style="margin-top:5px;" class="g-recaptcha" data-sitekey="6LdgyjQUAAAAAOaxn89zmS4RmVgCrbJhkYa7THgV"></div>
	<script src="https://www.google.com/recaptcha/api.js"></script>
	<input style="margin-top:5px;" class="btn btn-primary" type="submit" value="Login" />
	</div>
	</form>
	<a href="lost.php">Lost Password?</a>
	</div>
	<br>
	<div id="lresult"></div>
	</div>
	</div>
	</div>
	
	</div>
	
	<?
}else{
	?>
	<script>window.location="/";</script>
	<?
}

require_once('../footer.php');
?>