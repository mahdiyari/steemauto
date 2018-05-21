<script src="//cdnjs.cloudflare.com/ajax/libs/remarkable/1.7.1/remarkable.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-sanitize.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
<script src="/js/viewer_app.js"></script>
<style>
label{
	margin-top:11px;
}
</style>

<div class="content" ng-app="viewer_app" ng-controller="viewer_controller"> <!-- Content -->
	<div class="row" style="margin:0 !important">
		<div class="col-md-2"></div>
		<div style="" class="col-md-8">
			<div class="card">
				<div class="content">
					<h3>Welcome <? echo $name; ?>,</h3><br>
					This page is where you can schedule a post to publish in the future.

					<form style="display:;" id="post" onsubmit="post(); return false;">
						<!-- title -->
						<label for="title">Title:</label>
						<input id="title" placeholder="Post Title" name="title" type="text" class="form-control" required>
						<!-- content/body -->
						<label for="content">Content: (You can write your post on Steemit.com and copy the markdown or raw HTML Here.)</label>
						<textarea ng-model="viewer_markdown" rows="7" id="content" placeholder="Post Content" name="content" type="text" class="form-control" required></textarea>
						<sub><a href="https://simplemde.com/markdown-guide">Markdown</a> supported</sub><br>
						<!-- tags -->
						<label for="tags">Tags: (up to 5 tags and one space between them)</label>
						<input id="tags" placeholder="tag1 tag2 tag3 tag4 tag5" name="tags" type="text" class="form-control" required>
						<sub ><a style="color:red;" href="" onclick="$('#modaltags').modal('show')">Note about tags</a> (?)</sub>
						<br><br>
						<!-- select rewards type -->
						<label for="rewardstype">Rewards
							<select id="rewardstype" name="rewardstype">
								<option value="0">Default (50% / 50%)</option>
								<option value="1">Power Up 100%</option>
								<option value="2">Decline Payout (no rewards)</option>
							</select>
						</label>
						<br>
						<!-- upvote after publish -->
						<label><input id="upvotepost" type="checkbox" value="" checked> Upvote post</label>

						<!-- date & time list -->
						<?php
							date_default_timezone_set('UTC');
							$datenow = date('Y-m-d H:i');
						?>
						<hr>
						<label>Current date & time is <span style="color:green;"><? echo $datenow; ?> (UTC)</span></label><br>
						<label style="margin-top:7px;" for="date">Publish at
							<select id="date" name="date" required>
								<?php
									for ($i=1; $i <=168; $i++) { //printing list of date+time to publish post in that
										$plushourdate = date('Y-m-d H:i',strtotime($datenow . "+$i hours"));
										echo '<option value="'.$i.'">'.$plushourdate.' (UTC)</option>';
									 }
								 ?>
						</select></label><br>
						<!-- submit button -->
						<input style="margin-top:10px;"value="Submit" type="submit" class="btn btn-primary">
					</form>
					<br>
					<div id="result"></div>
					<h3 ng-if="viewer_rendered()">Preview:</h3>
					<div style="width:100%;" ng-bind-html="viewer_rendered()"></div>
				</div>
			</div>
		</div>
		<div class="col-md-2"></div>
	</div>
	<div class="row" style="margin:0 !important">
		<div style="" class="col-md-12">
			<?
			date_default_timezone_set('UTC');
			function after($x){
				$sec = $x - strtotime('now');
				if($sec > 0){
					if($sec > 60){
						if($sec > 3600){
							if($sec > 86400){
								$ago= 'After '.round($sec/86400).' Days';
							}else{
								$ago= 'After '.round($sec/3600).' Hours';
							}
						}else{
							$ago= 'After '.round($sec/60).' Minutes';
						}
					}else{
						$ago= 'After '.round($sec).' Seconds';
					}
				}else {
					$ago= 'Processed';
				}
				return $ago;
			}
			$result = $conn->query("SELECT EXISTS(SELECT * FROM `posts` WHERE `user`='$name')");
			foreach($result as $x){
				foreach($x as $x){}
			}
			if($x == 1){
			?>
			<div class="card">
				<div class="content">
					<h3 style=" padding-bottom:10px;">Scheduled Posts:</h3>
					<div style="max-height:600px; overflow:auto;" class="table-responsive-vertical shadow-z-1">
						  <!-- Table starts here -->

						<table id="table" class="table table-hover table-mc-light-blue">
						  <thead>
							<tr>
							  <th>#</th>
							  <th>Title</th>
							  <th>Content</th>
								<th>Publish Time</th>
							  <th>Status</th>
							  <th>Action</th>
							</tr>
						  </thead>
						  <tbody>
								<?
								$i = 1;
								$result = $conn->query("SELECT * FROM `posts` WHERE `user`='$name' ORDER BY `posts`.`date` ASC");
									foreach($result as $x){
									$now = strtotime('now');
								?>
										<tr class="tr2">
											<td data-title="ID"><? echo $i; ?></td>
											<td data-title="Name"><? echo $x['title']; ?></td>
											<td data-title="Status"><textarea disabled="" height="50px"><? echo $x['content']; ?></textarea></td>
											<td data-title="Status"><? echo after($x['date']); ?></td>
											<td data-title="Status"><? if($x['status'] == 0){echo 'Waiting';}elseif($x['status'] == 1){echo 'Published';}else{echo 'Error<sup>*</sup>';} ?></td>
											<td data-title="Status"><button class="btn btn-danger" onclick="if(confirm('Are You Sure?')) deletepost('<? echo $x['id']; ?>');">DELETE</button></td>
										</tr>

										<?
										$i += 1;
									}
									?>
							 </tbody>
							</table>
							<sup>*</sup>: If you got error, check tags. Bad formatted tags can cause an error in publishing posts.
						</div>

					</div>
				</div>

				<?

			}
			?>


			<script>
				document.getElementById('date').value =1;
				document.getElementById('title').value="";
				document.getElementById('content').value='';
				document.getElementById('tags').value='';
			</script>

		</div>
	</div>
</div> <!-- /Content -->
