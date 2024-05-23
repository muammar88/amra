<?php

/**
 *  -----------------------
 *	Model daftar provider cud cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_provider_visa_cud extends CI_Model
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

   # update provider visa 
   function update_provider_visa( $id, $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # update data provide visa
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('mst_provider', $data);
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
         $this->content = 'Melakukan perubahan data provider visa dengan provider visa id ' . $id . '.';
      }
      return $this->status;
   }

   # insert provider
   function insert_provider( $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # insert airlines data
      $this->db->insert('mst_provider', $data);
      # get provider id
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
         $this->content = 'Melakukan penambahan provider visa dengan provider id '.$id.'.';
      }
      return $this->status;
   }

   # delete provide visa
   function delete_provider_visa( $id ) {
      # Starting Transaction
      $this->db->trans_start();
      # delete mst airlines
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('mst_provider');
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
         $this->content = 'Melakukan penghapusan data provider visa dengan provider visa id ' . $id . '.';
      }
      return $this->status;
   }
}