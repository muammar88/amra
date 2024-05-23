<?php
/**
*  -----------------------
*	Model trans member
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_trans_member extends CI_Model
{
   private $company_id;

   function __construct(){
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   # get total trans member
   function get_total_trans_member($status, $search){
      $this->db->select('mtr.id')
               ->from('member_transaction_request AS mtr')
               ->join('personal AS p', 'mtr.personal_id=p.personal_id', 'inner')
               ->where('p.company_id', $this->company_id);
      if( $status != 'semua' ) {
         $this->db->where('mtr.status_request', $status);
      }
		if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
		}
		$r 	= $this->db->get();
		return $r->num_rows();
   }

   # get index trans member
   function get_index_trans_member( $limit = 6, $start = 0, $status, $search = '' ){
      $this->db->select('mtr.id, mtr.ref, mtr.amount, mtr.activity_type, p.fullname, p.identity_number,
                         mtr.status_request, mtr.status_note, mtr.approver, mtr.payment_source')
               ->from('member_transaction_request AS mtr')
               ->join('personal AS p', 'mtr.personal_id=p.personal_id', 'inner')
               ->where('p.company_id', $this->company_id);
      if( $status != 'semua' ) {
         $this->db->where('mtr.status_request', $status);
      }
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $this->db->order_by('mtr.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id,
                            'ref' => $row->ref,
                            'amount' => $row->amount,
                            'activity_type' => $row->activity_type,
                            'fullname' => $row->fullname,
                            'identity_number' => $row->identity_number,
                            'status_request' => $row->status_request,
                            'status_note' => $row->status_note,
                            'approver' => $row->approver,
                            // 'transfer_evidence' => $row->transfer_evidence,
                            'payment_source' => $row->payment_source);
         }
      }
      return $list;
   }

   function check_trans_member_id($id){
      $this->db->select('id')
         ->from('member_transaction_request')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # get info member transaction
   function get_info_member_transaction($id){
      $this->db->select('mtr.id, mtr.personal_id, mtr.ref, mtr.amount,  mtr.activity_type, mtr.status_request,
                         mtr.status_note, mtr.payment_source, mtr.transfer_evidence, p.fullname, p.no_hp, p.address')
         ->from('member_transaction_request AS mtr')
         ->join('personal AS p', 'mtr.personal_id=p.personal_id', 'inner')
         ->where('mtr.company_id', $this->company_id)
         ->where('mtr.id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $feedBack['id'] =  $rows->id;
            $feedBack['personal_id'] =  $rows->personal_id;
            $feedBack['ref'] =  $rows->ref;
            $feedBack['amount'] =  $rows->amount;
            $feedBack['activity_type'] =  $rows->activity_type;
            $feedBack['status_request'] =  $rows->status_request;
            $feedBack['status_note'] =  $rows->status_note;
            $feedBack['payment_source'] =  $rows->payment_source;
            $feedBack['transfer_evidence'] =  $rows->transfer_evidence;
            $feedBack['fullname'] =  $rows->fullname;
            $feedBack['no_hp'] =  $rows->no_hp;
            $feedBack['address'] =  $rows->address;
            if( $rows->activity_type == 'claim' ) {
               $feedBack['nomor_register_paket_transaksi'] =  $this->get_no_register_fee_keagenan($rows->ref);
            }elseif ( $rows->activity_type == 'payment_paket' ) {
               $info_paket_transaction = $this->get_no_register_payment_paket($rows->ref);
               $feedBack['nomor_register'] = $info_paket_transaction['nomor_register'];
               $feedBack['paket_transaction_id'] =  $info_paket_transaction['paket_transaction_id'];
               $feedBack['payment_methode'] =  $info_paket_transaction['payment_methode'];

            }
         }
      }
      return $feedBack;
   }

   function get_no_register_payment_paket($ref){
      $feedBack = '';
      if( $ref != '' ){
         $fee_id = array();
         $exp = explode(':', $ref);
         $paket_transaction_id = $exp[1];
         $this->db->select('no_register, payment_methode')
            ->from('paket_transaction')
            ->where('company_id', $this->company_id)
            ->where_in('id', $paket_transaction_id);
         $r = $this->db->get();
         if( $r->num_rows() > 0 ){
            $i = 0;
            foreach ($r->result() as $rows) {
               $feedBack = array('nomor_register' => $rows->no_register,
                                 'payment_methode' => $rows->payment_methode,
                                 'paket_transaction_id' => $paket_transaction_id);
            }
         }
      }
      return $feedBack;
   }

   function get_no_register_fee_keagenan($ref){
      $feedBack = '';
      if( $ref != '' ){
         $fee_id = array();
         $exp = explode(':', $ref);
         if(strpos($exp[1], '#') !== false){
            $fee_id = explode('#', $exp);
         } else{
            $fee_id[] = $exp[1];
         }
         $this->db->select('pt.no_register')
            ->from('fee_keagenan AS fk')
            ->join('paket_transaction AS pt', 'fk.paket_transaction_id=pt.id', 'inner')
            ->where('fk.company_id', $this->company_id)
            ->where_in('fk.id', $fee_id);
         $r = $this->db->get();
         if( $r->num_rows() > 0 ){
            $i = 0;
            foreach ($r->result() as $rows) {
               if( $i == 0 ){
                  $feedBack = $rows->no_register;
               }else{
                  $feedBack = ','.$rows->no_register;
               }
               $i++;
            }
         }
      }
      return  $feedBack;
   }

   function get_info_trans_paket($id){
      $this->db->select('ptmr.id, ptmr.no_register, ptmr.paket_id, ptmr.paket_type_id, ptmr.diskon, ptmr.agen_id, ptmr.payment_methode,
                        ptmr.total_mahram_fee, ptmr.mahram, ptmr.tenor, ptmr.down_payment, ptmr.total_paket_price, ptmr.price_per_pax,
                        ptmr.start_date, ptmr.input_date, ptmr.last_update,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', ptjmr.jamaah_id, ptjmr.leader ) SEPARATOR \';\')
                              FROM paket_transaction_jamaah_member_request AS ptjmr
                              WHERE ptjmr.company_id="'.$this->company_id.'" AND ptjmr.paket_transaction_id=ptmr.id) AS jamaah')
               ->from('paket_transaction_member_request AS ptmr')
               ->where('ptmr.company_id', $this->company_id)
               ->where('ptmr.id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $row) {
            $jamaah = array();
            $leader = 0;
            if( $row->jamaah != '' ){
               foreach ( explode(';', $row->jamaah) as $key => $value) {
                  $exp = explode('$', $value);
                  $jamaah[] = $exp[0];
                  if( $exp[1] == '1' ) {
                     $leader = $exp[0];
                  }
               }
            }
            $feedBack['id'] = $row->id;
            $feedBack['no_register'] = $row->no_register;
            $feedBack['paket_id'] = $row->paket_id;
            $feedBack['paket_type_id'] = $row->paket_type_id;
            $feedBack['diskon'] = $row->diskon;
            $feedBack['agen_id'] = $row->agen_id;
            $feedBack['payment_methode'] = $row->payment_methode;
            $feedBack['total_mahram_fee'] = $row->total_mahram_fee;
            $feedBack['mahram'] = $row->mahram;
            $feedBack['tenor'] = $row->tenor;
            $feedBack['down_payment'] = $row->down_payment;
            $feedBack['total_paket_price'] = $row->total_paket_price;
            $feedBack['price_per_pax'] = $row->price_per_pax;
            $feedBack['start_date'] = $row->start_date;
            $feedBack['input_date'] = $row->input_date;
            $feedBack['last_update'] = $row->last_update;
            $feedBack['jamaah'] = $jamaah;
            $feedBack['jamaah_leader'] = $leader;
         }
      }
      return $feedBack;
   }

   function get_info_keagenan($paket_id, $agen_id){
      $feedBack = array();
      # get fee agen and cabang
      $this->db->select('fee_agen, fee_cabang')
               ->from('paket')
               ->where('company_id', $this->company_id)
               ->where('id', $paket_id);
      $q = $this->db->get();
      $fee_agen = 0;
      $fee_cabang = 0;
      if( $q->num_rows() > 0  ){
         $row = $q->row();
         $fee_agen = $row->fee_agen;
         $fee_cabang = $row->fee_cabang;
      }
      # get upline
      $this->db->select('ag.level_agen, ag.upline,
                           (SELECT a.level_agen
                              FROM agen AS a
                              WHERE a.company_id="'.$this->company_id.'"
                              AND a.id=ag.upline) AS level_upline')
               ->from('agen AS ag')
               ->where('ag.company_id', $this->company_id)
               ->where('ag.id', $agen_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         $row = $q->row();
         $feedBack[] = array('company_id' => $this->company_id,
                             'agen_id' => $agen_id,
                             'level_agen' => $row->level_agen,
                             'fee' => $row->level_agen == 'agen' ? $fee_agen : ( $row->level_agen == 'cabang' ? $fee_cabang : 0 ),
                             'input_date' => date('Y-m-d'),
                             'last_update' => date('Y-m-d'));
         # only cabang can get fee free
         if( $row->upline != 0 AND $row->level_agen == 'agen' AND $row->level_upline == 'cabang' ) {
            $feedBack[] = array('company_id' => $this->company_id,
                                'agen_id' => $row->upline,
                                'level_agen' => $row->level_upline,
                                'fee' => $fee_cabang,
                                'input_date' => date('Y-m-d'),
                                'last_update' => date('Y-m-d'));
         }
      }
      return $feedBack;
   }

   # get infoo member transaction request
   function get_info_member($id){
      $this->db->select('payment_source, transfer_evidence')
         ->from('member_transaction_request')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $row) {
            $feedBack = array('payment_source' => $row->payment_source,
                              'bukti' => $row->transfer_evidence);
         }
      }
      return $feedBack;
   }

}
