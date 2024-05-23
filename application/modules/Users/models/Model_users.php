<?php
/**
*  -----------------------
*	Model sign up
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_users extends CI_Model
{

   # model get all tab
   function get_all_tab(){
      $this->db->select('id, name, icon, path, description')
         ->from('base_tab');
      $q  = $this->db->get();
      $feedBack = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $feedBack[$rows->id] = array('name' => $rows->name, 'icon' => $rows->icon, 'path' => $rows->path, 'description' => $rows->description);
         }
      }
      return $feedBack;
   }

   # model get modul submodul tab
   public function get_modul_submodul_tab( $modul_access ) {
      # get all tab
      $all_tab = $this->get_all_tab();
      $modulTab = array();
      $submodulTab = array();
      foreach ($modul_access as $key => $value) {
         if( $value['modul_path'] != '#'){
            if( isset( $value['tab']  ) && $value['tab'] != '' ){
               $modulTab[$value['modul_path']] = $value['tab'];
            }
         }else{
            foreach ($value['submodul'] as $keySUB => $valueSUB) {
               if( isset($valueSUB['tab']) && $valueSUB['tab'] != '' ){
                  $submodulTab[ $valueSUB['submodules_path'] ] = $valueSUB['tab'];
               }
            }
         }
      }
      return array('modul_tab' => $modulTab, 'submodul_tab' => $submodulTab);
   }

   # model get info profils
   function get_info_profil( $param, $level_akun ){
      $feedBack = array();
      if( $level_akun == 'administrator' ) {
         $this->db->select('photo_profil, name, email')
            ->from('company')
            ->where('email', $param);
         $q = $this->db->get();
         if( $q->num_rows() > 0 ){
            foreach ( $q->result() as $rows ) {
               # check photo
               $src = FCPATH . 'image/company/' . $rows->photo_profil;
               $photo = 'personal/avatar.svg';
               if( $rows->photo_profil != '' ){
                  if (file_exists($src)) {
         				$photo = 'company/'. $rows->photo_profil;
         			}
               }
               $feedBack['photo'] = $photo;
               $feedBack['name'] = $rows->name;
               $feedBack['email'] = $rows->email;
            }
         }
      }elseif ( $level_akun == 'staff' ) {
         $this->db->select('p.fullname, p.photo')
            ->from('base_users AS u')
            ->join('personal AS p', 'u.personal_id=p.personal_id', 'inner')
            ->where('p.nomor_whatsapp', $param)
            ->where('u.company_id', $this->session->userdata($this->config->item('apps_name'))['company_id']);
         $q = $this->db->get();
         if( $q->num_rows() > 0 ){
            foreach ($q->result() as $rows) {
               # check photo
               $src = FCPATH . 'image/personal/' . $rows->photo;
               $photo = 'personal/avatar.svg';
               if( $rows->photo != '' ){
                  if (file_exists($src)) {
         				$photo = 'personal/'. $rows->photo;
         			}
               }
               $feedBack['photo'] = $photo;
               $feedBack['name'] = $rows->fullname;
            }
         }
      }
      $feedBack['level_akun'] = $level_akun;
      return $feedBack;
   }

   # check if email is exist
   function check_email_username( $email_username ){
      $level_akun = $this->session->userdata($this->config->item('apps_name'))['level_akun'];
      if ( $level_akun == 'administrator' ) {
         $company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
         # check email from database
         $this->db->select('id')
            ->from('company')
            ->where('id != "'.$company_id.'"')
            ->where('email', $email_username);
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            return array('error' => true, 'error_msg' => 'Email sudah terdaftar dipangkalan data');
         } else {
            return array('error' => false);
         }
      } else {
         $user_id = $this->session->userdata($this->config->item('apps_name'))['user_id'];
         $this->db->select('user_id')
                  ->from('base_users')
                  ->where('username', $email_username)
                  ->where('user_id != "'.$user_id.'"');
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            return array('error' => true, 'error_msg' => 'Nomor Whatsapp sudah terdaftar dipangkalan data');
         } else {
            return array('error' => false);
         }
      }
   }

   function get_all_info_company( $code ){
      $this->db->select('id, code, name, whatsapp_number, telp, email, start_date_subscribtion, end_date_subscribtion')
         ->from('company')
         ->where('code', $code);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         $row = $q->row();
         $list['id'] = $row->id;
         $list['code'] = $row->code;
         $list['name'] = $row->name;
         $list['whatsapp_number'] = $row->whatsapp_number;
         $list['telp'] = $row->telp;
         $list['email'] = $row->email;
         $list['start_date_subscribtion'] = $row->start_date_subscribtion;
         $list['end_date_subscribtion'] = $row->end_date_subscribtion;
      }
      return $list;
   }

   # get info company
   function get_info_company( $code ){
      $this->db->select('c.id, c.code, c.name, c.whatsapp_number, c.telp, c.email, sp.duration, sp.pay_per_month, sp.total, sp.end_date_subscribtion')
         ->from('company AS c')
         ->join('subscribtion_payment_history AS sp', 'c.id=sp.company_id', 'inner')
         ->where('c.code', $code)
         ->where('payment_status', 'process');
      $this->db->group_start()
         ->where('c.payment_process', 'true')
         ->or_where('c.verified', 'unverified')
         ->group_end();
      $q = $this->db->get();
      // $code = '';
      $list = array();
      if( $q->num_rows() > 0 ) {
         $row = $q->row();
         $list['company_id'] = $row->id;
         $list['code'] = $row->code;
         $list['name'] = $row->name;
         $list['whatsapp_number'] = $row->whatsapp_number;
         $list['telp'] = $row->telp;
         $list['email'] = $row->email;
         $list['duration'] = $row->duration;
         $list['pay_per_month'] = $row->pay_per_month;
         $list['total'] = $row->total;
         $list['end_date_subscribtion'] = $row->end_date_subscribtion;
      }
      return $list;
   }

   function check_company_code_renew_exist( $code ){
      $this->db->select('c.id')
        ->from('company AS c')
        ->where('c.code', $code)
        ->where('c.payment_process', 'true')
        ->where('c.verified', 'verified');
     $q = $this->db->get();
     if( $q->num_rows() > 0 ) {
        return true;
     }else{
        return false;
     }
   }

   # check company code if exist
   function check_company_code_exist( $code ){
      $this->db->select('c.id')
         ->from('company AS c')
         ->join('subscribtion_payment_history AS sp', 'c.id=sp.company_id', 'inner')
         ->where('c.code', $code)
         ->where('sp.payment_status', 'process');
         $this->db->group_start()
            ->where('c.payment_process', 'true')
            ->or_where('c.verified', 'unverified')
            ->group_end();
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   function gen_order_id(){
      $feedBack = false;
		$rand = '';
		do {
			$rand = rand(0, 999999999);
			$q = $this->db->select('id')
				->from('subscribtion_payment_history')
				->where('order_id', $rand)
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
   }

   function check_renew_subscribtion( $code ){
      $this->db->select('c.id, c.code, c.name, c.whatsapp_number, c.telp, c.email, c.start_date_subscribtion, c.end_date_subscribtion')
         ->from('company AS c')
         ->where('c.payment_process', 'true')
         ->where('c.code', $code);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         $row = $q->row();
         $list['company_id'] = $row->id;
         $list['code'] = $row->code;
         $list['name'] = $row->name;
         $list['whatsapp_number'] = $row->whatsapp_number;
         $list['telp'] = $row->telp;
         $list['email'] = $row->email;
         // $list['duration'] = $row->duration;
         // $list['pay_per_month'] = $row->pay_per_month;
         // $list['total'] = $row->total;
         $list['start_date_subscribtion'] = $row->start_date_subscribtion;
         $list['end_date_subscribtion'] = $row->end_date_subscribtion;
      }
      return $list;
   }
}
