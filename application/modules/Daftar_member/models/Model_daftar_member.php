<?php

/**
 *  -----------------------
 *	Model daftar member
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_member extends CI_Model
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

   function get_total_daftar_member($search)
   {
      $this->db->select('personal_id')
         ->from('personal')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('fullname', $search)
            ->or_like('identity_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_member($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('p.personal_id, p.fullname, p.identity_number, p.gender, p.photo, p.birth_place,
                         p.birth_date, p.address, p.email, p.nomor_whatsapp,
                         (SELECT GROUP_CONCAT( user_id SEPARATOR \';\')
                            FROM base_users
                            WHERE personal_id=p.personal_id) AS userExist,
                         (SELECT GROUP_CONCAT( id SEPARATOR \';\')
                            FROM agen
                            WHERE company_id="' . $this->company_id . '" AND
                            personal_id=p.personal_id) AS agenExist,
                         (SELECT GROUP_CONCAT( id SEPARATOR \';\')
                            FROM jamaah
                            WHERE company_id="' . $this->company_id . '" AND
                            personal_id=p.personal_id) AS jamaahExist,
                         (SELECT GROUP_CONCAT( id SEPARATOR \';\')
                            FROM muthawif
                            WHERE company_id="' . $this->company_id . '" AND
                            personal_id=p.personal_id) AS muthawifExist')
         ->from('personal AS p')
         ->where('p.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $this->db->order_by('p.personal_id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {

            if ($row->photo != '') {
               $src = FCPATH . 'image/personal/' . $row->photo;
               if (file_exists($src)) {
                  $photo = $row->photo;
               } else {
                  $photo = 'default.png';
               }
            } else {
               $photo = 'default.png';
            }

            $register_as = array();

            if ($row->userExist != '') {
               $register_as[] = 'User';
            }
            if ($row->agenExist != '') {
               $register_as[] = 'Agen';
            }
            if ($row->jamaahExist != '') {
               $register_as[] = 'Jamaah';
            }
            if ($row->muthawifExist != '') {
               $register_as[] = 'Muthawif';
            }

            $list[] = array(
               'id' => $row->personal_id,
               'fullname' => $row->fullname,
               'identity_number' => $row->identity_number,
               'gender' => $row->gender == 0 ? 'Laki-laki' : 'Perempuan',
               'photo' => $photo,
               'register_as' => $register_as,
               'birth_place' => $row->birth_place,
               'birth_date' => $this->date_ops->change_date_t3($row->birth_date),
               'address' => $row->address,
               'email' => $row->email == '' ? '-' : $row->email,
               'nomor_whatsapp' => $row->nomor_whatsapp == '' ? '-' : $row->nomor_whatsapp
            );
         }
      }
      return $list;
   }

   function check_id_member_exist($id)
   {
      $this->db->select('personal_id')
         ->from('personal')
         ->where('company_id', $this->company_id)
         ->where('personal_id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   // get photo name
   function get_name_photo($id)
   {
      $this->db->select('photo')
         ->from('personal')
         ->where('company_id', $this->company_id)
         ->where('personal_id', $id);
      $q = $this->db->get();
      $photo = '';
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $photo = $rows->photo;
         }
      }
      return $photo != '' ? $photo : md5(date('Y-m-d H:i:s'));
   }

   function get_info_member($id)
   {
      $this->db->select('personal_id, fullname, identity_number, gender,
                         photo, birth_place, birth_date, address, account_name, 
                         number_account, bank_id, email, nomor_whatsapp')
         ->from('personal')
         ->where('company_id', $this->company_id)
         ->where('personal_id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $feedBack['id'] = $rows->personal_id;
            $feedBack['nama'] = $rows->fullname;
            $feedBack['no_identitas'] = $rows->identity_number;
            $feedBack['gender'] = $rows->gender;
            $feedBack['photo'] = $rows->photo;
            $feedBack['birth_place'] = $rows->birth_place;
            $feedBack['birth_date'] = $rows->birth_date;
            $feedBack['alamat'] = $rows->address;
            $feedBack['email'] = $rows->email;
            $feedBack['nomor_whatsapp'] = $rows->nomor_whatsapp;
            $feedBack['account_name'] = $rows->account_name;
            $feedBack['number_account'] = $rows->number_account;
            $feedBack['bank_id'] = $rows->bank_id;
         }
      }
      return $feedBack;
   }

   function check_other_level_akun_exist($id)
   {
      $this->db->select('p.personal_id,
                        (SELECT GROUP_CONCAT( user_id SEPARATOR \';\')
                           FROM base_users
                           WHERE personal_id=p.personal_id) AS userExist,
                        (SELECT GROUP_CONCAT( id SEPARATOR \';\')
                           FROM agen
                           WHERE company_id="' . $this->company_id . '" AND
                           personal_id=p.personal_id) AS agenExist,
                        (SELECT GROUP_CONCAT( id SEPARATOR \';\')
                           FROM jamaah
                           WHERE company_id="' . $this->company_id . '" AND
                           personal_id=p.personal_id) AS jamaahExist,
                        (SELECT GROUP_CONCAT( id SEPARATOR \';\')
                           FROM muthawif
                           WHERE company_id="' . $this->company_id . '" AND
                           personal_id=p.personal_id) AS muthawifExist')
         ->from('personal AS p')
         ->where('company_id', $this->company_id)
         ->where('personal_id', $id);
      $q = $this->db->get();
      $error_msg = array();
      //$as = '';
      if ($q->num_rows() > 0) {
         $msg = '';
         foreach ($q->result() as $rows) {

            if ($rows->userExist != '') {
               $error_msg[] = 'User';
            }
            if ($rows->agenExist != '') {
               $error_msg[] = 'Agen';
            }
            if ($rows->jamaahExist != '') {
               $error_msg[] = 'Jamaah';
            }
            if ($rows->muthawifExist != '') {
               $error_msg[] = 'Muthawif';
            }

            if (count($error_msg) > 0) {
               // $msg .= ' member masih terdaftar sebagai :';
               foreach ($error_msg as $key => $value) {
                  if ($key != 0) {
                     $msg .= ' ,<b>' . $value . '</b>';
                  } else {
                     $msg .= ' <b>' . $value . '</b>';
                  }
               }
            }
         }

         if (count($error_msg) > 0) {
            return array('error' => true, 'error_msg' => ' member masih terdaftar sebagai :' . $msg, 'as' => $msg);
         } else {
            return array('error' => false, 'error_msg' => 'Akun member dapat langsung dihapus.');
         }
      } else {
         return array('error' => true, 'error_msg' => 'Akun personal tidak ditemukans.');
      }
   }

   # get agen
   function get_agen()
   {
      $this->db->select('a.id, lk.nama, p.fullname')
         ->from('agen AS a')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->join('level_keagenan AS lk', 'a.level_agen_id=lk.id', 'inner')
         ->where('a.company_id', $this->company_id);
      // ->where('a.id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array(
               'id' => $rows->id,
               'level_agen' => $rows->nama,
               'fullname' => $rows->fullname
            );
         }
      }
      return $list;
   }

   # fullname
   function get_name_member($id)
   {
      $this->db->select('fullname, identity_number')
         ->from('personal')
         ->where('company_id', $this->company_id)
         ->where('personal_id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $feedBack['fullname'] = $rows->fullname;
            $feedBack['identity_number'] = $rows->identity_number;
         }
      }
      return $feedBack;
   }

   function check_uplink_id_exist($id)
   {
      $this->db->select('id')
         ->from('agen')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # check nomor whatsapp exist
   function check_no_whatsapp_exist( $nomor_whatsapp, $id = '' ){
      $this->db->select('personal_id')
         ->from('personal')
         ->where('company_id', $this->company_id)
         ->where('nomor_whatsapp', $nomor_whatsapp);
      if( $id != '' ){
         $this->db->where('personal_id !=', $id);
      }
      $q = $this->db->get();
      if( $q->num_rows() >  0){
         return true;
      }else{
         return false;
      }
   }

   # get level keagenan
   function get_level_keagenan(){
      $this->db->select('id, nama')
         ->from('level_keagenan')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0  )   {
         foreach ($q->result() as $rows) {
            $list[$rows->id] = $rows->nama;
         }
      }
      return $list;
   }

   # check level keagenan i
   function check_level_keagenan_axist($level_id){
      $this->db->select('id')
         ->from('level_keagenan')
         ->where('company_id', $this->company_id)
         ->where('id', $level_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   // list bank transfer
   function get_list_bank(){
      $this->db->select('id, nama_bank')
         ->from('mst_bank_transfer');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows ) {
            $list[$rows->id] = $rows->nama_bank; 
         }
      }
      return $list;
   }

   function check_list_bank_id($id){
      $this->db->select('id')
               ->from('mst_bank_transfer')
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }
}
