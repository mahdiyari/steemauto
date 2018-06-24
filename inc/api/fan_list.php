<?php

if(isset($_GET['user']) && $_GET['user'] !=''){
	$followed = $_GET['user'];	
	$stmt = $conn->prepare("SELECT `follower`,`weight` FROM `fanbase` WHERE `fan`=? AND `enable`=1");
	$stmt->bind_param('s', $followed);
	$stmt->execute();
	$result = $stmt->get_result();
	$followers = $result->fetch_all(MYSQLI_ASSOC);
	echo json_encode($followers);
}

?>