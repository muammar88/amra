<?php

/**
 *  -----------------------
 *	Model trans tiket CUD (Create Update Delete)
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_trans_tiket_cud extends CI_Model
{
   private $company_id;
   private $status;
   private $content;
   private $error;
   private $write_log;
   private $id;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      $this->error = 0;
      $this->write_log = 1;
   }

   # insert trnas tiket
   function insert_trans_tiket($data_transaction, $data_transaction_detail, $data_transaction_history, $data_jurnal)
   {
      # Starting Transaction
      $this->db->trans_start();
      // insert data transaction
      $this->db->insert('tiket_transaction', $data_transaction);
      $id = $this->db->insert_id();
      // insert data transaction detail
      foreach ($data_transaction_detail as $key => $data) {
         $data['tiket_transaction_id'] = $id;
         $this->db->insert('tiket_transaction_detail', $data);
      }
      // insert data transaction history
      $data_transaction_history['tiket_transaction_id'] = $id;
      $this->db->insert('tiket_transaction_history', $data_transaction_history);
      // insert jurnal
      foreach ($data_jurnal as $keyData => $valueData) {
         $this->db->insert('jurnal', $valueData);
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
         $this->content = 'Melakukan penyimpanan data transaksi tiket baru dengan nomor register ' . $data_transaction['no_register'] . '. dan nomor invoice ' . $data_transaction_history['invoice'];
      }
      return $this->status;
   }

   # insert pembayaran tiket
   function insert_pembayaran_tiket($data, $data_jurnal)
   {
      # Starting Transaction
      $this->db->trans_start();
      // insert data tiket transaction history
      $this->db->insert('tiket_transaction_history', $data);
      // insert data jurnal
      $this->db->insert('jurnal', $data_jurnal);
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
         $this->content = 'Melakukan penyimpanan data transaksi pembayaran tiket dengan nomor invoice ' . $data['invoice'];
      }
      return $this->status;
   }

   # delete transaksi tiket
   function delete_transaksi_tiket($id, $no_reg, $tiket_transaction_detail_id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete tiket transaction history
      $this->db->where('tiket_transaction_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('tiket_transaction_history');
      # delete tiket transactioon detail
      $this->db->where('tiket_transaction_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('tiket_transaction_detail');
      # delete tiket transaction refund
      $this->db->where('tiket_transaction_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('tiket_transaction_refund');
      # delete reschedule history detail
      foreach ($tiket_transaction_detail_id as $key => $value) :
         $this->db->where('tiket_transaction_detail_id', $value)
            ->where('company_id', $this->company_id)
            ->delete('reschedule_tiket_history_detail');
      endforeach;
      # delete reschedule tiket history
      $this->db->where('tiket_transaction_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('reschedule_tiket_history');
      # delete jurnal
      $this->db->where('source', 'tiket:noreg:' . $no_reg)
         ->where('company_id', $this->company_id)
         ->delete('jurnal');
      # delete tiket transaction
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('tiket_transaction');
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
         $this->content = 'Menghapus transaksi tiket dengan nomor register ' . $no_reg;
      }
      return $this->status;
   }

   function update_schedule($update_t_t_detail, $data_tiket_transaction, $data_tiket_transaction_history, $data_reschedule_tiket, $data_jurnal)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update total transaksi
      $this->db->where('id', $data_tiket_transaction['id']);
      $this->db->where('company_id', $this->company_id);
      $this->db->update('tiket_transaction', array('total_transaksi' => $data_tiket_transaction['total_transaksi']));
      # delete tiket transaction history
      $this->db->where('tiket_transaction_id', $data_tiket_transaction['id'])
         ->where('company_id', $this->company_id)
         ->delete('tiket_transaction_history');
      # insert new tiket transaction history
      $this->db->insert('tiket_transaction_history', $data_tiket_transaction_history);
      # insert data jurnal
      $this->db->insert('reschedule_tiket_history', $data_reschedule_tiket);
      $this->id = $this->db->insert_id();
      # update reschedule_tiket_history_detail
      foreach ($update_t_t_detail as $key => $value) {
         # update data tiket transaction detail
         $data = array();
         $data['departure_date'] = $value['new_departure_date'];
         $data['travel_price'] = $value['new_travel_price'];
         $data['costumer_price'] = $value['new_costumer_price'];
         $data['code_booking'] = $value['new_code_booking'];
         # update process
         $this->db->where('id', $value['tiket_transaction_detail_id']);
         $this->db->where('company_id', $this->company_id);
         $this->db->update('tiket_transaction_detail', $data);
         # insert to reschedule_tiket_history_detail
         $data = array();
         $data['history_id'] = $this->id;
         $data['company_id'] = $this->company_id;
         $data['tiket_transaction_detail_id'] = $value['tiket_transaction_detail_id'];
         $data['old_departure_date'] = $value['old_departure_date'];
         $data['old_travel_price'] = $value['old_travel_price'];
         $data['old_costumer_price'] = $value['old_costumer_price'];
         $data['old_code_booking'] = $value['old_code_booking'];
         $data['new_departure_date'] = $value['new_departure_date'];
         $data['new_travel_price'] = $value['new_travel_price'];
         $data['new_costumer_price'] = $value['new_costumer_price'];
         $data['new_code_booking'] = $value['new_code_booking'];
         $data['input_date'] = date('Y-m-d');
         # insert process
         $this->db->insert('reschedule_tiket_history_detail', $data);
      }
      # delete jurnal
      $this->db->where('source', 'tiket:noreg:' . $data_tiket_transaction['no_register']);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('jurnal');
      # insert jurnal
      foreach ($data_jurnal as $keyData => $valueData) {
         $this->db->insert('jurnal', $valueData);
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
         $this->content = 'Melakukan reschedule tiket dengan nomor registrasi ' . $data_tiket_transaction['no_register'];
      }
      return $this->status;
   }

   function reschedule_id()
   {
      $this->write_log = 0;
      return $this->id;
   }

   // insert refund
   function insert_refund($tiket_transaction_id, $data_transaksi, $list_id, $data_history, $data_tiket_transaction_refund, $data_jurnal)
   {
      # Starting Transaction
      $this->db->trans_start();
      # tiket_transaction_refund
      foreach ($data_tiket_transaction_refund as $key => $value) :
         $this->db->insert('tiket_transaction_refund', $value);
      endforeach;
      # delete detail tiket transaction in table tiket transaction  detail
      $string_id = '';
      foreach ($list_id as $key => $value) :
         $this->db->where('id', $value)
            ->where('company_id', $this->company_id)
            ->delete('tiket_transaction_detail');
         $string_id .= $key == 0 ? $value : ' ,' . $value;
      endforeach;
      # insert refund to tiket transaction history
      $this->db->insert('tiket_transaction_history', $data_history);
      # update data tiket_transaksi
      if ($data_transaksi['total_transaksi'] == 0) {
         $data_transaksi['status'] = 'refund';
      }
      $this->db->where('id', $tiket_transaction_id)
         ->where('company_id', $this->company_id)
         ->update('tiket_transaction', $data_transaksi);

      # insert jurnal
      foreach ($data_jurnal as $key => $value) :
         $this->db->insert('jurnal', $value);
      endforeach;
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
         $this->content = 'Melakukan transaksi refund tiket dengan tiket detail id ' . $string_id;
      }
      return $this->status;
   }

   /* Write log ticket transaction*/
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
