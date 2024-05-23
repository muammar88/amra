<?php
/**
 *  -----------------------
 *	Model template pesan whatsapp
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_template_pesan_whatsapp extends CI_Model
{
   private $company_id;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }
   
   # get total daftar template pesan whatsapp
   function get_total_daftar_template_pesan_whatsapp($search)
   {
      $this->db->select('id')
         ->from('template_pesan_whatsapp')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('nama_template', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   # get index daftar template pesan whatsapp
   function get_index_daftar_template_pesan_whatsapp($limit = 6, $start = 0, $search = ''){
      $this->db->select('id, nama_template, jenis_pesan, pesan, variable, last_update')
         ->from('template_pesan_whatsapp')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('nama_template', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q   = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'nama_template' => $row->nama_template,
               'jenis_pesan' => $this->convert_jenis_pesan($row->jenis_pesan),
               'pesan' => $row->pesan,
               'variable' => ($row->variable == '' ? '-' : $row->variable),
               'last_update' => $row->last_update
            );
         }
      }
      return $list;
   }

   function convert_jenis_pesan($jenis_pesan){
      return ucwords(str_replace("_"," ",$jenis_pesan));
   }

   # get variable by jenis pesan
   function get_variable_by_jenis_pesan($jenis_pesan){
      $this->db->select('nama_variable')
         ->from('variable')
         ->where('jenis_pesan', $jenis_pesan);
      $q = $this->db->get();
      $list = '';
      $i = 0;
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            if( $i == 0 ){
               $list .= '<b>{{'.$rows->nama_variable.'}}</b>';
            }else{
               $list .= ' , <b>{{'.$rows->nama_variable.'}}</b>';
            }
            $i++;
         }
      }
      return $list;
   }

   # check template id
   function check_template_id($id){
      $this->db->select('id')
         ->from('template_pesan_whatsapp')
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   # get info edit template
   function get_info_edit_template($id){
      $this->db->select('id, nama_template, jenis_pesan, pesan, variable')
         ->from('template_pesan_whatsapp')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list['id'] = $rows->id;
            $list['nama_template'] = $rows->nama_template;
            $list['jenis_pesan'] = $rows->jenis_pesan;
            $list['pesan'] = $rows->pesan;
            $list['variable'] = $rows->variable;
         }
      }
      return $list;   
   }
}