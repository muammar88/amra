<?php

/**
 *  -----------------------
 *	Model trans transport
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_trans_transport extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_daftar_transaksi_transport($search)
   {
      $this->db->select('id')
         ->from('transport_transaction')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('invoice', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_transaksi_transport($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('tt.id, tt.invoice, tt.payer, tt.payer_identity, tt.input_date, tt.address,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', mc.car_name, ttd.car_number, ttd.price ) SEPARATOR \';\')
                              FROM transport_transaction_detail AS ttd
                              INNER JOIN 	mst_car AS mc ON ttd.car_id=mc.id
                              WHERE ttd.company_id="' . $this->company_id . '"  AND
                              ttd.transport_transaction_id=tt.id) AS transaksi_transport_detail')
         ->from('transport_transaction AS tt')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('tt.invoice', $search)
            ->group_end();
      }
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $exp = explode(';', $rows->transaksi_transport_detail);
            $transport_transaksi_detail = array();
            $total_harga = 0;
            foreach ($exp as $key => $value) {
               $exp2 = explode('$', $value);
               $transport_transaksi_detail[] = array(
                  'jenis_mobil' => $exp2[0],
                  'nomor_plat' => $exp2[1],
                  'price' => $exp2[2]
               );
               $total_harga = $total_harga + $exp2[2];
            }
            $list[] = array(
               'id' => $rows->id,
               'invoice' => $rows->invoice,
               'payer' => $rows->payer,
               'payer_identity' => $rows->payer_identity,
               'address' => $rows->address,
               'tanggal_transaksi' => $this->date_ops->change_date_t3($rows->input_date),
               'detail' => $transport_transaksi_detail,
               'total' => $total_harga
            );
         }
      }
      return $list;
   }

   function get_list_car()
   {
      $this->db->select('id, car_name')
         ->from('mst_car')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[$rows->id] = $rows->car_name;
         }
      }
      return $list;
   }

   function get_list_id_car()
   {
      $this->db->select('id')
         ->from('mst_car')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = $rows->id;
         }
      }
      return $list;
   }

   function check_invoice_transport_exist($invoice)
   {
      $this->db->select('id')
         ->from('transport_transaction')
         ->where('company_id', $this->company_id)
         ->where('invoice', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # check id transaction transport exist
   function check_id_transaction_transport_exist($id)
   {
      $this->db->select('id')
         ->from('transport_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   # get invoice by id
   function get_invoice_by_id($id)
   {
      $this->db->select('invoice')
         ->from('transport_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $invoice = '';
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $invoice = $rows->invoice;
         }
      }
      return  $invoice;
   }
}
