<?php
// a Cronjob for Fanbase and Scheduled Posts
require_once('database.php');
require_once('functions.php');
date_default_timezone_set('UTC');

$result = $conn->query("SELECT EXISTS(SELECT * FROM `fanbase`)");
foreach($result as $c){
	foreach($c as $c){}
}
if($c ==1){ //Checking And Upvoting Fanbase
	$result = $conn->query("SELECT * FROM `fans` WHERE `followers`>0");
	foreach($result as $c){
		$fan = $c['fan'];
		$x = json_decode(call('get_account_history','"'.$fan.'", "-1", "20"'));
		$y = $x->result;
		for($i=count($x->result)-1;$i>=0;$i--){
			$timestampt = strtotime($y[$i][1]->timestamp);
			$now = strtotime('now');
			$dif = ($now - $timestampt)/60;
			if($dif < 35 && $y[$i][1]->op[0] == 'comment' && $y[$i][1]->op[1]->author ==$fan && ($y[$i][1]->op[1]->parent_author =='' ||$y[$i][1]->op[1]->parent_author ==null)){
				$permlink = $y[$i][1]->op[1]->permlink;
				$author = $fan;

				$b = call('get_content','"'.$author.'", "'.$permlink.'"');
				$ti=json_decode($b)->result->created;
				$tim = strtotime($ti);
				$created = ($now-$tim)/60;

				$result = $conn->query("SELECT EXISTS(SELECT * FROM `fanbase` WHERE `fan`='$fan')");
				foreach($result as $v){
					foreach($v as $v){}
				}
				if($v == 1){
					$result = $conn->query("SELECT * FROM `fanbase` WHERE `fan`='$fan'");
					foreach($result as $follower){
						if($follower['aftermin'] < $created){
							$weight = $follower['weight'];
							$wif = 'Posting Private Key';
							$followerr = $follower['follower'];
							$url='http://Upvote Server/?u='.$followerr.'&id=1&a='.$author.'&p='.$permlink.'&w='.$weight.'&wif='.$wif;
							$file = file_get_contents($url);
						}
					}
				}
				break;
			}
		}
	}
}

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