<?php
/**
*  -----------------------
*	Model riwayat deposit tabungan
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_riwayat_deposit_tabungan extends CI_Model
{
   private $company_id;

   function __construct(){
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_list_member_deposit_tabungan(){
      $this->db->select('personal_id, fullname, identity_number')
         ->from('personal')
         ->where('company_id', $this->company_id)
         ->order_by('fullname', 'asc');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list[] = array('id' => $rows->personal_id, 
                            'fullname' => $rows->fullname, 
                            'identity_number' => $rows->identity_number);
         }
      }
      return $list;
   }

   // daftar riwayat deposit
   function get_total_daftar_riwayat_deposit_tabungan($search){
      $this->db->select('dt.id')
               ->from('deposit_transaction AS dt')
               ->where('dt.company_id', $this->company_id)
               ->join('personal AS p', 'dt.personal_id=p.personal_id', 'inner')
               ->join('paket_transaction AS pt', 'dt.paket_transaction_id=pt.id', 'left')
               ->join('paket AS pkt', 'pt.paket_id=pkt.id', 'left');

      if ( array_key_exists( "tipe_transaksi" , $search ) ){
         if( $search['tipe_transaksi'] == 'tabungan_umrah' ) {
            $this->db->where( 'dt.transaction_requirement', 'paket_deposit' );
         }elseif ( $search['tipe_transaksi'] == 'deposit_saldo' ) {
            $this->db->where('transaction_requirement', 'deposit');
         }
      }
      if ( array_key_exists( "start_date",$search ) AND $search['start_date'] != ''  ) {
         $this->db->where( 'dt.input_date >=' , $search['start_date'] );
         if (array_key_exists( "end_date" , $search ) AND $search['end_date'] != '' ) {
            $this->db->where( 'dt.input_date <=', $search['end_date']. ' 23:59:59' );
         }else{
            $this->db->where( 'dt.input_date <= NOW()' );
         }
      }
      if (array_key_exists("member",$search)){
         if( $search['member'] > 0 ){
            $this->db->where('dt.personal_id', $search['member']);
         }
      }
      if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] != 'administrator') {
         $this->db->where('dt.approver', $this->session->userdata($this->config->item('apps_name'))['fullname']);
      }
      $r 	= $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_riwayat_deposit_tabungan($limit = 6, $start = 0, $search) {
      $this->db->select('dt.id, p.fullname, p.identity_number, dt.nomor_transaction, 
                        dt.debet, dt.kredit, dt.transaction_requirement, dt.info, 
                        dt.paket_transaction_id, pkt.paket_name, dt.approver, pt.no_register, dt.input_date')
               ->from('deposit_transaction AS dt')
               ->join('personal AS p', 'dt.personal_id=p.personal_id', 'inner')
               ->join('paket_transaction AS pt', 'dt.paket_transaction_id=pt.id', 'left')
               ->join('paket AS pkt', 'pt.paket_id=pkt.id', 'left')
               ->where('dt.company_id', $this->company_id);
      if ( array_key_exists( "tipe_transaksi" , $search ) ){
         if( $search['tipe_transaksi'] == 'tabungan_umrah' ) {
            $this->db->where( 'dt.transaction_requirement', 'paket_deposit' );
         }elseif ( $search['tipe_transaksi'] == 'deposit_saldo' ) {
            $this->db->where('dt.transaction_requirement', 'deposit');
         }
      }
      if ( array_key_exists( "start_date",$search ) AND $search['start_date'] != ''  ) {
         $this->db->where( 'dt.input_date >=' , $search['start_date'] );
         if (array_key_exists( "end_date" , $search ) AND $search['end_date'] != '' ) {
            $this->db->where( 'dt.input_date <=', $search['end_date']. ' 23:59:59' );
         }else{
            $this->db->where( 'dt.input_date <= NOW()' );
         }
      }
      if (array_key_exists("member",$search)){
         if( $search['member'] > 0 ){
            $this->db->where('dt.personal_id', $search['member']);
         }
      }
      if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] != 'administrator') {
         $this->db->where('dt.approver', $this->session->userdata($this->config->item('apps_name'))['fullname']);
      }
      $this->db->order_by('dt.input_date', 'desc')->limit($limit, $start);
      $q    = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array('id' => $rows->id,
                            'nomor_transaction' => $rows->nomor_transaction,
                            'fullname' => $rows->fullname,
                            'identity_number' => $rows->identity_number, 
                            'kredit' => $rows->kredit,
                            'debet' => $rows->debet,
                            'tipe_transaksi' => $rows->transaction_requirement, 
                            'paket_name' => $rows->paket_name,
                            'no_register' => $rows->no_register,
                            'penerima' => $rows->approver,
                            'info' => $rows->info,
                            'input_date' => $rows->input_date
                         );
         }
      }
      return $list;
   }


   function check_member_id($id){
      $this->db->select('personal_id')
         ->from('personal')
         ->where('company_id', $this->company_id)
         ->where('personal_id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }
}