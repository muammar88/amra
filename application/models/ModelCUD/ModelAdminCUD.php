<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ModelAdminCUD extends CI_Model
{

   private $status;
   private $content;
   private $error;

   public function __construct()
   {
      parent::__construct();
      $this->error = 0;
   }

   # update user profil
   public function updateUserProfil($data1, $data2)
   {
      $personal_id = $this->session->userdata($this->config->item('apps_name'))['personal_id'];
      $user_id = $this->session->userdata($this->config->item('apps_name'))['user_id'];
      $fullname = $this->session->userdata($this->config->item('apps_name'))['fullname'];
      # Starting Transaction
      $this->db->trans_start();
      # Updating Data Personal
      $this->db->where('personal_id', $this->session->userdata($this->config->item('apps_name'))['personal_id']);
      $this->db->update('personal', $data1);
      # Update Data User
      $this->db->where('user_id', $this->session->userdata($this->config->item('apps_name'))['user_id']);
      $this->db->update('base_users', $data2);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Mengubah Data Pengguna Dengan User ID : ' . $user_id . ' Dengan Nama ' . $fullname . ' ';
      }
      return $this->status;
   }

   /* Write log master data*/
   public function __destruct()
   {
      if ($this->status == true) {
         if ($this->error == 0) {
            $this->syslog->write_log($this->content);
         }
      }
   }
}
