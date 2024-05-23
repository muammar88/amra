<?php

/**
 *  -----------------------
 *	Model daftar surat menyurat
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_surat_menyurat extends CI_Model
{
   private $company_id;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   // get total surat menyurat
   public function get_total_surat_menyurat($search){
      $this->db->select('id')
         ->from('riwayat_surat_menyurat')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('nomor_surat', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get index surat menyurat
   public function get_index_surat_menyurat($limit = 6, $start = 0, $search = ''){
      $this->db->select('id, nomor_surat, tipe_surat, tanggal_surat, info, tujuan, nama_petugas, input_date')
         ->from('riwayat_surat_menyurat')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('nomor_surat', $search)
            ->group_end();
      }
      $this->db->order_by('input_date', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $tipe_surat = '';
            if( $row->tipe_surat == 'rekom_paspor' ) {
               $tipe_surat = 'Rekom Pembuatan Paspor';
            }elseif ( $row->tipe_surat == 'surat_cuti' ) {
               $tipe_surat = 'Surat Cuti';
            }
            $edit_status = false;
            if ( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ) {
               $edit_status = true;
            }

            $info = array();
            if( $row->tipe_surat == 'rekom_paspor' ){
               // decode json
               $feed = json_decode($row->info);
               // get info jamaah
               $info = $this->get_info_jamaah($feed->jamaah_id);
            }elseif ( $row->tipe_surat == 'surat_cuti' ) {
               // $tipe_surat = 'Surat Cuti';
               // decode json
               $feed = json_decode($row->info);
               // get info jamaah
               $info = $this->get_info_jamaah($feed->jamaah_id);
               $info['Jabatan'] = $feed->jabatan;
               $info['Keberangkatan'] = $this->date_ops->change_date($feed->keberangkatan);
               $info['Kepulangan'] = $this->date_ops->change_date($feed->kepulangan);
            }

            // {"jamaah_id":"47","jabatan":"Analis","keberangkatan":"2023-08-19","kepulangan":"2023-08-26"}

            $list[] = array(
               'id' => $row->id,
               'nomor_surat' => $row->nomor_surat,
               'tipe_surat' => strtoupper($tipe_surat),
               'tanggal_surat' => $row->tanggal_surat,
               'info' => $info,
               'tujuan' => $row->tujuan,
               'nama_petugas' => strtoupper($row->nama_petugas), 
               'input_date' => $row->input_date,
               'edit_status' => $edit_status
            );
         }
      }
      return $list;
   }

   // get info jamaah
   function get_info_jamaah( $jamaah_id ) {
      $this->db->select('p.fullname, p.identity_number, j.id')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('j.company_id', $this->company_id)
         ->where('j.id', $jamaah_id);
      $q = $this->db->get();
      $feedBack = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $feedBack['Nama Jamaah'] = $rows->fullname;
            $feedBack['Nomor Identitas'] = $rows->identity_number;
         }
      }
      return $feedBack;
   }

   // check konfigurasi
   function check_konfigurasi_surat(){
      $this->db->select('nama_tanda_tangan, jabatan_tanda_tangan, alamat_tanda_tangan, 
                         nama_perusahaan, izin_perusahaan, kota_perusahaan, provinsi_perusahaan,
                         alamat_perusahaan, no_kontak_perusahaan, website_perusahaan,
                         email_perusahaan')
         ->from('konfigurasi_surat_menyurat')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $error = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            if( $rows->nama_tanda_tangan == '' ) {
               $error = 1;
            }
            if( $rows->jabatan_tanda_tangan == '' ) {
               $error = 1;
            }
            if( $rows->alamat_tanda_tangan == '' ) {
               $error = 1;
            }
            if( $rows->nama_perusahaan == '' ) {
               $error = 1;
            }
            if( $rows->izin_perusahaan == '' ) {
               $error = 1;
            }
            if( $rows->kota_perusahaan == '' ) {
               $error = 1;
            }
            if( $rows->provinsi_perusahaan == '' ) {
               $error = 1;
            }
            if( $rows->alamat_perusahaan == '' ) {
               $error = 1;
            }
            if( $rows->no_kontak_perusahaan == '' ) {
               $error = 1;
            }
            if( $rows->website_perusahaan == '' ) {
               $error = 1;
            }
            if( $rows->email_perusahaan == '' ) {
               $error = 1;
            }
         }
      }else{
         $error = 1;
      }
      // filter
      if( $error == 0 ) {
         return true;
      }else{
         return false;
      }
   }


   function get_info_konfigurasi_surat_menyurat(){

      $nama_tanda_tangan = '';
      $jabatan_tanda_tangan = '';
      $alamat_tanda_tangan = '';
      $nama_perusahaan = '';
      $izin_perusahaan = '';
      $kota_perusahaan = '';
      $provinsi_perusahaan = '';
      $alamat_perusahaan = '';
      $no_kontak_perusahaan = '';
      $website_perusahaan = '';
      $email_perusahaan = '';

      $this->db->select('nama_tanda_tangan, jabatan_tanda_tangan, alamat_tanda_tangan, 
                         nama_perusahaan,izin_perusahaan, kota_perusahaan, provinsi_perusahaan,  alamat_perusahaan,
                         no_kontak_perusahaan, website_perusahaan, email_perusahaan')
         ->from('konfigurasi_surat_menyurat')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $error = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            if( $rows->nama_tanda_tangan != '' ) {
               $nama_tanda_tangan = $rows->nama_tanda_tangan;
            }
            if( $rows->jabatan_tanda_tangan !=  '' ) {
               $jabatan_tanda_tangan = $rows->jabatan_tanda_tangan;
            }
            if( $rows->alamat_tanda_tangan != '' ) {
               $alamat_tanda_tangan = $rows->alamat_tanda_tangan;
            }
            if( $rows->nama_perusahaan != '' ) {
               $nama_perusahaan = $rows->nama_perusahaan;
            }
            if( $rows->izin_perusahaan != '' ) {
               $izin_perusahaan = $rows->izin_perusahaan;
            }
            if( $rows->kota_perusahaan != '' ) {
               $kota_perusahaan = $rows->kota_perusahaan;
            }
            if( $rows->provinsi_perusahaan != '' ) {
               $provinsi_perusahaan = $rows->provinsi_perusahaan;
            }
            if( $rows->alamat_perusahaan != '' ) {
               $alamat_perusahaan = $rows->alamat_perusahaan;
            }
            if( $rows->no_kontak_perusahaan != '' ) {
               $no_kontak_perusahaan = $rows->no_kontak_perusahaan;
            }
            if( $rows->website_perusahaan != '' ) {
               $website_perusahaan = $rows->website_perusahaan;
            }
            if( $rows->email_perusahaan != '' ) {
               $email_perusahaan = $rows->email_perusahaan;
            }
         }
      }

      return array('nama_tanda_tangan' => $nama_tanda_tangan, 
                   'jabatan_tanda_tangan' => $jabatan_tanda_tangan, 
                   'alamat_tanda_tangan' => $alamat_tanda_tangan,
                   'nama_perusahaan' => $nama_perusahaan,
                   'izin_perusahaan' => $izin_perusahaan,
                   'kota_perusahaan' => $kota_perusahaan,
                   'provinsi_perusahaan' => $provinsi_perusahaan,
                   'alamat_perusahaan' => $alamat_perusahaan,
                   'no_kontak_perusahaan' => $no_kontak_perusahaan,
                   'website_perusahaan' => $website_perusahaan, 
                   'email_perusahaan' => $email_perusahaan);
   }

   // get jamaah
   function get_jamaah(){
      $this->db->select('p.fullname, p.identity_number, j.id')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('j.company_id', $this->company_id);
      $q = $this->db->get();
      $list = array(0 => 'Pilih Jamaah');
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list[$rows->id] = $rows->fullname.' ('.$rows->identity_number.')'; 
         }
      }
      return $list;
   }

   // check jamaah id exist
   function check_jamaah_id_exist($jamaah_id){
      $this->db->select('j.id')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('j.company_id', $this->company_id)
         ->where('j.id', $jamaah_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   // nomor surat menyurat exit
   function check_nomor_surat_exit( $nomor_surat ){
      $this->db->select('id')
         ->from('riwayat_surat_menyurat')
         ->where('company_id', $this->company_id)
         ->where('nomor_surat', $nomor_surat);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   // check riwayat surat menyurat id
   function check_riwayat_surat_menyurat_exist($id){
      $this->db->select('id')
         ->from('riwayat_surat_menyurat')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   // get nomor surat by id
   function get_nomor_surat_by_id($id){
      $this->db->select('nomor_surat, tipe_surat')
         ->from('riwayat_surat_menyurat')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      // $nomor_surat = '';
      $arr = array();
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         foreach ( $q->result() as $rows ) {
            $arr['nomor_surat'] = $rows->nomor_surat;
            $arr['tipe_surat'] = $rows->tipe_surat;
         }
      }
      return $arr;
   }

}