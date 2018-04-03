<?php

if(isset($_COOKIE['access_token'])){
	$access_token = $_COOKIE['access_token'];
	$x = 'https://steemconnect.com/api/me';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $x);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: $access_token"));
	$result = curl_exec($ch);
	if(!json_decode($result)->error && json_decode($result)->user){
		$name = json_decode($result)->user;
		$result = $conn->query("SELECT EXISTS(SELECT `user` FROM `users` WHERE `user`='$name')");
		foreach ($result as $key) {
			foreach ($key as $exists) {
				if(!$exists){
					$result = $conn->query("INSERT INTO `users`(`user`) VALUES ('$name')");
				}
			}
		}
		$log = 1;
	}else{
		$log = 0;
	}
}else{
	$log = 0;
}

?>
