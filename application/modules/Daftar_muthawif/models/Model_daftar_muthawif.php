<?php

/**
 *  -----------------------
 *	Model daftar muthawif
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_muthawif extends CI_Model
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

   function get_total_daftar_muthawif($search)
   {
      $this->db->select('m.id')
         ->from('muthawif AS m')
         ->join('personal AS p', 'm.personal_id=p.personal_id', 'inner')
         ->where('p.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_muthawif($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('m.id, p.fullname, p.identity_number, m.input_date,
                        (SELECT COUNT(paket_id)
                           FROM paket_muthawif
                           WHERE muthawif_id=m.id ) AS jumlahPaket')
         ->from('muthawif AS m')
         ->join('personal AS p', 'm.personal_id=p.personal_id', 'inner')
         ->where('p.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $this->db->order_by('m.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'fullname' => $row->fullname,
               'identity_number' => $row->identity_number,
               'jumlah_paket' => $row->jumlahPaket,
               'waktu_input' => $row->input_date
            );
         }
      }
      return $list;
   }

   # check id muthawif exist
   function check_id_muthawif_exist($id)
   {
      $this->db->select('id')
         ->from('muthawif')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }
}
