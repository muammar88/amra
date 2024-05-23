<?php

/**
 *  -----------------------
 *	Model artikel
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_artikel extends CI_Model
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

   function get_total_daftar_artikel($search)
   {
      $this->db->select('a.id')
         ->from('artikel AS a')
         ->join('topik AS t', 'a.topic_id=t.id', 'inner')
         ->where('a.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('a.title', $search)
            ->group_end();
      }
      $r = $this->db->get();
      return $r->num_rows();
   }

   # get index daftar artikel
   function get_index_daftar_artikel($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('a.id, a.title, a.photo, a.photo_caption, a.description, a.author, t.topik, a.place, a.headline, a.tag')
         ->from('artikel AS a')
         ->join('topik AS t', 'a.topic_id=t.id', 'inner')
         ->where('a.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('a.title', $search)
            ->group_end();
      }
      $this->db->order_by('a.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'title' => $row->title,
               'photo' => $row->photo,
               'photo_caption' => $row->photo_caption,
               'description' => $row->description,
               'fullname' => $row->author,
               'topik' => $row->topik,
               'place' => $row->place,
               'headline' => $row->headline,
               'tag' => $row->tag
            );
         }
      }
      return $list;
   }

   # get topik
   function get_topik()
   {
      $this->db->select('id, topik')
         ->from('topik')
         ->where('company_id', $this->company_id);
      $list = array();
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array('id' => $rows->id, 'topic' => $rows->topik);
         }
      }
      return $list;
   }

   function check_artikel_id_exist($id)
   {
      $this->db->select('id')
         ->from('artikel')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function check_topik_id_exist($id)
   {
      $this->db->select('id')
         ->from('topik')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function check_artikel_slide($slug, $id = 0)
   {
      $feedBack    = false;
      $i = 0;
      do {
         if ($i != 0) : $slug = $slug . '-' . $i;
         endif;

         $this->db->select('id')
            ->from('artikel')
            ->where('company_id', $this->company_id)
            ->where('slug', $slug);
         if ($id != 0) {
            $this->db->where('id !=', $id);
         }
         $q = $this->db->get();
         if ($q->num_rows() == 0) {
            $feedBack = true;
         }
         $i++;
      } while ($feedBack == false);
      return $slug;
   }

   # get photo name
   function get_photo_name($id)
   {
      $this->db->select('photo')
         ->from('artikel')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $photo = '';
      if ($q->num_rows() > 0) {
         $photo = $q->row()->photo;
      }
      return $photo;
   }

   # get info photo artikel
   function get_info_photo_artikel($id)
   {
      $this->db->select('photo, photo_caption')
         ->from('artikel')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $list['photo'] = $row->photo;
         $list['photo_caption'] = $row->photo_caption;
      }
      return $list;
   }

   # get info edit artikel
   function get_value_edit($id)
   {
      $this->db->select('id, title, photo, photo_caption, description, topic_id, place, headline')
         ->from('artikel')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['title'] = $rows->title;
            $list['photo'] = $rows->photo;
            $list['photo_caption'] = $rows->photo_caption;
            $list['description'] = $rows->description;
            $list['topic_id'] = $rows->topic_id;
            $list['place'] = $rows->place;
            $list['headline'] = $rows->headline;
         }
      }
      return $list;
   }
}
