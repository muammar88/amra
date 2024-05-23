<?php

/**
 *  -----------------------
 *	Model panduan manasik
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_panduan_manasik extends CI_Model
{
   private $company_id;
   private $feedBack;

   function __construct()
   {
        parent::__construct();
        $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   # get info panduan manasik
   function get_info_panduan_manasik($param){
      $this->db->select('id, title, part, description')
         ->from('panduan_manasik')
         ->where('tab', $param);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $list[] = array('id' => $rows->id, 'title' => $rows->title, 'part' => $rows->part, 'desc' => $rows->description);
         }
      }
      return $list;
   }

   function check_panduan_manasik($tab, $param){
      $this->db->select('id')
         ->from('panduan_manasik')
         ->where('tab', $tab)
         ->where('part', $param);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function get_detail_panduan_manasik($tab, $part){
      $this->db->select('d.content')
         ->from('panduan_manasik_detail AS d')
         ->join('panduan_manasik AS p', 'p.id=d.panduan_manasik_id', 'inner')
         ->where('p.tab', $tab)
         ->where('p.part', $part);
      $q = $this->db->get();
      $content = '';
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $content = $rows->content;
         }
      }
      return $content;
   }

   function check_exist_content($tab, $part){
      $this->db->select('d.content')
         ->from('panduan_manasik_detail AS d')
         ->join('panduan_manasik AS p', 'p.id=d.panduan_manasik_id', 'inner')
         ->where('p.tab', $tab)
         ->where('p.part', $part);
      $q = $this->db->get();
      $content = '';
      if( $q->num_rows() > 0 ){
        return true;
      }else{
        return false;
      }
   }

   function get_id($tab, $part){
      $this->db->select('id')
         ->from('panduan_manasik')
         ->where('tab', $tab)
         ->where('part', $part);
      $q = $this->db->get();
      $id = '';
      if( $q->num_rows() > 0 ) {
        $id = $q->row()->id;
      }
      return $id;
   }

   # get detail id
   function get_detail_id($tab, $part){
      $this->db->select('d.id')
         ->from('panduan_manasik_detail AS d')
         ->join('panduan_manasik AS p', 'p.id=d.panduan_manasik_id', 'inner')
         ->where('p.tab', $tab)
         ->where('p.part', $part);
      $q = $this->db->get();
      $id = '';
      if( $q->num_rows() > 0 ) {
        $id = $q->row()->id;
      }
      return $id;
   }

}
