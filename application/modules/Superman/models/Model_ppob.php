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
   private $status;
   private $content;
   private $error;
   private $write_log;

   public function __construct()
   {
      parent::__construct();
      $this->userdata = $this->session->userdata('superman');
      $this->error = 0;
      $this->write_log = 1;
   }

    function get_saldo_company_now( $id ){
        $this->db->select('saldo')
            ->from('company')
            ->where('id', $id);
        $sald0 = 0;
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $saldo = $rows->saldo;
            }
        }
        return $saldo;
    }
   function get_list_product_process(){
      $this->db->select('pth.id, pth.transaction_code, pth.trxid, pth.server, pth.server_price, pth.application_price,
                         pth.nomor_tujuan, pth.product_code, ppc.category_code, pthc.company_markup, pthc.company_price, pthc.company_id')
               ->from('ppob_transaction_history AS pth')
               ->join('ppob_prabayar_product AS ppp', 'pth.product_code=ppp.product_code', 'inner')
               ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner')
               ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner')
               ->join('ppob_transaction_history_company AS pthc', 'pth.id=pthc.ppob_transaction_history_id', 'inner')
               ->where('pth.status', 'process');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list[] = array("id" => $rows->id,
                            "transaction_code" => $rows->transaction_code,
                            "trxid" => $rows->trxid, 
                            "server" => $rows->server, 
                            "server_price" => $rows->server_price,
                            "application_price" => $rows->application_price,
                            "nomor_tujuan" => $rows->nomor_tujuan, 
                            "product_code" => $rows->product_code,
                            "category_code" => $rows->category_code, 
                            "company_price" => $rows->company_price, 
                            "company_id" => $rows->company_id);
         }
      }         
      return $list;
   }

   // get total daftar produk amra
   function get_total_daftar_produk_amra($search, $operator = '', $status_product = '', $server_product = ''){
       $this->db->select('ppp.id')
               ->from('ppob_prabayar_product AS ppp')
               ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner');
      if( $operator != 0){
         $this->db->where('ppp.operator_id', $operator);
      }
      if( $server_product != 'pilih_semua'){
         $this->db->where('ppp.server', $server_product);
      }
      if( $status_product != 'pilih_semua'){
         $this->db->where('ppp.status', $status_product);
      }
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('ppp.product_code', $search)
            ->or_like('ppp.product_name', $search)
            ->or_like('ppo.operator_name', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get index daftar produk amra
   function get_index_daftar_produk_amra($limit = 6, $start = 0, $search = '', $operator = '', $status_product = '', $server_product = ''){
      $this->db->select('ppp.*, ppo.operator_name')
               ->from('ppob_prabayar_product AS ppp')
               ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner');
      if( $operator != 0){
         $this->db->where('ppp.operator_id', $operator);
      }
      if( $server_product != 'pilih_semua'){
         $this->db->where('ppp.server', $server_product);
      }
      if( $status_product != 'pilih_semua'){
         $this->db->where('ppp.status', $status_product);
      }
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('ppp.product_code', $search)
            ->or_like('ppp.product_name', $search)
            ->or_like('ppo.operator_name', $search)
            ->group_end();
      }
      $this->db->order_by('ppp.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id,
                            'operator_name' => $row->operator_name,
                            'product_code' => $row->product_code,
                            'product_name' => $row->product_name,
                            'price' => $row->price,
                            'application_markup' => $row->application_markup,
                            'status' => $row->status,
                            'server' => $row->server,
                            'updated_at' => $row->updated_at);
         }
      }
      return $list;
   }

   // get data product
   function get_data_product( $id ) {
      $this->db->select('id, product_code, product_name, application_markup')
               ->from('ppob_prabayar_product')
               ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list['id'] = $rows->id;
            $list['product_code'] = $rows->product_code;
            $list['product_name'] = $rows->product_name;
            $list['application_markup'] = $rows->application_markup;
         }
      }
      return $list;
   }

   function get_data_edit_product( $id ) {
      $this->db->select('id, product_code, product_name, operator_id')
               ->from('ppob_prabayar_product')
               ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list['id'] = $rows->id;
            $list['product_code'] = $rows->product_code;
            $list['product_name'] = $rows->product_name;
            $list['operator_id'] = $rows->operator_id;
         }
      }
      return $list;
   }

   // product id
   function check_product_id ( $id ) {
      $this->db->select('id')
               ->from('ppob_prabayar_product')
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return TRUE;
      }else{
         return FALSE;
      }
   }

   // check operator
   function check_operator($id){
      $this->db->select('id')
               ->from('ppob_prabayar_operator')
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return TRUE;
      }else{
         return FALSE;
      }
   }

   // check kode product
   function check_kode_product($kode, $id = ''){
      $this->db->select('id')
               ->from('ppob_prabayar_product')
               ->where('product_code', $kode);
      if( $id != ''){
         $this->db->where('id !=', $id);
      }
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   function get_list_operator(){
       $this->db->select('ppo.id, ppo.operator_code, ppo.operator_name, ppc.category_code, ppc.category_name')
               ->from('ppob_prabayar_operator AS ppo')
               ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list[] = array('id' => $rows->id, 
                            'operator_code' => $rows->operator_code, 
                            'operator_name' => $rows->operator_name, 
                            "category_code" => $rows->category_code, 
                            "category_name" => $rows->category_name);
         }
      }
      return $list;
   }

   function get_total_daftar_operator_amra($search){
      $this->db->select('ppo.id')
               ->from('ppob_prabayar_operator AS ppo')
               ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner');;
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('ppo.operator_code', $search)
            ->or_like('ppo.operator_name', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_operator_amra($limit = 6, $start = 0, $search = ''){
      $this->db->select('ppo.id, ppo.operator_code, ppo.operator_name, ppc.category_code, ppc.category_name')
               ->from('ppob_prabayar_operator AS ppo')
               ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('ppo.operator_code', $search)
            ->or_like('ppo.operator_name', $search)
            ->group_end();
      }
      $this->db->order_by('ppo.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id,
                            'operator_code' => $row->operator_code,
                            'operator_name' => $row->operator_name,
                            'category_code' => $row->category_code,
                            'category_name' => $row->category_name);
         }
      }
      return $list;
   }

   // list category
   function get_list_category(){
      $this->db->select('id, category_code, category_name')
               ->from('ppob_prabayar_category');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list[] = array('id' => $rows->id, 
                            'category_name' => $rows->category_name, 
                            'category_code' => $rows->category_code);
         }
      }
      return $list;     
   }

   // check operator id
   function check_operator_id( $id ) {
      $this->db->select('id')
               ->from('ppob_prabayar_operator')
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }         
   }

   // check category id exist
   function check_category_ppob_exist($categori_id){  
      $this->db->select('id')
               ->from('ppob_prabayar_category')
               ->where('id', $categori_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   // get value operator
   function get_value_operator( $id ){
      $this->db->select('id, category_id, operator_code, operator_name')
               ->from('ppob_prabayar_operator')
               ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ( $q->result() as $row ) {
            $list['id'] = $row->id;
            $list['category_id'] = $row->category_id;
            $list['operator_code'] = $row->operator_code;
            $list['operator_name'] = $row->operator_name;
         }
      }
      return $list;
   }

   function get_total_daftar_operator_iak($search){
     $this->db->select('pipo.id')
               ->from('ppob_iak_prabayar_operator AS pipo')
               ->join('ppob_iak_prabayar_type AS pipt', 'pipo.type_id=pipt.id', 'inner');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('pipo.operator_name', $search)
            ->or_like('pipt.type', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_operator_iak($limit = 6, $start = 0, $search = ''){
      $this->db->select('pipo.id, pipo.operator, pipt.type')
               ->from('ppob_iak_prabayar_operator AS pipo')
               ->join('ppob_iak_prabayar_type AS pipt', 'pipo.type_id=pipt.id', 'inner');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('pipo.operator_name', $search)
            ->or_like('pipt.type', $search)
            ->group_end();
      }
      $this->db->order_by('pipo.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      $startNumber = $start + 1;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id,
                            'number' => $startNumber,
                            'operator_name' => $row->operator,
                            'type' => $row->type);
            $startNumber++;
         }
      }
      return $list;
   }

   // get total daftar product iak
   function get_total_daftar_product_iak($search){
      $this->db->select('pipp.id')
               ->from('ppob_iak_prabayar_product AS pipp')
               ->join('ppob_iak_prabayar_operator AS pipo', 'pipp.operator=pipo.id', 'inner');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
                  ->like('pipp.product_code', $search)
                  ->or_like('pipp.product_name', $search)
                  ->or_like('pipo.operator', $search)
                  ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get index daftar product iak
   function get_index_daftar_product_iak($limit = 6, $start = 0, $search = ''){
      $this->db->select('pipp.id, pipo.operator, pipp.product_code, pipp.product_name, pipp.product_price, pipp.status, pipp.updatedAt')
               ->from('ppob_iak_prabayar_product AS pipp')
               ->join('ppob_iak_prabayar_operator AS pipo', 'pipp.operator=pipo.id', 'inner');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('pipp.product_code', $search)
            ->or_like('pipp.product_name', $search)
            ->or_like('pipo.operator', $search)
            ->group_end();
      }
      $this->db->order_by('pipp.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      $startNumber = $start + 1;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id, 
                            'number' => $startNumber,
                            'operator' => $row->operator, 
                            'product_code' => $row->product_code, 
                            'product_name' => $row->product_name,
                            'product_price' => $row->product_price, 
                            'status' => $row->status, 
                            'updatedAt' => $row->updatedAt);
            $startNumber++;
         }
      }
      return $list;
   }



   // get total daftar product iak
   function get_total_daftar_product_tripay($search){
      $this->db->select('ptpp.id')
               ->from('ppob_tripay_prabayar_product AS ptpp')
               ->join('ppob_tripay_prabayar_operator AS ptpo', 'ptpp.operator_id=ptpo.id', 'inner');
                        
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
                  ->like('ptpp.product_code', $search)
                  ->or_like('ptpp.product_name', $search)
                  ->or_like('ptpo.operator', $search)
                  ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get index daftar product tripay
   function get_index_daftar_product_tripay($limit = 6, $start = 0, $search = ''){
      $this->db->select('ptpp.id, ptpo.operator, ptpp.product_code, ptpp.product_name, ptpp.price, ptpp.status')
               ->from('ppob_tripay_prabayar_product AS ptpp')
               ->join('ppob_tripay_prabayar_operator AS ptpo', 'ptpp.operator_id=ptpo.id', 'inner');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('ptpp.product_code', $search)
            ->or_like('ptpp.product_name', $search)
            ->or_like('ptpo.operator', $search)
            ->group_end();
      }
      $this->db->order_by('ptpp.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      $startNumber = $start + 1;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id, 
                            'number' => $startNumber,
                            'operator' => $row->operator, 
                            'product_code' => $row->product_code, 
                            'product_name' => $row->product_name,
                            'product_price' => $row->price, 
                            'status' => $row->status);
            $startNumber++;
         }
      }
      return $list;
   }




   // get total daftar product sinkronisasi
   function get_total_daftar_product_sinkronisasi($search, $kategori){
      $this->db->select('ppp.id')
               ->from('ppob_prabayar_product AS ppp')
               ->join('ppob_product_local_to_server_product AS pl', 'ppp.id=pl.product_id', 'left')
               ->join('ppob_iak_prabayar_product AS pipp', 'pl.product_id_iak=pipp.id', 'left')
               ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner');
      if( $kategori != 0 ) {
         $this->db->where('ppo.category_id', $kategori);
      }
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
                  ->like('ppp.product_code', $search)
                  ->or_like('ppp.product_name', $search)
                  ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get index
   function get_index_daftar_product_sinkronisasi($limit = 6, $start = 0, $search = '', $kategori){
      $this->db->select('ppp.id, ppp.product_code, ppp.product_name, ppp.price, ppp.status, 
                         pl.product_id_iak, pl.product_id_tripay, ppo.operator_name, ppp.updated_at, 
                         pipp.product_name AS product_name_iak,
                         ptpp.product_name AS product_name_tripay')
               ->from('ppob_prabayar_product AS ppp')
               ->join('ppob_product_local_to_server_product AS pl', 'ppp.id=pl.product_id', 'left')
               ->join('ppob_iak_prabayar_product AS pipp', 'pl.product_id_iak=pipp.id', 'left')
               ->join('ppob_tripay_prabayar_product AS ptpp', 'pl.product_id_tripay=ptpp.id', 'left')
               ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner');
      if( $kategori != 0 ) {
         $this->db->where('ppo.category_id', $kategori);
      }        
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
                  ->like('ppp.product_code', $search)
                  ->or_like('ppp.product_name', $search)
                  ->group_end();
      }
      $this->db->order_by('ppo.id', 'desc')->order_by('ppp.price', 'asc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id, 
                            'product_code' => $row->product_code, 
                            'product_name' => $row->product_name,
                            'price' => $row->price,
                            'operator' => $row->operator_name, 
                            'status' => $row->status, 
                            'product_id_iak' => $row->product_id_iak, 
                            'product_name_iak' => $row->product_name_iak,
                            'product_id_tripay' => $row->product_id_tripay, 
                            'product_name_tripay' => $row->product_name_tripay,
                            'updated_at' => $row->updated_at);
         }
      }
      return $list;
   }

   // get list product iak
   function get_list_product_iak(){
      $this->db->select('product_id_iak')
               ->from('ppob_product_local_to_server_product')
               ->where('product_id_iak !=','0');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $list[] = $rows->product_id_iak;
         }
      }         

      $this->db->select('prd_iak.id, prd_iak.product_name, op_iak.operator, t.type')
               ->from('ppob_iak_prabayar_product AS prd_iak')
               ->join('ppob_iak_prabayar_operator AS op_iak', 'prd_iak.operator=op_iak.id', 'inner')
               ->join('ppob_iak_prabayar_type AS t', 'op_iak.type_id=t.id', 'inner');
      if( count($list) > 0 ) {
         $this->db->where_not_in('prd_iak.id', $list);
      }  
      $this->db->order_by('t.id', 'asc');
      $seed = array();
      $r = $this->db->get();         
      if( $r->num_rows() > 0 ){
            foreach ($r->result() as $row) {
               $seed[] = array('id' => $row->id, 
                               'product_name' => $row->product_name, 
                               'operator' => $row->operator, 
                               'type' => $row->type);
         }
      }
      return $seed;
   }


   function get_list_product_tripay(){
      $this->db->select('product_id_tripay')
               ->from('ppob_product_local_to_server_product')
               ->where('product_id_tripay !=','0');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $list[] = $rows->product_id_tripay;
         }
      }         

      $this->db->select('prd_tripay.id, prd_tripay.product_name, op_tripay.operator, ct.category')
               ->from('ppob_tripay_prabayar_product AS prd_tripay')
               ->join('ppob_tripay_prabayar_operator AS op_tripay', 'prd_tripay.operator_id=op_tripay.id', 'inner')
               ->join('ppob_tripay_prabayar_kategori AS ct', 'op_tripay.category_id=ct.id', 'inner');
      if( count($list) > 0 ) {
         $this->db->where_not_in('prd_tripay.id', $list);
      }  
      $this->db->order_by('ct.id', 'asc');
      $seed = array();
      $r = $this->db->get();
      if( $r->num_rows() > 0 ){
            foreach ($r->result() as $row) {
               $seed[] = array('id' => $row->id, 
                               'product_name' => $row->product_name, 
                               'operator' => $row->operator, 
                               'category' => $row->category);
         }
      }
      return $seed;
   }

   // check iak product id exist
   function check_iak_product_id_exist($id){
      $this->db->select('id')
               ->from('ppob_iak_prabayar_product')
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return TRUE;
      }else{
         return FALSE;
      }
   }

   function check_tripay_product_id_exist($id){
      $this->db->select('id')
               ->from('ppob_tripay_prabayar_product')
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return TRUE;
      }else{
         return FALSE;
      }
   }


   function check_tabel_connection_exist($id){
      $this->db->select('product_id')
               ->from('ppob_product_local_to_server_product')
               ->where('product_id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   // update harga product
   function update_harga_produk(){
      $tripay = true;
      $iak = true;
      $this->db->select('id')
               ->from('ppob_prabayar_product');
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $row) {
            $this->db->select('pp.id, lc.product_id_iak, iak_product.product_price, tripay_product.price, 
                               tripay_product.status AS status_tripay, iak_product.status AS status_iak')
                     ->from('ppob_prabayar_product AS pp')
                     ->join('ppob_product_local_to_server_product AS lc', 'pp.id=lc.product_id', 'inner')
                     ->join('ppob_iak_prabayar_product AS iak_product', 'lc.product_id_iak=iak_product.id', 'left')
                     ->join('ppob_tripay_prabayar_product AS tripay_product', 'lc.product_id_tripay=tripay_product.id', 'left')
                     // ->where('lc.product_id_iak !=', '0')
                     ->where('pp.id', $row->id);
            $r = $this->db->get();
            if( $r->num_rows() > 0 ) {
               foreach ( $r->result() as $rows ) {
                  $data = array();
                  $state = "none";
                  if( $rows->status_iak != null AND $rows->status_tripay != null ){
                     if( $rows->status_tripay == 'tersedia' AND $rows->status_iak == 'active'){
                        if( $rows->price <= $rows->product_price ){
                           $state =  ( $tripay == true ? 'tripay' :  ( $iak == true ? 'iak' : "none" ) );
                        }else{
                           $state =  ( $iak == true ? 'iak' :  ( $tripay == true ? 'tripay' : "none" ) );
                        }
                     }elseif ( $rows->status_tripay == 'tersedia' AND $rows->status_iak == 'non active' ) {
                        $state = ( $tripay == true ? 'tripay' : "none" ); //tripay
                     }elseif ( $rows->status_tripay == 'tidak tersedia' AND $rows->status_iak == 'active' ) {
                        $state = ( $iak == true ? 'iak' : "none" );
                     }else{
                        $state = "none";
                     }
                  }elseif ( $rows->status_iak == null AND $rows->status_tripay != null ) {
                     if( $rows->status_tripay == 'tersedia'){
                        $state = ( $tripay == true ? 'tripay' : "none" ); // tripay
                     }else{
                        $state = "none";
                     } 
                  }elseif ( $rows->status_iak != null AND $rows->status_tripay == null ) {
                     if( $rows->status_iak == 'active'){
                        $state = ( $iak == true ? 'iak' : "none" );
                     }else{
                        $state = "none";
                     } 
                  }else{
                     $state = "none";
                  }
                  if( $state == "tripay"){
                     $data['status'] = 'active';
                     $data['price'] = $rows->price;
                     $data['server'] = 'tripay';
                     $data['updated_at'] = date('Y-m-d H:i:s');  
                  }elseif ( $state == "iak" ) {
                     $data['status'] = 'active';
                     $data['price'] = $rows->product_price;
                     $data['server'] = 'iak';
                     $data['updated_at'] = date('Y-m-d H:i:s');   
                  }else{
                     $data['status'] = 'inactive';
                     $data['price'] = 0;
                     $data['server'] = 'none';
                     $data['updated_at'] = date('Y-m-d H:i:s');   
                  }
                  # update data company
                  $this->db->where('id', $row->id)->update('ppob_prabayar_product', $data);
               }
            }
         }
         return true;
      }else{
         return false;
      }      
   }

   function get_kategori_product(){
      $this->db->select('ppo.id, ppc.category_code, ppc.category_name, ppo.operator_code, ppo.operator_name')
              ->from('ppob_prabayar_operator AS ppo')
              ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $row ) {
            $list[] = array('id' => $row->id, 
                            'name' => '('. $row->category_code . ':' . $row->category_name . ') '. $row->operator_code . ' ' . $row->operator_name );
         }
      }
      return $list;
   }

   function check_categori_operator($id){
      $this->db->select('id')
               ->from('ppob_prabayar_operator')
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

}