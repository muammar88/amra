<?php

/**
 *  -----------------------
 *	Model modal
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_modal extends CI_Model
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
      $list = array(0 => 'Periode Sekarang');
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

   function get_index_modal($param)
   {
      # get modal awal
      $this->db->select('s.saldo')
         ->from('saldo AS s')
         ->join('akun_secondary AS as', 's.akun_secondary_id=as.id', 'inner')
         ->where('s.company_id', $this->company_id)
         ->where('s.periode', $param['periode'])
         ->where('as.nomor_akun_secondary', '31000	');
      $q = $this->db->get();
      $modal_awal = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $modal_awal = $rows->saldo;
         }
      }
      # get penambahan modal 
      $penambahan_modal = 0;
      $pengurangan_modal = 0;
      $this->db->select('akun_debet, akun_kredit, saldo')
         ->from('jurnal')
         ->where('company_id', $this->company_id)
         ->where('periode_id', $param['periode']);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if ($rows->akun_kredit == '31000') {
               $penambahan_modal = $penambahan_modal + $rows->saldo;
            }
            if ($rows->akun_debet == '31000') {
               $pengurangan_modal = $pengurangan_modal - $rows->saldo;
            }
         }
      }
      # get ikhtisar laba rugi
      $ikhtisar_laba_rugi = $this->iktisar_laba_rugi($param);

      # get modal akhir
      $modal_akhir = $modal_awal + $penambahan_modal + $ikhtisar_laba_rugi + $pengurangan_modal;

      return array('modal_awal' => $modal_awal, 'penambahan_modal' => $penambahan_modal, 'ikhtisar_laba_rugi' => $ikhtisar_laba_rugi, 'pengurangan_modal' => $pengurangan_modal, 'modal_akhir' => $modal_akhir);
   }

   # iktisar laba rugi
   function iktisar_laba_rugi($param)
   {
      # akun primary
      $akun_primary = array(1, 2, 3);
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
      // $list = array();
      $total = array();
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

               if (isset($total[$value])) {
                  $total[$value] = $total[$value] + $saldo;
               } else {
                  $total[$value] = $saldo;
               }
            }
         }
      }
      return ($total[1] - $total[2] - $total[3]);
   }
}
