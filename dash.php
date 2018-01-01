<?php
require_once('func.php');
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
	}elseif($i ==14){ // Comment Auto Upvote set
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
if($a == 5 && isset($_POST['user']) && isset($_POST['weight']) && isset($_POST['minute']) && isset($_POST['enable'])){ // Settings For a Trailer
	$userr =$_POST['user'];
	$minute =$_POST['minute'];
	$weight =$_POST['weight'];
	$fcurator =$_POST['fcurator'];
	$enable =$_POST['enable'];
	if($fcurator == 1){
		$fcurator = 1;
	}else{
		$fcurator = 0;
	}
	if($enable == 1){
		$enable = 1;
	}else{
		$enable = 0;
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
				$result = $conn->query("UPDATE `followers` SET `weight`='$weight' , `aftermin`='$minute',`fcurator`='$fcurator',`enable`='$enable' WHERE `trailer`='$userr' AND `follower`='$name'");
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
if($a == 10 && isset($_POST['user']) && isset($_POST['weight']) && isset($_POST['minute']) && isset($_POST['enable'])){ // Settings For a Fan.
	$userr =$_POST['user'];
	$minute =$_POST['minute'];
	$weight =$_POST['weight'];
	$enable =$_POST['enable'];
	if($enable == 1){
		$enable = 1;
	}else{
		$enable = 0;
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
			$result = $conn->query("SELECT EXISTS(SELECT * FROM `fanbase` WHERE `fan`='$userr' AND `follower`='$name')");
			foreach($result as $c){
				foreach($c as $c){}
			}
			if($c == 1){
				$result = $conn->query("UPDATE `fanbase` SET `weight`='$weight' , `aftermin`='$minute',`enable`='$enable' WHERE `fan`='$userr' AND `follower`='$name'");
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

if($a == 13 && isset($_POST['user']) && isset($_POST['weight']) && isset($_POST['minute'])){ // Adding a user to Comment Upvote
	$userr =$_POST['user'];
	$weight =$_POST['weight']*100;
	$minute =$_POST['minute'];
	
	if(!call('get_accounts','["'.$userr.'"]')){
		echo 4; exit();
	}
	if(!is_numeric($weight) || !is_numeric($minute) || $weight <= 0 || $weight > 10000 || $minute < 0 || $minute > 30){
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
		if($userr == $name){echo 0; exit();}
		if($log == 1){
			$stmt = $conn->prepare("SELECT EXISTS(SELECT * FROM `commentupvote` WHERE `user`=? AND `commenter`=?)");
			$stmt->bind_param('ss', $name,$userr);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			foreach($row as $x){}
			if($x == 1){
				echo 2;
				exit();
			}else{
				$stmt = $conn->prepare("INSERT INTO `commentupvote`(`user`, `commenter`, `weight`, `aftermin`) VALUES (?,?,?,?)");
				$stmt->bind_param('ssss', $name,$userr,$weight,$minute);
				$stmt->execute();
				echo 1;
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

if($a == 133 && isset($_POST['user'])){ // Removing a user from comment upvote list
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
		if($userr == $name){echo 0; exit();}
		
		if($log == 1){
			$stmt = $conn->prepare("SELECT EXISTS(SELECT * FROM `commentupvote` WHERE `user`=? AND `commenter`=?)");
			$stmt->bind_param('ss', $name,$userr);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			foreach($row as $x){}
			if($x == 0){
				echo 2;
				exit();
			}else{
				$stmt = $conn->prepare("DELETE FROM `commentupvote` WHERE `commentupvote`.`user` =? AND `commenter`=?");
				$stmt->bind_param('ss', $name,$userr);
				$stmt->execute();
				echo 1;
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
if($a == 14 && isset($_POST['user']) && isset($_POST['weight']) && isset($_POST['minute']) && isset($_POST['enable'])){ // Changing Settings for Comment Upvote
	$userr =$_POST['user'];
	$weight =$_POST['weight']*100;
	$minute =$_POST['minute'];
	$enable =$_POST['enable'];
	
	if($enable == 1){
		$enable = 1;
	}else{
		$enable=0;
	}
	
	if(!call('get_accounts','["'.$userr.'"]')){
		echo 4; exit();
	}
	if(!is_numeric($weight) || !is_numeric($minute) || $weight <= 0 || $weight > 10000 || $minute < 0 || $minute > 30){
		echo 9;
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
		if($userr == $name){echo 0; exit();}
		
		if($log == 1){
			$stmt = $conn->prepare("SELECT EXISTS(SELECT * FROM `commentupvote` WHERE `user`=? AND `commenter`=?)");
			$stmt->bind_param('ss', $name,$userr);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			foreach($row as $x){}
			if($x == 0){
				echo 2;
				exit();
			}else{
				$stmt = $conn->prepare("UPDATE `commentupvote` SET `weight`=?,`aftermin`=?,`enable`=? WHERE `user`=? AND `commenter`=?");
				$stmt->bind_param('sssss', $weight,$minute,$enable,$name,$userr);
				$stmt->execute();
				echo 1;
				exit();
			}
		}else{
			echo 8;
			exit();
		}
	}else{
		echo 3;
		exit();
	}
}

if($a == 16 && isset($_POST['enable'])){ // Enablig claim rewards
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
			$stmt = $conn->prepare("UPDATE `users` SET `claimreward`=1 WHERE `user`=?");
			$stmt->bind_param('s', $name);
			$stmt->execute();
			echo 1;
			exit();
		}else{
			echo 8;
			exit();
		}
	}else{
		echo 3;
		exit();
	}
}
if($a == 16 && isset($_POST['disable'])){ // Disabling claim rewards
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
			$stmt = $conn->prepare("UPDATE `users` SET `claimreward`=0 WHERE `user`=?");
			$stmt->bind_param('s', $name);
			$stmt->execute();
			echo 1;
			exit();
		}else{
			echo 8;
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


include('inc/js.php'); // some css + js functions

if($auth == 0){ ?>

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
<a href="dash.php?i=2" class="btn btn-primary">Fan Base</a><br>
<a style="margin-top:5px;" href="dash.php?i=13" class="btn btn-primary">Upvote Comments</a>
<a style="margin-top:5px;" href="dash.php?i=11" class="btn btn-primary">Scheduled Posts</a><br>
<a style="margin-top:5px;" href="dash.php?i=16" class="btn btn-primary">Claim Rewards</a><br><br>
<a href="/auth" class="btn btn-danger">UnAuthorize (Leaving SteemAuto)</a>


</div>
</div>
</div>
</center>

<div class="col-md-3"></div>
</div>

<? 
}elseif($a == 1){ //Curation Trail
	include('inc/trail.php');
}elseif($a == 2){ //Fanbase
	include('inc/fanbase.php');
}elseif($a == 11){ // Scheduled Posts
	include('inc/scheduled.php');
}elseif($a == 13){ // Comment upvotes
	include('inc/commentupvote.php');
}elseif($a == 15){ // List of trailers and fanbase followers
	include('inc/list.php');
}elseif($a == 16){ // Claim Rewards
	include('inc/claimreward.php');
}else{
	header("Location: /");
} 


}

require('footer.php');
?>
