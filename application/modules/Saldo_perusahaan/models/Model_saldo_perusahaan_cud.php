<?php

/**
 *  -----------------------
 *	Model saldo perusahaan cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_saldo_perusahaan_cud extends CI_Model
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

   // request tambah saldo
   function request_tambah_saldo( $data ) {
      $this->db->trans_start();
      # insert request tambah saldo
      $this->db->insert('request_tambah_saldo_company', $data);
      # get 
      $request_id = $this->db->insert_id();
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
         $this->content = ' Melakukan request tambah saldo dengan id : ' . $request_id;
      }
      return $this->status;
   }

   // delete request tambah saldo 
   function delete_request_tambah_saldo( $id ) {
      $this->db->trans_start();
      # delete request tambah saldo
      $this->db->where('id', $id)
               ->where('company_id', $this->company_id)
               ->delete('request_tambah_saldo_company');
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
         $this->content = ' Menghapus data request tambah saldo dengan id : ' . $id;
      }
      return $this->status;
   }

   // update tambah saldo perusahaan
   function update_tambah_saldo_perusahaan( $id, $data ){
      $this->db->trans_start();
      #update process
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('request_tambah_saldo_company', $data);
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
         $this->content = ' Memperbaharui data request tambah saldo perusahaan dengan id : ' . $id;
      }
      return $this->status;
   }

   /* Write log mst topik */
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
