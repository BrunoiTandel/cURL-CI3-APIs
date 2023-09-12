<?php 
	// Generate Servtel SMS Token
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://api.servetel.in/v1/auth/login",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS  => '{"email":"Registered Email ID","password":"Password for that Account"}',
		CURLOPT_HTTPHEADER => array(
			'accept: application/json',
			'content-type: application/json'
		),
	)); 
	$response = curl_exec($curl);
	$data = json_decode($response);
	$servtel_sms_token = $data->access_token;

	// Send SMS
	$sms_message = 'Paste the Message to be sent to the desired user. NOTE: The message should be preapproved from the servtel. Only than the SMS will get triggered';
	$api_key = 'Your API Key';
	$ph_number = 'Mobile Number with country code Without + as prefix';
	$sender = 'Sender name here as provided on the servtel portal';
	
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://sms-alerts.servetel.in/api/v4/?api_key='.$api_key.'&method=sms&message='.urlencode($sms_message).'&to='.$ph_number.'&sender='.$sender,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
    )); 
    $response = curl_exec($curl);
    $curl_response_data = json_decode($response,true);
?>