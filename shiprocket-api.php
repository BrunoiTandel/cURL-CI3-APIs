<?php 
	function get_shiprocket_token() {
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/auth/login",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS  => '{"email": "API Token Here","password": "Password Here"}',
			CURLOPT_HTTPHEADER => array(
				'accept: application/json',
				'content-type: application/json'
			),
		)); 
		$response = curl_exec($curl);
		$data = json_decode($response);
		return $data->token;
	}

	function generate_shiptrocket_token() {
		$date = date('Y-m-d H:i:s');
		$data = $this->db->query('select created_date,token,timediff("'.$date.'",created_date) as time FROM shiprocket_token WHERE 1')->row_array();
		$update_date = explode(' ',$data['created_date']);

		$token = $data['token'];
		$time = explode(':', $data['time']);

		if (24 <= (int)$time[0]) {
			$token = $this->get_shiprocket_token();
			$this->db->query('UPDATE `shiprocket_token` SET `token`="'.$token.'", `created_date` = "'.$date.'" WHERE 1');
		}
		return $token;
	}

	function create_new_order($product_order_id) {
		$token = $this->generate_shiptrocket_token();
		$result = $this->create_shiprocket_order($token,$product_order_id);
		// echo $result;//json_encode($result);
	}

	function cancel_my_order($sub_order_id) {
		$token = $this->generate_shiptrocket_token();
		$result = $this->order_cancel_shiprocket($token,$sub_order_id);
		// echo $result;//json_encode($result);
	}

	function order_return($sub_order_id){
		$token = $this->generate_shiptrocket_token();
		$result = $this->order_return_shiprocket($token,$sub_order_id);	
	}

	function create_shiprocket_order($token,$product_order_id) {
		$arr = array();
		$sub_order = $this->db->query("SELECT * FROM product_sub_order WHERE order_id IN (".$product_order_id.')')->result_array();
		$order = $this->db->query('SELECT * FROM product_orders WHERE order_id='.$product_order_id)->row_array();
 	 	
		$payment_type = 'prepaid';
		$cod_charges = '0';
		if ($order['payment_method'] != '1') {
			$payment_type = 'COD';
			$cod_charges = $order['cod_charges'] != '' ? $order['cod_charges'] : 0;
		}

		$shipment_charges_count = 0;
		foreach ($sub_order as $key => $value) {
			$shipment_charges = ($order['applied_shipment_charges'] != '') ? $order['applied_shipment_charges'] : 0;
			if ($shipment_charges_count > 0) {
				$shipment_charges = 0;
			}
			$shipment_charges_count++;

			$product_order_price = $value['product_order_price'] * $value['sub_order_product_quantity'];

			if ($order['applied_coupon_type'] != null && $order['applied_coupon_type'] != '') {
				if ($order['applied_coupon_type'] == 0) {
					$product_order_price = round($product_order_price - ($product_order_price * ((float)$order['order_discounted_percentage'] / 100)),0);
				} elseif ($order['applied_coupon_type'] == 1) {
					$deduct_percentage = round(($product_order_price / $order['sub_total_price']) * 100,2);
					$deduct_amount = round($order['order_discounted_price'] * ($deduct_percentage / 100),2);
					$product_order_price = round($product_order_price - $deduct_amount,0);
				}
			}

			$product_order_price = $product_order_price + $cod_charges + $shipment_charges;
			if ($product_order_price == 0 || $product_order_price == '0') {
				$payment_type = 'prepaid';
			}
			$row['name'] = $value['sub_order_product_name'];
			$row['sku'] = "HNH".$value['product_sub_order_id'];
			$row['units'] = $value['sub_order_product_quantity'];
			$row['selling_price'] = $product_order_price;
			$row['discount'] ='';
			$row['tax'] ='';
			$row['hsn'] = '';  

			$prod_order = array( 
				"order_id"=> $value['product_sub_order_id'],
				"order_date"=> date('Y-m-d H:i:s'),
				"pickup_location"=> 'H&H',
				"channel_id"=> "",
				"comment"=> "",
				"billing_customer_name"=> $order['order_customer_name'],
				"billing_last_name"=> "",
				"billing_address"=> $order['product_order_address_line_1'],
				"billing_address_2"=> $order['product_order_address_line_2'],
				"billing_city"=> $order['product_order_city'],
				"billing_pincode"=> $order['product_order_pincode'],
				"billing_state"=> $order['product_order_state'],
				"billing_country"=> $order['product_order_country'],
				"billing_email"=> $order['customer_email'],
				"billing_phone"=> $order['customer_mobile'],
				"shipping_is_billing"=> true,
				"shipping_customer_name"=> "",
				"shipping_last_name"=> "",
				"shipping_address"=> "",
				"shipping_address_2"=> "",
				"shipping_city"=> "",
				"shipping_pincode"=> "",
				"shipping_country"=> "",
				"shipping_state"=> "",
				"shipping_email"=> "",
				"shipping_phone"=> "",
				"order_items"=> array($row),
				"payment_method"=> $payment_type,
				"shipping_charges"=> 0,
				"giftwrap_charges"=> 0,
				"transaction_charges"=> 0,
				"total_discount"=>0,
				"sub_total"=>$product_order_price,
				"length"=> 10,
				"breadth"=> 15,
				"height"=> 20,
				"weight"=> 2.5
			);

			$curl = curl_init();
	        curl_setopt_array($curl, array(
	            CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/orders/create/adhoc",
	            CURLOPT_RETURNTRANSFER => true,
	            CURLOPT_ENCODING => "",
	            CURLOPT_MAXREDIRS => 10,
	            CURLOPT_TIMEOUT => 0,
	            CURLOPT_FOLLOWLOCATION => true,
	            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	            CURLOPT_CUSTOMREQUEST => "POST",
	            CURLOPT_POSTFIELDS  => json_encode($prod_order),
	            CURLOPT_HTTPHEADER => array(
	                'accept: application/json',
	                 'Authorization: Bearer '.$token,
	                'content-type: application/json'
	            ),
	        )); 
	        $response = curl_exec($curl);
	        $data = json_decode($response);
	        
        	$this->db->query('UPDATE `product_sub_order` SET `shiprocket_order_id`="'.$data->order_id.'", `shiprocket_shipment_id` = "'.$data->shipment_id.'" WHERE product_sub_order_id = '.$value['product_sub_order_id']);
		}
	}

	function order_cancel_shiprocket($token,$sub_order_id){
		$sub_order = $this->db->query("SELECT shiprocket_order_id as order_id FROM product_sub_order WHERE product_sub_order_id IN (".$sub_order_id.')')->row_array();
		$curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/orders/cancel",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS  => json_encode(array('ids'=>array($sub_order['order_id']))),
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                 'Authorization: Bearer '.$token,
                'content-type: application/json'
            ),
        )); 
        return $response = curl_exec($curl);	
	}

	function order_return_shiprocket($token,$sub_order_id) {
		$arr = array();
		$sub_order = $this->db->query("SELECT * FROM product_sub_order WHERE product_sub_order_id = ".$sub_order_id)->result_array();
		$order = $this->db->query('SELECT * FROM product_orders WHERE order_id = '.$sub_order[0]['order_id'])->row_array();

		foreach ($sub_order as $key => $value) {
			$row['name'] = $value['sub_order_product_name'];
			$row['sku'] = rand();
			$row['units'] = $value['sub_order_product_quantity'];
			$row['selling_price'] = $value['product_order_price'];
			$row['discount'] ='';
			$row['tax'] ='';
			$row['hsn'] = '';
			array_push($arr, $row);
		}
		$payment_type = 'COD';
		if ($order['payment_method'] !='0') {
			$payment_type = 'prepaid';	
		}

        $order_date = explode(' ', $order['order_updated_date']);

		$prod_order = array( 
			"order_id"=> $sub_order[0]['shiprocket_order_id'],//$order_id,
			"order_date"=>  $order_date[0],
		    "pickup_location"=> $order['address_types'],
			"channel_id"=> "1153594", 
			"pickup_customer_name"=> $order['order_customer_name'],
			"pickup_last_name"=> "",
			"pickup_address"=> $order['product_order_address_line_1'],
			"pickup_address_2"=> $order['product_order_address_line_2'],
			"pickup_city"=> $order['product_order_city'],
			"pickup_state"=> $order['product_order_state'],
			"pickup_country"=> $order['product_order_country'],
			"pickup_pincode"=> $order['product_order_pincode'],
			"pickup_email"=> $order['customer_email'],
			"pickup_phone"=> $order['customer_mobile'],
			"pickup_isd_code"=>"91",
  			"pickup_location_id"=> '859168',
			// "shipping_is_billing"=> true,
			"shipping_customer_name"=> "Adithya",
			"shipping_last_name"=> "",
			"shipping_address"=> "#241 5th D Main Road, HRBR Layout 2nd Block Kalyan Nagar",
			"shipping_address_2"=> "",
			"shipping_city"=> "Bangalore",
			"shipping_pincode"=> "560043",
			"shipping_country"=> "India",
			"shipping_state"=> "Karnataka",
			"shipping_email"=> "adithya_v90@yahoo.com",
			"shipping_isd_code"=> "91",
			"shipping_phone"=> "9886586573",
			"order_items"=> $arr,
			"payment_method"=> $payment_type,  
			"total_discount"=>($order['order_discounted_price'] != '')?$order['order_discounted_price']:0,
			"sub_total"=>$order['grand_total_order_price'],
			"length"=> 10,
			"breadth"=> 15,
			"height"=> 20,
			"weight"=> 2.5
		);
		$order_data = json_encode($prod_order);

		$curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/orders/create/return",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS  => $order_data,
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                 'Authorization: Bearer '.$token,
                'content-type: application/json'
            ),
        )); 
        $response = curl_exec($curl);
        return $response;
	}

	function order_track_shiprocket($shipment_id) {
		$token = $this->generate_shiptrocket_token();
		$curl = curl_init();
	    curl_setopt_array($curl, array(
	        CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/courier/track/shipment/{$shipment_id}",
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_ENCODING => "",
	        CURLOPT_MAXREDIRS => 10,
	        CURLOPT_TIMEOUT => 0,
	        CURLOPT_FOLLOWLOCATION => true,
	        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	      /*  CURLOPT_CUSTOMREQUEST => "POST",
	        CURLOPT_POSTFIELDS  => json_encode($prod_order),*/
	        CURLOPT_HTTPHEADER => array(
	            'accept: application/json',
	             'Authorization: Bearer '.$token,
	            'content-type: application/json'
	        ),
	    )); 
	    $response = curl_exec($curl);
	    return $response;
	}
?>