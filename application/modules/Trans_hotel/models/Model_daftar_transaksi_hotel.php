<?php

/**
 *  -----------------------
 *	Model daftar transaksi hotel
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_transaksi_hotel extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_daftar_transaksi_hotel($search)
   {
      $this->db->select('id')
         ->from('hotel_transaction')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('invoice', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_transaksi_hotel($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('ht.id, ht.invoice, ht.payer, ht.payer_identity, ht.input_date,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', htd.name, htd.identity_number, htd.birth_place,
                                                htd.birth_date, mh.hotel_name, c.city_name,
                                                htd.check_in_date, htd.check_out_date, htd.price ) SEPARATOR \';\')
                              FROM hotel_transaction_detail AS htd
                              INNER JOIN mst_hotel AS mh ON htd.hotel_id=mh.id
                              INNER JOIN 	mst_city AS c ON htd.city_id=c.id
                              WHERE htd.company_id="' . $this->company_id . '"  AND
                              transaction_hotel_id=ht.id) AS transaksi_hotel_detail')
         ->from('hotel_transaction AS ht')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('ht.invoice', $search)
            ->group_end();
      }
      $this->db->order_by('ht.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $exp = explode(';', $rows->transaksi_hotel_detail);
            $hotel_transaksi_detail = array();
            $total_harga = 0;
            foreach ($exp as $key => $value) {
               $exp2 = explode('$', $value);
               $hotel_transaksi_detail[] = array(
                  'nama_pelanggan' => $exp2[0],
                  'nomor_identitas' => $exp2[1],
                  'tempat_lahir' => $exp2[2],
                  'tanggal_lahir' => $exp2[3],
                  'nama_hotel' => $exp2[4],
                  'nama_kota' => $exp2[5],
                  'check_in' => $this->date_ops->change_date_t3($exp2[6]),
                  'check_out' => $this->date_ops->change_date_t3($exp2[7]),
                  'price' => $exp2[8]
               );
               $total_harga = $total_harga + $exp2[8];
            }
            $list[] = array(
               'id' => $rows->id,
               'invoice' => $rows->invoice,
               'payer' => $rows->payer,
               'payer_identity' => $rows->payer_identity,
               'tanggal_transaksi' => $this->date_ops->change_date_t3($rows->input_date),
               'detail' => $hotel_transaksi_detail,
               'total' => strval($total_harga)
            );
         }
      }
      return $list;
   }

   # get list hotel
   function get_list_hotel()
   {
      $this->db->select('mh.id, mh.hotel_name, c.city_name')
         ->from('mst_hotel AS mh')
         ->join('mst_city AS c', 'mh.city_id=c.id', 'inner')
         ->where('mh.company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[$rows->id] = $rows->hotel_name . ' (Nama Kota : ' . $rows->city_name . ')';
         }
      }
      return $list;
   }

   # get_list_city
   function get_list_city()
   {
      $this->db->select('id, city_name, city_code')
         ->from('mst_city')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[$rows->id] = $rows->city_name . ' (' . $rows->city_code . ')';
         }
      }
      return $list;
   }

   function get_list_id_city()
   {
      $this->db->select('id')
         ->from('mst_city')
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

   function get_list_id_hotel()
   {
      $this->db->select('id')
         ->from('mst_hotel')
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

   function check_invoice_hotel_exist($invoice)
   {
      $this->db->select('id')
         ->from('hotel_transaction')
         ->where('company_id', $this->company_id)
         ->where('invoice', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # check transaks hotel id
   function check_transaksi_hotel_id_exist($id)
   {
      $this->db->select('id')
         ->from('hotel_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function get_invoice($id)
   {
      $this->db->select('invoice')
         ->from('hotel_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $invoice = '';
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $invoice = $rows->invoice;
         }
      }
      return  $invoice;
   }
}
