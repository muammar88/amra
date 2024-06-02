<?php
/**
*  -----------------------
*	Model beranda utama cud
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_beranda_utama_cud extends CI_Model
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

   # insert saldo payment log
   function insert_saldo_payment_log( $data )
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert saldo_company
      $data_company = array();
      $data_company['company_id'] = $this->company_id;
      $data_company['saldo'] = explode('.', $data['gross_amount'])[0];
      $data_company['request_type'] = 'deposit';
      $data_company['ket'] = 'Deposit saldo perusahaan sebesar '.  $this->kurs . ' ' .number_format( explode('.', $data['gross_amount'])[0] );
      $data_company['status'] = 'process';
      $data_company['input_date'] = date('Y-m-d H:i:s');
      $data_company['last_update'] = date('Y-m-d H:i:s');
      $this->db->insert('company_saldo_transaction', $data_company);
      # insert company
      $data_company_saldo_payment = array();
      $data_company_saldo_payment['order_id'] = $data['order_id'];
      $data_company_saldo_payment['company_saldo_transaction_id'] = $this->db->insert_id();
      $this->db->insert('company_saldo_payment', $data_company_saldo_payment);
      # insert saldo payment history
      $this->db->insert('payment_history', $data);
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
         $this->content = 'Melakukan penyimpanan log transaksi deposit saldo sebesar '. $this->kurs . ' ' . number_format( explode('.', $data['gross_amount'])[0] );
      }
      return $this->status;
   }

   #  update penolakan
   function penolakan_request($id, $data){
      $this->db->trans_start();
      $this->db->where('id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->update('member_transaction_request', $data);
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
         $this->content = ' Melakukan penolakan terhadap id member request ' . $id;
      }
      return $this->status;
   }

   function approve_request($id, $data ){
      # transaction start
      $this->db->trans_start();
      # update member request
      $this->db->where('id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->update('member_transaction_request', $data['member_transaction_request']);
      # insert to deposit table
      $deposit_id = 0;
      foreach ($data['deposit_transaction'] as $key => $value) {
        # insert deposit transaction
         $this->db->insert('deposit_transaction', $value);
         if( $value['transaction_requirement'] == 'paket_deposit'){
             $data['pool_deposit_transaction']['deposit_transaction_id'] = $this->db->insert_id();
         }
      }
      # insert fee keagenan & detail fee keagenan
      $fee_keagenan_id = 0;
      if( isset( $data['fee_keagenan'] ) ) {
        if( $this->db->insert('fee_keagenan', $data['fee_keagenan']) ) {
          $fee_keagenan_id = $this->db->insert_id();
          if ( isset( $data['detail_fee_keagenan'] ) AND count( $data['detail_fee_keagenan'] ) > 0 ) {
            foreach ( $data['detail_fee_keagenan'] as $key => $value ) {
              $value['fee_keagenan_id'] = $fee_keagenan_id;
              # insert detail fee keagenan
              $this->db->insert('detail_fee_keagenan', $value);
            }
          }
        }
      }
      # jurnal 
      if( isset( $data['jurnal'] ) ) {
         foreach ( $data['jurnal'] as $key => $value ) {
            $this->db->insert('jurnal', $value);
         }
      }
      # insert pool
      if( isset( $data['pool'] ) ) {
        $data['pool']['fee_keagenan_id'] = $fee_keagenan_id;
        if( $this->db->insert('pool', $data['pool']) ) {
          $data['pool_deposit_transaction']['pool_id'] = $this->db->insert_id();
        }
      }
      # insert pool deposit transaction
      if( isset( $data['pool_deposit_transaction'] ) ) {
        $this->db->insert('pool_deposit_transaction', $data['pool_deposit_transaction']);
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
         $this->content = 'Melakukan persetujuan terhadap id member request ' . $id;
      }
      return $this->status;
   }

   function update_headline($id, $data){
      # transaction start
      $this->db->trans_start();
      # update member request
      $this->db->where('id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->update('headline', $data);
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
         $this->content = 'Melakukan update data headline terhadap id : ' . $id;
      }
      return $this->status;
   }

   # insert headline
   function insert_headline($data){
      # Starting Transaction
     $this->db->trans_start();
     # insert headline
     $this->db->insert('headline', $data);
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
        $this->content = 'Melakukan penambahan headline ';
     }
     return $this->status;
   }

   // delete headline
   function delete_headline($id){
      # Starting Transaction
      $this->db->trans_start();
      # delete mst headline
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('headline');
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
         $this->content = 'Melakukan penghapusan data headline dengan id ' . $id;
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
