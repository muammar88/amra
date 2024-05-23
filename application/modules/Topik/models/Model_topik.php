<?php

/**
 *  -----------------------
 *	Model topik
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_topik extends CI_Model
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

   # total daftar topik
   function get_total_daftar_topik($search)
   {
      $this->db->select('id')
         ->from('topik')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('topik', $search)
            ->group_end();
      }
      $r = $this->db->get();
      return $r->num_rows();
   }

   #  get daftar topik
   function get_index_daftar_topik($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('id, topik, last_update')
         ->from('topik')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('topik', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'topik' => $row->topik,
               'last_update' => $row->last_update
            );
         }
      }
      return $list;
   }

   # check topik id
   function check_topik_id($id)
   {
      $this->db->select('id')
         ->from('topik')
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get info topik
   function get_info_topik($id)
   {
      $this->db->select('id, topik')
         ->from('topik')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['topik'] = $rows->topik;
         }
      }
      return $list;
   }
}
