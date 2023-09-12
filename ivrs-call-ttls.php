<?php
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  	CURLOPT_URL => 'https://api-smartflo.tatateleservices.com/v1/click_to_call',
	  	CURLOPT_RETURNTRANSFER => true,
	  	CURLOPT_ENCODING => '',
	  	CURLOPT_MAXREDIRS => 10,
	  	CURLOPT_TIMEOUT => 0,
	  	CURLOPT_FOLLOWLOCATION => true,
	  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  	CURLOPT_CUSTOMREQUEST => 'POST',
	  	CURLOPT_POSTFIELDS =>'{
	     	"agent_number": "Add Agent Phone number with Country code Without + Symbol",
	     	"destination_number": "Add User Phone number to whome the call has to be made with Country code Without + Symbol"
		}',
	  	CURLOPT_HTTPHEADER => array(
	    	'Authorization: Bearer Authentication Key here',
	    	'Content-Type: application/json'
	  	),
	));

	$response = curl_exec($curl);
	$curl_response = json_decode($response);
?>