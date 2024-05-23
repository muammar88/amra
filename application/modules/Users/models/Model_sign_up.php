<?php
/**
*  -----------------------
*	Model sign up
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_sign_up extends CI_Model
{
   public function email_checking( $email ) {
      $this->db->select('id')
         ->from('company')
         ->where('email', $email);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return TRUE;
      }else{
         return FALSE;
      }
   }

   /**
    * get salt
    */
   public function get_salt(){
      $this->db->select('setting_value')
         ->from('base_setting')
         ->where('setting_name', 'salt');
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return $q->row()->setting_value;
      }else{
         return '123456';
      }
   }

   # check nomor whatsapp
   public function check_nomor_whatsapp( $nomor_whatsapp ) {
      $this->db->select('id')
         ->from('company')
         ->where('whatsapp_number', $nomor_whatsapp);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   # generated company code
   function generated_company_code(){
		$feedBack = false;
		$rand = '';
		do {
			$rand = $this->text_ops->random_num(7);
			$q = $this->db->select('code')
							 ->from('company')
							 ->where('code', $rand)
							 ->get();
			if($q->num_rows() == 0){
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
   }

   function verified_code_exist( $verified_code ){
      $this->db->select('id')
         ->from('company')
         ->where('verified_code', $verified_code);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return TRUE;
      }else{
         return FALSE;
      }
   }

   function get_company_id_by_verified_code( $verified_code ){
      $this->db->select('id, verified')
         ->from('company')
         ->where('verified_code', $verified_code);
      $return = array()   ;
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         $row = $q->row();
         return array('company_id' => $row->id, 'verified' => $row->verified);
      }else{
         return $return;
      }
   }

}
