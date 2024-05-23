<?php

/**
 *  -----------------------
 *	Model pengguna cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_pengguna_cud extends CI_Model
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

   function insertPengguna($data, $data_personal)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert base personal
      $this->db->insert('personal', $data_personal);
      # insert base user
      $data['personal_id'] = $this->db->insert_id();
      $this->db->insert('base_users', $data);
      $user_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan pengguna baru dengan user id ' . $user_id;
      }
      return $this->status;
   }

   # update data personal
   function updatePengguna($id, $personal_id, $data, $data_personal)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data user
      $this->db->where('personal_id', $personal_id)
         ->where('company_id', $this->company_id)
         ->update('personal', $data_personal);
      # update data personal
      $this->db->where('user_id', $id)
         ->where('company_id', $this->company_id)
         ->update('base_users', $data);
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
         $this->content = 'Melakukan perubahan data user dengan id user ' . $id . ' dan id personal ' . $personal_id . '.';
      }
      return $this->status;
   }

   # delete data pengguna
   function delete_pengguna($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete base users
      $this->db->where('user_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('base_users');
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan penghapusan data pengguna dengan user id ' . $id . '.';
      }
      return $this->status;
   }

   /* Write log master data*/
   public function __destruct()
   {
      if ($this->write_log == 1) {
         if ($this->status == true) {
            if ($this->error == 0) {
               $this->syslog->write_log($this->content);
            }
         }
      }
   }
}
