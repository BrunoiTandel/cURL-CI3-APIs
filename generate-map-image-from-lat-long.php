<?php 
	$ch = curl_init();
	$daq = array("file"=>'https://maps.googleapis.com/maps/api/staticmap?size=512x512&maptype=roadmap&markers=size:mid%7Ccolor:red%7C'.'Add Latitude Here'.','.'Add Latitude Here'.'&key='.'Google Map Generated API Key'.'&zoom=15',"image_name"=>'Name of the Image to be kept');
	$image_upload_location = "Give path of upload file to the project image storage where this image will be stored. See example as below";
	// $image_upload_location = "https://fsiverify.com/candidate-common/candidate-assets/images/detected-map-image-from-admin/upload.php";
	$options = array(
	    CURLOPT_URL => $image_upload_location,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POSTFIELDS =>$daq,
	);
	curl_setopt_array($ch,$options);
 	curl_exec($ch);
 	curl_close($ch);

 	// Below code to store the generate map image at the provided location. This is to be codded in the above image_upload_location url provided
 	if(isset($_POST['file'])) {
 		$src =$_POST['file'];
 		$image_name = $_POST['image_name'];
 		$src = str_replace(' ','%20',$src);
 		$src = trim($src);
  		$imagePath = $image_name;
  		file_put_contents($imagePath,file_get_contents($src));
	}
?>