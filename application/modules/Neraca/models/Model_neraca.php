<?php

/**
 *  -----------------------
 *	Model neraca
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_neraca extends CI_Model
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

   function get_index_daftar_neraca($param)
   {
      # get saldo awal
      $saldo_awal = $this->akuntansi->saldo_awal($param['periode'], $this->company_id);
      # jurnal 
      $data_jurnal = $this->akuntansi->get_jurnal_by_periode($param['periode'], $this->company_id);
      $akun_debet = $data_jurnal['akun_debet'];
      $akun_kredit = $data_jurnal['akun_kredit'];

      // echo("----------------------------");
      // echo '<pre>'; print_r($akun_debet); echo '</pre>';
      // echo '<pre>'; print_r($akun_kredit); echo '</pre>';
      // // echo '<pre>'; print_r($saldo_awal); echo '</pre>';
      // echo("----------------------------");
      // list
      $list = $this->akuntansi->total_saldo($param['periode'], $akun_debet, $akun_kredit, $this->company_id, $saldo_awal );

      // echo "<br>";
      // print_r($list);
      // echo("+++++++++++++++++++++++++++++++++++");
      // echo '<pre>'; print_r($list); echo '</pre>';
      // echo("+++++++++++++++++++++++++++++++++++");
      // echo "<br>";
      return $list;
   }

}
