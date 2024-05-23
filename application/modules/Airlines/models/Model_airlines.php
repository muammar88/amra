<?php
/**
*  -----------------------
*	Model airlines
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_airlines extends CI_Model
{
   private $company_id;

   function __construct(){
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_daftar_airlines($search){
      $this->db->select('id')
               ->from('mst_airlines')
               ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('airlines_name', $search)
            ->group_end();
      }
      $r 	= $this->db->get();
      return $r->num_rows();
   }

   // (SELECT CONCAT_WS(\'$\', nomor_akun_secondary, nama_akun_secondary, path )
   //    FROM akun_secondary
   //    WHERE path=CONCAT(\'airlines:deposit:\', m.id) AND company_id='.$this->company_id.') AS akun_deposit,
   // (SELECT CONCAT_WS(\'$\', nomor_akun_secondary, nama_akun_secondary, path )
   //    FROM akun_secondary
   //    WHERE path=CONCAT(\'airlines:pendapatan:\', m.id) AND company_id='.$this->company_id.') AS akun_pendapatan,
   // (SELECT CONCAT_WS(\'$\', nomor_akun_secondary, nama_akun_secondary, path )
   //    FROM akun_secondary
   //    WHERE path=CONCAT(\'airlines:hpp:\', m.id) AND company_id='.$this->company_id.') AS akun_hpp

   function get_index_daftar_airlines($limit = 6, $start = 0, $search = ''){
      $this->db->select('id, airlines_name')
               ->from('mst_airlines AS m')
               ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('airlines_name', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            // $expDeposit = explode('$', $row->akun_deposit);
            // $expPendapatan = explode('$', $row->akun_pendapatan);
            // $expHPP = explode('$', $row->akun_hpp);

            $list[] = array('id' => $row->id,
                            'nama_airlines' => $row->airlines_name,
                            // 'nomor_akun_deposit' => $expDeposit[0],
                            // 'nama_akun_deposit' => $expDeposit[1],
                            // 'path_akun_deposit' => $expDeposit[2],
                            // 'nomor_akun_pendapatan' => $expPendapatan[0],
                            // 'nama_akun_pendapatan' => $expPendapatan[1],
                            // 'path_akun_pendapatan' => $expPendapatan[2],
                            // 'nomor_akun_hpp' => $expHPP[0],
                            // 'nama_akun_hpp' => $expHPP[1],
                            // 'path_akun_hpp' => $expHPP[2]
                         );
         }
      }
      return $list;
   }

   function check_id_airlines_exist( $id ){
      $this->db->select('id')
         ->from('mst_airlines')
         ->where('id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return  false;
      }
   }


   function generated_nomor_akun_airlines_deposit(){
      $this->db->select('nomor_akun_secondary, path')
         ->from('akun_secondary')
         ->like('path','airlines:deposit:')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         $main_airlines_akun = 12000;
         $secondary_airlines_akun =array();
         foreach ($q->result() as $row) {
            $secondary_airlines_akun[] = $row->nomor_akun_secondary;
         }
         $looping = true;
         $new_akun_airlines = $main_airlines_akun;
         while($looping){
            $new_akun_airlines++;
            if( ! in_array($new_akun_airlines, $secondary_airlines_akun) ){
               $looping = false;
            }
         }
         return $new_akun_airlines;
      }else{
         return '12001';
      }
   }

   function generated_nomor_akun_airlines_pendapatan(){
      $this->db->select('nomor_akun_secondary, path')
         ->from('akun_secondary')
         ->like('path','airlines:pendapatan:')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         $main_airlines_akun = 42000;
         $secondary_airlines_akun =array();
         foreach ($q->result() as $row) {
            $secondary_airlines_akun[] = $row->nomor_akun_secondary;
         }
         $looping = true;
         $new_akun_airlines = $main_airlines_akun;
         while($looping){
            $new_akun_airlines++;
            if( ! in_array($new_akun_airlines, $secondary_airlines_akun) ){
               $looping = false;
            }
         }
         return $new_akun_airlines;
      }else{
         return '42001';
      }
   }

   function generated_nomor_akun_airlines_hpp(){
      $this->db->select('nomor_akun_secondary, path')
         ->from('akun_secondary')
         ->like('path','airlines:hpp:')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         $main_airlines_akun = 51000;
         $secondary_airlines_akun =array();
         foreach ($q->result() as $row) {
            $secondary_airlines_akun[] = $row->nomor_akun_secondary;
         }
         $looping = true;
         $new_akun_airlines = $main_airlines_akun;
         while($looping){
            $new_akun_airlines++;
            if( ! in_array($new_akun_airlines, $secondary_airlines_akun) ){
               $looping = false;
            }
         }
         return $new_akun_airlines;
      }else{
         return '51001';
      }
   }

   # airlines name info for edit
   function get_info_edit_airlines( $id ){
      $this->db->select('id, airlines_name')
         ->from('mst_airlines')
         ->where('company_id',$this->company_id)
         ->where('id',$id);
      $q = $this->db->get();
      $feedBack = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $feedBack['id'] = $rows->id;
            $feedBack['airlines_name'] = $rows->airlines_name;
         }
      }
      return  $feedBack;
   }

   function get_akun_airlines_by_id($id){
      $this->db->select('(SELECT GROUP_CONCAT( nomor_akun_secondary SEPARATOR \';\')
                           FROM akun_secondary
                           WHERE path IN (CONCAT(\'airlines:deposit:\', m.id),CONCAT(\'airlines:pendapatan:\', m.id),CONCAT(\'airlines:hpp:\', m.id)) AND company_id='.$this->company_id.') AS nomor_akun ')
         ->from('mst_airlines AS m')
         ->where('m.id',$id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         $number_akun = array();
         foreach ($q->result() as $rows) {
            $number_akun = explode(';',$rows->nomor_akun);
         }
         return $number_akun;
      }else{
         return array();
      }
   }

   function checking_akun_airlines_in_jurnal($id){
      $nomor_akun = $this->get_akun_airlines_by_id($id);
      if( count($nomor_akun) > 0 ){
         $this->db->select('id, akun_debet, akun_kredit')
            ->from('jurnal')
            ->where_in('akun_debet', $nomor_akun)
            ->or_where_in('akun_kredit', $nomor_akun);
         $q = $this->db->get();
         if( $q->num_rows() > 0 )   {
            return true;
         }else{
            return false;
         }
      }else{
         return false;
      }
   }

}
