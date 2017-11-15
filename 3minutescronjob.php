<?php
// A cronjob for Curation Trails
require_once('database.php');
require_once('functions.php');
date_default_timezone_set('UTC');
$result = $conn->query("SELECT EXISTS(SELECT * FROM `followers`)");
foreach($result as $c){
	foreach($c as $c){}
}
if($c ==1){ //Checking and Upvoting Curation Trail
	$result = $conn->query("SELECT `user` FROM `trailers` WHERE `followers`>0");
	foreach($result as $c){
		$num = 0;
		$trailer = $c['user'];
		$x = json_decode(call('get_account_history','"'.$trailer.'", "-1", "10"'));
		$y = $x->result;
		for($i=count($x->result)-1;$i>=0;$i--){
			$timestampt = strtotime($y[$i][1]->timestamp);
			$now = strtotime('now');
			$dif = ($now - $timestampt)/60;
			if($dif < 35 && $y[$i][1]->op[0] == 'vote' && $y[$i][1]->op[1]->voter ==$trailer && $y[$i][1]->op[1]->author !=$trailer){
				$permlink = $y[$i][1]->op[1]->permlink;
				$author = $y[$i][1]->op[1]->author;
				$w = $y[$i][1]->op[1]->weight;

				$b = call('get_content','"'.$author.'","'.$permlink.'"');
				$ti=json_decode($b)->result->created;
				$tim = strtotime($ti);
				$created = ($now-$tim)/60;

				$result = $conn->query("SELECT EXISTS(SELECT * FROM `followers` WHERE `trailer`='$trailer')");
				foreach($result as $v){
					foreach($v as $v){}
				}
				if($v == 1){
					$result = $conn->query("SELECT * FROM `followers` WHERE `trailer`='$trailer'");
					foreach($result as $follower){
						if($follower['aftermin'] < $created){
							if($follower['fcurator'] == 1){
								$weight = $w;
							}else{
								$weight = $follower['weight'];
							}
							
							$wif = 'Posting Private Key';
							$followerr = $follower['follower'];
							$url='http://Upvote Server/?u='.$followerr.'&id=1&a='.$author.'&p='.$permlink.'&w='.$weight.'&wif='.$wif;
							$file = file_get_contents($url);
						}
					}
				}
				$num = $num +1;
				if($num > 1){
					break;
				}
			}
		}
		
	}
}


?>