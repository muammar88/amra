<?php

/**
 *  -----------------------
 *	Model daftar hotel
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_hotel extends CI_Model
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

   function get_total_daftar_hotel($search)
   {
      $this->db->select('h.id')
         ->from('mst_hotel AS h')
         ->join('mst_city AS c', 'h.city_id=c.id', 'inner')
         ->where('h.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('h.hotel_name', $search)
            ->or_like('c.city_name', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_hotel($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('h.id, h.hotel_name, c.city_name, c.city_code, h.star_hotel, h.description')
         ->from('mst_hotel AS h')
         ->join('mst_city AS c', 'h.city_id=c.id', 'inner')
         ->where('h.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('h.hotel_name', $search)
            ->or_like('c.city_name', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'hotel_name' => $row->hotel_name,
               'city_name' => $row->city_name,
               'city_code' => $row->city_code,
               'star_hotel' => $row->star_hotel,
               'description' => $row->description
            );
         }
      }
      return $list;
   }

   function get_list_city()
   {
      $this->db->select('id, city_name, city_code')
         ->from('mst_city')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'city_name' => $row->city_name,
               'city_code' => $row->city_code
            );
         }
      }
      return $list;
   }

   function check_hotel_id_exist($id)
   {
      $this->db->select('id')
         ->from('mst_hotel')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function check_city_id_exist($id)
   {
      $this->db->select('id')
         ->from('mst_city')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function get_info_hotel($id)
   {
      $this->db->select('id, hotel_name, description, star_hotel, city_id')
         ->from('mst_hotel')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['hotel_name'] = $rows->hotel_name;
            $list['description'] = $rows->description;
            $list['star_hotel'] = $rows->star_hotel;
            $list['city_id'] = $rows->city_id;
         }
      }
      return  $list;
   }
}
