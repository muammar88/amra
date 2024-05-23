<?php

/**
 *  -----------------------
 *	Model daftar member cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_member_cud extends CI_Model
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

   # insert member
   function insert_member($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert personal data
      $this->db->insert('personal', $data);
      # get personal id
      $personal_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan data member dengan nama member ' . $data['fullname'] . ' dan member id ' . $personal_id;
      }
      return $this->status;
   }

   # update data member
   function update_member($id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data member
      $this->db->where('personal_id', $id)
         ->where('company_id', $this->company_id)
         ->update('personal', $data);
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
         $this->content = 'Melakukan perubahan data member dengan personal id ' . $id . '.';
      }
      return $this->status;
   }

   # delete member
   function delete_member($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete member
      $this->db->where('personal_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('personal');
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
         $this->content = 'Melakukan penghapusan data member dengan personal id ' . $id . '.';
      }
      return $this->status;
   }

   # set sebagai muthawif
   function set_as_muthawif($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # define data
      $data = array();
      $data['company_id'] = $this->company_id;
      $data['personal_id'] = $id;
      $data['input_date'] = date('Y-m-d H:i:s');
      $data['last_update'] = date('Y-m-d H:i:s');
      # insert muthawif data
      $this->db->insert('muthawif', $data);
      # get muthawif id
      $muthawif_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan data member menjadi muthawif dengan personal id ' . $id . ' dan muthawif id ' . $muthawif_id;
      }
      return $this->status;
   }

   # set member as agen
   function set_member_as_agen($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert data agen
      $this->db->insert('agen', $data);
      # get agen id
      $agen_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan data member menjadi agen dengan personal id ' . $data['personal_id'] . ' dan agen id ' . $agen_id;
      }
      return $this->status;
   }

   /* Write log mst airlines */
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
