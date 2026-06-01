<?php 
if(isset($_POST['submit']) || isset($_POST['ajax'])){
	
		// Verify reCAPTCHA v2 first
		if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
			$secretKey = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";
			$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . $_POST['g-recaptcha-response']);
			$responseData = json_decode($verifyResponse);
			
			if (!$responseData->success) {
				echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
				echo '<script type="text/javascript">
					Swal.fire({
						icon: "error",
						title: "Verification Failed!",
						text: "Please complete the reCAPTCHA verification.",
						confirmButtonColor: "#d33"
					}).then(function() {
						window.location.href = "contact.php";
					});
				</script>';
				exit();
			}
		} else {
			echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
			echo '<script type="text/javascript">
				Swal.fire({
					icon: "error",
					title: "Verification Required!",
					text: "Please complete the reCAPTCHA verification.",
					confirmButtonColor: "#d33"
				}).then(function() {
					window.location.href = "contact.php";
				});
			</script>';
			exit();
		}
	
		$errorC = false;
	
		$the_name = $_POST['name'];
		$the_no = $_POST['no'];
		$the_message = $_POST['msg'];
		$the_subject = "New query on website from: ".$_POST['name'];
		
		# want to add aditional fields? just add them to the form in template_contact.php,
		# you dont have to edit this file
		
		//added fields that are not set explicit like the once above are combined and added before the actual message
		$already_used = array('yourname','email','website','message','ajax','myemail','myblogname','Send','Subject','imageURL');
		$attach = '';
		
		foreach ($_POST as $key => $field)
		{
			if(!in_array($key,$already_used))
			{
				$attach.= $key.": ".$field."<br /> \n";
			}
		}
		$attach.= "<br /> \n";
		
		
		if($the_name == "")
		{
			$errorC = true;
			$the_nameclass = "error";
		}else{
			$the_nameclass = "valid";
		}
		
		if($the_message == "")
		{
			$errorC = true;
			$the_messageclass = "error";
		}else{
			$the_messageclass = "valid";
		}
		
		
		
		if($the_subject != '') $the_subject = 'Subject: '.$the_subject."<br/>";
//		if($the_url != '') $the_url = 'Preview Image: '.$the_url."<br/>";
		
		if($errorC == false)
		{	
			$to      =  $_POST['myemail'];
			$subject = "New Message from " . $_POST['myblogname'];
			$header  = 'MIME-Version: 1.0' . "\r\n";
			$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$header .= 'From: website'."\r\n";
		
			$message1 = nl2br($_POST['msg']);
			
			
			$message = "New message from:  $the_name <br/>
			Contact: $the_no <br/>
			
Message: $message1 <br />";
			
		
			
			mail($to,
			$subject,
			$message,
			$header); 
			
			
			echo'<script type="text/javascript"> alert("Your message has been sent!Thank you!"); </script>';
			echo '<script type="text/javascript"> window.location.href="service.php"; </script>';
			
			
		}
		
}
	
	
function checkmymail($mailadresse){
	$email_flag=preg_match("!^\w[\w|\.|\-]+@\w[\w|\.|\-]+\.[a-zA-Z]{2,4}$!",$mailadresse);
	return $email_flag;
}
?>