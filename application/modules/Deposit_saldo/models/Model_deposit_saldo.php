<?php

/**
 *  -----------------------
 *	Model deposit saldo
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_deposit_saldo extends CI_Model
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

   function get_total_deposit_saldo($search)
   {
      $this->db->select('dt.id')
         ->from('deposit_transaction AS dt')
         ->join('personal AS p', 'dt.personal_id=p.personal_id', 'inner')
         ->where('dt.company_id', $this->company_id)
         ->where('dt.transaction_requirement','deposit');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->or_like('dt.nomor_transaction', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_deposit_saldo($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('dt.id, p.fullname, p.identity_number, dt.debet, dt.kredit, dt.saldo_sebelum, dt.saldo_sesudah,
                         dt.approver, dt.info, dt.nomor_transaction, dt.last_update')
         ->from('deposit_transaction AS dt')
         ->join('personal AS p', 'dt.personal_id=p.personal_id', 'inner')
         ->where('dt.company_id', $this->company_id)
         ->where('dt.transaction_requirement','deposit');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->or_like('dt.nomor_transaction', $search)
            ->group_end();
      }
      $this->db->order_by('dt.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'nomor_transaksi' => $row->nomor_transaction,
               'fullname' => $row->fullname,
               'identity_number' => $row->identity_number,
               'debet' => $row->debet,
               'kredit' => $row->kredit,
               'penerima' => $row->approver,
               'saldo_sebelum' => $row->saldo_sebelum, 
               'saldo_sesudah' => $row->saldo_sesudah,
               'info' => $row->info,
               'waktu_transaksi' => $row->last_update
            );
         }
      }
      return $list;
   }

   function get_list_member()
   {
      $this->db->select('personal_id, fullname, identity_number')
         ->from('personal')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list_member = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list_member[] = array(
               'id' => $row->personal_id,
               'fullname' => $row->fullname,
               'nomor_identitas' => $row->identity_number
            );
         }
      }
      return $list_member;
   }

   function check_member_id_exist($personal_id)
   {
      $this->db->select('personal_id')
         ->from('personal')
         ->where('company_id', $this->company_id)
         ->where('personal_id', $personal_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function check_nomor_transaksi($nomor_transaksi)
   {
      $this->db->select('id')
         ->from('deposit_transaction')
         ->where('company_id', $this->company_id)
         ->where('nomor_transaction', $nomor_transaksi);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function check_id_deposit_transaksi($id)
   {
      $this->db->select('id')
         ->from('deposit_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   // nomor transaction deposit
   function get_nomor_transaksi_deposit_saldo($id){
      $this->db->select('nomor_transaction')
         ->from('deposit_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $id)
         ->where('transaction_requirement', 'deposit');
      $q = $this->db->get();
      $nomor_transaction = '';
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $nomor_transaction = $rows->nomor_transaction;
         }
      }
      return $nomor_transaction;   
   }

   # saldo
   function info_deposit_tabungan($company_id, $personal_id)
   {
        $this->db->select('dt.debet, dt.kredit, dt.transaction_requirement')
                 ->from('deposit_transaction AS dt')
                 ->where('dt.personal_id', $personal_id)
                 ->where('dt.company_id', $company_id)
                 ->order_by('dt.id', 'desc');
        $q = $this->db->get();

        $debet_deposit = 0;
        $kredit_deposit = 0;

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                if( $rows->transaction_requirement == 'deposit' ){
                    if( $rows->debet != 0 ) {
                        $debet_deposit = $debet_deposit + $rows->debet;    
                    }
                    if( $rows->kredit != 0 ){
                        $kredit_deposit = $kredit_deposit + $rows->kredit;    
                    }
                }
            }
        }

        $total_deposit = $debet_deposit - $kredit_deposit;

        $this->db->select('dt.debet, dt.kredit, dt.transaction_requirement')
                 ->from('deposit_transaction AS dt')
                 ->join('pool_deposit_transaction AS pdt', 'dt.id=pdt.deposit_transaction_id', 'inner')
                 ->join('pool AS p', 'pdt.pool_id=p.id', 'inner')
                 ->where('dt.personal_id', $personal_id)
                 ->where('dt.company_id', $company_id)
                 ->where('p.active', 'active')
                 ->order_by('dt.id', 'desc');
        $q = $this->db->get();

        $debet_tabungan = 0;
        $kredit_tabungan = 0;

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                if ( $rows->transaction_requirement == 'paket_deposit' ) {
                    if( $rows->debet != 0 ) {
                        $debet_tabungan = $debet_tabungan + $rows->debet;    
                    }
                    if( $rows->kredit != 0 ){
                        $kredit_tabungan = $kredit_tabungan + $rows->kredit;    
                    }
                }
            }
        }

        $total_tabungan = $debet_tabungan - $kredit_tabungan;

        // get markup withdraw
        $this->db->select('markup_withdraw')
                 ->from('company')
                 ->where('id', $company_id);
        $q = $this->db->get();
        $markup_withdraw = 0;
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $markup_withdraw = $rows->markup_withdraw;
            }
        }

        # return
        return array('deposit' => $total_deposit, 'tabungan' => $total_tabungan, 'markup_withdraw' => $markup_withdraw);
   }
}
