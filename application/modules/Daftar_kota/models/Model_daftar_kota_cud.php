<?php

/**
 *  -----------------------
 *	Model trans paket cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_kota_cud extends CI_Model
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

   # insert city
   function insert_city($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert personal data
      $this->db->insert('mst_city', $data);
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
         $this->content = 'Melakukan penambahan data kota dengan nama ' . $data['city_name'] . ' dan kode kota ' . $data['city_code'] . '.';
      }
      return $this->status;
   }

   # update data kota
   function update_city($id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data kota
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('mst_city', $data);
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
         $this->content = 'Melakukan perubahan data kota dengan nama kota ' . $data['city_name'] . ' dan kode kota ' . $data['city_code'] . '.';
      }
      return $this->status;
   }

   // delete bank
   function delete_city($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete mst city
      $this->db->where('id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('mst_city');
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
         $this->content = 'Melakukan penghapusan data kota dengan id kota ' . $id . '.';
      }
      return $this->status;
   }

   // function update_city($id, $data){
   //
   //
   // }

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
