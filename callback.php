<?php
if(!isset($_GET['access_token']) || !isset($_GET['expires_in']) || $_GET['access_token'] == '' || $_GET['expires_in'] == ''){
	header("Content-type:application/json",true,401);
	die('{"error":"access_token or expires_in missing"}');
}else{
	$loginUrl = 'https://steemauto.com/api/v1/login';
	$postBody = [
		'access_token' => $_GET['access_token']
	];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $loginUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postBody));
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds 
	$result = curl_exec($ch);
	if (json_decode($result)->id == 1) {
		setcookie('access_key', json_decode($result)->access_key, time() + (86400 * 7), "/", NULL, NULL, TRUE); // 86400 = 1 day
		setcookie('username', json_decode($result)->username, time() + (86400 * 7), "/", NULL, NULL, TRUE); // 86400 = 1 day
		header("Location:/dash.php",true,301);
	} else {
		header("Content-type:application/json",true,401);
		die('{"error":"access_token is wrong"}');
	}
}

?>
