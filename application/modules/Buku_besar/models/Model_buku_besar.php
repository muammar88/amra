<?php

/**
 *  -----------------------
 *	Model buku besar
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_buku_besar extends CI_Model
{
   private $company_id;
   private $status;
   private $content;
   private $error;
   private $write_log;
   private $kurs;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # kurs
      $this->kurs = $this->session->userdata($this->config->item('apps_name'))['kurs'];
      $this->error = 0;
      $this->write_log = 1;
   }

   # get list akun
   function get_list_akun()
   {
      $this->db->select('id,nomor_akun_secondary, nama_akun_secondary')
         ->from('akun_secondary')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[$row->nomor_akun_secondary] = '(' . $row->nomor_akun_secondary . ') ' . $row->nama_akun_secondary;
         }
      }
      return $list;
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

   # check akun id exist
   function check_akun_id_exist($nomor_akun)
   {
      $this->db->select('id')
         ->from('akun_secondary')
         ->where('company_id', $this->company_id)
         ->where('nomor_akun_secondary', $nomor_akun);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # check periode exist
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

   # get total daftar buku besar
   function get_total_daftar_buku_besar($param)
   {
      $this->db->select('id')
         ->from('jurnal')
         ->where('company_id', $this->company_id);
      if (count($param) > 0) {
         $this->db->where('periode_id', $param['periode']);
         if (isset($param['akun']) and $param['akun'] != 0) {
            $this->db->group_start()
               ->where('akun_debet', $param['akun'])
               ->or_where('akun_kredit', $param['akun'])
               ->group_end();
         }
      }
      $r = $this->db->get();
      return $r->num_rows();
   }

   # get index daftar buku besar
   function get_index_daftar_buku_besar($param)
   {

      # Serial Number
      $this->db->select('ap.sn')
         ->from('akun_secondary AS as')
         ->join('akun_primary AS ap', 'as.akun_primary_id=ap.id', 'inner')
         ->where('as.company_id', $this->company_id)
         ->where('as.nomor_akun_secondary', $param['akun']);
      $q = $this->db->get();
      $sn = '';
      if ($q->num_rows() > 0) {
         $sn = $q->row()->sn;
      }

      # get saldo awal
      $this->db->select('s.saldo')
         ->from('saldo AS s')
         ->join('akun_secondary AS as', 's.akun_secondary_id=as.id', 'inner')
         ->where('s.company_id', $this->company_id)
         ->where('s.periode', $param['periode'])
         ->where('as.nomor_akun_secondary', $param['akun']);
      $q = $this->db->get();
      $saldo = 0;
      if ($q->num_rows() > 0) {
         $saldo = $q->row()->saldo;
      }
      # jurnal
      $this->db->select('id, ref, ket, akun_kredit, akun_debet, saldo, last_update')
         ->from('jurnal')
         ->where('company_id', $this->company_id);
      if (count($param) > 0) {
         $this->db->where('periode_id', $param['periode']);
         if (isset($param['akun']) and $param['akun'] != 0) {
            $this->db->group_start()
               ->where('akun_debet', $param['akun'])
               ->or_where('akun_kredit', $param['akun'])
               ->group_end();
         }
      }
      $this->db->order_by('id', 'desc');
      $q = $this->db->get();
      $list = array();
      $total_debet = ($sn == "D" ? $saldo : 0);
      $total_kredit = ($sn == "K" ? $saldo : 0);
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $akun_kredit = $this->kurs . ' 0';
            if ($param['akun'] == $rows->akun_kredit) {
               $akun_kredit = $this->kurs . ' ' . number_format($rows->saldo);
            }

            $akun_debet = $this->kurs . ' 0';
            if ($param['akun'] == $rows->akun_debet) {
               $akun_debet = $this->kurs . ' ' . number_format($rows->saldo);
            }

            if ($sn == 'D') {
               if ($param['akun'] == $rows->akun_kredit) {
                  $total_kredit = $total_kredit + $rows->saldo;
                  $saldo = $saldo - $rows->saldo;
               } elseif ($param['akun'] == $rows->akun_debet) {
                  $total_debet = $total_debet + $rows->saldo;
                  $saldo = $saldo + $rows->saldo;
               }
            } elseif ($sn == 'K') {
               if ($param['akun'] == $rows->akun_kredit) {
                  $saldo = $saldo + $rows->saldo;
               } elseif ($param['akun'] == $rows->akun_debet) {
                  $saldo = $saldo - $rows->saldo;
               }
            }

            $list[] = array(
               'id' => $rows->id,
               'ref' => $rows->ref,
               'ket' => $rows->ket,
               'akun_kredit' => $akun_kredit,
               'akun_debet' => $akun_debet,
               'saldo' => $saldo,
               'last_update' => $rows->last_update
            );
         }
      }
      return array(
         'total_debet' => $total_debet,
         'total_kredit' => $total_kredit,
         'saldo_akhir' => $saldo,
         'list' => $list
      );
   }
}
