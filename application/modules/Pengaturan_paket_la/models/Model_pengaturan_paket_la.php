<?php

/**
 *  -----------------------
 *	Model pengaturan paket la
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_pengaturan_paket_la extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_info_pengaturan_paket_la($company_id)
   {
      $this->db->select('kurs, note_paket_la, tanda_tangan')
         ->from('company')
         ->where('id', $company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if ($rows->tanda_tangan != '') :
               $tanda_tangan = file_exists(FCPATH . 'image/company/tanda_tangan/' . $rows->tanda_tangan) ? $rows->tanda_tangan : 'default.png';
            else :
               $tanda_tangan = 'default.png';
            endif;

            $des = str_replace('\n',' ',base64_decode($rows->note_paket_la));
            $des = str_replace('\r',' ',$des);

            $list = array(
               'kurs' => $rows->kurs, 
               'note_paket_la' => $des, 
               'tanda_tangan' => $tanda_tangan
            );
         }
      }


      return $list;
   }

      # get tanda tangan
   function get_tanda_tangan($company_id)
   {
      $this->db->select('tanda_tangan')
         ->from('company')
         ->where('id', $company_id);
      $q = $this->db->get();
      $result = '';
      if ($q->num_rows() > 0) {
         $result = $q->row()->tanda_tangan;
      }
      return $result;
   }

}