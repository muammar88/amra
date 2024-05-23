<?php

/**
 *  -----------------------
 *	Model request keagenan
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_request_keagenan extends CI_Model
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

   # get total request keagenan
   function get_total_request_keagenan($search)
   {
      $this->db->select('ar.id')
         ->from('agen_request AS ar')
         ->join('personal AS p', 'ar.member_id=p.personal_id', 'inner')
         ->where('ar.company_id', $this->company_id);
      if ($search != 'pilih_semua') {
         $this->db->group_start()
            ->like('ar.status_request', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   # get index request keagenan
   function get_index_request_keagenan($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('ar.id, ar.approver, ar.status_request, ar.status_note, p.fullname, p.identity_number')
         ->from('agen_request AS ar')
         ->join('personal AS p', 'ar.member_id=p.personal_id', 'inner')
         ->where('ar.company_id', $this->company_id);
      if ($search != 'pilih_semua') {
         $this->db->group_start()
            ->like('ar.status_request', $search)
            ->group_end();
      }
      $this->db->order_by('ar.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'fullname' => $row->fullname,
               'identity_number' => $row->identity_number,
               'approver' => $row->approver,
               'status_request' => $row->status_request,
               'status_note' => $row->status_note
            );
         }
      }
      return $list;
   }

   # check id request exist
   function check_id_request_exist($id)
   {
      $this->db->select('id')
         ->from('agen_request')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function get_upline($id)
   {
      $this->db->select('member_id, upline')
         ->from('agen_request')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $feedBack = array(
            'member_id' => $row->member_id,
            'upline' => $row->upline
         );
      }
      return $feedBack;
   }

   function get_agen_member_id($id)
   {
      $this->db->select('ar.member_id, a.id')
         ->from('agen_request AS ar')
         ->join('agen AS a', 'ar.member_id=a.personal_id', 'inner')
         ->where('ar.company_id', $this->company_id)
         ->where('ar.id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $feedBack = array('member_id' => $row->member_id, 'agen_id' => $row->id);
      }
      return $feedBack;
   }

   # check agen is active
   function check_agen_is_active($id)
   {
      $this->db->select('ar.id')
         ->from('agen_request AS ar')
         ->join('agen AS a', 'ar.member_id=a.personal_id', 'inner')
         ->where('ar.company_id', $this->company_id)
         ->where('ar.id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }
}
