<?php

/**
 *  -----------------------
 *	Model pengguna
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_pengguna extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_pengguna($search)
   {
      $this->db->select('u.user_id')
         ->from('base_users AS u')
         ->join('personal AS p', 'u.personal_id=p.personal_id', 'inner')
         ->join('base_groups AS g', 'u.group_id=g.group_id', 'inner')
         ->where('u.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.nomor_whatsapp', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   # get index pengguna
   function get_index_pengguna($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('u.user_id, p.nomor_whatsapp, p.fullname, g.nama_group, u.last_update')
         ->from('base_users AS u')
         ->join('personal AS p', 'u.personal_id=p.personal_id', 'inner')
         ->join('base_groups AS g', 'u.group_id=g.group_id', 'inner')
         ->where('u.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.nomor_whatsapp', $search)
            ->group_end();
      }
      $this->db->order_by('u.user_id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array(
               'id' => $rows->user_id,
               'nomor_whatsapp' => $rows->nomor_whatsapp,
               'fullname' => $rows->fullname,
               'nama_group' => $rows->nama_group,
               'last_update' => $rows->last_update
            );
         }
      }
      return $list;
   }

   # get daftar grup
   function get_daftar_grup()
   {
      $this->db->select('group_id, nama_group')
         ->from('base_groups')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = (array) $rows;
         }
      }
      return $list;
   }

   # check user id
   function check_user_id_exist($id)
   {
      $this->db->select('user_id')
         ->from('base_users')
         ->where('company_id', $this->company_id)
         ->where('user_id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # check grup id
   function check_grup_id($grup_id)
   {
      $this->db->select('group_id')
         ->from('base_groups')
         ->where('company_id', $this->company_id)
         ->where('group_id', $grup_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # check nomor whatsapp exist
   function check_nomor_whatsapp_exist( $nomor_whatsapp, $user_id = 0 )
   {
      $this->db->select('bu.user_id')
         ->from('base_users AS bu')
         ->join('personal AS p', 'bu.personal_id=p.personal_id', 'inner')
         ->where('p.nomor_whatsapp', $nomor_whatsapp);
      if ($user_id != 0) : $this->db->where('bu.user_id != ', $user_id);
      endif;
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get value pengguna
   function get_value_pengguna($id)
   {
      $this->db->select('u.user_id, p.nomor_whatsapp, p.fullname, u.group_id')
         ->from('base_users AS u')
         ->join('personal AS p', 'u.personal_id=p.personal_id', 'inner')
         ->where('u.company_id', $this->company_id)
         ->where('u.user_id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         $list = (array) $q->row();
      }
      return $list;
   }

   # get personal id
   function get_personal_id($id)
   {
      $this->db->select('personal_id')
         ->from('base_users')
         ->where('company_id', $this->company_id)
         ->where('user_id', $id);
      $q = $this->db->get();
      $personal_id = 0;
      if ($q->num_rows() > 0) {
         $personal_id = $q->row()->personal_id;
      }
      return $personal_id;
   }
}
