<?php

/**
 *  -----------------------
 *	Model daftar asuransi cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_asuransi_cud extends CI_Model
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

   # update asuransi 
   function update_asuransi( $id, $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # update data provide visa
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('mst_asuransi', $data);
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
         $this->content = 'Melakukan perubahan data asuransi dengan asuransi id ' . $id . '.';
      }
      return $this->status;
   }

   # insert asuransi
   function insert_asuransi( $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # insert asuransi data
      $this->db->insert('mst_asuransi', $data);
      # get asuransi id
      $id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan asuransi dengan asuransi id '.$id.'.';
      }
      return $this->status;
   }

   # delete asuransi
   function delete_asuransi( $id ) {
      # Starting Transaction
      $this->db->trans_start();
      # delete mst asuransi
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('mst_asuransi');
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
         $this->content = 'Melakukan penghapusan data asuransi dengan asuransi id ' . $id . '.';
      }
      return $this->status;
   }
}