<?php

/**
 *  -----------------------
 *	Model laba rugi
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_laba_rugi extends CI_Model
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

   function check_periode_exist($id)
   {
      $this->db->select('id')
         ->from('jurnal_periode')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get list periode
   function get_list_periode()
   {
      $this->db->select('id, nama_periode')
         ->from('jurnal_periode')
         ->where('company_id', $this->company_id)
         ->order_by('id', 'desc');
      $q = $this->db->get();
      $list = array( 0 => 'Periode Sekarang');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[$row->id] = $row->nama_periode;
         }
      }
      return $list;
   }

   # get last periode
   function last_periode()
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

   # get index daftar laba rugi
   function get_index_daftar_laba_rugi($param)
   {
      # akun primary
      $akun_primary = array(4, 5, 6);
      $salwo_awal = array();
      $this->db->select('akun_secondary_id, saldo')
         ->from('saldo')
         ->where('company_id', $this->company_id)
         ->where('periode', $param['periode']);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $saldo_awal[$rows->akun_secondary_id] = $rows->saldo;
         }
      }
      # jurnal
      $this->db->select('akun_debet, akun_kredit, saldo')
         ->from('jurnal')
         ->where('company_id', $this->company_id)
         ->where('periode_id', $param['periode']);
      $q = $this->db->get();
      $akun_debet = array();
      $akun_kredit = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if (isset($akun_debet[$rows->akun_debet])) {
               $akun_debet[$rows->akun_debet] = $akun_debet[$rows->akun_debet] + $rows->saldo;
            } else {
               $akun_debet[$rows->akun_debet] = $rows->saldo;
            }
            if (isset($akun_kredit[$rows->akun_kredit])) {
               $akun_kredit[$rows->akun_kredit] = $akun_kredit[$rows->akun_kredit] + $rows->saldo;
            } else {
               $akun_kredit[$rows->akun_kredit] = $rows->saldo;
            }
         }
      }
      # list
      $list = array();
      foreach ($akun_primary as $key => $value) {
         $this->db->select('as.id, as.nomor_akun_secondary, as.nama_akun_secondary, ap.sn')
            ->from('akun_secondary AS as')
            ->join('akun_primary AS ap', 'as.akun_primary_id=ap.id', 'inner')
            ->where('as.company_id', $this->company_id)
            ->where('as.akun_primary_id', $value)
            ->order_by('as.nomor_akun_secondary', 'asc');
         $q = $this->db->get();
         if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
               # get saldo
               $saldo = 0;
               if (isset($saldo_awal[$rows->id])) {
                  $saldo = $saldo + $saldo_awal[$rows->id];
               }

               if ($rows->sn == 'D') {
                  # akun debet
                  if (isset($akun_debet[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo + $akun_debet[$rows->nomor_akun_secondary];
                  }
                  # akun kredit
                  if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo - $akun_kredit[$rows->nomor_akun_secondary];
                  }
               } elseif ($rows->sn == 'K') {
                  # akun debet
                  if (isset($akun_debet[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo - $akun_debet[$rows->nomor_akun_secondary];
                  }
                  # akun kredit
                  if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo + $akun_kredit[$rows->nomor_akun_secondary];
                  }
               }

               $list[$value][] = array(
                  'nomor_akun' => $rows->nomor_akun_secondary,
                  'nama_akun_secondary' => $rows->nama_akun_secondary,
                  'saldo' => $saldo
               );
            }
         }
      }
      return $list;
   }
}
