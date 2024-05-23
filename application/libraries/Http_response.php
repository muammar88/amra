<?php

/**
 *  -----------------------
 *	Authentication library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Http_response
{

   private $response;

   public function __construct()
   {
      $this->CI = &get_instance();
   }

   public function feedBack($array)
   {
      $feedBack = array();
      foreach ($array as $key => $value) {
         $feedBack[$key] = $value;
      }
      $feedBack[$this->security->get_csrf_token_name()] = $this->security->get_csrf_hash();
      return $feedBack;
   }

   public function reponse()
   {
   }
}
