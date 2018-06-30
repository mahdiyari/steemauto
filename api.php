<?php
// json based api file
// parameter i defines kind of api that is called

// i = 1  json array of curation trail for a steemuser
//        usage api.php?i=1&user=<steemuseraccountwithout@>
// i = 2  json array of fanlist trail for a steemuser
//        usage api.php?i=2&user=<steemuseraccountwithout@> 

header('Access-Control-Allow-Origin: *');

require_once('inc/conf/db.php');
date_default_timezone_set('UTC');

if(isset($_GET['i'])){
	$i = $_GET['i'];
	if($i == 1){ //// Curation Trail List
		include('inc/api/trail_list.php');
	} elseif ($i == 2){
		include('inc/api/fan_list.php');
    }
	
// room for addtional apis	
}else{
	echo "No valid API method selected.";
}

?>	