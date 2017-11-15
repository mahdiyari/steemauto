<?php
require_once('database.php');
require_once('header.php');

if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['message'])){
	if(!isset($_POST['g-recaptcha-response'])){
		echo '<div id="alert" class="alert alert-danger">
					<span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>
					Captcha is Not Set.
					</div>
					<script>setTimeout(function(){ document.getElementById("alert").style.opacity = "0";setTimeout(function(){document.getElementById("alert").style.display = "none"; }, 10000) }, 10000);</script>';
	}else{
		$res = $_POST['g-recaptcha-response'];
		$secret= 'Captcha Secret Key';
		$ip = $_SERVER['REMOTE_ADDR'];
		$url = "https://www.google.com/recaptcha/api/siteverify";
		$dat = "secret=$secret&response=$res&remoteip=$ip";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dat);
		curl_setopt($ch, CURLOPT_POST, true);
		$result = json_decode(curl_exec($ch));
		if($result->success == false){
			echo '<div id="alert" class="alert alert-danger">
					<span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>
					Captcha is Wrong.
					</div>
					<script>setTimeout(function(){ document.getElementById("alert").style.opacity = "0";setTimeout(function(){document.getElementById("alert").style.display = "none"; }, 10000) }, 10000);</script>';
		}else{
			$fname = urlencode($_POST['name']);
			$email = urlencode($_POST['email']);
			$subject = urlencode($_POST['subject']);
			$message = urlencode($_POST['message']);
			$url="http://Email Server/?name=$fname&email=$email&subject=$subject&message=$message";
			$file = file_get_contents($url);
			if($file == 1){
				?>
				<script type="text/javascript">
					$(document).ready(function(){

						demo.initChartist();
						$.notify({
							icon: 'pe-7s-check',
							message: "Successfully Sent!"
						},{
							type: 'success',
							timer: 6000
						});
						

					});
				</script>
				
				<?
			}else{
				?>
			<script type="text/javascript">
					$(document).ready(function(){

						demo.initChartist();
						$.notify({
							icon: 'pe-7s-alert',
							message: "Error in sending Message!"
						},{
							type: 'danger',
							timer: 6000
						});
						

					});
				</script>	
				
<?
				}
		}
	}
}
?>


<div style="margin:1px;"><br /></div>



<!-- 
  ****************************************
  Contest Entry for Treehouse:
  "Design a Contact Form"
  Submitted by Lisa Wagner
  ****************************************
-->
<style>
.closebtn {
    margin-left: 15px;
    color: white;
    font-weight: bold;
    float: right;
    font-size: 22px;
    line-height: 20px;
    cursor: pointer;
    transition: 0.5s;
}

.closebtn:hover {
    color: black;
}
#alert {
    opacity: 1;
    transition: opacity 7s; /* 600ms to fade out */
	width: 300px;
	position:fixed;
	top:60px;
	right: 10px;
}



/* General Styles */

* {
   box-sizing:border-box;
   -webkit-box-sizing:border-box;
   -moz-box-sizing:border-box;
   -webkit-font-smoothing:antialiased;
   -moz-font-smoothing:antialiased;
   -o-font-smoothing:antialiased;
   font-smoothing:antialiased;
   text-rendering:optimizeLegibility;
}
body {
   color: #C0C0C0;
   font-family: Arial, san-serif;
}


/* Contact Form Styles */
h1 {
   margin: 10px 0 0 0;
}
h4{
   margin: 0 0 20px 0;
}
#contact-form {
   background-color:rgba(72,72,72,0.7);
   padding: 10px 20px 30px 20px;
   max-width:100%;
   float: left;
   left: 50%;
   position: relative;
   margin-top:5px;
   margin-bottom:80px;
   margin-left: -260px;
   border-radius:7px;
   -webkit-border-radius:7px;
   -moz-border-radius:7px;
}
#contact-form input,   
#contact-form select,   
#contact-form textarea,   
#contact-form label { 
   font-size: 15px;  
   margin-bottom: 2px;
   font-family: Arial, san-serif;
} 
#contact-form input,   
#contact-form select,   
#contact-form textarea { 
   width:100%;
   background: #555;
   border: 0; 
   -moz-border-radius: 4px;  
   -webkit-border-radius: 4px;  
   border-radius: 4px;
   margin-bottom: 25px;  
   padding: 5px;  
}  
#contact-form input:focus,   
#contact-form select:focus,   
#contact-form textarea:focus {  
   background-color: #555; 
}  
#contact-form textarea {
   width:100%;
   height: 150px;
}
#contact-form button[type="submit"] {
   cursor:pointer;
   width:100%;
   border:none;
   background:#991D57;
   background-image:linear-gradient(bottom, #8C1C50 0%, #991D57 52%);
   background-image:-moz-linear-gradient(bottom, #8C1C50 0%, #991D57 52%);
   background-image:-webkit-linear-gradient(bottom, #8C1C50 0%, #991D57 52%);
   color:#FFF;
   margin:0 0 5px;
   padding:10px;
   border-radius:5px;
}
#contact-form button[type="submit"]:hover {
   background-image:linear-gradient(bottom, #9C215A 0%, #A82767 52%);
   background-image:-moz-linear-gradient(bottom, #9C215A 0%, #A82767 52%);
   background-image:-webkit-linear-gradient(bottom, #9C215A 0%, #A82767 52%);
   -webkit-transition:background 0.3s ease-in-out;
   -moz-transition:background 0.3s ease-in-out;
   transition:background-color 0.3s ease-in-out;
}
#contact-form button[type="submit"]:active {
   box-shadow:inset 0 1px 3px rgba(0,0,0,0.5);
}
input:required, textarea:required {  
   box-shadow: none;
   -moz-box-shadow: none;  
   -webkit-box-shadow: none;  
   -o-box-shadow: none;  
} 
#contact-form .required {  
   font-weight:bold;  
   color: #fff;      
}

/* Hide success/failure message
   (especially since the php is missing) */
#failure, #success {
   color: #6EA070; 
   display:none;  
}

/* Make form look nice on smaller screens */
@media only screen and (max-width: 580px) {
   #contact-form{
      left: 3%;
      margin-right: 3%;
      width: 88%;
      margin-left: 0;
      padding-left: 3%;
      padding-right: 3%;
   }
}

</style>
<div class="container">
<div id="contact-form">
	<div>
		<h1>Nice to Meet You!</h1> 
		<h4>Have a question or just want to get in touch? Let's chat.</h4> 
	</div>
		<p id="failure">Oopsie...message not sent.</p>  
		<p id="success">Your message was sent successfully. Thank you!</p>

		   <form onsubmit="if(!grecaptcha.getResponse()){alert('Please Submit Captcha.'); return false;}" method="post">
			<div>
		      <label for="name">
		      	<span class="required">Name: *</span> 
		      	<input type="text" id="name" name="name" value="" placeholder="Your Name" required="required" tabindex="1" />
		      </label> 
			</div>
			<div>
		      <label for="email">
		      	<span class="required">Email: *</span>
		      	<input type="email" id="email" name="email" value="" placeholder="Your Email" tabindex="2" required="required" />
		      </label>  
			</div>
			<div>		          
		      <label for="subject">
			  <span class="required">Subject: *</span>
			      <input type="text" id="subject" name="subject" value="" placeholder="Subject" required="required" tabindex="3" />
		      </label>
			</div>
			<div>		          
		      <label for="message">
		      	<span class="required">Message: *</span> 
		      	<textarea id="message" name="message" placeholder="Please write your message here." tabindex="4" required="required"></textarea> 
		      </label>  
			</div>
			<div>
			<div style="margin-bottom:15px;margin-top:-15px;" class="g-recaptcha" data-sitekey="6LdgyjQUAAAAAOaxn89zmS4RmVgCrbJhkYa7THgV"></div>
			<script src="https://www.google.com/recaptcha/api.js"></script>
			</div>		           
			<div>		           
		      <button name="submit" type="submit" id="submit" >SEND</button> 
			</div>
		   </form>

	</div>
	</div>


<div style="margin-top:1px;"><br /></div>
	<?


include_once('footer.php');
?>
