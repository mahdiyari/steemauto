<?php
$active =0;
require_once('header.php');
?>
<!-- Full Width Image Header -->

<style>
.faq p {
	margin-left: 15px;	
}
</style>
<!-- Page Content -->
<div style="margin-top:25px;" class="row">
	<div class="content" style="padding-left:30px; padding-right:30px;">
		<div class="card">
			<div class="content faq" style="padding-left:30px; padding-right:30px;">
				<center><h2 class="head">Frequently Asked Questions</h2></center>
				<hr>
				<h3>How Steemauto works?</h3>
				<p>Steemauto will use your account posting authority to post, upvote, claim rewards and etc.</p>
				
				<h3>I sent money, why I can't login?</h3>
				<p>Go to your wallet and make sure you sent exactly 0.001 SBD. Then try again.</p>
				
				<h3>What is posting authority?</h3>
				<p>In the steem blockchain, each account have some keys to access to the account. One of these keys are 'posting key' which can be used to post, comment, upvote, follow, and etc. By using posting key, you can't access to account's balances.</p>
				
				<h3>Is Steemauto secure?</h3>
				<p>Steemauto is an open source app which you can see all source of it in the our <a href="https://github.com/mahdiyari/steemauto" target="_blank">GitHub repository</a>.</p>
				
				<h3>Why should I enter my password in the steemconnect.com?</h3>
				<p>We are using steemconnect.com to broadcast a transaction which will allow @steemauto to use your account posting authority and broadcasting transactions needs your password.
				Of course, you can remove this access any time by clicking on 'Unauthorize' button in your dashboard. </p>
				
				<h3>Do you have any access to my account balance?</h3>
				<p>No. @steemauto can only publish a post, upvote, follow, resteem, claim rewards.</p>
				
				<h3>Steemauto will use my account without my permission?</h3>
				<p>No. Steemauto only will work by the settings which you configured in your account.</p>
				
				<h3>What is the Curation trail?</h3>
				<p>By using 'Curation trail' section, you will be able to upvote on the posts (not comments) which are upvoted by the selected user. 
				You will be able to configure 'upvote weight' for each user in that page. 
				You can submit a short description and become a 'trail' which other users will be able to follow your upvotes. 
				Note: for upvoting that user's authored posts, you should use Fanbase. Curtaion trail will not follow self upvotes.</p>
				
				<h3>What is the Fanbase?</h3>
				<p>You can upvote your favorite authors immediately after publishing any post (not comments). 
				You will be able to configure 'upvote weight' for each user in that page. </p>
				
				<h3>My upvotes will be replaced?</h3>
				<p>No. In the other tools, if you upvote any post twice with different 'upvote weight', second upvote will be replaced with the old upvote. 
				But, in the Steemauto that is fixed. You can upvote any post manually before applying auto upvotes.</p>
				
				<h3>Any other question?</h3>
				<p>Ask your questions from the <a href="/contact.php" target="_blank">Contact</a> page.</p>

			</div>
		</div>
	</div>
</div>

<?php
require('footer.php');
?>
