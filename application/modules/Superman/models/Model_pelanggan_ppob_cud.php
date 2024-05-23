<?php

/**
 *  -----------------------
 *	Model pelanggan ppob cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_pelanggan_ppob_cud extends CI_Model
{
   // private $company_id;
   private $status;
   // private $content;
   private $error;
   private $write_log;

   public function __construct()
   {
      parent::__construct();
      $this->error = 0;
      $this->write_log = 1;
   }

   // insert new ppob costumer
   public function insert_new_pelanggan_ppob( $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # insert new data ppob costumer
      $this->db->insert('ppob_costumer', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
       } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
       }
      return $this->status;
   }

   // update ppob costumer
   function update_pelanggan_ppob( $id, $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # update ppob costumer
      $this->db->where('id', $id)
               ->update('ppob_costumer', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }


   // delete ppob costumer
   public function delete_pelanggan_ppob( $id ) {
      # Starting Transaction
      $this->db->trans_start();
      # delete produk
      $this->db->where('id', $id)
               ->delete('ppob_costumer');
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }

   // proses tambah saldo pelanggan
   function tambah_saldo_pelanggan($id, $data){
       # Starting Transaction
      $this->db->trans_start();
      # insert new data deposit
      $this->db->insert('ppob_costumer_deposit_history', $data['ppob_costumer_deposit_history']);
      # update data saldo di ppob costumer
      $this->db->where('id', $id)->update('ppob_costumer', $data['ppob_costumer']);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }


   function delete_riwayat_deposit_ppob( $id, $data ) {

       # Starting Transaction
      $this->db->trans_start();
      # update ppob costumer

      $data_costumer = array();
      $data_costumer['saldo'] = $data['saldo'];
      $data_costumer['updatedAt'] = date('Y-m-d H:i:s');
      $this->db->where('id', $data['costumer_id'])
               ->update('ppob_costumer', $data_costumer);
      # delete produk
      $this->db->where('id', $id)
               ->delete('ppob_costumer_deposit_history');
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }


}