<?php

/**
 *  -----------------------
 *	Users Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Atra404 extends CI_Controller
{

   private $company_code;

   /**
    * Construct
    */
   public function __construct()
   {
      parent::__construct();
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   public function index()
   {
      // add js files
      $this->index_loader->addData(array('title' => '404 Error'));
      // get setting values
      $data = $this->index_loader->Response();
      // sign up templating
      $this->templating->error_templating($data);
   }
}
