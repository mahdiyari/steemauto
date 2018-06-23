<?php
// json based api file
// parameter i defines kind of api that is called

// i = 1  json array of curation trail for a steemuser
//        usage api.php?i=1&user=<steemuseraccountwithout@>
 

require_once('inc/conf/db.php');
date_default_timezone_set('UTC');

if(isset($_GET['i'])){
	$i = $_GET['i'];
	if($i == 1){ //// Curation Trail List
		include('inc/api/list.php');
	}
// room for addtional apis	
}else{
	echo "No Valid API method Selected";
}

?>	