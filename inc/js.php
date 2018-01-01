
<style>
.closebtn{margin-left:15px;color:#fff;font-weight:700;float:right;font-size:22px;line-height:20px;cursor:pointer;transition:.3s}.closebtn:hover{color:#000}#alert{opacity:1;transition:opacity 5s;width:300px;position:fixed;top:60px;right:10px}.tr1 td,.tr2 td{max-width:250px}.material-switch>input[type=checkbox]{display:none}.material-switch>label{cursor:pointer;height:0;position:relative;width:40px}.material-switch>label::after,.material-switch>label::before{content:'';margin-top:-8px;position:absolute}.material-switch>label::before{background:#000;box-shadow:inset 0 0 10px rgba(0,0,0,.5);border-radius:8px;height:16px;opacity:.3;transition:all .4s ease-in-out;width:40px}.material-switch>label::after{background:#fff;border-radius:16px;box-shadow:0 0 5px rgba(0,0,0,.3);height:24px;left:-4px;top:-4px;transition:all .3s ease-in-out;width:24px}.material-switch>input[type=checkbox]:checked+label::before{background:inherit;opacity:.5}.material-switch>input[type=checkbox]:checked+label::after{background:inherit;left:20px}
</style>
<script>
function follow(user){
	$('.btn').attr('disabled','true');
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Successfully Followed by 100% upvote weight! you can access this setting after reloading page."
				},{
					type: 'success',
					timer: 6000
				});
				location.reload();
			}else if(this.responseText == 2){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Already <b>Followed!</b>"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=3", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user);
	
	return 1;
}
function unfollow(user){
	$('.btn').attr('disabled','true');
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Successfully <b>Unfollowed!</b>"
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else if(this.responseText == 2){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Already <b>Unfollowed!</b>"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=4", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user);
	
	return 1;
}
var recent;
var recentl;
var ne;
function showset(i){
	ne = '#set'+i;
	if(recent != null && recent != ne){
		recentl.hide(500);
	}
	if(recent == ne){
		$('#set'+i).toggle(500);
	}else{
		$('#set'+i).toggle(500);
		recent = '#set'+i;
		recentl = $('#set'+i);
	}
}

function settings(user){
	$('.btn').attr('disabled','true');
	var minute = document.getElementById('aftermin'+user).value;
	var weight = document.getElementById('weight'+user).value;
	if(minute == '' || minute == null){
		minute = 0;
	}
	if(weight == '' || weight == null){
		weight = 100;
	}
	var fcurator;
	if(document.getElementById('fcurator'+user).checked){
		  fcurator = 1;
	  }else{
		   fcurator = 0;
	  } 
	var enable;
	if(document.getElementById('enable'+user).checked){
		enable = 1;
	}else{
		enable = 0;
	}
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Changes Successfully Saved."
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=5", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user+"&weight="+weight+"&minute="+minute+"&fcurator="+fcurator+"&enable="+enable);
	
	return 1;
}
function showbecome(){
	$('#become').toggle(500);
}
function become(){
	$('.btn').attr('disabled','true');
	var desc = document.getElementById('description').value;
	if(desc == '' || desc == null){
		desc = 'none.';
	}
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Successfully Saved!"
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	desc = encodeURIComponent(desc);
	xmlhttp.open("POST", "dash.php?i=6", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("desc="+desc);
	
	return 1;
}


function follow1(user){
	$('.btn').attr('disabled','true');
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Successfully Followed by 100% upvote weight! you can access this setting after reloading page."
				},{
					type: 'success',
					timer: 6000
				});
				location.reload();
			}else if(this.responseText == 2){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Already Followed!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=7", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user);
	
	return 1;
}
function unfollow1(user){
	$('.btn').attr('disabled','true');
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Successfully Unfollowed!"
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else if(this.responseText == 2){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Already Unfollowed!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=8", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user);
	
	return 1;
}
function follow2(){
	$('.btn').attr('disabled','true');
	var user = document.getElementById('userx').value;
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Successfully Followed by 100% upvote weight! you can access this setting after reloading page."
				},{
					type: 'success',
					timer: 6000
				});
				location.reload();
			}else if(this.responseText == 2){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Already Followed!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else if(this.responseText == 4){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Incorrect Username!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=9", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user);
	
	return 1;
}
function settings1(user){
	$('.btn').attr('disabled','true');
	var minute = document.getElementById('aftermin'+user).value;
	var weight = document.getElementById('weight'+user).value;
	if(minute == '' || minute == null){
		minute = 0;
	}
	if(weight == '' || weight == null){
		weight = 100;
	}
	var enable;
	if(document.getElementById('enable'+user).checked){
		enable = 1;
	}else{
		enable = 0;
	}
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Changes Successfully Saved!"
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=10", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user+"&weight="+weight+"&minute="+minute+"&enable="+enable);
	
	return 1;
}
function post(){
	$('.btn').attr('disabled','true');
	var date = encodeURIComponent(document.getElementById('date').value);
	var title = encodeURIComponent(document.getElementById('title').value);
	var content = encodeURIComponent(document.getElementById('content').value);
	var tags = document.getElementById('tags').value;
	var tagss = tags.split(' ');
	if(tagss.length > 5){
		document.getElementById('result').innerHTML = '<div class="alert alert-danger">Enter Only 5 Tags. Separated by Spaces.</div>';
		$('.btn').removeAttr('disabled');
	}else{
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				console.log(this.responseText);
				if(this.responseText == 1){	
					document.getElementById('result').innerHTML = '<div class="alert alert-success"><strong>Success!</strong> Wait...</div>';
					location.reload(); 
				}else if(this.responseText == 2){
					document.getElementById('result').innerHTML = '<div class="alert alert-danger">Enter Only 5 Tags. Separated by Spaces.</div>';
					$('.btn').removeAttr('disabled');
				}else{
					document.getElementById('result').innerHTML = '<div class="alert alert-danger">Error! Check Inputs or Report.</div>';
					$('.btn').removeAttr('disabled');
				}
				
			}
		};
		tags = encodeURIComponent(tags);
		xmlhttp.open("POST", "dash.php?i=11", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("date="+date+"&title="+title+"&content="+content+"&tags="+tags);
	}
	
	
	return 1;
}
function deletepost(id){
	$('.btn').attr('disabled','true');
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			console.log(this.responseText);
			if(this.responseText == 1){	
				document.getElementById('result').innerHTML = '<div class="alert alert-success"><strong>Removed.</strong> Wait...</div>';
				location.reload(); 
			}else{
				document.getElementById('result').innerHTML = '<div class="alert alert-danger">Error! Something went wrong.</div>';
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=12", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("id="+id);

	return 1;
}
function addusertolist(){
	$('.btn').attr('disabled','true');
	var user = document.getElementById('username').value;
	var minute = document.getElementById('aftertime').value;
	var weight = document.getElementById('weight').value;
	if(minute == '' || minute == null){
		minute = 0;
	}
	if(weight == '' || weight == null){
		weight = 100;
	}
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "User Successfully Added!"
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else if(this.responseText == 2){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Already Added!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else if(this.responseText == 4){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Username is not Valid!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=13", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user+"&weight="+weight+"&minute="+minute);
	
	return 1;
}
function removeuserfromlist(user){
	$('.btn').attr('disabled','true');
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){
				$.notify({
					icon: 'pe-7s-check',
					message: "Successfully Removed!"
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else if(this.responseText == 2){
				$.notify({
					icon: 'pe-7s-attention',
					message: "Already Removed!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=133", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user);
	
	return 1;
}

function commentupvotesettings(user){
	$('.btn').attr('disabled','true');
	var minute = document.getElementById('aftermin'+user).value;
	var weight = document.getElementById('weight'+user).value;
	if(minute == '' || minute == null){
		minute = 0;
	}
	if(weight == '' || weight == null){
		weight = 100;
	}
	var enable;
	if(document.getElementById('enable'+user).checked){
		enable = 1;
	}else{
		enable = 0;
	}
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Changes Successfully Saved!"
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=14", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("user="+user+"&weight="+weight+"&minute="+minute+"&enable="+enable);
	
	return 1;
}

function enableclaimreward(user){
	$('.btn').attr('disabled','true');

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {

			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Changes Successfully Saved!"
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=16", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("enable=1");
	
	return 1;
}
function disableclaimreward(user){
	$('.btn').attr('disabled','true');

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {

			if(this.responseText == 1){	
				$.notify({
					icon: 'pe-7s-check',
					message: "Changes Successfully Saved!"
				},{
					type: 'success',
					timer: 6000
				});
				location.reload(); 
			}else{
				$.notify({
					icon: 'pe-7s-attention',
					message: "Unknown Error!"
				},{
					type: 'danger',
					timer: 6000
				});
				$('.btn').removeAttr('disabled');
			}
			
		}
	};
	xmlhttp.open("POST", "dash.php?i=16", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("disable=1");
	
	return 1;
}

</script>
