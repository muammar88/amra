<?php

/**
 *  -----------------------
 *	Model artikel
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_withdraw extends CI_Model
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

   

   function get_markup_withdraw_perusahaan(){
      $this->db->select('markup_withdraw')
               ->from('company')
               ->where('id', $this->company_id);
      $q = $this->db->get();
      $markup = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
             $markup = $rows->markup_withdraw;
         }
      }
      return $markup;
   }


   function get_total_withdraw( $search, $status )
   {
      $this->db->select('wm.id')
         ->from('withdraw_member AS wm')
         ->join('personal AS p', 'wm.personal_id=p.personal_id', 'inner')
         ->join('mst_bank_transfer AS mbt', 'wm.bank_id=mbt.id', 'inner')
         ->where('wm.company_id', $this->company_id)
         ->where('wm.status_request', $status);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('wm.transaction_number', $search)
            ->group_end();
      }
      $r = $this->db->get();
      return $r->num_rows();
   }

   # get index daftar artikel
   function get_index_withdraw($limit = 6, $start = 0, $search = '', $status)
   {
      $this->db->select('wm.id, p.fullname, p.identity_number, wm.transaction_number, wm.amount, wm.status_request, wm.status_note, 
                         wm.approver, wm.account_name, wm.account_number, mbt.nama_bank, mbt.logo_bank, wm.last_update')
         ->from('withdraw_member AS wm')
         ->join('personal AS p', 'wm.personal_id=p.personal_id', 'inner')
         ->join('mst_bank_transfer AS mbt', 'wm.bank_id=mbt.id', 'inner')
         ->where('wm.company_id', $this->company_id)
         ->where('wm.status_request', $status);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('wm.transaction_number', $search)
            ->group_end();
      }
      $this->db->order_by('wm.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'fullname' => $row->fullname,
               'identity_number' => $row->identity_number,
               'transaction_number' => $row->transaction_number,
               'amount' => $row->amount,
               'status_request' => $row->status_request,
               'status_note' => $row->status_note,
               'approver' => $row->approver,
               'account_name' => $row->account_name,
               'account_number' => $row->account_number,
               'nama_bank' => $row->nama_bank,
               'logo_bank' => base_url().'image/bank_logo/'.$row->logo_bank,
               'last_update' => $row->last_update
            );
         }
      }
      return $list;
   }

   function check_id_withdraw( $id ) {
      $this->db->select('id')
         ->from('withdraw_member')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return TRUE;
      }else{
         return FALSE;
      }
   }

   # get info withdraw
   function get_info_withdraw_by_id( $id ) {
      $this->db->select('id, amount, transaction_number, personal_id')
         ->from('withdraw_member')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      //$amount = 0;
      $list = array();
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            // $amount = $rows->amount;
            $list['id'] = $rows->id;
            $list['amount'] = $rows->amount;
            $list['transaction_number'] = $rows->transaction_number;
            $list['personal_id'] = $rows->personal_id;
         }
      }
      return $list;
   }
}