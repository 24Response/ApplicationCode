<?php        

/**
 * Safetyapi Controller
 * 
 * @created    17/11/2016
 * @package    Safety
 * @copyright  Copyright (C) 2016
 * @license    Proprietary
 * @author     Anuj Maurya
 */

class SafetyapiController extends AppController
{
	public $logID;

	/**
	 * Allows permissions to Web Services all actions 
	 * so that we can access them without authentication
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow();
	}
    
	/*
	 * Main function get all the web services requests
	 * and return response
     */
	public function index()
	{
		$this->layout = "";
		$this->autoRender = false;
		//Gets POST data
		$data = $this->data;        
		$this->_create_log($data);

		//Checks if request is valid  
		if ($this->_validate_request($data))
		{
			switch ($this->StringInputCleaner($data['Authentication']['name']))
			{
				
				case 'sign_in':
					$this->_authenticate_member_encrypt($data, CUSTOMER_GROUP_ID);
					break;
				
			   case 'trackme_swipe_msg':
				    $this->_trackme_swipe_msg($data);
					break;
					
				case 'swipe_status_update':
				    $this->_swipe_status_update($data);
					break;
				
               case 'incident_acceptance':
					$this->_incident_acceptance($data);
					break;

				case 'photo_upload':
					$this->_photo_upload($data);
					break;

				case 'send_notifications':
					$this->_send_pending_notifications($data);
					break;
				
				default:
					$this->_invalid_request($data);
					break;
			}
		}
		else if ($data['Authentication']['name'] == "logout")
		{
			$this->_update_logout_status($data);
		}
		else if ($data['Authentication']['name'] == "location_update")
		{
			$this->_location_update($data);
		}
		else if ($data['Authentication']['name'] == "emergency_contact_update")
		{
			$this->_emergency_contact_update($data);
		}
		else if ($data['Authentication']['name'] == "subscription_update")
		{
			$this->_subscription_update($data);
		}
		else if ($data['Authentication']['name'] == "notification_view")
		{
			$this->_notification_view($data);
		}
		else if ($data['Authentication']['name'] == "notification_swipe")
		{
			$this->_notification_swipe($data);
		}
		else if ($data['Authentication']['name'] == "customer_change_password")
		{
			$this->_change_encrypt_password($data);
		}
		else if ($data['Authentication']['name'] == "customer_track")
		{
			$this->_customer_track($data);
		}
		else if ($data['Authentication']['name'] == "customer_my_profile")
		{
			$this->_my_profile($data);
		}
		else if ($data['Authentication']['name'] == "forgot_password")
		{
			$this->_validate_username_encrypt($data, CUSTOMER_GROUP_ID);
		}
		else if ($data['Authentication']['name'] == "safe_location")
		{
			$this->_add_safe_location($data, CUSTOMER_GROUP_ID);
		}
		else if ($data['Authentication']['name'] == "view_safe_location")
		{
			$this->_view_safe_location($data, CUSTOMER_GROUP_ID);
		}
		else if ($data['Authentication']['name'] == "edit_safe_location")
		{
			$this->_edit_safe_location($data, CUSTOMER_GROUP_ID);
		}
		else if ($data['Authentication']['name'] == "delete_safe_location")
		{
			$this->_delete_safe_location($data, CUSTOMER_GROUP_ID);
		}
		else if ($data['Authentication']['name'] == "view_family_addon")
		{
			$this->_view_family_addon($data, CUSTOMER_GROUP_ID);
		}
		else if ($data['Authentication']['name'] == "edit_family_addon")
		{
			$this->_edit_family_addon($data, CUSTOMER_GROUP_ID);
		}
		else if ($data['Authentication']['name'] == "update_family_addon")
		{
			$this->_update_family_addon($data, CUSTOMER_GROUP_ID);
		}
		else if ($data['Authentication']['name'] == "add_family_addon")
		{
			$this->_add_family_addon($data, CUSTOMER_GROUP_ID);
		}
		else if ($data['Authentication']['name'] == "trackme_info")
		{
			$this->_addtrackme_encrypt_info($data, CUSTOMER_GROUP_ID);
		}
		else if ($data['Authentication']['name'] == "safe_call")
		{
			$modelName = "SafeCall";
			$this->_validate_call_encrypt($data, $modelName);
		}
		else if ($data['Authentication']['name'] == "safe_call_tracking")
		{
			$modelName = "SafeCall";
			$this->_tracking_encrypt($data, $modelName);
		}
		else if ($data['Authentication']['name'] == "safe_call_status_update")
		{
			$modelName = "SafeCall";
			$this->_status_update($data, $modelName);
		}
		else if ($data['Authentication']['name'] == "incident")
		{
			$modelName = "Incident";
			$this->_validate_call_encrypt($data, $modelName);
		}
		else if ($data['Authentication']['name'] == "incident_tracking")
		{
			$modelName = "Incident";
			$this->_tracking_encrypt($data, $modelName);
		}
		else if ($data['Authentication']['name'] == "incident_status_update")
		{
			$modelName = "Incident";
			$this->_status_update($data, $modelName);
		}
		else if ($data['Authentication']['name'] == "assistme")
		{
			$modelName = "assistmes";
			$this->_assist_me_encrypt_data($data, $modelName);
		}
		else if ($data['Authentication']['name'] == "buy_now")
		{
			$this->_buy_now($data);
		}
		else if ($data['Authentication']['name'] == "buy_now_test")
		{
			$this->_buy_now_test($data);
		}
		else if ($data['Authentication']['name'] == "disablebuynow_version_1.2.5")
		{
			$this->_disablebuynow_version3($data);
		}
		else if ($data['Authentication']['name'] == "state_list")
		{
			$this->_state_list($data);
		}
		else if ($data['Authentication']['name'] == "city_list")
		{
			$this->_city_list($data);
		}
		else if ($data['Authentication']['name'] == "category_list")
		{
			$this->_category_list($data);
		}
		else if ($data['Authentication']['name'] == "subcategory_list")
		{
			$this->_subcategory_list($data);
		}
		else if ($data['Authentication']['name'] == "resend_otp")
		{
			$this->_resend_otp($data);
		}
		else if ($data['Authentication']['name'] == "otr_coupon_verify")
		{
			$this->_otrcouponVerify($data);
		}
		else if ($data['Authentication']['name'] == "otr_activation")
		{
			$this->_accountactivation($data);
		}
		else if ($data['Authentication']['name'] == "otr_otp_verification")
		{
			$this->_otr_otp_verification($data);
		}
		else if ($data['Authentication']['name'] == "notification_update_info")
		{
			$this->_notification_user_update_info($data);
		}
		
		else if ($data['Authentication']['name'] == "responder_info")
		{
			$this->_user_responder_info($data);
		}
        else if ($data['Authentication']['name'] == "verify_coupon")
		{
			$this->_couponVerify($data);
		}
		else if ($data['Authentication']['name'] == "about_us")
		{
			$this->_about_us($data);
		}
		else if ($data['Authentication']['name'] == "faq")
		{
			$this->_faq($data);
		}
		else if ($data['Authentication']['name'] == "terms")
		{
			$this->_tnc($data);
		}
		else if ($data['Authentication']['name'] == "verify_family_addon_coupon")
		{
			$this->_family_addon_coupon($data);
		}
		else if ($data['Authentication']['name'] == "event_rating")
		{
			$this->_event_rating($data);
		}
		else if ($data['Authentication']['name'] == "package_details")
		{
			$this->_package_details($data);
		}
		else if ($data['Authentication']['name'] == "new_order")
		{
			$this->_buy_order($data);
		}
		else if ($data['Authentication']['name'] == "update_info")
		{
			$this->_updateReponseInformation($data);
		}
		else if ($data['Authentication']['name'] == "push_test")
		{
			$this->_push_test($data);
		}
		else
		{
			$this->_invalid_request($data);
		}
	}
    
    private function _couponVerify($data)
	{
		$packageId = $data["Data"]["package_id"];
        $coupon = $data["Data"]["coupon_code"];
        
        $this->loadModel('Coupon');
        $result = $this->Coupon->verify($coupon, $packageId);
        
        if ($result["validCoupon"] == 1 && $result["Percentage"] == 1)
        {
            $this->_set_response($data, 1, "Coupon applied successfully", "", array());
        }
        else
        {
            $this->_set_response($data, 0, "", INVALID_COUPON, array());
        }
	}
	
	private function _push_test($data)
	{
		$deviceToken = $data["Data"]["token"];
		$message = "You are being tracked now by OTR.Have a safe travel";
        $passphrase = '12345';
		echo $deviceToken;
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', 'certificates/ck.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

		// Open a connection to the APNS server
		$fp = stream_socket_client(
				'ssl://gateway.push.apple.com:2195', $err,
				$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

		if (!$fp)
			exit("Failed to connect: $err $errstr" . PHP_EOL);

		echo 'Connected to APNS Device Token = '. $deviceToken .'  socket id  ='.$fp. PHP_EOL;
		//LogData("Connected to APNS Device Token = ". $deviceToken ."  socket id  =".$fp,1,DBG_DETAIL); 
		// Create the payload body
		$body['aps'] = array(
				'alert' => $message,
				'sound' => 'default',
				'category' =>'EVENTACTION'
		);

		// Encode the payload as JSON
		$payload = json_encode($body);

		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));
		// Close the connection to the server
		// fclose($fp);

		//LogData("Message send to apple server and response from appl is  =".$result,1,DBG_DETAIL); 
		//echo "result success ".$result ." message :".$message;
		if (!$result){
			echo "Failed";
			return false;			

			}
			
		else{
		    echo "result success ".$result ." message :".$message;
			
		    return true;
			}

		fclose($fp);
	}
	
	private function _family_addon_coupon($data)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_coupon_code = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["coupon_code"]));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$coupon3 = trim($decrypted_coupon_code);
		$coupon = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$coupon3);
		$this->loadModel("Coupon");
        $this->Coupon->recursive = -1;
		$couponArr = $this->Coupon->find("first", array("conditions" => array("name" => $coupon)));
		$packageId = $couponArr['Coupon']['package_id'];
	    $this->loadModel('Coupon');
        $result = $this->Coupon->addon_verify_activation($coupon, $packageId);
        if ($result["validCoupon"] == 1)
        {
            $this->_set_response($data, 1, "Coupon applied successfully", "", array());
        }
        else
        {
            $this->_set_response($data, 0, "", INVALID_COUPON, array());
        }
		
	}
	
	/*Package_details*/
	
	private function _package_details($data)
	{
		
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$user_id = mdecrypt_generic($td, $this->hex2bin2($data['Data']['user_id']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$user_id1 = trim($user_id);
		$user_id2 = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $user_id1);
	    $this->loadModel('User');
		$this->User->recursive = -1;
		$User = $this->User->find('first', array('conditions' => array('id' => $user_id2)));
		if(empty($User['User']['id']))
		{
		 $this->_set_response($data, 0, "", "Invalid Request!", array());
		}
		else
		{
		$this->loadModel("Package");
        $this->Package->recursive = -1;
		$Package_deatils = $this->Package->find('all', array('conditions' => array('level' =>60)));
		$responseData = array();
		$responseData1 = array();
		$responseData2 = array();
		$i=0;
		$j=0;
		$k=0;
		foreach ($Package_deatils as $value1)
		{
		$responseData[$i]['id'] = $value1['Package']['id'];
		$responseData[$i]['name'] = $value1['Package']['name'];
		$responseData[$i]['package_type'] = $value1['Package']['description'];
		$responseData[$i]['price'] = $value1['Package']['amount'];
		$i++;
		}
		$package_info = array();
		$package_info['Package_details']= $responseData;
		$package_details = array();
	    $Package_deatils1 = $this->Package->find('all', array('conditions' => array('level' =>61)));
		foreach ($Package_deatils1 as $value)
		{
		$responseData1[$j]['id'] = $value['Package']['id'];
		$responseData1[$j]['name'] = $value['Package']['name'];
		$responseData1[$j]['package_type'] = $value['Package']['description'];
		$responseData1[$j]['actual_price'] = $value['Package']['actual_price'];
		$responseData1[$j]['discount'] = $value['Package']['discount'];
		$responseData1[$j]['price'] = $value['Package']['amount'];
		$j++;
		}
		$package_details['Helpme24'] = $responseData1;
		$Package_deatils2 = $this->Package->find('all', array('conditions' => array('level' =>62)));
		foreach ($Package_deatils2 as $value2)
		{
		$responseData2[$k]['id'] = $value2['Package']['id'];
		$responseData2[$k]['name'] = $value2['Package']['name'];
		$responseData2[$k]['package_type'] = $value2['Package']['description'];
		$responseData2[$k]['actual_price'] = $value2['Package']['actual_price'];
		$responseData2[$k]['discount'] = $value2['Package']['discount'];
		$responseData2[$k]['price'] = $value2['Package']['amount'];
		$k++;
		}
		$package_details['Track24'] = $responseData2;
		$package_info['Package_info']= $package_details;
	    $this->_set_response($data, 1, "Success", "", $package_info);
		}
		 
	}
	
	/*End Pkg Details*/

  
  /*Create new order*/
  	
	private function _buy_order($data)
	{
		
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$user_id = mdecrypt_generic($td, $this->hex2bin2($data['Data']['user_id']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$user_id1 = trim($user_id);
		$user_id2 = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $user_id1);
	    $this->loadModel('User');
		$this->User->recursive = -1;
		$User = $this->User->find('first', array('conditions' => array('id' => $user_id2)));
		if(empty($User['User']['id']))
		{
		 $this->_set_response($data, 0, "", "Invalid Request!", array());
		}
		else
		{
		$package_id = $data['Data']['package_id'];
		$this->_create_new_order($data,$user_id2,$package_id);
	    }
		 
	}
	
	
	
	private function _create_new_order($data,$id, $package_id)
	{  
        $user_id = $id;
		$this->loadModel('Package');
		$this->Package->recursive = -1;
		$this->Package->id = $package_id;
        $primaryUserID = $id;
        
        $date = date("d-m-Y", time());
		$addMonths = $this->Package->field('duration_in_months');
		$endDate = date("d-m-Y", strtotime($date . ' +' . $addMonths . ' months'));
		$finalendDate = date("d-m-Y", strtotime($endDate . ' -1 days'));
        $used = 0;
        
		//Creates new order
		$orderData = array();

		//Amount Calculations
		$totalAmount = $packageAmount = $this->Package->field('amount');

		$adjustAmount = 0;

        $couponDiscount = 0;
        $payableAmount = $totalAmount - $adjustAmount;
		$this->loadModel('Order');
		$this->Order->recursive = -1;
		$orderData['Order']['package_amount'] = $packageAmount;
		$orderData['Order']['tax_amount'] = 0;
		$orderData['Order']['total_amount'] = $totalAmount;
		$orderData['Order']['adjust_amount'] = $adjustAmount;
		$orderData['Order']['coupon_discount'] = $couponDiscount;
        $orderData['Order']['payable_amount'] = $payableAmount;
		$orderData['Order']['payment_mode'] = 1; //Online
		$orderData['Order']['order_status'] = 0; //Pending

		$orderData['Order']['created_by'] = $id;
		$orderData['Order']['modified_by'] = $id;

		$this->Order->create();
		$this->Order->save($orderData);
        $orderId = $this->Order->id;
        
		//Creates order details
		$orderDetailData = array();
		$this->loadModel('OrderDetail');
		$this->OrderDetail->recursive = -1;
		$orderDetailData['OrderDetail']['order_id'] = $this->Order->id;
		$orderDetailData['OrderDetail']['user_id'] = $id;
		$orderDetailData['OrderDetail']['package_id'] = $package_id;
		$orderDetailData['OrderDetail']['start_date'] = $date;
		$orderDetailData['OrderDetail']['end_date'] = $finalendDate;
		$orderDetailData['OrderDetail']['updated_start_date'] = $date;
		$orderDetailData['OrderDetail']['updated_end_date'] = $finalendDate;
		$orderDetailData['OrderDetail']['total_incidents'] = $this->Package->field('number_of_incidents');
		$orderDetailData['OrderDetail']['total_safe_calls'] = $this->Package->field('number_of_safe_calls');
        $orderDetailData['OrderDetail']['used_incidents'] = $used;
        $orderDetailData['OrderDetail']['used_safe_calls'] = $used;
		$orderDetailData['OrderDetail']['package_amount'] = $packageAmount;
		$orderDetailData['OrderDetail']['tax_amount'] = 0;
        $orderDetailData['OrderDetail']['primary_user_id'] = $primaryUserID;
		$orderDetailData['OrderDetail']['total_amount'] = $totalAmount;
		$orderDetailData['OrderDetail']['adjust_amount'] = $adjustAmount;
		$orderDetailData['OrderDetail']['coupon_discount'] = $couponDiscount;
        $orderDetailData['OrderDetail']['payable_amount'] = $payableAmount;
		$orderDetailData['OrderDetail']['order_status'] = 1;
		$orderDetailData['OrderDetail']['is_active'] = 0;
		$orderDetailData['OrderDetail']['created_by'] = $id;
		$orderDetailData['OrderDetail']['modified_by'] = $id;

		$this->Order->OrderDetail->create();
		$this->Order->OrderDetail->save($orderDetailData);
        $orderDetailId = $this->Order->OrderDetail->id;
        
		$order_details = array();
		$order_details['order_id'] = $orderId;
		$this->_set_response($data, 1, "Order Created successfully", "", $order_details);
	}

	
	private function _updateReponseInformation($data)
    {
        
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$user_id = mdecrypt_generic($td, $this->hex2bin2($data['Data']['user_id']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$user_id1 = trim($user_id);
		$user_id2 = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $user_id1);
	    $this->loadModel('User');
		$this->User->recursive = -1;
		$User = $this->User->find('first', array('conditions' => array('id' => $user_id2)));
		if(empty($User['User']['id']))
		{
		 $this->_set_response($data, 0, "", "Invalid Request!", array());
		}
		else
		{
		$orderId = $data['Data']['order_id'];
		$txnid = $data['Data']['transaction_id'];
		$gatewayResponse = $data['Data']['payment_gateway_response'];
		$paymentId = $data['Data']['payment_id'];
		$packageDetailId = $data['Data']['package_id'];
		$this->loadModel("OrderDetail");
		$this->OrderDetail->recursive = -1;
		$Order_details = $this->OrderDetail->find('first', array('conditions' => array('order_id' => $orderId)));
		$this->OrderDetail->id = $Order_details['OrderDetail']['id'];
		$this->OrderDetail->savefield('order_status', 1);
		$this->OrderDetail->savefield('is_active', 1);
		$this->loadModel("Order");
		$this->Order->recursive = -1;
		$this->Order->id = $orderId;
        $this->Order->saveField('payment_gateway_response', $gatewayResponse);
		$this->Order->saveField('payment_id', $paymentId);
		$this->Order->saveField('transaction_id', $txnid);
		$this->Order->saveField('order_status', 1);
		//update last package if available
		$q = "UPDATE order_details SET order_status = 0, is_active = 0 WHERE user_id = ".$User['User']['id']. " AND id <> ". $Order_details['OrderDetail']['id'];
        $this->Order->query($q);
	    $this->loadModel("CustomerRecord");
	    $this->CustomerRecord->recursive = -1;
		$usersDetails = $this->CustomerRecord->find("first", array("conditions" =>array("id" => $User['User']['id'])));
		$userInfoArray = array();
		$userInfoArray["fullname"] = $usersDetails["CustomerRecord"]["fullname"];
		$userInfoArray["email"] = $usersDetails["CustomerRecord"]["email"];
		$userInfoArray["mobile"] = $usersDetails["CustomerRecord"]["mobile"];
		$userInfoArray["package_name"] = $usersDetails["CustomerRecord"]["package_name"];
		$userInfoArray["order_id"] = $usersDetails["CustomerRecord"]["order_id"];		
        $userInfoArray["transaction_id"] = $usersDetails["CustomerRecord"]["transaction_id"];
		$userInfoArray["gender"] = $usersDetails["CustomerRecord"]["gender"];	
		$userInfoArray["package_id"] = $usersDetails["CustomerRecord"]["package_id"];
		$userInfoArray["user_id"] = $usersDetails["CustomerRecord"]["user_id"];		
		$userInfoArray["usage_limit"] = 0;	
		$userInfoArray["expire"] = 0;	
	    $this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "Registration Detail", "admin_signup_inform", $userInfoArray, array(), array(), StaticArrays::$successSignupContactEmails);
			if($packageDetailId == 84 || $packageDetailId == 85 || $packageDetailId == 86 || $packageDetailId == 87)
			{
			 /* $this->generateCoupons($userInfoArray);*/
			}
			else
			{
			
			}	
		 $this->_set_response($data, 1, "You have successfully purchased!!", "", array());				
		}
		
    }
	
	/*End Buy New Order*/
	
	private function _event_rating($data)
	{
		
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$event_id = mdecrypt_generic($td, $this->hex2bin2($data['Data']['event_id']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$event_id2 = trim($event_id);
		$event_id1 = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $event_id2);
		
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$customer_id = mdecrypt_generic($td, $this->hex2bin2($data['Data']['customer_id']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$customer_id2 = trim($customer_id);
		$customer_id1 = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $customer_id2);
		
		$this->loadModel('Incident');
		$this->Incident->recursive = -1;
		$incident = $this->Incident->find('first', array('conditions' => array('id' => $event_id1)));
		$this->loadModel('SafeCall');
		$this->SafeCall->recursive = -1;
		$SafeCall = $this->SafeCall->find('first', array('conditions' => array('id' => $event_id1)));
		$this->loadModel('User');
		$this->User->recursive = -1;
		$User = $this->User->find('first', array('conditions' => array('id' => $customer_id1)));
		if(empty($User['User']['id']))
		{
		 $this->_set_response($data, 0, "", "Invalid Request!", array());
		}
		else
		{
		$this->loadModel("star_ratings");
        $this->star_ratings->recursive = -1;
		$star_rating = array();
		$star_rating['star_ratings']['event_id'] = $event_id1;
		$star_rating['star_ratings']['customer_id'] = $customer_id1;
		$star_rating['star_ratings']['rating'] = $data['Data']['rating'];
		$star_rating['star_ratings']['feedback'] = $data['Data']['feedback'];
		$this->star_ratings->create();
	    if($this->star_ratings->save($star_rating))
			{  
	    $this->_set_response($data, 1, "Thank you for your feedback", "", array());
		    }
		}
		 
	}

  /*Change password using encryption*/
	private function _change_encrypt_password($data)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_oldpass = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["old_password"]));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$oldpassword = trim($decrypted_oldpass);
        $td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td2, $secret_key, $iv); 
		$decrypted_newpass = mdecrypt_generic($td2, $this->hex2bin2($data["Data"]["new_password"]));
		mcrypt_generic_deinit($td2); 
		mcrypt_module_close($td2); 
		$newpassword = trim($decrypted_newpass);
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td3, $secret_key, $iv); 
		$decrypted_userid = mdecrypt_generic($td3, $this->hex2bin2($data["Data"]["user_id"]));
		mcrypt_generic_deinit($td3); 
		mcrypt_module_close($td3); 
		$user_id = trim($decrypted_userid);
        $this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
        
        if($userDetails['User']['upmanual']!=$oldpassword)
        {
            $this->_set_response($data, 0, "", "Password does not match!", array()); 
        }
        else
        {
           $this->loadModel('User');
		   $this->User->recursive = -1;
		   $this->User->id = $user_id;
           $this->User->saveField('password', $newpassword);
		   $this->User->saveField('upmanual', $newpassword); 
		   $this->_set_response($data, 1, "Password Updated successfully", "", array()); 
        }
	}
	/*End Change encryption*/
	
	/*My profile*/
	private function _my_profile($data)
	{
		
	   $cipher = "rijndael-128"; 
	   $mode = "cbc"; 
	   $secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
	   $iv = "fedcba9876543210"; 
	   $key_len = strlen($secret_key); 
	   if($key_len < 16 ){ 
	   $addS = 16 - $key_len; 
	   for($i =0 ;$i < $addS; $i++){ 
	   $secret_key.=" "; 
		} 
		}else{ 
	   $secret_key = substr($secret_key, 0, 16); 
		} 
	   $td = mcrypt_module_open($cipher, "", $mode, $iv); 
	   mcrypt_generic_init($td, $secret_key, $iv); 
	   $decrypted_userid = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
	   mcrypt_generic_deinit($td); 
	   mcrypt_module_close($td); 
	   $user_id = trim($decrypted_userid);
	   $this->loadModel("User");
	   $this->User->recursive = -1;
	   $userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
	   if(empty($userDetails['User']['id']))
	   {
	   $this->_invalid_request($data);
	   }
	   else
	   {
	   $this->_getCustomerInfo($responseData, $user_id);
	   $this->_getServiceUsagesInfo($responseData, $user_id);
	   $this->_set_response($data, 1, $responseData, "", array()); 
	   }
	  
      
	}
	
	private function _getCustomerInfo(&$responseData, $userId)
	{
		$this->loadModel('UserProfile');
		$this->loadModel('User');
		$this->UserProfile->recursive = -1;
		$this->User->recursive = -1;
		$angles = $this->UserProfile->find('first', array('conditions' => array('user_id' => $userId)));
		$customerNameArr = $this->User->findById($userId);
		if (!empty($angles))
		{
			$responseData['first_name'] = $customerNameArr['User']['firstname'];
			$responseData['last_name'] = $customerNameArr['User']['lastname'];
			$responseData['mobile_number'] = $customerNameArr['User']['mobile'];
			$responseData['email_id'] = !empty($customerNameArr['User']['email']) ? $customerNameArr['User']['email']:"";
			$responseData['profile_pic'] = "http://apps1.onetouchresponse.com/profiles_pic/".$customerNameArr['User']['username'].'/'.$angles['UserProfile']['profile_pic'];
			$responseData['emergency_name1'] = !empty($angles['UserProfile']['emergency_name1']) ? $angles['UserProfile']['emergency_name1']:"";
			$responseData['emergency_phone1'] = !empty($angles['UserProfile']['emergency_phone1']) ? $angles['UserProfile']['emergency_phone1']:"";
			$responseData['emergency_email1'] = !empty($angles['UserProfile']['emergency_email1']) ? $angles['UserProfile']['emergency_email1']:"";
			$responseData['emergency_relation1'] = !empty($angles['UserProfile']['emergency_relation1']) ? $angles['UserProfile']['emergency_relation1']:"";
			$responseData['emergency_name2'] = !empty($angles['UserProfile']['emergency_name2']) ? $angles['UserProfile']['emergency_name2']:"";
			$responseData['emergency_phone2'] = !empty($angles['UserProfile']['emergency_phone2']) ? $angles['UserProfile']['emergency_phone2']:"";
			$responseData['emergency_email2'] = !empty($angles['UserProfile']['emergency_email2']) ? $angles['UserProfile']['emergency_email2']:"";
			$responseData['emergency_relation2'] = !empty($angles['UserProfile']['emergency_relation2']) ? $angles['UserProfile']['emergency_relation2']:"";
			$responseData['emergency_name3'] = !empty($angles['UserProfile']['emergency_name3']) ? $angles['UserProfile']['emergency_name3']:"";
			$responseData['emergency_phone3'] = !empty($angles['UserProfile']['emergency_phone3']) ? $angles['UserProfile']['emergency_phone3']:"";
			$responseData['emergency_email3'] = !empty($angles['UserProfile']['emergency_email3']) ? $angles['UserProfile']['emergency_email3']:"";
			$responseData['emergency_relation3'] = !empty($angles['UserProfile']['emergency_relation3']) ? $angles['UserProfile']['emergency_relation3']:"";
		}
	}

	private function _getServiceUsagesInfo(&$responseData, $userId)
	{
		$this->loadModel("CustomerRecord");
		$this->CustomerRecord->recursive = -1;
		$customer_data = $this->CustomerRecord->find("first", array("conditions" => array("id" => $userId)));
		$this->loadModel("Coupon");
        $this->Coupon->recursive = -1;
        $couponArr = $this->Coupon->find("first", array("conditions" => array("primary_user_id" =>$userId),'order' => 'Coupon.id DESC'));
		$coupon_code_number = $couponArr['Coupon']['name'];
		if (!empty($customer_data))
		{
			$responseData['HelpME_Limit'] = $customer_data['CustomerRecord']['total_incidents']== -1 ? "Unlimited" : $customer_data['CustomerRecord']['total_incidents'];
			$responseData['HelpME_Usages'] = $customer_data['CustomerRecord']['used_incidents'];
	        $responseData['TrackME_Limit'] = $customer_data['CustomerRecord']['total_safe_calls']== -1 ? "Unlimited" : $customer_data['CustomerRecord']['total_safe_calls'];
		    $responseData['TrackME_Usages'] = $customer_data['CustomerRecord']['used_safe_calls'];
			$responseData['Gender'] = $customer_data['CustomerRecord']['gender'];
			$responseData['Dob'] = !empty($customer_data['CustomerRecord']['dob']) ? $customer_data['CustomerRecord']['dob']:"";
			$responseData['Alternate_No'] = !empty($customer_data['CustomerRecord']['alternate_no']) ? $customer_data['CustomerRecord']['alternate_no']:"";
			$responseData['Date_Of_Subscription'] = $customer_data['CustomerRecord']['updated_start_date'];
			$responseData['Date_Of_Renewal'] = $customer_data['CustomerRecord']['updated_end_date'];
			$responseData['Country'] = $customer_data['CustomerRecord']['country']=='' ? "":$customer_data['CustomerRecord']['country'];
			$responseData['State'] = $customer_data['CustomerRecord']['state']=='' ? "":$customer_data['CustomerRecord']['state'];
			$responseData['City'] = $customer_data['CustomerRecord']['city']=='' ? "":$customer_data['CustomerRecord']['city'];
			$responseData['PinCode'] = !empty($customer_data['CustomerRecord']['pincode']) ? $customer_data['CustomerRecord']['pincode']:"";
			$responseData['Address_1'] = !empty($customer_data['CustomerRecord']['street_address1']) ? $customer_data['CustomerRecord']['street_address1']:"";
			$responseData['Address_2'] = !empty($customer_data['CustomerRecord']['street_address2']) ? $customer_data['CustomerRecord']['street_address2']:"";
			$responseData['package_name'] = $customer_data['CustomerRecord']['package_name'];
			$responseData['blood_group'] = !empty($customer_data['CustomerRecord']['blood_group']) ? $customer_data['CustomerRecord']['blood_group']:"";
			$responseData['coupon_code'] = $coupon_code_number;
			$responseData['user_id'] = $userId;
		}
		
	}
	
    
	private function _about_us($data)
	{
	$webinfo = array();
	$webinfo['about_us'] = "https://www.onetouchresponse.com/web_view/about_us.html";
	$this->_set_response($data, 1, "Success", "", $webinfo);
	}
	
	private function _tnc($data)
	{
	$tncinfo = array();
	$tncinfo['terms'] = "https://www.onetouchresponse.com/web_view/terms_and_conditions.html";
	$this->_set_response($data, 1, "Success", "", $tncinfo);
	}
	
	private function _faq($data)
	{
	$faqinfo = array();
	$faqinfo['faq'] = "https://www.onetouchresponse.com/web_view/faq.html";
	$this->_set_response($data, 1, "Success", "", $faqinfo);
	}
	
	
	/*End My profile*/
	
	/*Profile Update*/
	private function _location_update($data)
	{
		
		$cipher = "rijndael-128"; 
	    $mode = "cbc"; 
	    $secret_key = "D4:6E:AC:3F:F0:BE"; 
		 //iv length should be 16 bytes 
	    $iv = "fedcba9876543210"; 
	    $key_len = strlen($secret_key); 
	    if($key_len < 16 ){ 
	    $addS = 16 - $key_len; 
	    for($i =0 ;$i < $addS; $i++){ 
	    $secret_key.=" "; 
		} 
		}
		else{ 
	    $secret_key = substr($secret_key, 0, 16); 
		 } 
	    $td = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td, $secret_key, $iv); 
	    $decrypted_userid = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
	    mcrypt_generic_deinit($td); 
	    mcrypt_module_close($td); 
	    $user_id = trim($decrypted_userid);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td1, $secret_key, $iv); 
	    $decrypted_country = mdecrypt_generic($td1, $this->hex2bin2($data["Data"]["country"]));
	    mcrypt_generic_deinit($td1); 
	    mcrypt_module_close($td1); 
	    $country = trim($decrypted_country);
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td2, $secret_key, $iv); 
	    $decrypted_state = mdecrypt_generic($td2, $this->hex2bin2($data["Data"]["state"]));
	    mcrypt_generic_deinit($td2); 
	    mcrypt_module_close($td2); 
	    $state = trim($decrypted_state);
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td3, $secret_key, $iv); 
	    $decrypted_city = mdecrypt_generic($td3, $this->hex2bin2($data["Data"]["city"]));
	    mcrypt_generic_deinit($td3); 
	    mcrypt_module_close($td3); 
	    $city = trim($decrypted_city);
		$td4 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td4, $secret_key, $iv); 
	    $decrypted_pincode = mdecrypt_generic($td4, $this->hex2bin2($data["Data"]["pincode"]));
	    mcrypt_generic_deinit($td4); 
	    mcrypt_module_close($td4); 
	    $pincode = trim($decrypted_pincode);
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td5, $secret_key, $iv); 
	    $decrypted_street_address1 = mdecrypt_generic($td5, $this->hex2bin2($data["Data"]["street_address1"]));
	    mcrypt_generic_deinit($td5); 
	    mcrypt_module_close($td5); 
	    $street_address1 = trim($decrypted_street_address1);
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td6, $secret_key, $iv); 
	    $decrypted_street_address2 = mdecrypt_generic($td6, $this->hex2bin2($data["Data"]["street_address2"]));
	    mcrypt_generic_deinit($td6); 
	    mcrypt_module_close($td6); 
	    $street_address2 = trim($decrypted_street_address2);
	    $this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		if(empty($userDetails['User']['id']))
		 {
		$this->_invalid_request($data);
		 }
		else
		 {
		$this->loadModel('UserProfile');
	    $this->UserProfile->recursive = -1;
		$profileDetails = $this->UserProfile->find("first", array("conditions" => array("user_id" => $user_id)));
	    $this->UserProfile->id = $profileDetails['UserProfile']['id'];
	    $this->UserProfile->saveField('country', $country);
	    $this->UserProfile->saveField('state', $state); 
		$this->UserProfile->saveField('city', $city); 
		$this->UserProfile->saveField('pincode', $pincode); 
		$this->UserProfile->saveField('street_address1', $street_address1); 
		$this->UserProfile->saveField('street_address2', $street_address2); 
	    $this->_set_response($data, 1, "Profile Updated successfully", "", array());
		 }
	}
	
	private function _emergency_contact_update($data)
	{
		
		$cipher = "rijndael-128"; 
	    $mode = "cbc"; 
	    $secret_key = "D4:6E:AC:3F:F0:BE"; 
		 //iv length should be 16 bytes 
	    $iv = "fedcba9876543210"; 
	    $key_len = strlen($secret_key); 
	    if($key_len < 16 ){ 
	    $addS = 16 - $key_len; 
	    for($i =0 ;$i < $addS; $i++){ 
	    $secret_key.=" "; 
		} 
		}
		else{ 
	    $secret_key = substr($secret_key, 0, 16); 
		 } 
	    $td = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td, $secret_key, $iv); 
	    $decrypted_userid = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
	    mcrypt_generic_deinit($td); 
	    mcrypt_module_close($td); 
	    $user_id = trim($decrypted_userid);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td1, $secret_key, $iv); 
	    $decrypted_emergency_name1 = mdecrypt_generic($td1, $this->hex2bin2($data["Data"]["emergency_name1"]));
	    mcrypt_generic_deinit($td1); 
	    mcrypt_module_close($td1); 
	    $emergency_name1 = trim($decrypted_emergency_name1);
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td2, $secret_key, $iv); 
	    $decrypted_emergency_phone1 = mdecrypt_generic($td2, $this->hex2bin2($data["Data"]["emergency_phone1"]));
	    mcrypt_generic_deinit($td2); 
	    mcrypt_module_close($td2); 
	    $emergency_phone1 = trim($decrypted_emergency_phone1);
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td3, $secret_key, $iv); 
	    $decrypted_emergency_email1 = mdecrypt_generic($td3, $this->hex2bin2($data["Data"]["emergency_email1"]));
	    mcrypt_generic_deinit($td3); 
	    mcrypt_module_close($td3); 
	    $emergency_email1 = trim($decrypted_emergency_email1);
		$td4 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td4, $secret_key, $iv); 
	    $decrypted_emergency_relation1 = mdecrypt_generic($td4, $this->hex2bin2($data["Data"]["emergency_relation1"]));
	    mcrypt_generic_deinit($td4); 
	    mcrypt_module_close($td4); 
	    $emergency_relation1 = trim($decrypted_emergency_relation1);
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td5, $secret_key, $iv); 
	    $decrypted_emergency_name2 = mdecrypt_generic($td5, $this->hex2bin2($data["Data"]["emergency_name2"]));
	    mcrypt_generic_deinit($td5); 
	    mcrypt_module_close($td5); 
	    $emergency_name2 = trim($decrypted_emergency_name2);
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td6, $secret_key, $iv); 
	    $decrypted_emergency_phone2 = mdecrypt_generic($td6, $this->hex2bin2($data["Data"]["emergency_phone2"]));
	    mcrypt_generic_deinit($td6); 
	    mcrypt_module_close($td6); 
	    $emergency_phone2 = trim($decrypted_emergency_phone2);
		$td7 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td7, $secret_key, $iv); 
	    $decrypted_emergency_email2 = mdecrypt_generic($td7, $this->hex2bin2($data["Data"]["emergency_email2"]));
	    mcrypt_generic_deinit($td7); 
	    mcrypt_module_close($td7); 
	    $emergency_email2 = trim($decrypted_emergency_email2);
		$td8 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td8, $secret_key, $iv); 
	    $decrypted_emergency_relation2 = mdecrypt_generic($td8, $this->hex2bin2($data["Data"]["emergency_relation2"]));
	    mcrypt_generic_deinit($td8); 
	    mcrypt_module_close($td8); 
	    $emergency_relation2 = trim($decrypted_emergency_relation2);
		$this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		if(empty($userDetails['User']['id']))
		 {
		$this->_invalid_request($data);
		 }
		else
		 {
		$this->loadModel('UserProfile');
	    $this->UserProfile->recursive = -1;
		$profileDetails = $this->UserProfile->find("first", array("conditions" => array("user_id" => $user_id)));
	    $this->UserProfile->id = $profileDetails['UserProfile']['id'];
	    $this->UserProfile->saveField('emergency_name1', $emergency_name1);
	    $this->UserProfile->saveField('emergency_phone1', $emergency_phone1); 
		$this->UserProfile->saveField('emergency_email1', $emergency_email1); 
		$this->UserProfile->saveField('emergency_relation1', $emergency_relation1); 
		$this->UserProfile->saveField('emergency_name2', $emergency_name2);
	    $this->UserProfile->saveField('emergency_phone2', $emergency_phone2); 
		$this->UserProfile->saveField('emergency_email2', $emergency_email2); 
		$this->UserProfile->saveField('emergency_relation2', $emergency_relation2); 
		$this->_set_response($data, 1, "Emergency Contact Updated successfully", "", array());
		 }
	}
	
	private function _subscription_update($data)
	{
		
		$cipher = "rijndael-128"; 
	    $mode = "cbc"; 
	    $secret_key = "D4:6E:AC:3F:F0:BE"; 
		 //iv length should be 16 bytes 
	    $iv = "fedcba9876543210"; 
	    $key_len = strlen($secret_key); 
	    if($key_len < 16 ){ 
	    $addS = 16 - $key_len; 
	    for($i =0 ;$i < $addS; $i++){ 
	    $secret_key.=" "; 
		} 
		}
		else{ 
	    $secret_key = substr($secret_key, 0, 16); 
		 } 
	    $td = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td, $secret_key, $iv); 
	    $decrypted_userid = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
	    mcrypt_generic_deinit($td); 
	    mcrypt_module_close($td); 
	    $user_id1 = trim($decrypted_userid);
		$user_id = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $user_id1);
		
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td1, $secret_key, $iv); 
	    $decrypted_mobile = mdecrypt_generic($td1, $this->hex2bin2($data["Data"]["mobile"]));
	    mcrypt_generic_deinit($td1); 
	    mcrypt_module_close($td1); 
	    $mobile_num1 = trim($decrypted_mobile);
		$mobile_num = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $mobile_num1);
		
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td2, $secret_key, $iv); 
	    $decrypted_firstname = mdecrypt_generic($td2, $this->hex2bin2($data["Data"]["firstname"]));
	    mcrypt_generic_deinit($td2); 
	    mcrypt_module_close($td2); 
	    $firstname1 = trim($decrypted_firstname);
		$firstname = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $firstname1);
		
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td3, $secret_key, $iv); 
	    $decrypted_lastname = mdecrypt_generic($td3, $this->hex2bin2($data["Data"]["lastname"]));
	    mcrypt_generic_deinit($td3); 
	    mcrypt_module_close($td3); 
	    $lastname1 = trim($decrypted_lastname);
		$lastname = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',  $lastname1);
		
		$td4 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td4, $secret_key, $iv); 
	    $decrypted_email = mdecrypt_generic($td4, $this->hex2bin2($data["Data"]["email"]));
	    mcrypt_generic_deinit($td4); 
	    mcrypt_module_close($td4); 
	    $email1 = trim($decrypted_email);
		$email = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',  $email1);
		
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td5, $secret_key, $iv); 
	    $decrypted_alternate_no = mdecrypt_generic($td5, $this->hex2bin2($data["Data"]["alternate_no"]));
	    mcrypt_generic_deinit($td5); 
	    mcrypt_module_close($td5); 
	    $alternate_no1 = trim($decrypted_alternate_no);
		$alternate_no = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $alternate_no1);
		
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td6, $secret_key, $iv); 
	    $decrypted_gender = mdecrypt_generic($td6, $this->hex2bin2($data["Data"]["gender"]));
	    mcrypt_generic_deinit($td6); 
	    mcrypt_module_close($td6); 
	    $gender1 = trim($decrypted_gender);
		$gender = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $gender1);
		
		$td7 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td7, $secret_key, $iv); 
	    $decrypted_dob = mdecrypt_generic($td7, $this->hex2bin2($data["Data"]["dob"]));
	    mcrypt_generic_deinit($td7); 
	    mcrypt_module_close($td7); 
	    $dob1 = trim($decrypted_dob);
		$dob = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $dob1);
		
		$this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		$userDetails2 = $this->User->find("first", array("conditions" => array("mobile" => $mobile_num)));
		$user_id2 = $userDetails2['User']['id'];
		if(empty($userDetails['User']['id']))
		 {
		$this->_invalid_request($data);
		 }
		else if(!empty($user_id2) && $user_id!=$user_id2)
		{
		$this->_set_response($data, 0, "", "Mobile Number already in use!", array());
		}
		else
		 {
		$this->loadModel("User");
		$this->User->recursive = -1;
	    $this->User->id = $user_id;
		$this->User->saveField('firstname', $firstname);
		$this->User->saveField('lastname', $lastname);
		$this->User->saveField('email', $email);
		$this->User->saveField('mobile', $mobile_num);
		$this->loadModel('UserProfile');
	    $this->UserProfile->recursive = -1;
		$profileDetails = $this->UserProfile->find("first", array("conditions" => array("user_id" => $user_id)));
	    $this->UserProfile->id = $profileDetails['UserProfile']['id'];
	    $this->UserProfile->saveField('alternate_no', $alternate_no);
	    $this->UserProfile->saveField('gender', $gender); 
		$this->UserProfile->saveField('dob', $dob); 
		$this->UserProfile->saveField('blood_group', str_replace( " ","+",$data["Data"]["blood_group"])); 
		$this->_set_response($data, 1, "Subscription Info Updated successfully", "", array());
		 }
	}
	
	/*End Profile Update*/
	
	/*Notification View*/
	private function _notification_view($data)
	{
		$cipher = "rijndael-128"; 
	    $mode = "cbc"; 
	    $secret_key = "D4:6E:AC:3F:F0:BE"; 
		 //iv length should be 16 bytes 
	    $iv = "fedcba9876543210"; 
	    $key_len = strlen($secret_key); 
	    if($key_len < 16 ){ 
	    $addS = 16 - $key_len; 
	    for($i =0 ;$i < $addS; $i++){ 
	    $secret_key.=" "; 
		} 
		}
		else{ 
	    $secret_key = substr($secret_key, 0, 16); 
		 } 
	    $td = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td, $secret_key, $iv); 
	    $decrypted_userid = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
	    mcrypt_generic_deinit($td); 
	    mcrypt_module_close($td); 
	    $user_id = trim($decrypted_userid);
		$this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		if(empty($userDetails['User']['id']))
		 {
		$this->_invalid_request($data);
		 }
		else
		 {
		$i = 0; 
		$this->loadModel("Notification");
	    $this->Notification->recursive = -1;
		$query = "select notification_id,title,message,image from notifications t1 inner join notification_details t2 on t1.id=t2.notification_id where user_id=$user_id AND notification_active=1";
		$reportData =$this->Notification->query($query);
		$responseData = array();
		foreach ($reportData as $value1)
		{
		$responseData[$i]['id'] = $value1['t2']['notification_id'];
		$responseData[$i]['title'] = $value1['t1']['title'];
	    $responseData[$i]['message'] = $value1['t1']['message'];
		if(!empty($value1['t1']['image']))
		{
		$responseData[$i]['image'] = 'http://apps1.onetouchresponse.com/push_notification_img/'.$value1['t1']['image'];
		}
		$i++;
		}
		$notificationview1['notification_details']=array();
		$notificationview1['notification_details'] = $responseData;
		$this->_set_response($data, 1, "Success", "",$notificationview1);
		}
	 }
	 
	 private function _notification_swipe($data)
	{
		$cipher = "rijndael-128"; 
	    $mode = "cbc"; 
	    $secret_key = "D4:6E:AC:3F:F0:BE"; 
		 //iv length should be 16 bytes 
	    $iv = "fedcba9876543210"; 
	    $key_len = strlen($secret_key); 
	    if($key_len < 16 ){ 
	    $addS = 16 - $key_len; 
	    for($i =0 ;$i < $addS; $i++){ 
	    $secret_key.=" "; 
		} 
		}
		else{ 
	    $secret_key = substr($secret_key, 0, 16); 
		 } 
	    $td = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td, $secret_key, $iv); 
	    $decrypted_userid = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
	    mcrypt_generic_deinit($td); 
	    mcrypt_module_close($td); 
	    $user_id = trim($decrypted_userid);
		
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
	    mcrypt_generic_init($td1, $secret_key, $iv); 
	    $decrypted_notification_id = mdecrypt_generic($td1, $this->hex2bin2($data["Data"]["notification_id"]));
	    mcrypt_generic_deinit($td1); 
	    mcrypt_module_close($td1); 
	    $notification_id = trim($decrypted_notification_id);
		
		$this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		if(empty($userDetails['User']['id']))
		 {
		$this->_invalid_request($data);
		 }
		else
		 {
		$this->loadModel("NotificationDetail");
	    $this->NotificationDetail->recursive = -1;
		$notificationDetails = $this->NotificationDetail->find("first", array("conditions" => array("notification_id" => $notification_id,"user_id" => $user_id)));
			if(empty($notificationDetails['NotificationDetail']['id']))
			{
			$this->_invalid_request($data);
			}
			else
			{
			$this->NotificationDetail->id = $notificationDetails['NotificationDetail']['id'];
			$this->NotificationDetail->saveField('notification_active', 0);
			$this->_set_response($data, 1, "Success", "",array());
			}
		
		 }
		
	 }
	 
	/*End Notification View*/
    /*Family Add-On*/
	private function _view_family_addon($data)
	{
		
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_userid = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$user_id = trim($decrypted_userid);
		$this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		if(empty($userDetails['User']['id']))
		 {
		$this->_invalid_request($data);
		 }
		else
		 {
		$i = 0; 
		$this->loadModel("User");
		$this->User->recursive = -1;
	    $addonDetails = $this->User->find("all", array("conditions" => array("modified_by" => $user_id,"customer_code"=>"ADD-ON USER")));
		$responseData = array();
		foreach ($addonDetails as $value1)
		{
		$responseData[$i]['Addon_Id'] = $value1['User']['id'];
	    $responseData[$i]['username'] = $value1['User']['username'];
	    $responseData[$i]['mobile_number'] = $value1['User']['mobile'];
		$i++;
		}
		$this->_set_response($data, 1, "Success", "", $responseData);
		 }
	}
	
	private function _edit_family_addon($data)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_userid = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$user_id = trim($decrypted_userid);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_addon_id = mdecrypt_generic($td1, $this->hex2bin2($data["Data"]["addon_id"]));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$addon_id = trim($decrypted_addon_id);
		$this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		$userDetails1 = $this->User->find("first", array("conditions" => array("id" => $addon_id,"modified_by"=>$user_id)));
		if(empty($userDetails['User']['id']))
		 {
		$this->_invalid_request($data);
		 }
		else if(empty($userDetails1['User']['id']))
		 {
		$this->_invalid_request($data); 
		 }
		else
		{ 
		$this->loadModel("CustomerRecord");
		$this->CustomerRecord->recursive = -1;
	    $addonDetails = $this->CustomerRecord->find("first", array("conditions" => array("id" => $addon_id)));
		$responseData = array();
	    $responseData['Addon_Id'] = $addonDetails['CustomerRecord']['id'];
	    $responseData['first_name'] = $addonDetails['CustomerRecord']['firstname'];
	    $responseData['last_name'] = $addonDetails['CustomerRecord']['lastname'];
	    $responseData['mobile_number'] = $addonDetails['CustomerRecord']['mobile'];
		$responseData['email_id'] = $addonDetails['CustomerRecord']['email'];
		$responseData['gender'] = $addonDetails['CustomerRecord']['gender'];
		$responseData['DOB'] = date( "d-m-Y", strtotime($addonDetails['CustomerRecord']['dob']));
		$responseData['blood_group'] = $addonDetails['CustomerRecord']['blood_group'];
		$this->_set_response($data, 1, "Success", "", $responseData);
		 }
	}
	
	private function _update_family_addon($data)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_userid = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$user_id = trim($decrypted_userid);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_addon_id = mdecrypt_generic($td1, $this->hex2bin2($data["Data"]["addon_id"]));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$addon_id = trim($decrypted_addon_id);
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td2, $secret_key, $iv); 
		$decrypted_mobile = mdecrypt_generic($td2, $this->hex2bin2($data["Data"]["mobile"]));
		mcrypt_generic_deinit($td2); 
		mcrypt_module_close($td2); 
		$mobile3 = trim($decrypted_mobile);
		$mobile = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$mobile3);
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td3, $secret_key, $iv); 
		$decrypted_email = mdecrypt_generic($td3, $this->hex2bin2($data["Data"]["email"]));
		mcrypt_generic_deinit($td3); 
		mcrypt_module_close($td3); 
		$email3 = trim($decrypted_email);
		$email = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$email3);
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td5, $secret_key, $iv); 
		$decrypted_firstname = mdecrypt_generic($td5, $this->hex2bin2($data["Data"]["firstname"]));
		mcrypt_generic_deinit($td5); 
		mcrypt_module_close($td5); 
		$firstname3 = trim($decrypted_firstname);
		$firstname = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$firstname3);
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td6, $secret_key, $iv); 
		$decrypted_lastname = mdecrypt_generic($td6, $this->hex2bin2($data["Data"]["lastname"]));
		mcrypt_generic_deinit($td6); 
		mcrypt_module_close($td6); 
		$lastname3 = trim($decrypted_lastname);
		$lastname = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$lastname3);
		$td10 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td10, $secret_key, $iv); 
		$decrypted_dob = mdecrypt_generic($td10, $this->hex2bin2($data["Data"]["dob"]));
		mcrypt_generic_deinit($td10); 
		mcrypt_module_close($td10); 
		$dob3 = trim($decrypted_dob);
		$dob = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$dob3);
		$td15 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td15, $secret_key, $iv); 
		$decrypted_blood_group = mdecrypt_generic($td15, $this->hex2bin2($data["Data"]["blood_group"]));
		mcrypt_generic_deinit($td15); 
		mcrypt_module_close($td15); 
		$blood_group3 = trim($decrypted_blood_group);
		$blood_group = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$blood_group3);
		$td16 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td16, $secret_key, $iv); 
		$decrypted_gender = mdecrypt_generic($td16, $this->hex2bin2($data["Data"]["gender"]));
		mcrypt_generic_deinit($td16); 
		mcrypt_module_close($td16); 
		$gender3 = trim($decrypted_gender);
		$gender = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$gender3);
		$this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		$userDetails1 = $this->User->find("first", array("conditions" => array("id" => $addon_id,"modified_by"=>$user_id)));
		if(empty($userDetails['User']['id']))
		 {
		$this->_invalid_request($data);
		 }
		else if(empty($userDetails1['User']['id']))
		 {
		$this->_invalid_request($data); 
		 }
		else
		{ 
		$this->loadModel("User");
		$this->User->recursive = -1;
	    $this->User->id = $addon_id;
		$this->User->saveField('firstname', $firstname);
		$this->User->saveField('lastname', $lastname);
		$this->User->saveField('email', $email);
		$this->User->saveField('mobile', $mobile);
		$this->loadModel('UserProfile');
	    $this->UserProfile->recursive = -1;
		$profileDetails = $this->UserProfile->find("first", array("conditions" => array("user_id" => $addon_id)));
	    $this->UserProfile->id = $profileDetails['UserProfile']['id'];
	    $this->UserProfile->saveField('gender', $gender); 
		$this->UserProfile->saveField('dob', $dob); 
		$this->UserProfile->saveField('blood_group', str_replace( " ","+",$blood_group)); 
		$this->_set_response($data, 1, "Add_On Updated successfully", "", array());
		}
	}
	
	private function _add_family_addon($data)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_userid = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$user_id = trim($decrypted_userid);
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td2, $secret_key, $iv); 
		$decrypted_mobile = mdecrypt_generic($td2, $this->hex2bin2($data["Data"]["mobile"]));
		mcrypt_generic_deinit($td2); 
		mcrypt_module_close($td2); 
		$mobile3 = trim($decrypted_mobile);
		$mobile = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$mobile3);
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td3, $secret_key, $iv); 
		$decrypted_email = mdecrypt_generic($td3, $this->hex2bin2($data["Data"]["email"]));
		mcrypt_generic_deinit($td3); 
		mcrypt_module_close($td3); 
		$email3 = trim($decrypted_email);
		$email = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$email3);
		$td4 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td4, $secret_key, $iv); 
		$decrypted_password1 = mdecrypt_generic($td4, $this->hex2bin2($data["Data"]["password1"]));
		mcrypt_generic_deinit($td4); 
		mcrypt_module_close($td4); 
		$password3 = trim($decrypted_password1);
		$password = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$password3);
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td5, $secret_key, $iv); 
		$decrypted_firstname = mdecrypt_generic($td5, $this->hex2bin2($data["Data"]["firstname"]));
		mcrypt_generic_deinit($td5); 
		mcrypt_module_close($td5); 
		$firstname3 = trim($decrypted_firstname);
		$firstname = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$firstname3);
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td6, $secret_key, $iv); 
		$decrypted_lastname = mdecrypt_generic($td6, $this->hex2bin2($data["Data"]["lastname"]));
		mcrypt_generic_deinit($td6); 
		mcrypt_module_close($td6); 
		$lastname3 = trim($decrypted_lastname);
		$lastname = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$lastname3);
		$td7 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td7, $secret_key, $iv); 
		$decrypted_coupon_code = mdecrypt_generic($td7, $this->hex2bin2($data["Data"]["coupon_code"]));
		mcrypt_generic_deinit($td7); 
		mcrypt_module_close($td7); 
		$coupon_code3 = trim($decrypted_coupon_code);
		$coupon_code = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$coupon_code3);
		$td8 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td8, $secret_key, $iv); 
		$decrypted_address1 = mdecrypt_generic($td8, $this->hex2bin2($data["Data"]["address1"]));
		mcrypt_generic_deinit($td8); 
		mcrypt_module_close($td8); 
		$address12 = trim($decrypted_address1);
		$address1 = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$address12);
		$td9 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td9, $secret_key, $iv); 
		$decrypted_address2 = mdecrypt_generic($td9, $this->hex2bin2($data["Data"]["address2"]));
		mcrypt_generic_deinit($td9); 
		mcrypt_module_close($td9); 
		$address21 = trim($decrypted_address2);
		$address2 = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$address21);
		$td10 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td10, $secret_key, $iv); 
		$decrypted_dob = mdecrypt_generic($td10, $this->hex2bin2($data["Data"]["dob"]));
		mcrypt_generic_deinit($td10); 
		mcrypt_module_close($td10); 
		$dob3 = trim($decrypted_dob);
		$dob = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$dob3);
		$td11 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td11, $secret_key, $iv); 
		$decrypted_city = mdecrypt_generic($td11, $this->hex2bin2($data["Data"]["city"]));
		mcrypt_generic_deinit($td11); 
		mcrypt_module_close($td11); 
		$city3 = trim($decrypted_city);
		$city = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$city3);
		$td12 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td12, $secret_key, $iv); 
		$decrypted_state = mdecrypt_generic($td12, $this->hex2bin2($data["Data"]["state"]));
		mcrypt_generic_deinit($td12); 
		mcrypt_module_close($td12); 
		$state3 = trim($decrypted_state);
		$state = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$state3);
		$td13 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td13, $secret_key, $iv); 
		$decrypted_country = mdecrypt_generic($td13, $this->hex2bin2($data["Data"]["country"]));
		mcrypt_generic_deinit($td13); 
		mcrypt_module_close($td13); 
		$country2 = trim($decrypted_country);
		$country = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$country2);
		$td14 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td14, $secret_key, $iv); 
		$decrypted_pincode = mdecrypt_generic($td14, $this->hex2bin2($data["Data"]["pincode"]));
		mcrypt_generic_deinit($td14); 
		mcrypt_module_close($td14); 
		$pincode3 = trim($decrypted_pincode);
		$pincode = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$pincode3);
		$td15 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td15, $secret_key, $iv); 
		$decrypted_blood_group = mdecrypt_generic($td15, $this->hex2bin2($data["Data"]["blood_group"]));
		mcrypt_generic_deinit($td15); 
		mcrypt_module_close($td15); 
		$blood_group3 = trim($decrypted_blood_group);
		$blood_group = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$blood_group3);
		$td16 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td16, $secret_key, $iv); 
		$decrypted_gender = mdecrypt_generic($td16, $this->hex2bin2($data["Data"]["gender"]));
		mcrypt_generic_deinit($td16); 
		mcrypt_module_close($td16); 
		$gender3 = trim($decrypted_gender);
		$gender = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$gender3);
	    $this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		$userDetails1 = $this->User->find("first", array("conditions" => array("mobile" => $mobile)));
		$userDetails2 = $this->User->find("first", array("conditions" => array("email" => $email)));
		if(empty($userDetails['User']['id']))
		 {
		$this->_invalid_request($data);
		 }
		else if(!empty($userDetails1))
		 {
		$this->_set_response($data, 1, "", "Mobile Number Already In Use!!", array());
		 }
		else if(!empty($userDetails2))
		 {
		$this->_set_response($data, 1, "", "Email Id Already In Use!!", array());
		 }  
		else
		 {
		$this->loadModel("User");
        $this->User->recursive = -1;
		$profile = array();
		$profile['User']['group_id'] = 3;
		$profile['User']['parent_user_id'] = $userDetails['User']['parent_user_id'];
		$profile['User']['username'] = $email;
		$profile['User']['password'] = $password;
		$profile['User']['firstname'] = $firstname;
		$profile['User']['lastname'] = $lastname;
		$profile['User']['email'] = $email;
		$profile['User']['mobile'] = $mobile;
		$profile['User']['customer_code'] = 'ADD-ON USER';
		$profile['User']['terms_accepted'] = 1;
		$profile['User']['chk_no_objection'] = 1;
		$profile['User']['mobile_registration'] = '1';
		$profile['User']['fully_registered'] = '1';
		$profile['User']['unique_key'] = md5($email);
		$profile['User']['upmanual'] = $password;
		$profile['User']['coupon_code'] = $coupon_code;
		$profile['User']['is_active'] = '0';
		$profile['User']['is_deleted'] = '0';
		$profile['User']['created_by'] = $user_id;
		$profile['User']['modified_by'] = $user_id;
		$this->User->create();
	    if($this->User->save($profile))
			{
			$user_profile = array();
			$this->loadModel("Coupon");
			$this->Coupon->recursive = -1;
			$couponArr = $this->Coupon->find("first", array("conditions" => array("name" => $coupon_code)));
			$packageId = $couponArr["Coupon"]["package_id"];
			$parent_user = $couponArr["Coupon"]["primary_user_id"];
			$primaryUserID = $this->User->id;
			$this->loadModel("UserProfile");
            $this->UserProfile->recursive = -1;
			$user_profile["UserProfile"]["user_id"] = $this->User->id;
			$user_profile["UserProfile"]["gender"] = $gender;
			$user_profile["UserProfile"]["blood_group"] = str_replace( " ","+",$blood_group);
			$user_profile["UserProfile"]["street_address1"] = $address1;
			$user_profile["UserProfile"]["street_address2"] = $address2;
			$user_profile["UserProfile"]["dob"] = $dob;
			$user_profile["UserProfile"]["city"] = $city;
			$user_profile["UserProfile"]["state"] = $state;
			$user_profile["UserProfile"]["country"] = $country;
			$user_profile["UserProfile"]["pincode"] = $pincode;
            $this->User->UserProfile->create();
			if($this->User->UserProfile->save($user_profile))
							{
				$orderData = array();			
			    $this->loadModel('Package');
		        $this->Package->id = $packageId;
				//Amount Calculations
		        $totalAmount = $packageAmount = $this->Package->field('amount');
				$adjustAmount = 0;	
				$couponDiscount = 0;			
				$this->loadModel("Order");
		        $this->Order->recursive = -1;
				$orderData['Order']['package_amount'] = $packageAmount;
				$orderData['Order']['tax_amount'] = 0;
				$orderData['Order']['total_amount'] = $totalAmount;
				$orderData['Order']['adjust_amount'] = $adjustAmount;
				$orderData['Order']['coupon_discount'] = $couponDiscount;
				$orderData['Order']['payable_amount'] = 0;
		
				$orderData['Order']['payment_mode'] = 1; //Online
				$orderData['Order']['order_status'] = 0; //Pending
		
				$orderData['Order']['created_by'] = $primaryUserID;
				$orderData['Order']['modified_by'] = $primaryUserID;
		
				$this->Order->create();
				$this->Order->save($orderData);
				$orderId = $this->Order->id;
				
				//Creates order details
				$this->loadModel("OrderDetail");
	            $this->OrderDetail->recursive = -1;
				$orderDetailData = array();
				$date = date("d-m-Y", time());
				$odArr = $this->Order->OrderDetail->find("first", array(
                          "conditions" => array("OrderDetail.user_id" => $parent_user), 
                          "fields" => array("updated_end_date", "used_incidents")));
			    $used = $odArr["OrderDetail"]["used_incidents"];
                $finalendDate = $odArr["OrderDetail"]["updated_end_date"];			  
				$orderDetailData['OrderDetail']['order_id'] = $this->Order->id;
				$orderDetailData['OrderDetail']['user_id'] = $primaryUserID;
				$orderDetailData['OrderDetail']['package_id'] = $packageId;
				$orderDetailData['OrderDetail']['start_date'] = $date;
				$orderDetailData['OrderDetail']['end_date'] = $finalendDate;
				$orderDetailData['OrderDetail']['updated_start_date'] = $date;
				$orderDetailData['OrderDetail']['updated_end_date'] = $finalendDate;
				$orderDetailData['OrderDetail']['total_incidents'] = $this->Package->field('number_of_incidents');
				$orderDetailData['OrderDetail']['total_safe_calls'] = $this->Package->field('number_of_safe_calls');
				$orderDetailData['OrderDetail']['used_incidents'] = $used;
				$orderDetailData['OrderDetail']['used_safe_calls'] = $used;
				$orderDetailData['OrderDetail']['package_amount'] = $packageAmount;
				$orderDetailData['OrderDetail']['tax_amount'] = 0;
				$orderDetailData['OrderDetail']['primary_user_id'] = $primaryUserID;
				$orderDetailData['OrderDetail']['total_amount'] = $totalAmount;
				$orderDetailData['OrderDetail']['adjust_amount'] = $adjustAmount;
				$orderDetailData['OrderDetail']['coupon_discount'] = $couponDiscount;
				$orderDetailData['OrderDetail']['payable_amount'] = 0;
				$orderDetailData['OrderDetail']['is_active'] = 1;
				$orderDetailData['OrderDetail']['created_by'] = $primaryUserID;
				$orderDetailData['OrderDetail']['modified_by'] = $primaryUserID;
		
				$this->Order->OrderDetail->create();
				$this->Order->OrderDetail->save($orderDetailData);		
				$this->Order->OrderDetail->User->id = $primaryUserID;
				$this->Order->OrderDetail->User->saveField('fully_registered', 1);
				$this->Order->OrderDetail->User->saveField('is_active', 1);	
				$this->Order->id = $this->Order->id;
                $this->Order->saveField('order_status', 1);
                $orderDetailId = $this->Order->OrderDetail->id;
                $this->Order->OrderDetail->id = $orderDetailId;
                $this->Order->OrderDetail->saveField('order_status', 1);
				$date1 = new DateTime($date);
				$date2 = new DateTime($finalendDate);
				$interval = $date1->diff($date2);
				$userorderInfo1 = array();
				$user_full_name = $userDetails['User']['firstname'].' '.$userDetails['User']['lastname'];
				$userorderInfo1["parent_user"]  =  $user_full_name;
				$userorderInfo1["firstname"]  =  $firstname;
				$userorderInfo1["username"]   =  $email;
				$userorderInfo1["password"]   =  $password;
				$userorderInfo1["expiry_date"] = $finalendDate;
				$userorderInfo1["service_days"] = $interval->days;
				$userorderInfo1["expire"] =  $finalendDate;
				//Send email to user
               $this->sendEmail($email, FROM_EMAIL, "Registration Detail", "family_add_on_detail", $userorderInfo1);
            
				//Send sms to user
				$messageForsms = "Greetings! Your One Touch  username is " . $email . " and Password is " . $password . ".  Download the  Onetouch  app http://bit.ly/1OCqpeS and sign in.For help call us on +1800-4191-911 . Stay Safe";
				$this->sendSingleSms($messageForsms, $this->_convertMobileNo($mobile));
				$coupon = $coupon_code;

				//Updates coupon usage
				if (!empty($coupon))
				{
					$this->loadModel('Coupon');
					$this->Coupon->updateUsage($coupon);
				}

	            $this->_set_response($data, 1, "Add_On User Added successfully", "", array());
				            }
		    }
		 }
	}
	/*State List*/
	private function _state_list($data)
	{
	$this->loadModel("location_managers");
    $this->location_managers->recursive = -1;
	$locationDetails = $this->location_managers->find("all",array('fields'=>'DISTINCT location_managers.state',"conditions" => array("NOT" => array('location_managers.pincode' => array(0)))));
	$i = 0;
	$responseData = array();
	foreach ($locationDetails as $value1)
	{
	$responseData[$i]= $value1['location_managers']['state'];
	$i++;
	}
	$state_list['state_list']=array();
	$state_list['state_list'] = $responseData;
	$this->_set_response($data, 1, "success", "", $state_list);
	}
	/*End State List*/
	
	/*City List*/
	private function _city_list($data)
	{
	$state_name = $data["Data"]["state"];
	$this->loadModel("location_managers");
    $this->location_managers->recursive = -1;
	$locationDetails = $this->location_managers->find("all",array("conditions" => array('state' => $state_name)));
	$i = 0;
	$responseData = array();
	foreach ($locationDetails as $value1)
	{
	$responseData[$i]= $value1['location_managers']['city'];
	$i++;
	}
	$city_list['city_list'] = array();
	$city_list['city_list'] = $responseData;
	$this->_set_response($data, 1, "success", "", $city_list);
	}
	/*End City List*/
	
	/*Category List*/
	private function _category_list($data)
	{
	$this->loadModel('categories');
    $this->categories->recursive = -1;
	$cat_details = $this->categories->find("all",array('fields'=>'DISTINCT categories.cat_name',"conditions" => array("NOT" => array('categories.cat_name' => array('')))));
	$i = 0;
	$responseData = array();
	foreach ($cat_details as $value1)
	{
	$responseData[$i]= $value1['categories']['cat_name'];
	$i++;
	}
	$category_list['state_list']=array();
	$category_list['state_list'] = $responseData;
	$this->_set_response($data, 1, "success", "", $category_list);
	}
	/*End Category List*/
	
	/*Subcategory List*/
	private function _subcategory_list($data)
	{
	$category_name = $data["Data"]["category"];
	$this->loadModel('categories');
    $this->categories->recursive = -1;
	$subcatDetails = $this->categories->find("all",array("conditions" => array('cat_name' => $category_name)));
	$i = 0;
	$responseData = array();
	foreach ($subcatDetails as $value1)
	{
	$responseData[$i]= $value1['categories']['sub_cate_name'];
	$i++;
	}
	$subcategory_list['subcategory_list'] = array();
	$subcategory_list['subcategory_list'] = $responseData;
	$this->_set_response($data, 1, "success", "", $subcategory_list);
	}
	/*End Subcategory List*/
	
	/*End Family Add-On*/
	 private function _buy_now($data)
	{
	$buynowinfo = array();
	$buynowinfo['Click_Buynow'] = "https://www.onetouchresponse.com/users/buy_now";
	$this->_set_response($data, 1, "Success", "", $buynowinfo);
	}
	
	private function _buy_now_test($data)
	{
	$buynowinfo = array();
	$buynowinfo['Click_Buynow'] = "https://www.onetouchresponse.com/users/buy_now_test";
	$this->_set_response($data, 1, "Success", "", $buynowinfo);
	}
	
	private function _disablebuynow($data)
	{
	$buynowinfo = array();
	$buynowinfo['isEnabled_buynow'] = "true";
	$this->_set_response($data, 1, "Buy Now Button Status", "", $buynowinfo);
	}
	
	private function _newdisablebuynow($data)
	{
	$buynowinfo = array();
	$buynowinfo['isEnabled_buynow'] = "true";
	$this->_set_response($data, 1, "Buy Now Button Status", "", $buynowinfo);
	}
	private function _disablebuynow_version($data)
	{
	$buynowinfo = array();
	$buynowinfo['isEnabled_buynow'] = "true";
	$this->_set_response($data, 1, "Buy Now Button Status", "", $buynowinfo);
	}
	private function _disablebuynow_version2($data)
	{
	$buynowinfo = array();
	$buynowinfo['isEnabled_buynow'] = "true";
	$this->_set_response($data, 1, "Buy Now Button Status", "", $buynowinfo);
	}
	
	private function _disablebuynow_version3($data)
	{
	$buynowinfo = array();
	$buynowinfo['isEnabled_buynow'] = "true";
	$this->_set_response($data, 1, "Buy Now Button Status", "", $buynowinfo);
	}
	
	/*Customer location update*/
	private function _customer_track($data)
	{
	    
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_userid = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$intUserId = trim($decrypted_userid);
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td5, $secret_key, $iv); 
		$decrypted_longitude1 = mdecrypt_generic($td5, $this->hex2bin2($data['Data']['longitude']));
		mcrypt_generic_deinit($td5); 
		mcrypt_module_close($td5); 
		$longitude = trim($decrypted_longitude1);
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td6, $secret_key, $iv); 
		$decrypted_latitude1 = mdecrypt_generic($td6, $this->hex2bin2($data['Data']['latitude']));
		mcrypt_generic_deinit($td6); 
		mcrypt_module_close($td6); 
		$latitude = trim($decrypted_latitude1);
		$this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $intUserId)));
	    if (!empty($userDetails))
			 {
		 //Get group id from the user id
		 $this->loadModel("User");
		 $this->User->recursive = -1;
		 $this->User->id = $intUserId;
		 $this->User->saveField('latitude', $latitude);
		 $this->User->saveField('longitude', $longitude);
		 $this->User->saveField('modified_on', date("Y-m-d H:i:s"));
		 $this->_set_response($data, 1, TRACKING_UPDATE_SUCCESS,'','');
			 }
		 else
			 {
		  $this->_invalid_request($data);
			 }	
	}
	/*end location update*/
	
	 private function _notification_user_update_info($data)
	 {
		 
		 $cipher = "rijndael-128"; 
		 $mode = "cbc"; 
		 $secret_key = "D4:6E:AC:3F:F0:BE"; 
		 //iv length should be 16 bytes 
		 $iv = "fedcba9876543210"; 
		 $key_len = strlen($secret_key); 
		 if($key_len < 16 ){ 
		 $addS = 16 - $key_len; 
		 for($i =0 ;$i < $addS; $i++){ 
		 $secret_key.=" "; 
		 } 
		 }else{ 
		 $secret_key = substr($secret_key, 0, 16); 
		 } 
		 $td = mcrypt_module_open($cipher, "", $mode, $iv); 
		 mcrypt_generic_init($td, $secret_key, $iv); 
		 $decrypted_user_id = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
		 mcrypt_generic_deinit($td); 
		 mcrypt_module_close($td); 
		 $user_Id3 = trim($decrypted_user_id);
		 $user_Id = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $user_Id3);
         $UD_Id = $data["Data"]["ud_id"];
		 $this->loadModel("User");
		 $this->User->recursive = -1;
		 $userDetail = $this->User->find("first", array("conditions" => array("User.id" => $user_Id)));
		 if(empty($userDetail['User']['id']))
		 {
		 $this->_set_response($data, 0, "", "There is Something Wrong!!", array()); 
		 }
		 else
		 {
		 $querys="update users set user_ud_id='$UD_Id',push_status=1  WHERE id='".$user_Id."'";
		 $reportData =$this->User->query($querys);
		 $this->_set_response($data, 1, "Registration Id Updated Successfully!!", "", array());
		 }
	 }
	 
	
	 private function _user_responder_info($data)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$user_id2 = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$user_id1 = trim($user_id2);
		$user_Id = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $user_id1);
		$this->loadModel("Incident");
	    $this->Incident->recursive = -1;
	    $userDetails = $this->Incident->find("first", array("conditions" => array("customer_user_id" => $user_Id),'order' => 'Incident.id DESC'));
		$incident_id = $userDetails['Incident']['id'];
		if(!empty($incident_id))
		{
		$this->loadModel("IncidentResponder");
	    $this->IncidentResponder->recursive = -1;
	    $responderDetails = $this->IncidentResponder->find("first", array("conditions" => array("incident_id" => $incident_id,"is_accepted" => 1,"assign_responder" => 1),'order' => 'IncidentResponder.id DESC'));
		$responderDetails1 = $this->IncidentResponder->find("first", array("conditions" => array("incident_id" => $incident_id,"is_accepted" => 1,"assign_fr" => 1),'order' => 'IncidentResponder.id DESC'));
		$responders_id = $responderDetails['IncidentResponder']['responder_user_id'];
		$fr_user_id = $responderDetails1['IncidentResponder']['fr_user_id'];
		if(!empty($responders_id))
		{
		$this->loadModel("User");
	    $this->User->recursive = -1;
		$UDetails = $this->User->find("first", array("conditions" => array("id" => $responders_id)));
		$reportData = array();
		$this->loadModel("Photo");
		$querys="select * from photos  WHERE user_id='".$responders_id."' AND incident_id=0 AND safe_call_id=0";
		$reportData =$this->Photo->query($querys);
		$userinfo = array();
		$userinfo['first_name'] = $UDetails['User']['firstname'];
		$userinfo['last_name'] = $UDetails['User']['lastname'];
		$userinfo['mobile_number'] = $UDetails['User']['mobile'];
		$userinfo['latitude'] = $UDetails['User']['latitude'];
		$userinfo['longitude'] = $UDetails['User']['longitude'];
		if(empty($reportData))
		{
		$userinfo['photo'] = "http://apps1.onetouchresponse.com/profiles_pic/avatar-missing.png";
		}
		else
		{
		$userinfo['photo'] = "http://apps1.onetouchresponse.com/profiles_pic/".$reportData[0]['photos']['photo'];
		}
		$this->_set_response($data, 1, "Success", "", $userinfo);
		}
		else if(!empty($fr_user_id))
		{
		$this->loadModel("first_responders");
	    $this->first_responders->recursive = -1;
		$UDetails = $this->first_responders->find("first", array("conditions" => array("id" => $fr_user_id)));
	    $userinfo = array();
		$userinfo['first_name'] = $UDetails['first_responders']['p_name'];
		$userinfo['last_name'] = '';
		$userinfo['mobile_number'] = $UDetails['first_responders']['p_mobile_number'];
		$userinfo['latitude'] = $UDetails['first_responders']['latitude'];
		$userinfo['longitude'] = $UDetails['first_responders']['longitude'];
		if(empty($UDetails['first_responders']['photo']))
		{
		$userinfo['photo'] = "http://apps1.onetouchresponse.com/profiles_pic/avatar-missing.png";
		}
		else
		{
		$userinfo['photo'] = "http://apps1.onetouchresponse.com/img/all_fr/".$UDetails['first_responders']['photo'];
		}
		$this->_set_response($data, 1, "Success", "", $userinfo);
		}
		else
		{
		$this->_set_response($data, 2, "", "Failed", $responder_id);
		}
		}
		else
		{
		$this->_set_response($data, 2, "", "Failed", $responder_id);
		}
	}
	
	
	
/*Start campaign page Webservice*/

    private function _otrcouponVerify($data)
	{
		
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_coupon_code = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["coupon_code"]));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$coupon_code = trim($decrypted_coupon_code);
		$this->loadModel("Coupon");
        $this->Coupon->recursive = -1;
		$couponArr = $this->Coupon->find("first", array("conditions" => array("name" => $coupon_code)));
		$packageId = $couponArr['Coupon']['package_id'];
	    $this->loadModel('Coupon');
        $result = $this->Coupon->verify_activation($coupon_code, $packageId);
        if ($result["validCoupon"] == 1)
        {
            $this->_set_response($data, 1, "Coupon applied successfully", "", array());
        }
        else
        {
            $this->_set_response($data, 0, "", INVALID_COUPON, array());
        }
		
	}
	
	 private function _accountactivation($data)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_coupon_code = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["coupon_code"]));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$coupon_code = trim($decrypted_coupon_code);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_mobile_number = mdecrypt_generic($td1, $this->hex2bin2($data["Data"]["mobile_number"]));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$mobile_number = trim($decrypted_mobile_number);
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td2, $secret_key, $iv); 
		$decrypted_alternate_number = mdecrypt_generic($td2, $this->hex2bin2($data["Data"]["alternate_number"]));
		mcrypt_generic_deinit($td2); 
		mcrypt_module_close($td2); 
		$alternate_number = trim($decrypted_alternate_number);
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td3, $secret_key, $iv); 
		$decrypted_firstname = mdecrypt_generic($td3, $this->hex2bin2($data["Data"]["firstname"]));
		mcrypt_generic_deinit($td3); 
		mcrypt_module_close($td3); 
		$firstname = trim($decrypted_firstname);
		$td4 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td4, $secret_key, $iv); 
		$decrypted_lastname = mdecrypt_generic($td4, $this->hex2bin2($data["Data"]["lastname"]));
		mcrypt_generic_deinit($td4); 
		mcrypt_module_close($td4); 
		$lastname = trim($decrypted_lastname);
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td5, $secret_key, $iv); 
		$decrypted_email = mdecrypt_generic($td5, $this->hex2bin2($data["Data"]["email"]));
		mcrypt_generic_deinit($td5); 
		mcrypt_module_close($td5); 
		$email = trim($decrypted_email);
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td6, $secret_key, $iv); 
		$decrypted_password = mdecrypt_generic($td6, $this->hex2bin2($data["Data"]["password"]));
		mcrypt_generic_deinit($td6); 
		mcrypt_module_close($td6); 
		$password = trim($decrypted_password);
		$td7 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td7, $secret_key, $iv); 
		$decrypted_gender = mdecrypt_generic($td7, $this->hex2bin2($data["Data"]["gender"]));
		mcrypt_generic_deinit($td7); 
		mcrypt_module_close($td7); 
		$gender = trim($decrypted_gender);
		$td8 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td8, $secret_key, $iv); 
		$decrypted_dob = mdecrypt_generic($td8, $this->hex2bin2($data["Data"]["dob"]));
		mcrypt_generic_deinit($td8); 
		mcrypt_module_close($td8); 
		$dob = trim($decrypted_dob);
		$td9 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td9, $secret_key, $iv); 
		$decrypted_country = mdecrypt_generic($td9, $this->hex2bin2($data["Data"]["country"]));
		mcrypt_generic_deinit($td9); 
		mcrypt_module_close($td9); 
		$country = trim($decrypted_country);
		$td10 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td10, $secret_key, $iv); 
		$decrypted_state = mdecrypt_generic($td10, $this->hex2bin2($data["Data"]["state"]));
		mcrypt_generic_deinit($td10); 
		mcrypt_module_close($td10); 
		$state = trim($decrypted_state);
		$td11 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td11, $secret_key, $iv); 
		$decrypted_city = mdecrypt_generic($td11, $this->hex2bin2($data["Data"]["city"]));
		mcrypt_generic_deinit($td11); 
		mcrypt_module_close($td11); 
		$city = trim($decrypted_city);
		$td12 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td12, $secret_key, $iv); 
		$decrypted_pincode = mdecrypt_generic($td12, $this->hex2bin2($data["Data"]["pincode"]));
		mcrypt_generic_deinit($td12); 
		mcrypt_module_close($td12); 
		$pincode = trim($decrypted_pincode);
		$td13 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td13, $secret_key, $iv); 
		$decrypted_address = mdecrypt_generic($td13, $this->hex2bin2($data["Data"]["address"]));
		mcrypt_generic_deinit($td13); 
		mcrypt_module_close($td13); 
		$address = trim($decrypted_address);
		$td14 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td14, $secret_key, $iv); 
		$decrypted_emergency_name = mdecrypt_generic($td14, $this->hex2bin2($data["Data"]["emergency_name"]));
		mcrypt_generic_deinit($td14); 
		mcrypt_module_close($td14); 
		$emergency_name = trim($decrypted_emergency_name);
		$td15 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td15, $secret_key, $iv); 
		$decrypted_emergency_number = mdecrypt_generic($td15, $this->hex2bin2($data["Data"]["emergency_number"]));
		mcrypt_generic_deinit($td15); 
		mcrypt_module_close($td15); 
		$emergency_number = trim($decrypted_emergency_number);
		$td16 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td16, $secret_key, $iv); 
		$decrypted_emergency_email = mdecrypt_generic($td16, $this->hex2bin2($data["Data"]["emergency_email"]));
		mcrypt_generic_deinit($td16); 
		mcrypt_module_close($td16); 
		$emergency_email = trim($decrypted_emergency_email);
		$td17 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td17, $secret_key, $iv); 
		$decrypted_relation = mdecrypt_generic($td17, $this->hex2bin2($data["Data"]["relation"]));
		mcrypt_generic_deinit($td17); 
		mcrypt_module_close($td17); 
		$relation = trim($decrypted_relation);
		$td18 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td18, $secret_key, $iv); 
		$decrypted_terms_conditions = mdecrypt_generic($td18, $this->hex2bin2($data["Data"]["terms_conditions"]));
		mcrypt_generic_deinit($td18); 
		mcrypt_module_close($td18); 
		$terms_conditions = trim($decrypted_terms_conditions);
		$td19 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td19, $secret_key, $iv); 
		$decrypted_no_objections = mdecrypt_generic($td19, $this->hex2bin2($data["Data"]["no_objections"]));
		mcrypt_generic_deinit($td19); 
		mcrypt_module_close($td19); 
		$no_objections = trim($decrypted_no_objections);
		$td20 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td20, $secret_key, $iv); 
		$decrypted_imei = mdecrypt_generic($td20, $this->hex2bin2($data["Data"]["imei"]));
		mcrypt_generic_deinit($td20); 
		mcrypt_module_close($td20); 
		$imei = trim($decrypted_imei);
		$td21 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td21, $secret_key, $iv); 
		$decrypted_registered_from = mdecrypt_generic($td21, $this->hex2bin2($data["Data"]["registered_from"]));
		mcrypt_generic_deinit($td21); 
		mcrypt_module_close($td21); 
		$registered_from = trim($decrypted_registered_from);
		$this->loadModel("Coupon");
        $this->Coupon->recursive = -1;
		$couponArr = $this->Coupon->find("first", array("conditions" => array("name" => $coupon_code,"modified_by"=> 1)));
		$packageId = $couponArr['Coupon']['package_id'];
	    $this->loadModel('Coupon');
        $result = $this->Coupon->verify($coupon_code, $packageId);
        if ($result["validCoupon"] != 1)
        {
            $this->_set_response($data, 0, "", INVALID_COUPON, array());
        }
		else if(!preg_match("^[789]\d{9}$^", $mobile_number))
         {
               $this->_set_response($data, 0, "", "Please enter a valid mobile number.", array());
         }
		else if($mobile_number==$alternate_number)
		 {
		 $this->_set_response($data, 0, "", "Alternate & Mobile number are same.", array());
		 } 
		else if($mobile_number==$emergency_number)
		 {
		 $this->_set_response($data, 0, "", "Please Use different emergency number.", array());
		 } 
		else if($email==$emergency_email)
		 {
		 $this->_set_response($data, 0, "", "Please Use different emergency email id.", array());
		 }
		else
		{  
		$this->loadModel("User");
		$this->User->recursive = -1;
		
		//Remove incomlete registration with same email
		$incompleteDetails = $this->User->find("all", array("conditions" => 
            array("username" => $email ,"is_active"=> 0, "fully_registered" => 0, "group_id" => CUSTOMER_GROUP_ID)));
		
		if(!empty($incompleteDetails))
		{
		    
			$this->User->id = $incompleteDetails[0]["User"]["id"];
			$this->User->delete();
			
		}
        
        $this->User->id = null;
		
		//Check email already exists in db or not
		(int) $userCountE = $this->User->find("count", array("conditions" => 
            array("User.email" => $email, "User.is_active" => 1, 
            "User.is_deleted" => 0, "User.group_id" => CUSTOMER_GROUP_ID, "User.fully_registered" => 1)));
		
		//Check mobile already exists in db or not
        (int) $userCountM = $this->User->find("count", array("conditions" => 
            array("User.mobile" => $mobile_number, "User.is_active" => 1, 
            "User.is_deleted" => 0, "User.group_id" => CUSTOMER_GROUP_ID, "User.fully_registered" => 1)));
		
		if($userCountE <= 0 && $userCountM <= 0)
		{
			$userInsertArr["User"] = array();
			$userInsertArr["User"]["group_id"] = CUSTOMER_GROUP_ID;
			$userInsertArr["User"]["coupon_code"] = $coupon_code;
			$userInsertArr["User"]["username"] = $email;
			$userInsertArr["User"]["password"] = $password;
            $userInsertArr["User"]["upmanual"] = $password;
			$userInsertArr["User"]["email"]   = $email;
			$userInsertArr["User"]["mobile"] =  $mobile_number;
			$userInsertArr["User"]["last_imei"] = $imei;
			$userInsertArr["User"]["unique_key"] = $imei;
			$userInsertArr["User"]["mobile_registration"] = 1;
			$userInsertArr["User"]["terms_accepted"] = 1;
			$userInsertArr["User"]["chk_no_objection"] = 1;
			$userInsertArr["User"]["registered_from"] = $registered_from;
			$userInsertArr["User"]["firstname"] = $firstname;
			$userInsertArr["User"]["lastname"] = $lastname;
			$userInsertArr["User"]["market_code"] = "OTR APP";
			
	    if($this->User->save($userInsertArr))
			{
			    $user_id = $this->User->id;
				$otp = $this->_generateRandomOTPcode(5);
				$messageForsms = "Your OTP code is ".$otp;
				$this->sendSingleSms($messageForsms, $this->_convertMobileNo($mobile_number));
				//Send email to admin for registration initialised
                $userArr = array("firstname" => $userInsertArr["User"]["firstname"], "lastname" => $userInsertArr["User"]["lastname"], "email"=>$userInsertArr["User"]["username"] ,"mobile" =>$userInsertArr["User"]["mobile"]);
		
		
               $this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "OTR Activation Initialise Details", 
                        "admin_profile_entered_signup", $userArr , array(), array(), StaticArrays::$initializedContactEmails);
		
               /*$this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "Campaign User Initialise Details", "admin_profile_entered_signup", $userArr);*/
		
		      //Send email to user
		
		      /* $userorderInfo1 = array("username" => $userInsertArr["User"]["username"], "password" => $userInsertArr["User"]["upmanual"], "otp_key" => $otp, "unique_key" =>$userInsertArr["User"]["unique_key"]);
		
              $this->sendEmail($userInsertArr["User"]["username"], FROM_EMAIL, "Activation User Detail", "user_campaign_detail", $userorderInfo1); */
			  $responseData = array("user_id" => $user_id,"package_id"=>$packageId,"OTP_Code" => $otp,"gender" => $gender,"city" => $city,"state" => $state,"country" => $country,"pincode" => $pincode,"address"=>$address,"alternate_number"=>$alternate_number,"dob"=>$dob,"emergency_name" => $emergency_name,"emergency_number" => $emergency_number,"emergency_email" => $emergency_email,"relation"=>$relation);
			  $this->_set_response($data, 1, "Inserted successfully", "", $responseData);
			
			}
		}
		else
		{
			if($userCountE > 0) :
				$this->_set_response($data, 2, "", "Email already exists", array());
			else:
				$this->_set_response($data, 2, "", "Mobile already exists", array());
			endif;
		}
		}	
		
	}


  private function _otr_otp_verification($data)
	{
		
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
	    $td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_package_id = mdecrypt_generic($td1, $this->hex2bin2($data["Data"]["package_id"]));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$package_id = trim($decrypted_package_id);
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td2, $secret_key, $iv); 
		$decrypted_alternate_number = mdecrypt_generic($td2, $this->hex2bin2($data["Data"]["alternate_number"]));
		mcrypt_generic_deinit($td2); 
		mcrypt_module_close($td2); 
		$alternate_number = trim($decrypted_alternate_number);
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td3, $secret_key, $iv); 
		$decrypted_user_id = mdecrypt_generic($td3, $this->hex2bin2($data["Data"]["user_id"]));
		mcrypt_generic_deinit($td3); 
		mcrypt_module_close($td3); 
		$user_id = trim($decrypted_user_id);
		$td7 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td7, $secret_key, $iv); 
		$decrypted_gender = mdecrypt_generic($td7, $this->hex2bin2($data["Data"]["gender"]));
		mcrypt_generic_deinit($td7); 
		mcrypt_module_close($td7); 
		$gender = trim($decrypted_gender);
		$td8 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td8, $secret_key, $iv); 
		$decrypted_dob = mdecrypt_generic($td8, $this->hex2bin2($data["Data"]["dob"]));
		mcrypt_generic_deinit($td8); 
		mcrypt_module_close($td8); 
		$dob = trim($decrypted_dob);
		$td9 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td9, $secret_key, $iv); 
		$decrypted_country = mdecrypt_generic($td9, $this->hex2bin2($data["Data"]["country"]));
		mcrypt_generic_deinit($td9); 
		mcrypt_module_close($td9); 
		$country = trim($decrypted_country);
		$td10 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td10, $secret_key, $iv); 
		$decrypted_state = mdecrypt_generic($td10, $this->hex2bin2($data["Data"]["state"]));
		mcrypt_generic_deinit($td10); 
		mcrypt_module_close($td10); 
		$state = trim($decrypted_state);
		$td11 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td11, $secret_key, $iv); 
		$decrypted_city = mdecrypt_generic($td11, $this->hex2bin2($data["Data"]["city"]));
		mcrypt_generic_deinit($td11); 
		mcrypt_module_close($td11); 
		$city = trim($decrypted_city);
		$td12 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td12, $secret_key, $iv); 
		$decrypted_pincode = mdecrypt_generic($td12, $this->hex2bin2($data["Data"]["pincode"]));
		mcrypt_generic_deinit($td12); 
		mcrypt_module_close($td12); 
		$pincode = trim($decrypted_pincode);
		$td13 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td13, $secret_key, $iv); 
		$decrypted_address = mdecrypt_generic($td13, $this->hex2bin2($data["Data"]["address"]));
		mcrypt_generic_deinit($td13); 
		mcrypt_module_close($td13); 
		$address = trim($decrypted_address);
		$td14 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td14, $secret_key, $iv); 
		$decrypted_emergency_name = mdecrypt_generic($td14, $this->hex2bin2($data["Data"]["emergency_name"]));
		mcrypt_generic_deinit($td14); 
		mcrypt_module_close($td14); 
		$emergency_name = trim($decrypted_emergency_name);
		$td15 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td15, $secret_key, $iv); 
		$decrypted_emergency_number = mdecrypt_generic($td15, $this->hex2bin2($data["Data"]["emergency_number"]));
		mcrypt_generic_deinit($td15); 
		mcrypt_module_close($td15); 
		$emergency_number = trim($decrypted_emergency_number);
		$td16 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td16, $secret_key, $iv); 
		$decrypted_emergency_email = mdecrypt_generic($td16, $this->hex2bin2($data["Data"]["emergency_email"]));
		mcrypt_generic_deinit($td16); 
		mcrypt_module_close($td16); 
		$emergency_email = trim($decrypted_emergency_email);
		$td17 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td17, $secret_key, $iv); 
		$decrypted_relation = mdecrypt_generic($td17, $this->hex2bin2($data["Data"]["relation"]));
		mcrypt_generic_deinit($td17); 
		mcrypt_module_close($td17); 
		$relation = trim($decrypted_relation);
		
		$this->loadModel("User");   
		$this->User->recursive = -1;
        $userProfileInsertArr["UserProfile"] = array();
		$userProfileInsertArr["UserProfile"]["user_id"] = $user_id;
        $userProfileInsertArr["UserProfile"]["gender"] = $gender;
		$userProfileInsertArr["UserProfile"]["dob"] = $dob;
		$userProfileInsertArr["UserProfile"]["alternate_no"] = $alternate_number;
		$userProfileInsertArr["UserProfile"]["street_address1"] = $address;
		$userProfileInsertArr["UserProfile"]["city"] = $city;
		$userProfileInsertArr["UserProfile"]["state"] = $state;
		$userProfileInsertArr["UserProfile"]["country"] = $country;
        $userProfileInsertArr["UserProfile"]["pincode"] = $pincode;
		$userProfileInsertArr["UserProfile"]["emergency_name1"] = $emergency_name;
		$userProfileInsertArr["UserProfile"]["emergency_phone1"] = $emergency_number;
		$userProfileInsertArr["UserProfile"]["emergency_email1"] = $emergency_email;
		$userProfileInsertArr["UserProfile"]["emergency_relation1"] = $relation;
		$userProfileInsertArr["UserProfile"]["created_by"] = $user_id;
		$userProfileInsertArr["UserProfile"]["modified_by"] = $user_id;
				
		if($this->User->UserProfile->save($userProfileInsertArr))
		{
			//Get package details
			$this->loadModel("Package");
			
			$pkg_id = $package_id;
			
			$packageDetail = $this->Package->find("first",array("conditions" => 
                array("Package.id" => $pkg_id, "Package.is_active" => 1)));
			
			$this->loadModel("User");   
		    $this->User->recursive = -1;
			$usersdetail = $this->User->find("first", array("conditions" => array("id" => $user_id)));
			$coupon_code = $usersdetail['User']['coupon_code'];
			$fname = $usersdetail['User']['firstname'];
			$lname = $usersdetail['User']['lastname'];	
			
			if(!empty($packageDetail))
			{
				
			    $orderArr["Order"] = array();
				$orderArr["Order"]["package_amount"] = $packageDetail["Package"]["amount"];
				$orderArr["Order"]["tax_amount"] = 0;
				$orderArr["Order"]["total_amount"] = $packageDetail["Package"]["amount"];
				$orderArr["Order"]["payable_amount"] = $packageDetail["Package"]["amount"];
				$orderArr["Order"]["coupon_discount"] = 1;
				$orderArr["Order"]["coupon_code"] = $coupon_code;
				$orderArr["Order"]["payment_mode"] = 1;
				$orderArr["Order"]["order_status"] = 0;
				$orderArr["Order"]["created_by"] = $user_id;
				$orderArr["Order"]["modified_by"] = $user_id;
				
				$this->loadModel("Order");
				$this->Order->create();
				
				if($this->Order->save($orderArr))
				{
					//disable last package if availble
					$q = "UPDATE order_details SET order_status = 0, is_active = 0 
                             WHERE user_id = ".$user_id.";";
					$this->Order->query($q);
					
					$orderId = $this->Order->id;
					$date = date("Y-m-d");
					$strendDate = strtotime($date . ' +' . $packageDetail["Package"]["duration_in_months"] . ' months');
					$convendDate = date("d-m-Y", $strendDate);
					if($pkg_id==43)
					{
					$endDate = '30-06-2016';
					}
					else
					{
					$minendDate = strtotime($convendDate . ' -1 days');
					$endDate = date("d-m-Y", $minendDate);
					}
				    $orderDetailArr["OrderDetail"] = array();
					$orderDetailArr["OrderDetail"]["user_id"] = $user_id;
					$orderDetailArr["OrderDetail"]["order_id"] = $orderId;
					$orderDetailArr["OrderDetail"]["package_id"] = $pkg_id;
					$orderDetailArr["OrderDetail"]["start_date"] = $date;
					$orderDetailArr["OrderDetail"]["end_date"] = $endDate;
					$orderDetailArr["OrderDetail"]["updated_start_date"] = $date;
					$orderDetailArr["OrderDetail"]["updated_end_date"] = $endDate;
					$orderDetailArr["OrderDetail"]["total_incidents"] = $packageDetail["Package"]["number_of_incidents"];
					$orderDetailArr["OrderDetail"]["total_safe_calls"] = $packageDetail["Package"]["number_of_safe_calls"];
					$orderDetailArr["OrderDetail"]["package_amount"] = $orderArr["Order"]["package_amount"];
					$orderDetailArr["OrderDetail"]["tax_amount"] = $orderArr["Order"]["tax_amount"];
					$orderDetailArr["OrderDetail"]["total_amount"] = $orderArr["Order"]["total_amount"];
					$orderDetailArr["OrderDetail"]["payable_amount"] = $orderArr["Order"]["payable_amount"];
					$orderDetailArr["OrderDetail"]["coupon_discount"] = 1;
					$orderDetailArr["OrderDetail"]["coupon_code"] = $coupon_code;
					$orderDetailArr["OrderDetail"]["order_status"] = 0;
					$orderDetailArr["OrderDetail"]["is_active"] = 1;
					$orderDetailArr["OrderDetail"]["created_by"] = $user_id;
					$orderDetailArr["OrderDetail"]["modified_by"] = $user_id;
					
					$this->Order->OrderDetail->create();
					if($this->Order->OrderDetail->save($orderDetailArr))
					{
                        $orderDetailId = $this->Order->OrderDetail->id;
						$this->User->id = $user_id;
						$userUpdate["User"] = array();
						$userUpdate["User"]["fully_registered"] = 1;
						$userUpdate["User"]["is_active"] = 1;
						$this->User->save($userUpdate);
                        
                        //update last package if available
                        $q = "UPDATE order_details SET order_status = 0,
                                is_active = 0 WHERE user_id = ".$user_id. "
                                AND id <> ". $orderDetailId;
                        $this->Order->query($q);

                        $this->Order->id = $orderId;
                        $this->Order->saveField('order_status', 1);

                        $this->Order->OrderDetail->id = $orderDetailId;
                        $this->Order->OrderDetail->saveField('order_status', 1);
                        
                        $userInfoArr = $this->setUserInfoData($orderId);
					 //Send sms to emergency contacts
					 $InfoMsg = "Greetings from One Touch Response. You have been defined as an Emergency Contact by ".$fname . " ".$lname.". Stay Safe";
					 
					$this->sendMultipleSms($InfoMsg,array($this->_convertMobileNo($emergency_number)));
					if(!empty($emergency_email))
					{				  
					//Send email to emergency contacts
                    $this->sendEmail($emergency_email, FROM_EMAIL, "OTER", "user_emergency_contact_inform",array("firstname" => $fname, "lastname" => $lname));
					}
					/*Admin signup information*/					
					$userorderInfo23 = array();
					$userorderInfo23["fullname"]   =  $userInfoArr["fullname"];
					$userorderInfo23["email"]   =  $userInfoArr["email"];
					$userorderInfo23["mobile"]   =  $userInfoArr["mobile"];
					$userorderInfo23["package_name"]   = $packageDetail["Package"]["name"];
					$userorderInfo23["order_id"]   =  $userInfoArr["order_id"];
					$userorderInfo23["transaction_id"]   =  $userInfoArr["transaction_id"];
					$userorderInfo23["gender"]   =  $gender;
				    $userorderInfo23["coupon_code"]   =  $userInfoArr["coupon_code"];
					$userorderInfo23["emergency_name1"]   =  $emergency_name;
					$userorderInfo23["emergency_phone1"]   =  $emergency_number;
					$userorderInfo23["emergency_email1"]   =  $emergency_email;
					$userorderInfo23["emergency_relation1"]   = $relation;
					
					/*End admin signup information*/
					
						/*update customer group*/
						if($pkg_id == 36)
						{
						$this->loadModel("User");
						$this->User->recursive = -1;
						$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $user_id)));
						$this->User->saveField('parent_user_id', 40122);
						//Send email to admin
						$this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "Registration Detail", "admin_signup_inform", 
								$userorderInfo23, array(), array(), StaticArrays::$successSignupContactEmails);
			
						//Send sms to user
						$messageForsms = "Greetings! Your One Touch  username is " . $userInfoArr["username"] . " and Password is " . $userInfoArr["password"] . ".Download the  Onetouch app and sign in.For help call us on +1800-4191-911 . Stay Safe";
						$this->sendSingleSms($messageForsms, $this->_convertMobileNo($userInfoArr["mobile"]));
						$date1 = new DateTime($date);
						$date2 = new DateTime($endDate);
						$interval = $date1->diff($date2);
						$userorderInfo2 = array();
						$userorderInfo2["username"]   =  $userInfoArr["username"];
						$userorderInfo2["password"]   =  $userInfoArr["password"];
						$userorderInfo2["usage_limit"] = 0;
						$userorderInfo2["service_days"] = $interval->days;
						$userorderInfo2["expire"] = $endDate;
			            //Send email to user
						$this->sendEmail($userInfoArr['email'], FROM_EMAIL, "Welcome to One Touch Response", 
								"user_registration_detail", $userorderInfo2);
						}
						else if($pkg_id == 37)
						{
						$this->loadModel("User");
						$this->User->recursive = -1;
						$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $user_id)));
						$this->User->saveField('parent_user_id', 40435);
						//Send email to admin
						$this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "Registration Detail", "admin_signup_inform", 
								$userorderInfo23, array(), array(), StaticArrays::$successSignupContactEmails);
			
						//Send sms to user
						$messageForsms = "Greetings! Your One Touch  username is " . $userInfoArr["username"] . " and Password is " . $userInfoArr["password"] . ".Download the  Onetouch app and sign in.For help call us on +1800-4191-911 . Stay Safe";
						$this->sendSingleSms($messageForsms, $this->_convertMobileNo($userInfoArr["mobile"]));
						$date1 = new DateTime($date);
						$date2 = new DateTime($endDate);
						$interval = $date1->diff($date2);
						$userorderInfo2 = array();
						$userorderInfo2["username"]   =  $userInfoArr["username"];
						$userorderInfo2["password"]   =  $userInfoArr["password"];
						$userorderInfo2["usage_limit"] = 0;
						$userorderInfo2["service_days"] = $interval->days;
						$userorderInfo2["expire"] = $endDate;
			            //Send email to user
						$this->sendEmail($userInfoArr['email'], FROM_EMAIL, "Welcome to One Touch Response", 
								"user_registration_detail", $userorderInfo2);
						}
						else if($pkg_id == 40)
						{
						$this->loadModel("User");
						$this->User->recursive = -1;
						$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $user_id)));
						$this->User->saveField('parent_user_id', 40476);
						//Send email to admin
						$this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "Registration Detail", "admin_signup_inform", 
								$userorderInfo23, array(), array(), StaticArrays::$successSignupContactEmails);
			
						//Send sms to user
						$messageForsms = "Greetings! Your One Touch  username is " . $userInfoArr["username"] . " and Password is " . $userInfoArr["password"] . ".Download the  Onetouch app and sign in.For help call us on +1800-4191-911 . Stay Safe";
						$this->sendSingleSms($messageForsms, $this->_convertMobileNo($userInfoArr["mobile"]));
						$date1 = new DateTime($date);
						$date2 = new DateTime($endDate);
						$interval = $date1->diff($date2);
						$userorderInfo2 = array();
						$userorderInfo2["username"]   =  $userInfoArr["username"];
						$userorderInfo2["password"]   =  $userInfoArr["password"];
						$userorderInfo2["usage_limit"] = 0;
						$userorderInfo2["service_days"] = $interval->days;
						$userorderInfo2["expire"] = $endDate;
			            //Send email to user
						$this->sendEmail($userInfoArr['email'], FROM_EMAIL, "Welcome to One Touch Response", 
								"user_registration_detail", $userorderInfo2);
						}
						else if($pkg_id == 41)
						{
						$this->loadModel("User");
						$this->User->recursive = -1;
						$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $user_id)));
						$this->User->saveField('parent_user_id', 41007);
						//Send email to admin
						$this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "Registration Detail", "admin_signup_inform", 
								$userorderInfo23, array(), array(), StaticArrays::$successSignupContactEmails);
			
						//Send sms to user
						$messageForsms = "Greetings! Your One Touch  username is " . $userInfoArr["username"] . " and Password is " . $userInfoArr["password"] . ".Download the  Onetouch app and sign in.For help call us on +1800-4191-911 . Stay Safe";
						$this->sendSingleSms($messageForsms, $this->_convertMobileNo($userInfoArr["mobile"]));
						$date1 = new DateTime($date);
						$date2 = new DateTime($endDate);
						$interval = $date1->diff($date2);
						$userorderInfo2 = array();
						$userorderInfo2["username"]   =  $userInfoArr["username"];
						$userorderInfo2["password"]   =  $userInfoArr["password"];
						$userorderInfo2["usage_limit"] = 0;
						$userorderInfo2["service_days"] = $interval->days;
						$userorderInfo2["expire"] = $endDate;
			            //Send email to user
						$this->sendEmail($userInfoArr['email'], FROM_EMAIL, "Welcome to One Touch Response", 
								"user_registration_detail", $userorderInfo2);
						}
						else if($pkg_id == 42)
						{
						$this->loadModel("User");
						$this->User->recursive = -1;
						$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $user_id)));
						$this->User->saveField('parent_user_id', 41097);
						//Send email to admin
						$this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "Registration Detail", "admin_signup_inform", 
								$userorderInfo23, array(), array(), StaticArrays::$successSignupContactEmails);
			
						//Send sms to user
						$messageForsms = "Congratulations! Your services are activated now. Your user name is  " . $userInfoArr["username"] . " and Password is " . $userInfoArr["password"] . ". Click  http://bit.ly/1OCqpeS to download One Touch Response app & login through  your user name & password, For assistance please contact us at +91-124-4606921";
						$this->sendSingleSms($messageForsms, $this->_convertMobileNo($userInfoArr["mobile"]));
						$date1 = new DateTime($date);
						$date2 = new DateTime($endDate);
						$interval = $date1->diff($date2);
						$userorderInfo2 = array();
						$userorderInfo2["username"]   =  $userInfoArr["username"];
						$userorderInfo2["password"]   =  $userInfoArr["password"];
						$userorderInfo2["usage_limit"] = 0;
						$userorderInfo2["service_days"] = $interval->days;
						$userorderInfo2["expire"] = $endDate;
			            //Send email to user
						$this->sendEmail($userInfoArr['email'], FROM_EMAIL, "Welcome to One Touch Response", 
								"hcl_user_registration_detail", $userorderInfo2);
						}
						else if($pkg_id == 43)
						{
						$this->loadModel("User");
						$this->User->recursive = -1;
						$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $user_id)));
						$this->User->saveField('parent_user_id', 40992);
						//Send email to admin
						$this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "Registration Detail", "admin_signup_inform", 
								$userorderInfo23, array(), array(), StaticArrays::$successSignupContactEmails);
			
						//Send sms to user
					    $messageForsms = "Congratulations! Your services are activated now. Your user name is  " . $userInfoArr["username"] . " and Password is " . $userInfoArr["password"] . ". Click  http://bit.ly/1OCqpeS to download One Touch Response app & login through  your user name & password, For assistance please contact us at +91-124-4606921";
						$this->sendSingleSms($messageForsms, $this->_convertMobileNo($userInfoArr["mobile"]));
						$date1 = new DateTime($date);
						$date2 = new DateTime($endDate);
						$interval = $date1->diff($date2);
						$userorderInfo2 = array();
						$userorderInfo2["username"]   =  $userInfoArr["username"];
						$userorderInfo2["password"]   =  $userInfoArr["password"];
						$userorderInfo2["usage_limit"] = 0;
						$userorderInfo2["service_days"] = $interval->days;
						$userorderInfo2["expire"] = $endDate;
			            //Send email to user
						$this->sendEmail($userInfoArr['email'], FROM_EMAIL, "Welcome to One Touch Response", 
								"hcl_user_registration_detail", $userorderInfo2);
						}
						else
						{
                        $this->_generateFamilyCoupons($userInfoArr);
						}
						
                        //Updates coupon usage
                        if (!empty($coupon_code))
                          {
                        $this->loadModel('Coupon');
                        $this->Coupon->updateUsage($coupon_code);
                          }
                        $this->_set_response($data, 1, "Registered successfully", "", $orderArr);
					}
				}
			}
			else
			{
				$this->_set_response($data, 2, "", "Invalid package id", array());
			}
		}
		else
		{
			$this->_set_response(array(), 0, "", INVALID_REQUEST, array());
		}
		
	}
	
    private function _generateRandomOTPcode($length) 
	{
		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) 
		{
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	

	/*Resend otp*/
	private function _resend_otp($data)
	      {
		        $cipher = "rijndael-128"; 
				$mode = "cbc"; 
				$secret_key = "D4:6E:AC:3F:F0:BE"; 
				//iv length should be 16 bytes 
				$iv = "fedcba9876543210"; 
				$key_len = strlen($secret_key); 
				if($key_len < 16 ){ 
				$addS = 16 - $key_len; 
				for($i =0 ;$i < $addS; $i++){ 
				$secret_key.=" "; 
				} 
				}else{ 
				$secret_key = substr($secret_key, 0, 16); 
				} 
				$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
				mcrypt_generic_init($td1, $secret_key, $iv); 
				$decrypted_mobile_number = mdecrypt_generic($td1, $this->hex2bin2($data["Data"]["mobile_number"]));
				mcrypt_generic_deinit($td1); 
				mcrypt_module_close($td1); 
				$mobile_number = trim($decrypted_mobile_number);
				$mob="/^[7-9][0-9]*$/"; 
	            if(!preg_match($mob, $mobile_number))
				{
				$this->_set_response($data, 2, "", 'Invalid Mobile Number', '');
				}
				else if($mobile_number=='')
				{
				$this->_set_response($data, 2, "", 'Invalid Mobile Number', '');
				}
				else
				{
				$otp_code = array();
				$otp = $this->_generateRandomOTPcode(5);
				$messageForsms = "Your OTP code is ".$otp;
				$this->sendSingleSms($messageForsms, $this->_convertMobileNo($mobile_number));
				$otp_code['otp_code']=$otp;
				$this->_set_response($data, 1, "Success", "", $otp_code);
				}
	      }
	/*End resend otp*/
	
	/*End Supercab Webservice*/
	public function get_device_data()
	{
		$this->layout = "";
		$this->autoRender = false;
		$this->loadModel("DeviceDataLog");
		$this->loadModel("User");
        $this->loadModel("Setting");
        $adminMobileNumberArr = $this->Setting->findById(1);
        $adminMobileNumber = $this->_convertMobileNo($adminMobileNumberArr["Setting"]["admin_mobile_number"]);
		
		$inputData["DeviceDataLog"] = array();
		$inputData["DeviceDataLog"]["string_data"] = json_encode($_GET);
		
		if($this->DeviceDataLog->save($inputData))
		{
			$response['response_status'] = 1;
			$response['success_msg'] = DATA_INSERTED;
			$response['error_msg'] = "";
		}
		else
		{
			$response['response_status'] = 0;
			$response['success_msg'] = "";
			$response['error_msg'] = DATA_INSERTED_FAILED;
		}
		
		$username = substr($_GET["mobilenumber"], 2);
		
		$msg = $_GET["message"];
		
		$msg = explode("q=" , $msg);
		$msg = explode("&" , $msg[1]);
		$msg = explode("," , $msg[0]);
		
		$receivedon =  date('Y-m-d H:i:s' , strtotime($_GET["receivedon"]));
		
		//Get user information from username
		$this->User->recursive = 0;
        $this->User->contain("UserProfile");
		$userInfo = $this->User->find("first", array("conditions" => array("User.username" => $username, "User.is_active" => 1, 
            "User.is_deleted" => 0, "User.fully_registered" => 1)));
		
		if(!empty($userInfo)):

			$incidentData["Incident"] = array();
			$incidentData["Incident"]["customer_user_id"] = $userInfo["User"]["id"];
			$incidentData["Incident"]["latitude"] = $msg[0];
			$incidentData["Incident"]["longitude"] = $msg[1];
			$incidentData["Incident"]["start_timestamp"] = $receivedon;
			$incidentData["Incident"]["status"] = STATUS_LIVE;
			$incidentData["Incident"]["is_active"] = 1;
			
			$this->loadModel("Incident");
			if ($this->Incident->save($incidentData))
			{
				$incidentID = $this->Incident->id;
				$this->_send_request_to_responder($incidentID, $userInfo["User"]["id"], $msg[0], $msg[1]);
				
				//add an entry in incident tracking also
				$incidentTrackingData["IncidentTracking"] = array();
				$incidentTrackingData["IncidentTracking"]["incident_id"] = $incidentID;
				$incidentTrackingData["IncidentTracking"]["latitude"] = $msg[0];
				$incidentTrackingData["IncidentTracking"]["longitude"] = $msg[1];

				$this->Incident->IncidentTracking->create();
                if($this->Incident->IncidentTracking->save($incidentTrackingData))
                {
                    $messageIncidentInitialised = "We have received an Emergency Alert from " . $userInfo['User']['firstname'] . ' ' . $userInfo['User']['lastname'] ." and ".$userInfo['User']['mobile']." at " . date("Y-m-d H:i:s") . ". For the location click on http://maps.google.com/?ll=" . $msg[0] . "," . $msg[1]. " Assistance is being provided.";

                    $numbers = array($adminMobileNumber,
                            $this->_convertMobileNo($userInfo['UserProfile']['emergency_phone1']),
                            $this->_convertMobileNo($userInfo['UserProfile']['emergency_phone2']),
                            $this->_convertMobileNo($userInfo['UserProfile']['emergency_phone3']));

                    $this->sendMultipleSms($messageIncidentInitialised, $numbers);
                }
			}
			
		endif;
	}
	
	/*Upload Profile Pic*/
	public function upload_profile_pic()
	{
		header('Content-type: application/json'); //Set the header to json to recognise we are goming to rceive json
		$Rdata = file_get_contents('php://input'); // assighn all the received dump to variable
		
		if ($Rdata != '')
		{
			$myjson = json_decode($Rdata, true); // now decode the actual json received from the variable
			if($myjson["Authentication"]["name"]!="photo_upload_string")
			{
			$this->_set_response($myjson, 0, "", INVALID_AUTHENTICATION, array());
			}
			else if (!empty($myjson))
			{
				$this->_create_log($myjson);
				    $Data = $myjson["Data"]; //Extract Data array from json

				    $Auth = $myjson["Authentication"]; // extract authentication array from json

					$cipher = "rijndael-128"; 
					$mode = "cbc"; 
					$secret_key = "D4:6E:AC:3F:F0:BE"; 
					//iv length should be 16 bytes 
					$iv = "fedcba9876543210"; 
					$key_len = strlen($secret_key); 
					if($key_len < 16 ){ 
					$addS = 16 - $key_len; 
					for($i =0 ;$i < $addS; $i++){ 
					$secret_key.=" "; 
					} 
					}else{ 
					$secret_key = substr($secret_key, 0, 16); 
					} 
					$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
					mcrypt_generic_init($td1, $secret_key, $iv); 
					$decrypted_user_id = mdecrypt_generic($td1, $this->hex2bin2($myjson["Data"]["user_id"]));
					mcrypt_generic_deinit($td1); 
					mcrypt_module_close($td1); 
					$intUserId = trim($decrypted_user_id);
					$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
					mcrypt_generic_init($td3, $secret_key, $iv); 
					$decrypted_imgname = mdecrypt_generic($td3, $this->hex2bin2($Data["photo"]));
					mcrypt_generic_deinit($td3); 
					mcrypt_module_close($td3); 
					$imgname = trim($decrypted_imgname);
					$this->loadModel("User");
					$this->User->recursive = -1;
					$userDetails = $this->User->find("first", array("conditions" => array("id" => $intUserId)));
					$username=$userDetails['User']['username'];
					if ($intUserId != 0)
					{
					   if ($this->_profile_pic_upload($myjson, $intUserId))
									{
										
										$my_base64_string = $Data["photo_string_base64"]; //now get the received base64 string 

										$picname = $imgname; //get the picture name for saving the file
										//call the function to convert base64 to image
									    $image = $this->base64_to_jpeg($my_base64_string, $picname);
                                        mkdir("profiles_pic/".$username, 0777);
										//now copy the picture to desired folder
										copy($picname, 'profiles_pic/'.$username.'/' . $picname);
										
									 }   	
						
					}
					else
					{
						$this->_set_response($myjson, 0, "", INVALID_AUTHENTICATION, array());
					}
				
			}
			else
			{
				$this->_set_response($myjson, 0, "", INVALID_REQUEST, array());
			}
		}
		else
		{
			$this->_set_response(array(), 0, "", INVALID_REQUEST, array());
		}
		exit;
	}
	
  
   private function _profile_pic_upload($data, $userID)
	{
		
		    $this->loadModel('User');
		    $this->User->recursive = -1;
			$username= $this->User->find('first', array('conditions' => array('id' => $userID)));
			$this->loadModel('UserProfile');
		    $this->UserProfile->recursive = -1;
		    $usr_id = $this->UserProfile->find('first', array('conditions' => array('user_id' => $userID)));
			if(!empty($usr_id['UserProfile']['id']))
			{
			$cipher = "rijndael-128"; 
			$mode = "cbc"; 
			$secret_key = "D4:6E:AC:3F:F0:BE"; 
			//iv length should be 16 bytes 
			$iv = "fedcba9876543210"; 
			$key_len = strlen($secret_key); 
			if($key_len < 16 ){ 
			$addS = 16 - $key_len; 
			for($i =0 ;$i < $addS; $i++){ 
			$secret_key.=" "; 
			} 
			}else{ 
			$secret_key = substr($secret_key, 0, 16); 
			} 
			$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
			mcrypt_generic_init($td3, $secret_key, $iv); 
			$decrypted_photo = mdecrypt_generic($td3, $this->hex2bin2($data["Data"]["photo"]));
			mcrypt_generic_deinit($td3); 
			mcrypt_module_close($td3); 
			$photo = trim($decrypted_photo);
			$username1=$username['User']['username'];
			if(preg_match('~^[a-z_\.0-9]+\.(jp[e]?g|png|gif)$~i', $photo))
			  {
			  $this->UserProfile->id = $usr_id['UserProfile']['id'];
			  $this->UserProfile->saveField('profile_pic',$photo);
			  $profilepic['profile_pic']=array();
			  $profilepic['profile_pic'] = "http://apps1.onetouchresponse.com/profiles_pic/".$username1.'/'.$photo;
			  $this->_set_response($data, 1, PICTURE_UPLOAD_SUCCESS, "", $profilepic);
			  return true;
			  }
			else
			  {
			  $this->_set_response($data, 0, "", "Picture Upload format not valid", array());
			  return false;
			  }  
			
		    }
		    else
		    {
			$this->_invalid_request($data);
			return false;
		    }
		    
	}
	
	/*End Upload Profile Pic*/

	/*
	 * For uploading the photo from windows phone/Base64 string
	 */

	public function base64_image_upload()
	{
		header('Content-type: application/json'); //Set the header to json to recognise we are goming to rceive json
		$Rdata = file_get_contents('php://input'); // assighn all the received dump to variable
		
		if ($Rdata != '')
		{
			$myjson = json_decode($Rdata, true); // now decode the actual json received from the variable
			if($myjson["Authentication"]["name"]!="photo_upload_string")
			{
			$this->_set_response($myjson, 0, "", INVALID_AUTHENTICATION, array());
			}
			else if (!empty($myjson))
			{
				    $this->_create_log($myjson);
				    $Data = $myjson["Data"]; //Extract Data array from json

				    $Auth = $myjson["Authentication"]; // extract authentication array from json

					$cipher = "rijndael-128"; 
					$mode = "cbc"; 
					$secret_key = "D4:6E:AC:3F:F0:BE"; 
					//iv length should be 16 bytes 
					$iv = "fedcba9876543210"; 
					$key_len = strlen($secret_key); 
					if($key_len < 16 ){ 
					$addS = 16 - $key_len; 
					for($i =0 ;$i < $addS; $i++){ 
					$secret_key.=" "; 
					} 
					}else{ 
					$secret_key = substr($secret_key, 0, 16); 
					} 
					$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
					mcrypt_generic_init($td1, $secret_key, $iv); 
					$decrypted_user_id = mdecrypt_generic($td1, $this->hex2bin2($myjson["Data"]["user_id"]));
					mcrypt_generic_deinit($td1); 
					mcrypt_module_close($td1); 
					$intUserId = trim($decrypted_user_id);
					$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
					mcrypt_generic_init($td3, $secret_key, $iv); 
					$decrypted_imgname = mdecrypt_generic($td3, $this->hex2bin2($Data["photo"]));
					mcrypt_generic_deinit($td3); 
					mcrypt_module_close($td3); 
					$imgname = trim($decrypted_imgname);
					$this->loadModel("User");
					$this->User->recursive = -1;
					$userDetails = $this->User->find("first", array("conditions" => array("id" => $intUserId)));
					$username=$userDetails['User']['username'];
					if ($intUserId != 0)
					{
						//Get group id from the user id
						$this->loadModel("User");
						$this->User->recursive = -1;
						$groupArr = $this->User->findById($intUserId, array('fields' => 'group_id'));
						$groupID = $groupArr["User"]["group_id"];

						if ($groupID == CUSTOMER_GROUP_ID)
						{
							if ($this->_IsUserActive($intUserId, CUSTOMER_GROUP_ID))
							{
								if ($this->_IsPackageExpired($intUserId))
								{
									if ($this->_do_photo_upload($myjson, $intUserId))
									{
										
										$my_base64_string = $Data["photo_string_base64"]; //now get the received base64 string 

										$picname = $imgname; //get the picture name for saving the file
										//call the function to convert base64 to image
									    $image = $this->base64_to_jpeg($my_base64_string, $picname);
                                        mkdir("photos/".$username, 0777);
										//now copy the picture to desired folder
										copy($picname, 'photos/'.$username.'/' . $picname);

										//now copy the picture to desired folder
										/*copy($image, PHOTO_UPLOAD_ABS_PATH . $Auth["username"] . "/" . $picname);*/
									}   
								}
								else
								{
									$this->_set_response($myjson, 0, "", PACKAGE_EXPIRED, array());
								}
							}
							else
							{
								$this->_set_response($myjson, 0, "", INACTIVE_USER, array());
							}
						}
						else if($groupID == RESPONDER_GROUP_ID)
						{
						  $this->_set_response($myjson, 0, "", INVALID_GROUP, array());         
						}
						else
						{
						  $this->_set_response($myjson, 0, "", INVALID_GROUP, array());
						}
					}
					else
					{
						$this->_set_response($myjson, 0, "", INVALID_AUTHENTICATION, array());
					}
				
			}
			else
			{
				$this->_set_response($myjson, 0, "", INVALID_REQUEST, array());
			}
		}
		else
		{
			$this->_set_response(array(), 0, "", INVALID_REQUEST, array());
		}
		exit;
	}

	/**
	 * Private function to validate the request data
	 * 
	 * @param Array $data
	 * @return boolean
	 */
	private function _validate_request($data)
	{
		//Checks if it's valid request
		if (!isset($data['Authentication']['username']) || empty($data['Authentication']['username']) ||
						!isset($data['Authentication']['password']) || empty($data['Authentication']['password']) ||
						!isset($data['Authentication']['name']) || empty($data['Authentication']['name']))
		{
			return false;
		}
		
		return true;
	}

	/**
	 * Private function to validate the authentication details
	 * in the request data
	 * 
	 * @param Array $data
	 * @return boolean
	 */
	private function _validate_authentication($data)
	{
		$intUserId = 0;
		$UserCheckParams = array();
		$UserCheckParams['User.username'] = $this->StringInputCleaner($data['Authentication']['username']);
		$UserCheckParams['User.password'] = AuthComponent::password($data['Authentication']['password']);
		$UserCheckParams['User.is_deleted'] = 0;
        $UserCheckParams['User.is_active'] = 1;
		$this->loadModel('User');
		$arrUser = $this->User->find("all", array("conditions" => $UserCheckParams, 'fields' => 'id'));

		//Check if user is exist in the database or not
		if (isset($arrUser) && !empty($arrUser))
		{
			$intUserId = $arrUser[0]['User']['id'];
		}
		return $intUserId;
	}
	
	private function _validate_encrption_authentication($data)
	{
		$intUserId = 0;
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_pass = mdecrypt_generic($td, $this->hex2bin2($data['Authentication']['password']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$password1 = trim($decrypted_pass);
		$password = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $password1);
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_username = mdecrypt_generic($td, $this->hex2bin2($data['Authentication']['username']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$username1 = trim($decrypted_username);
		$username = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $username1);
		$UserCheckParams = array();
		$UserCheckParams['User.username'] = $this->StringInputCleaner($username);
		$UserCheckParams['User.password'] = AuthComponent::password($password);
		$UserCheckParams['User.is_deleted'] = 0;
        $UserCheckParams['User.is_active'] = 1;
		$this->loadModel('User');
		$arrUser = $this->User->find("all", array("conditions" => $UserCheckParams, 'fields' => 'id'));

		//Check if user is exist in the database or not
		if (isset($arrUser) && !empty($arrUser))
		{
			$intUserId = $arrUser[0]['User']['id'];
		}
		return $intUserId;
	}

	/**
	 * Private function to return invalid request
	 * 
	 * @param Array $data
	 */
	private function _invalid_request($data)
	{
		$this->_set_response($data, 0, "", INVALID_REQUEST, array());
	}

	/**
	 * Private function to set the response array
	 * 
	 * @param String $success_msg
	 * @param String $error_msg
	 * @param Array $response_data
	 */
	private function _set_response($input_data, $response_status, $success_msg, $error_msg, $response_data)
	{

		$response['name'] = $input_data['Authentication']['name'];
		$response['response_status'] = $response_status;
		$response['success_msg'] = $success_msg;
		$response['error_msg'] = $error_msg;
		if(!empty($response_data))
		{
			$response['Data'] = $response_data;
		}
		else
		{
			$response['Data'] = json_encode(new Object());
		}

		//Record the logs
		$this->_update_log($input_data, $response_status, $response);

		echo json_encode($response);
		return json_encode($response);
	}

   /**
	 * Private function to Authenticate Member
	 * 
	 * @param Array $data
	 */
	private function _authenticate_member_encrypt($data, $groupID)
	{
		$intUserId = $this->_validate_encrption_authentication($data);
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_imei = mdecrypt_generic($td, $this->hex2bin2($data['Data']['imei']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$imei3 = trim($decrypted_imei);
		$imei = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $imei3);
		if ($intUserId != 0)
		{
			//Sets response data in case of inactive user
			if (!$this->_IsUserActive($intUserId, $groupID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
			else if ($groupID != RESPONDER_GROUP_ID) //In case of Customer
			{
				//If there is no IMEI number
				if (!isset($imei) || empty($imei))
				{
					$this->_set_response($data, 0, "", IMEI_NOT_RECIEVED, array());
				}
				//Checks if user has already active call
				else if ($this->_havingActiveCalls($intUserId, $imei))
				{
					$this->_set_response($data, 2, "", HAVE_ACTIVE_CALL, array());
				}
				else
				{
					//Get last imei no.
					$this->loadModel("User");
					$dataUser = $this->User->findById($intUserId);
					
					//Success response for Customer
					$this->_updateloginstatus($intUserId);
					$this->loadModel("Setting");
				    $adminMobileNumberArr = $this->Setting->findById(1);
					$responseData = array("user_id"=>$intUserId,"mobile_no"=>$adminMobileNumberArr['Setting']['mobile_no']); // Adds FTP & Dialer info+918826521231
					$this->_getAngelInfo($responseData, $intUserId);
					$this->_getServicesInfo($responseData, $intUserId);
					$this->_getIncidentsRemainingBalance($responseData, $intUserId);
					$this->_getSafecallsRemainingBalance($responseData, $intUserId);
					$this->_updateUserIMEI($intUserId, $imei); //Updates User's Last IMEI
					
                    //Update users os type
                    if(isset($data['Data']['os_type']) && !empty($data['Data']['os_type'])):
                        $this->User->query("UPDATE users SET os_type = '".$data['Data']['os_type']."',modified_on='".date("Y-m-d H:i:s")."' WHERE id = ".$intUserId.";");
                    endif;
					//Adds Active Call Info in Response Data
					$this->_getActiveCalls($responseData, $intUserId);
					
					if(!empty($dataUser["User"]["last_imei"]) && $dataUser["User"]["last_imei"] != $imei)
					{
						$this->_set_response($data, 4, SUCCESSFULL_LOGIN_ALTERNATE_IMEI, "", $responseData);
					}
					else
					{
						$this->_set_response($data, 1, SUCCESSFULL_LOGIN, "", $responseData);
					}
				}
			}
			//Sets response data in case of success
			else
			{
				$this->_setResponderOnline($intUserId);
				$this->_updateloginstatus($intUserId);
				$responseData = array("user_id"=>$intUserId); 
			    //Adds Active Call Info in Response Data
				$this->_getResponderActiveCalls($responseData, $intUserId);
				//Success response for responder
				$this->_set_response($data, 1, SUCCESSFULL_LOGIN, "", $responseData);
			}
		}
		else
		{
			//Sets response data in case of failure
			$this->_set_response($data, 0, "", INVALID_AUTHENTICATION, array());
		}
		
	}
    
	private function hex2bin2($hexdata) { 
	
	$bindata=""; 
	for ($i = 0; $i < strlen($hexdata); $i += 2) {
			$bindata .= chr(hexdec(substr($hexdata, $i, 2)));
		  }
	return $bindata; 
	
	}
    
	/*
	 * Function for checking the user package is validity
	 * @param INT $userId 
	 * $output boolean
	 */

	private function _IsPackageExpired($userId)
	{
		$this->loadModel('OrderDetail');
		return $this->OrderDetail->getPackageExpire($userId);
	}

	/*
	 * Function for checking the user status
	 * @param INT @userId
	 * $output boolean
	 */

	private function _IsUserActive($userId, $groupID)
	{
		$statusCheckParmas = array();
		$statusCheckParmas['User.id'] = $userId;
		$statusCheckParmas['User.is_active'] = 1;
		$statusCheckParmas['User.group_id'] = $groupID; //Group id for customer type
		$this->loadModel("User");
		$recordCount = $this->User->find("count", array("conditions" => $statusCheckParmas));
		if ($recordCount > 0)
		{
			return true;
		}
		return false;
	}

	/*
	 * Method for Get Password
	 * @param Array $data 
	 * return String $password
	 */
	private function _get_password($data)
	{
		$new_password = $this->_generateRandomString();
		return $new_password;
	}

	/*
	 * @generate random string for forgot password
	 * 
	 */
	private function _generateRandomString($length = 8)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++)
		{
			$randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}

	/*
	 * Check username exist or not
	 * if exist then check package expiry and user status
	 * @param Array $data 
	 */
	private function _validate_username($data, $groupId)
	{
		if (isset($data["Authentication"]["username"]) && !empty($data["Authentication"]["username"]))
		{
			$username = $data["Authentication"]["username"];
		}
		else
		{
			$this->_invalid_request($data);
		}

		$userRecordArray = $this->_get_userdata_by_username($username, $groupId);
		if ($userRecordArray != null)
		{
			$intUserId = $userRecordArray['id'];

			//Sets response data in case of inactive user
			if (!$this->_IsUserActive($intUserId, CUSTOMER_GROUP_ID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
			else if ($groupId != RESPONDER_GROUP_ID)
			{
				//Sets response data in case of package expired
				if (!$this->_IsPackageExpired($intUserId))
				{
					$this->_set_response($data, 2, "", PACKAGE_EXPIRED, array());
				}
				else
				{
                    $password = "";
                    if(!empty($userRecordArray["upmanual"]))
                    {
                        $password = $userRecordArray["upmanual"];
                    }
					else
                    {
                        //Get new password for the user
                        $password = $this->_get_password($data);
                    
                        //Save new password in the user table
                        $this->loadModel("User");
                        $this->User->id = $intUserId;
                        $this->User->saveField('password', $password, true);
                        $this->User->saveField('upmanual', $password, true);
                    }

					//Arranging array for the view
					$userRecordArray["password"] = $password;

					//Sending email to user for account credetials
					$this->sendEmail($userRecordArray['email'], FROM_EMAIL, "Account Credentials", "user_forgot_password", $userRecordArray);
					
                    //Sending sms to user for account credetials
                    $messageForsms = "Your One Touch ID and Password is Username:" . $username . " Password:" . $password;
					$this->sendSingleSms($messageForsms, $this->_convertMobileNo($userRecordArray['mobile']));
					
                    $this->_set_response($data, 1, PASSWORD_SENT, "", array());
				}
			}
		}
		else
		{
			$this->_invalid_request($data);
		}
	}
	
	/*Encrypted forget password new API*/
	
	private function _validate_username_encrypt($data, $groupId)
	{
		
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_username = mdecrypt_generic($td, $this->hex2bin2($data["Authentication"]["username"]));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$username1= trim($decrypted_username);
		$username2= preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$username1);
		if (isset($username2) && !empty($username2))
		{
			$username = $username2;
		}
		else
		{
			$this->_invalid_request($data);
		}

		$userRecordArray = $this->_get_userdata_by_username($username, $groupId);
		if ($userRecordArray != null)
		{
			$intUserId = $userRecordArray['id'];

			//Sets response data in case of inactive user
			if (!$this->_IsUserActive($intUserId, CUSTOMER_GROUP_ID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
			else if ($groupId != RESPONDER_GROUP_ID)
			{
				//Sets response data in case of package expired
				if (!$this->_IsPackageExpired($intUserId))
				{
					$this->_set_response($data, 2, "", PACKAGE_EXPIRED, array());
				}
				else
				{
                    $password = "";
                    if(!empty($userRecordArray["upmanual"]))
                    {
                        $password = $userRecordArray["upmanual"];
                    }
					else
                    {
                        //Get new password for the user
                        $password = $this->_get_password($data);
                    
                        //Save new password in the user table
                        $this->loadModel("User");
                        $this->User->id = $intUserId;
                        $this->User->saveField('password', $password, true);
                        $this->User->saveField('upmanual', $password, true);
                    }

					//Arranging array for the view
					$userRecordArray["password"] = $password;
                    if(!empty($userRecordArray['email']))
					{
					//Sending email to user for account credetials
					$this->sendEmail($userRecordArray['email'], FROM_EMAIL, "Account Credentials", "user_forgot_password", $userRecordArray);
					}
                    //Sending sms to user for account credetials
                    $messageForsms = "Your One Touch ID and Password is Username:" . $username . " Password:" . $password;
					$this->sendSingleSms($messageForsms, $this->_convertMobileNo($userRecordArray['mobile']));
					
                    $this->_set_response($data, 1, PASSWORD_SENT, "", array());
				}
			}
		}
		else
		{
			$this->_invalid_request($data);
		}
	}
	/*END Encrypted forget password new API*/
	
	/*
	 * Check responder exist or not
	 * if exist then check package expiry and user status
	 * @param Array $data 
	 */
	
	private function _validate_responder_username($data, $groupId)
	{
		if (isset($data["Authentication"]["username"]) && !empty($data["Authentication"]["username"]))
		{
			$username = $data["Authentication"]["username"];
		}
		else
		{
			$this->_invalid_request($data);
		}

		$userRecordArray = $this->_get_userdata_by_username($username, $groupId);
		if ($userRecordArray != null)
		{
			$intUserId = $userRecordArray['id'];

			//Sets response data in case of inactive user
			if (!$this->_IsUserActive($intUserId, RESPONDER_GROUP_ID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
			else if ($groupId != CUSTOMER_GROUP_ID)
			{
				
				    $password = "";
                    if(!empty($userRecordArray["upmanual"]))
                    {
                        $password = $userRecordArray["upmanual"];
                    }
					else
                    {
                        //Get new password for the user
                        $password = $this->_get_password($data);
                    
                        //Save new password in the user table
                        $this->loadModel("User");
                        $this->User->id = $intUserId;
                        $this->User->saveField('password', $password, true);
                        $this->User->saveField('upmanual', $password, true);
                    }

					//Arranging array for the view
					$userRecordArray["password"] = $password;

					//Sending email to user for account credetials
					$this->sendEmail($userRecordArray['email'], FROM_EMAIL, "Account Credentials", "user_forgot_password", $userRecordArray);
					
                    //Sending sms to user for account credetials
                    $messageForsms = "Your One Touch ID and Password is Username:" . $username . " Password:" . $password;
					$this->sendSingleSms($messageForsms, $this->_convertMobileNo($userRecordArray['mobile']));
					
                    $this->_set_response($data, 1, PASSWORD_SENT, "", array());
				
			}
		}
		else
		{
			$this->_invalid_request($data);
		}
	}

	

	/*
	 * Get user id by the username 
	 * @param String $username
	 * returns user record if exist else returns 0
	 */
	private function _get_userdata_by_username($username, $groupId)
	{
		$this->loadModel("User");

		$findArray = array();
		$findArray['username'] = $username;
		$findArray['group_id'] = $groupId;

		$userRecordArray = $this->User->find('all', array('conditions' => $findArray, 'recursive' => -1));
		if (isset($userRecordArray) && !empty($userRecordArray))
		{
			return $userRecordArray[0]["User"];
		}
		return null;
	}
	
	

    /*AssistME Call*/
    private function _assist_me_data($data)
	 {
		$userId = $this->_validate_authentication($data);
		if ($userId != 0)
		{
			//Sets response data in case of inactive user
			if (!$this->_IsUserActive($userId, CUSTOMER_GROUP_ID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
		    //Checks if valid IMEI
			else if (!$this->_checkUserIMEI($userId, $data['Data']['imei']))
			{
				$this->_set_response($data, 3, "", INVALID_IMEI, array());
			}
			else
			{
				$this->loadModel("assistmes");
				$this->User->recursive = -1;
				$returnedId = null;
				$insertArr["assistmes"] = array();
				$insertArr["assistmes"]["id"] = "";
				$insertArr["assistmes"]["c_firstname"] = $data["Data"]["c_firstname"];
				$insertArr["assistmes"]["c_lname"] = $data["Data"]["c_lname"];
				$insertArr["assistmes"]["mobile"] = $data["Data"]["mobile"];
				$insertArr["assistmes"]["username"] = $data["Data"]["username"];
				$insertArr["assistmes"]["longitude"] = $data["Data"]["longitude"];
				$insertArr["assistmes"]["latitude"] = $data["Data"]["latitude"];
				$insertArr["assistmes"]["imei"] = $data["Data"]["imei"];
				$insertArr["assistmes"]["description"] = $data["Data"]["description"];
				$insertArr["assistmes"]["start_timestamp"] = date("Y-m-d H:i:s");
                $insertArr["assistmes"]["last_sent_date"] = date("Y-m-d H:i:s");
				$insertArr["assistmes"]["status"] = STATUS_LIVE;
				$insertArr["assistmes"]["is_active"] = 1;
				$insertArr["assistmes"]["created_by"] = $userId;
				$insertArr["assistmes"]["modified_by"] = $userId;
                if ($this->assistmes->save($insertArr))
				{
					
				  $this->_set_response($data, 1, CALL_INITIATE_SUCCESS_MESSAGE, "", $insertArrTrack);
					
				}
				else
				{
					
					$this->_invalid_request($data);
				}
			}
		}
		else
		{
			$this->_invalid_request($data);
		}
	}

    /*End AssistME Call*/

    /*AssistME Encrypted data*/
     private function _assist_me_encrypt_data($data)
	 {
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td15 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td15, $secret_key, $iv); 
		$decrypted_user_id = mdecrypt_generic($td15, $this->hex2bin2($data['Data']['user_id']));
		mcrypt_generic_deinit($td15); 
		mcrypt_module_close($td15); 
		$userId = trim($decrypted_user_id);
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_imei = mdecrypt_generic($td, $this->hex2bin2($data['Data']['imei']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$imei3 = trim($decrypted_imei);
		$imei = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$imei3);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_firstname = mdecrypt_generic($td1, $this->hex2bin2($data['Data']['c_firstname']));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$firstname1 = trim($decrypted_firstname);
		$firstname = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$firstname1);
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td2, $secret_key, $iv); 
		$decrypted_lastname = mdecrypt_generic($td2, $this->hex2bin2($data['Data']['c_lname']));
		mcrypt_generic_deinit($td2); 
		mcrypt_module_close($td2); 
		$lastname1 = trim($decrypted_lastname);
		$lastname = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$lastname1);
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td3, $secret_key, $iv); 
		$decrypted_mobile = mdecrypt_generic($td3, $this->hex2bin2($data['Data']['mobile']));
		mcrypt_generic_deinit($td3); 
		mcrypt_module_close($td3); 
		$mobile1 = trim($decrypted_mobile);
		$mobile = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$mobile1);
		$td4 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td4, $secret_key, $iv); 
		$decrypted_username = mdecrypt_generic($td4, $this->hex2bin2($data['Data']['username']));
		mcrypt_generic_deinit($td4); 
		mcrypt_module_close($td4); 
		$username1 = trim($decrypted_username);
		$username = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$username1);
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td5, $secret_key, $iv); 
		$decrypted_longitude = mdecrypt_generic($td5, $this->hex2bin2($data['Data']['longitude']));
		mcrypt_generic_deinit($td5); 
		mcrypt_module_close($td5); 
		$longitude3 = trim($decrypted_longitude);
		$longitude = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$longitude3);
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td6, $secret_key, $iv); 
		$decrypted_latitude = mdecrypt_generic($td6, $this->hex2bin2($data['Data']['latitude']));
		mcrypt_generic_deinit($td6); 
		mcrypt_module_close($td6); 
		$latitude3 = trim($decrypted_latitude);
		$latitude = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$latitude3);
		$td7 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td7, $secret_key, $iv); 
		$decrypted_description = mdecrypt_generic($td7, $this->hex2bin2($data['Data']['description']));
		mcrypt_generic_deinit($td7); 
		mcrypt_module_close($td7); 
		$description1 = trim($decrypted_description);
		$description = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$description1);
		$this->loadModel("User");
        $this->User->recursive = -1;
		$userData = $this->User->findById($userId);
		if (empty($latitude))
		  {
		$this->_set_response($data, 2, "", "Source Latitude is blank!", array());
		  }
		else if (empty($longitude))
	      {
		 $this->_set_response($data, 2, "", "Source Longitude is blank!", array());
		  }
		else if (!empty($userData['User']['id']))
		{
			//Sets response data in case of inactive user
			if (!$this->_IsUserActive($userId, CUSTOMER_GROUP_ID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
		    //Checks if valid IMEI
			else if (!$this->_checkUserIMEI($userId, $imei))
			{
				$this->_set_response($data, 3, "", INVALID_IMEI, array());
			}
			else
			{
				$this->loadModel("assistmes");
				$this->User->recursive = -1;
				$returnedId = null;
				$insertArr["assistmes"] = array();
				$insertArr["assistmes"]["id"] = "";
				$insertArr["assistmes"]["c_firstname"] = $firstname;
				$insertArr["assistmes"]["c_lname"] = $lastname;
				$insertArr["assistmes"]["mobile"] = $mobile;
				$insertArr["assistmes"]["username"] = $username;
				$insertArr["assistmes"]["longitude"] = $longitude;
				$insertArr["assistmes"]["latitude"] = $latitude;
				$insertArr["assistmes"]["imei"] = $imei;
				$insertArr["assistmes"]["description"] = $description;
				$insertArr["assistmes"]["start_timestamp"] = date("Y-m-d H:i:s");
                $insertArr["assistmes"]["last_sent_date"] = date("Y-m-d H:i:s");
				$insertArr["assistmes"]["status"] = STATUS_LIVE;
				$insertArr["assistmes"]["is_active"] = 1;
				$insertArr["assistmes"]["created_by"] = $userId;
				$insertArr["assistmes"]["modified_by"] = $userId;
                if ($this->assistmes->save($insertArr))
				{
					
				  $this->_set_response($data, 1, CALL_INITIATE_SUCCESS_MESSAGE, "", $insertArrTrack);
					
				}
				else
				{
					
					$this->_invalid_request($data);
				}
			}
		}
		else
		{
			$this->_invalid_request($data);
		}
	}
   /*End AssistME Encrypted data*/

  /*TrackME encrypted Info */
	
	private function _addtrackme_encrypt_info($data)
	{
		$modelName = "SafeCall";
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		}
		$td15 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td15, $secret_key, $iv); 
		$decrypted_userid = mdecrypt_generic($td15, $this->hex2bin2($data["Data"]["user_id"]));
		mcrypt_generic_deinit($td15); 
		mcrypt_module_close($td15); 
		$userId = trim($decrypted_userid); 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_imei = mdecrypt_generic($td, $this->hex2bin2($data['Data']['imei']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$imei = trim($decrypted_imei);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_destination = mdecrypt_generic($td1, $this->hex2bin2($data['Data']['destination']));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$destination = trim($decrypted_destination);
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td2, $secret_key, $iv); 
		$decrypted_lattitude = mdecrypt_generic($td2, $this->hex2bin2($data['Data']['destination_lattitude']));
		mcrypt_generic_deinit($td2); 
		mcrypt_module_close($td2); 
		$d_lattitude = trim($decrypted_lattitude);
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td3, $secret_key, $iv); 
		$decrypted_longitude = mdecrypt_generic($td3, $this->hex2bin2($data['Data']['destination_longitude']));
		mcrypt_generic_deinit($td3); 
		mcrypt_module_close($td3); 
		$d_longitude = trim($decrypted_longitude);
		$td4 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td4, $secret_key, $iv); 
		$decrypted_vehicle_no = mdecrypt_generic($td4, $this->hex2bin2($data['Data']['vehicle_no']));
		mcrypt_generic_deinit($td4); 
		mcrypt_module_close($td4); 
		$d_vehicle_no = trim($decrypted_vehicle_no);
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td5, $secret_key, $iv); 
		$time_interval = mdecrypt_generic($td5, $this->hex2bin2($data['Data']['call_time_interval']));
		mcrypt_generic_deinit($td5); 
		mcrypt_module_close($td5); 
		$c_time_interval = trim($time_interval);
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td6, $secret_key, $iv); 
		$decrypted_share_info = mdecrypt_generic($td6, $this->hex2bin2($data['Data']['share_info']));
		mcrypt_generic_deinit($td6); 
		mcrypt_module_close($td6); 
		$share_info = trim($decrypted_share_info);
		$td7 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td7, $secret_key, $iv); 
		$decrypted_swipe_status = mdecrypt_generic($td7, $this->hex2bin2($data['Data']['swipe_status']));
		mcrypt_generic_deinit($td7); 
		mcrypt_module_close($td7); 
		$swipe_status = trim($decrypted_swipe_status);
		$td8 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td8, $secret_key, $iv); 
		$decrypted_source_longitude = mdecrypt_generic($td8, $this->hex2bin2($data['Data']['source_longitude']));
		mcrypt_generic_deinit($td8); 
		mcrypt_module_close($td8); 
		$s_longitude = trim($decrypted_source_longitude);
		$td9 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td9, $secret_key, $iv); 
		$decrypted_source_latitude = mdecrypt_generic($td9, $this->hex2bin2($data['Data']['source_latitude']));
		mcrypt_generic_deinit($td9); 
		mcrypt_module_close($td9); 
		$s_latitude = trim($decrypted_source_latitude);
		$td10 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td10, $secret_key, $iv); 
		$decrypted_transport_mode = mdecrypt_generic($td10, $this->hex2bin2($data['Data']['transport_mode']));
		mcrypt_generic_deinit($td10); 
		mcrypt_module_close($td10); 
		$transport_mode = trim($decrypted_transport_mode);
		$this->loadModel("User");
        $this->User->recursive = -1;
		$userData = $this->User->findById($userId);
		if (empty($s_latitude))
		  {
		$this->_set_response($data, 2, "", "Source Latitude is blank!", array());
		  }
		else if (empty($s_longitude))
	      {
		 $this->_set_response($data, 2, "", "Source Longitude is blank!", array());
		  }
		else if (!empty($userData['User']['id']))
		{
		  //Sets response data in case of inactive user
			if (!$this->_IsUserActive($userId, CUSTOMER_GROUP_ID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
			//Sets response data in case of use full quota of package
			else if (!$this->_checkBalanceCallQuota($userId, $modelName))
			{
				$this->_set_response($data, 2, "", PACKAGE_EXPIRED, array());
			}
			//Sets response data in case of package expired
			else if (!$this->_IsPackageExpired($userId))
			{
				$this->_set_response($data, 2, "", PACKAGE_EXPIRED, array());
			}
			//Checks if valid IMEI
			else if (!$this->_checkUserIMEI($userId, $imei))
			{
				$this->_set_response($data, 3, "", INVALID_IMEI, array());
			}
			//Checks if valid no. of incidents and safecalls remaining for user or not
			else if (!$this->_checkSafeCallsIncidentsBalance($userId, $modelName))
			{
				$this->_set_response($data, 2, "", INSUFFICIENT_BALANCE, array());
			}
			else
			{
				$this->loadModel("trackme_add_infos");
				$this->trackme_add_infos->recursive = -1;
				$insertArr["trackme_add_infos"] = array();
				$insertArr["trackme_add_infos"]["user_id"] = $userId;
				$insertArr["trackme_add_infos"]["destination"] = $destination;
				$insertArr["trackme_add_infos"]["lattitude"] = $d_lattitude;
				$insertArr["trackme_add_infos"]["longitude"] = $d_longitude;
				$insertArr["trackme_add_infos"]["transport_mode"] = $transport_mode;
				$insertArr["trackme_add_infos"]["vehicle_no"] = $d_vehicle_no;
				if(!empty($d_vehicle_no))
				{
				$insertArr["trackme_add_infos"]["vehicle_no_pic"] = $d_vehicle_no;
				$this->vehicle_noplate_pic2($data);
				}
				$insertArr["trackme_add_infos"]["call_time_interval"] = $c_time_interval;
				$insertArr["trackme_add_infos"]["share_info"] = $share_info;
				$insertArr["trackme_add_infos"]["swipe_status"] =  $swipe_status;
				$insertArr["trackme_add_infos"]["created_by"] = $userId;
				$insertArr["trackme_add_infos"]["created_on"] = date("Y-m-d H:i:s");
				$insertArr["trackme_add_infos"]["modified_on"] = date("Y-m-d H:i:s");
				if ($this->trackme_add_infos->save($insertArr))	
				{
				$trackmeaddId = $this->trackme_add_infos->id;
				$this->loadModel("User");
				$this->loadModel("Setting");
				$adminMobileNumberArr = $this->Setting->findById(1);
				$adminMobileNumber = $this->_convertMobileNo($adminMobileNumberArr["Setting"]["admin_mobile_number"]);
				$this->User->recursive = 0;
				$userData = $this->User->findById($userId);
				$this->loadModel("SafeCall");
				$this->SafeCall->recursive = -1;
				$insertArr[$modelName] = array();
				$insertArr[$modelName]["id"] = "";
				$insertArr[$modelName]["customer_user_id"] = $userId;
				$insertArr[$modelName]["trackme_add_info_id"] = $trackmeaddId;
				$insertArr[$modelName]["longitude"] = $s_longitude;
				$insertArr[$modelName]["latitude"] = $s_latitude;
				$insertArr[$modelName]["imei"] = $imei;
				$insertArr[$modelName]["start_timestamp"] = date("Y-m-d H:i:s");
                $insertArr[$modelName]["last_sent_date"] = date("Y-m-d H:i:s");
				$insertArr[$modelName]["status"] = STATUS_LIVE;
				$insertArr[$modelName]["is_active"] = 1;
				$insertArr[$modelName]["created_by"] = $userId;
				$insertArr[$modelName]["modified_by"] = $userId;

				$this->loadModel($modelName);
				$this->{$modelName}->create();
				  //Saves TrackME
				  if ($this->{$modelName}->save($insertArr))
				  {
				  $returnedId = $this->{$modelName}->id;
				  $insertArrTrack[$modelName . "Tracking"] = array();
				  $insertArrTrack[$modelName . "Tracking"]["id"] = "";
				  $insertArrTrack[$modelName . "Tracking"]["safe_call_id"] = $returnedId;
				  $insertArrTrack[$modelName . "Tracking"]["longitude"] = $s_longitude;
				  $insertArrTrack[$modelName . "Tracking"]["latitude"] = $s_latitude;
				  $insertArrTrack[$modelName . "Tracking"]["created_by"] = $userId;
				  $insertArrTrack[$modelName . "Tracking"]["modified_by"] = $userId;
				  $this->loadModel($modelName . "Tracking");
				  $this->{$modelName . "Tracking"}->create();
				  $this->{$modelName . "Tracking"}->save($insertArrTrack);
				  $responseArray = array();
				  $this->loadModel("OrderDetail");
				  $this->OrderDetail->recursive = -1;
				  $order_details4 = $this->OrderDetail->find("first", array("conditions" => array("user_id" => $userId),'order' => 'OrderDetail.id DESC'));
			      $pr_id = $order_details4['OrderDetail']['primary_user_id'];
				   //Update counter of used safe calls in orderdetail
                           if(!empty($pr_id))
                            {
                                
                                $q = "UPDATE order_details SET used_safe_calls = used_safe_calls + 1 WHERE primary_user_id=".$pr_id."";
                                
                                $this->{$modelName}->query($q);
                            }
                            else
                            {
                                
								$query = "UPDATE order_details SET used_safe_calls = used_safe_calls + 1 WHERE user_id = " . $userId . " AND order_status = 1 AND is_active = 1";
								
                                $this->{$modelName}->query($query);
								
                            }
				  $responseArray["SafeCallID"] = $returnedId;
				  $responseArray["TrackME_Time"] = date("Y-m-d H:i:s");	
				  $responseArray["call_time_interval"] = $c_time_interval;	
				  $this->_getSafecallsRemainingBalance($responseArray, $userId);

				  //Message Content
				  $messageSafeCallInitialised = "Safe Call has been activated by: " . $userData['User']['firstname'] . ' ' . $userData['User']['lastname'] .
											" at : " . date("Y-m-d H:i:s") . " Location: http://maps.google.com/?ll=" . $s_latitude . "," . $s_longitude.". One Touch Response is tracking the location.";

                  $numbers = array($adminMobileNumber,$this->_convertMobileNo($userData['UserProfile']['emergency_phone1']),
									$this->_convertMobileNo($userData['UserProfile']['emergency_phone2']),
									$this->_convertMobileNo($userData['UserProfile']['emergency_phone3']));

				  $this->sendMultipleSms($messageSafeCallInitialised, $numbers);			
                  $this->_sendpushnotification($userId);
				  $this->_set_response($data, 1, CALL_INITIATE_SUCCESS_MESSAGE, "", $responseArray);
				  }
				  else
				  {
				  $this->_invalid_request($data);
				  }
				}  
				else
				{
					$this->_invalid_request($data);
				}
			 }
		}
		else
		{
		$this->_invalid_request($data);
		}	 	
    }		
	
	private function vehicle_noplate_pic2($data)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_username = mdecrypt_generic($td, $this->hex2bin2($data['Authentication']['username']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$username = trim($decrypted_username);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_vehicleno = mdecrypt_generic($td1, $this->hex2bin2($data["Data"]["vehicle_no_pic"]));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$vehicle_no = trim($decrypted_vehicleno);
		$my_base64_string = $data["Data"]["photo_string_base64"];
        $vehiclepic = $vehicle_no;
		//call the function to convert base64 to image
		$image = $this->base64_to_jpeg($my_base64_string, $vehiclepic);
        mkdir("trackme_pic/".$username, 0777);
		//now copy the picture to desired folder
		copy($image, 'trackme_pic/'.$username.'/' . $vehiclepic);
									
	}

 /*End TrackME encrypted info*/
	
	/*send push notification*/
	private function _sendpushnotification($userId)
	{
		$this->loadModel("User");
        $this->User->recursive = -1;
		$userData = $this->User->findById($userId);
		$registrationIDs = $userData['User']['user_ud_id'];
		$os_type = $userData['User']['os_type'];
		if(!empty($registrationIDs) && $os_type=='Android')
		  {
			$apiKey = "AIzaSyBpwEg7ps7KBQ0vImLTuDUArrz7t_FHbmc";
			$message = "You are being tracked now by OTR.Have a safe travel";
			// Set POST variables
			$url = 'https://android.googleapis.com/gcm/send';
			$fields = array(
				'registration_ids' => array($registrationIDs),
				'data' => array( "message" => $message ,'vibrate'   => 1,'sound'     => 1,'type' => 'text'),
			);
			$headers = array(
				'Authorization: key=' . $apiKey,
				'Content-Type: application/json'
			);
			// Open connection
			$ch = curl_init();
		
			// Set the URL, number of POST vars, POST data
			curl_setopt( $ch, CURLOPT_URL, $url);
			curl_setopt( $ch, CURLOPT_POST, true);
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields));
		
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// curl_setopt($ch, CURLOPT_POST, true);
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields));
		
			// Execute post
			$result = curl_exec($ch);
			 // Close connection
			curl_close($ch);
		  }
		  else if(!empty($registrationIDs) && $os_type=='ios')
		  {
		  $message = "You are being tracked now by OTR.Have a safe travel";
		  $this->_sendiospushnotification($registrationIDs,$message);
		  }								
	}
	
	private function _sendiospushnotification($registrationIDs,$message)
	{
		$deviceToken = $registrationIDs;
		$passphrase = '12345';
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', 'certificates/ck.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		
		// Open a connection to the APNS server
		$fp = stream_socket_client(
		'ssl://gateway.push.apple.com:2195', $err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		
		if (!$fp)
		exit("Failed to connect: $err $errstr" . PHP_EOL);
		
		/*echo 'Connected to APNS Device Token = '. $deviceToken .'  socket id  ='.$fp. PHP_EOL;*/
		//LogData("Connected to APNS Device Token = ". $deviceToken ."  socket id  =".$fp,1,DBG_DETAIL); 
		// Create the payload body
		$body['aps'] = array(
		'alert' => $message,
		'sound' => 'default',
		'category' =>'EVENTACTION'
		);
		
		// Encode the payload as JSON
		$payload = json_encode($body);
		
		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		
		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));
		// Close the connection to the server
		fclose($fp);
	}
	/*End Push Notification*/
	
    /*End TrackME Info*/
	
    /*TrackME Swipe message*/
	private function _trackme_swipe_msg($data)
	{
		$userId = $this->_validate_authentication($data);
		if ($userId != 0)
		{
			$this->loadModel("User");
			$this->User->recursive = -1;
			$userData = $this->User->findById($userId);
			$registrationIDs = $userData['User']['user_ud_id'];
			if(!empty($registrationIDs))
			  {
				$apiKey = "AIzaSyBpwEg7ps7KBQ0vImLTuDUArrz7t_FHbmc";
				$message = "Tell us if you are ok";
				// Set POST variables
				$url = 'https://android.googleapis.com/gcm/send';
				$fields = array(
					'registration_ids' => array($registrationIDs),
					'data' => array( "message" => $message ,'vibrate'   => 1,'sound'     => 1,'type' => 'option'),
				);
				$headers = array(
					'Authorization: key=' . $apiKey,
					'Content-Type: application/json'
				);
				// Open connection
				$ch = curl_init();
			
				// Set the URL, number of POST vars, POST data
				curl_setopt( $ch, CURLOPT_URL, $url);
				curl_setopt( $ch, CURLOPT_POST, true);
				curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
				//curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields));
			
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				// curl_setopt($ch, CURLOPT_POST, true);
				// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields));
			
				// Execute post
				$result = curl_exec($ch);
				 // Close connection
				curl_close($ch);
			  }
	    $this->loadModel("trackme_add_infos");
		$this->trackme_add_infos->recursive = -1;
		$trackme_infos = $this->trackme_add_infos->find('first', array('conditions' => array("user_id"=>$userId),'order' => 'trackme_add_infos.id DESC'));	
		$trackmeswipetime = array();
		$trackmeswipetime['trackmemovetime'] = $trackme_infos['trackme_add_infos']['modified_on'];
		$this->_set_response($data, 1, "Success", "", $trackmeswipetime);	  
		}
		else
		{
		$this->_invalid_request($data);	
		}  								
	}
	
	private function _swipe_status_update($data)
	{
		$userId = $this->_validate_authentication($data);
		if ($userId != 0)
		{
		 $swipe_status = $data["Data"]["swipe_status"];
		 $trackme_id = $data["Data"]["trackme_id"];
		 $this->loadModel("SafeCall");
		 $this->SafeCall->recursive = -1;
		 $safecall_detail = $this->SafeCall->find('first', array('conditions' => array("id"=>$trackme_id)));
		 if($swipe_status==1)
		 {
		 $this->loadModel("trackme_add_infos");
		 $this->trackme_add_infos->recursive = -1;
		 $this->trackme_add_infos->id = $safecall_detail['SafeCall']['trackme_add_info_id'];
		 $this->trackme_add_infos->saveField('swipe_status', $swipe_status);
		 $this->trackme_add_infos->saveField('modified_on',date("Y-m-d H:i:s"));
		 $this->_set_response($data, 1, "Success", "", array());	  
		 }
		 else
		 {
		 $this->loadModel("trackme_add_infos");
		 $this->trackme_add_infos->recursive = -1;
		 $this->trackme_add_infos->id = $safecall_detail['SafeCall']['trackme_add_info_id'];
		 $this->trackme_add_infos->saveField('swipe_status', $swipe_status);
		 $this->trackme_add_infos->saveField('modified_on',date("Y-m-d H:i:s"));
		 $this->_set_response($data, 1, "TrackME has been moved successfully!", "", array());	  
		 }
		}
		else
		{
		$this->_invalid_request($data);	
		}
						
	}
	
	/*END TrackME Swipe message*/
	
	/*Add Safe Location*/
	private function _add_safe_location($data)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_user_id = mdecrypt_generic($td, $this->hex2bin2($data['Data']['user_id']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$userId = trim($decrypted_user_id);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_location = mdecrypt_generic($td1, $this->hex2bin2($data['Data']['location']));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$location = trim($decrypted_location);
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td2, $secret_key, $iv); 
		$decrypted_latitude = mdecrypt_generic($td2, $this->hex2bin2($data['Data']['latitude']));
		mcrypt_generic_deinit($td2); 
		mcrypt_module_close($td2); 
		$latitude = trim($decrypted_latitude);
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td3, $secret_key, $iv); 
		$decrypted_longitude = mdecrypt_generic($td3, $this->hex2bin2($data['Data']['longitude']));
		mcrypt_generic_deinit($td3); 
		mcrypt_module_close($td3); 
		$longitude = trim($decrypted_longitude);
		if ($userId != 0)
		{
		$this->loadModel("trackme_safelocations");
	    $this->trackme_safelocations->recursive = -1;
	    $insertArr["trackme_safelocations"] = array();
	    $insertArr["trackme_safelocations"]["user_id"] = $userId;
		$insertArr["trackme_safelocations"]["location"] = $location;
		$insertArr["trackme_safelocations"]["latitude"] = $latitude;
		$insertArr["trackme_safelocations"]["longitude"] = $longitude;
		$insertArr["trackme_safelocations"]["is_active"] = 1;
		 if ($this->trackme_safelocations->save($insertArr))	
		  {
		  $this->_set_response($data, 1, "Safe_location Added Successfully!!", "", array());
		  }
		  else
		  {
		  $this->_invalid_request($data);
		  }
		}
		else
		{
		$this->_invalid_request($data);
		}
	}
		
	private function _view_safe_location($data)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_user_id = mdecrypt_generic($td, $this->hex2bin2($data['Data']['user_id']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$userId = trim($decrypted_user_id);
		if ($userId != 0)
		{
		$i = 0;
		$this->loadModel("trackme_safelocations");
	    $this->trackme_safelocations->recursive = -1;
	    $locationRecord = $this->trackme_safelocations->find('all', array('conditions' => array("user_id"=>$userId)));
	    $responseData = array();
		foreach ($locationRecord as $value1)
		{
		$responseData[$i]['id'] = $value1['trackme_safelocations']['id'];
		$responseData[$i]['location'] = $value1['trackme_safelocations']['location'];
		$responseData[$i]['latitude'] = $value1['trackme_safelocations']['latitude'];
		$responseData[$i]['longitude'] = $value1['trackme_safelocations']['longitude'];
		$responseData[$i]['created_on'] = $value1['trackme_safelocations']['created_on'];
	    $i++;
		}
		$location_data['Safe_Location']= array();
		$location_data['Safe_Location'] = $responseData;
		if (!empty($location_data))	
		  {
		  $this->_set_response($data, 1, "View All Safe Location!!", "", $location_data);
		  }
		  else
		  {
		  $this->_set_response($data, 0, "There is No Safe Locations!", "", array());
		  }
		}
		else
		{
		$this->_invalid_request($data);
		}
	}	
	
	
	private function _edit_safe_location($data)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_user_id = mdecrypt_generic($td, $this->hex2bin2($data['Data']['user_id']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$userId = trim($decrypted_user_id);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_location = mdecrypt_generic($td1, $this->hex2bin2($data['Data']['location']));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$location = trim($decrypted_location);
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td2, $secret_key, $iv); 
		$decrypted_latitude = mdecrypt_generic($td2, $this->hex2bin2($data['Data']['latitude']));
		mcrypt_generic_deinit($td2); 
		mcrypt_module_close($td2); 
		$latitude = trim($decrypted_latitude);
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td3, $secret_key, $iv); 
		$decrypted_longitude = mdecrypt_generic($td3, $this->hex2bin2($data['Data']['longitude']));
		mcrypt_generic_deinit($td3); 
		mcrypt_module_close($td3); 
		$longitude = trim($decrypted_longitude);
		$td4 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td4, $secret_key, $iv); 
		$decrypted_location_id = mdecrypt_generic($td4, $this->hex2bin2($data['Data']['location_id']));
		mcrypt_generic_deinit($td4); 
		mcrypt_module_close($td4); 
		$location_id = trim($decrypted_location_id);
		if ($userId != 0)
		{
		$this->loadModel("trackme_safelocations");
	    $this->trackme_safelocations->recursive = -1;
	    $locationRecord = $this->trackme_safelocations->find('first', array('conditions' => array("id"=>$location_id)));
			if(!empty($locationRecord['trackme_safelocations']['id']))
			{
			$this->trackme_safelocations->id = $location_id;
			$this->trackme_safelocations->saveField('location', $location);
			$this->trackme_safelocations->saveField('latitude', $latitude);
			$this->trackme_safelocations->saveField('longitude', $longitude);
			$this->trackme_safelocations->saveField('is_active', 1);
			$this->trackme_safelocations->saveField('modified_on',date("Y-m-d H:i:s"));
			$this->_set_response($data, 1, "Safe Location Updated Successfully!", "", array());
			}
			else
			{
			$this->_invalid_request($data);
			}
		}
		else
		{
		$this->_invalid_request($data);
		}
	}	
	
	
	private function _delete_safe_location($data)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_user_id = mdecrypt_generic($td, $this->hex2bin2($data['Data']['user_id']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$userId = trim($decrypted_user_id);
		$td4 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td4, $secret_key, $iv); 
		$decrypted_location_id = mdecrypt_generic($td4, $this->hex2bin2($data['Data']['location_id']));
		mcrypt_generic_deinit($td4); 
		mcrypt_module_close($td4); 
		$location_id = trim($decrypted_location_id);
		if ($userId != 0)
		{
		$this->loadModel("trackme_safelocations");
	    $this->trackme_safelocations->recursive = -1;
		$locationRecord = $this->trackme_safelocations->find('first', array('conditions' => array("id"=>$location_id)));
		    if(!empty($locationRecord['trackme_safelocations']['id']))
			{
			$this->trackme_safelocations->id = $location_id;
		    $this->trackme_safelocations->delete();
	        $this->_set_response($data, 1, "Safe Location Deleted Successfully!", "", array());
			}
			else
			{
			$this->_invalid_request($data);
			}
		}
		else
		{
		$this->_invalid_request($data);
		}
	}	
	
	/*End Add Safe Location*/
	
	/*
	 * For initiate a valid call
	 * if exist then check package expiry and user status
	 * @date array
	 * @ModelName String 
	 */
   /*Validate encrypted data*/
   private function _validate_call_encrypt($data, $modelName)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td15 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td15, $secret_key, $iv); 
		$decrypted_user_id = mdecrypt_generic($td15, $this->hex2bin2($data['Data']['user_id']));
		mcrypt_generic_deinit($td15); 
		mcrypt_module_close($td15); 
		$userId = trim($decrypted_user_id);
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_imei = mdecrypt_generic($td, $this->hex2bin2($data['Data']['imei']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$imei1 = trim($decrypted_imei);
		$imei = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $imei1);
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td5, $secret_key, $iv); 
		$decrypted_longitude1 = mdecrypt_generic($td5, $this->hex2bin2($data['Data']['longitude']));
	    mcrypt_generic_deinit($td5); 
		mcrypt_module_close($td5); 
		$decrypted_longitude3 = trim($decrypted_longitude1);
		$decrypted_longitude = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $decrypted_longitude3);
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td6, $secret_key, $iv); 
		$decrypted_latitude1 = mdecrypt_generic($td6, $this->hex2bin2($data['Data']['latitude']));
		mcrypt_generic_deinit($td6); 
		mcrypt_module_close($td6); 
		$decrypted_latitude3 = trim($decrypted_latitude1);
		$decrypted_latitude = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $decrypted_latitude3);
		$this->loadModel("User");
        $this->User->recursive = -1;
		$userData = $this->User->findById($userId);
		if (!empty($userData['User']['id']))
		{
		   //Sets response data in case of inactive user
			if (!$this->_IsUserActive($userId, CUSTOMER_GROUP_ID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
			//Sets response data in case of use full quota of package
			else if (!$this->_checkBalanceCallQuota($userId, $modelName))
			{
				$this->_set_response($data, 2, "", PACKAGE_EXPIRED, array());
			}
			//Sets response data in case of package expired
			else if (!$this->_IsPackageExpired($userId))
			{
				$this->_set_response($data, 2, "", PACKAGE_EXPIRED, array());
			}
			//Checks if valid IMEI
			else if (!$this->_checkUserIMEI($userId, $imei))
			{
				$this->_set_response($data, 3, "", INVALID_IMEI, array());
			}
			//Checks if valid no. of incidents and safecalls remaining for user or not
			else if (!$this->_checkSafeCallsIncidentsBalance($userId, $modelName))
			{
				$this->_set_response($data, 2, "", INSUFFICIENT_BALANCE, array());
			}
			else if (empty($decrypted_latitude))
			{
				$this->_set_response($data, 2, "", "Latitude is blank!", array());
			}
			else if (empty($decrypted_longitude))
			{
				$this->_set_response($data, 2, "", "Longitude is blank!", array());
			}
			else
			{
				$this->loadModel("User");
				$this->loadModel("Setting");
				$adminMobileNumberArr = $this->Setting->findById(1);
				$adminMobileNumber = $this->_convertMobileNo($adminMobileNumberArr["Setting"]["admin_mobile_number"]);
				$this->User->recursive = 0;
				$userData = $this->User->findById($userId);
				$returnedId = null;
				$insertArr[$modelName] = array();
				$insertArr[$modelName]["id"] = "";
				$insertArr[$modelName]["customer_user_id"] = $userId;
				$insertArr[$modelName]["longitude"] = $decrypted_longitude;
				$insertArr[$modelName]["latitude"] = $decrypted_latitude;
				$insertArr[$modelName]["imei"] = $imei;
				$insertArr[$modelName]["start_timestamp"] = date("Y-m-d H:i:s");
                $insertArr[$modelName]["last_sent_date"] = date("Y-m-d H:i:s");
				$insertArr[$modelName]["status"] = STATUS_LIVE;
				$insertArr[$modelName]["is_active"] = 1;
				$insertArr[$modelName]["created_by"] = $userId;
				$insertArr[$modelName]["modified_by"] = $userId;

				$this->loadModel($modelName);
				$this->{$modelName}->create();
				//Saves Incident/SafeCall
				if ($this->{$modelName}->save($insertArr))
				{
					$returnedId = $this->{$modelName}->id;
					$insertArrTrack[$modelName . "Tracking"] = array();
					$insertArrTrack[$modelName . "Tracking"]["id"] = "";
					//Checks if it's Safe Call
					if ($modelName == "SafeCall")
					{
						$insertArrTrack[$modelName . "Tracking"]["safe_call_id"] = $returnedId;
					}
					else//Incident Otherwise
					{
                        //Gets Incident Info
                        $incidentID = $this->{$modelName}->id;
                        $latitude = $this->{$modelName}->field('latitude');
                        $longitude = $this->{$modelName}->field('longitude');
                        /*$this->_send_request_to_responder($incidentID, $userId, $latitude, $longitude);*/
						if(!empty($data["Data"]["category"]))
						{
						$category = $data["Data"]["category"];
						$subcategory = $data["Data"]["subcategory"];
						$this->_update_incident_category_info($incidentID,$userId,$category,$subcategory);
						}
                        $insertArrTrack[$modelName . "Tracking"]["incident_id"] = $returnedId;
					}
					$insertArrTrack[$modelName . "Tracking"]["longitude"] =  $decrypted_longitude;
					$insertArrTrack[$modelName . "Tracking"]["latitude"] = $decrypted_latitude;
					$insertArrTrack[$modelName . "Tracking"]["created_by"] = $userId;
					$insertArrTrack[$modelName . "Tracking"]["modified_by"] = $userId;

					$this->loadModel($modelName . "Tracking");
					$this->{$modelName . "Tracking"}->create();
					//Saves Incident/SafeCall Tracking Information
					if ($this->{$modelName . "Tracking"}->save($insertArrTrack))
					{
						$responseArray = array();
						$this->loadModel("OrderDetail");
				        $this->OrderDetail->recursive = -1;
						$order_details4 = $this->OrderDetail->find("first", array("conditions" => array("user_id" => $userId),'order' => 'OrderDetail.id DESC'));
						$pr_id = $order_details4['OrderDetail']['primary_user_id'];
						if ($modelName == "SafeCall")
						{ 
						   
						   //Update counter of used safe calls in orderdetail
                           if(!empty($pr_id))
                            {
                                
                                $q = "UPDATE order_details SET used_safe_calls = used_safe_calls + 1 WHERE primary_user_id=".$pr_id."";
                                
                                $this->{$modelName}->query($q);
                            }
                            else
                            {
                                
								$query = "UPDATE order_details SET used_safe_calls = used_safe_calls + 1 WHERE user_id = " . $userId . " AND order_status = 1 AND is_active = 1";
								
                                $this->{$modelName}->query($query);
								
                            }
                            
							$responseArray["SafeCallID"] = $returnedId;
							
							$this->_getSafecallsRemainingBalance($responseArray, $userId);

							//Message Content
							$messageSafeCallInitialised = "Safe Call has been activated by: " . $userData['User']['firstname'] . ' ' . $userData['User']['lastname'] .
											" at : " . date("Y-m-d H:i:s") . " Location: http://maps.google.com/?ll=" . $decrypted_latitude . "," . $decrypted_longitude.". One Touch Response is tracking the location.";

                            $numbers = array($adminMobileNumber,
									$this->_convertMobileNo($userData['UserProfile']['emergency_phone1']),
									$this->_convertMobileNo($userData['UserProfile']['emergency_phone2']),
									$this->_convertMobileNo($userData['UserProfile']['emergency_phone3']));

							$this->sendMultipleSms($messageSafeCallInitialised, $numbers);
						}
						else
						{
							//Update counter of used incidents in orderdetail
                            if(!empty($pr_id))
                            {
                               
                                $q = "UPDATE order_details SET used_incidents = used_incidents + 1 WHERE primary_user_id = ".$pr_id."";
                                
                                $this->{$modelName}->query($q);
                            }
                            else
                            {
                                $query = "UPDATE order_details SET used_incidents = used_incidents + 1 WHERE user_id = ".$userId." AND order_status = 1 AND is_active = 1";
                                $this->{$modelName}->query($query);
                            }
                            
							$responseArray["IncidentID"] = $returnedId;
							
							$this->_getIncidentsRemainingBalance($responseArray, $userId);

							$messageIncidentInitialised = "We have received an Emergency Alert from " . $userData['User']['firstname'] . ' ' . $userData['User']['lastname'] ." and ".$userData['User']['mobile']." at " . date("Y-m-d H:i:s") . ". For the location click on http://maps.google.com/?ll=" .$decrypted_latitude. "," .$decrypted_longitude. " Assistance is being provided.";

							$numbers = array($adminMobileNumber,
									$this->_convertMobileNo($userData['UserProfile']['emergency_phone1']),
									$this->_convertMobileNo($userData['UserProfile']['emergency_phone2']),
									$this->_convertMobileNo($userData['UserProfile']['emergency_phone3']));

							$this->sendMultipleSms($messageIncidentInitialised, $numbers);
						}
						$this->_set_response($data, 1, CALL_INITIATE_SUCCESS_MESSAGE, "", $responseArray);
					}
				}
				else
				{
					$responseArray = array();
					if ($modelName == "SafeCall")
					{
						$responseArray["SafeCallID"] = $returnedId;
					}
					else
					{
						$responseArray["IncidentID"] = $returnedId;
					}
					$this->_set_response($data, 0, "", CALL_INITIATE_FAILED_MESSAGE, $responseArray);
				}
			}
		}
		else
		{
			$this->_invalid_request($data);
		}
	}
	/*End Validate encrypted data*/
	
	/*update category info*/
	private function _update_incident_category_info($incidentID,$userId,$category,$subcategory)
	{
	$this->loadModel("IncidentDetail");
    $this->IncidentDetail->recursive = -1;
	$insertArr["IncidentDetail"] = array();
	$insertArr["IncidentDetail"]["id"] = "";
	$insertArr["IncidentDetail"]["incident_id"] = $incidentID;
	$insertArr["IncidentDetail"]["category"] = $category;
	$insertArr["IncidentDetail"]["subcategory"] = $subcategory;
	$insertArr["IncidentDetail"]["start_time"] = date("Y-m-d H:i:s");
	$insertArr["IncidentDetail"]["created_on"] = date("Y-m-d H:i:s");
	$insertArr["IncidentDetail"]["created_by"] = $userId;
	$insertArr["IncidentDetail"]["modified_on"] = date("Y-m-d H:i:s");
	$insertArr["IncidentDetail"]["modified_by"] = $userId;
	$this->IncidentDetail->save($insertArr);
	}
	/*End Category info*/
	

    /**
	 * Sends incident request to nearest responder
	 * 
	 * @param Integer $incidentID
	 */
	private function _send_request_to_responder($incidentID, $userID, $latitude, $longitude)
	{
		//Gets Nearest Responder
		$sql = "SELECT Responder.id, 
                ACOS( SIN( RADIANS( latitude ) ) * SIN( RADIANS( {$latitude} ) ) + 
                    COS( RADIANS( latitude ) ) * COS( RADIANS( {$latitude} )) * 
                    COS( RADIANS( longitude ) - RADIANS( {$longitude} )) ) * 6380 AS distance 
                FROM users AS Responder 
                WHERE ACOS( SIN( RADIANS( latitude ) ) * SIN( RADIANS( {$latitude} ) ) + 
                    COS( RADIANS( latitude ) ) * COS( RADIANS( {$latitude} )) * 
                    COS( RADIANS( longitude ) - RADIANS( {$longitude} )) ) * 6380 < " .
						GET_RESPONDER_DISTANCE_KM .
						" AND group_id = 5 AND is_active = 1 AND responder_status = 1 AND is_social_login=1 AND modified_on >= date_add(UTC_TIMESTAMP(), INTERVAL 2 MINUTE)
                ORDER BY distance
                LIMIT 1;";
        
        $this->loadModel("Incident");            
        $responder = $this->Incident->query($sql);
        
		if (!empty($responder))
		{
			$responderID = $responder[0]['Responder']['id'];

			//Sends request to Respodner
			$this->loadModel('IncidentResponder');

			$data = array('IncidentResponder' => array('incident_id' => $incidentID,
							'responder_user_id' => $responderID, 'created_by' => $userID,
							'modified_by' => $userID));

			$this->IncidentResponder->create();
			$this->IncidentResponder->save($data);

			//Changes the status fields in user table
			$this->loadModel('User');
			$this->User->id = $responderID;
			$this->User->saveField('responder_status', RESPONDER_REQUESTED_STATUS);
			$this->User->saveField('_is_incident_request_unsent', 1);
		}
	}

	/*
	 * For tracking customer
	 * @params Array $date
	 * @params String $ModelName  
	 */
	/*Encrypted Tracking data*/
	private function _tracking_encrypt($data, $modelName)
	{
		
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		}
		$td15 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td15, $secret_key, $iv); 
		$decrypted_userid = mdecrypt_generic($td15, $this->hex2bin2($data["Data"]["user_id"]));
		mcrypt_generic_deinit($td15); 
		mcrypt_module_close($td15); 
		$userId = trim($decrypted_userid);  
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_safe_call_id = mdecrypt_generic($td, $this->hex2bin2($data['Data']['safe_call_id']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$safe_call_id = trim($decrypted_safe_call_id);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_incident_id = mdecrypt_generic($td1, $this->hex2bin2($data['Data']['incident_id']));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$incident_id = trim($decrypted_incident_id);
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td5, $secret_key, $iv); 
		$decrypted_longitude1 = mdecrypt_generic($td5, $this->hex2bin2($data['Data']['longitude']));
		mcrypt_generic_deinit($td5); 
		mcrypt_module_close($td5); 
		$decrypted_longitude3 = trim($decrypted_longitude1);
		$decrypted_longitude = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $decrypted_longitude3);
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td6, $secret_key, $iv); 
		$decrypted_latitude1 = mdecrypt_generic($td6, $this->hex2bin2($data['Data']['latitude']));
		mcrypt_generic_deinit($td6); 
		mcrypt_module_close($td6); 
		$decrypted_latitude3 = trim($decrypted_latitude1);
		$decrypted_latitude = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $decrypted_latitude3);
		$this->loadModel("User");
        $this->User->recursive = -1;
		$userData = $this->User->findById($userId);
		if (empty($decrypted_latitude))
		  {
		$this->_set_response($data, 2, "", "Latitude is blank!", array());
		  }
		else if (empty($decrypted_longitude))
	      {
		 $this->_set_response($data, 2, "", "Longitude is blank!", array());
		  }
		else if (!empty($userData['User']['id']))
		{
			if ($this->_validate_call_encrypt_credentials($data, $modelName))
			{
				if($modelName == "Incident")
				{
				$notification = $this->_send_notifications($userId);
				}
				else
				{
				$notification = array();
				}
				//update longitude and latitude in safe call
				$updateArr[$modelName] = array();
				$updateArr[$modelName]["latitude"] = $decrypted_latitude;
				$updateArr[$modelName]["longitude"] = $decrypted_longitude;
				$updateArr[$modelName]["modified_on"] = date("Y-m-d H:i:s");
				$updateArr[$modelName]["modified_by"] = $userId;

				$this->loadModel($modelName);
				if ($modelName == "SafeCall")
				{
                    
					$this->{$modelName}->id = $safe_call_id;
                    $lastMsgSentOn = $this->{$modelName}->field("last_sent_date");
                    
                    $this->loadModel("Setting");
                    $this->Setting->id = 1;
                    $durationMinutes = $this->Setting->field("safe_call_tracking_duration");
                    if(empty($durationMinutes))
                    {
                        $durationMinutes = 5;
                    }
                    
                    if(date("H:i") == date("H:i", strtotime('+'.$durationMinutes. ' minutes', strtotime($lastMsgSentOn))))
                    {
                        $this->loadModel("User");
                        $this->User->contain("UserProfile");
                        $userData = $this->User->findById($userId);
                           
                        //Message Content
                        $messageSafeCallRunning = $userData['User']['firstname'] . ' ' . $userData['User']['lastname'] .
                                        " has reached Location: http://maps.google.com/?ll=" . $decrypted_latitude . "," . $decrypted_longitude. ". One Touch Response is tracking.";
                        
                        $numbers = array(
                                $this->_convertMobileNo($userData['UserProfile']['emergency_phone1']),
                                $this->_convertMobileNo($userData['UserProfile']['emergency_phone2']),
                                $this->_convertMobileNo($userData['UserProfile']['emergency_phone3']));

                        $this->sendMultipleSms($messageSafeCallRunning, $numbers);
                        
                        $this->{$modelName}->saveField("last_sent_date", date("Y-m-d H:i:s"), true);
                    }
                    
				}
				else
				{
					$this->{$modelName}->id = $incident_id;
				}
				if ($this->{$modelName}->save($updateArr))
				{
					//insert record in safe call tracking
					$insertArr[$modelName . "Tracking"] = array();
					$insertArr[$modelName . "Tracking"]["id"] = "";
					if ($modelName == "SafeCall")
					{
						$insertArr[$modelName . "Tracking"]["safe_call_id"] = $safe_call_id;
						/*Trackme_swipe_status*/
						$this->loadModel("trackme_add_infos");
						$this->trackme_add_infos->recursive = -1;
						$trackme_infos = $this->trackme_add_infos->find('first', array('conditions' => array("user_id"=>$userID),'order' => 'trackme_add_infos.id DESC'));
						$swipe_updated_time = $trackme_infos['trackme_add_infos']['modified_on'];
						$current_time = date("Y-m-d H:i:s");
						if(!empty($swipe_updated_time))
						{
							$interval  = strtotime($current_time)-strtotime($swipe_updated_time);
							if($interval>509)
							{
							$notification['Incident_move_status'] = 'Yes'; 
							}
							else
							{
							$notification['Incident_move_status'] = 'No'; 
							}
						}
						/*End Trackme_swipe_status*/
					}
					else
					{
						$insertArr[$modelName . "Tracking"]["incident_id"] = $incident_id;
					}
					$insertArr[$modelName . "Tracking"]["latitude"] = $decrypted_latitude;
					$insertArr[$modelName . "Tracking"]["longitude"] = $decrypted_longitude;
					$insertArr[$modelName . "Tracking"]["created_by"] = $userId;
					$insertArr[$modelName . "Tracking"]["modified_by"] = $userId;

					$this->loadModel($modelName . "Tracking");
					$this->{$modelName . "Tracking"}->create();
					if ($this->{$modelName . "Tracking"}->save($insertArr))
					{
						$this->_set_response($data, 1, TRACKING_UPDATE_SUCCESS, "", $notification);
					}
					else
					{
						$this->_invalid_request($data);
					}
				}
				else
				{
					$this->_invalid_request($data);
				}
			}
			else
			{
				$this->_set_response($data, 0, "", INACTIVE_CALL, array());
			}
		}
		else
		{
			$this->_invalid_request($data);
		}
	}
	
	private function _validate_call_encrypt_credentials($data, $modelName)
	{
		$conditionsArray = array();
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_safe_call_id = mdecrypt_generic($td, $this->hex2bin2($data['Data']['safe_call_id']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$safe_call_id = trim($decrypted_safe_call_id);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_incident_id = mdecrypt_generic($td1, $this->hex2bin2($data['Data']['incident_id']));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$incident_id = trim($decrypted_incident_id);
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td2, $secret_key, $iv); 
		$decrypted_imei = mdecrypt_generic($td2, $this->hex2bin2($data['Data']['imei']));
		mcrypt_generic_deinit($td2); 
		mcrypt_module_close($td2); 
		$imei3 = trim($decrypted_imei);
		$imei = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $imei3);
		if ($modelName == "SafeCall")
		{
			$conditionsArray["SafeCall.id"] = $safe_call_id;
		}
		else
		{
			$conditionsArray["Incident.id"] = $incident_id;
		}
		$conditionsArray["imei"] = $imei;
		$conditionsArray["status"] = STATUS_LIVE;

		$this->loadModel($modelName);
		(int) $results = $this->{$modelName}->find("count", array("conditions" => $conditionsArray));

		if ($results > 0)
		{
			return true;
		}
		return false;
	}
    /*End Encrypted data*/
	/*
	 * Check for valid call credentials or not
	 * @params  Array data
	 * @params String ModelName  
	 * return Boolean
	 */
	private function _validate_call_credentials($data, $modelName)
	{
		$conditionsArray = array();
		if ($modelName == "SafeCall")
		{
			$conditionsArray["SafeCall.id"] = $data["Data"]["safe_call_id"];
		}
		else
		{
			$conditionsArray["Incident.id"] = $data["Data"]["incident_id"];
		}
		$conditionsArray["imei"] = $data["Data"]["imei"];
		$conditionsArray["status"] = STATUS_LIVE;

		$this->loadModel($modelName);
		(int) $results = $this->{$modelName}->find("count", array("conditions" => $conditionsArray));

		if ($results > 0)
		{
			return true;
		}
		return false;
	}

	/*
	 * Safe call final status update or closing the call
	 * @params  Array data
	 * @params String ModelName 
	 */
	private function _status_update($data, $modelName)
	{
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		}
		$td15 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td15, $secret_key, $iv); 
		$decrypted_userid = mdecrypt_generic($td15, $this->hex2bin2($data["Data"]["user_id"]));
		mcrypt_generic_deinit($td15); 
		mcrypt_module_close($td15); 
		$userId = trim($decrypted_userid);
		
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_safe_call_id = mdecrypt_generic($td, $this->hex2bin2($data['Data']['safe_call_id']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$safe_call_id = trim($decrypted_safe_call_id);
		
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_incident_id = mdecrypt_generic($td1, $this->hex2bin2($data['Data']['incident_id']));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$incident_id = trim($decrypted_incident_id);
		
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td5, $secret_key, $iv); 
		$decrypted_longitude1 = mdecrypt_generic($td5, $this->hex2bin2($data['Data']['longitude']));
		mcrypt_generic_deinit($td5); 
		mcrypt_module_close($td5); 
		$decrypted_longitude3 = trim($decrypted_longitude1);
		$decrypted_longitude = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $decrypted_longitude3);
		
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td6, $secret_key, $iv); 
		$decrypted_latitude1 = mdecrypt_generic($td6, $this->hex2bin2($data['Data']['latitude']));
		mcrypt_generic_deinit($td6); 
		mcrypt_module_close($td6); 
		$decrypted_latitude3 = trim($decrypted_latitude1);
		$decrypted_latitude = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $decrypted_latitude3);
		
		$this->loadModel("User");
        $this->User->recursive = -1;
		$userData1 = $this->User->findById($userId);
		if (empty($decrypted_latitude))
		  {
		$this->_set_response($data, 2, "", "Latitude is blank!", array());
		  }
		else if (empty($decrypted_longitude))
	      {
		 $this->_set_response($data, 2, "", "Longitude is blank!", array());
		  }
		else if (!empty($userData1))
		{
			if ($this->_validate_call_encrypt_credentials($data, $modelName))
			{
                $this->loadModel("User");
                $this->User->recursive = 0;
				$userData = $this->User->findById($userId);
                
				//update longitude and latitude in safe call
				$updateArr[$modelName] = array();
				$updateArr[$modelName]["latitude"] = $decrypted_latitude;
				$updateArr[$modelName]["longitude"] = $decrypted_longitude;
				$updateArr[$modelName]["end_timestamp"] = date("Y-m-d H:i:s");
				$updateArr[$modelName]["modified_on"] = date("Y-m-d H:i:s");
				$updateArr[$modelName]["status"] = STATUS_CLOSE;
				$updateArr[$modelName]["modified_by"] = $userId;

				$this->loadModel($modelName);
				if ($modelName == "SafeCall")
				{
					$this->{$modelName}->id = $safe_call_id;
				}
				else
				{
					$this->{$modelName}->id = $incident_id;
					//Update is_active_call_closed so that next tracking will update responder                    
					$this->_setResponderActiveCallClosed($incident_id);
				}
                
				if ($this->{$modelName}->save($updateArr))
				{
                    if ($modelName == "SafeCall"):
                        //Message Content
                        $messageSafeCallClosed = $userData['User']['firstname'] . ' ' . $userData['User']['lastname'] .
                                        " has reached his/her destination at " . date('Y-m-d H:i:s') . 
                                        ". Location: http://maps.google.com/?ll=" . $decrypted_latitude . "," . $decrypted_longitude. ".";

                        $numbers = array(
                                $this->_convertMobileNo($userData['UserProfile']['emergency_phone1']),
                                $this->_convertMobileNo($userData['UserProfile']['emergency_phone2']),
                                $this->_convertMobileNo($userData['UserProfile']['emergency_phone3']));

                        $this->sendMultipleSms($messageSafeCallClosed, $numbers);
                    endif;  
                            
					//insert record in safe call tracking
					$insertArr[$modelName . "Tracking"] = array();
					$insertArr[$modelName . "Tracking"]["id"] = "";
					if ($modelName == "SafeCall")
					{
						$insertArr[$modelName . "Tracking"]["safe_call_id"] = $safe_call_id;
					}
					else
					{
						$insertArr[$modelName . "Tracking"]["incident_id"] = $incident_id;
					}
					$insertArr[$modelName . "Tracking"]["latitude"] = $decrypted_latitude;
					$insertArr[$modelName . "Tracking"]["longitude"] = $decrypted_longitude;
					$insertArr[$modelName . "Tracking"]["created_by"] = $userId;
					$insertArr[$modelName . "Tracking"]["modified_by"] = $userId;

					$this->loadModel($modelName . "Tracking");
					$this->{$modelName . "Tracking"}->create();
					if ($this->{$modelName . "Tracking"}->save($insertArr))
					{
						$insertArrDetail[$modelName . "Detail"] = array();
						$insertArrDetail[$modelName . "Detail"]["id"] = "";
						$insertArrDetail[$modelName . "Detail"]["created_by"] = $userId;
						$insertArrDetail[$modelName . "Detail"]["modified_by"] = $userId;

						if ($modelName == "SafeCall")
						{
							$insertArrDetail[$modelName . "Detail"]["safe_call_id"] =$safe_call_id;
						}
						else
						{
							$insertArrDetail[$modelName . "Detail"]["incident_id"] = $incident_id;
						}

						$this->loadModel($modelName . "Detail");
						$this->{$modelName . "Detail"}->create();
						if ($this->{$modelName . "Detail"}->save($insertArrDetail))
						{
							if ($modelName == "Incident"):
								//Message Content
								$messageIncidentClosed = "ONE TOUCH EMERGENCY RESPONSE" .
												"SOS activated by: " . $userData['User']['firstname'] . ' ' . $userData['User']['lastname'] .
												" at : " . date("Y-m-d H:i:s") . " has been closed";

								$numbers = array(
										$userData['UserProfile']['emergency_phone1'],
										$userData['UserProfile']['emergency_phone2'],
										$userData['UserProfile']['emergency_phone3']);

								$this->sendMultipleSms($messageIncidentClosed, $numbers);
                            endif;
                            
							$this->loadModel('IncidentResponder');
							$this->IncidentResponder->recursive = -1;
							$incidentdetails = $this->IncidentResponder->find('first', array('conditions' => array('incident_id' => $incident_id)));
							$incident_responders_id= $incidentdetails['IncidentResponder']['id'];
							$this->loadModel('IncidentResponder');
							$this->IncidentResponder->recursive = -1;
							$this->IncidentResponder->id = $incident_responders_id;
							$this->IncidentResponder->saveField('assign_responder', 1);
							$this->IncidentResponder->saveField('is_accepted', 1);
							$this->_set_response($data, 1, TRACKING_UPDATE_SUCCESS, "", array());
						}
						else
						{
							$this->_invalid_request($data);
						}
					}
					else
					{
						$this->_invalid_request($data);
					}
				}
				else
				{
					$this->_invalid_request($data);
				}
			}
			else
			{
				$this->_set_response($data, 0, "", INACTIVE_CALL, array());
			}
		}
		else
		{
			$this->_invalid_request($data);
		}
	}
 
	/*
	 * Function for upload the photo
	 * @params Array $data
	 */
	private function _photo_upload($data)
	{
		$userID = $this->_validate_authentication($data);
		if ($userID != 0)
		{
			//Get group id from the user id
			$this->loadModel("User");
			$this->User->recursive = -1;
			$groupArr = $this->User->findById($userID, array('fields' => 'group_id'));
			$groupID = $groupArr["User"]["group_id"];

			if ($groupID == CUSTOMER_GROUP_ID)
			{
				if ($this->_IsUserActive($userID, CUSTOMER_GROUP_ID))
				{
					if ($this->_IsPackageExpired($userID))
					{
						$this->_do_photo_upload($data, $userID);
					}
					else
					{
						$this->_set_response($data, 0, "", PACKAGE_EXPIRED, array());
					}
				}
				else
				{
					$this->_set_response($data, 0, "", INACTIVE_USER, array());
				}
			}
			else if ($groupID == RESPONDER_GROUP_ID)
			{
				$this->_responder_photo_upload($data, $userID);
			}
			else
			{
				$this->_set_response($data, 0, "", INVALID_GROUP, array());
			}
		}
		else
		{
			$this->_set_response($data, 0, "", INVALID_AUTHENTICATION, array());
		}
	}

	/**
	 * Uploads photo
	 * 
	 * @param array $data
	 * @param int $userID
	 * @param boolean $base64
	 */
	private function _do_photo_upload($data, $userID)
	{
		$insertArr["Photo"] = array();
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_reference_id = mdecrypt_generic($td, $this->hex2bin2($data['Data']['reference_id']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$reference_id = trim($decrypted_reference_id);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_latitude = mdecrypt_generic($td1, $this->hex2bin2($data['Data']['latitude']));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$latitude3 = trim($decrypted_latitude);
		$latitude = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $latitude3);
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td2, $secret_key, $iv); 
		$decrypted_longitude = mdecrypt_generic($td2, $this->hex2bin2($data['Data']['longitude']));
		mcrypt_generic_deinit($td2); 
		mcrypt_module_close($td2); 
		$longitude3 = trim($decrypted_longitude);
		$longitude = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $longitude3);
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td3, $secret_key, $iv); 
		$decrypted_photo = mdecrypt_generic($td3, $this->hex2bin2($data["Data"]["photo"]));
		mcrypt_generic_deinit($td3); 
		mcrypt_module_close($td3); 
		$photo = trim($decrypted_photo);
		if(preg_match('~^[a-z_\.0-9]+\.(jp[e]?g|png|gif)$~i', $photo))
        {
		$insertArr["Photo"]["id"] = "";
		$insertArr["Photo"]["user_id"] = $userID;
		if ($data["Data"]["type"] == "Incident")
		{
			$insertArr["Photo"]["type"] = "Incident";
			$insertArr["Photo"]["incident_id"] = $reference_id;
		}
		if ($data["Data"]["type"] == "SafeCall")
		{
			$insertArr["Photo"]["type"] = "Safe Call";
			$insertArr["Photo"]["safe_call_id"] = $reference_id;
		}
		$insertArr["Photo"]["photo"] = $photo;
		$insertArr["Photo"]["latitude"] = $latitude;
		$insertArr["Photo"]["longitude"] = $longitude;
		$insertArr["Photo"]["is_active"] = 1;
		$insertArr["Photo"]["created_by"] = $userID;
		$insertArr["Photo"]["modified_by"] = $userID;

		$this->loadModel("Photo");
		$this->Photo->Behaviors->unload('MeioUpload');
		$this->Photo->create();
		if ($this->Photo->save($insertArr))
		{
			$this->_set_response($data, 1, PICTURE_UPLOAD_SUCCESS, "", array());
			return true;
		}
		else
		{
			$this->_invalid_request($data);
		}
		return false;
		}
		else
		{
		$this->_set_response($data, 0, "", "Picture Upload format not valid", array());
		}
	}

	/*
	 * Function for upload the photo from customer side
	 * @params Array $data
	 */
	private function _responder_photo_upload($data, $userID)
	{
		if ($this->_validating_responder_photo_upload_credentials($data))
		{
			$countArr = '';
			if ($data["Data"]["type"] == "Incident")
			{
				//Check incident  is live or not
				$conditionsArr = array();
				$conditionsArr["Incident.id"] = $data["Data"]["reference_id"];
				$conditionsArr["Incident.status"] = STATUS_LIVE;
				$conditionsArr["Incident.end_timestamp"] = null;
				$this->loadModel("Incident");
				(int) $countArr = $this->Incident->find("count", array("conditions" => $conditionsArr));
			}
			else if ($data["Data"]["type"] == "General")
			{
				(int) $countArr = 1;
			}
			if ($countArr > 0)
			{
				$insertArr["Photo"] = array();
				$insertArr["Photo"]["id"] = "";
				$insertArr["Photo"]["user_id"] = $userID;
				$insertArr["Photo"]["type"] = $data["Data"]["type"];
				if ($data["Data"]["type"] == "Incident")
				{
					$insertArr["Photo"]["incident_id"] = $data["Data"]["reference_id"];
				}
				$insertArr["Photo"]["photo"] = $data["Data"]["photo"];
				$insertArr["Photo"]["latitude"] = $data["Data"]["latitude"];
				$insertArr["Photo"]["longitude"] = $data["Data"]["longitude"];
				$insertArr["Photo"]["created_by"] = $userID;
				$insertArr["Photo"]["modified_by"] = $userID;

				$this->loadModel("Photo");
				$this->Photo->Behaviors->unload('MeioUpload');
				$this->Photo->create();
				if ($this->Photo->save($insertArr))
				{
					$this->_set_response($data, 1, PICTURE_UPLOAD_SUCCESS, "", array());
				}
				else
				{
					$this->_invalid_request($data);
				}
			}
			else
			{
				$this->_invalid_request($data);
			}
		}
		else
		{
			$this->_invalid_request($data);
		}
	}

	/*
	 * Function for validating the customer photo upload credentials
	 * @params Array $data
	 */
	private function _validating_responder_photo_upload_credentials($data)
	{
		$dataArr = $data["Data"];
		if (isset($dataArr["type"]) && !empty($dataArr["type"]) && $dataArr["type"] == "General" || $dataArr["type"] == "Incident")
		{
			if ($dataArr["type"] == "incident" && empty($dataArr["reference_id"]))
			{
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Function creates service log
	 * 
	 * @param array $input_data
	 */
	private function _create_log($input_data)
	{
		//Checks if base64 photo string is there then unset it 
		if (isset($input_data['Data']['photo_string_base64']))
			unset($input_data['Data']['photo_string_base64']);

		$userID = 0;
		$groupID = 0;
		$userName = !empty($input_data['Authentication']['username']) ? $input_data['Authentication']['username'] : null;
		
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
		//iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$Ausername = mdecrypt_generic($td, $this->hex2bin2($input_data['Authentication']['username']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$username1 = trim($Ausername);
		$username2 = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $username1);
		
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$Auser_id = mdecrypt_generic($td1, $this->hex2bin2($input_data['Data']['user_id']));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$user_id2 = trim($Auser_id);
		$user_id3 = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $user_id2);
		
		$usernewusername = !empty($username2) ? $username2 : null;
		
	    $user_id4 = !empty($user_id3) ? $user_id3 : null;

		if ($userName != null)
		{
			$this->loadModel("User");
			$userData = $this->User->find("all", array('fields' => array('id', 'group_id'),
					"recursive" => -1, 'conditions' => array("User.username" => $userName)));

			if (isset($userData) && !empty($userData))
			{
				$userID = $userData[0]["User"]["id"];
				$groupID = $userData[0]["User"]["group_id"];
			}
		}
		else if($usernewusername !=null)
		{
		    $this->loadModel("User");
			$userData = $this->User->find("all", array('fields' => array('id', 'group_id'),
					"recursive" => -1, 'conditions' => array("User.username" => $usernewusername)));

			if (isset($userData) && !empty($userData))
			{
				$userID = $userData[0]["User"]["id"];
				$groupID = $userData[0]["User"]["group_id"];
			}
		}
	    else if($user_id4 !=null)
		{
		    $this->loadModel("User");
			$userData = $this->User->find("all", array('fields' => array('id', 'group_id'),
					"recursive" => -1, 'conditions' => array("User.id" => $user_id4)));

			if (isset($userData) && !empty($userData))
			{
				$userID = $userData[0]["User"]["id"];
				$groupID = $userData[0]["User"]["group_id"];
			}
		}
		
		$serviceTypes = array_flip(StaticArrays::$saServiceTypes);
        
		$insertLogArr["Log"] = array();
		$insertLogArr["Log"]["id"] = "";
		$insertLogArr["Log"]["group_id"] = $groupID;
		$insertLogArr["Log"]["user_id"] = $userID;
		$insertLogArr["Log"]["service_type"] = $serviceTypes[$input_data['Authentication']['name']];
		$insertLogArr["Log"]["request_params"] = serialize($input_data);
		$insertLogArr["Log"]["actual_request"] = json_encode($input_data);
		$insertLogArr["Log"]["created_by"] = 1;
		$insertLogArr["Log"]["modified_by"] = 1;
		$insertLogArr["Log"]["created_on"] = date("Y-m-d H:i:s");
		$insertLogArr["Log"]["modified_on"] = date("Y-m-d H:i:s");

		$this->loadModel("Log");
		$this->Log->create();
		$this->Log->save($insertLogArr);
		$this->logID = $this->Log->id;
	}

	/*
	 * Funtion for inserting the logs
	 * @params Array $responseData
	 */
	private function _update_log($input_data, $response_status, $response)
	{
		//Checks if base64 photo string is there then unset it 
		if (isset($input_data['Data']['photo_string_base64']))
			unset($input_data['Data']['photo_string_base64']);

		$updateLogArr["Log"] = array();
		$updateLogArr["Log"]["id"] = $this->logID;
		$updateLogArr["Log"]["response_params"] = serialize($response);
		$updateLogArr["Log"]["response_status"] = $response_status;
		$updateLogArr["Log"]["actual_response"] = json_encode($response);
		$this->loadModel("Log");
		$this->Log->save($updateLogArr);
	}

	/*
	 * Notification send to user
	 * @params Int $userID
	 */
	private function _send_notifications($userID, $incidentRequest = 0)
	{
		$finalNotifications = array();
		$finalNotify = array();
		$incidentNotification = array();
        $read  =array();
		if ($this->_check_outstanding_notification($userID))
		{

			    
				/*Read & unread notification*/
				$this->loadModel("NotificationDetail");
				$conditionArray2 = array();
				
				$conditionArray2["NotificationDetail.user_id"] = $userID;
				
				$outstandingNotificationArr2 = $this->NotificationDetail->find("all", array("conditions" => $conditionArray2));
                $j=0; 
				foreach ($outstandingNotificationArr2 as $value1)
				{
					  
					  $this->loadModel("Notification");
					  $this->Notification->recursive = -1;
					
					  $conditionArr2 = array();
					  $conditionArr2["Notification.id"] = $value1["NotificationDetail"]["notification_id"];
					  $notificationDataArr2 = $this->Notification->find("first", array("conditions" => $conditionArr2));
					  
					  $read[$j]['id'] = $notificationDataArr2['Notification']['id'];
                      $read[$j]['title'] = $notificationDataArr2['Notification']['title'];
					  $read[$j]['message'] = $notificationDataArr2['Notification']['message'];
					  $read[$j]['Date_Time'] = $notificationDataArr2['Notification']['created_on'];
					  if($value1["NotificationDetail"]["is_push_sent"]==1)
					  {
					  $read[$j]['Status'] = "Read";
					  }
					  else
					  {
					  $read[$j]['Status'] = "Unread";
					  }
					  
					  $j++;
					  
					  $updateParams1 = array();
					  $updateParams1["NotificationDetail"]["is_push_sent"] = 1;
					  $updateParams1["NotificationDetail"]["modified_by"] = $userID;

					  $this->NotificationDetail->id = $value1["NotificationDetail"]["id"];
					  $this->NotificationDetail->save($updateParams1); 
					  
				}
				 /*End Read & unread notification*/
				 
				$finalNotifications = $read;
				
				$this->_update_users_table($userID);
			
		 }
		//In case responder tracking
		if ($incidentRequest == 1)
		{
			//Adds responder request information
			$this->loadModel('User');
			$this->User->id = $userID;
			$isIncidentRequest = $this->User->field('_is_incident_request_unsent');
			//If there is any incident request, that need to send
			//echo $isIncidentRequest;
			if ($isIncidentRequest == 1)
			{
				$this->User->saveField('_is_incident_request_unsent', 0);
				$this->loadModel('IncidentResponder');
				$this->IncidentResponder->contain(array('Incident', 'Incident.User', 'Incident.User.UserProfile'));
				$incidentResponders = $this->IncidentResponder->find('first', array('conditions' =>array('responder_user_id' => $userID, 'is_accepted' => 0,'Incident.status' => STATUS_LIVE)));

				$requestData = array();
				if (isset($incidentResponders) && !empty($incidentResponders)):
                $incidentID = $incidentResponders['IncidentResponder']['incident_id'];		
				$this->loadModel('Incident');
				$this->Incident->recursive = -1;
				$incident = $this->Incident->find('first', array('conditions' => array('id' => $incidentID)));
				$customer_ID= $incident['Incident']['customer_user_id'];
				$this->loadModel("CustomerRecord");
				$this->CustomerRecord->recursive = -1;
				$userDetails = $this->CustomerRecord->find("first", array("conditions" => array("user_id" => $customer_ID)));
				$requestData['incident_id'] = $incidentID;
				$requestData['latitude'] = $incident['Incident']['latitude'];
				$requestData['longitude'] = $incident['Incident']['longitude'];
				$requestData['customer_name'] =$userDetails['CustomerRecord']['fullname'];
				$requestData['customer_mobile'] =$userDetails['CustomerRecord']['mobile'];
				$requestData['customer_age'] = date_diff(date_create($userDetails['CustomerRecord']['dob']), date_create('today'))->y;
				$requestData['customer_gender'] = $userDetails['CustomerRecord']['gender'];
				$requestData['event_status'] = $incident['Incident']['status'];
				endif;
				$incidentNotification['IncidentRequest'] = $requestData;
			}
		}
		$Notifications["Notifications"] = $finalNotifications;
		$finalNotify = array_merge($Notifications, $incidentNotification);
		//***********************************
		
		if(!empty($incidentNotification))
		{
		$finalNotify = array_merge($Notifications, $incidentNotification);
		}
		else
		{
	    $this->loadModel('IncidentResponder');
		$incidentResponders = $this->IncidentResponder->find('first', array('conditions' =>array('responder_user_id' => $userID, 'is_accepted' => 1),"order" => array("IncidentResponder.id" => "DESC")));
        $requestData = array();
		$incidentID = $incidentResponders['IncidentResponder']['incident_id'];		
		$this->loadModel('Incident');
		$this->Incident->recursive = -1;
	    $incident = $this->Incident->find('first', array('conditions' => array('id' => $incidentID)));
		$event_status = array();
		$event_status['event_status'] = $incident['Incident']['status'];
		$Notifications["Notifications"] = $event_status;
		$finalNotify = $Notifications;
		}

        return $finalNotify;
	}

	/*
	 * Check outstanding notification
	 * @params Int $userID
	 */
	private function _check_outstanding_notification($userID)
	{
		$checkParams = array();
		$checkParams["User.id"] = $userID;
		/*$checkParams["User.is_unsent_push"] = 0;*/

		$this->loadModel("User");
		(int) $countArr = $this->User->find("count", array("conditions" => $checkParams));
		if ($countArr > 0)
		{
			return true;
		}
		return false;
	}

	/*
	 * Update table and set that all pending notifications
	 * has been sent
	 * update is_unsent_push = 1
	 * @params Int $userID
	 */
	private function _update_users_table($userID)
	{
		$updateParams = array();
		$updateParams["User"]["is_unsent_push"] = 0;

		$this->loadModel("User");
		$this->User->id = $userID;
		$this->User->save($updateParams);
	}

	/*
	 * For send the pending notifications to the users
	 * @params Int $userID
	 */
	private function _send_pending_notifications($data)
	{
		$intUserId = $this->_validate_authentication($data);
		if ($intUserId != 0)
		{
			$this->User->recursive = -1;
			$groupID = $this->User->findById($intUserId, array("fields" => "group_id"));
			if ($this->_IsUserActive($intUserId, $groupID["User"]["group_id"]))
			{
				if ($groupID["User"]["group_id"] == CUSTOMER_GROUP_ID)
				{
					if ($this->_IsPackageExpired($intUserId))
					{
						$notification = $this->_send_notifications($intUserId);
						$this->_set_response($data, 1, NOTIFICATION_SENT, "", $notification);
					}
				}
				else if ($groupID["User"]["group_id"] == RESPONDER_GROUP_ID)
				{
					$notification = $this->_send_notifications($intUserId);
					$this->_set_response($data, 1, NOTIFICATION_SENT, "", $notification);
				}
				else
				{
					$this->_invalid_request($data);
				}
			}
		}
	}

	
	/**
	 * Adds Angle information in response data
	 * 
	 * @param type $responseData
	 * @param type $userId
	 */
	private function _getAngelInfo(&$responseData, $userId)
	{
		$this->loadModel('UserProfile');
		$this->loadModel('User');
		$this->UserProfile->recursive = -1;
		$this->User->recursive = -1;
		$angles = $this->UserProfile->find('first', array('conditions' => array('user_id' => $userId)));
		$customerNameArr = $this->User->findById($userId);
		if (!empty($angles))
		{
			$responseData['customer_name'] = $customerNameArr['User']['firstname'] . ' ' . $customerNameArr['User']['lastname'];
			$responseData['customer_mobile'] = $customerNameArr['User']['mobile'];
			if($customerNameArr['User']['parent_user_id']=='90225')
			{
			$responseData['customer_app_logo'] = "http://apps1.onetouchresponse.com/img/app_logo/sc.png";
			$responseData['customer_iosapp_logo1'] = "http://apps1.onetouchresponse.com/img/app_logo/sc.png";
			$responseData['customer_iosapp_logo2'] = "http://apps1.onetouchresponse.com/img/app_logo/sc.png";
			$responseData['customer_iosapp_logo3'] = "http://apps1.onetouchresponse.com/img/app_logo/sc.png";
			}
			else
			{
			$responseData['customer_app_logo'] = "http://apps1.onetouchresponse.com/img/app_logo/logo.png";
			$responseData['customer_iosapp_logo1'] = "http://apps1.onetouchresponse.com/img/app_logo/otr.png";
			$responseData['customer_iosapp_logo2'] = "http://apps1.onetouchresponse.com/img/app_logo/otr.png";
			$responseData['customer_iosapp_logo3'] = "http://apps1.onetouchresponse.com/img/app_logo/otr.png";
			}
			$responseData['emergency_name1'] = $angles['UserProfile']['emergency_name1'];
			$responseData['emergency_phone1'] = $angles['UserProfile']['emergency_phone1'];
			$responseData['emergency_email1'] = $angles['UserProfile']['emergency_email1'];
			$responseData['emergency_relation1'] = $angles['UserProfile']['emergency_relation1'];
			$responseData['emergency_name2'] = $angles['UserProfile']['emergency_name2'];
			$responseData['emergency_phone2'] = $angles['UserProfile']['emergency_phone2'];
			$responseData['emergency_email2'] = $angles['UserProfile']['emergency_email2'];
			$responseData['emergency_relation2'] = $angles['UserProfile']['emergency_relation2'];
			$responseData['emergency_name3'] = $angles['UserProfile']['emergency_name3'];
			$responseData['emergency_phone3'] = $angles['UserProfile']['emergency_phone3'];
			$responseData['emergency_email3'] = $angles['UserProfile']['emergency_email3'];
			$responseData['emergency_relation3'] = $angles['UserProfile']['emergency_relation3'];
			if($customerNameArr['User']['customer_code']=='ADD-ON USER')
			{
			$responseData['my_family'] = "no";
			}
			else
			{
			$responseData['my_family'] = "yes";
			}
			
		}
	}

	private function _getServicesInfo(&$responseData, $userId)
	{
		$this->loadModel('User');
		$packageId = $this->User->getCurrentPackageIdFromUserId($userId);

		$services = "00";
		if (!empty($packageId))
		{
			$this->loadModel('Package');
			$package = $this->Package->find('first', array('conditions' => array('id' => $packageId)));
			if (!empty($package))
			{
				$incidents = $package['Package']['number_of_incidents'] == 0 ? "0" : "1";
				$safecalls = $package['Package']['number_of_safe_calls'] == 0 ? "0" : "1";
				$services = $incidents . $safecalls;
			}
		}
		$responseData['services'] = $services;
	}
	
	private function _getIncidentsRemainingBalance(&$responseData, $userId)
	{
		$this->loadModel('OrderDetail');
		$this->OrderDetail->recursive = -1;
		$lastOrderDetail = $this->OrderDetail->find("first", array("conditions" => array("user_id" => $userId, "order_status" => 1), "order" => array("OrderDetail.id" => "DESC")));
		$remainingIncidents = 0;
		if (!empty($lastOrderDetail))
		{
			if($lastOrderDetail["OrderDetail"]["total_incidents"] == -1)
			{
				$remainingIncidents = -1;
			}
			else
			{
				$remainingIncidents = $lastOrderDetail["OrderDetail"]["total_incidents"] - $lastOrderDetail["OrderDetail"]["used_incidents"];
			}
		}
		
		$responseData['remainingIncidents'] = $remainingIncidents;
	}
	
	private function _getSafecallsRemainingBalance(&$responseData, $userId)
	{
		$this->loadModel('OrderDetail');
		$this->OrderDetail->recursive = -1;
		$lastOrderDetail = $this->OrderDetail->find("first", array("conditions" => array("user_id" => $userId, "order_status" => 1), "order" => array("OrderDetail.id" => "DESC")));
		$remainingSafeCalls = 0;
		if (!empty($lastOrderDetail))
		{
			if($lastOrderDetail["OrderDetail"]["total_safe_calls"] == -1)
			{
				$remainingSafeCalls = -1;
			}
			else
			{
				$remainingSafeCalls = $lastOrderDetail["OrderDetail"]["total_safe_calls"] - $lastOrderDetail["OrderDetail"]["used_safe_calls"];
			}
		}
		
		$responseData['remainingSafeCalls'] = $remainingSafeCalls;
	}

	/**
	 * Checks if user have any active call
	 * 
	 * @param int $userID
	 * @return boolean
	 */
	private function _havingActiveCalls($userID, $imei)
	{
		$this->loadModel('User');
		$this->User->recursive = -1;

		$user = $this->User->find('first', array('conditions' => array(
						'id' => $userID
		)));

		if ($user['User']['last_imei'] == $imei)
		{
			return false;
		}

		$this->loadModel('Incident');
		$this->Incident->recursive = -1;

		$conditions = array(
				'customer_user_id' => $userID,
				'status' => STATUS_LIVE,
				'is_active' => 1,
				'is_deleted' => 0
		);

		$incidents = $this->Incident->find('count', array('conditions' => $conditions));

		if ($incidents > 0)
		{
			return true;
		}

		$this->loadModel('SafeCall');
		$this->SafeCall->recursive = -1;

		$safeCalls = $this->SafeCall->find('count', array('conditions' => $conditions));

		if ($safeCalls > 0)
		{
			return true;
		}

		return false;
	}

	/**
	 * Checks if user have any active call then adds in responseData
	 * 
	 * @param array $responseData
	 * @param int $userID
	 * @return boolean
	 */
	private function _getActiveCalls(&$responseData, $userID)
	{
		$this->loadModel('User');
		$this->User->recursive = -1;

		$user = $this->User->find('first', array('conditions' => array(
						'id' => $userID
		)));

		$this->loadModel('Incident');
		$this->Incident->recursive = -1;

		$conditions = array(
				'customer_user_id' => $userID,
				'status' => STATUS_LIVE,
				'end_timestamp' => null,
				'is_active' => 1,
				'is_deleted' => 0
		);

		$incident = $this->Incident->find('first', array('conditions' => $conditions));

		if (count($incident) > 0)
		{
			$responseData['active_incident'] = $incident['Incident']['id'];
		}
		else
		{
			$responseData['active_incident'] = 0;
		}

		$this->loadModel('SafeCall');
		$this->SafeCall->recursive = -1;

		$safecall = $this->SafeCall->find('first', array('conditions' => $conditions));
		if (count($safecall) > 0)
		{
			$responseData['active_safecall'] = $safecall['SafeCall']['id'];
		}
		else
		{
			$responseData['active_safecall'] = 0;
		}

		return false;
	}

	/**
	 * Checks if responder has any active call then adds in responseData
	 * 
	 * @param array $responseData
	 * @param int $userID
	 */
	private function _getResponderActiveCalls($responseData, $userID)
	{
		$this->loadModel('IncidentResponder');
		$this->IncidentResponder->recursive = -1;

		$incident = $this->IncidentResponder->query("
			SELECT 
			I.id, CONCAT(U.firstname, ' ', U.lastname) AS customer_name,
			U.latitude, U.longitude, U.mobile, UP.dob, UP.gender
			FROM incident_responders IR
			INNER JOIN incidents I ON IR.incident_id = I.id
			INNER JOIN users U ON IR.responder_user_id = U.id  
			LEFT JOIN user_profiles UP ON UP.user_id = U.id
			WHERE IR.responder_user_id = {$userID} 
			AND IR.is_accepted = 1 
			AND I.status = 1 
			LIMIT 1");

		if (!empty($incident))
		{
            $age = !empty($incident[0]['UP']['age'])?$this->_getAge($incident[0]['UP']['age']):"";
			$responseData['ActiveIncident']['incident_id'] = $incident[0]['I']['id'];
			$responseData['ActiveIncident']['latitude'] = $incident[0]['U']['latitude'];
			$responseData['ActiveIncident']['longitude'] = $incident[0]['U']['longitude'];
			$responseData['ActiveIncident']['customer_name'] = $incident[0][0]['customer_name'];
			$responseData['ActiveIncident']['customer_mobile'] = $incident[0]['U']['mobile'];
			$responseData['ActiveIncident']['customer_age'] = $age;
			$responseData['ActiveIncident']['customer_gender'] = $incident[0]['UP']['gender'];
		}
		else
		{
			$responseData['ActiveIncident']['incident_id'] = 0;
			$responseData['ActiveIncident']['latitude'] = '';
			$responseData['ActiveIncident']['longitude'] = '';
			$responseData['ActiveIncident']['customer_name'] = '';
			$responseData['ActiveIncident']['customer_mobile'] = '';
			$responseData['ActiveIncident']['customer_age'] = '';
			$responseData['ActiveIncident']['customer_gender'] = '';
		}
	}

	/**
	 * Udates user's last IMEI
	 * 
	 * @param Int $userID
	 * @param String $imei
	 */
	private function _updateUserIMEI($userID, $imei)
	{
		$this->loadModel('User');
		$this->User->recursive = -1;

		$this->User->id = $userID;
		$this->User->saveField('last_imei', $imei);
	}

	/**
	 * Checks user's last IMEI
	 * 
	 * @param Int $userID
	 * @param String $imei
	 */
	private function _checkUserIMEI($userID, $imei)
	{
		$this->loadModel('User');
		$this->User->recursive = -1;

		$this->User->id = $userID;
		return $this->User->field('last_imei') == $imei;
	}

	/**
	 * Sets responder's active call closed status in user table, so that next tracking
	 * services will update responder's app about the call status
	 * 
	 * @param int $incidentID
	 */
	private function _setResponderActiveCallClosed($incidentID)
	{
		$this->loadModel('IncidentResponder');
		$this->IncidentResponder->recursive = -1;
		$responder = $this->IncidentResponder->find('first', array('conditions' =>
				array('incident_id' => $incidentID)));

		if (!empty($responder))
		{
			$this->loadModel('User');
			$this->User->id = $responder['IncidentResponder']['responder_user_id'];
			$this->User->saveField('is_active_call_closed', 1);

			$this->_setResponderOnline($responder['IncidentResponder']['responder_user_id']);
		}
	}

	/**
	 * Checks if responder's call closed
	 * 
	 * @param type $notifications
	 * @param type $responderID
	 */
	private function _checkResponderCallClosed(&$notifications, $responderID)
	{
		$this->loadModel('User');
		$this->User->recursive = -1;
		$responder = $this->User->find('first', array('conditions' =>
				array('id' => $responderID)));

		if ($responder['User']['is_active_call_closed'])
		{
			$notifications['active_call_closed'] = 1;
		}
		else
		{
			$notifications['active_call_closed'] = 0;
		}
		//After sending notification we need to reset the flag so that next tracking 
		//will not get it 
		$this->User->saveField('is_active_call_closed', 0);
	}

	/**
	 * Set's responder status to online
	 * 
	 * @param int $responderID
	 */
	private function _setResponderOnline($responderID)
	{
		$this->loadModel('User');
		$this->User->id = $responderID;

		$this->User->saveField('responder_status', 1);
	}

   /**
	 * Set login status
	 * 
	 * @param int $intUserId
	 */
   private function _updateloginstatus($intUserId)
	{
		$this->loadModel('User');
		$this->User->id = $intUserId;

		$this->User->saveField('is_social_login', 1);
	}
	
	
	
	 private function _update_logout_status($data)
	{
		
		$cipher = "rijndael-128"; 
		$mode = "cbc"; 
		$secret_key = "D4:6E:AC:3F:F0:BE"; 
	     //iv length should be 16 bytes 
		$iv = "fedcba9876543210"; 
		$key_len = strlen($secret_key); 
		if($key_len < 16 ){ 
		$addS = 16 - $key_len; 
		for($i =0 ;$i < $addS; $i++){ 
		$secret_key.=" "; 
		} 
		}else{ 
		$secret_key = substr($secret_key, 0, 16); 
		} 
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_user_id = mdecrypt_generic($td, $this->hex2bin2($data["Data"]["user_id"]));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$user_Id3 = trim($decrypted_user_id);
		$user_Id = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $user_Id3);
        $this->loadModel("User");
	    $this->User->recursive = -1;
	    $userDetail = $this->User->find("first", array("conditions" => array("User.id" => $user_Id)));
	    if(empty($userDetail['User']['id']))
	    {
	    $this->_set_response($data, 0, "", "There is Something Wrong!!", array()); 
	    }
		else
		{
		$this->User->id = $user_Id;
        $this->User->saveField('is_social_login', 0);
		$this->User->saveField('logout_time', date("Y-m-d H:i:s"));
		$this->_set_response($data, 1, "Logout Successfully", "");
		}
		
		
	}
	
	/*
	 * Check that user have safe calls or incidents remaining in account
	 * or not
	 * @param int $userId
	 * @param string $modelName
	 */
	private function _checkSafeCallsIncidentsBalance($userId, $modelName)
	{
		$this->loadModel("OrderDetail");
		$this->OrderDetail->recursive= -1;
		$userOrderDetail = $this->OrderDetail->find("first", array("conditions" => array("OrderDetail.user_id" => $userId), "order" => array("OrderDetail.id" => "DESC")));
		if ($modelName == "Incident")
		{
			//in case user have unlimited no of incidents
			if ($userOrderDetail['OrderDetail']['total_incidents'] == -1)
			{
				return true;
			}
			else if ((int) $userOrderDetail['OrderDetail']['total_incidents'] > $userOrderDetail['OrderDetail']['used_incidents'])
			{
				return true;
			}
			return false;
		}
		else
		{
			//in case user have unlimited no of safecalls
			if ($userOrderDetail['OrderDetail']['total_safe_calls'] == -1)
			{
				return true;
			}			
			else if ((int) $userOrderDetail['OrderDetail']['total_safe_calls'] > $userOrderDetail['OrderDetail']['used_safe_calls'])
			{
				return true;
			}
			return false;
		}
	}

	/*
	 * Convert base64 to jpeg image
	 */
	private function base64_to_jpeg($base64_string, $output_file)
	{
		$ifp = fopen($output_file, "wb");
		fwrite($ifp, base64_decode($base64_string));
		fclose($ifp);
		return( $output_file );
	}
	
	/*
	 * Check balance calls quota 
	 */
	private function _checkBalanceCallQuota($userId, $modelName)
	{
		$this->loadModel("OrderDetail");
		
		$userOrderDetail = $this->OrderDetail->find("first", array("conditions" => array("OrderDetail.user_id" => $userId), "order" => array("OrderDetail.id" => "DESC")));

		//checks if unlimited package
		if($userOrderDetail["OrderDetail"]["total_incidents"] != -1 && $userOrderDetail["OrderDetail"]["total_safe_calls"] != -1)
		{
			//checks total quota used or not
			if($userOrderDetail["OrderDetail"]["total_incidents"] == $userOrderDetail["OrderDetail"]["used_incidents"] && $modelName == "Incident")
			{
				return false;
			}
			if($userOrderDetail["OrderDetail"]["total_safe_calls"] == $userOrderDetail["OrderDetail"]["used_safe_calls"] && $modelName == "SafeCall")
			{
				return false;
			}
		}
		return true;
	}
	
	private function _getRandomNumericString($length = 10) 
	{
		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) 
		{
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	private function _getAge($birthday)
	{
		 $age = strtotime($birthday);
		if($age === false)
		{
			return false;
		}
		list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age));
		$now = strtotime("now");
		list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now));
		$age = $y2 - $y1;
		if((int)($m2.$d2) < (int)($m1.$d1))
		$age -= 1;
		return $age; 
	}
    
 private function _generateFamilyCoupons($userInfoArray)
    {
        /*$usageLimit = 0;*/
		$users_id = $userInfoArray['id'];
		$userorderInfo23 = array();
		$this->loadModel('Package');
		$packages = $this->Package->find("first", array("conditions" => array("id" => $userInfoArray["package_id"])));
		$packagename = $packages['Package']['name'];	
		$userorderInfo23["fullname"]   =  $userInfoArray["fullname"];
        $userorderInfo23["email"]   =    $userInfoArray["email"];
	    $userorderInfo23["mobile"]   =   $userInfoArray["mobile"];
        $userorderInfo23["package_name"]   =  $packagename;
	    $userorderInfo23["order_id"]   =  $userInfoArray["order_id"];
        $userorderInfo23["transaction_id"]   =  $userInfoArray["transaction_id"];
	    $userorderInfo23["gender"]   =  $userInfoArray['profile_information']["gender"];
        $userorderInfo23["coupon_code"]   =  $userInfoArray["coupon_code"];
	    $userorderInfo23["emergency_name1"]   =  $userInfoArray['profile_information']["emergency_name1"];
        $userorderInfo23["emergency_phone1"]   =  $userInfoArray['profile_information']["emergency_phone1"];
	    $userorderInfo23["emergency_email1"]   =  $userInfoArray['profile_information']["emergency_email1"];
        $userorderInfo23["emergency_relation1"]   =  $userInfoArray['profile_information']["emergency_relation1"];
        if($userInfoArray["package_id"] == MONTHLY_PACKAGE_ID)
		{
			$usageLimit = FAMILY_COUPON_USERS_LIMIT_MONTHLY;
		}
		else if($userInfoArray["package_id"] == QUATERLY_PACKAGE_ID)
		{
			$usageLimit = FAMILY_COUPON_USERS_LIMIT_QUATERLY;
		}
		else if($userInfoArray["package_id"] == YEARLY_PACKAGE_ID)
		{
			$usageLimit = FAMILY_COUPON_USERS_LIMIT_ANNUALY;
		}
		else if($userInfoArray["package_id"] == ANNUAL_PACKAGE_ID)
		{
			$usageLimit = 3;
		}
		else if($userInfoArray["package_id"] == HALFERLY_PACKAGE_ID)
		{
			$usageLimit = FAMILY_COUPON_USERS_LIMIT_HALFRLY;
		}
		else if($userInfoArray["package_id"] == SAKHA_PACKAGE_ID)
		{
			$usageLimit = 3;
		}
		else if($userInfoArray["package_id"] == FREE_PACKAGE_ID)
		{
			$usageLimit = 1;
		}
		else if($userInfoArray["package_id"] == APOLO1_PACKAGE_ID)
		{
			$usageLimit = 4;
			$this->loadModel("User");
			$this->User->recursive = -1;
			$userDetail = $this->User->find("first", array("conditions" => array("User.id" =>$users_id)));
			$this->User->saveField('parent_user_id', 38753);
			  
		}
		else if($userInfoArray["package_id"] == APOLO_PACKAGE_ID)
		{
			$usageLimit = 4;
			$this->loadModel("User");
			$this->User->recursive = -1;
			$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $users_id)));
			$this->User->saveField('parent_user_id', 38753);
		}
		else if($userInfoArray["package_id"] == NEW_YEARLY_PACKAGE_ID)
		{
			$usageLimit = 5;
			$this->loadModel("User");
			$this->User->recursive = -1;
			$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $users_id)));
			$this->User->saveField('parent_user_id', 40121);
		}
	    else if($userInfoArray["package_id"] == NEW_STANDERD_ANNUAL_PKG_ID)
		{
			$usageLimit = 5;
		}
	    else if($userInfoArray["package_id"] == COMPLIMENTRY_PACKAGE_ID)
	    {
			$usageLimit = 5;
	    }
	    else if($userInfoArray["package_id"] == NEW_HALFERLY_PACKAGE_ID)
		{
	        $usageLimit = 4;
		}
	    else if($userInfoArray["package_id"] == 32)
		{
			$usageLimit = 3;
			$this->loadModel("User");
			$this->User->recursive = -1;
			$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $users_id)));
			$this->User->saveField('parent_user_id', 35542);
		}
		else if($userInfoArray["package_id"] == 47)
		{
			$usageLimit = 3;
			$this->loadModel("User");
			$this->User->recursive = -1;
			$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $users_id)));
			$this->User->saveField('parent_user_id', 52137);
		}
	    else if($userInfoArray["package_id"] == MEDTRONIC_PACKAGE_ID)
		{
			$usageLimit = 3;
			$this->loadModel("User");
			$this->User->recursive = -1;
			$userDetail = $this->User->find("first", array("conditions" => array("User.id" =>$users_id)));
			$this->User->saveField('parent_user_id', 40181);
		  
		}
	    else if($userInfoArray["package_id"] == COVADIAN_PACKAGE_ID)
	    {
			$usageLimit = 3;
			$this->loadModel("User");
			$this->User->recursive = -1;
			$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $users_id)));
			$this->User->saveField('parent_user_id', 40182);
			   
		}
		
		else if($userInfoArray["package_id"] == 21)
		{
			$usageLimit = 3;
			$this->loadModel("User");
			$this->User->recursive = -1;
			$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $users_id)));
			$this->User->saveField('parent_user_id', 33818);
		}
		
	    else
	    {
		    $usageLimit = 3;
	    }
        $this->loadModel("Coupon");
        $this->Coupon->recursive = -1;
        $couponArr1 = $userInfoArray["package_id"];
        $couponArr2 = $userInfoArray['id'];
        $couponArr3 = $this->generateCouponCodesRandom();
        $couponArr4 = "Family coupons";
        $couponArr5 = date("Y-m-d");
        $couponArr6 =  date('Y-m-d', strtotime("+30 days"));
        $couponArr7 = $usageLimit;
        $couponArr8 = $usageLimit;
        $couponArr9 = 1;
        $couponArr10 = $userInfoArray['id'];
        
		
	    $couponsql = "INSERT INTO coupons (package_id,primary_user_id,name,description,from_date,to_date,usage_limit,balance_limit,is_active,created_on,created_by,modified_on,modified_by)VALUES ('$couponArr1','$couponArr2','$couponArr3','$couponArr4','$couponArr5','$couponArr6','$couponArr7','$couponArr8','$couponArr9','$couponArr5','$couponArr10','$couponArr5','0')";
        
        if($this->Coupon->query($couponsql))
        {
            //Update coupon code in users table
            /*$this->loadModel("User");
            $this->User->id = $userInfoArray["id"];
            $this->User->saveField("coupon_code", $couponArr["Coupon"]["name"], false);*/
            
            //Send email to admin
            $this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "Registration Detail", "admin_signup_inform", 
                    $userorderInfo23, array(), array(), StaticArrays::$successSignupContactEmails);

            //Send sms to user
            $messageForsms = "Greetings! Your One Touch  username is " . $userInfoArray["username"] . " and
                    Password is " . $userInfoArray["password"] . ".  Download the  Onetouch app and sign in.
                    For help call us on +1800-4191-911 . Stay Safe";
            $this->sendSingleSms($messageForsms, $this->_convertMobileNo($userInfoArray["mobile"]));
        
            //Send sms to user inform coupon information
            $couponsInfoMsg = "Greetings! Thank you for subscribing to One Touch Response.
                    Use Coupon code ". $couponArr["Coupon"]["name"] ." to register family members on the app.";
            $this->sendSingleSms($couponsInfoMsg, $this->_convertMobileNo($userInfoArray["mobile"]));
            
            $date1 = new DateTime($userInfoArray["start_date"]);
            $date2 = new DateTime($userInfoArray["end_date"]);
            $interval = $date1->diff($date2);
            
           /* $userInfoArray["expiry_date"] = $userInfoArray["end_date"];
            $userInfoArray["coupon_code"] = $couponArr["Coupon"]["name"];
            $userInfoArray["usage_limit"] = $usageLimit;
            $userInfoArray["service_days"] = $interval->days;*/
			
			$userorderInfo2 = array();
			$userorderInfo2["username"]   =  $userInfoArray["username"];
            $userorderInfo2["password"]   =  $userInfoArray["password"];
			$userorderInfo2["expiry_date"] = $couponArr["Coupon"]["to_date"];
            $userorderInfo2["coupon_code"] = $couponArr["Coupon"]["name"];
			$userorderInfo2["coupon_code_validity"] = $couponArr["Coupon"]["to_date"];
            $userorderInfo2["usage_limit"] = 1;
            $userorderInfo2["service_days"] = $interval->days;
			$userorderInfo2["expire"] = $userInfoArray["end_date"];

            //Send email to user
            $this->sendEmail($userInfoArray['email'], FROM_EMAIL, "Welcome to One Touch Response", 
                    "user_registration_detail", $userorderInfo2);
        }
    }
}