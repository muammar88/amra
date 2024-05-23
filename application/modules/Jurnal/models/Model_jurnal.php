<?php

/**
 *  -----------------------
 *	Model daftar jurnal
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_jurnal extends CI_Model
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

   # get list periodeModel_jurnal
   function get_list_periode()
   {
      $this->db->select('id, nama_periode')
         ->from('jurnal_periode')
         ->where('company_id', $this->company_id)
         ->order_by('id', 'desc');
      $q = $this->db->get();
      $list = array('0' => 'Periode Sekarang');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[$row->id] = $row->nama_periode;
         }
      }

      // print_r($list);
      return $list;
   }

   # get last periode
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

   function get_total_daftar_jurnal($param)
   {
      $id = $this->get_last_periode();
      $this->db->select('id')
         ->from('jurnal')
         ->where('company_id', $this->company_id);
      if ($param['periode'] != 0) {
         $this->db->where('periode_id', $param['periode']);
      } else {
         $this->db->where('periode_id', 0);
      }
      if ($param['akun'] != 0) {
         $this->db->group_start()
            ->or_where('akun_debet', $param['akun'])
            ->or_where('akun_kredit', $param['akun'])
            ->group_end();
      }
      if ($param['tanggal'] != '' or $param['tanggal'] != null or !empty($param['tanggal'])) {
         $this->db->group_start()
            ->like('input_date', $param['tanggal'])
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_akun()
   {
      $this->db->select('nomor_akun_secondary,nama_akun_secondary')
         ->from('akun_secondary')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[$rows->nomor_akun_secondary] = $rows->nama_akun_secondary;
         }
      }
      return $list;
   }


   function get_index_daftar_jurnal($limit = 6, $start = 0, $param)
   {
      $id = $this->get_last_periode();
      # get list akun
      $list_akun = $this->get_akun();
      # get jurnal
      $this->db->select('id, ref, ket, akun_debet, akun_kredit, saldo, periode_id, input_date')
         ->from('jurnal')
         ->where('company_id', $this->company_id);
      if ($param['periode'] != 0) {
         $this->db->where('periode_id', $param['periode']);
      } else {
         $this->db->where('periode_id', 0);
      }
      if ($param['akun'] != 0) {
         $this->db->group_start()
            ->or_where('akun_debet', $param['akun'])
            ->or_where('akun_kredit', $param['akun'])
            ->group_end();
      }
      if ($param['tanggal'] != '' or $param['tanggal'] != null or !empty($param['tanggal'])) {
         $this->db->group_start()
            ->like('input_date', $param['tanggal'])
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'input_date' => $row->input_date,
               'ref' => $row->ref,
               'ket' => $row->ket,
               'akun_debet' => $row->akun_debet,
               'nama_akun_debet' => isset($list_akun[$row->akun_debet]) ? $list_akun[$row->akun_debet] : '-',
               'akun_kredit' => $row->akun_kredit,
               'nama_akun_kredit' => isset($list_akun[$row->akun_kredit]) ? $list_akun[$row->akun_kredit] : '-',
               'saldo' => $row->saldo,
               'periode' => $row->periode_id
            );
         }
      }
      return $list;
   }

   function check_jurnal_id_exist($id)
   {
      $this->db->select('id')
         ->from('jurnal')
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }
}
