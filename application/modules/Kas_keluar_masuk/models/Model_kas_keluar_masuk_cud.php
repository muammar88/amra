<?php

/**
 *  -----------------------
 *	Model trans paket cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_kas_keluar_masuk_cud extends CI_Model
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

   # update kas keluar masuk
   function update_kas_keluar_masuk($id, $data, $source, $data_jurnal)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data kas
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('kas_keluar_masuk', $data);
      # delete jurnal
      $this->db->where('source', $source)
         ->where('company_id', $this->company_id)
         ->delete('jurnal');
      # insert data jurnal
      foreach ($data_jurnal as $key => $value) {
         $this->db->insert('jurnal', $value);
      }
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
         $this->content = 'Melakukan perubahan data kas keluar masuk dengan kas id ' . $id . ' dan jurnal source ' . $source;
      }
      return $this->status;
   }

   # insert kas keluar masuk
   function insert_kas_keluar_masuk($data, $data_jurnal)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert kas keluar masuk
      $this->db->insert('kas_keluar_masuk', $data);
      $kas_id = $this->db->insert_id();
      # insert data jurnal
      foreach ($data_jurnal as $key => $value) {
         $this->db->insert('jurnal', $value);
      }
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
         $this->content = 'Melakukan penambahan kas keluar masuk dengan kas id ' . $kas_id . '.';
      }
      $airlines_id = $this->db->insert_id();
      return $this->status;
   }

   # delete kas masuk keluar
   function delete_kas_masuk_keluar($id, $source)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete kas keluar masuk
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('kas_keluar_masuk');
      # delete jurnal
      $this->db->where('source', 'generaltransaksi:invoice:' . $source)
         ->where('company_id', $this->company_id)
         ->delete('jurnal');
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
         $this->content = 'Melakukan penghapusan kas dengan id ' . $id . ' dan source jurnal ' . $source . '.';
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
