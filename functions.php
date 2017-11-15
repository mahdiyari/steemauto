<?php
require_once('node.php');

function call($x,$y){
	$data = '{"jsonrpc": "2.0", "method": "'.$x.'", "params": ['.$y.'], "id": 1}';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $GLOBALS['node']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$result = curl_exec($ch);
	$resultx = json_decode($result)->result;
	if($resultx == false || $resultx == null || $resultx == ''){
		return 0;
	}else{
		return $result;
	}
	curl_close($ch);
}
?> 
