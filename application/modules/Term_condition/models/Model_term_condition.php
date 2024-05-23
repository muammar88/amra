<?php

/**
 *  -----------------------
 *	Model term condition
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_term_condition extends CI_Model
{
   private $company_id;
   private $status;
   private $content;
   private $error;
   private $write_log;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      $this->error = 0;
      $this->write_log = 1;
   }

   public function check_code( $code ) {
      $this->db->select('id')
         ->from('company')
         ->where('code', $code);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   } 

   function get_info_company( $code ){
      $this->db->select('id, name, icon')
         ->from('company')
         ->where('code', $code);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list['title'] = $rows->name; 
            $list['icon'] = 'company/icon/'.$rows->icon; 
         }
      }
      return $list;
   }



}