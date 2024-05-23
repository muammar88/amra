<?php

/**
 *  -----------------------
 *	Model daftar bandara cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_bandara_cud extends CI_Model
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

   function update_bandara( $id, $data ){
      # Starting Transaction
     $this->db->trans_start();
     # update data airport
     $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('mst_airport', $data);
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
         $this->content = 'Melakukan perubahan data airport dengan id ' . $id . '.';
     }
     return $this->status;
   }

   # insert data bandara
   function insert_bandara( $data ){
      # Starting Transaction
      $this->db->trans_start();
      # insert bandara
      $this->db->insert('mst_airport', $data);
      $mst_airport_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan bandara dengan id ' . $mst_airport_id . '.';
      }
      return $this->status;
   }

   #  delete bandara
   function delete_bandara( $id )
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete bandara
      $this->db->where('id', $id)
               ->delete('mst_airport');
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
         $this->content = 'Melakukan penghapusan bandara ' . $id . '.';
      }
      return $this->status;
   }

}
