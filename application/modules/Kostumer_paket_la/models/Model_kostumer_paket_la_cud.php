<?php
/**
*  -----------------------
*	Model kostumer paket la cud
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_kostumer_paket_la_cud extends CI_Model
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

   function update_kostumer_paket_la($id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data kostumer paket la
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('paket_la_costumer', $data);
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
         $this->content = 'Melakukan perubahan data kostumer paket la dengan id kostumer ' . $id . '.';
      }
      return $this->status;
   }

   public function insert_kostumer_paket_la( $data ){
       # start transaction
      $this->db->trans_start();
      # insert komplain
      $this->db->insert('paket_la_costumer', $data );
      # define kostumer id
      $kostumer_id = $this->db->insert_id();
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
         $this->content = ' Menambahkan data kostumer dengan id : ' . $kostumer_id;
      }
      return $this->status;
   }

   // delete kostumer paket la
   function delete_kostumer_paket_la($id) {
      # start transaction
      $this->db->trans_start();
      # delete kostumer paket la
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('paket_la_costumer');
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
         $this->content = ' Menghapus data kostumer paket la dengan id : ' . $id;
      }
      return $this->status;
   }
}