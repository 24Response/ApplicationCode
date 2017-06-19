<?php    

/**
 * WebServices Controller
 * 
 * @created    11/11/2015
 * @package    SOS
 * @copyright  Copyright (C) 2015
 * @license    Proprietary
 * @author     Anuj Maurya
 */

class WebServicesController extends AppController
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
					$this->_authenticate_member($data, CUSTOMER_GROUP_ID);
					break;
				
				case 'sign_in_new':
					$this->_authenticate_member_encrypt($data, CUSTOMER_GROUP_ID);
					break;
					

				case 'sign_in_responder':
					$this->_authenticate_member($data, RESPONDER_GROUP_ID);
					break;
					
				case 'sign_in_fr_user':
					$this->_authenticate_fr_member($data);
					break;	
				
				case 'customer_my_profile':
				    $this->_my_profile($data);
					break;
				
				case 'customer_change_password':
				    $this->_change_password($data);
					break;
				
				case 'customer_change_password_new':
				    $this->_change_encrypt_password($data);
					break;			
				
				case 'location_update':
				    $this->_location_update($data);
					break;
				
				case 'emergency_contact_update':
				    $this->_emergency_contact_update($data);
					break;
					
				case 'subscription_update':
				    $this->_subscription_update($data);
					break;	
							
				case 'notification_view':
				    $this->_notification_view($data);
					break;
				
				case 'notification_swipe':
				    $this->_notification_swipe($data);
					break;		
				
				case 'view_family_addon':
				    $this->_view_family_addon($data);
					break;
				
				case 'edit_family_addon':
				    $this->_edit_family_addon($data);
					break;	
						
				case 'update_family_addon':
				    $this->_update_family_addon($data);
					break;	
							
	            case 'add_family_addon':
				    $this->_add_family_addon($data);
					break;
				
				case 'trackme_info':
				    $this->_addtrackme_info($data);
					break;	
			    
				case 'trackme_encrypt_info':
				    $this->_addtrackme_encrypt_info($data);
					break;	
				
				case 'trackme_swipe_msg':
				    $this->_trackme_swipe_msg($data);
					break;
					
				case 'swipe_status_update':
				    $this->_swipe_status_update($data);
					break;
				
				case 'safe_location':
				    $this->_add_safe_location($data);
					break;
					
				case 'view_safe_location':
				    $this->_view_safe_location($data);
					break;
			    
				case 'edit_safe_location':
				    $this->_edit_safe_location($data);
					break;
				
				case 'delete_safe_location':
				    $this->_delete_safe_location($data);
					break;
							
				case 'safe_call':
					$modelName = "SafeCall";
					$this->_validate_call($data, $modelName);
					break;
				
				case 'safe_call_encrypt':
					$modelName = "SafeCall";
					$this->_validate_call_encrypt($data, $modelName);
					break;	

				case 'safe_call_tracking':
					$modelName = "SafeCall";
					$this->_tracking($data, $modelName);
					break;
				
				case 'safe_call_tracking_encrypt':
					$modelName = "SafeCall";
					$this->_tracking_encrypt($data, $modelName);
					break;	

				case 'safe_call_status_update':
					$modelName = "SafeCall";
					$this->_status_update($data, $modelName);
					break;
					
               case 'incident':
					$modelName = "Incident";
					$this->_validate_call($data, $modelName);
					break;
				
			   case 'incident_encrypt':
					$modelName = "Incident";
					$this->_validate_call_encrypt($data, $modelName);
					break;		

				case 'incident_tracking':
					$modelName = "Incident";
					$this->_tracking($data, $modelName);
					break;
				
				case 'incident_tracking_encrypt':
					$modelName = "Incident";
					$this->_tracking_encrypt($data, $modelName);
					break;	

				case 'incident_status_update':
					$modelName = "Incident";
					$this->_status_update($data, $modelName);
					break;

				case 'responder_tracking':
					$this->_responder_tracking($data);
					break;
					
                case 'fr_tracking':
					$this->_fr_tracking($data);
					break;
				
				case 'fr_helpme_accept':
					$this->_fr_help_accept($data);
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
				
				case 'responder_logout':
					$this->_responder_logout($data);
					break;
				
				case 'sign_up1':
					$this->_sign_up1($data);
					break;
                
				case 'assistme':
					$modelName = "assistmes";
					$this->_assist_me_data($data, $modelName);
					break;	
				
				case 'assistme_encrypt':
					$modelName = "assistmes";
					$this->_assist_me_encrypt_data($data, $modelName);
					break;		
				
				case 'customer_track':
					$this->_customer_track($data);
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
		else if ($data['Authentication']['name'] == "frlogout")
		{
			$this->_fr_logout_status($data);
		}
		else if ($data['Authentication']['name'] == "forgot_password")
		{
			$this->_validate_username($data, CUSTOMER_GROUP_ID);
		}
		else if ($data['Authentication']['name'] == "forgot_password_new")
		{
			$this->_validate_username_encrypt($data, CUSTOMER_GROUP_ID);
		}
		else if ($data['Authentication']['name'] == "fr_forgot_password")
		{
			$this->_validate_fr_username($data);
		}
		else if ($data['Authentication']['name'] == "sign_up2")
		{
			$this->_sign_up2($data);
		}
		else if ($data['Authentication']['name'] == "sign_up3")
		{
			$this->_sign_up3($data);
		}
		else if ($data['Authentication']['name'] == "super_cabz")
		{   
		    $modelName = "Incident";
		    $this->_super_cab_details($data, $modelName);
		}
		else if ($data['Authentication']['name'] == "buy_now")
		{
			$this->_buy_now($data);
		}
		else if ($data['Authentication']['name'] == "buy_now_test")
		{
			$this->_buy_now_test($data);
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
		else if ($data['Authentication']['name'] == "campaign_page")
		{
			$this->_campaign_page_api($data);
		}
		else if ($data['Authentication']['name'] == "campaign_deatils")
		{
			$this->_campaign_order_deatils($data);
		}
		else if ($data['Authentication']['name'] == "terms_conditions")
		{
			$this->_campaign_terms_conditions($data);
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
		else if ($data['Authentication']['name'] == "otr_forgot_password")
		{
			$this->_otr_validate_username($data);
		}
		else if ($data['Authentication']['name'] == "notification_update_info")
		{
			$this->_notification_user_update_info($data);
		}
		else if ($data['Authentication']['name'] == "fr_notification_update_info")
		{
			$this->_fr_notification_user_update_info($data);
		}
		else if ($data['Authentication']['name'] == "responder_info")
		{
			$this->_user_responder_info($data);
		}
        else if ($data['Authentication']['name'] == "verify_coupon")
		{
			$this->_couponVerify($data);
		}
		else if ($data['Authentication']['name'] == "disablebuynow")
		{
			$this->_disablebuynow($data);
		}
		else if ($data['Authentication']['name'] == "newdisablebuynow")
		{
			$this->_newdisablebuynow($data);
		}
		else if ($data['Authentication']['name'] == "disablebuynow_version_1.2.3")
		{
			$this->_disablebuynow_version($data);
		}
		else if ($data['Authentication']['name'] == "disablebuynow_version_1.2.4")
		{
			$this->_disablebuynow_version2($data);
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
		else if ($data['Authentication']['name'] == "forgot_password_responder")
		{
			$this->_validate_responder_username($data, RESPONDER_GROUP_ID);
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
	
	private function _family_addon_coupon($data)
	{
		$this->loadModel("Coupon");
        $this->Coupon->recursive = -1;
		$coupon = $data["Data"]["coupon_code"];
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
	
	
	private function _event_rating($data)
	{
		
		$this->loadModel("star_ratings");
        $this->star_ratings->recursive = -1;
		$star_rating = array();
		$star_rating['star_ratings']['event_id'] = $data['Data']['event_id'];
		$star_rating['star_ratings']['customer_id'] = $data['Data']['customer_id'];
		$star_rating['star_ratings']['rating'] = $data['Data']['rating'];
		$star_rating['star_ratings']['feedback'] = $data['Data']['feedback'];
		$this->star_ratings->create();
	    if($this->star_ratings->save($star_rating))
			{  
	    $this->_set_response($data, 1, "Thank you for your feedback", "", array());
		    }
		 
	}
	
	private function _change_password($data)
	{
		
		$user_id = $this->_validate_authentication($data);
		$oldpassword = $data["Data"]["old_password"];
        $newpassword = $data["Data"]["new_password"];
        $this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
        
        if($userDetails['User']['upmanual']!=$oldpassword)
        {
           $this->_invalid_request($data);
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
	
  /*Change password using encryption*/
	private function _change_encrypt_password($data)
	{
		
		$user_id = $this->_validate_encrption_authentication($data);
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
        $this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
        
        if($userDetails['User']['upmanual']!=$oldpassword)
        {
            $this->_set_response($data, 1, $oldpassword, $user_id, array()); 
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
		
	   $user_id = $this->_validate_authentication($data);
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
			$responseData['profile_pic'] = "http://apps1.onetouchresponse.com/profiles_pic/".$customerNameArr['User']['username'].'/'.$angles['UserProfile']['profile_pic'];
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
			$responseData['Dob'] = $customer_data['CustomerRecord']['dob'];
			$responseData['Alternate_No'] = $customer_data['CustomerRecord']['alternate_no'];
			$responseData['Date_Of_Subscription'] = $customer_data['CustomerRecord']['updated_start_date'];
			$responseData['Date_Of_Renewal'] = $customer_data['CustomerRecord']['updated_end_date'];
			$responseData['Country'] = $customer_data['CustomerRecord']['country'];
			$responseData['State'] = $customer_data['CustomerRecord']['state'];
			$responseData['City'] = $customer_data['CustomerRecord']['city'];
			$responseData['PinCode'] = $customer_data['CustomerRecord']['pincode'];
			$responseData['Address_1'] = $customer_data['CustomerRecord']['street_address1'];
			$responseData['Address_2'] = $customer_data['CustomerRecord']['street_address2'];
			$responseData['package_name'] = $customer_data['CustomerRecord']['package_name'];
			$responseData['blood_group'] = $customer_data['CustomerRecord']['blood_group'];
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
		$user_id = $this->_validate_authentication($data);
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
	    $this->UserProfile->saveField('country', $data["Data"]["country"]);
	    $this->UserProfile->saveField('state', $data["Data"]["state"]); 
		$this->UserProfile->saveField('city', $data["Data"]["city"]); 
		$this->UserProfile->saveField('pincode', $data["Data"]["pincode"]); 
		$this->UserProfile->saveField('street_address1', $data["Data"]["street_address1"]); 
		$this->UserProfile->saveField('street_address2', $data["Data"]["street_address2"]); 
	    $this->_set_response($data, 1, "Profile Updated successfully", "", array());
		 }
	}
	
	private function _emergency_contact_update($data)
	{
		$user_id = $this->_validate_authentication($data);
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
	    $this->UserProfile->saveField('emergency_name1', $data["Data"]["emergency_name1"]);
	    $this->UserProfile->saveField('emergency_phone1', $data["Data"]["emergency_phone1"]); 
		$this->UserProfile->saveField('emergency_email1', $data["Data"]["emergency_email1"]); 
		$this->UserProfile->saveField('emergency_relation1', $data["Data"]["emergency_relation1"]); 
		$this->UserProfile->saveField('emergency_name2', $data["Data"]["emergency_name2"]);
	    $this->UserProfile->saveField('emergency_phone2', $data["Data"]["emergency_phone2"]); 
		$this->UserProfile->saveField('emergency_email2', $data["Data"]["emergency_email2"]); 
		$this->UserProfile->saveField('emergency_relation2', $data["Data"]["emergency_relation2"]); 
		$this->_set_response($data, 1, "Emergency Contact Updated successfully", "", array());
		 }
	}
	
	private function _subscription_update($data)
	{
		$user_id = $this->_validate_authentication($data);
		$this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		$mobile_num = $data["Data"]["mobile"];
		$userDetails2 = $this->User->find("first", array("conditions" => array("mobile" => $mobile_num)));
		$user_id2 = $userDetails2['User']['id'];
		if(empty($userDetails['User']['id']))
		 {
		$this->_invalid_request($data);
		 }
		else if($user_id!=$user_id2)
		{
		$this->_set_response($data, 0, "", "Mobile Number already in use!", array());
		}
		else
		 {
		$this->loadModel("User");
		$this->User->recursive = -1;
	    $this->User->id = $user_id;
		$this->User->saveField('firstname', $data["Data"]["firstname"]);
		$this->User->saveField('lastname', $data["Data"]["lastname"]);
		$this->User->saveField('email', $data["Data"]["email"]);
		$this->User->saveField('mobile', $data["Data"]["mobile"]);
		$this->loadModel('UserProfile');
	    $this->UserProfile->recursive = -1;
		$profileDetails = $this->UserProfile->find("first", array("conditions" => array("user_id" => $user_id)));
	    $this->UserProfile->id = $profileDetails['UserProfile']['id'];
	    $this->UserProfile->saveField('alternate_no', $data["Data"]["alternate_no"]);
	    $this->UserProfile->saveField('gender', $data["Data"]["gender"]); 
		$this->UserProfile->saveField('dob', $data["Data"]["dob"]); 
		$this->UserProfile->saveField('blood_group', str_replace( " ","+",$data["Data"]["blood_group"])); 
		$this->_set_response($data, 1, "Subscription Info Updated successfully", "", array());
		 }
	}
	
	/*End Profile Update*/
	
	/*Notification View*/
	private function _notification_view($data)
	{
		$user_id = $this->_validate_authentication($data);
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
		$query = "select notification_id,message from notifications t1 inner join notification_details t2 on t1.id=t2.notification_id where user_id=$user_id AND notification_active=1";
		$reportData =$this->Notification->query($query);
		$responseData = array();
		foreach ($reportData as $value1)
		{
		$responseData[$i]['id'] = $value1['t2']['notification_id'];
	    $responseData[$i]['message'] = $value1['t1']['message'];
		$i++;
		}
		$notificationview1['notification_details']=array();
		$notificationview1['notification_details'] = $responseData;
		$this->_set_response($data, 1, "Success", "",$notificationview1);
		}
	 }
	 
	 private function _notification_swipe($data)
	{
		$user_id = $this->_validate_authentication($data);
		$this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		if(empty($userDetails['User']['id']))
		 {
		$this->_invalid_request($data);
		 }
		else
		 {
		$notification_id = $data["Data"]["notification_id"]; 
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
		$user_id = $this->_validate_authentication($data);
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
		$user_id = $this->_validate_authentication($data);
		$add_id = $data["Data"]["addon_id"];
		$this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		$userDetails1 = $this->User->find("first", array("conditions" => array("id" => $add_id,"modified_by"=>$user_id)));
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
	    $addonDetails = $this->CustomerRecord->find("first", array("conditions" => array("id" => $add_id)));
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
		$user_id = $this->_validate_authentication($data);
		$add_id = $data["Data"]["addon_id"];
		$this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		$userDetails1 = $this->User->find("first", array("conditions" => array("id" => $add_id,"modified_by"=>$user_id)));
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
	    $this->User->id = $add_id;
		$this->User->saveField('firstname', $data["Data"]["firstname"]);
		$this->User->saveField('lastname', $data["Data"]["lastname"]);
		$this->User->saveField('email', $data["Data"]["email"]);
		$this->User->saveField('mobile', $data["Data"]["mobile"]);
		$this->loadModel('UserProfile');
	    $this->UserProfile->recursive = -1;
		$profileDetails = $this->UserProfile->find("first", array("conditions" => array("user_id" => $add_id)));
	    $this->UserProfile->id = $profileDetails['UserProfile']['id'];
	    $this->UserProfile->saveField('gender', $data["Data"]["gender"]); 
		$this->UserProfile->saveField('dob', $data["Data"]["dob"]); 
		$this->UserProfile->saveField('blood_group', str_replace( " ","+",$data["Data"]["blood_group"])); 
		$this->_set_response($data, 1, "Add_On Updated successfully", "", array());
		}
	}
	
	private function _add_family_addon($data)
	{
		$user_id = $this->_validate_authentication($data);
	    $this->loadModel("User");
		$this->User->recursive = -1;
		$userDetails = $this->User->find("first", array("conditions" => array("id" => $user_id)));
		$userDetails1 = $this->User->find("first", array("conditions" => array("mobile" => $data['Data']['mobile'])));
		$userDetails2 = $this->User->find("first", array("conditions" => array("email" => $data['Data']['email'])));
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
		$profile['User']['username'] = $data['Data']['email'];
		$profile['User']['password'] = $data['Data']['password1'];
		$profile['User']['firstname'] = $data['Data']['firstname'];
		$profile['User']['lastname'] = $data['Data']['lastname'];
		$profile['User']['email'] = $data['Data']['email'];
		$profile['User']['mobile'] = $data['Data']['mobile'];
		$profile['User']['customer_code'] = 'ADD-ON USER';
		$profile['User']['terms_accepted'] = 1;
		$profile['User']['chk_no_objection'] = 1;
		$profile['User']['mobile_registration'] = '1';
		$profile['User']['fully_registered'] = '1';
		$profile['User']['unique_key'] = md5($data['Data']['email']);
		$profile['User']['upmanual'] = $data['Data']['password1'];
		$profile['User']['coupon_code'] = $data['Data']['coupon_code'];
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
			$couponArr = $this->Coupon->find("first", array("conditions" => array("name" => $data['Data']['coupon_code'])));
			$packageId = $couponArr["Coupon"]["package_id"];
			$parent_user = $couponArr["Coupon"]["primary_user_id"];
			$primaryUserID = $this->User->id;
			$this->loadModel("UserProfile");
            $this->UserProfile->recursive = -1;
			$user_profile["UserProfile"]["user_id"] = $this->User->id;
			$user_profile["UserProfile"]["gender"] = $data['Data']['gender'];
			$user_profile["UserProfile"]["blood_group"] = str_replace( " ","+",$data['Data']['blood_group']);
			$user_profile["UserProfile"]["street_address1"] = $data['Data']['address1'];
			$user_profile["UserProfile"]["street_address2"] = $data['Data']['address2'];
			$user_profile["UserProfile"]["dob"] = $data['Data']['dob'];
			$user_profile["UserProfile"]["city"] = $data['Data']['city'];
			$user_profile["UserProfile"]["state"] = $data['Data']['state'];
			$user_profile["UserProfile"]["country"] =$data['Data']['country'];
			$user_profile["UserProfile"]["pincode"] = $data['Data']['pincode'];
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
				$userorderInfo1["firstname"]  =  $data['Data']['firstname'];
				$userorderInfo1["username"]   =  $data['Data']['email'];
				$userorderInfo1["password"]   =  $data['Data']['password1'];
				$userorderInfo1["expiry_date"] = $finalendDate;
				$userorderInfo1["service_days"] = $interval->days;
				$userorderInfo1["expire"] =  $finalendDate;
				//Send email to user
               $this->sendEmail($data['Data']['email'], FROM_EMAIL, "Registration Detail", "family_add_on_detail", $userorderInfo1);
            
				//Send sms to user
				$messageForsms = "Greetings! Your One Touch  username is " . $$data['Data']['email'] . " and Password is " . $data['Data']['password1'] . ".  Download the  Onetouch  app http://bit.ly/1OCqpeS and sign in.For help call us on +1800-4191-911 . Stay Safe";
				$this->sendSingleSms($messageForsms, $this->_convertMobileNo($data['Data']['mobile']));
				$coupon = $data['Data']['coupon_code'];

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
	
	/*Customer location update*/
	private function _customer_track($data)
	{
	 $intUserId = $this->_validate_authentication($data);
	 if ($intUserId != 0)
		 {
	 //Get group id from the user id
	 $this->loadModel("User");
	 $this->User->recursive = -1;
	 $this->User->id = $intUserId;
     $this->User->saveField('latitude', $data["Data"]["latitude"]);
	 $this->User->saveField('longitude', $data["Data"]["longitude"]);
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
		 $user_Id = $data["Data"]["user_id"];
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
	 
	  private function _fr_notification_user_update_info($data)
	 {
		 $user_Id = $data["Data"]["user_id"];
		 $UD_Id = $data["Data"]["ud_id"];
		 $this->loadModel("first_responders");
		 $this->first_responders->recursive = -1;
		 $userDetail = $this->first_responders->find("first", array("conditions" => array("first_responders.id" => $user_Id)));
		 if(empty($userDetail['first_responders']['id']))
		 {
		 $this->_set_response($data, 0, "", "There is Something Wrong!!", array()); 
		 }
		 else
		 {
		 $querys="update first_responders set user_ud_id='$UD_Id',push_status=1  WHERE id='".$user_Id."'";
		 $reportData =$this->first_responders->query($querys);
		 $this->_set_response($data, 1, "Registration Id Updated Successfully!!", "", array());
		 }
	 }
	
	 private function _user_responder_info($data)
	{
		$user_Id = $data["Data"]["user_id"];
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
	
	
	private function _sign_up1($data)
	{
		$this->loadModel("User");
		$this->User->recursive = -1;
		
		//Remove incomlete registration with same imei
		$incompleteDetails = $this->User->find("all", array("conditions" => 
            array("unique_key" => $data["Data"]["imei"], "username" => $data["Authentication"]["username"],  
            "fully_registered" => 0, "group_id" => CUSTOMER_GROUP_ID)));
		
		if(!empty($incompleteDetails))
		{
			$this->User->id = $incompleteDetails[0]["User"]["id"];
			$this->User->delete();
		}
        
        $this->User->id = null;
		
		//Check username already exists in db or not
		(int) $userCount = $this->User->find("count", array("conditions" => 
            array("User.username" => $data["Authentication"]["username"], "User.is_active" => 1, 
            "User.is_deleted" => 0, "User.group_id" => CUSTOMER_GROUP_ID, "User.fully_registered" => 1)));
        
		if($userCount <= 0)
		{
			$userInsertArr["User"] = array();
			$userInsertArr["User"]["group_id"] = CUSTOMER_GROUP_ID;
			$userInsertArr["User"]["username"] = $data["Authentication"]["username"];
			$userInsertArr["User"]["password"] = $data["Authentication"]["password"];
            $userInsertArr["User"]["upmanual"] = $data["Authentication"]["password"];
			$userInsertArr["User"]["email"] = $this->_getRandomNumericString() . "@gmail.com";;
			$userInsertArr["User"]["mobile"] = '9'. $this->_getRandomNumericString(9);
			$userInsertArr["User"]["unique_key"] = $data["Data"]["imei"];
			$userInsertArr["User"]["mobile_registration"] = 1;
            $userInsertArr["User"]["registered_from"] = "IOS";
			if($this->User->save($userInsertArr))
			{
                if (!file_exists(PHOTO_UPLOAD_ABS_PATH . $data["Authentication"]["username"])) 
                {
                    mkdir(PHOTO_UPLOAD_ABS_PATH . $data["Authentication"]["username"], 0777, true);
                }
                
				$responseData = array("user_id" => $this->User->id);
				$this->_set_response($data, 1, "Inserted successfully", "", $responseData);
			}
            else
            {
                $this->_set_response($data, 2, "", "Insertion Failed", array());
            }
        }
		else
		{
			$this->_set_response($data, 2, "", "Username already exists", array());
		}
	}
	
	
/*Start campaign page Webservice*/

    private function _otrcouponVerify($data)
	{
		
		$this->loadModel("Coupon");
        $this->Coupon->recursive = -1;
		$coupon = $data["Data"]["coupon_code"];
        $couponArr = $this->Coupon->find("first", array("conditions" => array("name" => $coupon)));
		$packageId = $couponArr['Coupon']['package_id'];
	    $this->loadModel('Coupon');
        $result = $this->Coupon->verify_activation($coupon, $packageId);
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
		$coupon_code=  $data["Data"]["coupon_code"];
		$mobile=  $data["Data"]["mobile_number"];
		$alternate_number=  $data["Data"]["alternate_number"];
		$firstname=  $data["Data"]["firstname"];
		$lastname=  $data["Data"]["lastname"];
		$email=  $data["Data"]["email"];
		$password=  $data["Data"]["password"];
		$gender=  $data["Data"]["gender"];
		$dob=  $data["Data"]["dob"];
		$country=  $data["Data"]["country"];
		$state=  $data["Data"]["state"];
		$city=  $data["Data"]["city"];
		$pincode=  $data["Data"]["pincode"];
		$address=  $data["Data"]["address"];
		$emer_name=  $data["Data"]["emergency_name"];
		$emer_number=  $data["Data"]["emergency_number"];
		$emer_email=  $data["Data"]["emergency_email"];
		$relation=  $data["Data"]["relation"];
		$terms=  $data["Data"]["terms_conditions"];
		$nobj=  $data["Data"]["no_objections"];
		$this->loadModel("Coupon");
        $this->Coupon->recursive = -1;
		$coupon = $data["Data"]["coupon_code"];
        $couponArr = $this->Coupon->find("first", array("conditions" => array("name" => $coupon,"modified_by"=> 1)));
		$packageId = $couponArr['Coupon']['package_id'];
	    $this->loadModel('Coupon');
        $result = $this->Coupon->verify($coupon, $packageId);
        if ($result["validCoupon"] != 1)
        {
            $this->_set_response($data, 0, "", INVALID_COUPON, array());
        }
		else if(!preg_match("^[789]\d{9}$^", $mobile))
         {
               $this->_set_response($data, 0, "", "Please enter a valid mobile number.", array());
         }
		else if($mobile==$alternate_number)
		 {
		 $this->_set_response($data, 0, "", "Alternate & Mobile number are same.", array());
		 } 
		else if($mobile==$emer_number)
		 {
		 $this->_set_response($data, 0, "", "Please Use different emergency number.", array());
		 } 
		else if($email==$emer_email)
		 {
		 $this->_set_response($data, 0, "", "Please Use different emergency email id.", array());
		 }
		else
		{  
		$this->loadModel("User");
		$this->User->recursive = -1;
		
		//Remove incomlete registration with same email
		$incompleteDetails = $this->User->find("all", array("conditions" => 
            array("username" => $data["Data"]["email"],"is_active"=> 0, "fully_registered" => 0, "group_id" => CUSTOMER_GROUP_ID)));
		
		if(!empty($incompleteDetails))
		{
		    
			$this->User->id = $incompleteDetails[0]["User"]["id"];
			$this->User->delete();
			
		}
        
        $this->User->id = null;
		
		//Check email already exists in db or not
		(int) $userCountE = $this->User->find("count", array("conditions" => 
            array("User.email" => $data["Data"]["email"], "User.is_active" => 1, 
            "User.is_deleted" => 0, "User.group_id" => CUSTOMER_GROUP_ID, "User.fully_registered" => 1)));
		
		//Check mobile already exists in db or not
        (int) $userCountM = $this->User->find("count", array("conditions" => 
            array("User.mobile" => $data["Data"]["mobile_number"], "User.is_active" => 1, 
            "User.is_deleted" => 0, "User.group_id" => CUSTOMER_GROUP_ID, "User.fully_registered" => 1)));
		
		if($userCountE <= 0 && $userCountM <= 0)
		{
			$userInsertArr["User"] = array();
			$userInsertArr["User"]["group_id"] = CUSTOMER_GROUP_ID;
			$userInsertArr["User"]["coupon_code"] = $coupon_code;
			$userInsertArr["User"]["username"] = $data["Data"]["email"];
			$userInsertArr["User"]["password"] = $data["Data"]["password"];
            $userInsertArr["User"]["upmanual"] = $data["Data"]["password"];
			$userInsertArr["User"]["email"]   = $data["Data"]["email"];
			$userInsertArr["User"]["mobile"] =  $data["Data"]["mobile_number"];
			$userInsertArr["User"]["last_imei"] = $data["Data"]["imei"];
			$userInsertArr["User"]["unique_key"] = $data["Data"]["imei"];
			$userInsertArr["User"]["mobile_registration"] = 1;
			$userInsertArr["User"]["terms_accepted"] = 1;
			$userInsertArr["User"]["chk_no_objection"] = 1;
			$userInsertArr["User"]["registered_from"] = $data["Data"]["registered_from"];
			$userInsertArr["User"]["firstname"] = $data["Data"]["firstname"];
			$userInsertArr["User"]["lastname"] = $data["Data"]["lastname"];
			$userInsertArr["User"]["market_code"] = "OTR APP";
			
	    if($this->User->save($userInsertArr))
			{
			    $user_id = $this->User->id;
				$otp = $this->_generateRandomOTPcode(5);
				$messageForsms = "Your OTP code is ".$otp;
				$mobile = $data["Data"]["mobile_number"];
				$gender = $data["Data"]["gender"];
				$city =  $data["Data"]["city"];
				$state =  $data["Data"]["state"];
				$country =  $data["Data"]["country"];
				$pincode =  $data["Data"]["pincode"];
				$this->sendSingleSms($messageForsms, $this->_convertMobileNo($mobile));
				//Send email to admin for registration initialised
                $userArr = array("firstname" => $userInsertArr["User"]["firstname"], "lastname" => $userInsertArr["User"]["lastname"], "email"=>$userInsertArr["User"]["username"] ,"mobile" =>$userInsertArr["User"]["mobile"]);
		
		
               $this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "OTR Activation Initialise Details", 
                        "admin_profile_entered_signup", $userArr , array(), array(), StaticArrays::$initializedContactEmails);
		
               /*$this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "Campaign User Initialise Details", "admin_profile_entered_signup", $userArr);*/
		
		      //Send email to user
		
		      /* $userorderInfo1 = array("username" => $userInsertArr["User"]["username"], "password" => $userInsertArr["User"]["upmanual"], "otp_key" => $otp, "unique_key" =>$userInsertArr["User"]["unique_key"]);
		
              $this->sendEmail($userInsertArr["User"]["username"], FROM_EMAIL, "Activation User Detail", "user_campaign_detail", $userorderInfo1); */
			  $responseData = array("user_id" => $user_id,"package_id"=>$packageId,"OTP_Code" => $otp,"gender" => $gender,"city" => $city,"state" => $state,"country" => $country,"pincode" => $pincode,"address"=>$address,"alternate_number"=>$alternate_number,"dob"=>$dob,"emergency_name" => $emer_name,"emergency_number" => $emer_number,"emergency_email" => $emer_email,"relation"=>$relation);
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
		$this->loadModel("User");   
		$this->User->recursive = -1;
        $userProfileInsertArr["UserProfile"] = array();
		$userProfileInsertArr["UserProfile"]["user_id"] = $data["Data"]["user_id"];
        $userProfileInsertArr["UserProfile"]["gender"] = $data["Data"]["gender"];
		$userProfileInsertArr["UserProfile"]["dob"] = $data["Data"]["dob"];
		$userProfileInsertArr["UserProfile"]["alternate_no"] = $data["Data"]["alternate_number"];
		$userProfileInsertArr["UserProfile"]["street_address1"] = $data["Data"]["address"];
		$userProfileInsertArr["UserProfile"]["city"] = $data["Data"]["city"];
		$userProfileInsertArr["UserProfile"]["state"] =$data["Data"]["state"];
		$userProfileInsertArr["UserProfile"]["country"] = $data["Data"]["country"];
        $userProfileInsertArr["UserProfile"]["pincode"] = $data["Data"]["pincode"];
		$userProfileInsertArr["UserProfile"]["emergency_name1"] = $data["Data"]["emergency_name"];
		$userProfileInsertArr["UserProfile"]["emergency_phone1"] = $data["Data"]["emergency_number"];
		$userProfileInsertArr["UserProfile"]["emergency_email1"] = $data["Data"]["emergency_email"];
		$userProfileInsertArr["UserProfile"]["emergency_relation1"] = $data["Data"]["relation"];
		$userProfileInsertArr["UserProfile"]["created_by"] = $data["Data"]["user_id"];
		$userProfileInsertArr["UserProfile"]["modified_by"] = $data["Data"]["user_id"];
				
		if($this->User->UserProfile->save($userProfileInsertArr))
		{
			//Get package details
			$this->loadModel("Package");
			
			$pkg_id = $data["Data"]["package_id"];
			
			$packageDetail = $this->Package->find("first",array("conditions" => 
                array("Package.id" => $pkg_id, "Package.is_active" => 1)));
			
			$this->loadModel("User");   
		    $this->User->recursive = -1;
			$usersdetail = $this->User->find("first", array("conditions" => array("id" => $data["Data"]["user_id"])));
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
				$orderArr["Order"]["created_by"] = $data["Data"]["user_id"];
				$orderArr["Order"]["modified_by"] = $data["Data"]["user_id"];
				
				$this->loadModel("Order");
				$this->Order->create();
				
				if($this->Order->save($orderArr))
				{
					//disable last package if availble
					$q = "UPDATE order_details SET order_status = 0, is_active = 0 
                             WHERE user_id = ".$data["Data"]["user_id"].";";
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
					$orderDetailArr["OrderDetail"]["user_id"] = $data["Data"]["user_id"];
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
					$orderDetailArr["OrderDetail"]["created_by"] = $data["Data"]["user_id"];
					$orderDetailArr["OrderDetail"]["modified_by"] = $data["Data"]["user_id"];
					
					$this->Order->OrderDetail->create();
					if($this->Order->OrderDetail->save($orderDetailArr))
					{
                        $orderDetailId = $this->Order->OrderDetail->id;
						$this->User->id = $data["Data"]["user_id"];
						$userUpdate["User"] = array();
						$userUpdate["User"]["fully_registered"] = 1;
						$userUpdate["User"]["is_active"] = 1;
						$this->User->save($userUpdate);
                        
                        //update last package if available
                        $q = "UPDATE order_details SET order_status = 0,
                                is_active = 0 WHERE user_id = ".$data["Data"]["user_id"] . "
                                AND id <> ". $orderDetailId;
                        $this->Order->query($q);

                        $this->Order->id = $orderId;
                        $this->Order->saveField('order_status', 1);

                        $this->Order->OrderDetail->id = $orderDetailId;
                        $this->Order->OrderDetail->saveField('order_status', 1);
                        
                        $userInfoArr = $this->setUserInfoData($orderId);
					 //Send sms to emergency contacts
					 $InfoMsg = "Greetings from One Touch Response. You have been defined as an Emergency Contact by ".$fname . " ".$lname.". Stay Safe";
					 
					$this->sendMultipleSms($InfoMsg,array($this->_convertMobileNo($data["Data"]["emergency_number"])));
					if(!empty($data["Data"]["emergency_email"]))
					{				  
					//Send email to emergency contacts
                    $this->sendEmail($data["Data"]["emergency_email"], FROM_EMAIL, "OTER", "user_emergency_contact_inform",array("firstname" => $fname, "lastname" => $lname));
					}
					/*Admin signup information*/					
					$userorderInfo23 = array();
					$userorderInfo23["fullname"]   =  $userInfoArr["fullname"];
					$userorderInfo23["email"]   =  $userInfoArr["email"];
					$userorderInfo23["mobile"]   =  $userInfoArr["mobile"];
					$userorderInfo23["package_name"]   = $packageDetail["Package"]["name"];
					$userorderInfo23["order_id"]   =  $userInfoArr["order_id"];
					$userorderInfo23["transaction_id"]   =  $userInfoArr["transaction_id"];
					$userorderInfo23["gender"]   =  $data["Data"]["gender"];
				    $userorderInfo23["coupon_code"]   =  $userInfoArr["coupon_code"];
					$userorderInfo23["emergency_name1"]   =  $data["Data"]["emergency_name"];
					$userorderInfo23["emergency_phone1"]   =  $data["Data"]["emergency_number"];
					$userorderInfo23["emergency_email1"]   =  $data["Data"]["emergency_email"];
					$userorderInfo23["emergency_relation1"]   = $data["Data"]["relation"];
					
					/*End admin signup information*/
					
						/*update customer group*/
						if($pkg_id == 36)
						{
						$this->loadModel("User");
						$this->User->recursive = -1;
						$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $data["Data"]["user_id"])));
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
						$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $data["Data"]["user_id"])));
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
						$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $data["Data"]["user_id"])));
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
						$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $data["Data"]["user_id"])));
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
						$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $data["Data"]["user_id"])));
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
						$userDetail = $this->User->find("first", array("conditions" => array("User.id" => $data["Data"]["user_id"])));
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
	
	private function _otr_validate_username($data)
	{
		
		if (isset($data["Authentication"]["username"]) && !empty($data["Authentication"]["username"]))
		{
			$username = $data["Authentication"]["username"];
		}
		else
		{
			$this->_invalid_request($data);
		}
        $this->loadModel('User');
        $this->User->recursive = -1;
        $userDetail = $this->User->find('first', array('conditions' => array(
                        'User.username' => $data["Authentication"]["username"])));
	    $username = $userDetail['User']['username'];	
		$email = $userDetail['User']['email'];
		$username_id = $userDetail['User']['id'];	
		$upmanual = $userDetail['User']['upmanual'];		
		if ($username_id != null)
		{
			$intUserId = $username_id;

			//Sets response data in case of inactive user
			if (!$this->_IsUserActive($intUserId, CUSTOMER_GROUP_ID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
			else 
			{
			        $password = "";
                    if(!empty($upmanual))
                    {
                        $password = $upmanual;
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
					$userRecordArray = array();
					$userRecordArray["username"] = $username;
					$userRecordArray["password"] = $password;

					//Sending email to user for account credetials
					$this->sendEmail($email, FROM_EMAIL, "Account Credentials", "user_forgot_password", $userRecordArray);
					
                    //Sending sms to user for account credetials
                    $messageForsms = "Your One Touch ID and Password is Username:" . $username . " Password:" . $password;
					$this->sendSingleSms($messageForsms, $this->_convertMobileNo($userDetail['User']['mobile']));
					
                    $this->_set_response($data, 1, PASSWORD_SENT, "", array());
				}
	     }
		else
		{
			$this->_invalid_request($data);
		}
	}

	private function _campaign_page_api($data)
	{
	   $this->_set_response($data, 2, "", "Your app version no longer supported please update new version", array());
		/*$this->loadModel("User");
		$this->User->recursive = -1;*/
		
		//Remove incomlete registration with same imei


		/*$incompleteDetails = $this->User->find("all", array("conditions" => 
            array("unique_key" => $data["Data"]["imei"], "username" => $data["Data"]["email"],  
            "fully_registered" => 0, "group_id" => CUSTOMER_GROUP_ID)));
		
		if(!empty($incompleteDetails))
		{
		    
			$this->User->id = $incompleteDetails[0]["User"]["id"];
			$this->User->delete();
			
		}
        
        $this->User->id = null;*/
		
		
		//Check email already exists in db or not
		/*(int) $userCountE = $this->User->find("count", array("conditions" => 
            array("User.email" => $data["Data"]["email"], "User.is_active" => 1, 
            "User.is_deleted" => 0, "User.group_id" => CUSTOMER_GROUP_ID, "User.fully_registered" => 1)));*/
		
		//Check mobile already exists in db or not
       /* (int) $userCountM = $this->User->find("count", array("conditions" => 
            array("User.mobile" => $data["Data"]["mobile"], "User.is_active" => 1, 
            "User.is_deleted" => 0, "User.group_id" => CUSTOMER_GROUP_ID, "User.fully_registered" => 1)));*/
		
		/*if($userCountE <= 0 && $userCountM <= 0)
		{
			$userInsertArr["User"] = array();
			$userInsertArr["User"]["group_id"] = CUSTOMER_GROUP_ID;
			$userInsertArr["User"]["username"] = $data["Data"]["email"];
			$userInsertArr["User"]["password"] = $data["Data"]["password"];
            $userInsertArr["User"]["upmanual"] = $data["Data"]["password"];
			$userInsertArr["User"]["email"]    = $data["Data"]["email"];
			$userInsertArr["User"]["mobile"] =  $data["Data"]["mobile"];
			$userInsertArr["User"]["unique_key"] = $data["Data"]["imei"];
			$userInsertArr["User"]["last_imei"] = $data["Data"]["imei"];
			$userInsertArr["User"]["mobile_registration"] = 1;
            $userInsertArr["User"]["registered_from"] =$data["Data"]["registered_from"];
			$userInsertArr["User"]["firstname"] = $data["Data"]["firstname"];
			$userInsertArr["User"]["lastname"] = $data["Data"]["lastname"];
			$userInsertArr["User"]["market_code"] = "OTR APP";
			
	    if($this->User->save($userInsertArr))
			{
			    $user_id = $this->User->id;
				$otp = $this->_generateRandomOTPcode(5);
				$messageForsms = "Your OTP code is ".$otp;
				$mobile = $data["Data"]["mobile"];
				$gender = $data["Data"]["gender"];
				$city =  $data["Data"]["city"];
				$state =  $data["Data"]["state"];
				$country =  $data["Data"]["country"];
				$pincode =  $data["Data"]["pincode"];
				$this->sendSingleSms($messageForsms, $this->_convertMobileNo($mobile));*/
				//Send email to admin for registration initialised
              /*  $userArr = array("firstname" => $userInsertArr["User"]["firstname"], "lastname" => $userInsertArr["User"]["lastname"], "email"=>$userInsertArr["User"]["username"] ,"mobile" =>$userInsertArr["User"]["mobile"]);
		
		
               $this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "OTR App User Initialise Details", 
                        "admin_profile_entered_signup", $userArr , array(), array(), StaticArrays::$initializedContactEmails);*/
		
               /*$this->sendEmail(ADMIN_EMAIL, FROM_EMAIL, "Campaign User Initialise Details", "admin_profile_entered_signup", $userArr);*/
		
		      //Send email to user
		
		      /* $userorderInfo1 = array("username" => $userInsertArr["User"]["username"], "password" => $userInsertArr["User"]["upmanual"], "otp_key" => $otp, "unique_key" =>$userInsertArr["User"]["unique_key"]);
		
              $this->sendEmail($userInsertArr["User"]["username"], FROM_EMAIL, "Campaign User Detail", "user_campaign_detail", $userorderInfo1); 
			  $responseData = array("user_id" => $user_id,"OTP_Code" => $otp,"gender" => $gender,"city" => $city,"state" => $state,"country" => $country,"pincode" => $pincode);
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
		}*/

		
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
	
	private function _campaign_order_deatils($data)
	{
		$this->loadModel("User");   
		$this->User->recursive = -1;
        $userProfileInsertArr["UserProfile"] = array();
		$userProfileInsertArr["UserProfile"]["user_id"] = $data["Data"]["user_id"];
        $userProfileInsertArr["UserProfile"]["gender"] = $data["Data"]["gender"];
		$userProfileInsertArr["UserProfile"]["city"] = $data["Data"]["city"];
		$userProfileInsertArr["UserProfile"]["state"] =$data["Data"]["state"];
		$userProfileInsertArr["UserProfile"]["country"] = $data["Data"]["country"];
        $userProfileInsertArr["UserProfile"]["pincode"] = $data["Data"]["pincode"];
		$userProfileInsertArr["UserProfile"]["created_by"] = $data["Data"]["user_id"];
		$userProfileInsertArr["UserProfile"]["modified_by"] = $data["Data"]["user_id"];
				
		if($this->User->UserProfile->save($userProfileInsertArr))
		{
			//Get package details
			$this->loadModel("Package");
			
			$pkg_id = '8';
			
			$packageDetail = $this->Package->find("first",array("conditions" => 
                array("Package.id" => $pkg_id, "Package.is_active" => 1)));
			
			if(!empty($packageDetail))
			{
				$orderArr["Order"] = array();
				$orderArr["Order"]["package_amount"] = $packageDetail["Package"]["amount"];
				$orderArr["Order"]["tax_amount"] = 0;
				$orderArr["Order"]["total_amount"] = $packageDetail["Package"]["amount"];
				$orderArr["Order"]["payable_amount"] = $packageDetail["Package"]["amount"];
				$orderArr["Order"]["payment_mode"] = 3;
				$orderArr["Order"]["order_status"] = 0;
				$orderArr["Order"]["created_by"] = $data["Data"]["user_id"];
				$orderArr["Order"]["modified_by"] = $data["Data"]["user_id"];
				
				$this->loadModel("Order");
				$this->Order->create();
				
				if($this->Order->save($orderArr))
				{
					//disable last package if availble
					$q = "UPDATE order_details SET order_status = 0, is_active = 0 
                             WHERE user_id = ".$data["Data"]["user_id"].";";
					$this->Order->query($q);
					
					$orderId = $this->Order->id;
					$date = date("Y-m-d");
					$strendDate = strtotime($date . ' +' . $packageDetail["Package"]["duration_in_months"] . ' months');
					$convendDate = date("d-m-Y", $strendDate);
					$minendDate = strtotime($convendDate . ' -1 days');
					$endDate = date("d-m-Y", $minendDate);

                    $orderDetailArr["OrderDetail"] = array();
					$orderDetailArr["OrderDetail"]["user_id"] = $data["Data"]["user_id"];
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
					$orderDetailArr["OrderDetail"]["order_status"] = 0;
					$orderDetailArr["OrderDetail"]["is_active"] = 1;
					$orderDetailArr["OrderDetail"]["created_by"] = $data["Data"]["user_id"];
					$orderDetailArr["OrderDetail"]["modified_by"] = $data["Data"]["user_id"];
					
					$this->Order->OrderDetail->create();
					if($this->Order->OrderDetail->save($orderDetailArr))
					{
                        $orderDetailId = $this->Order->OrderDetail->id;
						$this->User->id = $data["Data"]["user_id"];
						$userUpdate["User"] = array();
						$userUpdate["User"]["fully_registered"] = 1;
						$userUpdate["User"]["is_active"] = 1;
						$this->User->save($userUpdate);
                        
                        //update last package if available
                        $q = "UPDATE order_details SET order_status = 0,
                                is_active = 0 WHERE user_id = ".$data["Data"]["user_id"] . "
                                AND id <> ". $orderDetailId;
                        $this->Order->query($q);

                        $this->Order->id = $orderId;
                        $this->Order->saveField('order_status', 1);

                        $this->Order->OrderDetail->id = $orderDetailId;
                        $this->Order->OrderDetail->saveField('order_status', 1);
                        
                        $userInfoArr = $this->setUserInfoData($orderId);
                        $this->_generateFamilyCoupons($userInfoArr);
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

	
	private function _campaign_terms_conditions($data)
	{
	
       $terms_conditions = "<div id='mainheading'><span id='textstyle3'> User Agreement</span></div>
		<br>
		<p><b>User Agreement last updated: 25th April, 2016</b></p>
		<p><b>User Agreement Effective Date: 3rd December, 2015</b></p>
		<p>ASAS Tech Solutions Private Limited ('Company'), having its registered office at 2 Shanti Farms, Chandanhola, Mehrauli, New Delhi&ndash;74, is a technology based assisted solution provider for many of day to day life's emergency situations. The technological platform (i.e. the OTR Interface) of the Company enables the subscribers to avail first response assistance, emergency response and certain other specified assistance services (i.e. the OTR Services). The OTR Interface also enables the subscribers/users to avail the services of various other service providers and other services provided by the Company, on separate payment basis (i.e., Additional Services). </p>
		<p>
		This User Agreement governs the access or use by you of the OTR Interface and through it access OTR Services and Additional Services. PLEASE READ THESE TERMS AND CONDTIONS CAREFULLY AND ACCEPT THEM BEFORE ACCESSING OR USING THE OTR INTERFACE.</p>
		
		<p><b>PART I &ndash; GENERAL</b></p>
		
		<p><b>1. Definitions</b></p>
		
		<p>1.1.	The following words and expressions shall, unless the context otherwise requires, have the meanings set out below:</p>
		
		<p>1.1.1.	'<b>Registration Details</b>' means your details as provided to the Company for registering you, as the User of the OTR  Interface  for accessing OTR Services and Additional Services, as applicable, including any addenda and supplements thereto, and shall also include details of the applicable Subscription Fees, Subscription Term and any other details required by the Company, , under this User Agreement;</p>
		
		<p>1.1.2.	 '<b>OTR Interface</b>' shall mean Website and/or the Application and/or through the phone number to the 'Control and Command Center' of the Company, as may be applicable;</p>
		<p>
		1.1.3.	'<a href='https://www.onetouchresponse.com/pincode' ><b>Serviceable Location</b></a>' shall mean the specified areas where the Company would under ordinary circumstances be able to provide OTR Services and/or Additional Services through the OTR Interface; and
		</p>
		<p>
		1.1.4.	'<b>you</b>' or '<b>your</b>' or '<b>User</b>' refer to the person visiting, accessing, browsing through and/or using the OTR Interface and/or availing the OTR Services and/or the Additional Services at any point in time.
		</p>
		<p>
		1.2.	Other Capitalized terms used herein, shall have the meaning assigned to them in the respective Clauses of the User Agreement.
		</p>
		
		<p><b>2. Contractual Relationship</b></p>
		<p>
		2.1.	The Company is a technology based assisted solution provider for many of day to day life's emergency situations. 'Emergency', by itself often means inordinate circumstances and conditions. The Company is committed to providing solutions and assisted services on a best efforts basis, specified in terms of this Agreement within the <a href='https://www.onetouchresponse.com/pincode' >Serviceable Locations</a>. </p>
		<p>
		2.2.	Access to and usage of the OTR Interface and/or the OTR Services provided thereunder, constitutes your acceptance of and agreement with the terms and conditions specified herein, as amended from time to time, and SHALL OPERATE AS A BINDING AGREEMENT BETWEEN YOU AND THE COMPANY IN RESPECT OF OTR INTERFACE, OTR SERVICES AND/OR ADDITIONAL SERVICES ('<b>User Agreement</b>'). Accordingly, PLEASE ENSURE THAT YOU READ AND UNDERSTAND THIS USER AGREEMENT BEFORE YOU USE ANY OF THE OTR SERVICES AND/OR THE ADDITIONAL SERVICES. If you do not accept or understand any of the terms and conditions contained in the User Agreement, then please don't use the OTR Interface and/or avail any of the OTR Services or the Additional Services. 
		</p>
		<p>
		2.3.	The Company reserves the right, at all times, in its sole discretion, to improve, alter, change, modify, or otherwise amend the User Agreement (and the scope, nature and kind of OTR Services/Additional Services offered) and any other documents incorporated herein by reference, for any reason whatsoever, including for compliance with applicable laws or business purposes, without any requirement of obtaining your prior consent. The Company will post/notify the amended User Agreement at the Website and the Application. The Company will have the right to update the existing User Agreement on the OTR Interface with such amendments and may notify you by way of pop&ndash;ups or emails or in any other manner, as may be considered suitable by the Company. It is, however, your responsibility to review the User Agreement for any such changes and you are encouraged to check the terms and conditions frequently. If you do not agree to abide by these or any amended User Agreement, please do not use or access the OTR Interface. Continuation of provision of OTR Services and/or Additional Services to you shall be subject to your accepting the revised/updated User Agreement, and should you wish to discontinue access/usage of the OTR Services, you may write to the Company at touch@onetouchresponse.com and request termination of the Subscription Term and/or seek refund of the Subscription Fees paid by you as per the refund policy of the Company.</p>
		<p>
		2.4.	You will provide authentic, true and complete information in all instances where any information is requested of you. The Company reserves the right to confirm and validate the information and other details provided by you at any point of time.
		</p>
		<p>
		2.5.	If at any time, the information provided by you is found to be false or inaccurate (wholly or partly), without prejudice to any other rights and remedies available to the Company, the Company would be entitled to: (i) exemption from provision of OTR Services and/or Additional Services to you; and/or (ii) terminate this User Agreement with immediate effect; and/or debar you from using OTR Services; and/or (iii) restrict or deny your access to the OTR Interface (or any part thereof) at any point in time at its sole discretion.
		</p>
		<p><b>3. Eligibility and Reliance on Information Provided</b></p>
		<p>
		3.1.	Subject to clause 3.2 below, only persons who are competent to contract under the applicable Indian laws can use or access the OTR Interface and/or avail the OTR Services and/or Additional Services.
		</p>
		<p>
		3.2.	A minor (i.e. a person below the age of 18 years) can only access or use the OTR Interface and/or avail the OTR Services and/or Additional Services only when the OTR Services have been subscribed to, on behalf of the minor, by the parent or legal guardian of such minor by accepting the terms of the User Agreement.
		</p>
		<p>
		3.3.	You acknowledge that the Company relies completely on the information provided by you (or by a third party, in case such information is being provided by a third party's application/software/device, other than the OTR Interface) and the Company shall not be held liable if you or anyone who uses the OTR Interface and/or the OTR Services, is not eligible to use the same or if the Company is unable to render the OTR Services (or provide access to Additional Services) due provision of incorrect or incomplete information or non&ndash;provision of information by the User (or the third party, in case information is being provided through a third party's application/software/device, i.e., other than directly by the User on the OTR Interface).</p>
		
		<p><b>PART &ndash;II &ndash; OTR SERVICES</b></p>
		<p><b>4. OTR Services</b></p>
		<p>
		4.1.	The Company shall make available, certain first response assistance, emergency response and other assistance services within the <a href='https://www.onetouchresponse.com/pincode' >Serviceable Locations</a>, as specifically described at the Part A of the Schedule to this User Agreement, in accordance with the subscription plans availed by you (or on your behalf by a third party) ('<b>OTR</b> <b>Services</b>'). 
		</p>
		<p>
		4.2.	Subject to your successful registration on the OTR Interface, the OTR Services would be offered and provided to you pursuant to your request for the same through the OTR Interface, as per the subscription plans applicable to you. The OTR Services would be available to you only during the period subscribed by you as specified in the Registration Details, as may be renewed/extended mutually ('<b>Subscription Term</b>'). </p>
		
		<p>
		4.3.	Subscription of OTR Services does not entitle you to avail Additional Services, unless such Additional Services are separately paid for. </p>
		<p>
		4.4.	The personnel/service providers used by the Company as the first response team providing the OTR Services, so far as practicable, would be trained for provision of first response, basic assistance and coordination/facilitation services. Appropriate police verification of all such personnel prior to their deployment as the first response team providing the OTR Services would be undertaken by the Company or by the legal entities providing such personnel. To further protect the interest of its Users, the Company shall make best endeavours to inform such personnel/service providers that they will be held personally liable for any impulsive behaviour/outburst on their part, arising out of any altercations during personal interactions, and that the Company shall investigate all such reported incidents promptly, and take suitable measures/actions as deemed necessary in specific cases directly or with the assistance of the service providers, providing such personnel.</p>
		
		<p><b>5. Fees for OTR Services</b></p>
		<p>
		5.1.	You will pay all fees payable for the OTR Services specified at Part A of the Schedule to this User Agreement ('<b>Subscription Fees</b>') in the manner specified in the Registration Details. If the OTR Services are availed through another application provider or any device/other interface, whether on a monthly or on pay per use basis, the Subscription Fee shall mean such fee as you may pay directly to the Company or through the other providers, as the case may be. It is hereby clarified, the Subscription Fees if revised by the Company (in accordance with Clause 2.3 hereof), the revised Subscription Fee would be applicable to you after the expiry/upon renewal of the Subscription Term by you.</p>
		
		<p>
		5.2.	Except as otherwise specified herein or in the Registration Details, (i) fees are based upon subscription of OTR Services (irrespective of their actual usage during the Subscription Term, except in cases where a pay per use arrangement has been accepted by the Company); and (ii) payment obligations are non&ndash;cancellable and fees paid are non&ndash;refundable, except in accordance with the refund policy of the Company.</p>
		<p>
		5.3.	In case subscription to the OTR Interface (and associated services) is sponsored by your employer or any other third party, your employer or such third party shall be ordinarily responsible for payment of the Subscription Fees. </p>
		
		<p><b>6. Activation of False Alarm/Misuse of OTR Services</b></p>
		<p>
		6.1.	You hereby undertake to avoid any action that might cause the activation of a distress button or request for OTR Services in situations not qualifying as an emergency situation and at times not required by you ('<b>False Alarms</b>'). </p>
		<p>
		6.2.	You hereby undertake to ensure that False Alarms are kept to a minimum. Should you make more than 2 such False Alarms in a six (6) months' period, without prejudice to any other rights and remedies available to the Company, the Company would be entitled to: (i) terminate this User Agreement with immediate effect; and/or debar you from using OTR Services; and/or (ii) restrict or deny your access to the OTR Interface (or any part thereof) at any point in time at its sole discretion.</p>
		
		<p><b>PART &ndash; III &ndash; ADDITIONAL SERVICES</b></p>
		
		<p><b>7. Additional Services</b></p>
		
		<p>
		7.1.	You may avail services offered by certain specified third party service providers and/or seek assistance from state agencies providing emergency services/law and order (including, police, fire brigade etc.) ('<b>State Agencies</b>') which the Company or its representatives may connect you through the OTR Interface or otherwise and/or any additional services provided by the Company ('<b>Additional Services</b>'), at your sole discretion but ONLY subject to payment in lieu thereof. </p>
		<p>
		7.2.	Certain arrangements, where under you use OTR Services, through other app providers and/or device manufacturers, may not permit you to avail any or all Additional Services or certain OTR Services, and in all such cases, the provisions of this agreement related to such Additional Services not provided (and such OTR Services not being provided), are not applicable. </p>
		<p>
		7.3.	The Company or its representatives may at its discretion, also assist you in procuring Additional Services of third party services ('<b>Third Party Service Providers</b>'). You hereby acknowledge that the Company does not, and cannot exercise control over the effectiveness, quality, or safety vis a vis the Additional Services that is made use of by you from these Third Party Service Providers.</p>
		<p>
		It is hereby agreed, any provision or facilitation for Additional Services from Third Party Service Providers shall be provided at the sole discretion of the Company or its representatives and the Company and/or its representatives reserve their right to refuse provision or facilitation of any such services.</p>
		<p>
		7.4.	You acknowledge, agree and understand that the Company only facilitates you with a platform enabling you contact the Third Party Service Providers/State Agencies and the Company itself has no role in the execution or provision of such Additional Services.</p>
		<p>
		7.5.	The Company does not endorse any such Additional Services and in no event shall the Company be responsible or liable for any products or services provided as part of the Additional Services. The Company shall not be responsible for any breach of service or service deficiency (alongwith defects in goods provided in relation thereto) in the Additional Services provided by the Third Party Service Providers. The Company cannot assure nor guarantee the ability or intent of the Third Party Service Providers or any State Agencies to fulfil their obligations towards you, in relation to such Additional Services.</p>
		<p>
		7.6.	Without prejudice to the generality of the above, the Company will not be liable for:</p>
		<p>
		7.6.1.	any inconvenience suffered by you due to any failure or delay, on the part of the Third Party Service Providers or any State Agencies to provide the Additional Services or deficiency or inadequacy therein;</p>
				<p>
		7.6.2.	any misconduct or inappropriate behaviour by the Third Party Service Providers or any State Agencies or their personnel;</p>
		<p>
		7.6.3.	any misrepresentation and negligence on the part of such Third Party Service Providers or any State Agencies;</p>
		<p>
		7.6.4.	cancellation  or rescheduling or any variance in the fees charged;</p>
		
		<p><b>8. Charges and Expenses for Additional Services</b></p>
		<p>
		8.1.	Charges for Additional Services. The charges for the Additional Services (and goods provided in relation thereto) shall be paid by you directly to the Third Party Service Providers, your primary application provider, device provider/manufacturers, as and wherever applicable. The Company shall have not be responsible for collection/realization of the fees of the Third Party Service Providers from you. The terms and conditions for online payments shall be such as approved by the law in force for the time being and it is your and the Third Party Service Providers' responsibility to effect the transaction in a proper legal manner.</p>
		<p>
		8.2.	Expenses towards Additional Services incurred by the Company. Any expenses/costs incurred by the Company (and/or its representatives) in respect of any Additional Services rendered by the Company (and/or its representatives) shall be promptly reimbursed by you to the Company (including costs of any goods delivered to you). You hereby agree and acknowledge that the charges/expenses covered under this Clauses 8.1 and 8.2 are not included and covered under your Subscription Fees and the Subscription Fees is limited to use of OTR Interface and availing of OTR Services only. Without prejudice to any other rights and remedies available with the Company, the Company may adjust expenses due from you against the Subscription Fees towards the unexpired portion of the Subscription Term and reduce it accordingly, at its sole discretion, if the dues are not paid to the Company promptly. Conveyance expense, at a fixed rate shall ordinarily be payable by you with respect to travel of the Company's representatives for provision of Additional Services to you, and shall be informed to you at the relevant time, in accordance with the pre&ndash;determined rates of the Company.</p>
		<p>
		8.3.	Even if subscription to the OTR Interface (and associated services) is sponsored by your employer or any other third party on your behalf, you shall solely be responsible for payment of any charges/costs/expenses for any Additional Services availed in accordance with Clause 8.1 and Clause 8.2 above.</p>
		
		<p><b>PART &ndash; IV &ndash; EXCLUSIONS</b></p>

		<p><b>9. Exclusions</b></p>
		<p>
		9.1.	After you have requested for the provision of Additional Services, the Company or its representatives would be free to take such steps and call/connect you to the appropriate State Agencies and/or Third Party Service Providers as they deem necessary at their sole discretion. You may also deny availing such Additional Services or choose to avail Additional Services a Third Party Service Provider. 
		</p>
		<p>
		9.2.	In case you are unable to provide your consent for availing the Additional Services, you hereby authorise the Company, or its representatives, to take all necessary steps that they deem appropriate in their sole judgement (including to access your personal belongings, information and property, without being provided your consent for the same) and discharge the Company and its representatives of any and all liabilities, claims or damages that may arise in relations to such actions undertaken by the Company, or its representatives in the foregoing situations.</p>
		<p>
		9.3.	You hereby undertake not to use the OTR Interface or OTR Services and/or Additional Services for unauthorised/unlawful purpose or otherwise request/instruct the Company or its representatives to commit an act which is unauthorised/unlawful or in violation of rights of any third parties. You agree to indemnify the Company for any kind of damage incurred due to such unlawful use in terms of Clause 14 hereof.</p>
		<p>
		9.4.	The Company's representatives have a right to refuse to participate or provide assistance in matters that, in the view of the Company's representatives may amount to participation in or perpetuation of any illegal demand or activity, or which would threaten the personal safety or the life of the Company's representatives. In such cases, the Company's representatives would on a best effort basis call/contact the government agencies, i.e., fire department, police, etc. for assistance being the appropriate authorities under the law to address such situations.</p>
		<p>
		9.5.	To the extent permitted under the applicable law, the Company or its representatives would not act as witnesses to any legal proceedings/investigations. You agree to indemnify the Company for any cost/expense incurred by the Company or its representatives (including attorney fees) due to the any participation in the aforementioned legal proceedings/investigations in terms of Clause 14 hereof.</p>
		
		<p><b>10. Network Access and Devices</b></p>
		<p>
		10.1.	You are responsible for obtaining the data and/or telecom network access necessary to use the OTR Interface. Your mobile network's call, data and messaging rates and fees may apply, if you access or use the OTR Interface from a wireless&ndash;enabled device. You are responsible for acquiring and updating compatible hardware or devices and having necessary data and cellular coverage, necessary to access and use the OTR Interface and any updates thereto. </p>
		<p>
		10.2.	The Company does not guarantee that the OTR Interface, or any portion thereof, will function on any particular hardware or devices. In addition, the OTR Interface, OTR Services and/or Additional Services may be subject to malfunctions and delays inherent in the use of the telecom, Internet, mobile network electronic communications and GPS Systems. Further, you agree and acknowledge that the usability/accessibility of the OTR Interface depends on many factors that are not under the control of the Company, including weather conditions, telecommunications network coverage (dependent on factors such as lifts, tunnels, basements, etc. transmission interferences may also occur due to the electronic waves generated by electric systems), call dropping, failure to connect the call for any reason whatsoever buildings, Force Majeure Events and intentional or unintentional communication interferences.</p>
		<p>
		10.3.	You shall ensure that, once the OTR Interface is used to avail any OTR Services or Additional Services, the subject telecommunication device/mobile phone shall remain in close proximity of the person, so as to ensure response and assistance.</p>
		<p>
		10.4.	You hereby agree and acknowledge that in cases, where OTR Services/Additional Services is made available to you by a third party's mobile application/device ('Third Party Application or Device') (and not directly through the OTR Application on your mobile device), then your access/usage to the OTR Services/Additional Services are subject to the Company receiving, from such Third Party Application or Device, the appropriate alert, signal, call and/or constant data feed, upon the relevant OTR services button(s) being pressed by you on the Third Party Application or Device. The Company shall not be held liable if it is unable to render/provide access to the OTR Services/Additional Services due to failure of such Third Party Application or Device to provide the required information and send appropriate signals/data/alerts/calls/data feed to the Company.</p>
		<p>
		10.5.	The usability/accessibility of the OTR Services and/or Additional Services through any application/website/software or Third Party Application or Device (other than through the OTR Interface), would be subject to the telecom, Internet, electronic communications, GPS Systems and other functionalities/compatibility of such other application/website/software (other than through the OTR Interface). The Company shall not be liable towards any functionality/dysfunction/malfunction of such other application/website/software (other than through the OTR Interface) and any consequent delays/non provision/deficiency in OTR Services/Additional Services.
		</p>
		
		
		<p><b>PART &ndash; IV &ndash; INTELLECTUAL PROPERTY</b></p>
		<p><b>
		11.	Intellectual Property</b></p>
		<p>
		11.1.	The website i.e. <a href='https://www.onetouchresponse.com/'>www.onetouchresponse.com </a>('<b>Website</b>') is owned by the Company and the mobile application i.e., 'One Touch Response' ('<b>Application</b>') is the proprietary software of the Company.</p>
		<p>
		11.2.	This User Agreement, the OTR Interface and its contents, information, text, graphics, images, logos, button icons, software code, design, and the collection, arrangement and assembly of content on the OTR Interface, are the intellectual property of Company and are protected under applicable laws vis&ndash;a&ndash;vis copyright, trademark, design etc. All titles, ownership and intellectual property rights in the OTR Interface and its content shall remain with the Company, its affiliates, agents, authorized representatives or licensor's, as the case may be. 
		</p>
		<p>
		11.3.	Subject to your compliance with this User Agreement, the Company grants you a limited, non&ndash;exclusive, non&ndash;sub licensable, revocable, non&ndash;transferrable license to: (i) access and use the OTR Interface on your personal device solely in connection with your availing the OTR Services and/or Additional Services; and (ii) access and use any content, information and related materials that may be made available on the OTR Interface, in each case solely for your personal, non&ndash;commercial use. Any rights not expressly granted herein are reserved by Company and the Company's licensors. Neither this User Agreement nor your use of the OTR Interface convey or grant to you any rights: (i) in or related to the OTR Interface except for the limited license granted above; or (ii) to use or reference in any manner the Company's names, logos, product and service names, trademarks or services marks or those of the Company's licensors.</p>
			   <p>
		11.4.	You shall not: (i) remove any copyright, trademark or other proprietary content from the OTR Interface; (ii) reproduce, modify, prepare derivative works based upon, distribute, license, lease, sell, resell, transfer, publicly display, publicly perform, transmit, stream, broadcast or otherwise exploit the OTR Interface and/or the OTR Services and/or the Additional Services except as expressly permitted by the Company; (iii) decompile, reverse engineer or disassemble the OTR Interface; (iv) cause or launch any programs or scripts for the purpose of scraping, indexing, surveying, or otherwise data mining any portion of the OTR Interface or unduly burdening or hindering the operation and/or functionality of any aspect of the OTR Interface; or (v) attempt to gain unauthorized access to or impair any aspect of the OTR Interface or its related systems or networks.
		</p>
			 <p><b>12. Content</b><b> Provided by You</b></p>
		<p>
		12.1.	The Company may, in its sole discretion, permit you from time to time to submit, upload, publish or otherwise make available to the Company through the OTR Interface, content (text, images, audio, videos etc.) and information, including commentary and feedback related to the OTR Interface and/or the OTR Services and/or the Additional Services ('<b>Uploaded Content</b>'). Any Uploaded Content provided by you remains your property, however, you hereby grant the Company a worldwide, perpetual, irrevocable, transferrable, royalty&ndash;free license, with the right to sublicense, to use, copy, modify, create derivative works of, distribute, publicly display and otherwise exploit in any manner such Uploaded Content in any manner whatsoever (including in connection with the Company's business or otherwise), without further notice to or consent from you, and without the requirement of payment to you or any other person or entity.</p>
		<p>
		12.2.	You represent and warrant that: (i) you either are the sole and exclusive owner of all Uploaded Content or you have all rights, licenses, consents and releases necessary to grant the Company the license to the Uploaded Content as set forth above; and (ii) neither the Uploaded Content nor your submission, uploading, publishing or otherwise making available of such Uploaded Content nor the Company's use of the Uploaded Content as permitted herein will infringe, misappropriate or violate a third party's intellectual property or proprietary rights, or rights of publicity or privacy, or result in the violation of any applicable law or regulation.</p>
		<p>
		12.3.	You agree to not provide Uploaded Content or host, display, upload, modify, publish, transmit, update or share any information on the OTR Interface that: </p>
		<p>
		12.3.1.	belongs to another person and to which you do not have any right to;</p>
		<p>
		12.3.2.	is grossly harmful, harassing, blasphemous; defamatory, obscene, pornographic, paedophilic, libellous, invasive of another's privacy, hateful, or racially, ethnically objectionable, disparaging, relating or encouraging money laundering or gambling, or otherwise unlawful in any manner whatever;
		</p>
		<p>
		12.3.3.	harm minors in any way;
		</p>
		<p>
		12.3.4.	infringes any patent, trademark, copyright or other proprietary rights;
		</p>
		<p>
		12.3.5.	violates any law for the time being in force;
		</p>
		<p>
		12.3.6.	deceives or misleads the addressee about the origin of such messages or communicates any information which is grossly offensive or menacing in nature; 
		</p>
		<p>
		12.3.7.	impersonate another person;
		</p>
		<p>
		12.3.8.	contains software viruses or any other computer code, files or programs designed to interrupt, destroy or limit the functionality of any computer resource;
		</p>
		<p>
		12.3.9.	threatens the unity, integrity, defence, security or sovereignty of India, friendly relations with foreign states, or public order or causes incitement to the commission of any cognisable offence or prevents investigation of any offence or is insulting any other nation.
		</p>
		<p>
		or is otherwise offensive, as determined by the Company in its sole discretion, whether or not such material may be protected by law. The Company may, but shall not be obligated to, review, monitor, or remove Uploaded Content, at its sole discretion and at any time and for any reason, without notice to you. In case of you are in non&ndash;compliance of the applicable laws (including the Information Technology Act, 2000 and Indian Penal Code, 1860) and/or this User Agreement, the Company would be entitled to terminate this User Agreement with immediate effect.</p>
		
		<p><b>PART &ndash; V &ndash; DISCLAIMER; LIMITATION OF LIABILITY; INDEMNITY</b></p>
		
		<p><b>13.	Disclaimer and Limitation of Liability</b></p>
		<p>
		
		13.1.	THE OTR INTERFACE AND/OR THE OTR SERVICES OR ANY OTHER SERVICES INCLUDING FACILITATION FOR ANY SERVICES PROVIDED BY THE COMPANY ARE PROVIDED ON AN 'AS IS' AND 'AS AVAILABLE' BASIS. THE COMPANY DISCLAIMS ALL REPRESENTATIONS AND WARRANTIES, EXPRESS, IMPLIED, OR STATUTORY, NOT EXPRESSLY SET OUT IN THESE TERMS, INCLUDING THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NON&ndash;INFRINGEMENT. IN ADDITION, THE COMPANY MAKES NO REPRESENTATION, WARRANTY, OR GUARANTEE REGARDING THE RELIABILITY, TIMELINESS, QUALITY, SUITABILITY, OR AVAILABILITY OF THE OTR INTERFACE OR OTR SERVICES OR ANY ADDITIONAL SERVICES OR GOODS REQUESTED THROUGH THE USE OF THE OTR INTERFACE, OR THAT THE OTR INTERFACE AND/OR THE OTR SERVICES WILL BE UNINTERRUPTED OR ERROR&ndash;FREE. THE OTR INTERFACE AND OTR SERVICES ARE PROVIDED BY THE COMPANY ONLY ON A BEST EFFORTS AND DILIGENCE BASIS, AND THE COMPANY DISCLAIMS, AND YOU AGREE THAT THE COMPANY SHALL NOT HAVE ANY OBLIGATION AND RESPONSIBILITY UNDER THIS AGREEMENT EXCEPT TO PROVIDE THE OTR INTERFACE OR THE OTR SERVICES ON A BEST EFFORT BASIS. YOU AGREE THAT THE ENTIRE RISK ARISING OUT OF YOUR USE OF THE OTR INTERFACE AND/OR THE OTR SERVICES, AND/OR ANY ADDITIONAL SERVICE OR GOOD REQUESTED IN CONNECTION THEREWITH, REMAINS SOLELY WITH YOU OR WITH THE THIRD PARTY SERVICE PROVIDERS, TO THE MAXIMUM EXTENT PERMITTED UNDER APPLICABLE LAW. IT IS HEREBY CLARIFIED THE COMPANY DOES NOT SELL ANY GOODS OR MERCHANDISE ON THE OTR INTERFACE AND DOES NOT ENGAGE IN RETAIL OR WHOLESALE TRADING OF ANY PRODUCTS.</p>
		<p>
		13.2.	THE COMPANY DOES NOT ENDORSE, WARRANT, GUARANTEE, OR ASSUME RESPONSIBILITY FOR ANY PRODUCT OR SERVICE ADVERTISED OR OFFERED FROM A THIRD PARTY THROUGH THE OTR INTERFACE OR ANY HYPERLINKED SITE OR FEATURED IN ANY BANNER OR OTHER ADVERTISEMENT. THE COMPANY WILL NOT BE A PARTY TO OR IN ANY WAY BE RESPONSIBLE FOR MONITORING ANY TRANSACTION BETWEEN YOU AND ANY THIRD PARTY, INCLUDING THIRD PARTY SERVICE PROVIDERS. AS WITH THE USE OF ANY PRODUCT OR SERVICE, AND THE PUBLISHING OR POSTING OF ANY MATERIAL THROUGH ANY MEDIUM OR IN ANY ENVIRONMENT, YOU SHOULD USE YOUR BEST JUDGMENT AND EXERCISE CAUTION WHERE APPROPRIATE. YOU ACKNOWLEDGE THAT ADDITIONAL SERVICES REQUESTED THROUGH THE OTR INTERFACE MAY NOT BE PROFESSIONALLY LICENSED OR PERMITTED, AND YOU SHOULD AVAIL THE ADDITIONAL SERVICES IN YOUR SOLE JUDGEMENT.</p>
		<p>
		13.3.	YOU HEREBY UNCONDITIONALLY CONFIRM AND ACKNOWLEDGE THAT BY AVAILING ADDITIONAL SERVICES IN ACCORDANCE WITH THIS USER AGREEMENT YOU HAVE SURRENDERED AND UNILATERALLY RESIGN TO THE EXTENT PERMISSIBLE UNDER APPLICABLE LAWS, TO RAISE ANY CLAIMS OR INITIATE ANY LEGAL ACTIONS OR PROCEEDINGS ON ANY GROUNDS WHATSOEVER AGAINST THE COMPANY OR ITS REPRESENTATIVES WITH RESPECT TO, OR IN CONNECTION WITH, ARISING FROM ANY DEFECT OR DEFICIENCY IN ANY SERVICES OR PRODUCT PROVIDED BY THE THIRD PARTY SERVICES PROVIDERS OR OWING TO YOUR INTERACTIONS/TRANSACTIONS WITH THE THIRD PARTY SERVICE PROVIDERS. </p>
		<p>
		13.4.	THE COMPANY SHALL NOT BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, PUNITIVE, OR CONSEQUENTIAL DAMAGES, INCLUDING LOST PROFITS, LOST DATA, PERSONAL INJURY, OR PROPERTY, LIABILITY OR LOSSES ARISING OUT OF: (i) YOUR USE OF OR RELIANCE ON THE OTR INTERFACE AND/OR THE OTR SERVICES AND/OR THE ADDITIONAL SERVICES OR YOUR INABILITY TO ACCESS OR USE THE OTR INTERFACE AND/OR THE OTR SERVICES AND/OR THE ADDITIONAL SERVICES; OR (ii) ANY TRANSACTION OR RELATIONSHIP BETWEEN YOU AND ANY THIRD PARTY SERVICE PROVIDER OR ANY STATE AGENCIES. THE COMPANY SHALL NOT BE LIABLE FOR DELAY OR FAILURE IN PERFORMANCE RESULTING FROM CAUSES BEYOND THE COMPANY'S CONTROL. IN NO EVENT SHALL COMPANY'S TOTAL LIABILITY TO YOU IN CONNECTION WITH THE USER AGREEMENT FOR ALL DAMAGES, LOSSES AND CAUSES OF ACTION EXCEED THE SUBSRICPTION FEE PAID BY YOU TO THE COMPANY DURING THE RELEVANT SUBSRIPTION TERM.</p>
		
		<p><b>14. Indemnity</b></p>
		<p>
		You agree to indemnify and hold the Company and its officers, directors, employees, affiliates and agents (collectively referred to as '<b>Indemnified Parties</b>') harmless from any and all claims, demands, losses, liabilities, and expenses (including attorneys' fees), incurred or likely to be incurred by the Indemnified Parties, arising out of or in connection with: (i) your use of the OTR Interface and/or the OTR Services and/or the Additional Services or services or goods obtained through your such use; (ii) your breach or violation of any of this User Agreement and the applicable laws; (iii) The Company's use of your Uploaded Content; (iv) your violation of the rights of any third party, including Third Party Service Providers or (vi) acts done by the Company or its representatives under your instructions or request.</p>
		
		<p><b>PART &ndash; VI &ndash; TERMINATION</b></p>
		
		<p><b>15. Termination of the Subscription Term</b></p>
		<p>
		15.1.	Without prejudice to Clauses 2.5, 6.2 and 12.3 hereof, the Company may at its discretion terminate the Subscription Term (and/or this User Agreement):</p>
		<p>
		15.1.1.	either, by giving you a prior termination notice of 15 (fifteen) days if you commit a breach of this User Agreement and if you fail to cure such breach within 15 (fifteen) days of receiving such termination notice intimating the breach and the intention to terminate;</p>
		<p>
		15.1.2.	or, by immediate notice in case of any non&ndash;payment by you (or by a third party on your behalf) of any amounts due and payable to the Company and/or any Third Party Service Provider;</p>
		<p>
		15.1.3.	or, at will, by giving a prior termination notice of 30 (thirty) days, without assigning any reason whatsoever.</p>
		<p>
		15.1.4.	or, by giving a prior termination notice of 5 (five) days upon occurrence of a Force Majeure Event which has not been cured within 60 (sixty) days, in accordance with Clause 16.1.2 below.</p>
		<p>
		15.2.	You may request termination of the Subscription Term (and/or this User Agreement) at any time with 30 (thirty) days prior notice to the Company, stating the reasons for the intended termination. Such notice shall be deemed to be a Dispute notice in accordance with Clause 16.5.1 of this User Agreement. It is hereby clarified that in case of termination without a valid reason, you shall not be entitled to receive any refund of the Subscription Fees. </p>
		<p>
		15.3.	Without prejudice to the above ground for termination, in case your subscription has been procured through a corporate agreement with an entity paying the Subscription Fees on your behalf ('Corporate Tie&ndash;Up') or through another application provider or device provider/manufacturer, and such Corporate Tie&ndash;Up or arrangement with another application provider or device provider/manufacturer ends and/or the entity stops making payment of the Subscription Fees on your behalf, you would be free to avail the subscription for OTR Interface and associated services from the Company on your own, upon payment as an individual subscriber. In such circumstances, if you do not individually subscribe to the OTR Interface and associated services, the access to OTR Interface and associated services shall be discontinued. In case where your subscription of through Corporate Tie Up, the Company reserves a right to intimate to your employer/entity, and seek its assistance in resolution of any issues/complaints between you and the Company with regard to the OTR Interface or associated services.   </p>
		<p>
		15.4.	In the event of termination or expiry of the Subscription Term (and/or this User Agreement), the provisions of Clause 11 (Intellectual Property); Clause 12 (User Provided Content); Clause 13 (Disclaimer and Limitation of Liability), Clause 14 (Indemnity); Clause 15 (Termination of Subscription Term) and any other provision which is expressly stated to or by implication or by its nature is meant to survive expiry or termination of the Subscription Term (and/or this User Agreement), shall continue to remain valid and binding and shall survive such expiry of termination of the Subscription Term (and/or this User Agreement). Expiry or termination of the Subscription Term (and/or this User Agreement) shall not prejudice or affect any right or remedies or liabilities accrued prior to the date of such termination or expiry.</p>
		
		<p><b>PART &ndash; VII &ndash; MISCELLANEOUS</b></p>
		
		<p><b>16. Miscellaneous</b></p>
		
		<p>16.1.	Force Majeure</p>
		<p>
		16.1.1.	The Company shall not be in default or breach of this User Agreement and its obligations hereunder with respect to the OTR Interface and/or OTR Services (and its obligation to render the OTR Services shall stand suspended to such extent, without any liability), to the extent that performance of its obligations or attempts to cure any breach are delayed or prevented by reason of acts of God, acts of a public enemy, acts of any governmental or quasi&ndash;governmental agency or any of their political subdivisions, fire, flood, epidemics, explosion, power or irregularities/dysfunction/malfunction in telecommunications, internet, electronic, GPS and systems, quarantine restrictions; strikes or other labour unrest, earthquakes, civil commotion or revolutions, war, terrorist attack, freight embargoes, unusually severe weather conditions; any other cause beyond the control of the Company or any other reasonably unforeseen events or any other events as specified in Clause 10.2 above ('<b>Force Majeure Event</b>'). </p>
		<p>
		16.1.2.	Upon occurrence of a Force Majeure Event, the Company shall use its best efforts to avoid or remove these causes and/or circumstances of the Force Majeure Event; provided that if such causes continue for longer than sixty (60) days, the Company may, by written notice to you, terminate your Subscription Term by issuing a termination notice of five (5) days in accordance with Clause 15.1.4 above. </p>
		<p><b>
		16.2.	Assignment</b></p>
		<p>
		You shall not assign this User Agreement or any of its rights or obligations hereunder without the prior written consent of the Company.  It being clarified, the Company has the right to enter into agreements with third parties, with respect to the OTR Services being provided to you through the OTR Interface, without any requirement of obtaining your prior consent.</p>
		<p><b>
		16.3.	Loyalty and Promotional Programmes</b></p>
		<p>
		The Company reserves the right to introduce from time to time such loyalty and promotional programs as it deems appropriate. The terms and conditions of such loyalty and promotional programs would be specified separately at the relevant time.
		</p>
		<p><b>
		16.4.	Group Accidental Insurance Cover</b>
		</p>
		<p>
		In the event you are eligible, as per the terms of your subscription plans, to be covered under the Group Accidental Insurance Policy availed by the Company for its customers,  then please note that as per the terms and conditions provided by the insurance provider, such Group Accidental Insurance Policy  shall not cover Users with below conditions: 
		</p>
		<p>
		(i)	Any existing disability/deformity (Physical or mental impairment/infirmity or any condition hampering vision/hearing or mobility).
		</p>
		<p>
		(ii)	Diagnosed or under treatment for any terminal illness or any illness/disease restricting activities eg; Epilepsy/Seizure disorder.
		</p>
		<p>
		(iii)	Users involved with any hazardous activity, significant manual labor, operating heavy machinery, handling hazardous material, working at heights / underground / construction sites, oil rigging, high voltage, high temperature, working in aircrafts or sea&ndash;going vessels or adventure sports or armed forces.
		</p>
		<p>
		Please carefully read and take note of all the terms and conditions applicable to such Group Accidental Insurance Policy. Policy details given are indicative, not exhaustive. The customer needs to refer to the policy document sent to them for entire details of coverage & terms.
		</p>
		<p><b>
		16.5.	Dispute Resolution</b>
		</p>
		<p>
		16.5.1.	In the event of any dispute claims, controversies and conflicts, arising out of or relating to or in connection with this User Agreement or the breach, termination or invalidity thereof ('Dispute'), you and the authorized representatives of the Company shall attempt to resolve such Dispute with 30 (thirty) days of either of the party giving notice in writing to the other party of such Dispute.  
		</p>
		<p>
		16.5.2.	Upon the you and the Company being unable to resolve the Dispute as aforesaid, the Dispute shall be submitted to final and binding arbitration at the request of either of the disputing parties upon written notice to that effect to the other, and the Dispute shall be finally settled exclusively by arbitration as per the Indian Arbitration and Conciliation Act, 1996.  </p>
		<p>
		16.5.3.	The arbitration shall be conducted by a single arbitrator appointed by the Company. The venue of the arbitration shall be New Delhi, India. The language of the arbitration proceedings shall be English. The decision of the arbitrator shall be final and binding as to the Dispute. You and the Company agree that the fee and charges of the arbitrator shall be fixed in accordance with the Delhi International Arbitration Centre (DAC) (Administrative Cost Arbitrators' Fees) Rules, as are applicable at the time of commencement of the arbitration proceedings.</p>
		<p>
		16.5.4.	Subject to the Clauses 16.5.2 and 16.5.3 above, the courts in New Delhi, India shall have exclusive jurisdiction to deal with the Disputes between the parties including the arbitration proceedings and the award passed thereto.</p>
		<p>
		16.5.5.	The Company shall have the sole discretion to suspend provision of/access to the OTR Interface any or all OTR Services/Additional Services to you during the pendency of any Dispute with you.</p>
		<p><b>
		16.6.	Notices
		</b></p>
		<p>
		Notices shall be in writing and shall be deemed to have been received upon expiry of five days from the date of dispatch or transmission or on receipt of acknowledged delivery receipt (whichever is earlier) to the address set forth follows:</p>
		<p>
		16.6.1.	If to you to your name, address and email id indicated in  the Registration Details; and
		</p>
		<p>
		16.6.2.	If to the Company:	
		</p>
		<p>
		ASAS Tech Solutions Pvt. Ltd.</p>
		<p>
		3rd Floor, Building No. 113, Sector&ndash;44</p>
		<p>
		Gurgaon&ndash;122003, Haryana, India</p>
		
		<p>Attention: Head Customer Service</p>
		
		<p>Email: touch@onetouchresponse.com </p>
		
		<p><b>
		16.7.	Entire Agreement</b></p>
		<p>
		This User Agreement as amended from time to time, together with the Registration Details, , its Schedules, annexures and other documents specifically attached or referred to herein, constitutes the entire agreement between you and the Company with respect to the subject matter hereof, and supersedes all prior understandings, promises, representations, agreements and negotiations between the you and the Company, oral and written.
		</p>
		<p><b>
		16.8.	No Waiver</b></p>
		<p>
		Failure or delay by the Company to enforce any provision of this User Agreement against you shall not be deemed a waiver of future enforcement of that or any other provision.
		</p>
		<p><b>
		16.9.	Severability
		</b></p>
		<p>
		If any portion of this User Agreement is held to be illegal, invalid or unenforceable, such portion shall be deemed to be modified to the extent necessary to make such portion binding and enforceable, and such modified portion and all the remaining portions shall remain in full force and effect.
		</p>
		<p><b>
		16.10.	Privacy Policy
		</b></p>
		<p>
		The use and access to the OTR Interface and/or availing the OTR Services and/or Additional Services is subject to the Privacy Policy of the Company (available at: https://www.onetouchresponse.com/privacypolicy)
		</p>
		<p><b>
		16.11.	Grievance Policy
		</b></p>
		<p>
		In accordance with Information Technology Act, 2000 and the Information Technology (Intermediaries Guidelines) Rules, 2011, the contact details of the Grievance Officer who can be contacted for any complaints or concerns pertaining to the OTR Interface, including those pertaining to breach of the User Agreement or and other polices are published as under:
		</p>
		<p>
		Designation: 	Vice President Service Delivery
		</p>
		<p>
		Address:		ASAS Tech Solutions Pvt. Ltd.</p>
		<p>
		3rd Floor, Building No. 113, Sector&ndash;44</p>
		<p>
		Gurgaon&ndash;122003, Haryana, India</p>
		<p>
		Contact No:		+91.124.4606.900
		</p>
		<p>
		Email: 		grievance.officer@onetouchresponse.com 
		</p>
		<p><b>
		16.12.	Electronic Record
		</b></p>
		<p>
		This User Agreement is an electronic record, generated by a computer system and does not require any physical or digital signatures in terms of Section 4 of the Information Technology Act, 2000, for it to be recognised as a legally binding contract in terms of the Indian Contract Act, 1872.
		</p>
		<p>
		BY CHECKING AGAINST THE BOX 'I AGREE' YOU HEREBY ACKNOWLEDGE TO HAVE READ AND UNDERSTOOD AND UNCONDITIONALLY ACCEPT ALL THE TERMS AND CONDTIONS CONTAINED IN THE USER AGREEMENT. YOU AGREE TO ENTER INTO THIS USER AGREEMENT VOLUNTARILY, WITH FULL KNOWLEDGE OF ITS EFFECT.</p>
		
		
		<p><b>Part A of Schedule: OTR Services</b></p>
		
		<p>
		(i)	OTR Services as accessible on your mobile device (directly through the OTR Interface): 
		</p>
		<p><b>
		A.	HelpME/SOS Alert &ndash; On Demand Physical Response, 24x7</b></p>
		<p>
		The SOS Alert button gives you instant access to OTR Services. As soon as you press the SOS button, your mobile device will dial the pre&ndash;programmed number of our 24 X 7 Command & Control Center and an Incident Manager will be on call with you within a matter of seconds. He/She will gather all vital information on your situation and initiate an appropriate response. Simultaneously, our nearest Immediate Assistance Team on their Responder App receives an alert, and they will start moving towards your location, as can be tracked by our server, saving precious minutes in response time. Once they reach you, they will provide you assistance/protection and isolate you from the unsafe situation.</p>
		<p>
		One Touch Response will also provide coordination with external services such as Police, Fire Brigade & Ambulance, if the situation requires.
		</p>
		
		<p><b>
		B.	TrackME/Safe Call &ndash; On Demand Live Tracking, 24 X 7</b></p>
		<p>
		Our TrackME/Safe Call Service provides a reliable custodian, consistently keeping a watch over you, whenever required. We understand the need to have someone to watch over you, someone who knows your where about, in more than a few situations. Aligned with this, we provide constant monitoring service for instances, such as when you are on a long journey, travelling through a rough neighbourhood or commuting during work hours.
		</p>
		<p>
		The TrackME/Safe Call button on the OTR mobile app, gives you instant access to this service. As soon as you press the TrackME/Safe Call button, your mobile device will send us a request and an Incident Manager from our 24 X 7 Command & Control Center will be on call with you within a matter of seconds. He/She will gather all vital information on your situation and set up your tracking. He/She will then track you live on his screen and initiate notifications/tracking calls to your mobile number at a pre&ndash;agreed frequency. 
		</p>
		<p>
		If we are not able to contact you, our nearest Immediate Assistance Team will be alerted, and they will move towards your last tracked location, as can be tracked by our server, providing you safety and protection. 
		</p>
		<p>
		One Touch Response will also provide coordination with external services such as Police, Fire Brigade & Ambulance, if the situation requires.
		</p>
		<p><b>
		C.	AssistME Service &ndash; On Demand Assistance from State Agencies/Third Party Service Providers
		</b></p>
		<p>
		In case of any distress, just activate 'AssistME Button' on the OTR app, and get connected to our Command & Control Center, wherein our Incident Manager will gather all vital information and initiate an appropriate response. Based on the emergency situation, and your instructions, we call and coordinate with State Agencies and Third Party Service Providers, such that you can access their services (being the Additional Services specified in Part B of this Schedule below) and get a quick response and assistance.</p>
		
		<p><b>
		(ii)	OTR Services as accessible through a Third Party Application or Device (and not directly through the OTR Interface) 
		</b></p>
		<p>
		In case, you are accessing our HelpME or TrackME services through a third party application or device, we will provide the services as given above, subject to us getting the required data through the respective third party application or device, as accessible to you. </p>
		
		
		<p><b>Part B of Schedule: Additional Services
		</b></p>
		<p>
		Upon your activation of the 'AssistME Button' or 'SOS Button' on the OTR app, our Incident Manager at our Command & Control Centre can connect and coordinate with the Third Party Service Providers, to provide you with access to the following Additional Services. These Additional Services are provided on a best effort basis and at an additional charge payable directly to these service providers:</p>
		
		<table border='1' cellspacing='0' cellpadding='0' > 
		<tbody>
		<tr>
		<td ALIGN=CENTER><p><b>Medical Emergencies</b></p></td>
		<td ALIGN=CENTER><p><b>On Road Assistance</b></p></td>
		</tr>
		
		<tr>
		<td ALIGN=CENTER><p>&nbsp;Ambulance On Call&nbsp;</p></td>
		<td ALIGN=CENTER><p>&nbsp;Towing Services&nbsp;</p></td>
		</tr>
		
		<tr>
		<td ALIGN=CENTER><p>&nbsp;Medicine Delivery at Odd Hours&nbsp;</p></td>
		<td ALIGN=CENTER><p>&nbsp;Mechanic On call&nbsp;</p></td>
		</tr>
		
		<tr>
		
		<td ALIGN=CENTER><p>&nbsp;Mobile First Aid&nbsp;</p></td>
		<td ALIGN=CENTER><p>&nbsp;Tyre Puncture Repair&nbsp;</p></td>
		</tr>
		
		<tr>
		<td ALIGN=CENTER><p>&nbsp;</p></td>
		<td ALIGN=CENTER><p>&nbsp;Battery Jump Start&nbsp;</p></td>
		</tr>
		<tr>
		<td ALIGN=CENTER><p>&nbsp;</p></td>
		<td ALIGN=CENTER><p>&nbsp;Cab Arrangement&nbsp;</p></td>
		</tr>
		<tr>
		<td ALIGN=CENTER><p>&nbsp;</p></td>
		<td ALIGN=CENTER><p>&nbsp;Fuel Refill&nbsp;</p></td>
		</tr>
		</tbody>
		</table>
		<br>
		Download This Document<a href='https://www.onetouchresponse.com/Terms_and_Conditions.PDF' download='Terms and Conditions'>&nbsp;click here</a>&nbsp;";
	  $this->_set_response($data, 1, "Success", "", $terms_conditions);
	
	}
	
	/*End campaign page Webservice*/
	
	/*Resend otp*/
	private function _resend_otp($data)
	      {
		        $mob="/^[7-9][0-9]*$/"; 
	            if(!preg_match($mob, $data["Data"]["mobile_number"]))
				{
				$this->_set_response($data, 2, "", 'Invalid Mobile Number', '');
				}
				else if($data["Data"]["mobile_number"]=='')
				{
				$this->_set_response($data, 2, "", 'Invalid Mobile Number', '');
				}
				else
				{
				$otp_code = array();
				$otp = $this->_generateRandomOTPcode(5);
				$messageForsms = "Your OTP code is ".$otp;
				$mobile = $data["Data"]["mobile_number"];
				$this->sendSingleSms($messageForsms, $this->_convertMobileNo($mobile));
				$otp_code['otp_code']=$otp;
				$this->_set_response($data, 1, "Success", "", $otp_code);
				}
	      }
	/*End resend otp*/
	
	
	/*Start Supercab Webservice*/
    private function _super_cab_details($data, $modelName)
	{
		$this->loadModel("User");
		$this->User->recursive = -1;
		//Remove incomlete registration with same details
		$incompleteDetails = $this->User->find("all", array("conditions" => 
            array("mobile" => $data["Data"]["mobile"], "username" => $data["Data"]["username"],"customer_code" => $data["Data"]["trip_id"])));
		
		if(!empty($incompleteDetails))
		{
		    $incompleteDetails1 = $this->User->UserProfile->find("all", array("conditions" => array("user_id" => $incompleteDetails[0]["User"]["id"])));
		    if(!empty($incompleteDetails1))
		     {
		    $this->User->UserProfile->id = $incompleteDetails1[0]["UserProfile"]["id"];
			$this->User->UserProfile->delete();
			 }
			$this->User->id = $incompleteDetails[0]["User"]["id"];
			$this->User->delete();
		}
            $this->User->id = null;
		    $userInsertArr["User"] = array();
			$userInsertArr["User"]["group_id"] = CUSTOMER_GROUP_ID;
			$userInsertArr["User"]["username"] = $data["Data"]["username"];
			$userInsertArr["User"]["mobile"] = $data["Data"]["mobile"];
            $userInsertArr["User"]["customer_code"] =  $data["Data"]["trip_id"];
			$userInsertArr["User"]["password"] =  $this->_getRandomNumericString(9);
			$userInsertArr["User"]["upmanual"] =  $this->_getRandomNumericString(9);
			$userInsertArr["User"]["mobile_registration"] = 1;
            $userInsertArr["User"]["firstname"] = $data["Data"]["username"];
			$userInsertArr["User"]["market_code"] = "super_cab";
			$userInsertArr["User"]["latitude"] = $data["Data"]["latitude"];
			$userInsertArr["User"]["longitude"] = $data["Data"]["longitude"];
			$userInsertArr["User"]["is_active"] = 1;
			$userInsertArr["User"]["is_deleted"] = 0;
			$userInsertArr["User"]["fully_registered"] = 0;
	   if($this->User->save($userInsertArr))
			{
			    $user_id = $this->User->id;
			    $userProfileInsertArr["UserProfile"] = array();
				$userProfileInsertArr["UserProfile"]["user_id"] = $user_id;
				$userProfileInsertArr["UserProfile"]["city"] = "Delhi";
				$userProfileInsertArr["UserProfile"]["state"] = "Delhi";
				$userProfileInsertArr["UserProfile"]["country"] = "India";
				$userProfileInsertArr["UserProfile"]["gender"] = $data["Data"]["gender"];
				$this->User->UserProfile->save($userProfileInsertArr);
	    $userId = $this->_validate_supercab($userInsertArr);
		if ($userId != 0)
		{
			//Sets response data in case of inactive user
			if (!$this->_IsUserActive($userId, CUSTOMER_GROUP_ID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
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
				$insertArr[$modelName]["longitude"] = $data["Data"]["longitude"];
				$insertArr[$modelName]["latitude"] = $data["Data"]["latitude"];
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
                        $this->_send_request_to_responder($incidentID, $userId, $latitude, $longitude);
						
                        $insertArrTrack[$modelName . "Tracking"]["incident_id"] = $returnedId;
					}
					$insertArrTrack[$modelName . "Tracking"]["longitude"] = $data["Data"]["longitude"];
					$insertArrTrack[$modelName . "Tracking"]["latitude"] = $data["Data"]["latitude"];
					$insertArrTrack[$modelName . "Tracking"]["created_by"] = $userId;
					$insertArrTrack[$modelName . "Tracking"]["modified_by"] = $userId;

					$this->loadModel($modelName . "Tracking");
					$this->{$modelName . "Tracking"}->create();
					//Saves Incident/SafeCall Tracking Information
					$this->{$modelName . "Tracking"}->save($insertArrTrack);
					$this->_set_response($data, 1, "Success", "",$insertArrTrack);
				}
				else
				{
					$this->_set_response($data, 2, "", "There is something wrong!!");
				}
			}
		 }
		}
		else
				{
					$this->_set_response($data, 2, "", "There is something wrong!!");
				}
	}
	
	
	private function _validate_supercab($userInsertArr)
	{
		$intUserId = 0;
		$UserCheckParams = array();
		$UserCheckParams['User.username'] = $this->StringInputCleaner($userInsertArr['User']['username']);
		$UserCheckParams['User.password'] = AuthComponent::password($userInsertArr['User']['password']);
		$UserCheckParams['User.is_deleted'] = 0;

		$this->loadModel('User');
		$arrUser = $this->User->find("all", array("conditions" => $UserCheckParams, 'fields' => 'id'));

		//Check if user is exist in the database or not
		if (isset($arrUser) && !empty($arrUser))
		{
			$intUserId = $arrUser[0]['User']['id'];
		}
		return $intUserId;
	}
	
	/*End Supercab Webservice*/
	
	
	private function _sign_up2($data)
	{
		$this->loadModel("User");
		$this->User->recursive = -1;
		
		//Check email already exists in db or not
		(int) $userCountE = $this->User->find("count", array("conditions" => 
            array("User.email" => $data["Data"]["email"], "User.is_active" => 1, 
            "User.is_deleted" => 0, "User.group_id" => CUSTOMER_GROUP_ID, "User.fully_registered" => 1)));
		
		//Check mobile already exists in db or not
        (int) $userCountM = $this->User->find("count", array("conditions" => 
            array("User.mobile" => $data["Data"]["mobile"], "User.is_active" => 1, 
            "User.is_deleted" => 0, "User.group_id" => CUSTOMER_GROUP_ID, "User.fully_registered" => 1)));
		
		if($userCountE <= 0 && $userCountM <= 0)
		{
			$userInsertArr["User"] = array();
			$userInsertArr["User"]["firstname"] = $data["Data"]["firstname"];
			$userInsertArr["User"]["lastname"] = $data["Data"]["lastname"];
			$userInsertArr["User"]["email"] = $data["Data"]["email"];
			$userInsertArr["User"]["mobile"] = $data["Data"]["mobile"];
			
			$this->User->id = $data["Data"]["user_id"];
					
			if($this->User->save($userInsertArr))
			{
				$userProfileInsertArr["UserProfile"] = array();
				$userProfileInsertArr["UserProfile"]["user_id"] = $data["Data"]["user_id"];
				$userProfileInsertArr["UserProfile"]["dob"] = $data["Data"]["dob"];
				$userProfileInsertArr["UserProfile"]["age"] = $this->_getAge($data["Data"]["dob"]);
				$userProfileInsertArr["UserProfile"]["gender"] = $data["Data"]["gender"];
				$userProfileInsertArr["UserProfile"]["city"] = "Delhi";
				$userProfileInsertArr["UserProfile"]["state"] = "Delhi";
				$userProfileInsertArr["UserProfile"]["country"] = "India";
				
				if($this->User->UserProfile->save($userProfileInsertArr))
				{
					$responseData = array("user_id" => $data["Data"]["user_id"], "userprofile_id" => $this->User->UserProfile->id);
					$this->_set_response($data, 1, "Inserted successfully", "", $responseData);
				}
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
	
	private function _sign_up3($data)
	{
		$this->loadModel("User");   
		$this->User->recursive = -1;
        
        if(isset($data["Data"]["coupon_code"]) && !empty($data["Data"]["coupon_code"]))
        {
            $this->loadModel("Coupon");

            $this->Coupon->updateUsage($data["Data"]["coupon_code"]);
        }
		
		$userProfileInsertArr["UserProfile"] = array();
		$userProfileInsertArr["UserProfile"]["street_address1"] = $data["Data"]["address"];
        $userProfileInsertArr["UserProfile"]["street_address2"] = $data["Data"]["address2"];
		$userProfileInsertArr["UserProfile"]["city"] = $data["Data"]["city"];
		$userProfileInsertArr["UserProfile"]["state"] =$data["Data"]["state"];
		$userProfileInsertArr["UserProfile"]["country"] = $data["Data"]["country"];
        $userProfileInsertArr["UserProfile"]["pincode"] = $data["Data"]["pincode"];
		
		$userProfileInsertArr["UserProfile"]["emergency_name1"] = $data["Data"]["emergency_name1"];
		$userProfileInsertArr["UserProfile"]["emergency_phone1"] = $data["Data"]["emergency_phone1"];
		$userProfileInsertArr["UserProfile"]["emergency_email1"] = $data["Data"]["emergency_email1"];
		$userProfileInsertArr["UserProfile"]["emergency_relation1"] = $data["Data"]["emergency_relation1"];
        
        $userProfileInsertArr["UserProfile"]["emergency_name2"] = $data["Data"]["emergency_name2"];
		$userProfileInsertArr["UserProfile"]["emergency_phone2"] = $data["Data"]["emergency_phone2"];
		$userProfileInsertArr["UserProfile"]["emergency_email2"] = $data["Data"]["emergency_email2"];
		$userProfileInsertArr["UserProfile"]["emergency_relation2"] = $data["Data"]["emergency_relation2"];
        
        $userProfileInsertArr["UserProfile"]["emergency_name3"] = $data["Data"]["emergency_name3"];
		$userProfileInsertArr["UserProfile"]["emergency_phone3"] = $data["Data"]["emergency_phone3"];
		$userProfileInsertArr["UserProfile"]["emergency_email3"] = $data["Data"]["emergency_email3"];
		$userProfileInsertArr["UserProfile"]["emergency_relation3"] = $data["Data"]["emergency_relation3"];
		
		$userProfileInsertArr["UserProfile"]["created_by"] = $data["Data"]["user_id"];
		$userProfileInsertArr["UserProfile"]["modified_by"] = $data["Data"]["user_id"];
		
		$this->User->UserProfile->id = $data["Data"]["userprofile_id"];
		if($this->User->UserProfile->save($userProfileInsertArr))
		{
			//Get package details
			$this->loadModel("Package");
			
			$packageDetail = $this->Package->find("first",array("conditions" => 
                array("Package.id" => $data["Data"]["package_id"], "Package.is_active" => 1)));
			
			if(!empty($packageDetail))
			{
				$orderArr["Order"] = array();
				$orderArr["Order"]["package_amount"] = $packageDetail["Package"]["amount"];
				$orderArr["Order"]["tax_amount"] = 0;
				$orderArr["Order"]["total_amount"] = $packageDetail["Package"]["amount"];
				$orderArr["Order"]["payable_amount"] = $packageDetail["Package"]["amount"];
				$orderArr["Order"]["payment_mode"] = 3;
				$orderArr["Order"]["order_status"] = 0;
				$orderArr["Order"]["created_by"] = $data["Data"]["user_id"];
				$orderArr["Order"]["modified_by"] = $data["Data"]["user_id"];
				
				$this->loadModel("Order");
				$this->Order->create();
				
				if($this->Order->save($orderArr))
				{
					//disable last package if availble
					$q = "UPDATE order_details SET order_status = 0, is_active = 0 
                             WHERE user_id = ".$data["Data"]["user_id"].";";
					$this->Order->query($q);
					
					$orderId = $this->Order->id;
					$date = date("Y-m-d");
					$strendDate = strtotime($date . ' +' . $packageDetail["Package"]["duration_in_months"] . ' months');
					$convendDate = date("d-m-Y", $strendDate);
					$minendDate = strtotime($convendDate . ' -1 days');
					$endDate = date("d-m-Y", $minendDate);

                    $orderDetailArr["OrderDetail"] = array();
					$orderDetailArr["OrderDetail"]["user_id"] = $data["Data"]["user_id"];
					$orderDetailArr["OrderDetail"]["order_id"] = $orderId;
					$orderDetailArr["OrderDetail"]["package_id"] = $data["Data"]["package_id"];
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
					$orderDetailArr["OrderDetail"]["order_status"] = 0;
					$orderDetailArr["OrderDetail"]["is_active"] = 1;
					$orderDetailArr["OrderDetail"]["created_by"] = $data["Data"]["user_id"];
					$orderDetailArr["OrderDetail"]["modified_by"] = $data["Data"]["user_id"];
					
					$this->Order->OrderDetail->create();
					if($this->Order->OrderDetail->save($orderDetailArr))
					{
                        $orderDetailId = $this->Order->OrderDetail->id;
						$this->User->id = $data["Data"]["user_id"];
						$userUpdate["User"] = array();
						$userUpdate["User"]["fully_registered"] = 1;
						$userUpdate["User"]["is_active"] = 1;
						$this->User->save($userUpdate);
                        
                        //update last package if available
                        $q = "UPDATE order_details SET order_status = 0,
                                is_active = 0 WHERE user_id = ".$data["Data"]["user_id"] . "
                                AND id <> ". $orderDetailId;
                        $this->Order->query($q);

                        $this->Order->id = $orderId;
                        $this->Order->saveField('order_status', 1);

                        $this->Order->OrderDetail->id = $orderDetailId;
                        $this->Order->OrderDetail->saveField('order_status', 1);
                        
                        $userInfoArr = $this->setUserInfoData($orderId);
                        $this->_generateFamilyCoupons($userInfoArr);
                        $this->_set_response($data, 1, "Registered successfully", "", array());
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
			if (!empty($myjson))
			{
				$this->_create_log($myjson);
				if ($this->_validate_request($myjson))
				{
					$intUserId = $this->_validate_authentication($myjson);
					if ($intUserId != 0)
					{
					   if ($this->_profile_pic_upload($myjson, $intUserId))
									{
										$Data = $myjson["Data"]; //Extract Data array from json

										$Auth = $myjson["Authentication"]; // extract authentication array from json

										$my_base64_string = $Data["photo_string_base64"]; //now get the received base64 string 

										$picname = $Data["photo"]; //get the picture name for saving the file
										//call the function to convert base64 to image
									    $image = $this->base64_to_jpeg($my_base64_string, $picname);
                                        mkdir("profiles_pic/".$Auth["username"], 0777);
										//now copy the picture to desired folder
										copy($picname, 'profiles_pic/'.$Auth["username"].'/' . $picname);
										
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
			$this->UserProfile->id = $usr_id['UserProfile']['id'];
            $this->UserProfile->saveField('profile_pic',$data["Data"]["photo"]);
			$profilepic['profile_pic']=array();
			$profilepic['profile_pic'] = "http://apps1.onetouchresponse.com/profiles_pic/".$username['User']['username'].'/'.$data["Data"]["photo"];
			$this->_set_response($data, 1, PICTURE_UPLOAD_SUCCESS, "", $profilepic);
			return true;
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
			if (!empty($myjson))
			{
				$this->_create_log($myjson);
				if ($this->_validate_request($myjson))
				{
					$intUserId = $this->_validate_authentication($myjson);
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
										$Data = $myjson["Data"]; //Extract Data array from json

										$Auth = $myjson["Authentication"]; // extract authentication array from json

										$my_base64_string = $Data["photo_string_base64"]; //now get the received base64 string 

										$picname = $Data["photo"]; //get the picture name for saving the file
										//call the function to convert base64 to image
									    $image = $this->base64_to_jpeg($my_base64_string, $picname);
                                        mkdir("photos/".$Auth["username"], 0777);
										//now copy the picture to desired folder
										copy($picname, 'photos/'.$Auth["username"].'/' . $picname);

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
						           if ($this->_do_photo_upload($myjson, $intUserId))
									{
										$Data = $myjson["Data"]; //Extract Data array from json

										$Auth = $myjson["Authentication"]; // extract authentication array from json

										$my_base64_string = $Data["photo_string_base64"]; //now get the received base64 string 

										$picname = $Data["photo"]; //get the picture name for saving the file
										//call the function to convert base64 to image
									    $image = $this->base64_to_jpeg($my_base64_string, $picname);
                                        mkdir("photos/".$Auth["username"], 0777);
										//now copy the picture to desired folder
										copy($picname, 'photos/'.$Auth["username"].'/' . $picname);

										//now copy the picture to desired folder
										/*copy($image, PHOTO_UPLOAD_ABS_PATH . $Auth["username"] . "/" . $picname);*/
									}   	
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
		$password = trim($decrypted_pass);
		$td = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td, $secret_key, $iv); 
		$decrypted_username = mdecrypt_generic($td, $this->hex2bin2($data['Authentication']['username']));
		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td); 
		$username = trim($decrypted_username);
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

    /*
	* FR User Authentication
    */
	public function fr_base64_image_upload()
	{
		header('Content-type: application/json'); //Set the header to json to recognise we are goming to rceive json
		$Rdata = file_get_contents('php://input'); // assighn all the received dump to variable
		
		if ($Rdata != '')
		{
			$myjson = json_decode($Rdata, true); // now decode the actual json received from the variable
			if (!empty($myjson))
			{
				$this->_create_log($myjson);
				if ($this->_validate_request($myjson))
				{
					$intUserId = $this->_validate_fr_authentication($myjson);
					if ($intUserId != 0)
					{
					   if ($this->_fr_do_photo_upload($myjson, $intUserId))
									{
										$Data = $myjson["Data"]; //Extract Data array from json

										$Auth = $myjson["Authentication"]; // extract authentication array from json

										$my_base64_string = $Data["photo_string_base64"]; //now get the received base64 string 

										$picname = $Data["photo"]; //get the picture name for saving the file
										//call the function to convert base64 to image
									    $image = $this->base64_to_jpeg($my_base64_string, $picname);
                                        mkdir("photos/".$Auth["username"], 0777);
										//now copy the picture to desired folder
										copy($picname, 'photos/'.$Auth["username"].'/' . $picname);
										
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
				$this->_set_response($myjson, 0, "", INVALID_REQUEST, array());
			}
		}
		else
		{
			$this->_set_response(array(), 0, "", INVALID_REQUEST, array());
		}
		exit;
	}
	
  
   private function _fr_do_photo_upload($data, $userID)
	{
		
		    $this->loadModel('Incident');
			$this->Incident->recursive = -1;
			$incident = $this->Incident->find('first', array('conditions' => array('id' => $data["Data"]["reference_id"])));
			$start_time = $incident['Incident']['start_timestamp'];
			$this->loadModel("fr_incident_details");
			$this->fr_incident_details->recursive = -1;
			$insertArray["fr_incident_details"] = array();
			$insertArray["fr_incident_details"]["id"] = "";
			$insertArray["fr_incident_details"]["incident_id"] = $data["Data"]["reference_id"];
			$insertArray["fr_incident_details"]["photo"] = $data["Data"]["photo"];
			$insertArray["fr_incident_details"]["start_time"] = $start_time;
			$insertArray["fr_incident_details"]["arrival_time"] = date("Y-m-d H:i:s");
			$insertArray["fr_incident_details"]["arrival_status"] = 1;
			$insertArray["fr_incident_details"]["created_on"] = date("Y-m-d H:i:s");
			$insertArray["fr_incident_details"]["created_by"] = $userID;
			$insertArray["fr_incident_details"]["modified_on"] = date("Y-m-d H:i:s");
			$insertArray["fr_incident_details"]["modified_by"] = $userID;
		    if($this->fr_incident_details->save($insertArray))
			{
			/*update fr_responder status*/
			$this->loadModel('first_responders');
		    $this->first_responders->id = $userID;
            $this->first_responders->saveField('fr_event_status', 1);
			/*Close fr_responder status*/
			$this->_set_response($data, 1, PICTURE_UPLOAD_SUCCESS, "", array());
			return true;
		    }
		    else
		    {
			$this->_invalid_request($data);
			return false;
		    }
		    
	}
    
	private function _authenticate_fr_member($data)
	{
		$intUserId = $this->_validate_fr_authentication($data);
		if ($intUserId != 0)
		{
			    $this->_setFROnline($intUserId);
				$responseData = $this->_getSettingDetails($intUserId); // Adds FTP & Dialer info
			    //Adds Active Call Info in Response Data
				/*$this->_getResponderActiveCalls($responseData, $intUserId);*/
				//Success response for responder
				$this->_set_response($data, 1, SUCCESSFULL_LOGIN, "", $responseData);
		
		}
		else
		{
			//Sets response data in case of failure
			$this->_set_response($data, 0, "", INVALID_AUTHENTICATION, array());
		}
	}
   
    private function _validate_fr_username($data)
	{
		
		
		if (isset($data["Authentication"]["username"]) && !empty($data["Authentication"]["username"]))
		{
			$username = $data["Authentication"]["username"];
		}
		$userRecordArray = $this->_get_frdata_by_username($username);
		if ($userRecordArray != null)
		{
			$intUserId = $userRecordArray['id'];
            //Sets response data in case of inactive user
			if (!$this->_IsFRUserActive($intUserId))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
			else
			{
                    $password = $userRecordArray["p_manual"];
                    //Sending sms to user for account credetials
                    $messageForsms = "Your One Touch ID and Password is Username:" . $username . " Password:" . $password;
					$this->sendSingleSms($messageForsms, $this->_convertMobileNo($userRecordArray['p_mobile_number']));
					
                    $this->_set_response($data, 1, PASSWORD_SENT, "", array());
			}
		}
		else
		{
			$this->_invalid_request($data);
		}
	}
	
	private function _get_frdata_by_username($username)
	{
		$this->loadModel("first_responders");
        $findArray = array();
		$findArray['username'] = $username;
		$userRecordArray = $this->first_responders->find('all', array('conditions' => $findArray, 'recursive' => -1));
		if (isset($userRecordArray) && !empty($userRecordArray))
		{
			return $userRecordArray[0]["first_responders"];
		}
		return null;
	}
	
	private function _setFROnline($frID)
	{
		$this->loadModel('first_responders');
		$this->first_responders->id = $frID;

		$this->first_responders->saveField('fr_login_status', 1);
	}
	
	private function _fr_logout_status($data)
	{
		$this->loadModel('first_responders');
		$this->first_responders->id = $data["Data"]["user_id"];
        $this->first_responders->saveField('fr_event_status', 1);
		$this->first_responders->saveField('fr_login_status', 0);
		$this->_set_response($data, 1, "Logout Successfully", "");
	}
	
	private function _fr_tracking($data)
	{
		$userID = $this->_validate_fr_authentication($data);
		if ($userID != 0)
		{
			if (!$this->_IsFRUserActive($userID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
			else
			{
			        
					$updateArray["first_responders"] = array();
					$updateArray["first_responders"]["latitude"] = $data["Data"]["latitude"];
					$updateArray["first_responders"]["longitude"] = $data["Data"]["longitude"];
					$updateArray["first_responders"]["modified_on"] = date("Y-m-d H:i:s");
                    $this->loadModel("first_responders");
					$this->first_responders->id = $userID;
					if ($this->first_responders->save($updateArray))
					{
						
					 /*send notification in 10 KM radius*/
					 $this->loadModel("first_responders");
					 $this->first_responders->recursive = -1;
					 $fr_details = $this->first_responders->find('first', array('conditions' => array('id' => $userID,'fr_event_status' =>1)));
					 $fr_lat = $fr_details['first_responders']['latitude'];
					 $fr_long = $fr_details['first_responders']['longitude'];
			         $this->loadModel('IncidentResponder');
			         $this->IncidentResponder->contain(array('Incident', 'Incident.User', 'Incident.User.UserProfile'));
			         $incidentResponders = $this->IncidentResponder->find('first', array('conditions' =>array('assign_fr' => 1, 'is_accepted' => 0,'Incident.status' => STATUS_LIVE)));
					 $incidentID = $incidentResponders['IncidentResponder']['incident_id'];	
					 $this->loadModel('Incident');
			         $this->Incident->recursive = -1;
			         $incident = $this->Incident->find('first', array('conditions' => array('id' => $incidentID)));
					 $customer_ID= $incident['Incident']['customer_user_id'];
					 $latitude = $incident['Incident']['latitude'];
			         $longitude = $incident['Incident']['longitude'];
					 $event_status = $incident['Incident']['status'];
					 /*distance count*/
					 $theta = $fr_long - $longitude;
					 $dist = sin(deg2rad($fr_lat)) * sin(deg2rad($latitude)) +  cos(deg2rad($fr_lat)) * cos(deg2rad($latitude)) * cos(deg2rad($theta));
					 $dist = acos($dist);
					 $dist = rad2deg($dist);
					 $miles = $dist * 60 * 1.1515;
					 $total_dis = $miles * 1.609344;
					 /*End Total Distance*/
					 if($total_dis < 10)
					 {
					 $notification = $this->_send_fr_notifications($customer_ID,$incidentID,$latitude,$longitude,$event_status); 
					 }
					 else
					 {
					 $this->loadModel("IncidentResponder");
					 $this->IncidentResponder->recursive = -1;
					 $fr_responder = $this->IncidentResponder->find('first', array('conditions' => array('fr_user_id' => $userID),'order' => 'IncidentResponder.id DESC'));
					 $this->loadModel('Incident');
			         $this->Incident->recursive = -1;
			         $incident = $this->Incident->find('first', array('conditions' => array('id' => $fr_responder['IncidentResponder']['incident_id'])));
					 $incidents1 = array();
					 $incidents1['IncidentRequest'] = '';
					 $incidents1['event_status'] = $incident['Incident']['status'];
					 $notification = $incidents1;
					 }
					 
					 /*End notification*/
					 $this->_set_response($data, 1, TRACKING_UPDATE_SUCCESS, "",$notification);
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
	
	
	private function _send_fr_notifications($customer_ID,$incidentID,$latitude,$longitude,$event_status)
	{
		  
		    $incidentNotification = array();
            $requestData = array();
		    $this->loadModel("CustomerRecord");
			$this->CustomerRecord->recursive = -1;
			$userDetails = $this->CustomerRecord->find("first", array("conditions" => array("user_id" => $customer_ID)));
			$requestData['incident_id'] = $incidentID;
			$requestData['latitude'] = $latitude;
			$requestData['longitude'] = $longitude;
			$requestData['customer_name'] =$userDetails['CustomerRecord']['fullname'];
			$requestData['customer_mobile'] =$userDetails['CustomerRecord']['mobile'];
			$requestData['customer_age'] = date_diff(date_create($userDetails['CustomerRecord']['dob']), date_create('today'))->y;
			$requestData['customer_gender'] = $userDetails['CustomerRecord']['gender'];
			$requestData['event_status'] = $event_status;
			$incidentNotification['IncidentRequest'] = $requestData;
		    return $incidentNotification;
	}
	
	private function _fr_help_accept($data)
	{
		$userID = $this->_validate_fr_authentication($data);
		
		if ($userID != 0)
		{
			if (!$this->_IsFRUserActive($userID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
			else
			{
				$incidentID = $data["Data"]["incident_id"];
				
				if ($data["Data"]["status"]=='false')
				{
					$this->_set_response($data, 1, "Request Has Been Cancelled By You!!", "", array());
				}
				else if ($this->_isValidIncident($incidentID))
				{
					if ($this->_isAlreadyAcceptedFRIncident($incidentID))
					{
						if ($this->_isIncidentAllocatedToFR($incidentID))
						{
							$incidentFRID = $this->_getIncidentFRID($incidentID);
							if ($incidentFRID != 0)
							{
								$this->loadModel("IncidentResponder");
								$this->IncidentResponder->id = $incidentFRID;
								$this->IncidentResponder->saveField('fr_user_id', $userID);
								$this->IncidentResponder->saveField('assign_fr', 1);
								if ($this->IncidentResponder->saveField('is_accepted', 1))
								{
									
									$this->loadModel('first_responders');
									$this->first_responders->recursive = -1;
									$this->first_responders->id = $userID;
									$this->first_responders->saveField('fr_event_status', 2);
									$this->_set_response($data, 1, UPDATE_SUCCESS, "", array());
								}
								else
								{
									$this->_set_response($data, 0, "", UPDATE_FAILED, array());
								}
							}
						}
						else
						{
							$this->_set_response($data, 0, "", WRONG_INCIDENT, array());
						}
					}
					else
					{
					 
						
						$this->_set_response($data, 0, "", ALREADY_ACCEPTED, array());
					}
				}
				else
				{
					$this->_set_response($data, 0, "", INVALID_INCIDENT, array());
				}
			}
		}
		else
		{
			$this->_set_response($data, 0, "", INVALID_AUTHENTICATION, array());
		}
	}
	
	private function _validate_fr_authentication($data)
	{
		$intUserId = 0;
		$UserCheckParams = array();
		$UserCheckParams['username'] = $this->StringInputCleaner($data['Authentication']['username']);
		$UserCheckParams['p_password'] = AuthComponent::password($data['Authentication']['password']);
		$UserCheckParams['is_active'] = 1;
		$this->loadModel('first_responders');
		$arrUser = $this->first_responders->find("first", array("conditions" => $UserCheckParams));

		//Check if user is exist in the database or not
		if (isset($arrUser) && !empty($arrUser))
		{
			$intUserId = $arrUser['first_responders']['id'];
		}
		return $intUserId;
	}
	
	private function _IsFRUserActive($userId)
	{
		$statusCheckParmas = array();
		$statusCheckParmas['id'] = $userId;
		$statusCheckParmas['is_active'] = 1;
	    $this->loadModel("first_responders");
		$recordCount = $this->first_responders->find("count", array("conditions" => $statusCheckParmas));
		if ($recordCount > 0)
		{
			return true;
		}
		return false;
	}
	
	private function _isIncidentAllocatedToFR($incidentID)
	{
		(int) $incidentID;
		
		if ($incidentID > 0)
		{
			$checkParamas = array();
			$checkParamas["incident_id"] = $incidentID;
		    $checkParamas["is_accepted"] = 0;
			$this->loadModel("IncidentResponder");
			$countArray = $this->IncidentResponder->find("count", array("conditions" => $checkParamas));
			if ($countArray > 0)
			{
				return true;
			}
		}
		return false;
	}
	
	private function _getIncidentFRID($incidentID)
	{
		(int) $incidentID;
		
		if ($incidentID > 0)
		{
			$indcidentFRID = 0;

			$checkParamas = array();
			$checkParamas["incident_id"] = $incidentID;
			$checkParamas["is_accepted"] = 0;
            $this->loadModel("IncidentResponder");
			$recordArray = $this->IncidentResponder->find("all", array("conditions" => $checkParamas,
					'fields' => array("id")));
			if (isset($recordArray) && !empty($recordArray))
			{
				$indcidentFRID = $recordArray[0]["IncidentResponder"]["id"];
				return $indcidentFRID;
			}
		}
		return $indcidentFRID;
	}
	
	private function _isAlreadyAcceptedFRIncident($incidentID)
	{
		(int) $incidentID;
		if ($incidentID > 0)
		{
			$checkParamas = array();
			$checkParamas["incident_id"] = $incidentID;
			$checkParamas["is_accepted"] = false;

			$this->loadModel("IncidentResponder");
			$countArray = $this->IncidentResponder->find("count", array("conditions" => $checkParamas));
			if ($countArray > 0)
			{
				return true;
			}
		}
		return false;
	}
	
	/*
	End FR User Authentication
	*/

	/**
	 * Private function to Authenticate Member
	 * 
	 * @param Array $data
	 */
	private function _authenticate_member($data, $groupID)
	{
		$intUserId = $this->_validate_authentication($data);
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
				if (!isset($data['Data']['imei']) || empty($data['Data']['imei']))
				{
					$this->_set_response($data, 0, "", IMEI_NOT_RECIEVED, array());
				}
				//Checks if user has already active call
				else if ($this->_havingActiveCalls($intUserId, $data['Data']['imei']))
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
					$responseData = $this->_getSettingDetails($intUserId); // Adds FTP & Dialer info
					$this->_getAngelInfo($responseData, $intUserId);
					$this->_getServicesInfo($responseData, $intUserId);
					$this->_getIncidentsRemainingBalance($responseData, $intUserId);
					$this->_getSafecallsRemainingBalance($responseData, $intUserId);
					$this->_updateUserIMEI($intUserId, $data['Data']['imei']); //Updates User's Last IMEI
					
                    //Update users os type
                    if(isset($data['Data']['os_type']) && !empty($data['Data']['os_type'])):
                        $this->User->query("UPDATE users SET os_type = '".$data['Data']['os_type']."',modified_on='".date("Y-m-d H:i:s")."' WHERE id = ".$intUserId.";");
                    endif;
					//Adds Active Call Info in Response Data
					$this->_getActiveCalls($responseData, $intUserId);
					
					if(!empty($dataUser["User"]["last_imei"]) && $dataUser["User"]["last_imei"] != $data['Data']['imei'])
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
				$responseData = $this->_getSettingDetails($intUserId); // Adds FTP & Dialer info
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
		$imei = trim($decrypted_imei);
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
					$responseData = array("user_id"=>$intUserId); // Adds FTP & Dialer info
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
				$responseData = $this->_getSettingDetails($intUserId); // Adds FTP & Dialer info
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
		$username2= trim($decrypted_username);
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
		$userId = $this->_validate_encrption_authentication($data);
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
		$imei = trim($decrypted_imei);
		$td1 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td1, $secret_key, $iv); 
		$decrypted_firstname = mdecrypt_generic($td1, $this->hex2bin2($data['Data']['c_firstname']));
		mcrypt_generic_deinit($td1); 
		mcrypt_module_close($td1); 
		$firstname = trim($decrypted_firstname);
		$td2 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td2, $secret_key, $iv); 
		$decrypted_lastname = mdecrypt_generic($td2, $this->hex2bin2($data['Data']['c_lname']));
		mcrypt_generic_deinit($td2); 
		mcrypt_module_close($td2); 
		$lastname = trim($decrypted_lastname);
		$td3 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td3, $secret_key, $iv); 
		$decrypted_mobile = mdecrypt_generic($td3, $this->hex2bin2($data['Data']['mobile']));
		mcrypt_generic_deinit($td3); 
		mcrypt_module_close($td3); 
		$mobile = trim($decrypted_mobile);
		$td4 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td4, $secret_key, $iv); 
		$decrypted_username = mdecrypt_generic($td4, $this->hex2bin2($data['Data']['username']));
		mcrypt_generic_deinit($td4); 
		mcrypt_module_close($td4); 
		$username = trim($decrypted_username);
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td5, $secret_key, $iv); 
		$decrypted_longitude = mdecrypt_generic($td5, $this->hex2bin2($data['Data']['longitude']));
		mcrypt_generic_deinit($td5); 
		mcrypt_module_close($td5); 
		$longitude = trim($decrypted_longitude);
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td6, $secret_key, $iv); 
		$decrypted_latitude = mdecrypt_generic($td6, $this->hex2bin2($data['Data']['latitude']));
		mcrypt_generic_deinit($td6); 
		mcrypt_module_close($td6); 
		$latitude = trim($decrypted_latitude);
		$td7 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td7, $secret_key, $iv); 
		$decrypted_description = mdecrypt_generic($td7, $this->hex2bin2($data['Data']['description']));
		mcrypt_generic_deinit($td7); 
		mcrypt_module_close($td7); 
		$description = trim($decrypted_description);
		if ($userId != 0)
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

    /*Add TrackME info*/
    private function _addtrackme_info($data)
	{
		$userId = $this->_validate_authentication($data);
		$modelName = "SafeCall";
		if ($userId != 0)
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
			else if (!$this->_checkUserIMEI($userId, $data['Data']['imei']))
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
				$insertArr["trackme_add_infos"]["destination"] = $data["Data"]["destination"];
				$insertArr["trackme_add_infos"]["lattitude"] = $data["Data"]["destination_lattitude"];
				$insertArr["trackme_add_infos"]["longitude"] = $data["Data"]["destination_longitude"];
				$insertArr["trackme_add_infos"]["transport_mode"] = $data["Data"]["transport_mode"];
				$insertArr["trackme_add_infos"]["vehicle_no"] = $data["Data"]["vehicle_no"];
				if(!empty($data["Data"]["vehicle_no_pic"]))
				{
				$insertArr["trackme_add_infos"]["vehicle_no_pic"] = $data["Data"]["vehicle_no_pic"];
				$this->vehicle_noplate_pic($data);
				}
				$insertArr["trackme_add_infos"]["call_time_interval"] = $data["Data"]["call_time_interval"];
				$insertArr["trackme_add_infos"]["share_info"] = $data["Data"]["share_info"];
				$insertArr["trackme_add_infos"]["swipe_status"] =  $data["Data"]["swipe_status"];
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
				$insertArr[$modelName]["latitude"] = $data["Data"]["source_latitude"];
				$insertArr[$modelName]["longitude"] = $data["Data"]["source_longitude"];
				$insertArr[$modelName]["imei"] = $data["Data"]["imei"];
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
				  $insertArrTrack[$modelName . "Tracking"]["latitude"] = $data["Data"]["source_longitude"];
				  $insertArrTrack[$modelName . "Tracking"]["longitude"] = $data["Data"]["source_latitude"];
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
				  $responseArray["call_time_interval"] = $data["Data"]["call_time_interval"];	
				  $this->_getSafecallsRemainingBalance($responseArray, $userId);

				  //Message Content
				  $messageSafeCallInitialised = "Safe Call has been activated by: " . $userData['User']['firstname'] . ' ' . $userData['User']['lastname'] .
											" at : " . date("Y-m-d H:i:s") . " Location: http://maps.google.com/?ll=" . $data["Data"]["source_longitude"] . "," . $data["Data"]["source_longitude"].". One Touch Response is tracking the location.";

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
	
	private function vehicle_noplate_pic($data)
	{
		$username = $data['Authentication']['username'];
        $my_base64_string = $data["Data"]["photo_string_base64"];
        $vehiclepic = $data["Data"]["vehicle_no_pic"];
		//call the function to convert base64 to image
		$image = $this->base64_to_jpeg($my_base64_string, $vehiclepic);
        mkdir("trackme_pic/".$username, 0777);
		//now copy the picture to desired folder
		copy($image, 'trackme_pic/'.$username.'/' . $vehiclepic);
									
	}
	
	/*TrackME encrypted Info */
	
	 private function _addtrackme_encrypt_info($data)
	{
		$userId = $this->_validate_encrption_authentication($data);
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
		if ($userId != 0)
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
		if(!empty($registrationIDs))
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
		$userId = $this->_validate_authentication($data);
		if ($userId != 0)
		{
		$this->loadModel("trackme_safelocations");
	    $this->trackme_safelocations->recursive = -1;
	    $insertArr["trackme_safelocations"] = array();
	    $insertArr["trackme_safelocations"]["user_id"] = $userId;
		$insertArr["trackme_safelocations"]["location"] = $data["Data"]["location"];
		$insertArr["trackme_safelocations"]["latitude"] = $data["Data"]["latitude"];
		$insertArr["trackme_safelocations"]["longitude"] = $data["Data"]["longitude"];
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
		$userId = $this->_validate_authentication($data);
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
		$userId = $this->_validate_authentication($data);
		if ($userId != 0)
		{
		$this->loadModel("trackme_safelocations");
	    $this->trackme_safelocations->recursive = -1;
		$this->trackme_safelocations->id = $data["Data"]["location_id"];
		$this->trackme_safelocations->saveField('location', $data["Data"]["location"]);
		$this->trackme_safelocations->saveField('latitude', $data["Data"]["latitude"]);
		$this->trackme_safelocations->saveField('longitude', $data["Data"]["longitude"]);
		$this->trackme_safelocations->saveField('is_active', 1);
		$this->trackme_safelocations->saveField('modified_on',date("Y-m-d H:i:s"));
	    $this->_set_response($data, 1, "Safe Location Updated Successfully!", "", array());
		}
		else
		{
		$this->_invalid_request($data);
		}
	}	
	
	
	private function _delete_safe_location($data)
	{
		$userId = $this->_validate_authentication($data);
		if ($userId != 0)
		{
		$this->loadModel("trackme_safelocations");
	    $this->trackme_safelocations->recursive = -1;
		$this->trackme_safelocations->id = $data["Data"]["location_id"];
		$this->trackme_safelocations->delete();
	    $this->_set_response($data, 1, "Safe Location Deleted Successfully!", "", array());
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
	private function _validate_call($data, $modelName)
	{
		$userId = $this->_validate_authentication($data);
		if ($userId != 0)
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
			else if (!$this->_checkUserIMEI($userId, $data['Data']['imei']))
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
				$insertArr[$modelName]["longitude"] = $data["Data"]["longitude"];
				$insertArr[$modelName]["latitude"] = $data["Data"]["latitude"];
				$insertArr[$modelName]["imei"] = $data["Data"]["imei"];
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
					$insertArrTrack[$modelName . "Tracking"]["longitude"] = $data["Data"]["longitude"];
					$insertArrTrack[$modelName . "Tracking"]["latitude"] = $data["Data"]["latitude"];
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
											" at : " . date("Y-m-d H:i:s") . " Location: http://maps.google.com/?ll=" . $data["Data"]["latitude"] . "," . $data["Data"]["longitude"].". One Touch Response is tracking the location.";

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

							$messageIncidentInitialised = "We have received an Emergency Alert from " . $userData['User']['firstname'] . ' ' . $userData['User']['lastname'] ." and ".$userData['User']['mobile']." at " . date("Y-m-d H:i:s") . ". For the location click on http://maps.google.com/?ll=" . $data["Data"]["latitude"] . "," . $data["Data"]["longitude"]. " Assistance is being provided.";

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
	
	/*Validate encrypted data*/
   private function _validate_call_encrypt($data, $modelName)
	{
		$userId = $this->_validate_encrption_authentication($data);
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
		$imei = trim($decrypted_imei);
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td5, $secret_key, $iv); 
		$decrypted_longitude1 = mdecrypt_generic($td5, $this->hex2bin2($data['Data']['longitude']));
		$decrypted_longitude = trim($decrypted_longitude1);
		mcrypt_generic_deinit($td5); 
		mcrypt_module_close($td5); 
		$longitude = trim($decrypted_longitude);
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td6, $secret_key, $iv); 
		$decrypted_latitude1 = mdecrypt_generic($td6, $this->hex2bin2($data['Data']['latitude']));
		$decrypted_latitude = trim($decrypted_latitude1);
		if ($userId != 0)
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
											" at : " . date("Y-m-d H:i:s") . " Location: http://maps.google.com/?ll=" . $data["Data"]["latitude"] . "," . $data["Data"]["longitude"].". One Touch Response is tracking the location.";

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

							$messageIncidentInitialised = "We have received an Emergency Alert from " . $userData['User']['firstname'] . ' ' . $userData['User']['lastname'] ." and ".$userData['User']['mobile']." at " . date("Y-m-d H:i:s") . ". For the location click on http://maps.google.com/?ll=" . $data["Data"]["latitude"] . "," . $data["Data"]["longitude"]. " Assistance is being provided.";

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
	private function _tracking($data, $modelName)
	{
		$userID = $this->_validate_authentication($data);
		if ($userID != 0)
		{
			if ($this->_validate_call_credentials($data, $modelName))
			{
				if($modelName == "Incident")
				{
				$notification = $this->_send_notifications($userID);
				}
				else
				{
				$notification = array();
				}
				//update longitude and latitude in safe call
				$updateArr[$modelName] = array();
				$updateArr[$modelName]["latitude"] = $data["Data"]["latitude"];
				$updateArr[$modelName]["longitude"] = $data["Data"]["longitude"];
				$updateArr[$modelName]["modified_on"] = date("Y-m-d H:i:s");
				$updateArr[$modelName]["modified_by"] = $userID;

				$this->loadModel($modelName);
				if ($modelName == "SafeCall")
				{
                    
					$this->{$modelName}->id = $data["Data"]["safe_call_id"];
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
                        $userData = $this->User->findById($userID);
                           
                        //Message Content
                        $messageSafeCallRunning = $userData['User']['firstname'] . ' ' . $userData['User']['lastname'] .
                                        " has reached Location: http://maps.google.com/?ll=" . $data["Data"]["latitude"] . "," . $data["Data"]["longitude"]. ". One Touch Response is tracking.";
                        
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
					$this->{$modelName}->id = $data["Data"]["incident_id"];
				}
				if ($this->{$modelName}->save($updateArr))
				{
					//insert record in safe call tracking
					$insertArr[$modelName . "Tracking"] = array();
					$insertArr[$modelName . "Tracking"]["id"] = "";
					if ($modelName == "SafeCall")
					{
						$insertArr[$modelName . "Tracking"]["safe_call_id"] = $data["Data"]["safe_call_id"];
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
						$insertArr[$modelName . "Tracking"]["incident_id"] = $data["Data"]["incident_id"];
					}
					$insertArr[$modelName . "Tracking"]["latitude"] = $data["Data"]["latitude"];
					$insertArr[$modelName . "Tracking"]["longitude"] = $data["Data"]["longitude"];
					$insertArr[$modelName . "Tracking"]["created_by"] = $userID;
					$insertArr[$modelName . "Tracking"]["modified_by"] = $userID;

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
	
   /*Encrypted Tracking data*/
	private function _tracking_encrypt($data, $modelName)
	{
		$userID = $this->_validate_encrption_authentication($data);
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
		$td5 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td5, $secret_key, $iv); 
		$decrypted_longitude1 = mdecrypt_generic($td5, $this->hex2bin2($data['Data']['longitude']));
		$decrypted_longitude = trim($decrypted_longitude1);
		mcrypt_generic_deinit($td5); 
		mcrypt_module_close($td5); 
		$longitude = trim($decrypted_longitude);
		$td6 = mcrypt_module_open($cipher, "", $mode, $iv); 
		mcrypt_generic_init($td6, $secret_key, $iv); 
		$decrypted_latitude1 = mdecrypt_generic($td6, $this->hex2bin2($data['Data']['latitude']));
		$decrypted_latitude = trim($decrypted_latitude1);
		if ($userID != 0)
		{
			if ($this->_validate_call_encrypt_credentials($data, $modelName))
			{
				if($modelName == "Incident")
				{
				$notification = $this->_send_notifications($userID);
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
				$updateArr[$modelName]["modified_by"] = $userID;

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
                        $userData = $this->User->findById($userID);
                           
                        //Message Content
                        $messageSafeCallRunning = $userData['User']['firstname'] . ' ' . $userData['User']['lastname'] .
                                        " has reached Location: http://maps.google.com/?ll=" . $data["Data"]["latitude"] . "," . $data["Data"]["longitude"]. ". One Touch Response is tracking.";
                        
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
					$insertArr[$modelName . "Tracking"]["created_by"] = $userID;
					$insertArr[$modelName . "Tracking"]["modified_by"] = $userID;

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
		$imei = trim($decrypted_imei);
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
		$userID = $this->_validate_authentication($data);
		if ($userID != 0)
		{
			if ($this->_validate_call_credentials($data, $modelName))
			{
                $this->loadModel("User");
                $this->User->recursive = 0;
				$userData = $this->User->findById($userID);
                
				//update longitude and latitude in safe call
				$updateArr[$modelName] = array();
				$updateArr[$modelName]["latitude"] = $data["Data"]["latitude"];
				$updateArr[$modelName]["longitude"] = $data["Data"]["longitude"];
				$updateArr[$modelName]["end_timestamp"] = date("Y-m-d H:i:s");
				$updateArr[$modelName]["modified_on"] = date("Y-m-d H:i:s");
				$updateArr[$modelName]["status"] = STATUS_CLOSE;
				$updateArr[$modelName]["modified_by"] = $userID;

				$this->loadModel($modelName);
				if ($modelName == "SafeCall")
				{
					$this->{$modelName}->id = $data["Data"]["safe_call_id"];
				}
				else
				{
					$this->{$modelName}->id = $data["Data"]["incident_id"];
					//Update is_active_call_closed so that next tracking will update responder                    
					$this->_setResponderActiveCallClosed($data["Data"]["incident_id"]);
				}
                
				if ($this->{$modelName}->save($updateArr))
				{
                    if ($modelName == "SafeCall"):
                        //Message Content
                        $messageSafeCallClosed = $userData['User']['firstname'] . ' ' . $userData['User']['lastname'] .
                                        " has reached his/her destination at " . date('Y-m-d H:i:s') . 
                                        ". Location: http://maps.google.com/?ll=" . $data['Data']['latitude'] . "," . $data['Data']['longitude']. ".";

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
						$insertArr[$modelName . "Tracking"]["safe_call_id"] = $data["Data"]["safe_call_id"];
					}
					else
					{
						$insertArr[$modelName . "Tracking"]["incident_id"] = $data["Data"]["incident_id"];
					}
					$insertArr[$modelName . "Tracking"]["latitude"] = $data["Data"]["latitude"];
					$insertArr[$modelName . "Tracking"]["longitude"] = $data["Data"]["longitude"];
					$insertArr[$modelName . "Tracking"]["created_by"] = $userID;
					$insertArr[$modelName . "Tracking"]["modified_by"] = $userID;

					$this->loadModel($modelName . "Tracking");
					$this->{$modelName . "Tracking"}->create();
					if ($this->{$modelName . "Tracking"}->save($insertArr))
					{
						$insertArrDetail[$modelName . "Detail"] = array();
						$insertArrDetail[$modelName . "Detail"]["id"] = "";
						$insertArrDetail[$modelName . "Detail"]["created_by"] = $userID;
						$insertArrDetail[$modelName . "Detail"]["modified_by"] = $userID;

						if ($modelName == "SafeCall")
						{
							$insertArrDetail[$modelName . "Detail"]["safe_call_id"] = $data["Data"]["safe_call_id"];
						}
						else
						{
							$insertArrDetail[$modelName . "Detail"]["incident_id"] = $data["Data"]["incident_id"];
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
							$incidentdetails = $this->IncidentResponder->find('first', array('conditions' => array('incident_id' => $data["Data"]["incident_id"])));
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
	 * For tracking the responder
	 * @params  Array data
	 */
	private function _responder_tracking($data)
	{
		$userID = $this->_validate_authentication($data);
		if ($userID != 0)
		{
			if (!$this->_IsUserActive($userID, RESPONDER_GROUP_ID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
			else
			{
				$insertArray["ResponderTracking"] = array();
				$insertArray["ResponderTracking"]["id"] = "";
				$insertArray["ResponderTracking"]["user_id"] = $userID;
				$insertArray["ResponderTracking"]["latitude"] = $data["Data"]["latitude"];
				$insertArray["ResponderTracking"]["longitude"] = $data["Data"]["longitude"];
				$insertArray["ResponderTracking"]["created_by"] = $userID;
				$insertArray["ResponderTracking"]["modified_by"] = $userID;

				$this->loadModel("ResponderTracking");
				$this->ResponderTracking->create();
				if ($this->ResponderTracking->save($insertArray))
				{
					$updateArray["User"] = array();
					$updateArray["User"]["latitude"] = $data["Data"]["latitude"];
					$updateArray["User"]["longitude"] = $data["Data"]["longitude"];
					$updateArray["User"]["modified_by"] = $userID;
					$updateArray["User"]["modified_on"] = date("Y-m-d H:i:s");

					$this->loadModel("User");
					$this->User->id = $userID;
					if ($this->User->save($updateArray))
					{
					 $finalNotifications = array();
		             $finalNotify = array();
		             $incidentNotification = array();
					 $Notifications = array();
					 //Adds responder request information
			         $this->loadModel('User');
			         $this->User->id = $userID;
			         $this->User->saveField('_is_incident_request_unsent', 0);
				     $this->loadModel('IncidentResponder');
				     $this->IncidentResponder->contain(array('Incident', 'Incident.User', 'Incident.User.UserProfile'));
				     $incidentResponders = $this->IncidentResponder->find('first', array('conditions' =>array('responder_user_id' => $userID, 'is_accepted' => 0,'Incident.status' => STATUS_LIVE)));   
					 $incidentResponders2 = $this->IncidentResponder->find('first', array('conditions' =>array('responder_user_id' => $userID),"order" => array("IncidentResponder.id" => "DESC")));

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
					 if(!empty($requestData))
					 {
					 $incidentNotification['IncidentRequest'] = $requestData;
					 }
					 else
					 {
					 $this->loadModel('IncidentResponder');
					 $this->IncidentResponder->recursive = -1;
					 $incidentResponders2 = $this->IncidentResponder->find('first', array('conditions' =>array('responder_user_id' => $userID),"order" => array("IncidentResponder.id" => "DESC")));
					 $incidentID1 = $incidentResponders2['IncidentResponder']['incident_id'];		
				     $this->loadModel('Incident');
				     $this->Incident->recursive = -1;
				     $incident1 = $this->Incident->find('first', array('conditions' => array('id' => $incidentID1)));
				     $event_status2= $incident1['Incident']['status'];
					 $event_status = array();
		             $event_status['event_status'] = $event_status2;
		             $incidentNotification['Notifications'] = $event_status;
					 }
				     $this->_set_response($data, 1, TRACKING_UPDATE_SUCCESS, "", $incidentNotification);
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

	/*
	 * Incident accetance request send to the reponder
	 * @params  Array data
	 */
	private function _incident_acceptance($data)
	{
		$responderID = $this->_validate_authentication($data);
		if ($responderID != 0)
		{
			if (!$this->_IsUserActive($responderID, RESPONDER_GROUP_ID))
			{
				$this->_set_response($data, 2, "", INACTIVE_USER, array());
			}
			else
			{
				$incidentID = $data["Data"]["incident_id"];
				$incidentStatus = "false";
				if (isset($data["Data"]["status"]) && !empty($data["Data"]["status"]))
				{
					$incidentStatus = $data["Data"]["status"];
				}
				if ($this->_isValidIncident($incidentID))
				{
					if ($this->_isAlreadyAcceptedIncident($incidentID))
					{
						if ($this->_isIncidentAllocatedToResponder($incidentID, $responderID))
						{
							$incidentResponderID = $this->_getIncidentResponderID($incidentID, $responderID);
							if ($incidentResponderID != 0)
							{
								$updateParams["IncidentResponder"] = array();
								$updateParams["IncidentResponder"]["is_accepted"] = ($incidentStatus == "true") ? 1 : 0;

								if ($incidentStatus == "false")
								{
									$this->_setResponderOnline($responderID);
								}
                                
								$this->loadModel("IncidentResponder");
								$this->IncidentResponder->id = $incidentResponderID;
								$this->IncidentResponder->saveField('assign_responder', 1);
								if ($this->IncidentResponder->saveField('is_accepted', $updateParams["IncidentResponder"]["is_accepted"]))
								{
									
									$this->loadModel('User');
									$this->User->recursive = -1;
									$this->User->id = $responderID;
									$this->User->saveField('responder_status', 1);
									$this->_set_response($data, 1, UPDATE_SUCCESS, "", array());
								}
								else
								{
									$this->_set_response($data, 0, "", UPDATE_FAILED, array());
								}
							}
						}
						else
						{
							$this->_setResponderOnline($responderID);
							$this->_set_response($data, 0, "", WRONG_INCIDENT, array());
						}
					}
					else
					{
					 
						$this->_setResponderOnline($responderID);
						$this->_set_response($data, 0, "", ALREADY_ACCEPTED, array());
					}
				}
				else
				{
					$this->_set_response($data, 0, "", INVALID_INCIDENT, array());
				}
			}
		}
		else
		{
			$this->_set_response($data, 0, "", INVALID_AUTHENTICATION, array());
		}
	}

	/*
	 * For verifying the valid incident or not
	 * @param Int $incidentID
	 * return boolean
	 */
	private function _isValidIncident($incidentID)
	{
		(int) $incidentID;
		if ($incidentID > 0)
		{
			$checkParamas = array();
			$checkParamas["Incident.id"] = $incidentID;
			$checkParamas["Incident.status"] = STATUS_LIVE;
			$checkParamas["Incident.end_timestamp"] = null;

			$this->loadModel("Incident");
			$countArray = $this->Incident->find("count", array("conditions" => $checkParamas));
			if ($countArray > 0)
			{
				return true;
			}
		}
		return false;
	}

	/*
	 * For verifying the incident already accepted or not
	 * @param Int $incidentID
	 * return boolean
	 */
	private function _isAlreadyAcceptedIncident($incidentID)
	{
		(int) $incidentID;
		if ($incidentID > 0)
		{
			$checkParamas = array();
			$checkParamas["incident_id"] = $incidentID;
			$checkParamas["is_accepted"] = false;

			$this->loadModel("IncidentResponder");
			$countArray = $this->IncidentResponder->find("count", array("conditions" => $checkParamas));
			if ($countArray > 0)
			{
				return true;
			}
		}
		return false;
	}

	/*
	 * For verifying the incident is allocated to this responder or not
	 * @param $incidentID int
	 * @param $responderID int
	 * return boolean
	 */
	private function _isIncidentAllocatedToResponder($incidentID, $responderID)
	{
		(int) $incidentID;
		(int) $responderID;

		if ($incidentID > 0 && $responderID > 0)
		{
			$checkParamas = array();
			$checkParamas["incident_id"] = $incidentID;
			$checkParamas["responder_user_id"] = $responderID;

			$this->loadModel("IncidentResponder");
			$countArray = $this->IncidentResponder->find("count", array("conditions" => $checkParamas));
			if ($countArray > 0)
			{
				return true;
			}
		}
		return false;
	}

	/*
	 * For gettind the id for the incident responder table id for 
	 * update
	 * @param $incidentID int
	 * @param $responderID int
	 * return $incidentResponderID int
	 */
	private function _getIncidentResponderID($incidentID, $responderID)
	{
		(int) $incidentID;
		(int) $responderID;

		if ($incidentID > 0 && $responderID > 0)
		{
			$indcidentResponderID = 0;

			$checkParamas = array();
			$checkParamas["incident_id"] = $incidentID;
			$checkParamas["responder_user_id"] = $responderID;
			$checkParamas["is_accepted"] = false;

			$this->loadModel("IncidentResponder");
			$recordArray = $this->IncidentResponder->find("all", array("conditions" => $checkParamas,
					'fields' => array("id")));
			if (isset($recordArray) && !empty($recordArray))
			{
				$indcidentResponderID = $recordArray[0]["IncidentResponder"]["id"];
				return $indcidentResponderID;
			}
		}
		return $indcidentResponderID;
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
		$insertArr["Photo"]["id"] = "";
		$insertArr["Photo"]["user_id"] = $userID;
		if ($data["Data"]["type"] == "Incident")
		{
			$insertArr["Photo"]["type"] = "Incident";
			$insertArr["Photo"]["incident_id"] = $data["Data"]["reference_id"];
		}
		if ($data["Data"]["type"] == "SafeCall")
		{
			$insertArr["Photo"]["type"] = "Safe Call";
			$insertArr["Photo"]["safe_call_id"] = $data["Data"]["reference_id"];
		}
		$insertArr["Photo"]["photo"] = $data["Data"]["photo"];
		$insertArr["Photo"]["latitude"] = $data["Data"]["latitude"];
		$insertArr["Photo"]["longitude"] = $data["Data"]["longitude"];
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
	 * Retruns the setting details (FTP & mobile)
	 * 
	 * @return Array
	 */
	private function _getSettingDetails($intUserId)
	{
		$responseData = array();

		$this->loadModel('Setting');
		$data = $this->Setting->find('first');

		$responseData['ftp_host'] = $data['Setting']['ftp_host'];
		$responseData['ftp_username'] = $data['Setting']['ftp_username'];
		$responseData['ftp_password'] = $data['Setting']['ftp_password'];
		$responseData['mobile_no'] = $data['Setting']['mobile_no'];
        if(!empty($intUserId))
		{
		$responseData['user_id'] = $intUserId;
		}
		return $responseData;
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
		$this->loadModel('User');
		$this->User->id = $data["Data"]["user_id"];

		$this->User->saveField('is_social_login', 0);
		$this->_set_response($data, 1, "Logout Successfully", "");
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
	
	/*
	 * Logout responder
	 */
	private function _responder_logout($data)
	{
		$userID = $this->_validate_authentication($data);
		if ($userID != 0)
		{
			$insertArray["ResponderTracking"] = array();
			$insertArray["ResponderTracking"]["id"] = "";
			$insertArray["ResponderTracking"]["user_id"] = $userID;
			$insertArray["ResponderTracking"]["latitude"] = $data["Data"]["latitude"];
			$insertArray["ResponderTracking"]["longitude"] = $data["Data"]["longitude"];
			$insertArray["ResponderTracking"]["created_by"] = $userID;
			$insertArray["ResponderTracking"]["modified_by"] = $userID;

			$this->loadModel("ResponderTracking");
			$this->ResponderTracking->create();
			if ($this->ResponderTracking->save($insertArray))
			{
				$updateArray["User"] = array();
				$updateArray["User"]["latitude"] = $data["Data"]["latitude"];
				$updateArray["User"]["longitude"] = $data["Data"]["longitude"];
				$updateArray["User"]["responder_status"] = 0;//offline state of responder
				$updateArray["User"]["modified_by"] = $userID;
				$updateArray["User"]["modified_on"] = date("Y-m-d H:i:s");

				$this->loadModel("User");
				$this->User->id = $userID;
				$this->User->save($updateArray);
				$this->_set_response($data, 1, SUCCESSFULL_LOGOUT, "", array());
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