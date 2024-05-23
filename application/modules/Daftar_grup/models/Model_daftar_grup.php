<?php

/**
 *  -----------------------
 *	Model daftar grup
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_grup extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   # get total daftar grup
   function get_total_daftar_grup($search)
   {
      $this->db->select('group_id')
         ->from('base_groups')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('nama_group', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   # get index daftar grup
   function get_index_daftar_grup($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('group_id, nama_group, group_access, last_update')
         ->from('base_groups')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('nama_group', $search)
            ->group_end();
      }
      $this->db->order_by('group_id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array(
               'id' => $rows->group_id,
               'nama_group' => $rows->nama_group,
               'group_access' => $this->get_modul(unserialize($rows->group_access)),
               'real_group_access' => unserialize($rows->group_access),
               'last_update' => $rows->last_update
            );
         }
      }
      return $list;
   }

   // get modul
   function get_modul($group_access)
   {
      $modul = $group_access['modul'];
      $submodul = '';
      $i = 0;
      if (isset($group_access['submodul']) && count($group_access['submodul']) > 0) {
         foreach ($group_access['submodul'] as $key => $value) {
            if ($i == 0) {
               $submodul .= $value;
            } else {
               $submodul .= ',' . $value;
            }
            $i++;
         }
      }

      if ($submodul != '') {
         $submodul = 'AND submodules_id IN(' . $submodul . ')';
      }
      // echo $submodul;
      $this->db->select('m.modul_name,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\',\', submodules_id, submodules_name ) SEPARATOR \';\')
                              FROM base_submodules
                              WHERE modul_id=m.modul_id ' . $submodul . ' ) AS submodules')
         ->from('base_modules AS m')
         ->where_in('m.modul_id', $modul)
         ->order_by('m.modul_id', 'asc');
      $list = array();
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $submodulInner = array();
            if ($rows->submodules != null) {
               $expSUB    = explode(';', $rows->submodules);
               foreach ($expSUB as $key => $value) {
                  $expSUB2 = explode(',', $value);
                  $submodulInner[] = $expSUB2[1];
               }
            }
            $list[$rows->modul_name] = $submodulInner;
         }
      }
      return $list;
   }

   # get info grup
   function get_info_grup()
   {
      $this->db->select('modul_id, modul_name')
         ->from('base_modules')
         ->where('modul_id !=', 8);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $submodul = array();
            $this->db->select('submodules_id, submodules_name')
               ->from('base_submodules')
               ->where('modul_id', $rows->modul_id);
            $r = $this->db->get();
            if ($r->num_rows() > 0) {
               foreach ($r->result() as $rowr) {
                  $submodul[] = array('id' => $rowr->submodules_id, 'name' => $rowr->submodules_name);
               }
            }
            $list[$rows->modul_id] = array(
               'modul_id' => $rows->modul_id,
               'modul_name' => $rows->modul_name,
               'submodul' => $submodul
            );
         }
      }
      return $list;
   }

   function get_menu_submenu_id()
   {
      $this->db->select('modul_id')
         ->from('base_modules');
      $q = $this->db->get();
      $modul_id = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $modul_id[] = $rows->modul_id;
         }
      }

      $this->db->select('submodules_id')
         ->from('base_submodules');
      $q = $this->db->get();
      $submodul_id = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $submodul_id[] = $rows->submodules_id;
         }
      }
      return array('modul_id' => $modul_id, 'submodul_id' => $submodul_id);
   }


   function check_group_id($id)
   {
      $this->db->select('group_id')
         ->from('base_groups')
         ->where('group_id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get info grup
   function get_info_edit_grup($id)
   {
      $this->db->select('group_id, nama_group, group_access')
         ->from('base_groups')
         ->where('company_id', $this->company_id)
         ->where('group_id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         $list = (array)$q->row();
         $unserialize = unserialize($list['group_access']);
         $list['modul'] = $unserialize['modul'];
         $list['submodul'] = $unserialize['submodul'];
         unset($list['group_access']);
      }
      return $list;
   }
}
