<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Smsgateway {

   private $sms;

   function __construct()
	{
		$this->sms = &get_instance();
	}

   function send_otp($otp, $receiver){

      $message = 'PIN%20OTP%20ANDA%20ADALAH%20:'.$otp;

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://app.whatspie.com/api/messages',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => 'receiver='.$receiver.'&device='.$this->sms->config->item('device_otp').'&message='.$message.'&type=chat',
		  CURLOPT_HTTPHEADER => array(
		    'Accept: application/json',
		    'Content-Type: application/x-www-form-urlencoded',
		    'Authorization: Bearer '.$this->sms->config->item('token_otp')
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		return $response;
   }


}
