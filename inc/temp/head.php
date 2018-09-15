
<?php

//die('Adding some options... try 5 minutes later. ');


require_once('inc/conf/db.php');
require_once('inc/dep/login_register.php');


?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="icon" href="img/logo.png">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>SteemAuto - Curation Trail and Fanbase</title>
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
	<meta name="viewport" content="width=device-width" />
	<!-- Bootstrap core CSS     -->
	<link href="/assets/css/bootstrap.min.css" rel="stylesheet" />

	<!-- Animation library for notifications   -->
	<link href="/assets/css/animate.min.css" rel="stylesheet"/>
	<script src="/js/jquery.js"></script>
	<!--  Light Bootstrap Table core CSS    -->
	<link href="/assets/css/light-bootstrap-dashboard.css" rel="stylesheet"/>

	<!--  CSS for Demo Purpose, don't include it in your project     -->
	<link href="/assets/css/demo.css" rel="stylesheet" />

	<!--     Fonts and icons     -->
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
	<link href="/assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
	<script src="/steem.min.js"></script>
</head>
<body>
	<div class="wrapper" >
		<div class="sidebar" data-color="light-green" data-image="/img/logo.png">
			<!-- Tip 1: you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple" Tip 2: you can also add an image using data-image tag -->

			<div class="sidebar-wrapper">
				<div class="logo">
					<a href="/" class="simple-text">
						SteemAuto
					</a>
				</div>

				<ul class="nav">
					<li <? if($active ==0){echo 'class="active"';} ?>>
						<a href="/">
							<i class="pe-7s-home"></i>
							<p>Home</p>
						</a>
					</li>
				   <? if($log){ ?> <li <? if($active ==1){echo 'class="active"';} ?>>
						<a href="/dash.php">
							<i class="pe-7s-user"></i>
							<p>Dashboard</p>
						</a>
					</li>
					<li <? if($active ==2){echo 'class="active"';} ?>>
						<a href="/dash.php?i=1">
							<i class="pe-7s-angle-up-circle"></i>
							<p>Curation Trail</p>
						</a>
					</li>
					<li <? if($active ==3){echo 'class="active"';} ?>>
						<a href="/dash.php?i=2">
							<i class="pe-7s-like"></i>
							<p>Fanbase</p>
						</a>
					</li>
					<li <? if($active ==4){echo 'class="active"';} ?>>
						<a href="/dash.php?i=11">
							<i class="pe-7s-date"></i>
							<p>Schedule Posts</p>
						</a>
					</li>
					<li <? if($active ==5){echo 'class="active"';} ?>>
						<a href="/dash.php?i=13">
							<i class="pe-7s-comment"></i>
							<p>Upvote Comments</p>
						</a>
					</li>
					<li <? if($active ==6){echo 'class="active"';} ?>>
						<a href="/dash.php?i=16">
							<i class="pe-7s-wallet"></i>
							<p>Claim Rewards</p>
						</a>
					</li>
					<li>
						<a onclick="logout();">
							<i class="pe-7s-back-2"></i>
							<p>Logout</p>
						</a>
					</li>
				   <? }else{ ?>
				   <li>
						<a href="https://steemconnect.com/oauth2/authorize?client_id=steem.app&redirect_uri=https://steemauto.com/callback.php&scope=login">
							<i class="pe-7s-door-lock"></i>
							<p>Login / Register</p>
						</a>
					</li>
				   <? } ?>

			<!-- <li class="active-pro"><a href="upgrade.html"><i class="pe-7s-rocket"></i><p>Upgrade to PRO</p></a></li> -->
				</ul>
			</div>
		</div>

		<div class="main-panel" style="margin:0;padding:0;">
			<nav class="navbar navbar-default navbar-fixed">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="/">SteemAuto</a>
					</div>
					<div class="collapse navbar-collapse">

						<ul class="nav navbar-nav navbar-left">
							 <li>
							   <a href="/faq.php">
									<p style="" class="">FAQ</p>
								</a>
							</li>
							<li>
								<a href="/donations.php">
								 <p style="" class="">Donations</p>
							 </a>
						 </li>
						</ul>

				  <!--      <ul class="nav navbar-nav navbar-left">
							 <li>
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="fa fa-dashboard"></i>
									<p class="hidden-lg hidden-md">Dashboard</p>
								</a>
							</li>
							<li class="dropdown">
								  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
										<i class="fa fa-globe"></i>
										<b class="caret hidden-sm hidden-xs"></b>
										<span class="notification hidden-sm hidden-xs">5</span>
										<p class="hidden-lg hidden-md">
											5 Notifications
											<b class="caret"></b>
										</p>
								  </a>
								  <ul class="dropdown-menu">
									<li><a href="#">Notification 1</a></li>
									<li><a href="#">Notification 2</a></li>
									<li><a href="#">Notification 3</a></li>
									<li><a href="#">Notification 4</a></li>
									<li><a href="#">Another notification</a></li>
								  </ul>
							</li>
							<li>
							   <a href="">
									<i class="fa fa-search"></i>
									<p class="hidden-lg hidden-md">Search</p>
								</a>
							</li>
						</ul> -->

						<ul class="nav navbar-nav navbar-right">

							<li class="dropdown">
								  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><p>Support <b class="caret"></b></p></a>
								  <ul class="dropdown-menu">
									<li><a target="_blank" href="https://steemit.com/steemauto/@mahdiyari/steemauto-com-or-curation-trail-fanbase-scheduled-posts-video">Help Video</a></li>
									<li><a href="/contact.php">Contact Us</a></li>
								  </ul>
							</li>
							<li class="separator hidden-lg hidden-md"></li>
						</ul>
					</div>
				</div>
			</nav>
			<script>
				function setCookie(cname, cvalue, exdays) {
					var d = new Date();
					d.setTime(d.getTime() + (exdays*24*60*60*1000));
					var expires = "expires="+ d.toUTCString();
					document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
				}
				function logout(){
					callApi('https://steemauto.com/api/v1/logout','')
					window.location="/";
				}
			</script>
			<style>
				.navbar-toggle{
					position: fixed;
					top: 5px;
					right: 15px;
					z-index: 9999;
					background-color: #204ac3;
					padding: 5px;
					border: 1px solid #000;
				}
				.navbar-toggle:hover{
					background-color: #062272 !important;
				}
				.navbar-toggle:focus{
					background-color: #062272 !important;
				}
				.navbar-default .navbar-toggle .icon-bar {
					background-color: #fff !important;
				}
			</style>
