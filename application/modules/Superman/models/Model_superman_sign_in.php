<?php

/**
 *  -----------------------
 *	Model superman sign in
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_superman_sign_in extends CI_Model
{
   private $company_id;
   private $status;
   private $content;
   private $error;
   private $write_log;

   public function __construct()
   {
      parent::__construct();
      $this->error = 0;
      $this->write_log = 1;
   }

   // 
   function check_username_superman( $username ) {

   }


}