<?php

/**
 *  -----------------------
 *	Model tipe paket la
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_tipe_paket_la extends CI_Model
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

   function get_total_daftar_tipe_paket_la($search)
   {
      $this->db->select('id')
         ->from('mst_paket_type_la')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('paket_type_name', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }


   function get_index_daftar_tipe_paket_la($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('mpt.id, mpt.paket_type_name,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', tpf.facilities_la_id, mfl.facilities_name, mfl.price, tpf.pax ) SEPARATOR \';\')
                               FROM paket_type_la_facilities AS tpf
                               INNER JOIN mst_facilities_la AS mfl ON tpf.facilities_la_id=mfl.id
                               WHERE tpf.paket_type_id=mpt.id) AS fasilitas_la')
         ->from('mst_paket_type_la AS mpt')
         ->where('mpt.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('mpt.paket_type_name', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $exp  = explode(';', $row->fasilitas_la);
            $list_fasilitas = array();
            foreach ($exp as $key => $value) {
               $exp2 = explode('$', $value);
               $list_fasilitas[] = array(
                  'id' => $exp2[0],
                  'nama_fasilitas' => $exp2[1],
                  'harga' => $exp2[2],
                  'pax' => $exp2[3]
               );
            }
            $list[] = array(
               'id' => $row->id,
               'nama_tipe_paket' => $row->paket_type_name,
               'fasilitas' => $list_fasilitas
            );
         }
      }
      return $list;
   }

   function get_list_fasilitas()
   {
      $this->db->select('id, facilities_name, price')
         ->from('mst_facilities_la')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array(
               'id' => $rows->id,
               'nama_fasilitas' => $rows->facilities_name,
               'harga' => $rows->price
            );
         }
      }
      return $list;
   }

   function check_fasilitas_id_tipe_paket_la_exist($fasilitas_id)
   {
      $this->db->select('id')
         ->from('mst_facilities_la')
         ->where('company_id',  $this->company_id)
         ->where('id', $fasilitas_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }


   function get_info_edit_tipe_paket_la($id)
   {
      $this->db->select('mpt.id, mpt.paket_type_name,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', facilities_la_id, pax ) SEPARATOR \';\')
                              FROM paket_type_la_facilities WHERE paket_type_id = mpt.id
                                 AND company_id="' . $this->company_id . '") AS fasilitas')
         ->from('mst_paket_type_la AS mpt')
         ->where('mpt.id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $fasilitas = array();
            foreach (explode(';', $row->fasilitas) as $key => $value) {
               $exp2 = explode('$', $value);
               $fasilitas[] = array(
                  'fasilitas_id' => $exp2[0],
                  'pax' => $exp2[1]
               );
            }

            $list['id'] = $row->id;
            $list['paket_type_name'] = $row->paket_type_name;
            $list['fasilitas'] = $fasilitas;
         }
      }
      return $list;
   }

   function check_tipe_paket_id_la($id)
   {
      $this->db->select('id')
         ->from('mst_paket_type_la')
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }
}
