<?php

/**
 *  -----------------------
 *	Model trans tiket CUD (Create Update Delete)
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_kas_keluar_masuk extends CI_Model
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

   // get total kas keluar masuk
   function get_total_kas_keluar_masuk($search)
   {
      $this->db->select('id')
         ->from('kas_keluar_masuk')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('invoice', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get index kas keluar masuk
   function get_index_kas_keluar_masuk($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('kkm.id, kkm.invoice, kkm.dibayar_diterima, kkm.status_kwitansi, kkm.input_date,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', akun_debet, akun_kredit ) SEPARATOR \';\')
                              FROM jurnal
                              WHERE source=CONCAT(\'generaltransaksi:invoice:\', kkm.invoice)
                                    AND company_id="' . $this->company_id . '" ) AS nomor_akun')
         ->from('kas_keluar_masuk AS kkm')
         ->where('kkm.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('kkm.invoice', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         # get list akun
         $akun = $this->get_list_akun();
         # looping
         foreach ($q->result() as $row) {
            $list_akun_terlibat = array();
            if ($row->nomor_akun != '') {
               $exp = explode(';', $row->nomor_akun);
               foreach ($exp as $key => $value) {
                  $exp2 = explode('$', $value);
                  $list_akun_terlibat[] = $akun[trim($exp2[0])];
                  $list_akun_terlibat[] = $akun[trim($exp2[1])];
               }
            }
            $list[] = array(
               'id' => $row->id,
               'invoice' => $row->invoice,
               'dibayar_diterima' => $row->dibayar_diterima,
               'status_kwitansi' => $row->status_kwitansi,
               'akun_terlibat' => $list_akun_terlibat,
               'input_date' => $row->input_date
            );
         }
      }
      return $list;
   }

   function get_list_akun()
   {
      $this->db->select('id,nomor_akun_secondary, nama_akun_secondary')
         ->from('akun_secondary')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array(0 => 'Pilih semua akun');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[$row->nomor_akun_secondary] = '(' . $row->nomor_akun_secondary . ') ' . $row->nama_akun_secondary;
         }
      }
      return $list;
   }

   function check_kas_id_exist($id)
   {
      $this->db->select('id')
         ->from('kas_keluar_masuk')
         ->where('id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function get_list_nomor_akun()
   {
      $this->db->select('id,nomor_akun_secondary, nama_akun_secondary')
         ->from('akun_secondary')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array(0 => 'Pilih semua akun');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = $row->nomor_akun_secondary;
         }
      }
      return $list;
   }

   function check_invoice_exist($invoice)
   {
      $this->db->select('id')
         ->from('kas_keluar_masuk')
         ->where('company_id', $this->company_id)
         ->where('invoice', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function get_last_periode()
   {
      $this->db->select('id')
         ->from('jurnal_periode')
         ->where('company_id', $this->company_id)
         ->order_by('id', 'desc')
         ->limit('1');
      $q = $this->db->get();
      $id = '';
      if ($q->num_rows() > 0) {
         $id = $q->row()->id;
      }
      return $id;
   }

   function get_invoice_by_id($id)
   {
      $this->db->select('invoice')
         ->from('kas_keluar_masuk')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return $q->row()->invoice;
      } else {
         return '';
      }
   }

   # get info kas keluar masuk
   function get_info_kas_keluar_masuk($id)
   {
      $this->db->select('kkm.id, kkm.invoice, kkm.dibayar_diterima, kkm.receiver, kkm.status_kwitansi, kkm.input_date,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', ref, ket, akun_debet, akun_kredit, saldo ) SEPARATOR \';\')
                              FROM jurnal
                              WHERE source=CONCAT(\'generaltransaksi:invoice:\', kkm.invoice)
                                    AND company_id="' . $this->company_id . '" ) AS nomor_akun')
         ->from('kas_keluar_masuk AS kkm')
         ->where('kkm.id', $id)
         ->where('kkm.company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $exp = explode(';', $row->nomor_akun);
            $arr = array();
            $ref = '';
            $ket = '';
            foreach ($exp as $key => $value) {
               $exp2 = explode('$', $value);
               $arr[] = array(
                  'akun_debet' => $exp2[2],
                  'akun_kredit' => $exp2[3],
                  'saldo' => $exp2[4]
               );
               $ref = $exp2[0];
               $ket = $exp2[1];
            }

            $expTanggal = explode(' ', $row->input_date);
            $list['id'] = $row->id;
            $list['invoice'] = $row->invoice;
            $list['dibayar_diterima'] = $row->dibayar_diterima;
            $list['receiver'] = $row->receiver;
            $list['status_kwitansi'] = $row->status_kwitansi;
            $list['input_date'] = $expTanggal[0] . 'T' . $expTanggal[1];
            $list['akun_terlibat'] = $arr;
            $list['ref'] = $ref;
            $list['ket'] = $ket;
         }
      }
      return $list;
   }
}
