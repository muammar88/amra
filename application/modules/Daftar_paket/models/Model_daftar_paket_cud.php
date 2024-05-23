<?php

/**
 *  -----------------------
 *	Model daftar paket cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_paket_cud extends CI_Model
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

   # update paket
   function update_paket($id, $data, $data_muthawif, $data_itinerary, $data_paket_price)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete muthawif
      $this->db->where('paket_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('paket_muthawif');
      # insert data muthawif
      foreach ($data_muthawif as $key => $value) {
         $value['paket_id'] = $id;
         $this->db->insert('paket_muthawif', $value);
      }
      # delete itinerary
      $this->db->where('paket_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('paket_itinerary');
      # insert itinerary
      foreach ($data_itinerary as $key => $value) {
         $value['paket_id'] = $id;
         $this->db->insert('paket_itinerary', $value);
      }
      # delete paket price
      $this->db->where('paket_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('paket_price');
      # insert paket price
      foreach ($data_paket_price as $key => $value) {
         $value['paket_id'] = $id;
         $this->db->insert('paket_price', $value);
      }
      # update paket data
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('paket', $data);
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
         $this->content = 'Melakukan perubahan data paket dengan paket id ' . $id . ' dan nama_paket : ' . $data['paket_name'] . '.';
      }
      return $this->status;
   }

   # insert paket
   function insert_paket($data, $data_muthawif, $data_itinerary, $data_paket_price)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert data paket
      $this->db->insert('paket', $data);
      # get id
      $paket_id = $this->db->insert_id();
      # insert data muthawif
      foreach ($data_muthawif as $key => $value) {
         $value['paket_id'] = $paket_id;
         $this->db->insert('paket_muthawif', $value);
      }
      # insert itinerary
      foreach ($data_itinerary as $key => $value) {
         $value['paket_id'] = $paket_id;
         $this->db->insert('paket_itinerary', $value);
      }
      # insert paket price
      foreach ($data_paket_price as $key => $value) {
         $value['paket_id'] = $paket_id;
         $this->db->insert('paket_price', $value);
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
         $this->content = 'Melakukan penambahan data paket dengan nama paket ' . $data['paket_name'] . '.';
      }
      return $this->status;
   }

   # delete paket
   function delete_paket($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete muthawif
      $this->db->where('paket_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('paket_muthawif');
      # delete itinerary
      $this->db->where('paket_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('paket_itinerary');
      # delete paket price
      $this->db->where('paket_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('paket_price');
      # get paket transaction id
      $this->db->select('id')
         ->from('paket_transaction')
         ->where('company_id', $this->company_id)
         ->where('paket_id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            # delete paket transaction history
            $this->db->where('paket_transaction_id', $rows->id)
               ->where('company_id', $this->company_id)
               ->delete('paket_transaction_history');
            # paket_transaction_installement_history
            $this->db->where('paket_transaction_id', $rows->id)
               ->where('company_id', $this->company_id)
               ->delete('paket_transaction_installement_history');
            # delete paket transaction jamaah
            $this->db->where('paket_transaction_id', $rows->id)
               ->where('company_id', $this->company_id)
               ->delete('paket_transaction_jamaah');
            # paket_installment_scheme
            $this->db->where('paket_transaction_id', $rows->id)
               ->where('company_id', $this->company_id)
               ->delete('paket_installment_scheme');
            #  handover_facilities
            $this->db->where('paket_transaction_id', $rows->id)
               ->where('company_id', $this->company_id)
               ->delete('handover_facilities');
            # handover_item
            $this->db->where('paket_transaction_id', $rows->id)
               ->where('company_id', $this->company_id)
               ->delete('handover_item');
         }
      }
      # get room id
      $this->db->select('id')
         ->from('rooms')
         ->where('company_id', $this->company_id)
         ->where('paket_id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            # delete room jamaah
            $this->db->where('room_id', $rows->id)
               ->where('company_id', $this->company_id)
               ->delete('rooms_jamaah');
         }
      }
      # get bus id
      $this->db->select('id')
         ->from('bus')
         ->where('company_id', $this->company_id)
         ->where('paket_id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            # delete bus jamaah
            $this->db->where('bus_id', $rows->id)
               ->where('company_id', $this->company_id)
               ->delete('bus_jamaah');
         }
      }
      # delete paket
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('paket');
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
         $this->content = 'Menghapus data paket dengan paket id ' . $id . '.';
      }
      return $this->status;
   }

   /* Write log mst airlines */
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
