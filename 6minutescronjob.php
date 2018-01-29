<?php
// a Cronjob for Fanbase and Scheduled Posts
require_once('database.php');
require_once('functions.php');
date_default_timezone_set('UTC');


$now = strtotime('now');
$result = $conn->query("SELECT EXISTS(SELECT * FROM `posts` WHERE `date`<'$now')");
foreach($result as $x){
	foreach($x as $x){}
}
if($x ==1){ // Checking Posts and Publishing in Steem Blockchain
	$wif = 'Posting Private Key';
	$result = $conn->query("SELECT * FROM `posts` WHERE `date`<'$now'");
	foreach($result as $post){
		$author = $post['user'];
		$title = urlencode($post['title']);
		$content = urlencode($post['content']);
		$json = urlencode($post['json']);
		$permlink = urlencode($post['permlink']);
		$parentAuthor = '';
		$parentPermlink = $post['maintag'];
		$url = "http://Posting Server/?id=1&wif=$wif&parentAuthor=$parentAuthor&parentPermlink=$parentPermlink&author=$author&permlink=$permlink&title=$title&body=$content&jsonMetadata=$json";
		$file = file_get_contents($url);
		if($file == 1){
			$id = $post['id'];
			$result = $conn->query("DELETE FROM `posts` WHERE `posts`.`id` = '$id'");
		}else{
		    print_r($file);
		}
	}
}


?>
