<?php 
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	function send_mail_using_zeptomail($variable_array) {
		$mail = new PHPMailer();
		$mail->Encoding = "base64";
		$mail->SMTPAuth = true;
		$mail->Host = "smtp.zeptomail.in";
		$mail->Port = 587;
		$mail->Username = "emailapikey";
		$mail->Password = 'Password Here';
		$mail->SMTPSecure = 'TLS';
		$mail->isSMTP();
		$mail->IsHTML(true);
		$mail->CharSet = "UTF-8";
		$mail->From = "Email id of the Sender";
		$mail->addAddress('Email Address of the receiver');
		$mail->Body = 'Email Message';
		if (isset($variable_array['add_to_cc']) && $variable_array['add_to_cc'] == 1) {
			$mail->AddCC('Add CC email Ids here');
		}
		$mail->Subject = 'Mail Subject';
		if (isset($variable_array['attachment_available']) && $variable_array['attachment_available'] == 1 && isset($variable_array['attachment_files']) && $variable_array['attachment_files'] != '') {
			$mail->addAttachment($variable_array['attachment_files'].$variable_array['attach_file_name']);
		}
		// $mail->SMTPDebug = 1;
		// $mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str"; echo "<br>";};
		if(!$mail->Send()) {
			return 0;
		    // echo "Mail sending failed";
		} else {
			return 1;
		    // echo "Successfully sent";
		}
	}
?>