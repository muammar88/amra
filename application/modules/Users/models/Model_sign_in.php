<?php
/**
*  -----------------------
*	Model sign in
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_sign_in extends CI_Model
{
   private $CI;
   private $krs;

   public function __construct()
   {
      parent::__construct();
      $this->CI = &get_instance();
      $this->CI->load->model('Model_sign_in_cud', 'model_sign_in_cud');
      $this->krs = array('rupiah' => 'Rp', 'dollar' => '$', 'riyal' => 'SAR');
   }

   	# generated kode akun bank
   public function generated_kode_akun_bank(){
      $this->db->select('nomor_akun_secondary, path')
         ->from('akun_secondary')
         ->like('path','bank')
         ->where('company_id', $this->session->userdata($this->config->item('apps_name'))['company_id']);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         $main_bank_akun = 0;
         $secondary_bank_akun =array();
         foreach ($q->result() as $row) {
            if( $row->path == 'bank' ){
               $main_bank_akun = $row->nomor_akun_secondary;
            }else{
               $secondary_bank_akun[] = $row->nomor_akun_secondary;
            }
         }
         $looping = true;
         $new_akun_bank = $main_bank_akun;
         while($looping){
            $new_akun_bank++;
            if( ! in_array($new_akun_bank, $secondary_bank_akun) ){
               $looping = false;
            }
         }
         return $new_akun_bank;
      }else{
         return '11021';
      }
   }

	// generate nomor akun deposit
   public function generated_nomor_akun_airlines_deposit(){
		// $CI =& get_instance();
      $this->db->select('nomor_akun_secondary, path')
				         ->from('akun_secondary')
				         ->like('path','airlines:deposit:')
				         ->where('company_id', $this->session->userdata($this->config->item('apps_name'))['company_id']);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         $main_airlines_akun = 12000;
         $secondary_airlines_akun =array();
         foreach ($q->result() as $row) {
            $secondary_airlines_akun[] = $row->nomor_akun_secondary;
         }
         $looping = true;
         $new_akun_airlines = $main_airlines_akun;
         while($looping){
            $new_akun_airlines++;
            if( ! in_array($new_akun_airlines, $secondary_airlines_akun) ){
               $looping = false;
            }
         }
         return $new_akun_airlines;
      }else{
         return '12001';
      }
   }

	// generated nomor akun airlines pendapatan
   public function generated_nomor_akun_airlines_pendapatan(){
      $this->db->select('nomor_akun_secondary, path')
         ->from('akun_secondary')
         ->like('path','airlines:pendapatan:')
         ->where('company_id', $this->session->userdata($this->config->item('apps_name'))['company_id']);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         $main_airlines_akun = 42000;
         $secondary_airlines_akun =array();
         foreach ($q->result() as $row) {
            $secondary_airlines_akun[] = $row->nomor_akun_secondary;
         }
         $looping = true;
         $new_akun_airlines = $main_airlines_akun;
         while($looping){
            $new_akun_airlines++;
            if( ! in_array($new_akun_airlines, $secondary_airlines_akun) ){
               $looping = false;
            }
         }
         return $new_akun_airlines;
      }else{
         return '42001';
      }
   }

	// generated nomor akub airlines hpp
	function generated_nomor_akun_airlines_hpp(){
      $this->db->select('nomor_akun_secondary, path')
         ->from('akun_secondary')
         ->like('path','airlines:hpp:')
         ->where('company_id', $this->session->userdata($this->config->item('apps_name'))['company_id']);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         $main_airlines_akun = 51000;
         $secondary_airlines_akun =array();
         foreach ($q->result() as $row) {
            $secondary_airlines_akun[] = $row->nomor_akun_secondary;
         }
         $looping = true;
         $new_akun_airlines = $main_airlines_akun;
         while($looping){
            $new_akun_airlines++;
            if( ! in_array($new_akun_airlines, $secondary_airlines_akun) ){
               $looping = false;
            }
         }
         return $new_akun_airlines;
      }else{
         return '51001';
      }
   }

   function _get_info_company($company_id){
      $this->db->select('*')
         ->from('company')
         ->where('id', $company_id);
      $q = $this->db->get();
      $list = array();
      foreach( $q->result() as $rows ) {
         $list[] = $rows->code;
      }
   }

   function _subscribtion_tab_access( $company_id ){
      $this->db->select('id, tab_id, start_date_subscribtion, end_date_subscribtion')
         ->from('subscribtion_tab_access')
         ->where('company_id', $company_id)
         ->where('end_date_subscribtion >=', date('Y-m-d'));
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0){
         foreach ($q->result() as $rows) {
            $list[] = $rows->tab_id;
         }
      }
      return $list;
   }

   /**
    * get all modul access
    */
   public function modul_access( $type, $level, $data_company, $group_access = '' ) {
      # filter
      $modul_access = array();
      $submodul_access = array();
      if( $group_access != '' ) {
         $list = unserialize($group_access);
         $modul_access = $list['modul'];
         $submodul_access = $list['submodul'];
      }

      $subscribtion_tab = $this->_subscribtion_tab_access( $data_company['id'] );

      // get all tab
      $this->db->select('id, name, icon, path, description, subscibtion_type')
               ->from('base_tab');
      $s = $this->db->get();
      $tab = array();
      if( $s->num_rows() > 0 ){
         foreach ($s->result() as $rows) {
            if( $type != 'unlimited' ) {
              if( $rows->subscibtion_type == 'default' ) {
                 $tab[$rows->id] = array('name' => $rows->name, 'icon' => $rows->icon, 'path' => $rows->path, 'description' => $rows->description);
              }else{
                 if( in_array( $rows->id, $subscribtion_tab ) ) {
                    $tab[$rows->id] = array('name' => $rows->name, 'icon' => $rows->icon, 'path' => $rows->path, 'description' => $rows->description);
                 }
              }
            }else{
               $tab[$rows->id] = array('name' => $rows->name, 'icon' => $rows->icon, 'path' => $rows->path, 'description' => $rows->description);
            }
         }
      }

      // get modul
      $this->db->select('modul_id, modul_name, modul_path, modul_icon, tab')
         ->from('base_modules');
      if( $level == 'staff') {
         $this->db->where('modul_id != ', '8');
      }
      # filter group access
      if( $group_access != '' ) {
         if( count($modul_access) > 0 ) {
            $this->db->where_in('modul_id', $modul_access);
         }
      }
      $q = $this->db->get();
      $modul_access = array();
      if( $q->num_rows() > 0 ) {
         $i = 1;
         foreach ($q->result() as $rowq) {
            // tab module
            if( $rowq->tab != '' ) {
               $modul_tab = array();
               foreach ( unserialize( $rowq->tab ) as $key => $value ) {
                  if( $type != 'unlimited' ) {
                     if ( array_key_exists( $value, $tab ) ) {
                        $modul_tab[] = array('name' => $tab[$value]['name'],
                                             'icon' => $tab[$value]['icon'],
                                             'path' => $tab[$value]['path'],
                                             'description' => $tab[$value]['description']);
                     }
                  }else{
                     $modul_tab[] = array('name' => $tab[$value]['name'],
                                          'icon' => $tab[$value]['icon'],
                                          'path' => $tab[$value]['path'],
                                          'description' => $tab[$value]['description']);
                  }
               }
               # filter module
               if( count($modul_tab) > 0  ) {
                  $modul_access[ $rowq->modul_id ]['modul_id'] = $rowq->modul_id;
                  $modul_access[ $rowq->modul_id ]['modul_name'] = $rowq->modul_name;
                  $modul_access[ $rowq->modul_id ]['modul_path'] = $rowq->modul_path;
                  $modul_access[ $rowq->modul_id ]['modul_icon'] = $rowq->modul_icon;
                  $modul_access[ $rowq->modul_id ]['tab'] = $modul_tab;
               }
            }elseif (  $rowq->tab == '' && $rowq->modul_path == '#' ) {
               $this->db->select('submodules_id, submodules_name, submodules_path, tab')
                        ->from('base_submodules')
                        ->where('modul_id', $rowq->modul_id);
               if( $level != 'administrator' ) {
                  $this->db->where('submodules_id !=', '55');
               }         
               if( $group_access != '' ) {
                  if( count( $submodul_access ) > 0 ) {
                     $this->db->where_in('submodules_id', $submodul_access);
                  }
               }
               $r = $this->db->get();
               $submodul = array();
               if( $r->num_rows() > 0 ) {
                  foreach ( $r->result() as $rowr ) {
                     if( $rowr->tab != '' ) {
                        $submodul_tab = array();
                        $tabUn = unserialize( $rowr->tab );
                        foreach ( $tabUn  as $key_sub_tab => $value_sub_tab ) {
                           if( $type != 'unlimited' ) {
                              if ( array_key_exists($value_sub_tab,$tab) ) {
                                 $submodul_tab[] = array('name' => $tab[$value_sub_tab]['name'],
                                                       'icon' => $tab[$value_sub_tab]['icon'],
                                                       'path' => $tab[$value_sub_tab]['path'],
                                                       'description' => $tab[$value_sub_tab]['description']);
                              }
                           }else{
                              $submodul_tab[] = array('name' => $tab[$value_sub_tab]['name'],
                                                    'icon' => $tab[$value_sub_tab]['icon'],
                                                    'path' => $tab[$value_sub_tab]['path'],
                                                    'description' => $tab[$value_sub_tab]['description']);
                           }
                        }
                        if( count($submodul_tab) > 0  ){
                           $submodul[ $rowr->submodules_id ]['submodul_id'] = $rowr->submodules_id;
                           $submodul[ $rowr->submodules_id ]['submodules_name'] = $rowr->submodules_name;
                           $submodul[ $rowr->submodules_id ]['submodules_path'] = $rowr->submodules_path;
                           $submodul[ $rowr->submodules_id ]['tab'] = $submodul_tab;
                        }
                     }
                  }
                  # filter submodule
                  if( count($submodul) > 0  ){
                     $modul_access[ $rowq->modul_id ]['modul_id'] = $rowq->modul_id;
                     $modul_access[ $rowq->modul_id ]['modul_name'] = $rowq->modul_name;
                     $modul_access[ $rowq->modul_id ]['modul_path'] = $rowq->modul_path;
                     $modul_access[ $rowq->modul_id ]['modul_icon'] = $rowq->modul_icon;
                     $modul_access[ $rowq->modul_id ]['submodul'] = $submodul;
                  }
               }
            }
         }
      }
      return $modul_access;
   }

   function _unlimited_user_access( $data_company, $kurs ){
      $modul_access = $this->modul_access('unlimited', 'administrator', $data_company);
      ksort( $modul_access );
      # check photo
      $src = FCPATH . 'image/company/' . $data_company['photo_profil'];
      $photo = 'personal/avatar.svg';
      if( $data_company['photo_profil'] != '' ) {
         if ( file_exists( $src ) ) {
            $photo = 'company/'. $data_company['photo_profil'];
         }
      }
      # define session variable
      return array('Is_login' => true,
                     'kurs' => $this->krs[$kurs], 
                     'company_type' => 'unlimited',
                     'verified' => 'verified',
                     'start_date_subscribtion' => 'unlimited',
                     'end_date_subscribtion' => 'unlimited',
                     'company_id' => $data_company['id'],
                     'company_code' => $data_company['code'],
                     'company_name' => $data_company['name'],
                     'logo' => $data_company['logo'] != '' ? $data_company['logo'] : 'logo.svg' ,
                     'icon' => $data_company['icon'] != '' ? 'company/icon/'.$data_company['icon'] : 'icon.ico' ,
                     'photo' => $photo,
                     'email' => $data_company['email'],
                     'level_akun' => 'administrator',
                     'modul_access' => $modul_access);
   }

   function _limited_user_access( $data_company, $kurs ){
      // generated array
      $modul_access = $this->modul_access('limited', 'administrator', $data_company);
      ksort( $modul_access );
      # check photo
      $src = FCPATH . 'image/company/' . $data_company['photo_profil'];
      $photo = 'personal/avatar.svg';
      if( $data_company['photo_profil'] != '' ) {
         if ( file_exists($src) ) {
            $photo = 'company/'. $data_company['photo_profil'];
         }
      }
      //
      return array('Is_login' => true,
                   'kurs' => $this->krs[$kurs], 
                   'company_type' => 'limited',
                   'verified' => 'verified',
                   'start_date_subscribtion' => $data_company['start_date_subscribtion'],
                   'end_date_subscribtion' => $data_company['end_date_subscribtion'],
                   'company_id' => $data_company['id'],
                   'company_code' => $data_company['code'],
                   'company_name' => $data_company['name'],
                   'logo' => $data_company['logo'] != '' ? $data_company['logo'] : 'logo.svg' ,
                   'icon' => $data_company['icon'] != '' ? 'company/icon/'.$data_company['icon'] : 'icon.ico' ,
                   'photo' => $photo,
                   'email' => $data_company['email'],
                   'level_akun' => 'administrator',
                   'modul_access' => $modul_access);
   }

   function _unlimited_staff_access( $data_staff, $kurs ){

      // $krs = array('rupiah' => 'Rp', 'dollar' => '$', 'riyal' => 'SAR');

      # get module access
      $modul_access = $this->modul_access('unlimited', 'staff', $data_staff, $data_staff['group_access'] );
      ksort( $modul_access );
      # photo
      $photo = 'personal/avatar.svg';
      $src = FCPATH . 'image/personal/' . $data_staff['photo'];
      if( $data_staff['photo'] != '' ) {
         if ( file_exists($src) ) {
				$photo = 'personal/'. $data_staff['photo'];
			}
      }
      # feedBack
      return array('Is_login' => true,
                   'kurs' => $this->krs[$kurs], 
                   'company_type' => 'unlimited',
                   'verified' => 'verified',
                   'start_date_subscribtion' => 'unlimited',
                   'end_date_subscribtion' => 'unlimited',
                   'company_id' => $data_staff['id'],
                   'company_code' => $data_staff['code'],
                   'company_name' => $data_staff['name'],
                   'logo' => $data_staff['logo'] != '' ? $data_staff['logo'] : 'logo.svg' ,
                   'icon' => $data_staff['icon'] != '' ? 'company/icon/'.$data_staff['icon'] : 'icon.ico' ,
                   'photo' => $photo,
                   'user_id' => $data_staff['user_id'],
                   'nomor_whatsapp' => $data_staff['nomor_whatsapp'],
                   'fullname' => $data_staff['fullname'],
                   'level_akun' => 'staff',
                   'modul_access' => $modul_access);
   }

   function _limited_staff_access( $data_staff, $kurs ){
      # get module access
     $modul_access = $this->modul_access('limited', 'staff', $data_staff, $data_staff['group_access'] );
     ksort( $modul_access );
     # photo
     $photo = 'personal/avatar.svg';
     $src = FCPATH . 'image/personal/' . $data_staff['photo'];
     if( $data_staff['photo'] != '' ) {
        if ( file_exists($src) ) {
           $photo = 'personal/'. $data_staff['photo'];
        }
     }
     # feedBack
     return array('Is_login' => true,
                  'kurs' => $this->krs[$kurs], 
                  'company_type' => 'limited',
                  'verified' => 'verified',
                  'start_date_subscribtion' => $data_staff['start_date_subscribtion'],
                  'end_date_subscribtion' => $data_staff['end_date_subscribtion'],
                  'company_id' => $data_staff['id'],
                  'company_code' => $data_staff['code'],
                  'company_name' => $data_staff['name'],
                  'logo' => $data_staff['logo'] != '' ? $data_staff['logo'] : 'logo.svg' ,
                  'icon' => $data_staff['icon'] != '' ? 'company/icon/'.$data_staff['icon'] : 'icon.ico' ,
                  'photo' => $photo,
                  'user_id' => $data_staff['user_id'],
                  'nomor_whatsapp' => $data_staff['nomor_whatsapp'],
                  'fullname' => $data_staff['fullname'],
                  'level_akun' => 'staff',
                  'modul_access' => $modul_access);
   }

   # check username password
   public function username_password_authentication( $level_akun, $userNameArray, $password, $code = '' ) {
      $error = 0;
      $error_msg = '';
      $active = true;
      $company_code = '';
      $kurs = 'rupiah';
      $feedBack = array();
      if( $level_akun == 'administrator' ) {
         $this->db->select('id, code, logo, icon, name, email, password, photo_profil, company_type, verified, kurs,
                            start_date_subscribtion, end_date_subscribtion')
            ->from('company')
            ->where('email', $userNameArray['email'] )
            ->where('verified', 'verified');
            if( $code != ''){
              $this->db->where('code', $code);
            }
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            $rows = $q->row();
            $company_code = $rows->code;
            $kurs = $rows->kurs;
            if( $rows->end_date_subscribtion < date('Y-m-d') AND $rows->company_type == 'limited' ) {
               $active = false;
               $error = 1;
               $error_msg = 'Masa berlangganan akun anda sudah berakhir. Selahkan perpanjang masa berlangganan akun anda untuk mulai menggunakan kembali aplikasi AMRA';
            }else{
               if ( password_verify( $password . '_' . $this->systems->getSalt(), $rows->password ) ) {
                  $file_name = $q->list_fields();
                  $data_company = array();
                  foreach ($file_name as $key => $value) {
                     $data_company[$value] = $rows->$value;
                  }
                  if( $rows->company_type == 'unlimited' ) {
                     $feedBack = $this->_unlimited_user_access( $data_company, $kurs );
                  } else {
                     // limited
                     $feedBack = $this->_limited_user_access( $data_company, $kurs );
                  }
               }else{
                  $error = 1;
                  $error_msg = 'Verifikasi gagal dilakukan.';
               }
            }
         }else{
            $error = 1;
            $error_msg = 'Akun perusahaan tidak terdaftar dipangkalan data.';
         }
      } elseif ( $level_akun == 'staff' ) {
         $this->db->select('u.user_id,
                            p.password, p.photo, p.fullname, p.nomor_whatsapp,
                            c.id, c.code, c.name, c.email, c.company_type, c.logo, c.icon, c.kurs,
                            c.verified, c.start_date_subscribtion, c.end_date_subscribtion,
                            g.group_access')
            ->from('base_users AS u')
            ->join('base_groups AS g', 'u.group_id=g.group_id', 'inner')
            ->join('company AS c', 'u.company_id=c.id', 'inner')
            ->join('personal AS p', 'u.personal_id=p.personal_id', 'inner')
            ->where('p.personal_id', $userNameArray['personal_id'])
            ->where('c.verified', 'verified');
            if( $code != ''){
              $this->db->where('code', $code);
            }
         $q = $this->db->get();
         if( $q->num_rows() > 0  ) {
            $rows = $q->row();
            $company_code = $rows->code;
            $kurs = $rows->kurs;
            if( $rows->end_date_subscribtion < date('Y-m-d') AND $rows->company_type == 'limited' ) {
               $active = false;
               $error = 1;
               $error_msg = 'Masa berlangganan akun perusahaan ini sudah berakhir. Anda kembali dapat menggunakan aplikasi AMRA setelah masa berlangganan sudah diperpanjang.';
            }else{
               if ( password_verify( $password . '_' . $this->systems->getSalt(), $rows->password ) ) {
                  $file_name = $q->list_fields();
                  $data_staff = array();
                  foreach ($file_name as $key => $value) {
                     $data_staff[$value] = $rows->$value;
                  }
                  if( $rows->company_type == 'unlimited' ){
                     $feedBack = $this->_unlimited_staff_access( $data_staff, $kurs );
                  }elseif ( $rows->company_type == 'limited' ) {
                     $feedBack = $this->_limited_staff_access( $data_staff, $kurs );
                  }
               }else{
                  $error = 1;
                  $error_msg = 'Verifikasi gagal dilakukan.';
               }
            }
         }else{
            $error = 1;
            $error_msg = 'Akun staff tidak terdaftar dipangkalan data.';
         }
      }
      # define set userdata
      return array('error' => $error,
                   'error_msg' => $error_msg,
                   'active' => $active,
                   'company_code' => $company_code,
                   'kurs' => $kurs,
                   'feedBack' => $feedBack);
   }

   # input data bawaan
   function input_data_bawaan($company_id){
      // get akun primary
      $this->db->select('id, nomor_akun, nama_akun')
               ->from('default_akun_primary');
      $q = $this->db->get();
      $data_akun_primary = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $row) {
            $temp_data = array();
            $temp_data['company_id'] = $company_id;
            $temp_data['nomor_akun'] = $row->nomor_akun;
            $temp_data['nama_akun'] = $row->nama_akun;
            # get data secondary
            $this->db->select('nomor_akun_secondary, nama_akun_secondary, tipe_akun, path')
                     ->from('default_akun_secondary')
                     ->where('akun_primary_id', $row->id);
            $r = $this->db->get();
            $data_akun_secondary = array();
            if( $r->num_rows() > 0 ){
               foreach ($r->result() as $rowr) {
                  $temp_data_secondary = array();
                  $temp_data_secondary['company_id'] = $company_id;
                  $temp_data_secondary['nomor_akun_secondary'] = $rowr->nomor_akun_secondary;
                  $temp_data_secondary['nama_akun_secondary'] = $rowr->nama_akun_secondary;
                  $temp_data_secondary['tipe_akun'] = $rowr->tipe_akun;
                  $temp_data_secondary['path'] = $rowr->path;
                  # data akun secondary
                  $data_akun_secondary[] = $temp_data_secondary;
               }
            }
            $temp_data['akun_secondary'] = $data_akun_secondary;
            $data_akun_primary[] = $temp_data;
         }
      }
      # INSERT PROSES
      $this->db->trans_start();
      # insert
      foreach ($data_akun_primary as $key => $value) {
         $data = array();
         $data['company_id'] = $value['company_id'];
         $data['nomor_akun'] = $value['nomor_akun'];
         $data['nama_akun'] = $value['nama_akun'];
         # insert process
         $this->db->insert('akun_primary', $data);
         # get id
         $akun_primary_id = $this->db->insert_id();
         foreach ($value['akun_secondary'] as $key_secondary => $value_secondary) {
            $data_secondary = array();
            $data_secondary['company_id'] = $value_secondary['company_id'];
            $data_secondary['akun_primary_id'] = $akun_primary_id;
            $data_secondary['nomor_akun_secondary'] = $value_secondary['nomor_akun_secondary'];
            $data_secondary['nama_akun_secondary'] = $value_secondary['nama_akun_secondary'];
            $data_secondary['tipe_akun'] = $value_secondary['tipe_akun'];
            $data_secondary['path'] = $value_secondary['path'];
            # insert process
            $this->db->insert('akun_secondary', $data_secondary);
         }
      }
      # inser process mst airlines
      $this->db->insert('mst_airlines', array('company_id' => $company_id,
                                              'airlines_name' => "GARUDA INDONESIA",
                                              'input_date' => date('Y-m-d H:i:s'),
                                              'last_update' => date('Y-m-d H:i:s')));
      $airlines_id = $this->db->insert_id(); # get id
      # akun
      $data_akun_airlines = array();
      # deposit
      $data_akun_airlines['deposit'] = array('akun_primary_id' => '1',
                                             'company_id' => $company_id,
                                             'nomor_akun_secondary' => $this->generated_nomor_akun_airlines_deposit(),
                                             'nama_akun_secondary' => 'DEPOSIT '.strtoupper("GARUDA INDONESIA"),
                                             'tipe_akun' => 'bawaan',
                                             'path' => 'airlines:deposit:'.$airlines_id);  // harus ditambah id
      // akun pendapatan
      $data_akun_airlines['pendapatan'] = array('akun_primary_id' => '4',
                                                'company_id' => $company_id,
                                                'nomor_akun_secondary' => $this->generated_nomor_akun_airlines_pendapatan(),
                                                'nama_akun_secondary' => 'PENDAPATAN '.strtoupper("GARUDA INDONESIA"),
                                                'tipe_akun' => 'bawaan',
                                                'path' => 'airlines:pendapatan:'.$airlines_id);  // harus ditambah id
      // akun hpp
      $data_akun_airlines['hpp'] = array('akun_primary_id' => '5',
                                         'company_id' => $company_id,
                                         'nomor_akun_secondary' => $this->generated_nomor_akun_airlines_hpp(),
                                         'nama_akun_secondary' => 'HPP '.strtoupper("GARUDA INDONESIA"),
                                         'tipe_akun' => 'bawaan',
                                         'path' => 'airlines:hpp:'.$airlines_id); // harus ditambah id
      # insert process
      foreach ($data_akun_airlines as $key => $value) {
         $this->db->insert('akun_secondary', $value);
      }
      # insert mst city
      $this->db->insert('mst_city', array('company_id' => $company_id,
                                          'city_name' => 'Jeddah',
                                          'city_code' => 'JED',
                                          'input_date' => date('Y-m-d H:i:s'),
                                          'last_update' => date('Y-m-d H:i:s')));
      $city_id = $this->db->insert_id(); # get id
      # insert mst city
      $this->db->insert('mst_hotel', array('company_id' => $company_id,
                                          'hotel_name' => 'Darut Tauhid',
                                          'description' => '',
                                          'star_hotel' => 5,
                                          'city_id' => $city_id,
                                          'input_date' => date("Y-m-d H:i:s"),
                                          'last_update' => date("Y-m-d H:i:s")));
      # insert mst bank
      $kode_bank = $this->random_code_ops->rand_bank_code();
      $this->db->insert('mst_bank', array('company_id' => $company_id,
                                          'kode_bank' => $kode_bank ,
                                          'nama_bank' => 'BSI',
                                          'input_date' => date('Y-m-d'),
                                          'last_update' => date('Y-m-d')));
      # insert akun bank
      $this->db->insert('akun_secondary', array('company_id' => $company_id,
                                                'akun_primary_id' => '1',
                                                'nomor_akun_secondary' => $this->generated_kode_akun_bank(),
                                                'nama_akun_secondary' => strtoupper("BSI"),
                                                'tipe_akun' => 'bawaan',
                                                'path' => 'bank:kodebank:'.$kode_bank));
      # insert mst airport
      $this->db->insert('mst_airport', array('company_id' => $company_id,
                                             'city_id' => $city_id,
                                             'airport_name' => 'Jeddah Airport'));
      # insert mst facilities
      $this->db->insert('mst_facilities', array('company_id' => $company_id,
                                                'facilities_name' => 'Tas',
                                                'input_date' =>  date('Y-m-d H:i:s'),
                                                'last_update' =>  date('Y-m-d H:i:s')));
      # insert mst paket type
      $this->db->insert('mst_paket_type', array('company_id' => $company_id,
                                                'paket_type_name' => 'Normal',
                                                'input_date' =>  date('Y-m-d H:i:s'),
                                                'last_update' =>  date('Y-m-d H:i:s')));
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ( $this->db->trans_status() === FALSE )
      {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         return FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         return TRUE;
      }
   }

   // check exist company code
   function checkExistCompanyCode( $company_code ){
      $this->db->select('id')
         ->from('company')
         ->where('code', $company_code);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # check verified company
   function check_verified_company( $email ){
      $this->db->select('id')
         ->from('company')
         ->where('email', $email)
         ->where('verified', 'verified');
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # check email exist
   function check_email_exist( $email ){
      $this->db->select('id')
         ->from('company')
         ->where('email', $email);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function check_verification($email){
      $this->db->select('id')
         ->from('company')
         ->where('email', $email)
         ->where('verified', 'verified');
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function get_wa( $email ){
      $this->db->select('whatsapp_number')
         ->from('company')
         ->where('email', $email)
         ->where('verified', 'unverified');
      $q = $this->db->get();
      $wa = 0;
      if( $q->num_rows() > 0 ){
         $wa = $q->row()->whatsapp_number;
      }
      return $wa;
   }


   function gen_otp_staff($personal_id){
      $feedBack = false;
      $rand = '';
      do {
         $rand = rand(0, 999999);
         $q = $this->db->select('personal_id')
                       ->from('personal AS p')
                       ->where('p.personal_id', $personal_id)
                       ->where('p.otp', $rand)
                       ->get();
         if ($q->num_rows() == 0) {
            $feedBack = true;
         }
      } while ($feedBack == false);

      $date = strtotime(date('Y-m-d H:i:s'));
      $date = strtotime("+3 minute", $date);
      $otp_expire =  date('Y-m-d H:i:s', $date);

      $this->db->where('personal_id', $personal_id)
               ->update('personal', array('otp' => $rand, 'otp_expire' => $otp_expire ) );

      return $rand;
   }

   function gen_otp( $email ) {
      $feedBack = false;
      $rand = '';
      do {
         $rand = rand(0, 999999);
         $q = $this->db->select('id')
            ->from('company')
            ->where('email', $email)
            ->where('otp', $rand)
            ->get();
         if ($q->num_rows() == 0) {
            $feedBack = true;
         }
      } while ($feedBack == false);

      # get exp time
      // $dateTime = new DateTime(date('Y-m-d H:i:s'));
      // $otp_expire = $dateTime->modify('+3 minutes');

      $date = strtotime(date('Y-m-d H:i:s'));
      $date = strtotime("+3 minute", $date);
      $otp_expire =  date('Y-m-d H:i:s', $date);

      // echo $otp_expire;
      // $this->db->update('company', array('otp' => $rand, 'otp_expire' => $otp_expire ));
      $this->db->where('email', $email)->update('company', array('otp' => $rand, 'otp_expire' => $otp_expire ) );

      return $rand;
   }

   function check_token_is_valid( $token, $email ){
      $this->db->select('id, otp_expire')
         ->from('company')
         ->where('email', $email)
         ->where('otp', $token)
         ->where('verified', 'unverified');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ( $q->result() as $rows ) {
            if( $rows->otp_expire > date('Y-m-d H:i:s') ) {
               $list['error'] = false;
               $list['error_msg'] = 'ditemukan';
            }else{
               $list['error'] = true;
               $list['error_msg'] = 'OTP sudah expired';
            }
         }
      }else{
         $list['error'] = true;
         $list['error_msg'] = 'OTP tidak ditemukan.';
      }
      return $list;
   }

   # kode perusahaan
   function check_kode_perusahaan( $kode_perusahaan ){
      $this->db->select('id')
         ->from('company')
         ->where('code', $kode_perusahaan);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # check nomor whatsapp by code
   function check_nomor_whatsapp_by_code( $nomor_whatsapp, $kode_perusahaan ){
      $this->db->select('p.personal_id')
         ->from('personal AS p')
         ->join('company AS c', 'p.company_id=c.id', 'inner')
         ->where('p.nomor_whatsapp', $nomor_whatsapp)
         ->where('c.code', $kode_perusahaan);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # get personal id
   function get_personal_id( $nomor_whatsapp, $kode_perusahaan ){
      $this->db->select('p.personal_id')
               ->from('personal AS p')
               ->join('company AS c', 'p.company_id=c.id', 'inner')
               ->where('p.nomor_whatsapp', $nomor_whatsapp)
               ->where('c.code', $kode_perusahaan);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return $q->row()->personal_id;
      }else{
         return 0;
      }
   }


   function check_token_is_real($token, $kode_perusahaan, $nomor_whatsapp){
      $this->db->select('p.personal_id, p.otp_expire')
        ->from('personal AS p')
        ->join('company AS c', 'p.company_id=c.id', 'inner')
        ->where('p.otp', $token)
        ->where('c.code', $kode_perusahaan)
        ->where('p.nomor_whatsapp', $nomor_whatsapp);
     $q = $this->db->get();
     $list = array();
     if( $q->num_rows() > 0 ){
        foreach ( $q->result() as $rows ) {
           if( $rows->otp_expire > date('Y-m-d H:i:s') ) {
             $list['error'] = false;
             $list['error_msg'] = 'ditemukan';
           }else{
             $list['error'] = true;
             $list['error_msg'] = 'OTP sudah expired';
           }
        }
     }else{
        $list['error'] = true;
        $list['error_msg'] = 'OTP tidak ditemukan.';
     }
     return $list;
   }

   # check kode perusahaan exist
   function check_kode_perusahaan_exist( $kode ) {
      $this->db->select('id')
         ->from('company')
         ->where('code', $kode)
         ->where('end_date_subscribtion <', date('Y-m-d'));
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   // check company code exist
   function check_company_code_exist( $code ){
      $this->db->select('id')
         ->from('company')
         ->where('code', $code);
      $q = $this->db->get();
      if ( $q->num_rows() > 0 ) {
         return true;
      } else {
         return false;
      }
   }

   // get info subscribtion duration
   function get_info_subscribtion_duration($email){
      $this->db->select('sph.start_date_subscribtion, sph.end_date_subscribtion')
         ->from('subscribtion_payment_history AS sph')
         ->join('company AS c', 'sph.company_id=c.id', 'inner')
         ->where('sph.payment_status', 'accept')
         ->where('email', $email);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list['start_date_subscribtion'] = $rows->start_date_subscribtion;
            $list['end_date_subscribtion'] = $rows->end_date_subscribtion;
         }
      }   
      return $list;
   }

}
