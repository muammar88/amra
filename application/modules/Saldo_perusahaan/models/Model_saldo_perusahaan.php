<?php

/**
 *  -----------------------
 *	Model saldo perusahaan
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_saldo_perusahaan extends CI_Model
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


   #  get total daftar riwayat mutasi saldo
   function get_total_daftar_riwayat_mutasi_saldo($search)
   {
      $this->db->select('id')
         ->from('company_saldo_transaction')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('ket', $search)
            ->or_like('request_type', $search)
            ->group_end();
      }
      $r = $this->db->get();
      return $r->num_rows();
   }

   # get total index daftar riwayat mutasi saldo
   function get_index_daftar_riwayat_mutasi_saldo($limit = 6, $start = 0, $search = '')
   {

      $this->db->select('id, saldo, request_type, ket, status, last_update')
         ->from('company_saldo_transaction')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('ket', $search)
            ->or_like('request_type', $search)
            ->group_end();
      }
      $this->db->order_by('last_update', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $kode = '';
            $status = '';
            if( $row->request_type != 'deposit' ) {
               $exp = explode(':', $row->ket);
               $kode = '#'.$exp[2]; 
               if( $row->request_type == 'pruchase' ) {
                  $status = $this->get_status($exp[2]);
               }
            }else{
               $status = $row->status;
            }

            $list[] = array(
               'id' => $row->id,
               'kode' => $kode,
               'saldo' => $row->saldo,
               'request_type' => $row->request_type,
               'ket' => $row->ket,
               'status' => $status,
               'last_update' => $row->last_update,
            );
         }
      }
      return $list;
   }

   function get_status($kode){
      $this->db->select('status')
               ->from('ppob_transaction_history')
               ->where('transaction_code', $kode);
      $q = $this->db->get();
      $status = '';
      if ( $q->num_rows() > 0 ) { 
         foreach ( $q->result() as $rows ) {
            $status = $rows->status;
         }
      }
      return $status;
   }

   #  get total daftar riwayat tambah saldo
   function get_total_daftar_riwayat_tambah_saldo($search)
   {
      $this->db->select('id')
         ->from('request_tambah_saldo_company')
         ->where('company_id', $this->company_id);
         // ->where('status', $status);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('kode', $search)
            ->group_end();
      }
      $r = $this->db->get();
      return $r->num_rows();
   }

   # get total index daftar riwayat tambah saldo
   function get_index_daftar_riwayat_tambah_saldo($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('id, kode, bank, nomor_akun_bank, nama_akun_bank, biaya, kode_biaya, status, status_kirim, waktu_kirim, alasan_tolak, last_update')
         ->from('request_tambah_saldo_company')
         ->where('company_id', $this->company_id);
         // ->where('status', $status);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('kode', $search)
            ->group_end();
      }
      $this->db->order_by('last_update', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'kode' => $row->kode,
               'bank' => $row->bank,
               'nomor_akun_bank' => $row->nomor_akun_bank,
               'nama_akun_bank' => $row->nama_akun_bank,
               'total_biaya' => $row->biaya + $row->kode_biaya,
               'status' => $row->status,
               'status_kirim' => $row->status_kirim,
               'waktu_kirim' => $row->waktu_kirim,
               'alasan_tolak' => $row->alasan_tolak,
               'last_update' => $row->last_update,
            );
         }
      }
      return $list;
   }

   # check request proses
   function check_request_proses($id = ''){
      $this->db->select('id')
               ->from('request_tambah_saldo_company')
               ->where('company_id', $this->company_id)
               ->where('status', 'proses');
      if( $id != '' ) {
         $this->db->where('id != ', $id);
      }
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   // get bank admin
   function get_bank_admin(){
      $this->db->select('*')
               ->from('admin_bank');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) { 
         foreach ( $q->result() as $rows ) {
            $list[] = array( 'id' => $rows->id, 
                             'bank_name' => $rows->bank_name, 
                             'account_bank_name' => $rows->account_bank_name, 
                             'account_bank_number' => $rows->account_bank_number);
         }
      }
      return $list;         
   }

   // check bank transfer
   function check_bank_transfer($id){
      $this->db->select('id')
               ->from('admin_bank')
               ->where('id', $id);
      $q = $this->db->get();
      if ( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function get_info_bank($id){
      $this->db->select('*')
               ->from('admin_bank')
               ->where('id', $id);
      $q = $this->db->get();
      $list  = array();
      if ( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list['bank_name'] = $rows->bank_name;
            $list['account_bank_name'] = $rows->account_bank_name;
            $list['account_bank_number'] = $rows->account_bank_number;
         }
      }
      return $list;
   }

   // check id request tambah saldo
   function check_id_request_tambah_saldo($id){
      $this->db->select('id')
               ->from('request_tambah_saldo_company')
               ->where('company_id', $this->company_id)
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   function get_info_edit_riwayat($id){
      $this->db->select('*')
               ->from('request_tambah_saldo_company')
               ->where('company_id', $this->company_id)
               ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list['id'] = $rows->id;
            $list['bank_id'] = $this->get_bank_id( $rows->bank );
            $list['biaya'] = $rows->biaya;
            $list['kode_biaya'] = $rows->kode_biaya;
         }
      }
      return $list;         
   }

   // get bank id
   function get_bank_id($bank_name) {
      $this->db->select('id')
               ->from('admin_bank')
               ->where('bank_name', $bank_name);
      $q = $this->db->get();
      $bank_id = 0;
      if ( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $bank_id = $rows->id;
         }
      }
      return $bank_id;
   }

}
