<?php
$active =0;
require_once('inc/conf/db.php');
require_once('inc/dep/login_register.php');
if($log){
	header("Location: /dash.php");
}else{
	include('templates/steemauto-design-02/index.html');
}
?>
