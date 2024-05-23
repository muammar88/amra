<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_pengaturan_umum_cud extends CI_Model
{

   private $content = '';
   private $error = 1;
   private $CI;

   public function __construct()
   {
      parent::__construct();
      $this->CI = &get_instance();
      $this->CI->load->model('Model_Read/Model_pengaturan_umum', 'model_pengaturanumum');
      $this->CI->load->model('Model_CUD/Model_pengaturan_umum_cud', 'model_pengaturanumum_cud');
   }

   function updatePengguna($data_user, $data_personal, $user_id)
   {
      $error = 0;
      $personal_id = $this->CI->model_pengaturanumum->getPersonalID($user_id);
      // update personal table
      $this->db->where('personal_id', $personal_id);
      $updatePersonal = $this->db->update('personal', $data_personal);
      if (!$updatePersonal) {
         $error = 1;
      }
      if ($error == 0) {
         // update user table 
         $this->db->where('user_id', $user_id);
         $updateUser = $this->db->update('base_users', $data_user);
         if (!$updateUser) {
            $error = 1;
         } else {
            $this->error = 0;
            $this->content = ' Memperbaharui Data Pengguna ' . $data_personal['fullname'] . '';
         }
      }
      if ($error == 0) {
         return true;
      } else {
         return false;
      }
   }

   // insert pengguna
   function insertPengguna($data_user, $data_personal)
   {
      $error = 0;
      $personal_id = '';
      $data_user     = array_merge($data_user,  array('input_date' => date('Y-m-d')));
      $data_personal = array_merge($data_personal,  array('input_date' => date('Y-m-d')));
      /* insert process */
      if (!$this->db->insert('personal', $data_personal)) {
         $error = 1;
      } else {
         $personal_id = $this->db->insert_id();
      }
      if ($error == 0) {
         $data_user['personal_id'] = $personal_id;
         if (!$this->db->insert('base_users', $data_user)) {
            $error = 1;
         } else {
            $this->content     = ' Menambahkan Pengguna ' . $data_personal['fullname'] . '. ';
            $this->error     = 0;
         }
      }
      if ($error == 0) {
         return true;
      } else {
         return false;
      }
   }

   function delete_pengguna($id)
   {
      $error = 0;
      $personal_info = $this->CI->model_pengaturanumum->getPersonalIDFullname($id);

      // delete personal info 
      $this->db->where('personal_id', $personal_info['personal_id']);
      $delete = $this->db->delete('personal');
      if (!$delete) {
         $error = 1;
      }
      if ($error == 0) {
         // delete user info
         $this->db->where('user_id', $id);
         $deleteUser = $this->db->delete('base_users');
         if (!$deleteUser) {
            $error = 1;
         } else {
            $this->content   = ' Menghapus Pengguna ' . $personal_info['fullname'] . '. ';
            $this->error = 0;
         }
      }
      if ($error == 0) {
         return true;
      } else {
         return false;
      }
   }

   function updatePejabat($data, $id)
   {
      $this->db->where('id_pejabat', $id);
      $update = $this->db->update('pejabat', $data);
      if (!$update) {
         return false;
      } else {
         $this->content   = ' Memperbaharui Data Pejabat Dengan Nama Pejabat ' . $data['nama_pejabat'] . '. ';
         $this->error = 0;
         return true;
      }
   }

   function delete_pejabat($id)
   {
      $nama_pejabat = $this->CI->model_pengaturanumum->getPejabatName($id);
      // delete personal info 
      $data['nama_pejabat'] = '';
      $data['nip']          = '';
      $data['no_st']        = '';
      $data['tanggal_st']   = '';
      $data['alamat']       = '';
      $data['last_update']  = date('Y-m-d');

      $this->db->where('id_pejabat', $id);
      $update = $this->db->update('pejabat', $data);
      if (!$update) {
         return false;
      } else {
         $this->content  = ' Mengosongkan Data Pejabat Dengan Nama Pejabat ' . $nama_pejabat . '. ';
         $this->error = 0;
         return true;
      }
   }

   function updatePengaturanSystem($data)
   {
      foreach ($data as $ket => $value) {
         $this->db->where('setting_name', $ket);
         $update = $this->db->update('base_setting', array('setting_value' => $value));
         if (!$update) {
            return false;
         } else {
            $this->content  = ' Memperbaharui Pengaturan System ';
            $this->error = 0;
            return true;
         }
      }
   }

   function update_menu($id, $data)
   {
      $this->db->where('group_id', $id);
      $update = $this->db->update('base_groups', $data);
      if (!$update) {
         return false;
      } else {
         $this->content  = ' Memperbaharui Data Group Dengan Nama Group ' . $data['nama_group'];
         $this->error = 0;
         return true;
      }
   }

   function insert_menu($data)
   {
      if (!$this->db->insert('base_groups', $data)) {
         return false;
      } else {
         $this->content  = ' Menambahkan Group Baru Dengan Nama ' . $data['nama_group'] . '. ';
         $this->error    = 0;
         return true;
      }
   }

   function deleteGroupPengguna($id)
   {
      $nama_group = $this->CI->model_pengaturanumum->getGroupName($id);
      // delete personal info 
      $this->db->where('group_id', $id);
      $delete = $this->db->delete('base_groups');
      if (!$delete) {
         return false;
      } else {
         $this->content   = ' Menghapus Group Pengguna Dengan Nama Group' . $nama_group . '. ';
         $this->error = 0;
         return true;
      }
   }

   /* Write log master data*/
   public function __destruct()
   {
      if ($this->error == 0) {
         $this->syslog->write_log($this->content);
      }
   }
}
