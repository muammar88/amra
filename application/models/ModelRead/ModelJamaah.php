<?php

/**
 *  -----------------------
 *	Model Jamaah
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class ModelJamaah extends CI_Model
{
   function get_total_jamaah($search)
   {
      $this->db->select('j.id')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // $this->db->like('p.fullname', $search);
   function get_index_jamaah($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('j.id, p.fullname, p.identity_number, p.birth_place, p.birth_date, j.passport_number')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $this->db->order_by('j.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'nomor_identitas' => $row->identity_number,
               'fullname' => $row->fullname,
               'tempat_lahir' => $row->birth_place,
               'tanggal_lahir' => $this->date_ops->change_date_t4($row->birth_date),
               'nomor_passport' => $row->passport_number,
               'total_pembelian' => $this->totalBeli($row->id)
            );
         }
      }
      return array('list' => $list, 'total' => $this->get_total_jamaah($search));
   }

   // count total pembelian
   function totalBeli($id)
   {
      $this->db->select('COUNT(DISTINCT(paket_transaction_id)) AS total')
         ->from('paket_transaction_jamaah')
         ->where('jamaah_id', $id);
      $q = $this->db->get();

      return $q->row()->total;
   }

   function getJamaah()
   {
      $this->db->select('j.id, p.fullname, p.identity_number')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner');
      $q = $this->db->get();
      $return = array(0 => 'Pilih Jamaah');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->id] = $row->fullname . ' (' . $row->identity_number . ')';
         }
      }
      return $return;
   }

   function getStatusMahram()
   {
      $this->db->select('id, mahram_type_name')
         ->from('mst_mahram_type');
      $q = $this->db->get();
      $return = array(0 => 'Pilih Status Mahram');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->id] = $row->mahram_type_name;
         }
      }
      return $return;
   }

   function getPendidikan()
   {
      $this->db->select('id_pendidikan, nama_pendidikan')
         ->from('mst_pendidikan');
      $q = $this->db->get();
      $return = array(0 => "Pilih Pendidikan");
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->id_pendidikan] = $row->nama_pendidikan;
         }
      }
      return $return;
   }

   function get_personal_info($nomor_identitas)
   {
      $this->db->select('personal_id, fullname, gender, birth_place, birth_date, address, no_hp, email, identity_number, photo, username')
         ->from('personal')
         ->where('identity_number', $nomor_identitas);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return['personal_id'] = $row->personal_id;
            $return['fullname'] = $row->fullname;
            $return['gender'] = $row->gender == 'Laki-laki' ? 0 : 1;
            $return['birth_place'] = $row->birth_place;
            $return['birth_date'] = $row->birth_date;
            $return['address'] = $row->address;
            $return['no_hp'] = $row->no_hp;
            $return['email'] = $row->email;
            $return['photo'] = $row->photo;
            $return['username'] = $row->username;
         }
      }
      return $return;
   }

   function getPhotoPersonalName($id)
   {
      $this->db->select('photo')
         ->from('personal')
         ->where('personal_id', $id);
      $q = $this->db->get();
      $photo = md5(date('Ymdhis')) . '.jpeg';
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $photo = $row->photos;
      }
      return $photo;
   }

   // get data jamaah
   function getDataJamaah($id)
   {
      $this->db->select('p.personal_id, p.fullname, p.identity_number, p.gender,
                         p.photo, p.birth_place, p.birth_date, p.address, p.no_hp, p.email,
                         j.id AS jamaah_id, j.father_name, j.passport_number,
                         j.passport_dateissue, j.passport_place, j.validity_period, j.pos_code,
                         j.telephone, j.hajj_experience, j.hajj_year, j.umrah_experience, j.umrah_year,
                         j.departing_from, j.desease, j.last_education, j.blood_type,
                         j.photo_4_6, j.photo_3_4, j.fc_passport, j.profession_name, j.profession_instantion_name,
                         j.profession_instantion_address, j.profession_instantion_telephone, j.fc_kk, j.fc_ktp,
                         j.buku_nikah, j.akte_lahir, j.buku_kuning, j.keterangan, j.nama_keluarga, j.alamat_keluarga,
                         j.telephone_keluarga, j.status_nikah, j.tanggal_nikah, j.input_date, j.last_update,
                         p.username, mah.mahram_id, mah.status')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->join('mahram AS mah', 'j.id=mah.jamaah_id', 'left')
         ->where('j.id', $id);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return['personal_id'] = $row->personal_id;
            $return['fullname'] = $row->fullname;
            $return['identity_number'] = $row->identity_number;
            $return['gender'] = $row->gender;
            $return['photo'] = $row->photo;
            $return['birth_place'] = $row->birth_place;
            $return['birth_date'] = $row->birth_date;
            $return['address'] = $row->address;
            $return['no_hp'] = $row->no_hp;
            $return['email'] = $row->email;
            $return['jamaah_id'] = $row->jamaah_id;
            $return['father_name'] = $row->father_name;
            $return['passport_number'] = $row->passport_number;
            $return['passport_dateissue'] = $row->passport_dateissue;
            $return['passport_place'] = $row->passport_place;
            $return['validity_period'] = $row->validity_period;
            $return['pos_code'] = $row->pos_code;
            $return['telephone'] = $row->telephone;
            $return['hajj_experience'] = $row->hajj_experience;
            $return['hajj_year'] = $row->hajj_year;
            $return['umrah_experience'] = $row->umrah_experience;
            $return['umrah_year'] = $row->umrah_year;
            $return['departing_from'] = $row->departing_from;
            $return['desease'] = $row->desease;
            $return['last_education'] = $row->last_education;
            $return['blood_type'] = $row->blood_type;
            $return['profession_name'] = $row->profession_name;
            $return['profession_instantion_name'] = $row->profession_instantion_name;
            $return['profession_instantion_address'] = $row->profession_instantion_address;
            $return['profession_instantion_telephone'] = $row->profession_instantion_telephone;
            $return['photo_4_6'] = $row->photo_4_6 == 'ada' ? 1 : 0;
            $return['photo_3_4'] = $row->photo_3_4 == 'ada' ? 1 : 0;
            $return['fc_passport'] = $row->fc_passport == 'ada' ? 1 : 0;
            $return['fc_kk'] = $row->fc_kk == 'ada' ? 1 : 0;
            $return['fc_ktp'] = $row->fc_ktp == 'ada' ? 1 : 0;
            $return['buku_nikah'] = $row->buku_nikah == 'ada' ? 1 : 0;
            $return['akte_lahir'] = $row->akte_lahir == 'ada' ? 1 : 0;
            $return['buku_kuning'] = $row->buku_kuning == 'ada' ? 1 : 0;
            $return['keterangan'] = $row->keterangan;
            $return['nama_keluarga'] = $row->nama_keluarga;
            $return['alamat_keluarga'] = $row->alamat_keluarga;
            $return['telephone_keluarga'] = $row->telephone_keluarga;
            $return['status_nikah'] = $row->status_nikah;
            $return['tanggal_nikah'] = $row->tanggal_nikah;
            $return['mahram_id'] = $row->mahram_id != null ? $row->mahram_id : 0;
            $return['status_mahram'] = $row->status != null ? $row->status : 0;
            $return['username'] = $row->username;
         }
      }
      return $return;
   }

   // checking jamaah exist
   function checkJamaahExist($id)
   {
      $this->db->select('id')
         ->from('jamaah')
         ->where('id', $id);
      $q = $this->db->get();
      return $q->num_rows();
   }

   function infoJamaah($id)
   {
      $this->db->select('p.fullname')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('j.id', $id);
      $q = $this->db->get();
      return $q->row()->fullname;
   }

   // check exist jamaah
   function checkExistJamaahByPersonalId($personalId)
   {
      $this->db->select('id')
         ->from('jamaah')
         ->where('personal_id', $personalId);
      $q = $this->db->get();
      return $q->num_rows();
   }

   // check username
   function checkUsername($param)
   {
      $username = $param['username'];
      $this->db->select('personal_id')
         ->from('personal')
         ->where('username', $username);
      if (isset($param['personal_id'])) {
         $this->db->where('personal_id != "' . $param['personal_id'] . '"');
      }
      $q = $this->db->get();
      return $q->num_rows();
   }
}
