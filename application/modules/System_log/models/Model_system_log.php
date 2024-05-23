<?php

/**
 *  -----------------------
 *	Model tipe paket la
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_system_log extends CI_Model
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


   function get_total_daftar_system_log($search)
   {
      $this->db->select('bsl.log_id')
         ->from('base_system_log AS bsl')
         ->join('base_users AS u', 'bsl.user_id=u.user_id', 'left')
         ->join('personal AS p', 'u.personal_id=p.personal_id', 'left')
         ->where('bsl.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.nomor_whatsapp', $search)
            ->or_like('bsl.log_msg', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_system_log($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('bsl.log_id, bsl.log_msg, bsl.input_date,
                        IFNULL(p.fullname, "administrator") AS fullname,
                        IFNULL(p.nomor_whatsapp, "administrator") AS nomor_whatsapp')
         ->from('base_system_log AS bsl')
         ->join('base_users AS u', 'bsl.user_id=u.user_id', 'left')
         ->join('personal AS p', 'u.personal_id=p.personal_id', 'left')
         ->where('bsl.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.nomor_whatsapp', $search)
            ->or_like('bsl.log_msg', $search)
            ->group_end();
      }
      $this->db->order_by('bsl.log_id', 'desc')
         ->limit($limit, $start);
      $q    = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         $list = $this->data_ops->simple_get($q);
      }
      return $list;
   }
}
