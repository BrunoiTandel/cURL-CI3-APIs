<?php 
	$url ="https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode('Address Here')."&key=Google Map generated API Key Here";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$responseJson = curl_exec($ch);
	curl_close($ch);
	$latitude = '';
	$longitude = '';
	$response = json_decode($responseJson);
	if ($response->status == 'OK') {
	    $latitude = $response->results[0]->geometry->location->lat;
	    $longitude = $response->results[0]->geometry->location->lng;
	}
?>