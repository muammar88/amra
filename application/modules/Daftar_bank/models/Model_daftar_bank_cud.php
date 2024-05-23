<?php

/**
 *  -----------------------
 *	Model daftar bank cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_bank_cud extends CI_Model
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

   # update data bank
   function update_bank($id, $data, $kode_bank)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data bank
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('mst_bank', $data['mst_bank']);
      # update data akun
      $this->db->where('path', 'bank:kodebank:' . $kode_bank)
         ->where('company_id', $this->company_id)
         ->update('akun_secondary', $data['akun']);
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
         $this->content = 'Melakukan perubahan data bank dengan kode bank ' . $kode_bank . '.';
      }
      return $this->status;
   }

   function insert_bank($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert personal data
      $this->db->insert('mst_bank', $data['mst_bank']);
      # insert data akun
      $this->db->insert('akun_secondary', $data['akun']);
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
         $this->content = 'Melakukan penambahan bank dengan kode bank ' . $data['mst_bank']['kode_bank'] . '.';
      }
      return $this->status;
   }

   // delete bank
   function delete_bank($id, $kode_bank)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete bank akun number
      $this->db->where('path', 'bank:kodebank:' . $kode_bank);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('akun_secondary');
      # delete mst bank
      $this->db->where('id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('mst_bank');
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
         $this->content = 'Melakukan penghapusan bank dengan kode bank ' . $kode_bank . '.';
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
