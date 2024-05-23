<?php

/**
 *  -----------------------
 *	Model akun
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_neraca_lajur extends CI_Model
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

   # get index daftar lajur
   function get_index_daftar_neraca_lajur($param)
   {
      # periode id
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
      # Serial Number
      $this->db->select('ap.sn, ap.pos, as.id, as.nomor_akun_secondary, as.nama_akun_secondary')
         ->from('akun_secondary AS as')
         ->join('akun_primary AS ap', 'as.akun_primary_id=ap.id', 'inner')
         ->where('as.company_id', $this->company_id)
         ->order_by('as.nomor_akun_secondary', 'asc');
      $q = $this->db->get();
      $list = array();
      $total = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {

            $saldo_awal_debet =  $rows->sn == "D" ? (!isset($saldo_awal[$rows->id]) ? 0 : $saldo_awal[$rows->id]) : 0;
            $saldo_awal_kredit = $rows->sn == "K" ? (!isset($saldo_awal[$rows->id]) ? 0 : $saldo_awal[$rows->id]) : 0;
            $penyesuaian_akun_debet = isset($akun_debet[$rows->nomor_akun_secondary]) ? $akun_debet[$rows->nomor_akun_secondary] : 0;
            $penyesuaian_akun_kredit = isset($akun_kredit[$rows->nomor_akun_secondary]) ? $akun_kredit[$rows->nomor_akun_secondary] : 0;

            if ($rows->pos == 'NRC') {
               $neraca_debet = $saldo_awal_debet + $penyesuaian_akun_debet;
               $neraca_kredit = $saldo_awal_kredit + $penyesuaian_akun_kredit;
               $laba_debet = 0;
               $laba_kredit = 0;
            } else {
               $neraca_debet = 0;
               $neraca_kredit = 0;
               $laba_debet = $saldo_awal_debet + $penyesuaian_akun_debet;
               $laba_kredit = $saldo_awal_kredit + $penyesuaian_akun_kredit;
            }

            # total
            $total['saldo_awal_debet'] = isset($total['saldo_awal_debet']) ? ($total['saldo_awal_debet'] + $saldo_awal_debet) : $saldo_awal_debet;
            $total['saldo_awal_kredit'] = isset($total['saldo_awal_kredit']) ? ($total['saldo_awal_kredit'] + $saldo_awal_kredit) : $saldo_awal_kredit;
            $total['penyesuaian_akun_debet'] = isset($total['penyesuaian_akun_debet']) ? ($total['penyesuaian_akun_debet'] + $penyesuaian_akun_debet) : $penyesuaian_akun_debet;
            $total['penyesuaian_akun_kredit'] = isset($total['penyesuaian_akun_kredit']) ? ($total['penyesuaian_akun_kredit'] + $penyesuaian_akun_kredit) : $penyesuaian_akun_kredit;
            $total['saldo_disesuaikan_debet'] = isset($total['saldo_disesuaikan_debet']) ? ($total['saldo_disesuaikan_debet'] + ($saldo_awal_debet + $penyesuaian_akun_debet)) : ($saldo_awal_debet + $penyesuaian_akun_debet);
            $total['saldo_disesuaikan_kredit'] = isset($total['saldo_disesuaikan_kredit']) ? ($total['saldo_disesuaikan_kredit'] + ($saldo_awal_kredit + $penyesuaian_akun_kredit)) : ($saldo_awal_kredit + $penyesuaian_akun_kredit);
            $total['neraca_debet'] = isset($total['neraca_debet']) ? ($total['neraca_debet'] + $neraca_debet) : $neraca_debet;
            $total['neraca_kredit'] = isset($total['neraca_kredit']) ? ($total['neraca_kredit'] + $neraca_kredit) : $neraca_kredit;
            $total['laba_debet'] = isset($total['laba_debet']) ? ($total['laba_debet'] + $laba_debet) : $laba_debet;
            $total['laba_kredit'] = isset($total['laba_kredit']) ? ($total['laba_kredit'] + $laba_kredit) : $laba_kredit;

            $list[] = array(
               'sn' => $rows->sn,
               'nomor_akun_secondary' => $rows->nomor_akun_secondary,
               'nama_akun_secondary' => $rows->nama_akun_secondary,
               'saldo_awal_debet' => $saldo_awal_debet,
               'saldo_awal_kredit' => $saldo_awal_kredit,
               'penyesuaian_akun_debet' => $penyesuaian_akun_debet,
               'penyesuaian_akun_kredit' => $penyesuaian_akun_kredit,
               'saldo_disesuaikan_debet' => ($saldo_awal_debet + $penyesuaian_akun_debet),
               'saldo_disesuaikan_kredit' => ($saldo_awal_kredit + $penyesuaian_akun_kredit),
               'neraca_debet' => $neraca_debet,
               'neraca_kredit' => $neraca_kredit,
               'laba_debet' => $laba_debet,
               'laba_kredit' => $laba_kredit
            );
         }
      }
      return array('list' => $list, 'total' => $total);
   }
}
