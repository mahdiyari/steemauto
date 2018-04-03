
			<footer class="footer">
				<div class="container-fluid">
					<center>
					<p class="copyright">
						<a href="https://github.com/myary/steemauto">Steemauto</a>, Made by Steem Witness <a target="_blank" href="https://steemit.com/@mahdiyari">@mahdiyari</a> with Love <i style="color:red;"class="pe-7s-like"></i> for Steem Users. |<a href="https://discord.gg/qhKDfEp"> Discord Channel</a>
						<br>
						<a href="/privacy-policy.php">Privacy Policy</a> | <a href="/about-us.php">About us</a>
					</p>
					</center>
				</div>
			</footer>
		</div>
	</div>

	<!-- Modal pop-up starts -->
	<?
	if($log){
	?>
	<div class="modal fade" id="modalwitness" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Support Steemauto</h4>
				</div>
				<div class="modal-body">
					<!-- body -->
					Steemauto is a free and unlimited service which is built for steem community. As a growing website, we need to pay for our servers. We are using a server with <span style="color:green;">256GB RAM</span> and that is not enough for processing over <span style="color:red;">10 millions</span> of upvotes per day. I want to keep Steemauto free and unlimited, and I need your help for this purpose.
					<br>Kindly, follow below links and support Steemauto by voting @mahdiyari as a witness.
					<br><br><a href="https://steemconnect.com/sign/account-witness-vote?witness=mahdiyari&approve=1" target="_blank">Vote by steemconnect</a> or <a href="https://steemit.com/~witnesses" target="_blank">Vote by steemit</a>
				</div>
				<div style="" class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-default" onclick="hidemodalwitness();">Hide forever</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modaldonations" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Support Steemauto</h4>
					</div>
					<div class="modal-body">
						<!-- body -->
						Please check our donations page too: <a href="/donations.php">Click here</a>
					</div>
					<div style="" class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-default" onclick="hidemodaldonations();">Hide forever</button>
					</div>
				</div>
			</div>
		</div>
	<script>
	function getCookie(cname) {
		var name = cname + "=";
		var decodedCookie = decodeURIComponent(document.cookie);
		var ca = decodedCookie.split(';');
		for(var i = 0; i <ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}
	function setCookie(cname, cvalue, exdays) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires="+ d.toUTCString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

	$(function(){
		if(!getCookie('modalwitness')){
			$('#modalwitness').modal('show');
		}
		if(!getCookie('modaldonations')){
			$('#modaldonations').modal('show');
			setCookie('modaldonations','true',365);
		}
	});
	function hidemodalwitness(){
		setCookie('modalwitness','true',365);
		$('#modalwitness').modal('hide');
	}
	function hidemodaldonations(){
		setCookie('modaldonations','true',365);
		$('#modaldonations').modal('hide');
	}

	</script>
	<?
	}
	?>
	<!-- Modal pop-up end -->

	<!-- Modal about tags -->
	<div class="modal fade" id="modaltags" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Note about tags:</h4>
				</div>
				<div class="modal-body">
					<!-- body -->
					<p>Wrong tags can cause an error in publishing posts.<br>
						You can't start a tag with digits.<br>
						You should use only lowercase letters, digits and one dash.<br>
						Each tag must end with a letter or number.<br><br>
						<span style="color:red;">Wrong examples: <code>Tag 4tag tag- tag_abc tag@asd ab-cd-ef</code></span><br>
						<span style="color:green;">Correct examples: <code>tag tag4 -tag tag-abc tag41abc</code></span>

					</p>
				</div>
				<div style="" class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

</body>

<!--   Core JS Files   -->
<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
<!--  Checkbox, Radio & Switch Plugins -->
<script src="assets/js/bootstrap-checkbox-radio-switch.js"></script>
<!--  Charts Plugin -->

<!--  Notifications Plugin    -->
<script src="assets/js/bootstrap-notify.js"></script>
<!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
<script src="assets/js/light-bootstrap-dashboard.js"></script>
<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->

<script type="text/javascript">
	$(document).ready(function(){



		//$.notify({
	//			icon: 'pe-7s-attention',
	//			message: "all problems fixed. just ignore this message."
	//		},{
	//			type: 'warning',
	//			timer: 6000
	//		});

	});
</script>
<script>
	$(document).ready(function(){
	$('[data-toggle="tooltip"]').tooltip();
	});
</script>

</html>
