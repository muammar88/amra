<?php

/**
 *  -----------------------
 *	Model airlines
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_trans_passport extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_daftar_transaksi_passport($search)
   {
      $this->db->select('id')
         ->from('passport_transaction')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('invoice', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   # get index daftar transaksi passport
   function get_index_daftar_transaksi_passport($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('pt.id, pt.invoice, pt.payer, pt.payer_identity, pt.input_date,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', ptd.name, ptd.identity_number, ptd.birth_place,
                                                ptd.birth_date, c.city_name, ptd.price, ptd.address, ptd.kartu_keluarga_number ) SEPARATOR \';\')
                              FROM passport_transaction_detail AS ptd
                              INNER JOIN 	mst_city AS c ON ptd.city_id=c.id
                              WHERE ptd.company_id="' . $this->company_id . '"  AND
                              transaction_passport_id=pt.id) AS transaksi_passport_detail')
         ->from('passport_transaction AS pt')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('pt.invoice', $search)
            ->group_end();
      }
      $this->db->order_by('pt.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $exp = explode(';', $rows->transaksi_passport_detail);
            $passport_transaksi_detail = array();
            $total_harga = 0;
            foreach ($exp as $key => $value) {
               $exp2 = explode('$', $value);
               $passport_transaksi_detail[] = array(
                  'nama_pelanggan' => $exp2[0],
                  'nomor_identitas' => $exp2[1],
                  'tempat_lahir' => $exp2[2],
                  'tanggal_lahir' => $exp2[3],
                  'nama_kota' => $exp2[4],
                  'price' => $exp2[5],
                  'address' => $exp2[6],
                  'kartu_keluarga_number' => $exp2[7]
               );
               $total_harga = $total_harga + $exp2[5];
            }
            $list[] = array(
               'id' => $rows->id,
               'invoice' => $rows->invoice,
               'payer' => $rows->payer,
               'payer_identity' => $rows->payer_identity,
               'tanggal_transaksi' => $this->date_ops->change_date_t3($rows->input_date),
               'detail' => $passport_transaksi_detail,
               'total' => $total_harga
            );
         }
      }
      return $list;
   }

   # get list city
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

   function check_invoice_passport_exist($invoice)
   {
      $this->db->select('id')
         ->from('passport_transaction')
         ->where('company_id', $this->company_id)
         ->where('invoice', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function check_id_passport_exist($id)
   {
      $this->db->select('id')
         ->from('passport_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get invoice by id
   function get_invoice_by_id($id)
   {
      $this->db->select('invoice')
         ->from('passport_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q =  $this->db->get();
      $invoice = '';
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $invoice = $rows->invoice;
         }
      }
      return $invoice;
   }
}
