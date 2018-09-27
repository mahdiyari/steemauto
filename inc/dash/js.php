
<style>
.closebtn{margin-left:15px;color:#fff;font-weight:700;float:right;font-size:22px;line-height:20px;cursor:pointer;transition:.3s}.closebtn:hover{color:#000}#alert{opacity:1;transition:opacity 5s;width:300px;position:fixed;top:60px;right:10px}.tr1 td,.tr2 td{max-width:250px}.material-switch>input[type=checkbox]{display:none}.material-switch>label{cursor:pointer;height:0;position:relative;width:40px}.material-switch>label::after,.material-switch>label::before{content:'';margin-top:-8px;position:absolute}.material-switch>label::before{background:#000;box-shadow:inset 0 0 10px rgba(0,0,0,.5);border-radius:8px;height:16px;opacity:.3;transition:all .4s ease-in-out;width:40px}.material-switch>label::after{background:#fff;border-radius:16px;box-shadow:0 0 5px rgba(0,0,0,.3);height:24px;left:-4px;top:-4px;transition:all .3s ease-in-out;width:24px}.material-switch>input[type=checkbox]:checked+label::before{background:inherit;opacity:.5}.material-switch>input[type=checkbox]:checked+label::after{background:inherit;left:20px}
</style>
<script>
function callApi(url, body){
	$('.btn').attr('disabled','true')
	const xmlhttp = new XMLHttpRequest()
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (JSON.parse(this.responseText).id == 1) {
				$.notify({
					icon: 'pe-7s-check',
					message: JSON.parse(this.responseText).result
				},{
					type: 'success',
					timer: 8000
				})
				location.reload()
			} else {
				$.notify({
					icon: 'pe-7s-attention',
					message: JSON.parse(this.responseText).error
				},{
					type: 'danger',
					timer: 8000
				})
				$('.btn').removeAttr('disabled')
			}
		}
	}
	xmlhttp.open('POST', url, true)
	xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
	xmlhttp.send(body)

	return 1
}

function follow(user){ //follow a trail
	callApi('api/v1/dashboard/curation_trail/follow', 'trail=' + encodeURIComponent(user))
	return 1
}
function unfollow(user){ //unfollow a trail
	callApi('api/v1/dashboard/curation_trail/unfollow', 'trail=' + encodeURIComponent(user))
	return 1
}
let recent = null
let recentl = null
let ne = null
function showset(i){ //show settings
	ne = '#set' + i
	if(recent !== null && recent !== ne){
		recentl.hide(500)
	}
	if (recent === ne){
		$('#set' + i).toggle(500)
	} else {
		$('#set' + i).toggle(500)
		recent = '#set' + i
		recentl = $('#set' + i)
	}
}

function settings(user){ //settings for trail
	let minute = document.getElementById('aftermin' + user).value
	let votingway
	const radios = document.getElementsByName('votingway' + user)
	for (let i = 0, length = radios.length; i < length; i++) {
		if (radios[i].checked) {
			votingway = radios[i].value
			break
		}
	}
	const weight = document.getElementById('weight'+user).value
	if (minute === '' || minute === null) {
		minute = 0
	}
	if (weight === '' || weight === null) {
		weight = 50
	}
	let enable
	if (document.getElementById('enable' + user).checked) {
		enable = 1
	} else {
		enable = 0
	}
	const body = 'trail=' + encodeURIComponent(user) +
		'&weight=' + encodeURIComponent(weight) +
		'&minute=' + encodeURIComponent(minute) +
		'&votingway=' + encodeURIComponent(votingway) +
		'&enable=' + encodeURIComponent(enable)
	
	callApi('api/v1/dashboard/curation_trail/settings', body)
	return 1
}

function showbecome(){ //show become trail
	$('#become').toggle(500);
}
function become(){ //becoming/editing trail
	let desc = document.getElementById('description').value
	if (desc == '' || desc == null) {
		desc = 'none.'
	}
	callApi('api/v1/dashboard/curation_trail/become', 'desc=' + encodeURIComponent(desc))
	return 1
}


function follow1(user){ //follow a fan
	callApi('api/v1/dashboard/fanbase/follow', 'fan=' + encodeURIComponent(user))
	return 1
}
function unfollow1(user){ //unfollow a fan
	callApi('api/v1/dashboard/fanbase/unfollow', 'fan=' + encodeURIComponent(user))	
	return 1;
}
function follow2(){ //follow a fan by form
	const user = document.getElementById('userx').value
	callApi('api/v1/dashboard/fanbase/follow', 'fan=' + encodeURIComponent(user))
	return 1
}
function settings1(user){ //settings for a fan
	let minute = document.getElementById('aftermin'+user).value
	let weight = document.getElementById('weight'+user).value
	const dailylimit = document.getElementById('dailylimit'+user).value
	if (minute === '' || minute === null) {
		minute = 0
	}
	if (weight === '' || weight === null) {
		weight = 100
	}
	var enable;
	if (document.getElementById('enable' + user).checked) {
		enable = 1
	} else {
		enable = 0
	}
	const body = 'fan=' + encodeURIComponent(user) +
		'&weight=' + encodeURIComponent(weight) +
		'&minute=' + encodeURIComponent(minute) +
		'&enable=' + encodeURIComponent(enable) +
		'&dailylimit=' + encodeURIComponent(dailylimit)

	callApi('api/v1/dashboard/fanbase/settings', body)
	return 1
}
function post(){
	const date = encodeURIComponent(document.getElementById('date').value)
	const title = encodeURIComponent(document.getElementById('title').value)
	const content = encodeURIComponent(document.getElementById('content').value)
	const rewardstype = encodeURIComponent(document.getElementById('rewardstype').value)
	const beneficiarytype = encodeURIComponent(document.getElementById('beneficiarytype').value)
	let upvotepost = 1
	if (document.getElementById('upvotepost').checked) {
		upvotepost = 1
	} else {
		upvotepost = 0
	}
	let tags = document.getElementById('tags').value
	const ctregex = /[^a-z0-9\-\,\ ]/g
	tags = tags.toLowerCase()
	tags = tags.replace(ctregex,'')
	const tagss = tags.split(/[ ,]+/)
	if (tagss.length > 5) {
		document.getElementById('result').innerHTML = '<div class="alert alert-danger">Enter Only 5 Tags. Separated by Spaces.</div>'
	} else {
		let arrtags = []
		for (let tag of tagss) {
			arrtags.push(encodeURIComponent(tag))
		}
		tags = encodeURIComponent(tags)

		const body = 'date=' + date +
			'&title=' + title +
			'&content=' + content +
			'&rewardstype=' + rewardstype +
			'&upvotepost=' + upvotepost +
			'&tags=' + JSON.stringify(arrtags) +
			'&beneficiarytype=' + beneficiarytype

		callApi('api/v1/dashboard/schedule_post/submit', body)
	}
	return 1
}
function deletepost(id){
	callApi('api/v1/dashboard/schedule_post/delete', 'id=' + encodeURIComponent(id))
	return 1
}
function addusertolist(){
	const user = document.getElementById('username').value
	let minute = document.getElementById('aftertime').value
	let weight = document.getElementById('weight').value
	if (minute === '' || minute === null) {
		minute = 0
	}
	if (weight === '' || weight === null) {
		weight = 100
	}
	const body = 'user=' + encodeURIComponent(user) +
		'&weight=' + encodeURIComponent(weight) +
		'&minute=' + encodeURIComponent(minute)
	callApi('api/v1/dashboard/comment_upvote/add', body)
	return 1
}
function removeuserfromlist(user){
	callApi('api/v1/dashboard/comment_upvote/delete', 'user=' + encodeURIComponent(user))
	return 1
}

function commentupvotesettings(user){
	let minute = document.getElementById('aftermin' + user).value
	let weight = document.getElementById('weight' + user).value
	if (minute === '' || minute === null) {
		minute = 0
	}
	if (weight === '' || weight === null) {
		weight = 100
	}
	let enable
	if(document.getElementById('enable' + user).checked) {
		enable = 1
	} else {
		enable = 0
	}
	const body = 'user=' + encodeURIComponent(user) +
		'&weight=' + encodeURIComponent(weight) +
		'&minute=' + encodeURIComponent(minute) +
		'&enable=' + encodeURIComponent(enable)
	callApi('api/v1/dashboard/comment_upvote/settings', body)
	return 1
}

function enableclaimreward(){
	callApi('api/v1/dashboard/claim_reward/toggle')
	return 1
}
function disableclaimreward(){
	callApi('api/v1/dashboard/claim_reward/toggle')
	return 1
}

function updateTrail(){
	callApi('api/v1/dashboard/curation_trail/update')
	return 1
}

</script>
