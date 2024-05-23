<?php
/**
*  -----------------------
*	Model trans member cud
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_trans_member_cud extends CI_Model
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

   # approve process
   function approve_claim_trans_member( $fee_id, $data_deposit, $data_member_transaction, $member_transaction_id ){

      $this->db->trans_start();
      # update fee keagenan table
      foreach ($fee_id as $key => $value) {
         $data = array();
         $data['payment_status'] = 'paid';
         $data['member_transaction_request_id'] = $member_transaction_id;
         $data['last_update'] = date('Y-m-d');

         $this->db->where('id', $value);
         $this->db->where('company_id', $this->company_id );
         $this->db->update('fee_keagenan', $data);
      }
      # insert depoosit transaction
      $insert = $this->db->insert('deposit_transaction', $data_deposit);
      $deposit_id = $this->db->insert_id();
      # update member transaction table
      $data_member_transaction['ref'] = 'deposit_id:'.$deposit_id.';'.$data_member_transaction['ref'];
      $this->db->where('id', $member_transaction_id);
      $this->db->where('company_id', $this->company_id );
      $this->db->update('member_transaction_request', $data_member_transaction);

      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ( $this->db->trans_status() === FALSE )
      {
          # Something Went Wrong.
          $this->db->trans_rollback();
          $this->status = FALSE;
          $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menyetujui request transaksi member dengan transaksi member id nya '.$member_transaction_id.' ';
      }
      return $this->status;
   }

   function approve_deposit_trans_member($data_deposit, $data_member_transaction, $member_transaction_id){
      $this->db->trans_start();
      # insert deposit transaction
      $insert = $this->db->insert('deposit_transaction', $data_deposit);
      $deposit_id = $this->db->insert_id();
      # update member transaction table
      $data_member_transaction['ref'] = 'deposit_id:'.$deposit_id;
      $this->db->where('id', $member_transaction_id);
      $this->db->where('company_id', $this->company_id );
      $this->db->update('member_transaction_request', $data_member_transaction);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ( $this->db->trans_status() === FALSE )
      {
          # Something Went Wrong.
          $this->db->trans_rollback();
          $this->status = FALSE;
          $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menyetujui request deposit transaksi member dengan transaksi member id nya '.$member_transaction_id.' ';
      }
      return $this->status;
   }

   function approve_buy_paket_trans_member($id, $data_paket_transaction, $data_fee_agen, $data_paket_transaction_history,
                                          $data_installement_scheme, $data_jamaah, $data_member_transaction, $data_deposit){
      $this->db->trans_start();
      # insert data paket transaction
      $insert = $this->db->insert('paket_transaction', $data_paket_transaction);
      $paket_transaction_id = $this->db->insert_id();
      # insert data fee agen if not null
      if( count($data_fee_agen) > 0  ){
         foreach ($data_fee_agen as $key => $value) {
            $value['paket_transaction_id'] = $paket_transaction_id;
            $insert = $this->db->insert('paket_transaction', $value);
         }
      }
      # insert paket history
      $data_paket_transaction_history['paket_transaction_id'] = $paket_transaction_id;
      if( $data_paket_transaction['payment_methode'] == 0 ){
         $this->db->insert('paket_transaction_history', $data_paket_transaction_history);
      }elseif ( $data_paket_transaction['payment_methode'] == 1 ) {
         # insert paket transaction installement history
         $this->db->insert('paket_transaction_installement_history', $data_paket_transaction_history);
         # insert instalement scheme
         foreach ($data_installement_scheme as $key => $value) {
            $value['paket_transaction_id'] = $paket_transaction_id;
            $this->db->insert('paket_installment_scheme', $value);
         }
      }
      #  insert data jamaah
      foreach ($data_jamaah as $key => $value) {
         $value['paket_transaction_id'] = $paket_transaction_id;
         $this->db->insert('paket_transaction_jamaah', $value);
      }
      # update data member transaction
      $data_member_transaction['ref'] = 'paket_transaction_id:'.$paket_transaction_id.';'.$data_member_transaction['ref'];
      $this->db->where('id', $id);
      $this->db->where('company_id', $this->company_id );
      $this->db->update('member_transaction_request', $data_member_transaction);
      # insert data deposit
      if( count( $data_deposit ) > 0 ){
         $this->db->insert('deposit_transaction', $data_deposit);
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ( $this->db->trans_status() === FALSE )
      {
          # Something Went Wrong.
          $this->db->trans_rollback();
          $this->status = FALSE;
          $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menyetujui request pembelian paket member dengan transaksi member id nya '.$id.' ';
      }
      return $this->status;
   }

   function approve_payment_paket_trans_member($id, $info_member_transaction, $data_paket_transaction_history, $data_member_transaction, $data_deposit){
      $this->db->trans_start();
      # insert paket history
      if( $info_member_transaction['payment_methode'] == 0 ){
         $this->db->insert('paket_transaction_history', $data_paket_transaction_history);
      }elseif ( $info_member_transaction['payment_methode'] == 1 ) {
         # insert paket transaction installement history
         $this->db->insert('paket_transaction_installement_history', $data_paket_transaction_history);
      }
      # update data member transaction
      $this->db->where('id', $id);
      $this->db->where('company_id', $this->company_id );
      $this->db->update('member_transaction_request', $data_member_transaction);
      # insert data deposit
      if( count( $data_deposit ) > 0 ){
         $this->db->insert('deposit_transaction', $data_deposit);
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ( $this->db->trans_status() === FALSE )
      {
          # Something Went Wrong.
          $this->db->trans_rollback();
          $this->status = FALSE;
          $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menyetujui request pembayaran paket member dengan transaksi member id nya '.$id.' ';
      }
      return $this->status;
   }

   # decline trans member
   function decline_trans_member($id){
      $this->db->trans_start();
      # delete member transaction request
      $this->db->where('id', $id)
               ->where('company_id', $this->company_id )
               ->delete('member_transaction_request');
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ( $this->db->trans_status() === FALSE )
      {
          # Something Went Wrong.
          $this->db->trans_rollback();
          $this->status = FALSE;
          $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menghapus member transaksi request dengan member transaksi request id: '.$id.' ';
      }
      return $this->status;
   }

   /* Write log master data*/
   public function __destruct()
   {
      if( $this->write_log == 1 ){
         if( $this->status == true){
            if ( $this->error == 0 ) {
               $this->syslog->write_log( $this->content );
            }
         }
      }
   }
}
