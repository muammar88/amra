<?php

/**
 *  -----------------------
 *	Model superman cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_superman_cud extends CI_Model
{
   private $company_id;
   private $status;
   private $content;
   private $error;
   private $write_log;

   public function __construct()
   {
      parent::__construct();
      $this->error = 0;
      $this->write_log = 1;
   }

   public function takeCharge( $id, $data_transaksi, $data_company ) {
      # Starting Transaction
      $this->db->trans_start();
       # insert company_saldo_transaction
      $this->db->insert('company_saldo_transaction', $data_transaksi);
      # update data company
      $this->db->where('id', $id)
               ->update('company', $data_company);
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
         // $this->content = 'Melakukan reject request tambah saldo dengan id ' . $id . '.';
      }
      return $this->status;
   }

   // reject tambah saldo
   public function reject_tambah_saldo($id){
      # Starting Transaction
      $this->db->trans_start();

      # update data airlines
      $this->db->where('id', $id)
               ->update('company_saldo_transaction', array('status' => 'rejected'));
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
         // $this->content = 'Melakukan reject request tambah saldo dengan id ' . $id . '.';
      }
      return $this->status;
   }

   function approve_tambah_saldo($id, $info_perusahaan){
      # Starting Transaction
      $this->db->trans_start();
      // pembahan 
      $this->db->where('id', $info_perusahaan['company_id'])
               ->update('company', array('saldo' => $info_perusahaan['saldo_perusahaan'] + $info_perusahaan['saldo_ditambah'], 
                                          'last_update' => date('Y-m-d')  ));
      # update data company saldo transaksi
      $this->db->where('id', $id)
               ->update('company_saldo_transaction', array('status' => 'accepted', 'last_update' => date('Y-m-d H:i:s')));
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
         // $this->content = 'Melakukan approve request tambah saldo dengan id ' . $id . '.';
      }
      return $this->status;
   }

   # tambah saldo perusahaan
   function tambah_saldo_perusahaan( $id, $data ){
      # Starting Transaction
      $this->db->trans_start();
      // update company
      $this->db->where('id', $id)
               ->update('company', $data['company']);
      # insert company saldo transaction
      $this->db->insert('company_saldo_transaction', $data['company_saldo_transaction']);
      # get id transaksi
      $id_transkasi = $this->db->insert_id();
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
         // $this->content = 'Melakukan penambahan saldo dengan id perusahaan ' . $id . ' dan id transaksi '.$id_transkasi. '.';
      }
      return $this->status;
   }

   // approve tambah waktu berlangganan
   function approve_tambah_waktu_berlangganan ( $id, $data, $company_id, $order_id, $company_saldo_id ) { 
      # Starting Transaction
      $this->db->trans_start();
      // update company
      $this->db->where('id', $company_id)
               ->update('company', $data['company']);
      // update company
      $this->db->where('id', $id)
               ->update('subscribtion_payment_history', $data['subscribtion_payment_history']);  
      if( $company_saldo_id != '' ) {
         $this->db->where('id', $company_saldo_id)
                  ->update('company_saldo_transaction', $data['company_saldo_transaction']);           
      }                
      # get id transaksi
      $id_transkasi = $this->db->insert_id();
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
         // $this->content = 'Melakukan approve request tambah waktu berlangganan dengan order id' . $order_id ;
      }
      return $this->status;
   }

   // reject tambah waktu berlangganan
   function reject_tambah_waktu_berlangganan( $id, $data, $order_id, $company_saldo_id  ){
      # Starting Transaction
      $this->db->trans_start();
      // update company
      $this->db->where('id', $id)
               ->update('subscribtion_payment_history', $data['subscribtion_payment_history']);  
      if( $company_saldo_id != '' ) {
         $this->db->where('id', $company_saldo_id)
                  ->update('company_saldo_transaction', $data['company_saldo_transaction']);           
      }                
      # get id transaksi
      $id_transkasi = $this->db->insert_id();
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
         // $this->content = 'Melakukan reject request tambah waktu berlangganan dengan order id' . $order_id ;
      }
      return $this->status;
   }

   // tambah waktu berlangganan perusahaan
   function tambah_waktu_berlangganan_perusahaan($id, $data){
      # Starting Transaction
      $this->db->trans_start();
      // update company
      $this->db->where('id', $id)
               ->update('company', $data);
      // # get id transaksi
      // $id_transkasi = $this->db->insert_id();
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
      }
      return $this->status;
   }

   // update data perusahaan
   function update_data_perusahaan( $id, $data ) {
      # Starting Transaction
      $this->db->trans_start();
      // update company
      $this->db->where('id', $id)->update('company', $data);
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
      }
      return $this->status;
   }

   // insert data perusahaan
   function insert_data_perusahaan( $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # insert data company
      $this->db->insert('company', $data);
      # get id transaksi
      $id_perusahaan = $this->db->insert_id();
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
      }
      return $this->status;
   }

   // delete perusahaan
   function delete_perusahaan($id){
      # Starting Transaction
      $this->db->trans_start();
       # delete perusahaan
      $this->db->where('id', $id)->delete('company');
      # get id transaksi
      // $id_perusahaan = $this->db->insert_id();
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
      }
      return $this->status;
   }

   // tambah saldo
   function tambah_saldo( $id, $data ) {
      # Starting Transaction
      $this->db->trans_start();
       # insert data company
      $this->db->insert('company_saldo_transaction', $data['company_saldo_transaction']);
      // update company
      $this->db->where('id', $id)
               ->update('company', $data['company']);
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
      }
      return $this->status;
   }


   function tambah_waktu_berlangganan_per_perusahaan($id, $data){
      # Starting Transaction
      $this->db->trans_start();
      // update company
      $this->db->where('id', $id)
               ->update('company', $data);
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
      }
      return $this->status;
   }


   function approve_request_tambah_saldo( $id, $company_id, $data) {
      $this->db->trans_start();
      // update company
      $this->db->where('id', $company_id)
               ->update('company', $data['company']);
      // update data request tambah saldo company
      $this->db->where('id', $id)
               ->update('request_tambah_saldo_company', $data['request_tambah_saldo_company']);
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
      }
      return $this->status;
   }


   function tolak_request_tambah_saldo($id, $data){
      $this->db->trans_start();
      // update data request tambah saldo company
      $this->db->where('id', $id)
               ->update('request_tambah_saldo_company', $data);
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
      }
      return $this->status;
   }

   // delete request tambah saldo pelanggan amra
   function delete_request_tambah_saldo( $id ) {
      # Starting Transaction
      $this->db->trans_start();
       # delete perusahaan
      $this->db->where('id', $id)->delete('request_tambah_saldo_company');
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
      }
      return $this->status;
   }

}