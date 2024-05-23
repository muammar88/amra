<?php

/**
 *  -----------------------
 *	Model trans paket cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_tipe_paket_cud extends CI_Model
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

   // update tipe paket
   function update_tipe_paket($id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data tipe paket
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('mst_paket_type', $data);
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
         $this->content = 'Melakukan perubahan data tipe paket dengan id tipe paket ' . $id . '.';
      }
      return $this->status;
   }

   # insert proses
   function insert_tipe_paket($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert tipe paket data
      $this->db->insert('mst_paket_type', $data);
      # tipe paket id
      $tipe_paket_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan tipe paket baru dengan nama tipe paket ' . $data['paket_type_name'] . ' dan dengan tipe paket id ' . $tipe_paket_id . '.';
      }
      return $this->status;
   }

   # delete tipe paket
   function delete_tipe_paket($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete mst_paket_type
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('mst_paket_type');
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
         $this->content = 'Melakukan penghapusan data tipe paket dengan tipe paket id ' . $id . '.';
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
