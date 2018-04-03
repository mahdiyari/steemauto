<?php
if(!isset($_GET['access_token']) || !isset($_GET['expires_in']) || $_GET['access_token'] == '' || $_GET['expires_in'] == ''){
	header("Content-type:application/json",true,401);
	die('{"error":"access_token or expires_in Missing"}');
}else{
	setcookie('access_token',$_GET['access_token'], time() + (86400 * 7), "/"); // 86400 = 1 day
	header("Location:/dash.php",true,301);
}

?>
