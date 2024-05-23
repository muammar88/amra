<?php

/**
 *  -----------------------
 *	Model slider
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_slider extends CI_Model
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

   #  get total daftar slider
   function get_total_daftar_slider($search)
   {
      $this->db->select('id')
         ->from('slider')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('title', $search)
            ->group_end();
      }
      $r = $this->db->get();
      return $r->num_rows();
   }

   # get total index daftar
   function get_index_daftar_slider($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('id, img, title')
         ->from('slider')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('title', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'img' => $row->img,
               'title' => $row->title
            );
         }
      }
      return $list;
   }


   function check_slider_id($slide_id)
   {
      $this->db->select('id')
         ->from('slider')
         ->where('id', $slide_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function get_slide_name($id)
   {
      $this->db->select('img')
         ->from('slider')
         ->where('id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $img = '';
      if ($q->num_rows() > 0) {
         $img = $q->row()->img;
      }
      return $img;
   }

   # get info slider
   function get_info_slider($id)
   {
      $this->db->select('img, title')
         ->from('slider')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['img'] = $rows->img;
            $list['title'] = $rows->img;
         }
      }
      return $list;
   }

   function get_info_slider_by_id($id)
   {
      $this->db->select('id, img, title')
         ->from('slider')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['img'] = $rows->img;
            $list['title'] = $rows->title;
         }
      }
      return $list;
   }
}
