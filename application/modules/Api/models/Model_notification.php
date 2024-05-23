<?php
/**
*  -----------------------
*	Model notification
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_notification extends CI_Model
{
   // private $company_id;

   function __construct(){
      parent::__construct();
   }



   # check order exist
   function check_order_exist( $order_id ){
      $this->db->select('id, transaction_type, order_id, status_code, gross_amount ')
         ->from('payment_history')
         ->where('order_id', $order_id)
         ->where('transaction_status', 'pending');
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         $row = $q->row();
         // $info['order_id'].$result['status_code'].$info['gross_amount']
         return array('num' => true,
                      'transaction_type' => $row->transaction_type,
                      'order_id' => $row->order_id,
                      'status_code' => $row->status_code,
                      'gross_amount' => $row->gross_amount);
      } else {
         return array('num' => false);
      }
   }

   # info saldo transaction
   function info_saldo_transaction($order_id){
      $this->db->select('cst.id, cst.company_id, c.saldo')
         ->from('company_saldo_transaction AS cst')
         ->join('company AS c', 'cst.company_id=c.id', 'inner')
         ->join('company_saldo_payment AS csp', 'cst.id=csp.company_saldo_transaction_id', 'inner')
         ->where('csp.order_id', $order_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['company_id'] = $rows->company_id;
            $list['saldo'] = $rows->saldo;
         }
      }
      return $list;
   }

   function get_info_subscribtion($order_id){
      $this->db->select('start_date_subscribtion, end_date_subscribtion, company_id')
         ->from('subscribtion_payment_history')
         ->where('order_id', $order_id)
         ->where('payment_status', 'process');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $list['start_date_subscribtion'] = $rows->start_date_subscribtion;
            $list['end_date_subscribtion'] = $rows->end_date_subscribtion;
            $list['company_id'] = $rows->company_id;
         }
      }
      return $list;
   }

   function get_info_company($id){
      $this->db->select('start_date_subscribtion, end_date_subscribtion')
         ->from('company')
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         $row = $q->row();
         $list['start_date_subscribtion'] = $row->start_date_subscribtion;
         $list['end_date_subscribtion'] = $row->end_date_subscribtion;
      }
      return $list;
   }


   function get_kas_company($id, $saldo_tambahan){
      $this->db->select('s.saldo, s.akun_secondary_id')
               ->from('saldo AS s')
               ->join('akun_secondary AS as', 's.akun_secondary_id=as.id', 'inner')
               ->where('s.company_id', $id)
               ->where('as.nomor_akun_secondary', '11010')
               ->where('s.periode', '0');
      $q = $this->db->get();
      $saldo_awal = 0;
      // $not_found_saldo_awal = false;
      $akun_secondary_id = 0;
      if( $q->num_rows() > 0 ){
         foreach ( $q->result() as $rows ) {
            $saldo_awal = $saldo_awal + $rows->saldo;
            $akun_secondary_id = $rows->akun_secondary_id;
         }
      }
      // else{
      //    $not_found_saldo_awal = true;
      // }

      // get debet kredit kas
      $this->db->select('saldo, akun_debet, akun_kredit')
               ->from('jurnal')
               ->where('company_id', $id)
               ->where('periode_id', '0')
               ->where('akun_debet ="11010" OR akun_kredit="11010"');
      $q = $this->db->get();
      $debet = 0;
      $kredit = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            if( $rows->akun_debet == '11010' ) {
               $debet = $debet + $rows->saldo;
            }
            if( $rows->akun_kredit == '11010' ) {
               $kredit = $kredit + $rows->saldo;
            }
         }
      }

      $total_saldo = $saldo_awal + ($debet - $kredit);
      if( $total_saldo <= 0 ) {
         if( $akun_secondary_id == 0 ) {
            # insert data saldo
            $this->db->insert('saldo', array('company_id' => $id, 
                                             'akun_secondary_id' => $this->get_akun_secondary_id($id, '11010'),
                                             'saldo' => $saldo_tambahan,
                                             'periode' => 0,
                                             'input_date' => date('Y-m-d'),
                                             'last_update' => date('Y-m-d')));
         }else{
            $this->db->where('akun_secondary_id', $akun_secondary_id)
                     ->where('company_id', $id)
                     ->where('periode', '0')
                     ->update('saldo', array('saldo' => ($saldo_awal + $saldo_tambahan), 'last_update' => date('Y-m-d')));
         }
      }
   }

   // get akun secondary id
   function get_akun_secondary_id( $company_id, $akun ) {
      $this->db->select('id')
         ->from('akun_secondary')
         ->where('company_id', $company_id)
         ->where('nomor_akun_secondary', $akun);
      $q = $this->db->get();
      $id = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $id = $rows->id;
         }
      }   
      return $id;
   }

}
