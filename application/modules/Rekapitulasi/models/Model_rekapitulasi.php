<?php

/**
 *  -----------------------
 *	Model trans transport
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_rekapitulasi extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_daftar_rekapitulasi($search)
   {
      $this->db->select('id')
         ->from('recapitulation')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('recapitulation_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // (SELECT GROUP_CONCAT( CONCAT_WS(\'#\', ttd.costumer_price, ma.airlines_name) SEPARATOR \'&\')
   //    FROM tiket_transaction_detail AS ttd
   //    INNER JOIN mst_airlines AS ma ON ttd.airlines_id=ma.id
   //    WHERE ttd.company_id="'.$this->company_id.'" AND ttd.tiket_transaction_id=t.id) AS detail_tiket

   function get_detail_transaksi_tiket($tiket_transaction_id)
   {
      $this->db->select('ttd.costumer_price, ma.airlines_name, ttd.code_booking, ttd.pax')
         ->from('tiket_transaction_detail AS ttd')
         ->join('mst_airlines AS ma', 'ttd.airlines_id=ma.id', 'inner')
         ->where('ttd.company_id', $this->company_id)
         ->where('ttd.tiket_transaction_id', $tiket_transaction_id);
      $list = array();
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array(
               'kode_booking' => $rows->code_booking,
               'nama_maskapai' => $rows->airlines_name,
               'harga_kostumer' => $rows->costumer_price,
               'pax' => $rows->pax
            );
         }
      }
      return $list;
   }

   function get_index_daftar_rekapitulasi($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('r.id, r.recapitulation_number, r.receiver, r.receiver_address, r.input_date, r.last_update,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', t.id, t.no_register, t.total_transaksi ) SEPARATOR \';\')
                              FROM recapitulation_detail AS rd
                              INNER JOIN tiket_transaction AS t ON rd.tiket_transaction_id=t.id
                              WHERE rd.company_id="' . $this->company_id . '" AND
                              rd.recapitulation_id=r.id) AS recapitulasi_detail')
         ->from('recapitulation AS r')
         ->where('r.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('r.recapitulation_number', $search)
            ->group_end();
      }
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $exp = explode(';', $rows->recapitulasi_detail);
            $recapitulasi_detail = array();
            $total_harga = 0;
            foreach ($exp as $key => $value) {
               $exp2 = explode('$', $value);
               $recapitulasi_detail[] = array(
                  'no_register' => $exp2[1],
                  'total_transaksi' => $exp2[2],
                  'detail_transaksi_tiket' => $this->get_detail_transaksi_tiket($exp2[0])
               );
               $total_harga = $total_harga + $exp2[2];
            }
            $list[] = array(
               'id' => $rows->id,
               'recapitulation_number' => $rows->recapitulation_number,
               'penerima' => $rows->receiver,
               'alamat_penerima' => $rows->receiver_address,
               'tanggal_transaksi' => $rows->input_date,
               'detail' => $recapitulasi_detail,
               'total' => $total_harga
            );
         }
      }
      return $list;
   }


   function get_total_daftar_tiket($search, $listRekap)
   {
      $this->db->select('id')
         ->from('tiket_transaction')
         ->where('company_id', $this->company_id);
      if (count($listRekap) > 0) {
         $this->db->where_not_in('id', $listRekap);
      }
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('no_register', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_tiket($limit = 6, $start = 0, $search = '', $listRekap)
   {
      $this->db->select('tt.id, tt.no_register, tt.total_transaksi, tt.input_date,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', ttd.code_booking, ttd.costumer_price, ma.airlines_name ) SEPARATOR \';\')
                              FROM tiket_transaction_detail AS ttd
                              INNER JOIN mst_airlines AS ma ON ttd.airlines_id=ma.id
                              WHERE ttd.company_id="' . $this->company_id . '" AND ttd.tiket_transaction_id=tt.id) AS detail_tiket')
         ->from('tiket_transaction AS tt')
         ->where('tt.company_id', $this->company_id);
      if (count($listRekap) > 0) {
         $this->db->where_not_in('tt.id', $listRekap);
      }
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('tt.no_register', $search)
            ->group_end();
      }
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $detail = array();
            $exp = explode(';', $rows->detail_tiket);
            foreach ($exp as $key => $value) {
               $exp2 = explode('$', $value);
               $detail[] = array(
                  'code_booking' => $exp2[0],
                  'costumer_price' => $exp2[1],
                  'airlines_name' => $exp2[2]
               );
            }

            $list[] = array(
               'id' => $rows->id,
               'no_register' => $rows->no_register,
               'total_transaksi' => $rows->total_transaksi,
               'tanggal_transaksi' => $rows->input_date,
               'detail' => $detail
            );
         }
      }
      return $list;
   }

   function check_tiket_transaction_id($tiket_transaction_id)
   {

      $this->db->select('tiket_transaction_id')
         ->from('recapitulation_detail')
         ->where('company_id');
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = $rows->tiket_transaction_id;
         }
      }

      $feedBack = false;
      foreach ($tiket_transaction_id as $key => $value) {
         if (in_array($value, $list)) {
            $feedBack = true;
         }
      }
      return $feedBack;
   }

   function check_invoice_rekapitulasi($invoice)
   {
      $this->db->select('id')
         ->from('recapitulation')
         ->where('company_id', $this->company_id)
         ->where('recapitulation_number', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function get_was_rekap()
   {
      $this->db->select('tiket_transaction_id')
         ->from('recapitulation_detail')
         ->where('company_id', $this->company_id);
      $list = array();
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = $rows->tiket_transaction_id;
         }
      }
      return $list;
   }

   function check_id_rekapitulasi_exist($id)
   {
      $this->db->select('id')
         ->from('recapitulation')
         ->where('id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function get_invoice_rekapitulasi($id)
   {
      $this->db->select('recapitulation_number')
         ->from('recapitulation')
         ->where('id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $invoice = '';
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $invoice = $rows->recapitulation_number;
         }
      }
      return $invoice;
   }
   // function get_tiket_transaction(){
   //    $this->db->select('no_register, total_transaksi ')
   //       ->from('tiket_transaction')
   //
   //
   //
   // }

}
