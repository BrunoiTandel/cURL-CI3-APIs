<?php 
	$apiKey = urlencode('API Key Here');
    $numbers = urlencode(implode(',', array('Numbers to whom the message is to be triggered. Can be multiple numbers')));
    $sender = urlencode('Sender Name as provided in the Textlocal Portal');
    $message = rawurlencode("Preapproved message that is to be sent to the user");
    $curl_data = 'apikey=' . $apiKey . '&numbers=' . $numbers . "&sender=" . $sender . "&message=" . $message;
    $ch = curl_init('https://api.textlocal.in/send/');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response);
    curl_close($ch);
?>