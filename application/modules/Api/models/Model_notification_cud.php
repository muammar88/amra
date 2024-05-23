<?php
/**
*  -----------------------
*	Model notification cud
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_notification_cud extends CI_Model
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

   # update transaction
   public function update_transaction(  $data_transaction, $data_history, $data_company, $company_id, $order_id, $company_saldo_transaction_id, $data_jurnal ){
      # start transaction
      $this->db->trans_start();
      # update saldo transaction saldo
      if( count($data_transaction) > 0 ){
         $this->db->where('id', $company_saldo_transaction_id)
                  ->update('company_saldo_transaction', $data_transaction);
      }
      # update payment history
      $this->db->where('order_id', $order_id)
               ->update('payment_history', $data_history);
      // count if jurnal exist
      if( count( $data_jurnal ) > 0  ) {
         $this->db->insert('jurnal', $data_jurnal);
      }
      # update saldo company
      if( count($data_company) > 0 ){
         # update saldo company
         $this->db->where('id', $company_id)
                  ->update('company', $data_company);
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
      }
      return $this->status;
   }

   function update_subscribtion_transaction( $data_company, $data_payment_history, $data_subscribtion_payment_history, $company_id, $order_id ){
      # start transaction
      $this->db->trans_start();
      # update subscribtion_payment_history
      if( count($data_subscribtion_payment_history) > 0 ){
         $this->db->where('order_id', $order_id)
            ->update('subscribtion_payment_history', $data_subscribtion_payment_history);
      }
      # update payment history
      $this->db->where('order_id', $order_id)
         ->update('payment_history', $data_payment_history);
      # update saldo company
      if( count( $data_company ) > 0 ){
         # update saldo company
         $this->db->where('id', $company_id)
            ->update('company', $data_company);
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
      }
      return $this->status;
   }

}
