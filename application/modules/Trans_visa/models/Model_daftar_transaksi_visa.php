<?php

/**
 *  -----------------------
 *	Model daftar transaksi visa
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_transaksi_visa extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_daftar_transaksi_visa($search)
   {
      $this->db->select('id')
         ->from('visa_transaction')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('invoice', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_transaksi_visa($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('vt.id, vt.invoice, vt.payer, vt.payer_identity, vt.input_date,
                        (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', vtd.name, vtd.identity_number, vtd.gender,
                              vtd.birth_place, vtd.birth_date, rt.request_name,
                              c.city_name, vtd.passport_number, vtd.date_issued, vtd.price ) SEPARATOR \';\')
                           FROM visa_transaction_detail AS vtd
                           INNER JOIN request_type AS rt ON vtd.request_id=rt.id
                           INNER JOIN 	mst_city AS c ON vtd.profession_city=c.id
                           WHERE vtd.company_id="' . $this->company_id . '"  AND
                           transaction_visa_id=vt.id) AS transaksi_visa_detail')
         ->from('visa_transaction AS vt')
         ->where('vt.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('vt.invoice', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $exp = explode(';', $row->transaksi_visa_detail);
            $visa_transaksi_detail = array();
            $total_harga = 0;
            foreach ($exp as $key => $value) {
               $exp2 = explode('$', $value);
               $visa_transaksi_detail[] = array(
                  'nama_pelanggan' => $exp2[0],
                  'nomor_identitas' => $exp2[1],
                  'jenis_kelamin' => $exp2[2],
                  'tempat_lahir' => $exp2[3],
                  'tanggal_lahir' => $exp2[4],
                  'nama_permohonan' => $exp2[5],
                  'nama_kota' => $exp2[6],
                  'nomor_passport' => $exp2[7],
                  'berlaku_sd' => $exp2[8],
                  'price' => $exp2[9]
               );
               $total_harga = $total_harga + $exp2[9];
            }

            $list[] = array(
               'id' => $row->id,
               'invoice' => $row->invoice,
               'payer' => $row->payer,
               'payer_identity' => $row->payer_identity,
               'tanggal_transaksi' => $row->input_date,
               'total' => $total_harga,
               'detail' => $visa_transaksi_detail
            );
         }
      }
      return $list;
   }

   # get request type
   function get_request_type()
   {
      $this->db->select('id, request_name')
         ->from('request_type');
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[$row->id] = $row->request_name;
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

   function check_transaksi_visa_id_exist($id)
   {
      $this->db->select('id')
         ->from('visa_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }


   function get_list_id_request_type()
   {
      $this->db->select('id')
         ->from('request_type');
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = $row->id;
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

   function check_invoice_exist($invoice)
   {
      $this->db->select('id')
         ->from('visa_transaction')
         ->where('company_id', $this->company_id)
         ->where('invoice', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function get_invoice($id)
   {
      $this->db->select('invoice')
         ->from('visa_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $invoice = '';
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $invoice = $rows->invoice;
         }
      }
      return $invoice;
   }
}
