<?php

/**
 *  -----------------------
 *	Model tipe paket la cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_tipe_paket_la_cud extends CI_Model
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

   # update tipe paket la
   function update_tipe_paket_la($id, $data, $data_fasilitas)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data mst paket_type_la
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('mst_paket_type_la', $data);
      # delete fasilitas tipe paket la
      $this->db->where('paket_type_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('paket_type_la_facilities');
      # insert paket_type_la_facilities data
      foreach ($data_fasilitas as $key => $value) {
         $this->db->insert('paket_type_la_facilities', $value);
      }
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
         $this->content = 'Melakukan perubahan data fasilitas tipe paket la dengan tipe paket id ' . $id . '.';
      }
      return $this->status;
   }

   # insert tipe paket la
   function insert_tipe_paket_la($data, $data_fasilitas)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert mst paket_type_la
      $this->db->insert('mst_paket_type_la', $data);
      # tipe paket la id fasilitas la id
      $tipe_paket_la_id = $this->db->insert_id();
      #insert fasilitas tipe paket la
      foreach ($data_fasilitas as $key => $value) {
         $value['paket_type_id'] = $tipe_paket_la_id;
         $this->db->insert('paket_type_la_facilities', $value);
      }
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
         $this->content = 'Melakukan penambahan tipe paket la dengan tipe paket la id ' . $tipe_paket_la_id;
      }
      return $this->status;
   }

   # delete tipe paket la
   function delete_tipe_paket_la($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete paket_type_la_facilities
      $this->db->where('paket_type_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('paket_type_la_facilities');
      # delete mst tipe paket la
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('mst_paket_type_la');
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
         $this->content = 'Melakukan penghapusan data tipe paket la dengan tipe paket la id ' . $id . '.';
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
