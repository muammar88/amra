<?php
/**
 *  -----------------------
 *	Model ppob
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_ppob extends CI_Model
{
   private $company_id;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_daftar_trans_ppob($search){
      $this->db->select('pth.id')
               ->from('ppob_transaction_history AS pth')
               ->join('ppob_transaction_history_company AS pthc', 'pth.id=pthc.ppob_transaction_history_id', 'inner')
               ->join('personal AS p', 'pthc.personal_id=p.personal_id', 'inner')
               ->where('pthc.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('pth.transaction_code', $search)
            ->or_like('pth.product_code', $search)
            ->or_like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_trans_ppob($limit = 6, $start = 0, $search = ''){
      $this->db->select('pth.id, pth.transaction_code, pth.product_code, pth.application_price, pth.status, pth.created_at, 
                         pthc.company_markup, pthc.company_price, p.fullname, p.identity_number')
               ->from('ppob_transaction_history AS pth')
               ->join('ppob_transaction_history_company AS pthc', 'pth.id=pthc.ppob_transaction_history_id', 'inner')
               ->join('personal AS p', 'pthc.personal_id=p.personal_id', 'inner')
               ->where('pthc.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
                  ->like('pth.transaction_code', $search)
                  ->or_like('pth.product_code', $search)
                  ->or_like('p.fullname', $search)
                  ->or_like('p.identity_number', $search)
                  ->group_end();
      }
      $this->db->order_by('pth.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id,
                            'transaction_code' => $row->transaction_code,
                            'product_code' => $row->product_code,
                            'application_price' => $row->application_price,
                            'status' => $row->status,
                            'created_at' => $row->created_at,
                            'company_markup' => $row->company_markup,
                            'company_price' => $row->company_price,
                            'fullname' => $row->fullname,
                            'identity_number' => $row->identity_number);
         }
      }
      return $list;
   }

   function get_total_daftar_markup_ppob($search, $tipe){
      if ( $tipe == 'prabayar' ) {
         $this->db->select('ppp.id')
                  ->from('ppob_prabayar_product AS ppp')
                  ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner')
                  ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner');
         if ($search != '' or $search != null or !empty($search)) {
            $this->db->group_start()
               ->or_like('ppp.product_code', $search)
               ->group_end();
         }
      }else{
          $this->db->select('ppp.id')
                  ->from('ppob_pascabayar_product AS ppp')
                  ->join('ppob_pascabayar_category AS ppc', 'ppp.category_id=ppc.id', 'inner');
         if ($search != '' or $search != null or !empty($search)) {
            $this->db->group_start()
               ->or_like('ppp.product_code', $search)
               ->group_end();
         }
      }
     
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_markup_ppob($limit = 6, $start = 0, $search = '', $tipe) {
      // get default markup
      $this->db->select('company_markup')
               ->from('company')
               ->where('id', $this->company_id);
      $q = $this->db->get();
      $markup_company_default = 0;
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $markup_company_default = $rows->company_markup;
         }
      }
      // get markup company
      $this->db->select('product_code, type, markup_company')
               ->from('ppob_company_markup')
               ->where('company_id', $this->company_id);
      $list_markup = array();
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list_markup[$rows->product_code] = $rows->markup_company;
         }
      }
      // get list
      if ( $tipe == 'prabayar' ) {
         $this->db->select('ppp.id, ppp.product_name, ppp.product_code, ppp.price, ppp.application_markup')
                  ->from('ppob_prabayar_product AS ppp')
                  ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner')
                  ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner');
         if ($search != '' or $search != null or !empty($search)) {
            $this->db->group_start()
               ->or_like('ppp.product_code', $search)
               ->group_end();
         }
      }else{
          $this->db->select('ppp.id, ppp.product_name, ppp.product_code, ppp.product_fee AS price, ppp.application_markup')
                  ->from('ppob_pascabayar_product AS ppp')
                  ->join('ppob_pascabayar_category AS ppc', 'ppp.category_id=ppc.id', 'inner');
         if ($search != '' or $search != null or !empty($search)) {
            $this->db->group_start()
               ->or_like('ppp.product_code', $search)
               ->group_end();
         }
      }
      $this->db->order_by('ppp.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $row ) {
            if( array_key_exists($row->product_code, $list_markup) ) {
               $markup_perusahaan = $list_markup[$row->product_code];
            } else {
               $markup_perusahaan = $markup_company_default;
            }
            $list[] = array('id' => $row->id,
                            'product_code' => $row->product_code,
                            'product_name' => $row->product_name,
                            'tipe' => $tipe, 
                            'harga_aplikasi' => ($row->price + $row->application_markup),
                            'markup_perusahaan' => $markup_perusahaan);
         }
      }
      return $list;
   }

   // check product code exist
   function check_product_code_exist( $product_code, $tipe ) {
      if( $tipe == 'prabayar' ) {
         $this->db->select('id')
                  ->from('ppob_prabayar_product')
                  ->where('product_code', $product_code);
      }else{
         $this->db->select('id')
                  ->from('ppob_pascabayar_product')
                  ->where('product_code', $product_code);
      }
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   // get markup perusahaan
   function get_markup_perusahaan($product_code, $tipe){
      // get markup company
      $this->db->select('markup_company')
               ->from('ppob_company_markup')
               ->where('product_code', $product_code)
               ->where('type', $tipe)
               ->where('company_id', $this->company_id);
      $markup = 0;
      $q = $this->db->get();
      if ( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $markup = $rows->markup_company;
         }
      } else {
          // get default markup
         $this->db->select('company_markup')
                  ->from('company')
                  ->where('id', $this->company_id);
         $q = $this->db->get();
         $markup = 0;
         if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
               $markup = $rows->company_markup;
            }
         }
      }
      // return
      return $markup;
   }

   // check id markup perusahaan exist
   function check_id_markup_perusahaan_exist($id){
      $this->db->select('id')
               ->from('ppob_company_markup')
               ->where('company_id', $this->company_id)
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }


   function get_markup_company(){
      $this->db->select('company_markup')
               ->from('company')
               ->where('id', $this->company_id);
      $q = $this->db->get();
      $markup = 0;
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $markup = $rows->company_markup;
         }
      }
      return $markup;
   }
}