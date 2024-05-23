<?php

/**
 *  -----------------------
 *	Model daftar peminjaman cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_peminjaman_cud extends CI_Model
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

   public function insert_daftar_peminjaman( $data ) {

      # Starting Transaction
      $this->db->trans_start();

      # define fee keagenan id variable
      $fee_keagenan_id = 0;
      # check fee keagenan
      if ( isset( $data['fee_keagenan'] ) ) {
         # insert peminjaman
         $this->db->insert('fee_keagenan', $data['fee_keagenan']);
         # get peminjaman id
         $fee_keagenan_id = $this->db->insert_id();
         # detail fee keagenan
         foreach ( $data['detail_fee_keagenan'] as $key => $value ) {
            # fee keagenan id
            $value['fee_keagenan_id'] = $fee_keagenan_id;
            # insert peminjaman
            $this->db->insert('detail_fee_keagenan', $value);
         }
      }

      if( isset( $data['pool'] ) ){
         # insert pool
         $data['pool']['fee_keagenan_id'] = $fee_keagenan_id;
         # insert process
         $this->db->insert('pool', $data['pool']);
         # get pool id
         $pool_id = $this->db->insert_id();

          # insert deposit transaction
         if( isset( $data['deposit_transaction'] ) ) {
            $this->db->insert('deposit_transaction', $data['deposit_transaction']);
            # get deposit_transaction id
            $deposit_transaction_id = $this->db->insert_id();

            $data['pool_deposit_transaction']['deposit_transaction_id'] = $deposit_transaction_id;
         }


         # insert pool deposit transaction 
         $data['pool_deposit_transaction']['pool_id'] = $pool_id;
         # insert pool_deposit_transaction
         $this->db->insert('pool_deposit_transaction', $data['pool_deposit_transaction']);

         # insert peminjaman
         $data['peminjaman']['pool_id'] = $pool_id;
      }
      

      # insert process
      $this->db->insert('peminjaman', $data['peminjaman']);
      # get peminjaman id
      $peminjaman_id = $this->db->insert_id();

      # insert skema 
      $skema = $data['skema_peminjaman'];
      # insert skema
      foreach ($skema as $key => $value) {
         # define peminjaman id
         $value['peminjaman_id'] = $peminjaman_id;
         # insert process
         $this->db->insert('skema_peminjaman', $value);
      }

      # insert pembayaran peminjaman
      if( isset( $data['pembayaran_peminjaman'] ) ) {
         # define peminjaman id         
         $data['pembayaran_peminjaman']['peminjaman_id'] = $peminjaman_id;
         #insert process
         $this->db->insert('pembayaran_peminjaman', $data['pembayaran_peminjaman']);
      }


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
         $this->content = 'Melakukan penambahan daftar peminjaman dengan nomor register ' . $data['peminjaman']['register_number'] . '.';
      }
      return $this->status;
   }


   # update skema peminjaman
   function update_skema_peminjaman($peminjaman_id, $data){
      # Starting Transaction
      $this->db->trans_start();
      # update process 
      foreach ($data as $key => $value) {
         $this->db->where('peminjaman_id', $peminjaman_id)
                  ->where('id', $key)
                  ->where('company_id', $this->company_id)
                  ->update('skema_peminjaman', array('amount' => $value['amount'], 
                                                     'due_date' => $value['due_date']));
      }

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
         $this->content = 'Melakukan update skema peminjaman dengan peminjaman_id ' . $peminjaman_id . '.';
      }
      return $this->status;
   }

   # insert pembayaran peminjaman
   function insert_pembayaran_peminjaman($data, $data_peminjaman){
      # Starting Transaction
      $this->db->trans_start();
      # insert process
      $this->db->insert('pembayaran_peminjaman', $data);
      # update peminjaman
      if( count($data_peminjaman) > 0  ){
         $this->db->where('id', $data['peminjaman_id'])
                  ->where('company_id', $this->company_id)
                  ->update('peminjaman', $data_peminjaman);
      }
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
         $this->content = 'Melakukan pembayaran peminjaman dengan invoice : ' . $data['invoice'] . '.';
      }
      return $this->status;

   }

   # mengahapus data peminjaman
   function delete_cicilan_peminjaman($peminjaman_id){
      # Starting Transaction
      $this->db->trans_start();
      # delete skema peminjaman
      $this->db->where('peminjaman_id', $peminjaman_id)
         ->where('company_id', $this->company_id)
         ->delete('skema_peminjaman');

      # delete pembayaran peminjaman
      $this->db->where('peminjaman_id', $peminjaman_id)
         ->where('company_id', $this->company_id)
         ->delete('pembayaran_peminjaman');

      # delete peminjaman
      $this->db->where('id', $peminjaman_id)
         ->where('company_id', $this->company_id)
         ->delete('peminjaman');   
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
         $this->content = 'Menghapus data peminjaman dengan peminjaman id : ' . $peminjaman_id . '.';
      }
      return $this->status;
   }

}