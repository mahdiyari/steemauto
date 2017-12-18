
	<div class="content"> <!-- Content -->
	<div class="row" style="margin:0 !important">
	<div class="col-md-3"></div>
	<div style="" class="col-md-6">
	<div class="card">
	<div class="content">
	<h3>Welcome <? echo $name; ?>,</h3><br>
	Here you can add a Post to published in the future.
	
	<form style="display:;" id="post" onsubmit="post(); return false;">
	<label for="title">Title:</label>
	<input id="title" placeholder="Post Title" name="title" type="text" class="form-control" required>
	<label for="content">Content: (You can Write in Steemit.com and Copy Markdown or Raw Html Here.)</label>
	<textarea id="content" placeholder="Post Content" name="content" type="text" class="form-control" required></textarea>
	<label for="tags">Tags:</label>
	<input id="tags" placeholder="tag1 tag2 tag3 tag4 tag5" name="tags" type="text" class="form-control" required>
	<label style="margin-top:7px;" for="date">Publish After <select id="date" name="date" required>
	<option value="1">1</option> 
	<option value="2">2</option> 
	<option value="3">3</option> 
	<option value="4">4</option> 
	<option value="5">5</option> 
	<option value="6">6</option> 
	<option value="7">7</option> 
	<option value="8">8</option> 
	<option value="9">9</option> 
	<option value="10">10</option> 
	<option value="11">11</option> 
	<option value="12">12</option> 
	<option value="13">13</option> 
	<option value="14">14</option> 
	<option value="15">15</option> 
	<option value="16">16</option> 
	<option value="17">17</option> 
	<option value="18">18</option> 
	<option value="19">19</option> 
	<option value="20">20</option> 
	<option value="21">21</option> 
	<option value="22">22</option> 
	<option value="23">23</option> 
	<option value="24">24</option> 
	<option value="25">25</option> 
	<option value="26">26</option> 
	<option value="27">27</option> 
	<option value="28">28</option> 
	<option value="29">29</option> 
	<option value="30">30</option> 
	<option value="31">31</option> 
	<option value="32">32</option> 
	<option value="33">33</option> 
	<option value="34">34</option> 
	<option value="35">35</option> 
	<option value="36">36</option> 
	<option value="37">37</option> 
	<option value="38">38</option> 
	<option value="39">39</option> 
	<option value="40">40</option> 
	<option value="41">41</option> 
	<option value="42">42</option> 
	<option value="43">43</option> 
	<option value="44">44</option> 
	<option value="45">45</option> 
	<option value="46">46</option> 
	<option value="47">47</option> 
	<option value="48">48</option> 
	<option value="49">49</option> 
	<option value="50">50</option> 
	<option value="51">51</option> 
	<option value="52">52</option> 
	<option value="53">53</option> 
	<option value="54">54</option> 
	<option value="55">55</option> 
	<option value="56">56</option> 
	<option value="57">57</option> 
	<option value="58">58</option> 
	<option value="59">59</option> 
	<option value="60">60</option> 
	<option value="61">61</option> 
	<option value="62">62</option> 
	<option value="63">63</option> 
	<option value="64">64</option> 
	<option value="65">65</option> 
	<option value="66">66</option> 
	<option value="67">67</option> 
	<option value="68">68</option> 
	<option value="69">69</option> 
	<option value="70">70</option> 
	<option value="71">71</option> 
	<option value="72">72</option>
	<option value="73">73</option> 
	<option value="74">74</option> 
	<option value="75">75</option> 
	<option value="76">76</option> 
	<option value="77">77</option> 
	<option value="78">78</option> 
	<option value="79">79</option> 
	<option value="80">80</option> 
	<option value="81">81</option> 
	<option value="82">82</option> 
	<option value="83">83</option> 
	<option value="84">84</option> 
	<option value="85">85</option> 
	<option value="86">86</option> 
	<option value="87">87</option> 
	<option value="88">88</option> 
	<option value="89">89</option> 
	<option value="90">90</option> 
	<option value="91">91</option> 
	<option value="92">92</option> 
	<option value="93">93</option> 
	<option value="94">94</option> 
	<option value="95">95</option> 
	<option value="96">96</option> 
	<option value="97">97</option> 
	<option value="98">98</option> 
	<option value="99">99</option> 
	<option value="100">100</option>
	</select> Hours.</label><br>
	<input style="margin-top:10px;"value="Submit" type="submit" class="btn btn-primary">
	</form>
	<br>
	<div id="result"></div>
	</div>
	</div>
	</div>
	<div class="col-md-3"></div>
	</div>
	<div class="row" style="margin:0 !important">
	<div style="" class="col-md-12">
	<?
	date_default_timezone_set('UTC');
	function after($x){
		$sec = $x - strtotime('now');
		if($sec > 60){
			if($sec > 3600){
				if($sec > 86400){
					$ago= round($sec/86400).' Days';
				}else{
					$ago= round($sec/3600).' Hours';
				}
			}else{
				$ago= round($sec/60).' Minutes';
			}
		}else{
			$ago= round($sec).' Seconds';
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
				<td data-title="Status">After <? echo after($x['date']); ?></td>
				<td data-title="Status"><button class="btn btn-danger" onclick="if(confirm('Are You Sure?')) deletepost('<? echo $x['id']; ?>');">DELETE</button></td>
			</tr>

			<?
			$i += 1;
		}
		?>
			  </tbody>
		</table>
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
