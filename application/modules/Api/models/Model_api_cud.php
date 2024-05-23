<?php

/**
 *  -----------------------
 *	Model api cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_api_cud extends CI_Model
{
   private $company_id;
   private $status;
   private $content;
   private $error;
   private $write_log;

   public function __construct()
   {
      parent::__construct();
      // $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      $this->error = 0;
      $this->write_log = 1;
   }

  

   function record_ip_insert($ip){
      # Starting Transaction
      $this->db->trans_start();
      # insert ppob_transaction_history
      $this->db->insert('riwayat_ip', array('ip' => $ip, 'input_date' => date('Y-m-d H:i:s')));
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

   # insert ppob transaction
   function insert_ppob_transaction( $company_id, $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # insert ppob_transaction_history
      $this->db->insert('ppob_transaction_history', $data['ppob_transaction_history']);
      # get id
      $data['ppob_transaction_history_company']['ppob_transaction_history_id'] = $this->db->insert_id();
      # insert ppob_transaction_history_company
      $this->db->insert('ppob_transaction_history_company', $data['ppob_transaction_history_company']);
      # insert deposit transaction
      $this->db->insert('deposit_transaction', $data['deposit_transaction']);
      # jurnal
      $this->db->insert('jurnal', $data['jurnal']);
      # update saldo company 
      $this->db->where('id', $company_id)
                  ->update('company', $data['company']);
      # insert transaction saldo company
      $this->db->insert('company_saldo_transaction', $data['company_saldo_transaction']);
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

   # update notfi
   function update_notif( $id, $company_id, $personal_id ) {
      $this->db->select('id')
               ->from('notif_reader')
               ->where('company_id', $company_id)
               ->where('personal_id', $personal_id)
               ->where('notif_id', $id);
      $q = $this->db->get();
      if( $q->num_rows() == 0 ) {
         $data = array();
         $data['personal_id'] = $personal_id;
         $data['company_id'] = $company_id;
         $data['notif_id'] = $id;
         $data['created_at'] = date('Y-m-d H:i:s');
         # Starting Transaction
         $this->db->trans_start();
         # insert paket transaction
         $this->db->insert('notif_reader', $data);
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
      }else{
         return true;
      }
   }


   function insert_transaction_process( $data, $company_id )
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert paket transaction
      $this->db->insert('paket_transaction', $data['paket_transaction']);
      $paket_transaction_id = $this->db->insert_id();
      # insert paket transaction jamaah
      $data['paket_transaction_jamaah']['paket_transaction_id'] = $paket_transaction_id;
      $this->db->insert('paket_transaction_jamaah', $data['paket_transaction_jamaah']);
      # paket transaction history
      $data['paket_transaction_history']['paket_transaction_id'] = $paket_transaction_id;
      $this->db->insert('paket_transaction_history', $data['paket_transaction_history']);
      #  update pool
      if (isset($data['pool'])) {
         $data['pool']['paket_transaction_id'] = $paket_transaction_id;
         # update pool
         $this->db->where('id', $data['pool_id'])
            ->where('company_id', $company_id)
            ->update('pool', $data['pool']);
         # insert deposit
         if (isset($data['deposit_transaction'])) {
            $deposit_transaction = $data['deposit_transaction'];
            foreach ($deposit_transaction as $key => $value) {
               $value['paket_transaction_id'] = $paket_transaction_id;
               $this->db->insert('deposit_transaction', $value);
            }
         }
         # insert handover facilities
         if (isset($data['handover_facilities'])) {
            $handover_facilities = $data['handover_facilities'];
            foreach ($handover_facilities as $key => $value) {
               $value['paket_transaction_id'] = $paket_transaction_id;
               $this->db->insert('handover_facilities', $value);
            }
         }
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
         $this->content = 'Menambahkan data transaksi paket dengan nomor Register : ' . $data['paket_transaction']['no_register'] . ' dengan nama kode paket' . $data['info_paket']['kode'] . ' dan nama paket :' . $data['info_paket']['paket_name'] . ' ';
      }
      return $this->status;
   }


   function delete_deposit_and_jurnal_ppob( $ref_id, $product_id ){
      # Starting Transaction
      $this->db->trans_start();
      # delete deposit transaction
      $this->db->where('info', 'Pembelian Produk PPOB dengan Nomor Transaksi:'.$ref_id)->delete('deposit_transaction');
      # delete jurnal
      $this->db->where('source', 'ppob:transaction_code:'.$ref_id)->delete('jurnal');
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

   // update status transaksi ppob
   function update_status_transaksi_ppob( $ref_id, $data_update ){
      # Starting Transaction
      $this->db->trans_start();
      # update data ppob_transaction_history
      $this->db->where('transaction_code', $ref_id)
               ->update('ppob_transaction_history', $data_update);
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

   // update
   function update_status_ppob( $feedBack, $company_id ){
      # Starting Transaction
      $this->db->trans_start();  
      // status
      if( $feedBack['status'] == 'Gagal' ) {
         # update data ppob_transaction_history
         $this->db->where('transaction_code', $feedBack['transaction_code'])
                  ->update('ppob_transaction_history', array('ket' => $feedBack['pesan'], 'status' => 'failed'));
         # delete deposit transaction
         $this->db->where('info', 'Pembelian Produk PPOB dengan Nomor Transaksi:'.$feedBack['transaction_code'])->delete('deposit_transaction');
         # delete jurnal
         $this->db->where('source', 'ppob:transaction_code:'.$feedBack['transaction_code'])->delete('jurnal');
         # update data saldo company
         $this->db->where('id', $company_id)
                  ->update('company', array('saldo' => $feedBack['get_back_saldo']));
      }else if( $feedBack['status'] == 'Sukses' ){
         # update data ppob_transaction_history
         $this->db->where('transaction_code', $feedBack['transaction_code'])
                  ->update('ppob_transaction_history', array('ket' => $feedBack['pesan'], 'status' => 'success'));
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
      }
      return $this->status;
   }

   function delete_failed_ppob_transaction_company($company_id, $transaction_code, $price){
      # Starting Transaction
        $this->db->trans_start();
        # delete deposit
        $this->db->where('info', 'Pembelian Produk PPOB dengan Nomor Transaksi:' . $transaction_code )
                 ->where('company_id', $company_id)
                 ->delete('deposit_transaction');
        # delete jurnal
        $this->db->where('source', 'ppob:transaction_code:' . $transaction_code )
                 ->where('company_id', $company_id)
                 ->delete('jurnal');
        # company saldo transaction    
        $this->db->where('ket', 'PPOB:transaction_code:' . $transaction_code )
                 ->where('company_id', $company_id)
                 ->delete('company_saldo_transaction');    
        # update data saldo company
        $this->db->where('id', $company_id)
                 ->update('company', array('saldo' => $price));
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

   // delete failed ppob transaction costumer
   function delete_failed_ppob_transaction_costumer( $costumer_id, $price ){
      # update data saldo costumer
      $this->db->where('id', $costumer_id)
               ->update('ppob_costumer', array('saldo' => $price) );
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