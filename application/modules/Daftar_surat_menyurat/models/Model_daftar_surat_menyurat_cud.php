<?php

/**
 *  -----------------------
 *	Model daftar surat menyurat cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_surat_menyurat_cud extends CI_Model
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

   # update setting surat menyurat
   public function update_setting_surat_menyurat( $data ) {
      # Starting Transaction
      $this->db->trans_start();
       # delete
      $this->db->where('company_id', $this->company_id)
               ->delete('konfigurasi_surat_menyurat');
      # insert 
      $this->db->insert('konfigurasi_surat_menyurat', $data);
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
         $this->content = 'Melakukan proses update setting surat menyurat dengan company_id ' . $this->company_id . '.';
      }
      return $this->status;
   }

   // insert surat menyurat
   function insert_surat_menyurat( $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # insert surat menyurat
      $this->db->insert('riwayat_surat_menyurat', $data);
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
         $this->content = 'Melakukan cetak surat menyurat dengan nomor surat ' . $data['nomor_surat'] . '.';
      }
      return $this->status;
   }

   // delete riwayat surat
   function delete_riwayat_surat( $id ){
            # Starting Transaction
      $this->db->trans_start();
        # delete
      $this->db->where('id', $id)
               ->where('company_id', $this->company_id)
               ->delete('riwayat_surat_menyurat');
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
         $this->content = 'Melakukan delete surat menyurat dengan id ' . $id . '.';
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