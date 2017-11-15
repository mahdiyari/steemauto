<?php
require_once('functions.php');
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
	}else{
		$a = 0;
		$active = 1;
	}
}
if($a == 3 && isset($_POST['user'])){
	$userr =$_POST['user'];
	
	if(isset($_COOKIE['luser']) && isset($_COOKIE['lpw'])){ // Following a Trailer
		require_once('d_b.php');
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user` = ?");
		$stmt->bind_param('s', $name);
		$name = $_COOKIE['luser'];
		$pw = $_COOKIE['lpw'];
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if($row['pw'] == $pw){
			$log = 1;
		}else{
			$log = 0;
		}
		if($log == 1){
			if($userr == $name){
				echo 0;exit();
			}
			$result = $conn->query("SELECT EXISTS(SELECT * FROM `followers` WHERE `trailer`='$userr' AND `follower`='$name')");
			foreach($result as $c){
				foreach($c as $c){}
			}
			if($c == 0){
				$result = $conn->query("INSERT INTO `followers`(`trailer`, `follower`, `weight`) VALUES ('$userr','$name','10000')");
				$result = $conn->query("UPDATE `trailers` SET `followers`=`followers`+1 WHERE `user`='$userr'");
				echo 1;
				exit();
			}else{
				echo 2;
				exit();
			}
		}else{
			echo 0;
			exit();
		}
	}else{
		exit();
	}
}
if($a == 4 && isset($_POST['user'])){ // Unfollowing a Trailer
	$userr =$_POST['user'];
	
	if(isset($_COOKIE['luser']) && isset($_COOKIE['lpw'])){
		require_once('d_b.php');
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user` = ?");
		$stmt->bind_param('s', $name);
		$name = $_COOKIE['luser'];
		$pw = $_COOKIE['lpw'];
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if($row['pw'] == $pw){
			$log = 1;
		}else{
			$log = 0;
		}
		if($log == 1){
			if($userr == $name){
				echo 0;exit();
			}
			$result = $conn->query("SELECT EXISTS(SELECT * FROM `followers` WHERE `trailer`='$userr' AND `follower`='$name')");
			foreach($result as $c){
				foreach($c as $c){}
			}
			if($c == 1){
				$result = $conn->query("DELETE FROM `followers` WHERE `followers`.`trailer`='$userr' AND `followers`.`follower`='$name'");
				$result = $conn->query("UPDATE `trailers` SET `followers`=`followers`-1 WHERE `user`='$userr'");
				echo 1;
				exit();
			}else{
				echo 2;
				exit();
			}
		}else{
			echo 0;
			exit();
		}
	}else{
		exit();
	}
}
if($a == 5 && isset($_POST['user']) && isset($_POST['weight']) && isset($_POST['minute'])){ // Settings For a Trailer
	$userr =$_POST['user'];
	$minute =$_POST['minute'];
	$weight =$_POST['weight'];
	$fcurator =$_POST['fcurator'];
	if($fcurator == 1){
		$fcurator = 1;
	}else{
		$fcurator = 0;
	}
	if(!is_numeric($minute) || !is_numeric($weight)){
		echo 0;
		exit();
	}
	if($minute > 30 || $minute < 0 || $weight < 0.01 || $weight > 100){
		echo 0;
		exit();
	}
	$weight = 100*$weight;
	if(isset($_COOKIE['luser']) && isset($_COOKIE['lpw'])){
		require_once('d_b.php');
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user` = ?");
		$stmt->bind_param('s', $name);
		$name = $_COOKIE['luser'];
		$pw = $_COOKIE['lpw'];
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if($row['pw'] == $pw){
			$log = 1;
		}else{
			$log = 0;
		}
		if($log == 1){
			$result = $conn->query("SELECT EXISTS(SELECT * FROM `followers` WHERE `trailer`='$userr' AND `follower`='$name')");
			foreach($result as $c){
				foreach($c as $c){}
			}
			if($c == 1){
				$result = $conn->query("UPDATE `followers` SET `weight`='$weight' , `aftermin`='$minute',`fcurator`='$fcurator' WHERE `trailer`='$userr' AND `follower`='$name'");
				echo 1;
				exit();
			}else{
				echo 2;
				exit();
			}
		}else{
			echo 0;
			exit();
		}
	}else{
		echo 3;
		exit();
	}
}
if($a == 6 && isset($_POST['desc'])){ // Becoming a Trailer
	$desc =$_POST['desc'];
	if(isset($_COOKIE['luser']) && isset($_COOKIE['lpw'])){
		require_once('d_b.php');
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user` = ?");
		$stmt->bind_param('s', $name);
		$name = $_COOKIE['luser'];
		$pw = $_COOKIE['lpw'];
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if($row['pw'] == $pw){
			$log = 1;
		}else{
			$log = 0;
		}
		if($log == 1){
			$result = $conn->query("SELECT EXISTS(SELECT * FROM `trailers` WHERE `user`='$name')");
			foreach($result as $c){
				foreach($c as $c){}
			}
			if($c == 1){
				$stmt = $conn->prepare("UPDATE `trailers` SET `description`=? WHERE `user`='$name'");
				$stmt->bind_param('s', $desc);
				$stmt->execute();
				echo 1;
				exit();
			}else{
				$stmt = $conn->prepare("INSERT INTO `trailers`(`user`, `description`) VALUES (?,?)");
				$stmt->bind_param('ss', $name,$desc);
				$stmt->execute();
				echo 1;
				exit();
			}
		}else{
			echo 0;
			exit();
		}
	}else{echo 3;exit();}
}
if($a == 7 && isset($_POST['user'])){ // Following a Fan
	$userr =$_POST['user'];
	if(isset($_COOKIE['luser']) && isset($_COOKIE['lpw'])){
		require_once('d_b.php');
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user` = ?");
		$stmt->bind_param('s', $name);
		$name = $_COOKIE['luser'];
		$pw = $_COOKIE['lpw'];
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if($row['pw'] == $pw){
			$log = 1;
		}else{
			$log = 0;
		}
		if($log == 1){
			if($userr == $name){
				echo 0;exit();
			}
			$result = $conn->query("SELECT EXISTS(SELECT * FROM `fans` WHERE `fan`='$userr')");
			foreach($result as $c){
				foreach($c as $c){}
			}
			if($c == 1){
				$result = $conn->query("SELECT EXISTS(SELECT * FROM `fanbase` WHERE `fan`='$userr' AND `follower`='$name')");
				foreach($result as $c){
					foreach($c as $c){}
				}
				if($c == 1){
					echo 2; exit();
				}else{
					$stmt = $conn->prepare("INSERT INTO `fanbase`(`fan`, `follower`,`weight`) VALUES (?,?,'10000')");
					$stmt->bind_param('ss', $userr ,$name);
					$stmt->execute();
					$result = $conn->query("UPDATE `fans` SET `followers`=`followers`+1 WHERE `fan`='$userr'");
					echo 1;
					exit();
				}
				
			}else{
				$stmt = $conn->prepare("INSERT INTO `fans`(`fan`) VALUES (?)");
				$stmt->bind_param('s', $userr);
				$stmt->execute();
				$stmt = $conn->prepare("INSERT INTO `fanbase`(`fan`, `follower`,`weight`) VALUES (?,?,'10000')");
				$stmt->bind_param('ss', $userr,$name);
				$stmt->execute();
				$result = $conn->query("UPDATE `fans` SET `followers`=`followers`+1 WHERE `fan`='$userr'");
				echo 1;
				exit();
			}
		}else{echo 0;exit();}
	}else{echo 3;exit();}
}
if($a == 8 && isset($_POST['user'])){ // Unfollowing a Fan
	$userr =$_POST['user'];
	if(isset($_COOKIE['luser']) && isset($_COOKIE['lpw'])){
		require_once('d_b.php');
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user` = ?");
		$stmt->bind_param('s', $name);
		$name = $_COOKIE['luser'];
		$pw = $_COOKIE['lpw'];
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if($row['pw'] == $pw){
			$log = 1;
		}else{
			$log = 0;
		}
		if($log == 1){
			if($userr == $name){
				echo 0;exit();
			}
			$result = $conn->query("SELECT EXISTS(SELECT * FROM `fans` WHERE `fan`='$userr')");
			foreach($result as $c){
				foreach($c as $c){}
			}
			if($c == 1){
				$result = $conn->query("SELECT EXISTS(SELECT * FROM `fanbase` WHERE `fan`='$userr' AND `follower`='$name')");
				foreach($result as $c){
					foreach($c as $c){}
				}
				if($c == 1){
					$result = $conn->query("UPDATE `fans` SET `followers`=`followers`-1 WHERE `fan`='$userr'");
					$result = $conn->query("DELETE FROM `fanbase` WHERE `fanbase`.`fan`='$userr' AND `fanbase`.`follower`='$name'");
					echo 1;
					exit();
				}else{
					echo 2;
					exit();
				}
				
			}else{
				echo 3;
				exit();
			}
		}else{echo 0;exit();}
	}else{echo 4;exit();}
}
if($a == 9 && isset($_POST['user'])){ // Following a Fan by submitting form
	$userr =$_POST['user'];
	if(!call('get_accounts','["'.$userr.'"]')){
		echo 4; exit();
	}
	
	if(isset($_COOKIE['luser']) && isset($_COOKIE['lpw'])){
		require_once('d_b.php');
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user` = ?");
		$stmt->bind_param('s', $name);
		$name = $_COOKIE['luser'];
		$pw = $_COOKIE['lpw'];
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if($row['pw'] == $pw){
			$log = 1;
		}else{
			$log = 0;
		}
		if($log == 1){
			if($userr == $name){
				echo 0;exit();
			}
			$result = $conn->query("SELECT EXISTS(SELECT * FROM `fans` WHERE `fan`='$userr')");
			foreach($result as $c){
				foreach($c as $c){}
			}
			if($c == 1){
				$result = $conn->query("SELECT EXISTS(SELECT * FROM `fanbase` WHERE `fan`='$userr' AND `follower`='$name')");
				foreach($result as $c){
					foreach($c as $c){}
				}
				if($c == 1){
					echo 2; exit();
				}else{
					$userr =$_POST['user'];
					$stmt = $conn->prepare("INSERT INTO `fanbase`(`fan`, `follower`,`weight`) VALUES (?,?,'10000')");
					$stmt->bind_param('ss', $userr,$name);
					$stmt->execute();
					$result = $conn->query("UPDATE `fans` SET `followers`=`followers`+1 WHERE `fan`='$userr'");
					echo 1;
					exit();
				}
				
			}else{
				$stmt = $conn->prepare("INSERT INTO `fans`(`fan`) VALUES (?)");
				$stmt->bind_param('s', $userr);
				$stmt->execute();
				$stmt = $conn->prepare("INSERT INTO `fanbase`(`fan`, `follower`,`weight`) VALUES (?,?,'10000')");
				$stmt->bind_param('ss', $userr,$name);
				$stmt->execute();
				$result = $conn->query("UPDATE `fans` SET `followers`=`followers`+1 WHERE `fan`='$userr'");
				echo 1;
				exit();
			}
		}else{echo 0;exit();}
	}else{echo 3;exit();}
}
if($a == 10 && isset($_POST['user']) && isset($_POST['weight']) && isset($_POST['minute'])){ // Settings For a Fan.
	$userr =$_POST['user'];
	$minute =$_POST['minute'];
	$weight =$_POST['weight'];
	if(!is_numeric($minute) || !is_numeric($weight)){
		echo 0;
		exit();
	}
	if($minute > 30 || $minute < 0 || $weight < 0.01 || $weight > 100){
		echo 0;
		exit();
	}
	$weight = 100*$weight;
	if(isset($_COOKIE['luser']) && isset($_COOKIE['lpw'])){
		require_once('d_b.php');
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user` = ?");
		$stmt->bind_param('s', $name);
		$name = $_COOKIE['luser'];
		$pw = $_COOKIE['lpw'];
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if($row['pw'] == $pw){
			$log = 1;
		}else{
			$log = 0;
		}
		if($log == 1){
			$result = $conn->query("SELECT EXISTS(SELECT * FROM `fanbase` WHERE `fan`='$userr' AND `follower`='$name')");
			foreach($result as $c){
				foreach($c as $c){}
			}
			if($c == 1){
				$result = $conn->query("UPDATE `fanbase` SET `weight`='$weight' , `aftermin`='$minute' WHERE `fan`='$userr' AND `follower`='$name'");
				echo 1;
				exit();
			}else{
				echo 2;
				exit();
			}
		}else{
			echo 0;
			exit();
		}
	}else{
		echo 3;
		exit();
	}
}
if($a == 11 && isset($_POST['date']) && is_numeric($_POST['date']) && isset($_POST['title']) && isset($_POST['content']) && isset($_POST['tags'])){ // Submit Post for Schedule
	$date =$_POST['date'];
	$title =$_POST['title'];
	$content =$_POST['content'];
	$tags =$_POST['tags'];
	if($date <1 || $date >100){
		echo 0;
		exit();
	}
	$tagss = explode(" ", $tags); //converting tags to array
	if(sizeof($tagss) >5){
		echo 2;
		exit();
	}
	$main = $tagss[0];
	function clean($string) {
		$string = strtolower($string);
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
		return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
	}

	$tag = implode('","',$tagss);
	$tags = "[\"$tag\"]"; //converting tags to array inside string
	$json = '{"tags":'.$tags.',"links":[],"app":"steemauto/0.01","format":"markdown"}';
	$perm = clean($title);
	$time= strtotime('now');
	$permlink = $perm.'-'.$time;
	$date = $time + $date*3600;
	if(isset($_COOKIE['luser']) && isset($_COOKIE['lpw'])){
		require_once('d_b.php');
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user` = ?");
		$stmt->bind_param('s', $name);
		$name = $_COOKIE['luser'];
		$pw = $_COOKIE['lpw'];
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if($row['pw'] == $pw){
			$log = 1;
		}else{
			$log = 0;
		}
		if($log == 1){
			$stmt = $conn->prepare("INSERT INTO `posts`(`user`, `title`, `content`, `date`,`maintag`, `json`, `permlink`) VALUES (?,?,?,?,?,?,?)");
			$stmt->bind_param('sssssss', $name,$title,$content,$date,$main,$json,$permlink);
			$stmt->execute();
			echo 1;
			exit();
		}else{
			echo 0;
			exit();
		}
	}else{
		echo 3;
		exit();
	}
}
if($a == 12 && isset($_POST['id']) && is_numeric($_POST['id'])){ // Delete Post from Schedule
	$id =$_POST['id'];
	if($id < 1){
		echo 0;
		exit();
	}
	if(isset($_COOKIE['luser']) && isset($_COOKIE['lpw'])){
		require_once('d_b.php');
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user` = ?");
		$stmt->bind_param('s', $name);
		$name = $_COOKIE['luser'];
		$pw = $_COOKIE['lpw'];
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if($row['pw'] == $pw){
			$log = 1;
		}else{
			$log = 0;
		}
		if($log == 1){
			$result = $conn->query("SELECT EXISTS(SELECT * FROM `posts` WHERE `user`='$name' AND `id`='$id')");
			foreach($result as $x){
				foreach($x as $x){}
			}
			if($x == 1){
				$result = $conn->query("DELETE FROM `posts` WHERE `posts`.`id` = '$id'");
				echo 1;
				exit();
			}else{
				echo 0;
				exit();
			}
		}else{
			echo 0;
			exit();
		}
	}else{
		echo 3;
		exit();
	}
}

require_once('header.php');
require_once('functions.php');
if($log == 0){
	header("Location: register",true);
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


?>
<style>
<style>
.closebtn{margin-left:15px;color:#fff;font-weight:700;float:right;font-size:22px;line-height:20px;cursor:pointer;transition:.3s}.closebtn:hover{color:#000}#alert{opacity:1;transition:opacity 5s;width:300px;position:fixed;top:60px;right:10px}.tr1 td,.tr2 td{max-width:250px}.material-switch>input[type=checkbox]{display:none}.material-switch>label{cursor:pointer;height:0;position:relative;width:40px}.material-switch>label::after,.material-switch>label::before{content:'';margin-top:-8px;position:absolute}.material-switch>label::before{background:#000;box-shadow:inset 0 0 10px rgba(0,0,0,.5);border-radius:8px;height:16px;opacity:.3;transition:all .4s ease-in-out;width:40px}.material-switch>label::after{background:#fff;border-radius:16px;box-shadow:0 0 5px rgba(0,0,0,.3);height:24px;left:-4px;top:-4px;transition:all .3s ease-in-out;width:24px}.material-switch>input[type=checkbox]:checked+label::before{background:inherit;opacity:.5}.material-switch>input[type=checkbox]:checked+label::after{background:inherit;left:20px}
</style>
<script>
function follow(user){
	$('.btn').attr('disabled','true');
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Successfully Followed by 100% upvote weight! you can access this setting after reloading page."
				},{
					type: 'success',
					timer: 6000
				});
				location.reload();
			}else if(this.responseText == 2){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Already <b>Followed!</b>"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=3", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user);
	
	return 1;
}
function unfollow(user){
	$('.btn').attr('disabled','true');
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Successfully <b>Unfollowed!</b>"
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else if(this.responseText == 2){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Already <b>Unfollowed!</b>"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=4", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user);
	
	return 1;
}
var recent;
var recentl;
var ne;
function showset(i){
	ne = '#set'+i;
	if(recent != null && recent != ne){
		recentl.hide(500);
	}
	if(recent == ne){
		$('#set'+i).toggle(500);
	}else{
		$('#set'+i).toggle(500);
		recent = '#set'+i;
		recentl = $('#set'+i);
	}
}

function settings(user){
	$('.btn').attr('disabled','true');
	var minute = document.getElementById('aftermin'+user).value;
	var weight = document.getElementById('weight'+user).value;
	if(minute == '' || minute == null){
		minute = 0;
	}
	if(weight == '' || weight == null){
		weight = 100;
	}
	var fcurator;
	if(document.getElementById('fcurator'+user).checked){
		  fcurator = 1;
	  }else{
		   fcurator = 0;
	  } 
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Changes Successfully Saved."
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=5", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user+"&weight="+weight+"&minute="+minute+"&fcurator="+fcurator);
	
	return 1;
}
function showbecome(){
	$('#become').toggle(500);
}
function become(){
	$('.btn').attr('disabled','true');
	var desc = document.getElementById('description').value;
	if(desc == '' || desc == null){
		desc = 'none.';
	}
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Successfully Saved!"
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	desc = encodeURIComponent(desc);
	xmlhttp.open("POST", "dash.php?i=6", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("desc="+desc);
	
	return 1;
}


function follow1(user){
	$('.btn').attr('disabled','true');
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Successfully Followed by 100% upvote weight! you can access this setting after reloading page."
				},{
					type: 'success',
					timer: 6000
				});
				location.reload();
			}else if(this.responseText == 2){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Already Followed!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=7", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user);
	
	return 1;
}
function unfollow1(user){
	$('.btn').attr('disabled','true');
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Successfully Unfollowed!"
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else if(this.responseText == 2){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Already Unfollowed!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=8", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user);
	
	return 1;
}
function follow2(){
	$('.btn').attr('disabled','true');
	var user = document.getElementById('userx').value;
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Successfully Followed by 100% upvote weight! you can access this setting after reloading page."
				},{
					type: 'success',
					timer: 6000
				});
				location.reload();
			}else if(this.responseText == 2){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Already Followed!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else if(this.responseText == 4){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Incorrect Username!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=9", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user);
	
	return 1;
}
function settings1(user){
	$('.btn').attr('disabled','true');
	var minute = document.getElementById('aftermin'+user).value;
	var weight = document.getElementById('weight'+user).value;
	if(minute == '' || minute == null){
		minute = 0;
	}
	if(weight == '' || weight == null){
		weight = 100;
	}
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Changes Successfully Saved!"
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=10", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user+"&weight="+weight+"&minute="+minute);
	
	return 1;
}
function post(){
	$('.btn').attr('disabled','true');
	var date = encodeURIComponent(document.getElementById('date').value);
	var title = encodeURIComponent(document.getElementById('title').value);
	var content = encodeURIComponent(document.getElementById('content').value);
	var tags = document.getElementById('tags').value;
	var tagss = tags.split(' ');
	if(tagss.length > 5){
		document.getElementById('result').innerHTML = '<div class="alert alert-danger">Enter Only 5 Tags. Separated by Spaces.</div>';
		$('.btn').removeAttr('disabled');
	}else{
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				console.log(this.responseText);
				if(this.responseText == 1){	
					document.getElementById('result').innerHTML = '<div class="alert alert-success"><strong>Success!</strong> Wait...</div>';
					location.reload(); 
				}else if(this.responseText == 2){
					document.getElementById('result').innerHTML = '<div class="alert alert-danger">Enter Only 5 Tags. Separated by Spaces.</div>';
					$('.btn').removeAttr('disabled');
				}else{
					document.getElementById('result').innerHTML = '<div class="alert alert-danger">Error! Check Inputs or Report.</div>';
					$('.btn').removeAttr('disabled');
				}
				
			}
		};
		tags = encodeURIComponent(tags);
		xmlhttp.open("POST", "dash.php?i=11", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("date="+date+"&title="+title+"&content="+content+"&tags="+tags);
	}
	
	
	return 1;
}
function deletepost(id){
	$('.btn').attr('disabled','true');
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			console.log(this.responseText);
			if(this.responseText == 1){	
				document.getElementById('result').innerHTML = '<div class="alert alert-success"><strong>Removed.</strong> Wait...</div>';
				location.reload(); 
			}else{
				document.getElementById('result').innerHTML = '<div class="alert alert-danger">Error! Something went wrong.</div>';
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=12", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("id="+id);

	return 1;
}



</script>


<? if($auth == 0){ ?>

<div class="content">
<div class="card">
<div class="content">
<center>
<h3>Welcome <? echo $name; ?>,</h3>
<br>Please add @steemauto to your Account's posting auths by One of following Apps:<br>
<br><a class="btn btn-success" href="https://v2.steemconnect.com/authorize/@steemauto/?redirect_uri=https://steemauto.com/dash.php">SteemConnect</a> or <a class="btn btn-success" href="https://steemauto.com/auth/">SteemAuto</a>
<br><br>Both Are Secure.
<br>otherwise, You will not be able to Use Our site.
</center>
</div>
</div>
</div>

<? }else{ ?>
<? if($a ==0){ //Dashboard ?> 
<div class="content">
<div class="col-md-3"></div>
<center>
<div class="col-md-6">
<div class="card">
<div class="content">
<h3>Welcome <? echo $name; ?>,</h3>

<br>Please Choose One:<br>
<a href="dash.php?i=1" class="btn btn-primary">Curation Trail</a>
<a href="dash.php?i=2" class="btn btn-primary">Fan Base</a>
<a href="dash.php?i=11" class="btn btn-primary">Scheduled Posts</a><br><br>
<a href="/auth" class="btn btn-danger">UnAuthorize (Leaving SteemAuto)</a>


</div>
</div>
</div>
</center>

<div class="col-md-3"></div>
</div>

<? }elseif($a == 1){ //Curation Trail ?>



<div class="content"> <!-- 1 -->
<div class="row" style="margin:0 !important"> <!-- 2 -->
<div class="col-md-3"></div>
<div class="col-md-6"> <!-- 3 -->
<div class="card"> <!-- 4 -->
<div class="content"> <!-- 5 -->
<h3>Welcome <? echo $name; ?>,</h3><br>
Here you can see a List of Curation Trailers and follow them.<br>
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
	  <th>Action</th>
	</tr>
  </thead>
  <tbody>
		
		<?
		$result = $conn->query("SELECT * FROM `followers` WHERE `follower` = '$name'");
		$k = 1;
		foreach($result as $n){
			$nn = $n['trailer'];
			$result = $conn->query("SELECT * FROM `trailers` WHERE `user` = '$nn'");
			foreach($result as $b){
				if($n['fcurator'] == 1){
					$w = 'Auto <abbr data-toggle="tooltip" title="You will Follow Curator Upvote Weight.">?</abbr>';
					$fc = 1;
				}else{
					$w = ($n['weight']/100).'%';
					$fc = 0;
				}
	?>

		<tr class="tr1">
		  <td data-title="ID"><? echo $k; ?></td>
		  <td data-title="Name"><a href="https://steemit.com/@<? echo $b['user']; ?>" target="_blank">@<? echo $b['user']; ?></a></td>
		  <td data-title="Link"><? echo substr(strip_tags($b['description']),0,100); ?></td>
		  <td data-title="Status"><? echo $b['followers']; ?></td>
		  <td data-title="Status"><? echo $w; ?></td>
		  <td data-title="Status"><? echo $n['aftermin']; ?> min</td>

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
	<div id="setweight<? echo $b['user']; ?>" <? if($fc == 1){echo 'style="display:none;"';} ?>><label>Default Weight is 100%. leave it empty to be default.</label>
  <input id="weight<? echo $b['user']; ?>" placeholder="Voting Weight" name="weight" type="number" class="form-control" step="0.01" min="0" max="100">
  </div>
 <li style="margin-top:5px; margin-bottom:5px;" class="list-group-item">
	Follow Curator Weight:
	<div class="material-switch pull-right">
		<input id="fcurator<? echo $b['user']; ?>" name="fcurator" class="fcurator" type="checkbox" <? if($fc == 1){echo 'checked';} ?>/>
		<label for="fcurator<? echo $b['user']; ?>" id="fcurator1" class="label-primary"></label>
	</div>
</li>
  <label>Time to wait before voting. Default Time is 0 minutes.</label>
  <input id="aftermin<? echo $b['user']; ?>" placeholder="Upvoting After X Minutes." name="aftermin" type="number" class="form-control" step="1" min="0" max="30">
  <input style="margin-top:10px;"value="Save Settings" type="submit" class="btn btn-primary">
 </form></div>
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

<div class="row" style="margin:0 !important"> <!-- 9 -->
 
<div class="col-md-12"> <!-- 10 -->
<div class="card"> <!-- card -->
<div class="content"> <!-- content -->
	
<!-- -->	

<h3 style="border-bottom:1px solid #000; padding-bottom:10px;">Trailers:</h3>

<? 
$result = $conn->query("SELECT EXISTS(SELECT * FROM `trailers`)");
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
$i = 1;
$result = $conn->query("SELECT * FROM `trailers` ORDER BY `trailers`.`followers` DESC");
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
		  <td data-title="Name"><a href="https://steemit.com/@<? echo $x['user']; ?>" target="_blank">@<? echo $x['user']; ?></a></td>
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
	</div>

<? } ?>


</div><!-- /content -->
</div><!-- /card -->
</div><!-- /10 -->

</div><!-- /9 -->


</div><!-- /1 -->

<? }elseif($a == 2){ //Fanbase
?>
<div class="content"> <!-- Content -->
<div class="row" style="margin:0 !important;">
<div class="col-md-3"></div>
<div class="col-md-6">
<div class="card">
<div class="content">
<h3>Welcome <? echo $name; ?>,</h3><br>
Here you can see a List of Popular Authors and upvote them.<br>
Follow someone to auto upvote that user's posts.
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
	  <th>Action</th>
	</tr>
  </thead>
  <tbody>
		
		<?
		$result = $conn->query("SELECT * FROM `fanbase` WHERE `follower` = '$name'");
		$k = 1;
		foreach($result as $n){
			$nn = $n['fan'];
			$result = $conn->query("SELECT * FROM `fans` WHERE `fan` = '$nn'");
			foreach($result as $b){
	?>

		<tr class="tr1">
		  <td data-title="ID"><? echo $k; ?></td>
		  <td data-title="Name"><a href="https://steemit.com/@<? echo $b['fan']; ?>" target="_blank">@<? echo $b['fan']; ?></a></td>
		  <td data-title="Status"><? echo $b['followers']; ?></td>
		  <td data-title="Status"><? echo $n['weight']/100; ?>%</td>
		  <td data-title="Status"><? echo $n['aftermin']; ?> min</td>

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
			<label>Default Weight is 100%. leave it empty to be default.</label>
		  <input id="weight<? echo $b['fan']; ?>" placeholder="Voting Weight" name="weight" type="number" class="form-control" step="0.01" min="0" max="100">
		  <label>Time to wait before voting. Default Time is 0 minutes.</label>
		  <input id="aftermin<? echo $b['fan']; ?>" placeholder="Upvoting After X Minutes." name="aftermin" type="number" class="form-control" step="1" min="0" max="30">
		  <input style="margin-top:10px;"value="Save Settings" type="submit" class="btn btn-primary">
		 </form></div>
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
<!-- -->	
<div class="row" style="margin:0 !important;"> <!-- Row -->
<div class="col-md-12"> <!-- Col-12 -->
<div class="card"><!-- card -->
<div class="content"><!-- content -->
<h3 style="border-bottom:1px solid #000; padding-bottom:10px;">Top Fans:</h3>


<? 
$result = $conn->query("SELECT EXISTS(SELECT * FROM `fans`)");
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
$i = 1;
$result = $conn->query("SELECT * FROM `fans` ORDER BY `fans`.`followers` DESC");
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
		  <td data-title="Name"><a href="https://steemit.com/@<? echo $x['fan']; ?>" target="_blank">@<? echo $x['fan']; ?></a></td>
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
	</div>

<? } ?>


</div><!-- /contact -->
</div><!-- /card -->
</div><!-- /Col-12 -->
</div><!-- /Row -->


</div> <!-- /Content -->

	<?
}elseif($a == 11){ // Scheduled Posts
	?>

	<div class="content"> <!-- Content -->
	<div class="row" style="margin:0 !important">
	<div class="col-md-3"></div>
	<div style="" class="col-md-6">
	<div class="card">
	<div class="content">
	<h3>Welcome <? echo $name; ?>,</h3><br>
	Here you can add a Post to published in the future.
	
	<form style="display:;" id="post" onsubmit="post(); return false;">
	<label for="title">Title:</label>
	<input id="title" placeholder="Post Title" name="title" type="text" class="form-control" required>
	<label for="content">Content: (You can Write in Steemit.com and Copy Markdown or Raw Html Here.)</label>
	<textarea id="content" placeholder="Post Content" name="content" type="text" class="form-control" required></textarea>
	<label for="tags">Tags:</label>
	<input id="tags" placeholder="tag1 tag2 tag3 tag4 tag5" name="tags" type="text" class="form-control" required>
	<label style="margin-top:7px;" for="date">Publish After <select id="date" name="date" required>
	<option value="1">1</option> 
	<option value="2">2</option> 
	<option value="3">3</option> 
	<option value="4">4</option> 
	<option value="5">5</option> 
	<option value="6">6</option> 
	<option value="7">7</option> 
	<option value="8">8</option> 
	<option value="9">9</option> 
	<option value="10">10</option> 
	<option value="11">11</option> 
	<option value="12">12</option> 
	<option value="13">13</option> 
	<option value="14">14</option> 
	<option value="15">15</option> 
	<option value="16">16</option> 
	<option value="17">17</option> 
	<option value="18">18</option> 
	<option value="19">19</option> 
	<option value="20">20</option> 
	<option value="21">21</option> 
	<option value="22">22</option> 
	<option value="23">23</option> 
	<option value="24">24</option> 
	<option value="25">25</option> 
	<option value="26">26</option> 
	<option value="27">27</option> 
	<option value="28">28</option> 
	<option value="29">29</option> 
	<option value="30">30</option> 
	<option value="31">31</option> 
	<option value="32">32</option> 
	<option value="33">33</option> 
	<option value="34">34</option> 
	<option value="35">35</option> 
	<option value="36">36</option> 
	<option value="37">37</option> 
	<option value="38">38</option> 
	<option value="39">39</option> 
	<option value="40">40</option> 
	<option value="41">41</option> 
	<option value="42">42</option> 
	<option value="43">43</option> 
	<option value="44">44</option> 
	<option value="45">45</option> 
	<option value="46">46</option> 
	<option value="47">47</option> 
	<option value="48">48</option> 
	<option value="49">49</option> 
	<option value="50">50</option> 
	<option value="51">51</option> 
	<option value="52">52</option> 
	<option value="53">53</option> 
	<option value="54">54</option> 
	<option value="55">55</option> 
	<option value="56">56</option> 
	<option value="57">57</option> 
	<option value="58">58</option> 
	<option value="59">59</option> 
	<option value="60">60</option> 
	<option value="61">61</option> 
	<option value="62">62</option> 
	<option value="63">63</option> 
	<option value="64">64</option> 
	<option value="65">65</option> 
	<option value="66">66</option> 
	<option value="67">67</option> 
	<option value="68">68</option> 
	<option value="69">69</option> 
	<option value="70">70</option> 
	<option value="71">71</option> 
	<option value="72">72</option>
	<option value="73">73</option> 
	<option value="74">74</option> 
	<option value="75">75</option> 
	<option value="76">76</option> 
	<option value="77">77</option> 
	<option value="78">78</option> 
	<option value="79">79</option> 
	<option value="80">80</option> 
	<option value="81">81</option> 
	<option value="82">82</option> 
	<option value="83">83</option> 
	<option value="84">84</option> 
	<option value="85">85</option> 
	<option value="86">86</option> 
	<option value="87">87</option> 
	<option value="88">88</option> 
	<option value="89">89</option> 
	<option value="90">90</option> 
	<option value="91">91</option> 
	<option value="92">92</option> 
	<option value="93">93</option> 
	<option value="94">94</option> 
	<option value="95">95</option> 
	<option value="96">96</option> 
	<option value="97">97</option> 
	<option value="98">98</option> 
	<option value="99">99</option> 
	<option value="100">100</option>
	</select> Hours.</label><br>
	<input style="margin-top:10px;"value="Submit" type="submit" class="btn btn-primary">
	</form>
	<br>
	<div id="result"></div>
	</div>
	</div>
	</div>
	<div class="col-md-3"></div>
	</div>
	<div class="row" style="margin:0 !important">
	<div style="" class="col-md-12">
<?
date_default_timezone_set('UTC');
function after($x){
	$sec = $x - strtotime('now');
	if($sec > 60){
		if($sec > 3600){
			if($sec > 86400){
				$ago= round($sec/86400).' Days';
			}else{
				$ago= round($sec/3600).' Hours';
			}
		}else{
			$ago= round($sec/60).' Minutes';
		}
	}else{
		$ago= round($sec).' Seconds';
	}
	return $ago;
}
$result = $conn->query("SELECT EXISTS(SELECT * FROM `posts` WHERE `user`='$name')");
foreach($result as $x){
	foreach($x as $x){}
}
if($x == 1){
	

?>
<div class="card">
<div class="content">
<h3 style=" padding-bottom:10px;">Scheduled Posts:</h3>
<div style="max-height:600px; overflow:auto;" class="table-responsive-vertical shadow-z-1">
  <!-- Table starts here -->
  
<table id="table" class="table table-hover table-mc-light-blue">
  <thead>
	<tr>
	  <th>#</th>
	  <th>Title</th>
	  <th>Content</th>
	  <th>Publish Time</th>
	  <th>Action</th>
	</tr>
  </thead>
  <tbody>
<?
$i = 1;
$result = $conn->query("SELECT * FROM `posts` WHERE `user`='$name' ORDER BY `posts`.`date` ASC");
	foreach($result as $x){
	$now = strtotime('now');
?>
		<tr class="tr2">
			<td data-title="ID"><? echo $i; ?></td>
			<td data-title="Name"><? echo $x['title']; ?></td>
			<td data-title="Status"><textarea disabled="" height="50px"><? echo $x['content']; ?></textarea></td>
			<td data-title="Status">After <? echo after($x['date']); ?></td>
			<td data-title="Status"><button class="btn btn-danger" onclick="if(confirm('Are You Sure?')) deletepost('<? echo $x['id']; ?>');">DELETE</button></td>
		</tr>

		<?
		$i += 1;
	}
	?>
		  </tbody>
	</table>
	</div>
	
	
	</div>
	</div>
	
	
	<script>
	document.getElementById('date').value =1;
	document.getElementById('title').value="";
	document.getElementById('content').value='';
	document.getElementById('tags').value='';
	</script>
	
	</div>
	</div>
	</div> <!-- /Content -->
	<?
	
	
} 
 }else{
	 header("Location: /");
 } 


}

require('footer.php');
?>