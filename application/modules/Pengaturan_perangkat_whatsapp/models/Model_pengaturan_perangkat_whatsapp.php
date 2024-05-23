<?php

/**
 *  -----------------------
 *	Model pengaturan perangkat whatsapp
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_pengaturan_perangkat_whatsapp extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   // function get_info_api_key(){
   //    $this->db->select('setting_value')
   //       ->from('base_setting')
   //       ->where('setting_name','api_key');
   //    $q = $this->db->get();
   //    $api_key = '';
   //    if( $q->num_rows() > 0 ) {
   //       foreach ($q->result() as $rows) {
   //          $api_key = $rows->setting_value;
   //       }
   //    }
   //    return $api_key;
   // }

   // function get_info_device(){
   //    $this->db->select('device_key, device_number')
   //       ->from('company')
   //       ->where('id', $this->company_id);
   //    $q = $this->db->get();
   //    $list = array();
   //    if( $q->num_rows() > 0 ) {
   //       foreach ( $q->result() as $rows ) {
   //          $list['device_key'] = $rows->device_key;
   //          $list['device_number'] = $rows->device_number;
   //       }
   //    }
   //    return $list;
   // }


}