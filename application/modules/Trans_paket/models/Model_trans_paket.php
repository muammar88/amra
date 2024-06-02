<?php

/**
 *  -----------------------
 *	Model trans paket
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_trans_paket extends CI_Model
{
   private $company_id;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_level_agen(){
      $list = array();
      $this->db->select('id, level_agen_id')
         ->from('agen')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list[$rows->id] = $rows->level_agen_id;
         }
      }
      return $list;
   }

   // get info paket transaction
   function get_info_paket_transaction( $paket_transaction_id ){
      $this->db->select('pt.fee_keagenan_id, j.agen_id, j.id')
         ->from('paket_transaction AS pt')
         ->join('paket_transaction_jamaah AS ptj', 'pt.id=ptj.paket_transaction_id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->where('pt.id', $paket_transaction_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         # row
         $row = $q->row();
         $list = $this->fee_keagenan_deposit_paket( $row->id, $row->fee_keagenan_id );
      }
      return $list;
   }


   function fee_keagenan_deposit_paket($jamaah_id, $fee_keagenan_id){
      // get_personal_id
      $this->db->select('a.level_agen_id, a.upline')
         ->from('agen AS a')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->join('jamaah AS j', 'p.personal_id=j.personal_id', 'inner')
         ->where('a.company_id', $this->company_id)
         ->where('j.id', $jamaah_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            if( $rows->upline != 0 ){
               // echo "<br>====1=====<br>";
               $tree = $this->agen_upline_tree($rows->upline, $fee_keagenan_id);

               // echo "<br>=====<br>";
               // echo $rows->upline;
               // echo "<br>=====<br>";

               // print_r($tree);

               // echo "<br>====xxxx====<br>";
               // echo $tree[$rows->upline]['level_agen_id']."<br>";
               // echo $rows->level_agen_id."<br>";
               // echo "<br>====xxxx====<br>";
               if( count($tree) > 0 ){
                  if( $tree[$rows->upline]['level_agen_id'] > $rows->level_agen_id ) { // apabila level upline lebih besar dari level jamaah
                     $list = $tree;
                     // echo "<br>====2=====<br>";
                  }
               }
            }
         }
      }else{
         $this->db->select('agen_id')
            ->from('jamaah')
            ->where('company_id', $this->company_id)
            ->where('id', $jamaah_id);
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
               if( $rows->agen_id != 0 ) {
                  // echo "<br>====3=====<br>";
                  $list = $this->agen_upline_tree($rows->agen_id, $fee_keagenan_id);
               }
            }
         }
      }

      return $list;
   }


   function agen_upline_tree($agen_id, $fee_keagenan_id){
      $list = array();

      $feedBack = false;
      # level keagenan
      $level_keagenan = $this->fee_default_level_agen();
      $last_level = 0;
      do {
         $this->db->select('a.id, p.fullname, a.level_agen_id, a.upline, la.nama')
            ->from('agen AS a')
            ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
            ->join('level_keagenan AS la', 'a.level_agen_id=la.id', 'inner')
            ->where('a.company_id', $this->company_id)
            ->where('a.id', $agen_id);
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
               if($last_level < $rows->level_agen_id) {
                  $fee = 0;
                  if( $fee_keagenan_id != 0 ) {
                     $this->db->select('fee')
                        ->from('detail_fee_keagenan')
                        ->where('fee_keagenan_id', $fee_keagenan_id)
                        ->where('agen_id', $agen_id);
                     $s = $this->db->get();
                     if ( $s->num_rows() > 0 ) {
                        $fee = $s->row()->fee;
                     }
                     // print( "<br>==========1<br>" );   
                  } else {
                     $fee = $level_keagenan[$rows->level_agen_id];
                     // print( "<br>==========2<br>" );
                  }

                  // echo "<br>fee_keagenan_id";
                  // echo $fee;
                  // echo "<br>fee_keagenan_id";

                  # fee
                  $list[$rows->id] = array('id' => $rows->id,
                                           'level_agen_id' => $rows->level_agen_id,
                                           'level' => $rows->nama,
                                           'nama_agen' => $rows->fullname,
                                           'fee' => $fee);
                  $last_level = $rows->level_agen_id;

                  if( $rows->upline != 0 ) {
                     $agen_id = $rows->upline;
                  }else{
                     $feedBack = true;
                  }
               }else{
                  $feedBack = true;
               }
            }
         }else{
            $feedBack = true;
         }
      } while ($feedBack == false);

      // print( "<br>==========<br>" );
      // print_r( $list );
      // print( "<br>==========<br>" );

      return $list;
   }

   function fee_default_level_agen(){
      $this->db->select('id, default_fee')
         ->from('level_keagenan')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $list[$rows->id] = $rows->default_fee;
         }
      }
      return $list;
   }


   function get_paket_transaksi()
   {
      $this->db->select('p.id, p.kode, p.paket_name, p.departure_date, p.duration_trip, p.photo, p.tutup_paket,
                        (SELECT COUNT(jamaah_id)
                           FROM paket_transaction_jamaah AS ptj
                           INNER JOIN  paket_transaction AS pt ON ptj.paket_transaction_id = pt.id
                           WHERE pt.paket_id = p.id AND ptj.company_id=\'' . $this->company_id . '\') AS totalJamaah')
         ->from('paket AS p')
         ->where('p.departure_date >= NOW()')
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {

            $photo = 'image_not_found.png';
            if( $row->photo != '' ){
               $src = FCPATH . 'image/paket/' . $row->photo;
               if (file_exists($src)) {
                    $photo = $row->photo;
               }
            }

            $return[] = array(
               'id' => $row->id,
               'kode' => $row->kode,
               'paket_name' => $row->paket_name,
               'status_paket' => $row->tutup_paket,
               'departure_date' => $this->date_ops->change_date_t4($row->departure_date),
               'duration_trip' => $row->duration_trip,
               'price' => $this->getPrice($row->id),
               'photo' => $photo,
               'totalJamaah' => $row->totalJamaah
            );
         }
      }
      return $return;
   }

   function getPrice($id)
   {
      $this->db->select('price')
         ->from('paket_price')
         ->where('paket_id', $id);
      $q = $this->db->get();
      $listPrice = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $listPrice[] = $row->price;
         }
      }
      $min = min($listPrice);
      $max = max($listPrice);
      $price = array();
      if ($min == $max) {
         $prices[] = $this->text_ops->shortIDCurrency($max);
      } else {
         $prices[0] = $this->text_ops->shortIDCurrency($min);
         $prices[1] = $this->text_ops->shortIDCurrency($max);
      }
      return $prices;
   }

   function get_total_all_trans_jamaah($search)
   {
      $this->db->select('j.id')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->join('agen AS a', 'j.agen_id=a.id', 'left')
         ->join('personal AS na', 'a.personal_id=na.personal_id', 'left')
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

   function get_index_trans_jamaah($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('j.id, p.fullname, na.fullname AS nama_agen, p.identity_number, p.birth_place, p.birth_date, j.passport_number')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->join('agen AS a', 'j.agen_id=a.id', 'left')
         ->join('personal AS na', 'a.personal_id=na.personal_id', 'left')
         ->where('p.company_id', $this->company_id);
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
               'nama_agen' => $row->nama_agen, 
               'total_pembelian' => $this->_totalBeli($row->id)
            );
         }
      }
      return $list;
   }

   // count total pembelian
   function _totalBeli($id)
   {
      $this->db->select('COUNT(DISTINCT(paket_transaction_id)) AS total')
         ->from('paket_transaction_jamaah')
         ->where('jamaah_id', $id);
      $q = $this->db->get();
      return $q->row()->total;
   }

   function get_jamaah($except_jamaah_id = 0)
   {
      $this->db->select('j.id, p.fullname, p.identity_number')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('p.company_id', $this->company_id);
      if ($except_jamaah_id != 0) {
         $this->db->where('j.id != ', $except_jamaah_id);
      }
      $q = $this->db->get();
      $return = array(0 => 'Pilih Jamaah');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            
            if ( strpos($row->fullname, '"') !== false ) {
               $name = str_replace('"','',$row->fullname);
            } elseif ( strpos($row->fullname, "'") !== false ){
               $name = str_replace("'","",$row->fullname);
            }else{
               $name = $row->fullname;
            }
            
            $return[$row->id] = $name  . ' (' . $row->identity_number . ')';
         }
      }
      return $return;
   }

   function get_status_mahram()
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

   function get_pekerjaan(){
      $this->db->select('id, nama_pekerjaan')
         ->from('mst_pekerjaan');
      $q = $this->db->get();
      $return = array(0 => "Pilih Pekerjaan");
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->id] = $row->nama_pekerjaan;
         }
      }
      return $return;  
   }

   function get_pendidikan()
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
      $this->db->select('personal_id, fullname, gender, birth_place, birth_date, address, nomor_whatsapp,
                        email, identity_number, photo')
         ->from('personal')
         ->where('identity_number', $nomor_identitas)
         ->where('company_id', $this->company_id);
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
            $return['email'] = $row->email;
            $return['photo'] = $row->photo;
            $return['nomor_whatsapp'] = $row->nomor_whatsapp;
         }
      }
      return $return;
   }

   // check jamaah exist by nomor identitas
   function checkJamaahByNomorIdentitas($nomor_identitas)
   {
      $this->db->select('p.personal_id')
         ->from('personal AS p')
         ->join('jamaah AS j', 'p.personal_id=j.personal_id', 'inner')
         ->where('p.identity_number', $nomor_identitas)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   // check nomor whatsapps
   function checkNomorWhatsapp($nomor_whatsapp, $personal_id = 0)
   {
      $this->db->select('personal_id')
         ->from('personal')
         ->where('nomor_whatsapp', $nomor_whatsapp)
         ->where('company_id', $this->company_id);
      if ($personal_id != 0) {
         $this->db->where('personal_id != "' . $personal_id . '"');
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # check identity number exist
   function check_identity_number($identity_number, $personal_id = 0)
   {
      $this->db->select('personal_id')
         ->from('personal')
         ->where('company_id', $this->company_id)
         ->where('identity_number', $identity_number);
      if ($personal_id != 0) {
         $this->db->where('personal_id != ', $personal_id);
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function getPhotoPersonalName($personal_id)
   {
      $this->db->select('photo')
         ->from('personal')
         ->where('personal_id', $personal_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $photo = $row->photo;
      } else {
         $photo = md5(date('Ymdhis')) . '.jpeg';
      }
      return $photo;
   }

   // 
   function check_jamaah_is_exist($id)
   {
      $this->db->select('j.id')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('j.id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   // fullname jamaah
   function fullname_jamaah($id)
   {
      $this->db->select('p.fullname')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('j.id', $id);
      $q = $this->db->get();
      return $q->row()->fullname;
   }

   // check exist jamaah
   function check_exist_jamaah_by_personal_id($personal_id)
   {
      $this->db->select('j.id')
         ->from('jamaah AS j')
         ->join('personal AS p', 'p.personal_id=j.personal_id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('p.personal_id', $personal_id);
      $q = $this->db->get();
      return $q->num_rows();
   }

   // 
   function get_data_jamaah($id)
   {
      $this->db->select('p.personal_id, p.fullname, p.identity_number, p.gender,
                         p.photo, p.birth_place, p.birth_date, p.address, p.email,
                         j.pasport_name,j.id AS jamaah_id, j.father_name, j.passport_number, j.agen_id,
                         j.jenis_identitas, j.kewarganegaraan, j.title,
                         j.passport_dateissue, j.passport_place, j.validity_period, j.pos_code,
                         j.telephone, j.hajj_experience, j.hajj_year, j.umrah_experience, j.umrah_year,
                         j.departing_from, j.desease, j.last_education, j.blood_type,
                         j.photo_4_6, j.photo_3_4, j.fc_passport, j.pekerjaan_id, j.profession_instantion_name,
                         j.profession_instantion_address, j.profession_instantion_telephone, j.fc_kk, j.fc_ktp,
                         j.buku_nikah, j.akte_lahir, j.buku_kuning, j.keterangan, j.nama_keluarga, j.alamat_keluarga,
                         j.telephone_keluarga, j.status_nikah, j.tanggal_nikah, j.input_date, j.last_update, p.nomor_whatsapp,
                         j.kelurahan_id, v.district_id, d.regency_id, r.province_id,  
                         (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', mahram_id, status ) SEPARATOR \';\')
                            FROM mahram
                            WHERE jamaah_id=j.id) AS mahramStatus')
         ->from('jamaah AS j')
         ->join('reg_villages AS v', 'j.kelurahan_id=v.id', 'left')
         ->join('reg_districts AS d', 'v.district_id=d.id', 'left')
         ->join('reg_regencies AS r', 'd.regency_id=r.id', 'left')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->join('mahram AS mah', 'j.id=mah.jamaah_id', 'left')
         ->where('p.company_id', $this->company_id)
         ->where('j.id', $id);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return['personal_id'] = $row->personal_id;
            $return['fullname'] = $row->fullname;
            $return['identity_number'] = $row->identity_number;
            $return['gender'] = $row->gender;
            $return['agen_id'] = $row->agen_id;
            $return['nama_pasport']= $row->pasport_name;
            $photo = '';
            if ($row->photo != '') {
               $src = FCPATH . 'image/personal/' . $row->photo;
               if (file_exists($src)) {
                  $photo = 'personal/' . $row->photo;
               }
            }
            $return['photo'] = $photo;
            $return['birth_place'] = $row->birth_place;
            $return['birth_date'] = $row->birth_date;
            $return['address'] = $row->address;
            $return['nomor_whatsapp'] = $row->nomor_whatsapp;
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
            $return['pekerjaan'] = $row->pekerjaan_id;
            $return['jenis_identitas'] = $row->jenis_identitas;
            $return['kewarganegaraan'] = $row->kewarganegaraan;
            $return['title'] = $row->title;
            $return['kelurahan_id'] = $row->kelurahan_id;
            $return['kecamatan_id'] = $row->district_id;
            $return['kabupaten_kota_id'] = $row->regency_id;
            $return['provinsi_id'] = $row->province_id;
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

            $listMahram = array();
            if ($row->mahramStatus != '') {
               $exp    = explode(';', $row->mahramStatus);
               foreach ($exp as $key => $value) {
                  $exp2 = explode('$', $value);
                  $listMahram[] = array('jamaah_id' => $exp2[0], 'status' => $exp2[1]);
               }
            }
            $return['mahramStatus'] = $listMahram;
            $return['nomor_whatsapp'] = $row->nomor_whatsapp;
         }
      }
      return $return;
   }

   # list
   function get_list_provinsi_kab_kota_kec_kel_by_kelurahan_id( $district_id, $regency_id, $province_id ) {
      # get village by district 
      $this->db->select('id, name')
         ->from('reg_villages')
         ->where('district_id', $district_id);
      $kelurahan[0] = '-- Pilih Kelurahan --';
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach( $q->result()  AS $rows ) {
            $kelurahan[$rows->id] = $rows->name;
         } 
      }
      # get district by regency 
      $this->db->select('id, name')
         ->from('reg_districts')
         ->where('regency_id', $regency_id);
      $kecamatan[0] = '-- Pilih Kecamatan --';
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach( $q->result()  AS $rows ) {
            $kecamatan[$rows->id] = $rows->name;
         } 
      }
      # get regency by province  
      $this->db->select('id, name')
         ->from('reg_regencies')
         ->where('province_id', $province_id);
      $kabupaten_kota[0] = '-- Pilih Kabupaten / Kota --';
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach( $q->result()  AS $rows ) {
            $kabupaten_kota[$rows->id] = $rows->name;
         } 
      }
      # get all province 
      $this->db->select('id, name')
         ->from('reg_provinces');
      $q = $this->db->get();
      $provinsi[0] = '-- Pilih Provinsi --';   
      if( $q->num_rows() > 0 ){
         foreach( $q->result() AS $rows ) {
            //$list[] = array('id' => $rows->id, 'name' => $rows->name);
            $provinsi[$rows->id] = $rows->name;
         }
      }
      # return
      return array('kelurahan' => $kelurahan, 'kecamatan' => $kecamatan, 'kabupaten_kota' => $kabupaten_kota, 'provinsi' => $provinsi);
   }

   # check provinsi id
   function check_provinsi_id($provinsi_id){
      $this->db->select('id')
         ->from('reg_provinces')
         ->where('id', $provinsi_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # check kabupaten koda id
   function check_kabupaten_kota_id( $kabupaten_kota_id ){
      $this->db->select('id')
         ->from('reg_regencies')
         ->where('id', $kabupaten_kota_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   function check_kecamatan_id( $kecamatan_id ) {
      $this->db->select('id')
         ->from('reg_districts')
         ->where('id', $kecamatan_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function get_kecamatan_by_provinsi_id_and_kabupaten_kota_id($province_id, $kabupaten_kota_id){
      # get district by regency  
      $this->db->select('id, name')
         ->from('reg_districts')
         ->where('regency_id', $kabupaten_kota_id);
      $kecamatan[0] = '-- Pilih Kecamatan --';
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach( $q->result() AS $rows ) {
            $kecamatan[$rows->id] = $rows->name;
         }
      }
      return $kecamatan;
   }

   function get_kelurahan_by_kecamatan_id($kecamatan_id){
      $this->db->select('id, name')
         ->from('reg_villages')
         ->where('district_id', $kecamatan_id);
      $kelurahan[0] = '-- Pilih Kelurahan --';
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach( $q->result() AS $rows ) {
            $kelurahan[$rows->id] = $rows->name;
         }
      }
      return $kelurahan;     
   }

   # check kelurahan id
   function check_kelurahan_id( $kelurahan_id ){
      $this->db->select('id')
         ->from('reg_villages')
         ->where('id', $kelurahan_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # get kecamatan by provinsi id
   function get_kabupaten_kota_by_provinsi_id($province_id){
      # get regency by province  
      $this->db->select('id, name')
         ->from('reg_regencies')
         ->where('province_id', $province_id);
      $kabupaten_kota[0] = '-- Pilih Kabupaten / Kota --';
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach( $q->result() AS $rows ) {
            $kabupaten_kota[$rows->id] = $rows->name;
         }
      }
      return $kabupaten_kota;
   }

   function get_provinsi(){
      $this->db->select('id, name')
         ->from('reg_provinces');
      $q = $this->db->get();
      $list[0] = '-- Pilih Provinsi --';   
      if( $q->num_rows() > 0 ){
         foreach( $q->result() AS $rows ) {
            $list[$rows->id] = $rows->name;
         }
      }
      return $list;
   }

   # check is personal was as jamaah
   function check_jamaah_is_exist_by_personal_id($personal_id, $jamaah_id = 0)
   {
      $this->db->select('j.id')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('j.personal_id', $personal_id);
      if ($jamaah_id != 0) {
         $this->db->where('j.id !=', $jamaah_id);
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # check personal id
   function check_personal_id($personal_id)
   {
      $this->db->select('personal_id')
         ->from('personal')
         ->where('personal_id', $personal_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   // get photo name
   function get_photo_name($personal_id)
   {
      $this->db->select('photo')
         ->from('personal')
         ->where('personal_id', $personal_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return $q->row()->photo;
      } else {
         return '';
      }
   }

   # check paket ids
   function check_paket_id($paket_id)
   {
      $this->db->select('id')
         ->from('paket')
         ->where('id', $paket_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # paket name
   function get_paket_name($paket_id)
   {
      $this->db->select('paket_name')
         ->from('paket')
         ->where('id', $paket_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $paket_name = '';
      if ($q->num_rows() > 0) {
         $paket_name = $q->row()->paket_name;
      }
      return $paket_name;
   }


   function get_total_transaksi_paket($search, $paket_id)
   {
      $this->db->select('pt.id')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('p.company_id', $this->company_id)
         ->where('pt.batal_berangkat', '0');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.paket_name', $search)
            ->or_like('pt.no_register', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_transaksi_paket($limit = 6, $start = 0, $search = '', $paket_id)
   {
      $this->db->select('pt.id, pt.no_register, p.paket_name, p.id AS paket_id, ptype.paket_type_name, pt.total_paket_price, pt.total_mahram_fee,
                         pt.no_visa, pt.tgl_berlaku_visa, pt.tgl_akhir_visa, pt.payment_methode, p.departure_date, pt.diskon, p.tutup_paket,
                         (SELECT price FROM paket_price WHERE paket_id=pt.paket_id AND paket_type_id=pt.paket_type_id) AS harga')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->join('mst_paket_type AS ptype', 'pt.paket_type_id=ptype.id', 'inner')
         ->where('pt.company_id', $this->company_id)
         ->where('pt.paket_id', $paket_id)
         ->where('pt.batal_berangkat', '0');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.paket_name', $search)
            ->or_like('pt.no_register', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $sudahbayar = $this->getSudahBayar($row->id, $row->payment_methode);
            $sisa = $row->total_paket_price - $sudahbayar;
            $agen = false;
            if( count( $this->get_info_paket_transaction( $row->id ) ) > 0  ) {
               $agen = true;
            }
            $list[] = array(
               'id' => $row->id,
               'nomor_register' => $row->no_register,
               'paket_id' => $row->paket_id,
               'status_paket' => $row->tutup_paket,
               'paket_name' => $row->paket_name,
               'agen' => $agen,
               'no_visa' => ($row->no_visa != '' ? $row->no_visa : '-'), 
               'tgl_berlaku_visa' => ($row->tgl_berlaku_visa != '0000-00-00' ? $row->tgl_berlaku_visa : '-'), 
               'tgl_akhir_visa' => ($row->tgl_akhir_visa != '0000-00-00' ? $row->tgl_akhir_visa : '-'),
               'paket_type_name' => $row->paket_type_name,
               'total_paket_price' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->total_paket_price),
               'harga' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->harga),
               'metode_pembayaran' => $row->payment_methode == 0 ? 'Cash' : 'Cicilan',
               'sudah_dibayar' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($sudahbayar),
               'sisa' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($sisa),
               'fee_mahram' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->total_mahram_fee),
               'departure_date' => $this->date_ops->change_date_t4($row->departure_date),
               // 'diskon' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->diskon),
               'jamaah' => $this->getJamaahInPaket($row->id)
            );
         }
      }
      return $list;
   }

   function checkDepositTransaction($paket_transaction_id){
      $this->db->select('pdt.deposit_transaction_id, j.personal_id')
         ->from('pool_deposit_transaction AS pdt')
         ->join('pool AS p', 'pdt.pool_id=p.id', 'inner')
         ->join('jamaah AS j', 'p.jamaah_id=j.id', 'inner')
         ->where('p.paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $num = false;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            
            $this->db->select('id')
               ->from('deposit_transaction')
               ->where('company_id', $this->company_id)
               ->where('personal_id', $rows->personal_id)
               ->where('id >', $rows->deposit_transaction_id);
            $r = $this->db->get();
            if( $r->num_rows() > 0 ) {
               $num = true;
            }
         }
      }
      return $num;
   }

   function getInfoVisa($paket_transaction_id){
      $this->db->select('no_visa, tgl_berlaku_visa, tgl_akhir_visa')
         ->from('paket_transaction')
         ->where('id', $paket_transaction_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach( $q->result() AS $rows ){
            $list['no_visa'] = $rows->no_visa;
            $list['tgl_berlaku_visa'] = $rows->tgl_berlaku_visa;
            $list['tgl_akhir_visa'] = $rows->tgl_akhir_visa;
         }
      }
      return $list;
   }

   function getSudahBayar($paket_transaction_id, $payment_methode)
   {
      if ($payment_methode == 0) {
         $this->db->select('paid, ket')
            ->from('paket_transaction_history')
            ->where('paket_transaction_id', $paket_transaction_id);
         $q = $this->db->get();
         $bayar = 0;
         $refund = 0;
         $pindah_paket = 0;
         if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
               if ($row->ket == 'cash') {
                  $bayar = $bayar + $row->paid;
               } elseif ($row->ket == 'refund') {
                  $refund = $refund + $row->paid;
               } elseif ($row->ket == 'pindah_paket') {
                  $pindah_paket = $pindah_paket + $row->paid;
               }
            }
         }
         return $bayar - $refund - $pindah_paket;
      } else {
         $this->db->select('paid, ket')
            ->from('paket_transaction_installement_history')
            ->where('paket_transaction_id', $paket_transaction_id);
         $q = $this->db->get();
         $bayar = 0;
         if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
               $bayar = $bayar + $row->paid;
            }
         }
         return $bayar;
      }
   }

   function getJamaahInPaket($paket_transaction_id)
   {
      $this->db->select('p.fullname, p.identity_number')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('ptj.company_id', $this->company_id)
         ->where('ptj.paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $list = '<ul class="pl-3 list">';
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list .= '<li>' . $row->fullname . ' <br>(No ID: ' . $row->identity_number . ')</li>';
         }
      }
      $list .= '</ul>';
      return $list;
   }

   # get total jamaah paket
   function get_total_jamaah_paket($search, $paket_id)
   {
      $this->db->select('ptj.jamaah_id')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('ptj.company_id', $this->company_id)
         ->where('pt.batal_berangkat', '0');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('per.fullname', $search)
            ->or_like('per.identity_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_jamaah_paket($limit = 6, $start = 0, $search = '', $paket_id)
   {
      $this->db->select('pt.no_register, ptj.paket_transaction_id, ptj.jamaah_id, per.fullname, per.identity_number,
                           p.paket_name, p.tutup_paket, mpt.paket_type_name, pt.payment_methode,
                             (SELECT price
                                FROM paket_price
                                WHERE paket_id=pt.paket_id
                                   AND paket_type_id=pt.paket_type_id) AS harga,
                             (SELECT GROUP_CONCAT( person.fullname SEPARATOR \';\') FROM mahram AS mah
                                INNER JOIN jamaah AS jam ON mah.mahram_id=jam.id
                                INNER JOIN personal AS person ON jam.personal_id=person.personal_id
                                WHERE mah.jamaah_id=ptj.jamaah_id) AS mahrams')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->join('mst_paket_type AS mpt', 'pt.paket_type_id=mpt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('ptj.company_id', $this->company_id)
         ->where('pt.batal_berangkat', '0');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('per.fullname', $search)
            ->or_like('per.identity_number', $search)
            ->group_end();
      }
      $this->db->order_by('j.id', 'desc')->limit($limit, $start);
      $r    = $this->db->get();
      $list = array();
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $row) {
            $list[] = array(
               'jamaah_id' => $row->jamaah_id,
               'status_paket' => $row->tutup_paket,
               'paket_id' => $paket_id,
               'no_register' => $row->no_register,
               'paket_transaction_id' => $row->paket_transaction_id,
               'metode_pembayaran' => $row->payment_methode,
               'fullname' => $row->fullname,
               'identity_number' => $row->identity_number,
               'paket_name' => $row->paket_name,
               'paket_type_name' => $row->paket_type_name,
               'harga' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->harga),
               'handover_item' => $this->listItemHandover($row->paket_transaction_id, $row->jamaah_id),
               'handover_facility'  => $this->listItemFasilitas($row->paket_transaction_id, $row->jamaah_id),
               'mahram' => $row->mahrams == NULL ? 'Tidak ada mahram' : $row->mahrams
            );
         }
      }
      return $list;
   }

   # list item
   function listItemHandover($paket_transaction_id, $jamaah_id)
   {
      $this->db->select('hi.item_name')
         ->from('handover_item AS hi')
         ->join('paket_transaction AS pt', 'hi.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('hi.paket_transaction_id', $paket_transaction_id)
         ->where('p.company_id', $this->company_id)
         ->where('hi.jamaah_id', $jamaah_id)
         ->where('hi.status', 'diambil');
      $list = '';
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list .= '<li>' . $row->item_name . '</li>';
         }
      } else {
         $list .= '<li>Barang Jamaah Tidak Ditemukan</li>';
      }
      return $list;
   }

   function listItemFasilitas($paket_transaction_id, $jamaah_id)
   {
      $this->db->select('m.facilities_name')
         ->from('handover_facilities AS hf')
         ->join('paket_transaction AS pt', 'hf.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->join('mst_facilities AS m', 'hf.facilities_id=m.id', 'inner')
         ->where('hf.paket_transaction_id', $paket_transaction_id)
         ->where('p.company_id', $this->company_id)
         ->where('hf.jamaah_id', $jamaah_id);
      $q = $this->db->get();
      $list = '';
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list .= '<li>' . $row->facilities_name . '</li>';
         }
      } else {
         $list .= '<li>Fasilitas Tidak Ditemukan.</li>';
      }
      return $list;
   }

   # get price paket by paket id
   function get_price_list_paket_by_paket_id($paket_id)
   {
      $this->db->select('pp.paket_type_id, pp.price, mpt.paket_type_name')
         ->from('paket_price AS pp')
         ->join('paket AS p', 'pp.paket_id=p.id', 'inner')
         ->join('mst_paket_type AS mpt', 'pp.paket_type_id=mpt.id', 'inner')
         ->where('pp.paket_id', $paket_id)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $return = array(0 => 'Pilih Tipe Paket');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->paket_type_id] = $row->paket_type_name . ' (' . $this->session->userdata($this->config->item('apps_name'))['kurs'] . ' ' . number_format($row->price) . ')';
         }
      }
      return $return;
   }

   # get jamaah not in paket
   function get_jamaah_not_in_paket($paket_id)
   {
      // get jamaah in this paket
      $this->db->select('ptj.jamaah_id')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('p.company_id', $this->company_id)
         ->where('pt.batal_berangkat', '0');
      $q = $this->db->get();
      $jamaah_in_paket = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $jamaah_in_paket[] = $row->jamaah_id;
         }
      }

      # get jamaah not in paket
      $this->db->select('j.id, p.fullname, p.identity_number')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('p.company_id', $this->company_id);
      if (count($jamaah_in_paket) > 0) {
         $this->db->where_not_in('j.id', $jamaah_in_paket);
      }
      $q = $this->db->get();
      $return = array(0 => 'Pilih Jamaah');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $total_deposit = $this->get_total_deposit_paket($row->id);
            if( $total_deposit > 0 ){
               $return[$row->id] = $row->fullname . ' (' . $row->identity_number . ') | Total Deposit : ' . $this->session->userdata($this->config->item('apps_name'))['kurs'] . ' ' . number_format($total_deposit);
            }
         }
      }
      return $return;
   }

   #
   function get_total_deposit_paket($jamaah_id)
   {
      $this->db->select('dt.debet, dt.kredit')
         ->from('deposit_transaction AS dt')
         ->join('pool_deposit_transaction AS pdt', 'dt.id=pdt.deposit_transaction_id', 'inner')
         ->join('pool AS p', 'pdt.pool_id=p.id', 'inner')
         ->where('dt.company_id', $this->company_id)
         ->where('p.jamaah_id', $jamaah_id)
         ->where('p.active', 'active');
      $q = $this->db->get();
      $debet = 0;
      $kredit = 0;
      // $total_deposit = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $debet = $debet + $rows->debet;
            $kredit = $kredit + $rows->kredit;
         }
      }

      return ($debet - $kredit);
   }

   # get agen
   function get_agen()
   {
      $this->db->select('a.id, p.fullname, p.identity_number')
         ->from('agen AS a')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $return = array(0 => 'Pilih Agen');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->id] = $row->fullname . ' (' . $row->identity_number . ')';
         }
      }
      return $return;
   }

   // check paket price
   function check_price_paket($paket_id, $paket_type_id)
   {
      $this->db->select('pp.paket_id')
         ->from('paket_price AS pp')
         ->join('paket AS p', 'pp.paket_id=p.id', 'inner')
         ->where('pp.paket_id', $paket_id)
         ->where('p.company_id', $this->company_id)
         ->where('pp.paket_type_id', $paket_type_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get price transaksi paket
   function get_price_transaksi_paket($paket_id, $paket_type_id)
   {
      // get price
      $this->db->select('pp.price')
         ->from('paket_price AS pp')
         ->join('paket AS p', 'pp.paket_id=p.id', 'inner')
         ->where('pp.paket_id', $paket_id)
         ->where('p.company_id', $this->company_id)
         ->where('pp.paket_type_id', $paket_type_id);
      $q = $this->db->get();
      $harga = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $harga = $row->price;
         }
      }
      return $harga;
   }

   # get biaya mahram
   function get_biaya_mahram($paket_id)
   {
      $this->db->select('mahram_fee')
         ->from('paket')
         ->where('company_id', $this->company_id)
         ->where('id', $paket_id);
      $r = $this->db->get();
      $biaya_mahram = 0;
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $row) {
            $biaya_mahram = $row->mahram_fee;
         }
      }
      return $biaya_mahram;
   }

   # get info need mahram
   function get_info_need_mahram($jamaah_id)
   {
      $this->db->select('gender, birth_date')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('id', $jamaah_id)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $return = false;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $umur = $this->date_ops->get_umur($row->birth_date);
            if ($row->gender == '0') {
               if ($umur <= 17) {
                  $return = true;
               }
            } elseif ($row->gender == '1') {
               if ($umur <= 46) {
                  $return = true;
               }
            }
         }
      }
      return $return;
   }

   # get personal info
   function depositorPaket()
   {
      $this->db->select('p.id, per.fullname, per.identity_number')
         ->from('pool AS p')
         ->join('jamaah AS j', 'p.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('p.active', 'active');
      $q = $this->db->get();
      $list = array(array('id' => 0, 'name' => 'Pilih Penyetor'));
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $deposit_paket = $this->get_deposit_paket_jamaah($rows->id);
            if ($deposit_paket > 0) {
               $list[] = array('id' => $rows->id, 'name' => $rows->fullname . ' (' . $rows->identity_number . '); Deposit : ' . $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($deposit_paket) . ' ');
            }
         }
      }
      return $list;
   }

   function get_info_paket( $paket_id ){
      $paket_name = '';
      $kode_paket = '';

      $this->db->select('paket_name, kode')
         ->from('paket')
         ->where('id', $paket_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         foreach( $q->result() AS $rows){
            $paket_name = $rows->paket_name;
            $kode_paket = $rows->kode; 
         }
      }
      return array('paket_name' => $paket_name, 'kode_paket' => $kode_paket);
   }

   function get_deposit_paket_jamaah($pool_id)
   {
      $this->db->select('dt.debet')
         ->from('pool_deposit_transaction AS pdt')
         ->join('deposit_transaction AS dt', 'pdt.deposit_transaction_id=dt.id', 'inner')
         ->where('pdt.pool_id', $pool_id)
         ->where('pdt.company_id', $this->company_id);
      $q = $this->db->get();
      $total_deposit = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $total_deposit = $total_deposit + $rows->debet;
         }
      }
      return $total_deposit;
   }


   # company id
   function getDepositInfo($personal_id)
   {
      $this->db->select('dt.debet, dt.kredit')
         ->from('deposit_transaction AS dt')
         ->join('personal AS p', 'dt.personal_id=p.personal_id', 'inner')
         ->where('dt.personal_id', $personal_id)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $debet = 0;
      $kredit = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $debet = $debet + $row->debet;
            $kredit = $kredit + $row->kredit;
         }
      }
      return $debet - $kredit;
   }

   # check paket transaction id
   function check_paket_transaction_id($paket_transaction_id)
   {
      $this->db->select('pt.id')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pt.id', $paket_transaction_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function checkNoRegisterExist($no_register, $paket_transaction_id = 0)
   {
      $this->db->select('pt.id')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.no_register', $no_register)
         ->where('p.company_id', $this->company_id);
      if ($paket_transaction_id != 0) {
         $this->db->where('pt.id != ' . $paket_transaction_id);
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # check paket type paket
   function checkPaketTypePaket($paket_id, $paket_type_id)
   {
      $this->db->select('pp.paket_type_id')
         ->from('paket_price AS pp')
         ->join('paket AS p', 'pp.paket_id=p.id', 'inner')
         ->where('pp.paket_id', $paket_id)
         ->where('pp.paket_type_id', $paket_type_id)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return  FALSE;
      }
   }

   function checkAgenExist($agenID)
   {
      $this->db->select('a.id')
         ->from('agen AS a')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('a.id', $agenID)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return  FALSE;
      }
   }

   function checkNoInvoiceExist($invoice, $paket_transaction_id = 0)
   {
      $this->db->select('pth.id')
         ->from('paket_transaction_history AS pth')
         ->join('paket_transaction AS pt', 'pth.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pth.invoice', $invoice)
         ->where('p.company_id', $this->company_id);
      if ($paket_transaction_id != 0) {
         $this->db->where('pth.id != ' . $paket_transaction_id);
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function checkDepositPembayaran($pembayaran, $penyetor)
   {
      $this->db->select('dt.debet, dt.kredit')
         ->from('deposit_transaction AS dt')
         ->join('personal AS p', 'dt.personal_id=p.personal_id', 'inner')
         ->where('dt.personal_id', $penyetor)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $debet = 0;
      $kredit = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $debet = $debet + $row->debet;
            $kredit = $kredit + $row->kredit;
         }
      }

      $deposit = $debet - $kredit;
      if ($deposit < 0) {
         $deposit = 0;
      }

      if ($pembayaran <= $deposit) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function getNamaCodePaket($paket_id)
   {
      $this->db->select('kode, paket_name')
         ->from('paket')
         ->where('company_id', $this->company_id)
         ->where('id', $paket_id);
      $q = $this->db->get();
      // $kode = 'NotFound';
      $array = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $array['kode'] = $row->kode;
         $array['nama'] = $row->paket_name;
      }
      return $array;
   }


   function getPaketPricePerType($paket_id, $paket_type_id)
   {
      $this->db->select('pp.price')
         ->from('paket_price AS pp')
         ->join('paket AS p', 'pp.paket_id=p.id', 'inner')
         ->where('pp.paket_id', $paket_id)
         ->where('pp.paket_type_id', $paket_type_id);
      $q = $this->db->get();
      $price = 0;
      if ($q->num_rows() > 0) {
         $price = $q->row()->price;
      }
      return $price;
   }

   function getBiayaMahram($paket_id)
   {
      $this->db->select('mahram_fee')
         ->from('paket')
         ->where('company_id', $this->company_id)
         ->where('id', $paket_id);
      $r = $this->db->get();
      $biaya_mahram = 0;
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $row) {
            $biaya_mahram = $row->mahram_fee;
         }
      }
      return $biaya_mahram;
   }

   function getInfoDepositorByJamaahId($jamaah_id)
   {
      $this->db->select('p.fullname, p.nomor_whatsapp, p.address')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('j.id', $jamaah_id)
         ->where('j.company_id', $this->company_id);
      $q = $this->db->get();
      $array = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $array['fullname'] = $row->fullname;
         $array['nomor_whatsapp'] = $row->nomor_whatsapp;
         $array['address'] = $row->address;
      }
      return $array;
   }

   function getPenyetorName($personal_id)
   {
      $this->db->select('fullname, nomor_whatsapp, address')
         ->from('personal')
         ->where('personal_id', $personal_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $array = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $array['fullname'] = $row->fullname;
         $array['nomor_whatsapp'] = $row->nomor_whatsapp;
         $array['address'] = $row->address;
      }
      return $array;
   }

   // get info transaction paket
   function getInfoTransaksiPaket($paket_id, $paket_transaction_id)
   {
      $this->db->select('pt.no_register, pt.payment_methode, pt.total_paket_price, ptj.jamaah_id, j.personal_id, pt.fee_keagenan_id')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->join('paket_transaction_jamaah AS ptj', 'pt.id=ptj.paket_transaction_id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('p.company_id', $this->company_id)
         ->where('pt.id', $paket_transaction_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         return array('no_register' => $row->no_register, 
                      'payment_methode' => $row->payment_methode, 
                      'total_price' => $row->total_paket_price, 
                      'jamaah_id' => $row->jamaah_id, 
                      'personal_id' => $row->personal_id,
                      'fee_keagenan_id' => $row->fee_keagenan_id);
      }
   }

   # get jumlah pembayaran
   function getJumlahPembayaran($paket_transaction_id)
   {
      $this->db->select('pth.paid, pth.ket')
         ->from('paket_transaction_history AS pth')
         ->join('paket_transaction AS pt', 'pth.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pth.paket_transaction_id', $paket_transaction_id);
      $q  = $this->db->get();
      $bayar = 0;
      $refund = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if ($row->ket == 'cash') {
               $bayar = $bayar + $row->paid;
            } elseif ($row->ket == 'refund') {
               $refund = $refund + $row->paid;
            }
         }
      }
      return array('bayar' => $bayar, 'refund' => $refund);
   }

   function getRiwayatTransactionCash($paket_transaction_id)
   {
      // get history transaksi
      $this->db->select('pth.invoice, pth.paid, pth.ket, pth.receiver, pth.deposit_name, pth.source, pth.last_update')
         ->from('paket_transaction_history AS pth')
         ->join('paket_transaction AS pt', 'pth.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pth.paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $array = array();
      $total_bayar = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $array[] = array(
               'invoice' => $row->invoice,
               'paid' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->paid),
               'penerima' => $row->receiver,
               'ket' => $row->ket,
               'sumber_biaya' => $row->source,
               'penyetor' => $row->deposit_name,
               'tanggal_transaksi' => $row->last_update
            );
            if ($row->ket == 'cash') {
               $total_bayar = $total_bayar + $row->paid;
            } elseif ($row->ket == 'refund') {
               $total_bayar = $total_bayar - $row->paid;
            }
         }
      }
      return array('list' => $array, 'total_bayar' => $total_bayar);
   }

   // get info transaksi paket cash
   function getInfoPembayaranCash($paket_transaction_id)
   {
      // get history transaksi
      $this->db->select('pth.invoice, pth.paid, pth.ket, pth.receiver, pth.deposit_name, pth.source, pth.last_update')
         ->from('paket_transaction_history AS pth')
         ->join('paket_transaction AS pt', 'pth.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pth.paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $array = array();
      $total_bayar = 0;
      $total_bayar_not_dp = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $array[] = array(
               'invoice' => $row->invoice,
               'paid' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->paid),
               'penerima' => $row->receiver,
               'ket' => $row->ket,
               'sumber_biaya' => $row->source,
               'penyetor' => $row->deposit_name,
               'tanggal_transaksi' => $row->last_update
            );
            if ($row->ket == 'cash') {
               $total_bayar = $total_bayar + $row->paid;
            } elseif ($row->ket == 'refund') {
               $total_bayar = $total_bayar - $row->paid;
            } elseif ($row->ket == 'pindah_paket') {
               $total_bayar = $total_bayar - $row->paid;
            }
         }
      }

      // get total price
      $this->db->select('pt.total_paket_price')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pt.id', $paket_transaction_id);
      $total_price = 0;
      $r = $this->db->get();
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $rows) {
            $total_price = $total_price + $rows->total_paket_price;
         }
      }

      // feedback
      return array(
         'list' => $array,
         'total_harga' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($total_price),
         'total_bayar' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($total_bayar),
         'sisa' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($total_price - $total_bayar),
         'invoice' => $this->text_ops->get_invoice_transaksi_paket_cash()
      );
   }

   // get last info kwitansi cash
   function getLastInfoKwitansiCash($paket_transaction_id)
   {
      $this->db->select('ptih.invoice, pt.no_register')
         ->from('paket_transaction_history AS ptih')
         ->join('paket_transaction AS pt', 'ptih.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('ptih.paket_transaction_id', $paket_transaction_id)
         ->order_by('ptih.input_date', 'asc');
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack['invoice'] = $row->invoice;
            $feedBack['no_register'] = $row->no_register;
         }
      }
      return $feedBack;
   }

   // check invoice cash
   function checkInvoiceCash($invoice)
   {
      $this->db->select('id')
         ->from('paket_transaction_history')
         ->where('invoice', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return FALSE;
      } else {
         return TRUE;
      }
   }

   // check personal exist
   function checkPersonalExist($personal_id)
   {
      $this->db->select('personal_id')
         ->from('personal')
         ->where('personal_id', $personal_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   // get no register
   function getNoRegister($paket_transaction_id)
   {
      $this->db->select('pt.no_register')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pt.id', $paket_transaction_id);
      $q = $this->db->get();
      $no_register = 'Tidak ditemukan.';
      if ($q->num_rows() > 0) {
         $no_register = $q->row()->no_register;
      }
      return $no_register;
   }

   function getInfoHandOverBarang($paket_transaction_id, $jamaah_id)
   {
      $this->db->select('h.id, h.item_name, h.status, h.date_taken, h.date_returned, h.giver_handover,
                           IF(h.receiver_handover != \'0\',
                                 (SELECT fullname
                                  FROM personal
                                  WHERE personal_id=h.receiver_handover
                                       AND company_id=\'' . $this->company_id . '\' ), \'Administrator\') AS receiver_handover,
                           IF(h.giver_returned != \'0\',
                                (SELECT fullname
                                 FROM personal
                                 WHERE personal_id=h.giver_returned
                                       AND company_id=\'' . $this->company_id . '\'), \'Administrator\') AS giver_returned,
                           h.receiver_returned ')
         ->from('handover_item AS h')
         ->join('jamaah AS j', 'h.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('h.paket_transaction_id', $paket_transaction_id)
         ->where('p.company_id', $this->company_id)
         ->where('h.jamaah_id', $jamaah_id);
      $q = $this->db->get();
      $list_barang = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list_barang[$row->id] = array(
               'id' => $row->id,
               'item_name' => $row->item_name,
               'status' => $row->status,
               'giver_handover' => $row->giver_handover,
               'receiver_handover' => $row->receiver_handover,
               'giver_returned' => $row->giver_returned != null ? $row->giver_returned : '-',
               'receiver_returned' => $row->receiver_returned != '' ? $row->receiver_returned : '-',
               'date_taken' => $row->date_taken,
               'date_returned' => $row->date_returned
            );
         }
      }
      return $list_barang;
   }

   # @return true if exist
   function checkInvoiceHandover($invoice)
   {
      $this->db->select('invoice_handover')
         ->from('handover_item')
         ->where('invoice_handover', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # return true if exist
   function checkInvoiceReturned($invoice)
   {
      $this->db->select('invoice_returned')
         ->from('handover_item')
         ->where('invoice_returned', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function facilities_list($paket_transaction_id, $jamaah_id, $handover_facilities_id = 0)
   {
      $this->db->select('hf.id, hf.invoice, hf.facilities_id, m.facilities_name, hf.receiver_name, hf.receiver_identity, hf.date_transaction,
                           IF(hf.officer != \'0\',
                              (SELECT fullname
                               FROM personal
                               WHERE personal_id=hf.officer
                                    AND company_id=\'' . $this->company_id . '\' ), \'Administrator\') AS officers')
         ->from('handover_facilities AS hf')
         ->join('mst_facilities AS m', 'hf.facilities_id=m.id', 'inner')
         ->where('hf.paket_transaction_id', $paket_transaction_id)
         ->where('hf.jamaah_id', $jamaah_id);
      if ($handover_facilities_id != 0) {
         $this->db->where('hf.id != ', $handover_facilities_id);
      }
      $q = $this->db->get();
      $list_id = array();
      $list_barang = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list_id[] = $row->id;
            $list_barang[] = array(
               'id' => $row->id,
               'invoice' => $row->invoice,
               'facilities_id' => $row->facilities_id,
               'facilities_name' => $row->facilities_name,
               'petugas' => $row->officers,
               'receiver_name' => $row->receiver_name,
               'receiver_identity' => $row->receiver_identity,
               'date_transaction' => $row->date_transaction
            );
         }
      }
      return array('list_id' => $list_id, 'list_barang' => $list_barang);
   }

   function getInfoHandOverFasilitas($paket_transaction_id, $jamaah_id)
   {
      # list facilities
      $facilities_list = $this->facilities_list($paket_transaction_id, $jamaah_id);
      # get fasilitas
      $this->db->select('id, facilities_name')
         ->from('mst_facilities');
      if (count($facilities_list['list_id']) > 0) {
         $this->db->where_not_in('id', $facilities_list['list_id']);
      }
      $q = $this->db->get();
      $list_fasilitas = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list_fasilitas[] = array('id' => $row->id, 'name' => $row->facilities_name);
         }
      }
      return array('list_barang' => $facilities_list['list_barang'], 'list_fasilitas' => $list_fasilitas);
   }

   function checkInvoiceFasilitas($invoice)
   {
      $this->db->select('hf.invoice')
         ->from('handover_facilities AS hf')
         ->join('jamaah AS j', 'hf.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('hf.invoice', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function list_handover_facilities($handover_facilities_id)
   {
      $this->db->select('hf.jamaah_id, hf.paket_transaction_id, p.fullname, pkt.paket_name')
         ->from('handover_facilities AS hf')
         ->join('jamaah AS j', 'hf.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->join('paket_transaction AS pt', 'hf.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS pkt', 'pt.paket_id=pkt.id', 'inner')
         ->where('hf.id', $handover_facilities_id)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $jamaah_id = 0;
      $paket_transaction_id = 0;
      $fullname = '';
      $paket_name = '';
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $jamaah_id = $row->jamaah_id;
         $paket_transaction_id = $row->paket_transaction_id;
         $fullname = $row->fullname;
         $paket_name = $row->paket_name;
      }

      return array(
         'facilities_list' => $this->facilities_list($paket_transaction_id, $jamaah_id, $handover_facilities_id)['list_barang'],
         'fullname' => $fullname,
         'paket_name' => $paket_name
      );
   }

   function check_handover_facilities_id($handover_facilities_id)
   {
      $this->db->select('id')
         ->from('handover_facilities')
         ->where('id', $handover_facilities_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }


   function getPaketNotThis($paket_id, $jamaah_id)
   {
      # paket jamaah
      $this->db->select('pt.paket_id')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('ptj.jamaah_id', $jamaah_id)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $listPaketJamaah = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if (!in_array($row->paket_id, $listPaketJamaah)) {
               $listPaketJamaah[] = $row->paket_id;
            }
         }
      }

      # paket
      $this->db->select('id, paket_name')
         ->from('paket')
         ->where(' id != "' . $paket_id . '"')
         ->where('departure_date >= NOW()')
         ->where('company_id', $this->company_id)
         ->where_not_in('id', $listPaketJamaah);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id, 'paket_name' => $row->paket_name);
         }
      }
      return $list;
   }

   function getInfoJamaah($jamaah_id)
   {
      $this->db->select('j.id, p.fullname')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('j.id', $jamaah_id)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack['id'] = $row->id;
            $feedBack['fullname'] = $row->fullname;
         }
      }
      return $feedBack;
   }

   function getInfoPaketSekarang($paket_transaction_id)
   {
      $this->db->select('p.paket_name, pt.price_per_pax, pt.total_paket_price')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pt.id', $paket_transaction_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $transaksiPembayaran = $this->getHistoryPaketTransaction($paket_transaction_id, $row->total_paket_price);
            $feedBack['paket_sekarang'] = $row->paket_name;
            $feedBack['total_harga_paket_sekarang'] = $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->total_paket_price);
            $feedBack['harga_per_paket_sekarang'] = $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->price_per_pax);
            $feedBack['biaya_yang_sudah_dibayar_sekarang'] = $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($transaksiPembayaran['sudahBayar']);
            $feedBack['sisa_pembayaran_sekarang'] = $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($transaksiPembayaran['sisaBayar']);
         }
      }
      return $feedBack;
   }

   function getHistoryPaketTransaction($paket_transaction_id, $total_pembayaran)
   {
      $sudahBayar = $this->getTransaksiPaketSudahBayar($paket_transaction_id);
      return array('sudahBayar' => $sudahBayar, 'sisaBayar' => $total_pembayaran - $sudahBayar);
   }

   function getTransaksiPaketSudahBayar($paket_transaction_id)
   {
      $this->db->select('paid, ket')
         ->from('paket_transaction_history')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $sudah_dibayar = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if ($row->ket == 'cash') {
               $sudah_dibayar = $sudah_dibayar + $row->paid;
            } elseif ($row->ket == 'refund') {
               $sudah_dibayar = $sudah_dibayar - $row->paid;
            } elseif ($row->ket == 'pindah_paket') {
               $sudah_dibayar = $sudah_dibayar - $row->paid;
            }
         }
      }
      return $sudah_dibayar;
   }

   function checkTipePaketPrice($paket_id, $paket_type_id)
   {
      $this->db->select('pp.paket_id')
         ->from('paket_price AS pp')
         ->join('paket AS p', 'pp.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pp.paket_id', $paket_id)
         ->where('pp.paket_type_id', $paket_type_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function checkNoRegisterPaketPrice($paket_id, $no_register)
   {
      $this->db->select('pt.paket_id')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pt.paket_id', $paket_id)
         ->where('pt.no_register', $no_register);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function getPriceByPaketTipePaket($paket_id, $tipe_paket_id, $biaya_yang_dipindah, $paket_transaction_id_now, $refund = 0, $jamaah_id)
   {
      $this->db->select('pp.price, p.mahram_fee')
         ->from('paket_price AS pp')
         ->join('paket AS p', 'pp.paket_id=p.id', 'inner')
         ->where('pp.paket_id', $paket_id)
         ->where('p.company_id', $this->company_id)
         ->where('pp.paket_type_id', $tipe_paket_id);
      $q = $this->db->get();
      $price = 0;
      $mahram_fee = 0;
      $biaya_mahram = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $price = $row->price;
            $mahram_fee = $row->mahram_fee;
         }
      }
      $sisa_pembayaran = ($price + $refund) - $biaya_yang_dipindah;
      if ($this->get_info_need_mahram($jamaah_id)) {
         $sisa_pembayaran = $sisa_pembayaran + $mahram_fee;
         $biaya_mahram = $mahram_fee;
      }
      $pembayaran_berlebih = $biaya_yang_dipindah - ($price + $refund) - $biaya_mahram;

      return array(
         'harga_paket_tujuan' => $price,
         'sisa_pembayaran' =>  $sisa_pembayaran < 0 ? 0 : $sisa_pembayaran,
         'pembayaran_berlebih' => $pembayaran_berlebih < 0 ? 0 : $pembayaran_berlebih,
         'biaya_mahram' => $biaya_mahram
      );
   }

   function getPriceByPaketNoRegister($paket_id, $no_register, $biaya_yang_dipindah, $paket_transaction_id_now, $refund = 0, $jamaah_id)
   {
      // get info
      $this->db->select('pt.id, pt.total_paket_price, pt.price_per_pax, p.mahram_fee')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.no_register', $no_register)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $total_paket_price = 0;
      $price = 0;
      $mahram_fee = 0;
      $biaya_mahram = 0;
      $paket_transaction_id = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $paket_transaction_id = $row->id;
            $total_paket_price = $row->total_paket_price;
            $price = $row->price_per_pax;
            $mahram_fee = $row->mahram_fee;
         }
      }

      $sudahBayar = 0;
      $this->db->select('paid, ket')
         ->from('paket_transaction_history')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $refundPT = 0;
         $pindahPT = 0;
         $cashPT = 0;
         foreach ($q->result() as $row) {
            if ($row->ket == 'cash') {
               $cashPT = $cashPT + $row->paid;
            } elseif ($row->ket == 'refund') {
               $refundPT = $refundPT + $row->paid;
            } elseif ($row->ket == 'pindah_paket') {
               $pindahPT = $pindahPT + $row->paid;
            }
         }
         $sudahBayar = $cashPT - ($refundPT + $pindahPT);
      }

      if ($this->get_info_need_mahram($jamaah_id)) {
         $biaya_mahram = $mahram_fee;
      }

      $totalBiayaJamaah = $price + $biaya_mahram;
      $totalPembayaranSekarang = $total_paket_price + $totalBiayaJamaah + $refund;
      $sisa_pembayaran = $totalPembayaranSekarang  - ($sudahBayar + $biaya_yang_dipindah);
      $pembayaran_berlebih = $sudahBayar + $biaya_yang_dipindah - $totalPembayaranSekarang;

      return array(
         'harga_paket_tujuan' => $price,
         'sisa_pembayaran' =>  $sisa_pembayaran < 0 ? 0 : $sisa_pembayaran,
         'pembayaran_berlebih' => $pembayaran_berlebih < 0 ? 0 : $pembayaran_berlebih,
         'biaya_mahram' => $biaya_mahram
      );
   }


   function getInfoTipePaket($paket_id)
   {
      $this->db->select('pp.paket_type_id, pp.price, mp.paket_type_name')
         ->from('paket_price AS pp')
         ->join('mst_paket_type AS mp', 'pp.paket_type_id=mp.id', 'inner')
         ->join('paket AS p', 'pp.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pp.paket_id', $paket_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack[] = array('id' => $row->paket_type_id, 'paket_type_name' => $row->paket_type_name, 'price' => $row->price);
         }
      }
      return $feedBack;
   }

   // info get info no register
   function getInfoNoRegister($paket_id)
   {
      $this->db->select('pt.id, pt.no_register, pt.price_per_pax')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack[] = array('id' => $row->id, 'no_register' => $row->no_register, 'price' => $row->price_per_pax);
         }
      }
      return $feedBack;
   }


   // check jamaah if in paket
   public function checkJamaahInPaket($jamaah_id, $paket_transaction_id)
   {
      $this->db->select('jamaah_id')
         ->from('paket_transaction_jamaah')
         ->where('paket_transaction_id', $paket_transaction_id)
         ->where('jamaah_id', $jamaah_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function checkPaketExist($paket_id)
   {
      $this->db->select('id, paket_name, photo')
         ->from('paket')
         ->where('company_id', $this->company_id)
         ->where('id', $paket_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         return array('success' => TRUE, 'paket_name' => $row->paket_name, 'photo' => $row->photo);
      } else {
         return array('success' => FALSE, 'paket_name' => 'Not Found');
      }
   }

   # check total jamaah not in
   function checkTotalJamaahNotIn($paket_transaction_id, $jamaah_id)
   {
      $this->db->select('ptj.jamaah_id')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('ptj.paket_transaction_id', $paket_transaction_id)
         ->where('p.company_id', $this->company_id)
         ->where('ptj.jamaah_id != "' . $jamaah_id . '" ');
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # check tipe paket or no register if exist
   function checkTipeNoRegisterTujuan($paket_id, $tipe_aksi, $tipe_no_register)
   {
      if ($tipe_aksi == 0) {
         $this->db->select('pp.price')
            ->from('paket_price AS pp')
            ->join('paket AS p', 'pp.paket_id=p.id', 'inner')
            ->where('pp.paket_id', $paket_id)
            ->where('p.company_id', $this->company_id)
            ->where('pp.paket_type_id', $tipe_no_register);
         $q = $this->db->get();
         if ($q->num_rows() > 0) {
            return TRUE;
         } else {
            return FALSE;
         }
      } elseif ($tipe_aksi == 1) {
         $this->db->select('pt.id')
            ->from('paket_transaction AS pt')
            ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
            ->where('p.company_id', $this->company_id)
            ->where('pt.paket_id', $paket_id)
            ->where('pt.no_register', $tipe_no_register);
         $q = $this->db->get();
         if ($q->num_rows() > 0) {
            return TRUE;
         } else {
            return FALSE;
         }
      } else {
         return FALSE;
      }
   }


   # get info paket
   function getInfoPaketAsal($paket_transaction_id, $jamaah_id)
   {

      // echo "<br>=====Jamaah ID=====<br>";
      // echo $jamaah_id;
      // echo "<br>==========================<br>";
      // echo "<br>=====Paket Transaction ID====<br>";
      // echo $paket_transaction_id;
      // echo "<br>==========================<br>";

      $this->db->select('p.id AS paket_id, p.kode, p.paket_name, p.mahram_fee, pt.paket_type_id, pt.no_register,
                         pt.price_per_pax, mpt.paket_type_name')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->join('mst_paket_type AS mpt', 'pt.paket_type_id=mpt.id', 'inner')
         ->where('pt.id', $paket_transaction_id)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $feedBack = array();
      $mahram_fee = 0;
      $price_per_pax = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack['kode'] = $row->kode;
            $feedBack['paket_id'] = $row->paket_id;
            $feedBack['paket_transaction_id'] = $paket_transaction_id;
            $feedBack['paket_name'] = $row->paket_name;
            $feedBack['no_register'] = $row->no_register;
            $feedBack['price_per_pax'] = $row->price_per_pax;
            $feedBack['paket_type_name'] = $row->paket_type_name;
            $feedBack['paket_type_id'] = $row->paket_type_id;
            $mahram_fee = $row->mahram_fee;
            $price_per_pax = $row->price_per_pax;
         }
      }
      // kalkulasi paket lama
      $this->db->select('jamaah_id')
         ->from('paket_transaction_jamaah')
         ->where('paket_transaction_id', $paket_transaction_id)
         ->where('jamaah_id != "' . $jamaah_id . '"');
      $q = $this->db->get();
      $num = $q->num_rows();
      if ($num > 0) {
         $feedBack['kalkulasiPaketLama'] = true;
         $jamaah_needMahram = 0;
         foreach ($q->result() as $rows) {
            $feedBackMahram = $this->get_info_need_mahram($rows->jamaah_id);
            if ($feedBackMahram === TRUE) {
               $jamaah_needMahram++;
            }
         }

         # total jamaah yang membutuhkan mahram
         $totalBiayaMahram = $mahram_fee * $jamaah_needMahram;
         $totalPaketPrice = ($num * $price_per_pax) + $totalBiayaMahram;

         // echo "<br>=====Jumlah NUM====<br>";
         // echo $num;
         // echo "<br>=====Total Mahram Fee=====<br>";
         // echo $totalBiayaMahram;
         // echo "<br>==========================<br>";
         // echo "<br>=====Total Paket Price====<br>";
         // echo $totalPaketPrice;
         // echo "<br>==========================<br>";

         $feedBack['total_mahram_fee'] = $totalBiayaMahram;
         $feedBack['total_paket_price'] = $totalPaketPrice;
      } else {
         $feedBack['kalkulasiPaketLama'] = false;
      }

      return $feedBack;
   }

   // get info jamaah pindah paket
   function getInfoJamaahPindahPaket($jamaah_id)
   {
      $this->db->select('p.fullname, p.address, p.nomor_whatsapp')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('j.id', $jamaah_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack['fullname'] = $row->fullname;
            $feedBack['address'] = $row->address;
            $feedBack['nomor_whatsapp'] = $row->nomor_whatsapp;
            $feedBack['jamaah_id'] = $jamaah_id;
            $feedBack['needMahram'] = $this->get_info_need_mahram($jamaah_id);
         }
      }
      return $feedBack;
   }

   # get info paket tujuan
   function getInfoPaketTujuan($paket_id, $tipe_aksi, $tipe_noreg, $jamaah_id)
   {
      if ($tipe_aksi == 0) {
         $no_register = $this->text_ops->get_no_register();
         $this->db->select('p.id AS paket_id, pc.price, p.kode, p.paket_name, p.mahram_fee, mpt.paket_type_name')
            ->from('paket_price AS pc')
            ->join('paket AS p', 'pc.paket_id=p.id', 'inner')
            ->join('mst_paket_type AS mpt', 'pc.paket_type_id=mpt.id', 'inner')
            ->where('pc.paket_id', $paket_id)
            ->where('p.company_id', $this->company_id)
            ->where('pc.paket_type_id', $tipe_noreg);
      } else {
         $no_register = $tipe_noreg;
         $this->db->select('p.id AS paket_id, p.kode, p.paket_name, p.mahram_fee, mpt.paket_type_name,
                            pt.id AS paket_transaction_id, pt.paket_type_id, pt.price_per_pax,
                            pt.total_mahram_fee, pt.total_paket_price')
            ->from('paket_transaction AS pt')
            ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
            ->join('mst_paket_type AS mpt', 'pt.paket_type_id=mpt.id', 'inner')
            ->where('p.company_id', $this->company_id)
            ->where('pt.no_register', $tipe_noreg);
      }
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack['kode'] = $row->kode;
            $feedBack['paket_id'] = $row->paket_id;
            $feedBack['paket_name'] = $row->paket_name;
            $feedBack['no_register'] = $no_register;
            $feedBack['mahram_fee'] = $row->mahram_fee;
            if ($tipe_aksi == 0) {
               $feedBack['price_per_pax'] = $row->price;
            } else {
               $feedBack['price_per_pax'] = $row->price_per_pax;
            }
            $feedBack['paket_type_name'] = $row->paket_type_name;

            if ($tipe_aksi == 1) {
               $feedBack['updatePaketTujuan'] = true;
               $total_paket_price = $row->total_paket_price + $row->price_per_pax;
               $total_mahram_fee = $row->total_mahram_fee;
               if ($this->get_info_need_mahram($jamaah_id) === TRUE) {
                  $total_mahram_fee = $total_mahram_fee + $row->mahram_fee;
                  $total_paket_price = $total_paket_price + $row->mahram_fee;
               }
               $feedBack['paket_transaction_id'] = $row->paket_transaction_id;
               $feedBack['total_mahram_fee'] = $total_mahram_fee;
               $feedBack['total_paket_price'] = $total_paket_price;
            } else {
               $feedBack['updatePaketTujuan'] = false;
            }

            if ($tipe_aksi == 0) {
               $feedBack['paket_type_id'] = $tipe_noreg;
            } else {
               $feedBack['paket_type_id'] = $row->paket_type_id;
            }
         }
      }
      return $feedBack;
   }

   // get info transaksi cicilan
   function get_info_transaksi_cicilan($paket_transaction_id)
   {
      $this->db->select('pt.no_register, pt.payment_methode')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.id', $paket_transaction_id)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $array = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $array['no_register'] = $row->no_register;
            $array['metode_pembayaran'] = $row->payment_methode;
         }
      }
      return $array;
   }

   // get info pembayaran cicilan
   function getInfoPembayaranCicilan($paket_transaction_id)
   {
      $this->db->select('invoice, paid, ket, receiver, deposit_name, last_update')
         ->from('paket_transaction_installement_history')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $array = array();
      $total_bayar = 0;
      $total_bayar_not_dp = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $array[] = array(
               'invoice' => $row->invoice,
               'paid' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->paid),
               'penerima' => $row->receiver,
               'ket' => $row->ket,
               'penyetor' => $row->deposit_name,
               'tanggal_transaksi' => $row->last_update
            );
            $total_bayar = $total_bayar + $row->paid;
            if ($row->ket != 'dp') {
               $total_bayar_not_dp = $total_bayar_not_dp + $row->paid;
            }
         }
      }

      $this->db->select('pt.total_paket_price')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pt.id', $paket_transaction_id);
      $total_price = 0;
      $r = $this->db->get();
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $rows) {
            $total_price = $total_price + $rows->total_paket_price;
         }
      }

      $riwayat_angsuran = array();
      // riwayat Angsuran
      if ($total_bayar_not_dp > 0) {
         $this->db->select('term, amount, duedate')
            ->from('paket_installment_scheme')
            ->where('paket_transaction_id', $paket_transaction_id);
         $w = $this->db->get();
         $sisa = $total_bayar_not_dp;
         if ($w->num_rows() > 0) {
            foreach ($w->result() as $roww) {
               $bayar = $sisa;
               $sisa = $sisa - $roww->amount;
               if ($sisa <= 0) {
                  $riwayat_angsuran[] = array(
                     'term' => $roww->term,
                     'bayar' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($bayar),
                     'sisa' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format(abs($sisa)),
                     'ket' => 'Pembayaran angsuran ' . $roww->term
                  );
                  break;
               } else {
                  $riwayat_angsuran[] = array(
                     'term' => $roww->term,
                     'bayar' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($roww->amount),
                     'sisa' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . ' 0',
                     'ket' => 'Pembayaran angsuran ' . $roww->term
                  );
               }
            }
         }
      }

      $sisa = $total_price - $total_bayar;
      return array(
         'list' => $array,
         'total_harga' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($total_price),
         'total_bayar' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($total_bayar),
         'riwayat_angsuran' => $riwayat_angsuran,
         'sisa' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($sisa),
         'invoice' => $this->text_ops->get_invoice_transaksi_paket_cicilan()
      );
   }

   // check invoice cicilan
   function checkInvoiceCicilan($invoice)
   {
      $this->db->select('ptih.id')
         ->from('paket_transaction_installement_history AS ptih')
         ->join('paket_transaction AS pt', 'ptih.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('ptih.invoice', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return FALSE;
      } else {
         return TRUE;
      }
   }

   function getSkemaCicilan($paket_transaction_id)
   {
      $this->db->select('term, amount, duedate')
         ->from('paket_installment_scheme')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $list = array();
      $total_amount = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[$row->term] = array(
               'term' => $row->term,
               'amount' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->amount),
               'duedate' => $row->duedate
            );
            $total_amount = $total_amount + $row->amount;
         }
      }

      $this->db->select('pt.down_payment, pt.total_paket_price')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pt.id', $paket_transaction_id);
      $r = $this->db->get();
      $total_cicilan = 0;
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $ror) {
            $total_cicilan = $ror->total_paket_price - $ror->down_payment;
         }
      }
      return array(
         'listSkemaCicilan' => $list,
         'totalCicilan' => $total_cicilan,
         'totalAmount' => $total_amount
      );
   }

   function getTotalCicilan($paket_transaction_id)
   {
      $this->db->select('pt.total_paket_price, pt.down_payment')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.id', $paket_transaction_id)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $total_cicilan = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $total_cicilan = $row->total_paket_price - $row->down_payment;
         }
      }
      return $total_cicilan;
   }

   function getLastInfoKwitansiCicilan($paket_transaction_id)
   {
      $this->db->select('ptih.invoice, pt.no_register')
         ->from('paket_transaction_installement_history AS ptih')
         ->join('paket_transaction AS pt', 'ptih.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('ptih.paket_transaction_id', $paket_transaction_id)
         ->where('p.company_id', $this->company_id)
         ->order_by('ptih.input_date', 'asc');
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack['invoice'] = $row->invoice;
            $feedBack['no_register'] = $row->no_register;
         }
      }
      return $feedBack;
   }


   function get_tanda_tangan()
   {
      $list = array();
      $this->db->select('p.fullname, u.user_id, g.nama_group')
         ->from('base_users AS u')
         ->join('base_groups AS g', 'u.group_id=g.group_id', 'inner')
         ->join('personal AS p', 'u.personal_id=p.personal_id', 'inner')
         ->where('p.company_id', $this->company_id);
      if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] != 'administrator') {
         $this->db->where('u.user_id', $this->session->userdata($this->config->item('apps_name'))['user_id']);
      } else {
         $list[] = array(
            'id' => 0,
            'fullname' => 'Administrator ' . $this->session->userdata($this->config->item('apps_name'))['company_name'],
            'jabatan' => 'Administrator'
         );
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->user_id, 'fullname' => $row->fullname, 'jabatan' => $row->nama_group);
         }
      }
      return  $list;
   }

   function getTandaTanganName($user_id)
   {
      $this->db->select('p.fullname, g.nama_group')
         ->from('base_users AS u')
         ->join('base_groups AS g', 'u.group_id=g.group_id', 'inner')
         ->join('personal AS p', 'u.personal_id=p.personal_id', 'inner')
         ->where('u.user_id', $user_id)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $array = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $array['jabatan_petugas'] = $row->nama_group;
            $array['nama_petugas'] = $row->fullname;
         }
      }
      return $array;
   }

   function checkTandaTangan($user_id)
   {
      if ($user_id == 0) {
         return TRUE;
      } else {
         $this->db->select('u.user_id')
            ->from('base_users AS u')
            ->join('personal AS p', 'u.personal_id=p.personal_id', 'inner')
            ->where('u.user_id', $user_id)
            ->where('p.company_id', $this->company_id);
         $q = $this->db->get();
         if ($q->num_rows() > 0) {
            return TRUE;
         } else {
            return FALSE;
         }
      }
   }

   function get_total_manifes_paket($paket_id, $search)
   {
      $this->db->select('jamaah_id')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('paket_transaction pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('per.company_id', $this->company_id)
         ->where('pt.batal_berangkat', '0');

      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('per.fullname', $search)
            ->or_like('per.identity_number', $search)
            ->group_end();
      }

      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_manifes_paket($paket_id, $limit = 6, $start = 0, $search = '')
   {
      $this->db->select('ptj.jamaah_id, per.fullname, per.birth_date, per.birth_place,
                         j.passport_number, j.passport_dateissue, j.passport_place,
                         j.validity_period, per.identity_number, per.nomor_whatsapp')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('paket_transaction pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('per.company_id', $this->company_id)
         ->where('pt.batal_berangkat', '0');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('per.fullname', $search)
            ->or_like('per.identity_number', $search)
            ->group_end();
      }
      $this->db->order_by('pt.input_date', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list_uncomplete = array();
            if ($row->fullname == '') {
               $list_uncomplete[] = 'Nama Jamaah';
            }
            if ($row->birth_date == '') {
               $list_uncomplete[] = 'Tanggal Lahir';
            }
            if ($row->birth_place == '') {
               $list_uncomplete[] = 'Tempat Lahir';
            }
            if ($row->passport_number == '') {
               $list_uncomplete[] = 'Nomor Passport';
            }
            if ($row->passport_dateissue == '') {
               $list_uncomplete[] = 'Tanggal Dikeluarkan Passport';
            }
            if ($row->passport_place == '') {
               $list_uncomplete[] = 'Tempat Dikeluarkan Passport';
            }
            if ($row->validity_period == '') {
               $list_uncomplete[] = 'Masa Berlaku Passport';
            }
            $list_item_uncomplete = '';
            $status = 'COMPLETE';
            if (count($list_uncomplete) > 0) {
               $list_item_uncomplete .= '<ul class="pl-3 list">';
               foreach ($list_uncomplete as $key => $value) {
                  $list_item_uncomplete .= '<li>' . $value . '</li>';
               }
               $status = 'UNCOMPLETE';
               $list_item_uncomplete .= '</ul>';
            } else {
               $list_item_uncomplete .= 'ITEM LENGKAP';
            }

            $list[] = array(
               'id' => $row->jamaah_id,
               'paket_id' => $paket_id,
               'nomor_identitas' => $row->identity_number,
               'fullname' => $row->fullname,
               'nomor_whatsapp' => $row->nomor_whatsapp,
               'umur' => $this->date_ops->get_umur($row->birth_date),
               'tgl_lahir' => $this->date_ops->change_date($row->birth_date),
               'status' => $status,
               'item_uncomplate' => $list_item_uncomplete
            );
         }
      }
      return $list;
   }


   function get_total_syarat_paket($paket_id, $search = '')
   {
      $this->db->select('ptj.jamaah_id')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS pkt', 'pt.paket_id=pkt.id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('pkt.company_id', $this->company_id)
         ->where('pt.batal_berangkat', '0');

      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_syarat_paket($paket_id, $limit = 6, $start = 0, $search = '')
   {
      $this->db->select('p.fullname, p.identity_number, p.gender, j.passport_number,
                         j.photo_4_6, j.photo_3_4, j.fc_passport, j.fc_kk, j.fc_ktp,
                         j.buku_nikah, j.akte_lahir, j.buku_kuning')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('pt.company_id', $this->company_id)
         ->where('pt.batal_berangkat', '0');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $this->db->order_by('pt.input_date', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $syarat_prasyarat = array();
            $syarat_prasyarat['Nomor_Passport'] = $row->passport_number != '' ? true : false;
            $syarat_prasyarat['Photo_4_6'] = $row->photo_4_6 == 'ada' ? true : false;
            $syarat_prasyarat['Photo_3_4'] = $row->photo_3_4 == 'ada' ? true : false;
            $syarat_prasyarat['Foto_Copy_Passport'] = $row->fc_passport == 'ada' ? true : false;
            $syarat_prasyarat['Foto_Copy_KK'] = $row->fc_kk == 'ada' ? true : false;
            $syarat_prasyarat['Foto_Copy_KTP'] = $row->fc_ktp == 'ada' ? true : false;
            $syarat_prasyarat['Buku_Nikah'] = $row->buku_nikah == 'ada' ? true : false;
            $syarat_prasyarat['Akte_Lahir'] = $row->akte_lahir == 'ada' ? true : false;
            $syarat_prasyarat['Buku_Kuning'] = $row->buku_kuning == 'ada' ? true : false;

            $list[] = array(
               'fullname' => $row->fullname,
               'nomor_identitas' => $row->identity_number,
               'gender' => $row->gender == 0 ? 'Laki-laki' : 'Perempuan',
               'syarat_prasyarat' => $syarat_prasyarat
            );
         }
      }

      return $list;
   }

   function get_total_kamar_paket($paket_id, $search = '')
   {
      $this->db->select('r.id')
         ->from('rooms AS r')
         ->join('mst_hotel AS h', 'r.hotel_id=h.id', 'inner')
         ->join('mst_city AS c', 'h.city_id=c.id', 'inner')
         ->where('r.paket_id', $paket_id)
         ->where('r.company_id', $this->company_id);

      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('h.hotel_name', $search)
            ->or_like('r.room_type', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_kamar_paket($paket_id, $limit = 6, $start = 0, $search = '')
   {
      $this->db->select('r.id, h.hotel_name, r.room_type, c.city_name, r.room_capacity,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', per.fullname, per.identity_number ) SEPARATOR \';\')
                              FROM rooms_jamaah AS rj
                              INNER JOIN jamaah AS j ON rj.jamaah_id=j.id
                              INNER JOIN personal AS per ON j.personal_id=per.personal_id
                              WHERE rj.room_id=r.id) AS jamaahs ')
         ->from('rooms AS r')
         ->join('mst_hotel AS h', 'r.hotel_id=h.id', 'inner')
         ->join('mst_city AS c', 'h.city_id=c.id', 'inner')
         ->where('r.paket_id', $paket_id)
         ->where('r.company_id', $this->company_id);

      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('h.hotel_name', $search)
            ->or_like('r.room_type', $search)
            ->group_end();
      }
      $this->db->order_by('r.last_update', 'desc')->limit($limit, $start);
      $list = array();
      $r = $this->db->get();
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $row) {

            $jamaah = array();
            if ($row->jamaahs != '') {
               $exp = explode(';', $row->jamaahs);
               foreach ($exp as $exp_key => $exp_value) {
                  $exp_to = explode('$', $exp_value);
                  $jamaah[] = array('name' => $exp_to[0], 'identity_number' => $exp_to[1]);
               }
            }

            if( $row->room_type == 'laki_laki' ) {
               $room_type = 'Laki - laki';
            }else if( $row->room_type == 'perempuan' ) {
               $room_type = 'Perempuan';
            }else{
               $room_type = '-';
            }

            $list[] = array(
               'id' => $row->id,
               'paket_id' => $paket_id,
               'room_type' => $room_type,
               'hotel_name' => $row->hotel_name,
               'city_name' => $row->city_name,
               'room_capacity' => $row->room_capacity,
               'jamaah' => $jamaah
            );
         }
      }
      return $list;
   }

   function get_hotel($paket_id)
   {
      $this->db->select('h.id, h.hotel_name, c.city_name')
         ->from('mst_hotel AS h')
         ->join('mst_city AS c', 'h.city_id=c.id', 'inner')
         ->where('h.company_id', $this->company_id);
      $list = array();
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'nama_hotel' => $row->hotel_name . ' (Kota : ' . $row->city_name . ')'
            );
         }
      }
      return $list;
   }

   function get_jamaah_by_paket($paket_id)
   {
      $this->db->select('j.id, per.fullname, per.identity_number')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->where('ptj.company_id', $this->company_id)
         ->where('pt.paket_id', $paket_id)
         ->where('pt.batal_berangkat', '0');
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[$row->id] =  $row->fullname . ' (' . $row->identity_number . ')';
         }
      }
      return $list;
   }

   // check if exist nama hotel
   function check_nama_hotel($hotel)
   {
      $this->db->select('id')
         ->from('mst_hotel')
         ->where('id', $hotel)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function check_room_id($room_id)
   {
      $this->db->select('id')
         ->from('rooms')
         ->where('id', $room_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function get_data_kamar($room_id)
   {
      $this->db->select('r.id, mh.hotel_name, r.room_type, r.room_capacity')
         ->from('rooms AS r')
         ->join('mst_hotel AS mh', 'r.hotel_id=mh.id', 'inner')
         ->where('r.company_id', $this->company_id)
         ->where('r.id', $room_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list =  array(
               'id' => $row->id,
               'hotel_name' => $row->hotel_name,
               'room_type' => $row->room_type,
               'room_capacity' => $row->room_capacity
            );
         }
      }
      return $list;
   }

   function get_data_kamar_by_id($paket_id, $id)
   {
      $this->db->select('id, hotel_id, room_type, room_capacity,
                           (SELECT GROUP_CONCAT( jamaah_id SEPARATOR \';\')
                              FROM rooms_jamaah
                              WHERE room_id=r.id AND company_id="' . $this->company_id . '") AS jamaah_kamar')
         ->from('rooms AS r')
         ->where('id', $id)
         ->where('paket_id', $paket_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['hotel_id'] = $rows->hotel_id;
            $list['room_type'] = $rows->room_type;
            $list['room_capacity'] = $rows->room_capacity;
            $list['jamaah'] =  explode(';', $rows->jamaah_kamar);
         }
      }
      return $list;
   }

   function get_total_bus_paket($paket_id, $search = '')
   {
      $this->db->select('b.id')
         ->from('bus AS b')
         ->join('mst_city AS c', 'b.city_id=c.id', 'inner')
         ->where('b.paket_id', $paket_id)
         ->where('b.company_id', $this->company_id);

      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('b.bus_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_bus_paket($paket_id, $limit = 6, $start = 0, $search = '')
   {
      $this->db->select('b.id, b.bus_number, b.bus_capacity, b.bus_leader, c.city_name,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', per.fullname, per.identity_number ) SEPARATOR \';\')
                              FROM bus_jamaah AS bj
                              INNER JOIN jamaah AS j ON bj.jamaah_id=j.id
                              INNER JOIN personal AS per ON j.personal_id=per.personal_id
                              WHERE bj.bus_id=b.id AND bj.company_id="' . $this->company_id . '") AS jamaahs')
         ->from('bus AS b')
         ->join('mst_city AS c', 'b.city_id=c.id', 'inner')
         ->where('b.paket_id', $paket_id)
         ->where('b.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('b.bus_number', $search)
            ->group_end();
      }
      $this->db->order_by('b.last_update', 'desc')->limit($limit, $start);
      $list = array();
      $r = $this->db->get();
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $row) {

            $jamaah = array();
            if ($row->jamaahs != '') {
               $exp = explode(';', $row->jamaahs);
               foreach ($exp as $exp_key => $exp_value) {
                  $exp_to = explode('$', $exp_value);
                  $jamaah[] = array('name' => $exp_to[0], 'identity_number' => $exp_to[1]);
               }
            }

            $list_local = array();
            $list_local['id'] = $row->id;
            $list_local['paket_id'] = $paket_id;
            $list_local['bus_number'] = $row->bus_number;
            $list_local['bus_capacity'] = $row->bus_capacity;
            $list_local['bus_leader'] = $row->bus_leader;
            $list_local['city_name'] = $row->city_name;
            if (count($jamaah) > 0) {
               $list_local['jamaah'] = $jamaah;
            }

            $list[] = $list_local;
         }
      }
      return $list;
   }

   function get_city()
   {
      $this->db->select('id, city_name')
         ->from('mst_city')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[$rows->id] = $rows->city_name;
         }
      }
      return $list;
   }

   // check city id
   function check_city_id_exist($id)
   {
      $q = $this->db->select('id')
         ->from('mst_city')
         ->where('id', $id)
         ->where('company_id', $this->company_id)
         ->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function check_bus_id($bus_id)
   {
      $this->db->select('id')
         ->from('bus')
         ->where('id', $bus_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   // get data bus
   function get_data_bus($bus_id, $paket_id)
   {
      $this->db->select('id, bus_number, bus_leader')
         ->from('bus')
         ->where('id', $bus_id)
         ->where('paket_id', $paket_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list = array(
               'id' => $rows->id,
               'bus_number' => $rows->bus_number,
               'bus_leader' => $rows->bus_leader
            );
         }
      }
      return $list;
   }

   // get data bus by id
   function get_data_bus_by_id($paket_id, $bus_id)
   {
      $this->db->select('b.id, b.bus_number, b.bus_capacity, b.bus_leader, b.city_id,
                           (SELECT GROUP_CONCAT( jamaah_id SEPARATOR \';\')
                              FROM bus_jamaah
                              WHERE bus_id=b.id AND company_id="' . $this->company_id . '") AS jamaah_bus')
         ->from('bus AS b')
         ->where('b.id', $bus_id)
         ->where('b.paket_id', $paket_id)
         ->where('b.company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list = array(
               'id' => $rows->id,
               'bus_number' => $rows->bus_number,
               'bus_capacity' => $rows->bus_capacity,
               'bus_leader' => $rows->bus_leader,
               'city_id' => $rows->city_id,
               'jamaah' => $rows->jamaah_bus != null ? explode(';', $rows->jamaah_bus) : array()
            );
         }
      }
      return $list;
   }

   function get_jamaah_and_agen_id_by_paket_id($paket_id)
   {
      $this->db->select('ptj.jamaah_id, j.agen_id')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('j.agen_id !=', '0')
         ->where('ptj.company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[$rows->jamaah_id] = $rows->agen_id;
         }
      }
      return $list;
   }

   function get_total_agen_paket($paket_id, $search)
   {
      # list paket id
      // $list_paket_id = $this->get_jamaah_and_agen_id_by_paket_id($paket_id);
      $c = 0;
      $this->db->select('dfk.id, dfk.fee, dfk.sudah_bayar, per.fullname, per.identity_number, per.nomor_whatsapp,
                         per.address, lk.nama, p.fullname AS nama_jamaah, p.identity_number AS no_identitas_jamaah')
               ->from('detail_fee_keagenan AS dfk')
               ->join('level_keagenan AS lk', 'dfk.level_agen_id=lk.id', 'inner')
               ->join('fee_keagenan AS fk', 'dfk.fee_keagenan_id=fk.id', 'inner')
               ->join('personal AS p', 'fk.personal_id=p.personal_id', 'inner')
               ->join('agen AS a', 'dfk.agen_id=a.id', 'inner')
               ->join('personal AS per', 'a.personal_id=per.personal_id', 'inner')
               ->join('jamaah AS j', 'p.personal_id=j.personal_id', 'inner')
               ->join('paket_transaction AS pt', 'fk.id=pt.fee_keagenan_id', 'inner')
               ->where('dfk.company_id', $this->company_id)
               ->where('pt.paket_id', $paket_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('per.fullname', $search)
            ->or_like('per.identity_number', $search)
            ->group_end();
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $c++;
      }
      return $c;
   }

   # get index agen paket
   function get_index_agen_paket($paket_id, $limit = 6, $start = 0, $search = '')
   {
      $c = 0;
      $list = array();
      $this->db->select('dfk.id, dfk.fee, dfk.sudah_bayar, per.fullname, per.identity_number, per.nomor_whatsapp,
                         per.address, lk.nama, p.fullname AS nama_jamaah, p.identity_number AS no_identitas_jamaah')
         ->from('detail_fee_keagenan AS dfk')
         ->join('level_keagenan AS lk', 'dfk.level_agen_id=lk.id', 'inner')
         ->join('fee_keagenan AS fk', 'dfk.fee_keagenan_id=fk.id', 'inner')
         ->join('personal AS p', 'fk.personal_id=p.personal_id', 'inner')
         ->join('agen AS a', 'dfk.agen_id=a.id', 'inner')
         ->join('personal AS per', 'a.personal_id=per.personal_id', 'inner')
         ->join('jamaah AS j', 'p.personal_id=j.personal_id', 'inner')
         ->join('paket_transaction AS pt', 'fk.id=pt.fee_keagenan_id', 'inner')
         ->where('dfk.company_id', $this->company_id)
         ->where('pt.paket_id', $paket_id);
      if ( $search != '' or $search != null or !empty($search) ) {
         $this->db->group_start()
            ->like('per.fullname', $search)
            ->or_like('per.identity_number', $search)
            ->group_end();
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array(
               'id' => $rows->id,
               'fee' => $rows->fee,
               'sudah_bayar' => $rows->sudah_bayar,
               'nama_agen' => $rows->fullname,
               'no_identitas_agen' => $rows->identity_number,
               'wa_agen' => $rows->nomor_whatsapp,
               'alamat_agen' => $rows->address,
               'level_agen' => $rows->nama,
               'nama_jamaah' => $rows->nama_jamaah,
               'no_identitas_jamaah' => $rows->no_identitas_jamaah
            );
         }
      }



      // foreach ($list_paket_id as $key => $value) {

      // }
      return $list;
   }


   function countFeeAgen($paket_transaction_id)
   {
      $this->db->select('dfk.fee')
         ->from('detail_fee_keagenan AS dfk')
         ->join('fee_keagenan As fk', 'dfk.fee_keagenan_id=fk.id', 'inner')
         ->join('paket_transaction AS pt', 'fk.id=pt.fee_keagenan_id', 'inner')
         ->where('dfk.company_id', $this->company_id)
         ->where('pt.id', $paket_transaction_id);
      $q = $this->db->get();
      $total_fee = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $total_fee = $total_fee + $rows->fee;
         }
      }
      return $total_fee;
   }

   function get_data_k_t($paket_id)
   {
      $feedBack = array();
      # get total harga paket
      $this->db->select('pt.id, pt.total_paket_price,pt.total_mahram_fee, pt.paket_type_id, mpt.paket_type_name,
                         pt.payment_methode, pt.diskon,
                           (SELECT count(jamaah_id)
                              FROM paket_transaction_jamaah
                              WHERE paket_transaction_id=pt.id) AS jumlah_jamaah,
                           (SELECT price FROM paket_price
                              WHERE paket_id=pt.paket_id AND paket_type_id=pt.paket_type_id) AS paket_price,
                           IF(pt.payment_methode = \'0\',
                              (SELECT SUM(paid) FROM paket_transaction_history
                                 WHERE paket_transaction_id=pt.id AND ket="cash"),
                              (SELECT SUM(paid) FROM paket_transaction_installement_history
                                 WHERE paket_transaction_id=pt.id )) AS paided,
                              (SELECT SUM(paid) FROM paket_transaction_history
                                 WHERE paket_transaction_id=pt.id AND ket="refund") AS refund')
         ->from('paket_transaction AS pt')
         ->join('mst_paket_type AS mpt', 'pt.paket_type_id=mpt.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('pt.company_id', $this->company_id);
      $q = $this->db->get();
      $total_paket_price = 0;
      $total_sudah_dibayar = 0;
      $total_diskon = 0;
      $total_fee_agen = 0;
      $paket_type = array();

      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $total_paket_price = $total_paket_price + $row->total_paket_price;
            $total_diskon = $total_diskon + $row->diskon;
            if (isset($paket_type[$row->paket_type_id])) {
               $paket_type[$row->paket_type_id]['jumlah_jamaah'] = $paket_type[$row->paket_type_id]['jumlah_jamaah'] + $row->jumlah_jamaah;
               $paket_type[$row->paket_type_id]['total_harga_paket_type'] = $paket_type[$row->paket_type_id]['total_harga_paket_type'] + $row->total_paket_price;
               $paket_type[$row->paket_type_id]['total_mahram_fee'] = $paket_type[$row->paket_type_id]['total_mahram_fee'] + $row->total_mahram_fee;
               $paket_type[$row->paket_type_id]['total_diskon'] = $paket_type[$row->paket_type_id]['total_diskon'] + $row->diskon;
            } else {
               $paket_type[$row->paket_type_id] = array(
                  'paket_type_name' => $row->paket_type_name,
                  'jumlah_jamaah' => $row->jumlah_jamaah,
                  'total_harga_paket_type' => $row->total_paket_price,
                  'total_mahram_fee' => $row->total_mahram_fee,
                  'total_diskon' => $row->diskon,
                  'harga_paket' => $row->paket_price
               );
            }

            $total_fee_paket_transaction = $this->countFeeAgen($row->id);
            $total_fee_agen = $total_fee_agen + $total_fee_paket_transaction;
            $total_sudah_dibayar = $total_sudah_dibayar + ($row->payment_methode == 0 ? ($row->paided - $row->refund) : $row->paided);
         }

         $this->db->select('id, uraian, number,                              (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', id, uraian, unit, biaya ) SEPARATOR \';\')
                                 FROM aktualisasi_kegiatan_paket_detail
                                 WHERE aktualisasi_id=akp.id AND company_id="' . $this->company_id . '") AS aktualisasi_detail')
            ->from('aktualisasi_kegiatan_paket AS akp')
            ->where('akp.paket_id', $paket_id)
            ->where('akp.company_id', $this->company_id)
            ->order_by('number', 'asc');
         $q = $this->db->get();
         $aktualisasi = array();
         $total_aktualisasi = 0;
         if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
               $total_detail = 0;
               $list_aktualisasi = array();
               $list_aktualisasi['id'] = $rows->id;
               $list_aktualisasi['uraian'] = $rows->uraian;
               $list_aktualisasi['number'] = $rows->number;
               if ($rows->aktualisasi_detail != '') {
                  $detail_aktualisasi = array();
                  $exp = explode(';', $rows->aktualisasi_detail);
                  foreach ($exp as $keyExp => $valueExp) {
                     $exp_2 = explode('$', $valueExp);
                     $detail_aktualisasi[] = array(
                        'id' => $exp_2[0],
                        'uraian' => $exp_2[1],
                        'unit' => $exp_2[2],
                        'biaya' => $exp_2[3]
                     );
                     $total_aktualisasi = $total_aktualisasi + ($exp_2[3] * $exp_2[2]);
                     $total_detail = $total_detail + $exp_2[3] * $exp_2[2];
                  }
                  $list_aktualisasi['detail_aktualisasi'] = $detail_aktualisasi;
               }
               if ($total_detail > 0) {
                  $list_aktualisasi['total'] = $total_detail;
               } else {
                  $list_aktualisasi['total'] = '';
               }
               $aktualisasi[] = $list_aktualisasi;
            }
         }
         $feedBack['total_paket_price'] = $total_paket_price;
         $feedBack['total_harga_per_tipe_paket'] = $paket_type;
         $feedBack['total_sudah_dibayar'] = $total_sudah_dibayar;
         $feedBack['total_diskon'] = $total_diskon;
         $feedBack['total_piutang'] = $total_paket_price - $total_sudah_dibayar;
         $feedBack['total_fee_agen'] = $total_fee_agen;
         $feedBack['total_aktualisasi'] = $total_aktualisasi + $total_fee_agen;
         $feedBack['aktualisasi'] = $aktualisasi;
         $feedBack['keuntungan'] = $total_paket_price - ($total_aktualisasi + $total_fee_agen);
      }

      return $feedBack;
   }

   function get_jumlah_jamaah($paket_id){
      $this->db->select('id')
         ->from('paket_transaction')
         ->where('paket_id', $paket_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();

      return $q->num_rows();
   }

   function get_info_aktualisasi($paket_id, $aktualisasi_id = 0)
   {
      $this->db->select('number')
         ->from('aktualisasi_kegiatan_paket')
         ->where('paket_id', $paket_id)
         ->where('company_id', $this->company_id);
      if ($aktualisasi_id != 0) {
         $this->db->where('id !=', $aktualisasi_id);
      }
      $q = $this->db->get();
      $list_number = array('1');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list_number[] = $rows->number;
         }
      }
      return $list_number;
   }

   function check_nomor_aktualisasi($nomor, $aktualisasi_id = 0)
   {
      $this->db->select('id')
         ->from('aktualisasi_kegiatan_paket')
         ->where('number', $nomor)
         ->where('company_id', $this->company_id);
      if ($aktualisasi_id != 0) {
         $this->db->where('id !=', $aktualisasi_id);
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get aktualisasi anggaran info
   function get_aktualisasi_anggaran_info($aktualisasi_id)
   {
      $this->db->select('id, paket_id, uraian, number')
         ->from('aktualisasi_kegiatan_paket')
         ->where('id', $aktualisasi_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $data = array();
      $feedBack = array();
      $paket_id = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $data['id'] = $rows->id;
            $data['uraian'] = $rows->uraian;
            $data['number'] = $rows->number;
            $paket_id = $rows->paket_id;
         }
         $feedBack = array(
            'data' => $this->get_info_aktualisasi($paket_id, $aktualisasi_id),
            'value' => $data,
            'paket_id' => $paket_id
         );
      }
      return $feedBack;
   }

   // check aktualisasi id
   function check_aktualisasi_id($aktualisasi_id)
   {
      $this->db->select('id')
         ->from('aktualisasi_kegiatan_paket')
         ->where('id', $aktualisasi_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get paket id
   function get_paket_id_by_aktualisasi_id($aktualisasi_id)
   {
      $this->db->select('paket_id')
         ->from('aktualisasi_kegiatan_paket')
         ->where('id', $aktualisasi_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $paket_id = 0;
      if ($q->num_rows() > 0) {
         $paket_id = $q->row()->paket_id;
      }
      return $paket_id;
   }

   # Check aktualisasi detail id
   function check_aktualisasi_detail_id($aktualisasi_detail_id)
   {
      $this->db->select('id')
         ->from('aktualisasi_kegiatan_paket_detail')
         ->where('id', $aktualisasi_detail_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   # get aktualisasi anggaran detail
   function get_aktualisasi_anggaran_detail_info($aktualisasi_detail_id)
   {
      $this->db->select('akpd.id, akpd.uraian, akpd.unit, akpd.biaya, akp.paket_id, akpd.aktualisasi_id')
         ->from('aktualisasi_kegiatan_paket_detail AS akpd')
         ->join('aktualisasi_kegiatan_paket AS akp', 'akpd.aktualisasi_id=akp.id', 'inner')
         ->where('akpd.id', $aktualisasi_detail_id)
         ->where('akpd.company_id', $this->company_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         $value = array();
         $paket_id = 0;
         $aktualisasi_id = 0;
         foreach ($q->result() as $rows) {
            $value['id'] = $rows->id;
            $value['uraian'] = $rows->uraian;
            $value['unit'] = $rows->unit;
            $value['biaya'] = $rows->biaya;
            $paket_id = $rows->paket_id;
            $aktualisasi_id = $rows->aktualisasi_id;
         }
         $feedBack = array('value' => $value, 'paket_id' => $paket_id, 'aktualisasi_id' => $aktualisasi_id);
      }
      return $feedBack;
   }

   function get_paket_id_by_aktualisasi_detail_id($aktualisasi_detail_id)
   {
      $this->db->select('a.paket_id')
         ->from('aktualisasi_kegiatan_paket_detail AS ad')
         ->join('aktualisasi_kegiatan_paket AS a', 'ad.aktualisasi_id=a.id', 'inner')
         ->where('ad.id', $aktualisasi_detail_id);
      $q = $this->db->get();
      $paket_id = 0;
      if ($q->num_rows() > 0) {
         $paket_id = $q->row()->paket_id;
      }
      return $paket_id;
   }

   # get status paket
   function get_status_paket($paket_id)
   {
      $this->db->select('tutup_paket')
         ->from('paket')
         ->where('id', $paket_id);
      $q = $this->db->get();
      $status_paket = 'buka';
      if ($q->num_rows() > 0) {
         $status_paket = $q->row()->tutup_paket;
      }
      return $status_paket;
   }

   // check hutang jamaah
   function check_hutang_jamaah($paket_id)
   {
      $this->db->select('id, no_register, total_paket_price, payment_methode')
         ->from('paket_transaction')
         ->where('company_id', $this->company_id)
         ->where('paket_id', $paket_id)
         ->where('payment_methode', '0')
         ->where('batal_berangkat', '0');
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $sudahbayar = $this->getSudahBayar($row->id, $row->payment_methode);
            $sisa = $row->total_paket_price - $sudahbayar;
            if ($sisa > 0) {
               $feedBack[$row->no_register] = $sisa;
            }
         }
      }
      return $feedBack;
   }

   function get_akun_number($param)
   {
      $this->db->select('path, nomor_akun_secondary')
         ->from('akun_secondary')
         ->where('company_id', $this->company_id)
         ->where_in('path', $param);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack[$row->path] = $row->nomor_akun_secondary;
         }
      }
      return $feedBack;
   }

   # get paket name
   function get_simple_info_paket($paket_id)
   {
      $this->db->select('paket_name, kode')
         ->from('paket')
         ->where('company_id', $this->company_id)
         ->where('id', $paket_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $feedBack['paket_name'] = $row->paket_name;
         $feedBack['kode'] = $row->kode;
      }
      // get total package cost 
      $this->db->select('total_paket_price')
               ->from('paket_transaction')
               ->where('company_id', $this->company_id)
               ->where('paket_id', $paket_id);
      $r = $this->db->get();
      $total_harga_paket = 0;
      if( $r->num_rows() > 0 ){
         foreach ($r->result() as $rows) {
            $total_harga_paket = $total_harga_paket + $rows->total_paket_price;
         }
      }
      $feedBack['total_harga_paket'] = $total_harga_paket;         

      return $feedBack;
   }

   # get last periode
   function get_last_periode()
   {
      $this->db->select('id')
         ->from('jurnal_periode')
         ->where('company_id', $this->company_id)
         ->order_by('id', 'desc')
         ->limit('1');
      $q = $this->db->get();
      $id = '';
      if ($q->num_rows() > 0) {
         $id = $q->row()->id;
      }
      return $id;
   }

   function get_total_all_transaksi_paket($search)
   {
      $this->db->select('pt.id')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('IF(pt.payment_methode = \'0\', p.tutup_paket=\'buka\' AND pt.company_id = \'' . $this->company_id . '\', pt.company_id = \'' . $this->company_id . '\') ')
         ->where('p.departure_date >', date('Y-m-d'))
         ->where('pt.batal_berangkat', '0');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.paket_name', $search)
            ->or_like('pt.no_register', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }


   function get_index_all_transaksi_paket($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('pt.id, pt.no_register, p.paket_name, p.id AS paket_id, ptype.paket_type_name, pt.total_paket_price, pt.total_mahram_fee,
                         pt.no_visa, pt.tgl_berlaku_visa, pt.tgl_akhir_visa, pt.payment_methode, p.departure_date, pt.diskon, p.tutup_paket,
                         (SELECT price FROM paket_price WHERE paket_id=pt.paket_id AND paket_type_id=pt.paket_type_id) AS harga')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->join('mst_paket_type AS ptype', 'pt.paket_type_id=ptype.id', 'inner')
         ->where('IF(pt.payment_methode = \'0\', p.tutup_paket=\'buka\' AND pt.company_id = \'' . $this->company_id . '\', pt.company_id = \'' . $this->company_id . '\') ')
         ->where('p.departure_date >', date('Y-m-d'))
         ->where('pt.company_id', $this->company_id)
         ->where('pt.batal_berangkat', '0');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.paket_name', $search)
            ->or_like('pt.no_register', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $sudahbayar = $this->getSudahBayar($row->id, $row->payment_methode);
            $sisa = $row->total_paket_price - $sudahbayar;
            $list[] = array(
               'id' => $row->id,
               'nomor_register' => $row->no_register,
               'paket_id' => $row->paket_id,
               'status_paket' => $row->tutup_paket,
               'paket_name' => $row->paket_name,
               'no_visa' => ($row->no_visa != '' ? $row->no_visa : '-'), 
               'tgl_berlaku_visa' => ($row->tgl_berlaku_visa != '0000-00-00' ? $row->tgl_berlaku_visa : '-'), 
               'tgl_akhir_visa' => ($row->tgl_akhir_visa != '0000-00-00' ? $row->tgl_akhir_visa : '-'),
               'paket_type_name' => $row->paket_type_name,
               'total_paket_price' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->total_paket_price),
               'harga' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->harga),
               'metode_pembayaran' => $row->payment_methode == 0 ? 'Cash' : 'Cicilan',
               'sudah_dibayar' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($sudahbayar),
               'sisa' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($sisa),
               'fee_mahram' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->total_mahram_fee),
               'departure_date' => $this->date_ops->change_date_t4($row->departure_date),
               'diskon' => $this->session->userdata($this->config->item('apps_name'))['kurs'] . number_format($row->diskon),
               'jamaah' => $this->getJamaahInPaket($row->id)
            );
         }
      }
      return $list;
   }

   function get_info_keagenan($paket_id, $agen_id)
   {
      $feedBack = array();
      # get fee agen and cabang
      $this->db->select('fee_agen, fee_cabang')
         ->from('paket')
         ->where('company_id', $this->company_id)
         ->where('id', $paket_id);
      $q = $this->db->get();
      $fee_agen = 0;
      $fee_cabang = 0;
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $fee_agen = $row->fee_agen;
         $fee_cabang = $row->fee_cabang;
      }
      # get upline
      $this->db->select('ag.level_agen, ag.upline,
                           (SELECT a.level_agen
                              FROM agen AS a
                              WHERE a.company_id="' . $this->company_id . '"
                              AND a.id=ag.upline) AS level_upline')
         ->from('agen AS ag')
         ->where('ag.company_id', $this->company_id)
         ->where('ag.id', $agen_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $feedBack[] = array(
            'company_id' => $this->company_id,
            'agen_id' => $agen_id,
            'level_agen' => $row->level_agen,
            'fee' => $row->level_agen == 'agen' ? $fee_agen : ($row->level_agen == 'cabang' ? $fee_cabang : 0),
            'input_date' => date('Y-m-d'),
            'last_update' => date('Y-m-d')
         );
         # only cabang can get fee free
         if ($row->upline != 0 and $row->level_agen == 'agen' and $row->level_upline == 'cabang') {
            $feedBack[] = array(
               'company_id' => $this->company_id,
               'agen_id' => $row->upline,
               'level_agen' => $row->level_upline,
               'fee' => $fee_cabang,
               'input_date' => date('Y-m-d'),
               'last_update' => date('Y-m-d')
            );
         }
      }
      return $feedBack;
   }

   function get_total_all_transaksi_paket_agen($search)
   {
      $this->db->select('id')
         ->from('agen AS a')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('a.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_all_transaksi_paket_agen($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('a.id, p.fullname, p.identity_number, p.gender, p.nomor_whatsapp, p.address, p.email, lv.nama,
                        (SELECT SUM(fee)
                           FROM detail_fee_keagenan
                           WHERE agen_id=a.id AND company_id="' . $this->company_id . '") AS fee_keagenan,
                        (SELECT SUM(biaya)
                           FROM fee_keagenan_payment
                           WHERE agen_id=a.id AND company_id="' . $this->company_id . '") total_pembayaran,
                        (SELECT COUNT(id)
                           FROM detail_fee_keagenan
                           WHERE agen_id=a.id AND company_id="' . $this->company_id . '") AS total_transaksi')
         ->from('agen AS a')
         ->join('level_keagenan AS lv', 'a.level_agen_id=lv.id', 'inner')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('a.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'fullname' => $row->fullname,
               'identity_number' => $row->identity_number,
               'gender' => $row->gender,
               'nomor_whatsapp' => $row->nomor_whatsapp,
               'level_agen' => $row->nama,
               'address' => $row->address,
               'email' => $row->email,
               'paid_fee' => $row->total_pembayaran == '' ? 0 : $row->total_pembayaran,
               'unpaid_fee' => ($row->fee_keagenan - $row->total_pembayaran) == '' ? 0 : ($row->fee_keagenan - $row->total_pembayaran),
               'total_transaksi' => $row->total_transaksi
            );
         }
      }
      return $list;
   }

   # check id keagenan
   function check_id_keagenan($id)
   {
      $this->db->select('id')
         ->from('agen')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get info fee keagenan
   function get_info_fee_keagenan($id)
   {
      $this->db->select('a.id, p.fullname, p.identity_number, p.gender, p.nomor_whatsapp, p.address, p.email, a.level_agen,
                        (SELECT SUM(fee)
                           FROM fee_keagenan
                           WHERE agen_id=a.id AND company_id="' . $this->company_id . '") AS fee_keagenan,
                        (SELECT SUM(biaya)
                           FROM fee_keagenan_payment
                           WHERE agen_id=a.id AND company_id="' . $this->company_id . '") total_pembayaran,
                        (SELECT COUNT(id)
                           FROM fee_keagenan
                           WHERE agen_id=a.id AND company_id="' . $this->company_id . '") AS total_transaksi')
         ->from('agen AS a')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('a.company_id', $this->company_id)
         ->where('a.id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['invoice'] = $this->random_code_ops->rand_payment_invoice();
            $list['id'] = $rows->id;
            $list['fullname'] = $rows->fullname;
            $list['identity_number'] = $rows->identity_number;
            $list['unpaid'] = ($rows->fee_keagenan - $rows->total_pembayaran)  == '' ? 0 : ($rows->fee_keagenan - $rows->total_pembayaran);
         }
      }
      return $list;
   }

   #
   function check_invoice($invoice)
   {
      $this->db->select('id')
         ->from('fee_keagenan_payment')
         ->where('invoice', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get info agen
   function get_info_agen($paket_id, $id)
   {
      # harga fee agen
      $this->db->select('fee_agen, fee_cabang')
         ->from('paket')
         ->where('id', $paket_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $list['agen'] = $row->fee_agen;
         $list['cabang'] = $row->fee_cabang;
      }
      # get info agen
      $this->db->select('a.id, p.fullname, a.level_agen, a.upline')
         ->from('agen AS a')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('a.company_id', $this->company_id)
         ->where('a.id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $feedBack[$row->level_agen] = array('id' => $row->id, 'fullname' => $row->fullname, 'level_agen' => ucfirst($row->level_agen), 'fee' => $list[$row->level_agen]);
         if ($row->level_agen == 'agen' and $row->upline != 0) {
            # get info cabang
            $this->db->select('a.id, p.fullname, a.level_agen')
               ->from('agen AS a')
               ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
               ->where('a.company_id', $this->company_id)
               ->where('a.level_agen', 'cabang')
               ->where('a.id', $row->upline);
            $r = $this->db->get();
            if ($r->num_rows() > 0) {
               $rows = $r->row();
               $feedBack['cabang'] = array('id' => $row->upline, 'fullname' => $rows->fullname, 'level_agen' => ucfirst($rows->level_agen), 'fee' => $list['cabang']);
            }
         }
      }
      return $feedBack;
   }

   # get upline agen
   function get_upline_agen($agen_id)
   {
      $this->db->select('ag.id, ag.level_agen')
         ->from('agen AS a')
         ->join('agen AS ag', 'a.upline=ag.id', 'inner')
         ->where('a.id', $agen_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $feedBack['id'] = $row->id;
         $feedBack['level_agen'] = $row->level_agen;
      }
      return $feedBack;
   }

   # get info agen
   function get_list_agen()
   {
      $this->db->select('a.id, p.fullname, p.identity_number')
         ->from('agen AS a')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('a.company_id', $this->company_id);
      $q = $this->db->get();
      $list = array('Pilih Agen');
      if ($q->num_rows() >  0) {
         foreach ($q->result() as $rows) {
            $list[$rows->id] = $rows->fullname . ' (' . $rows->identity_number . ')';
         }
      }
      return $list;
   }

   function get_agen_selected($pool_id)
   {
      $this->db->select('per.fullname')
         ->from('pool AS p')
         ->join('jamaah AS j', 'p.jamaah_id=j.id', 'inner')
         ->join('agen AS a', 'j.agen_id=a.id', 'inner')
         ->join('personal AS per', 'a.personal_id=per.personal_id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('p.id', $pool_id);
      $q = $this->db->get();
      $agen_selected = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $agen_selected = $rows->fullname;
         }
      }
      return $agen_selected;
   }

   function check_agen_is_exist($agen_id)
   {
      $this->db->select('id')
         ->from('agen')
         ->where('company_id', $this->company_id)
         ->where('id', $agen_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get fee keagenan
   function get_fee_agen($pool_id)
   {
      $this->db->select('dfk.fee, lk.nama, p.fullname')
         ->from('detail_fee_keagenan AS dfk')
         ->join('fee_keagenan AS fk', 'dfk.fee_keagenan_id=fk.id')
         ->join('pool AS po', 'fk.id=po.fee_keagenan_id', 'inner')
         ->join('level_keagenan AS lk', 'dfk.level_agen_id=lk.id', 'inner')
         ->join('agen AS a', 'dfk.agen_id=a.id', 'inner')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('dfk.company_id', $this->company_id)
         ->where('po.id', $pool_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array('fee' => $rows->fee, 'nama' => $rows->nama, 'fullname' => $rows->fullname);
         }
      }
      return $list;
   }

   # check penyetor exist
   function check_penyetor_exist($jamaah_id)
   {
      $this->db->select('id')
         ->from('pool')
         ->where('company_id', $this->company_id)
         ->where('jamaah_id', $jamaah_id)
         ->where('active', 'active');
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function get_pool_id_by_jamaah_id($jamaah_id)
   {
      $this->db->select('id')
         ->from('pool')
         ->where('company_id', $this->company_id)
         ->where('jamaah_id', $jamaah_id);
      $q = $this->db->get();
      $pool_id = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $pool_id = $rows->id;
         }
      }
      return $pool_id;
   }

   function get_agen_info($jamaah_id)
   {
      $this->db->select('per.fullname, j.agen_id')
         ->from('jamaah AS j')
         ->join('agen AS a', 'j.agen_id=a.id', 'inner')
         ->join('personal AS per', 'a.personal_id=per.personal_id', 'inner')
         ->where('j.company_id', $this->company_id)
         ->where('j.id', $jamaah_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['nama_agen'] = $rows->fullname;
            $list['agen_id'] = $rows->agen_id;
         }
      }
      return $list;
   }

   function check_jamaah_is_in_paket($paket_id, $jamaah_id)
   {
      $this->db->select('pj.jamaah_id')
         ->from('paket_transaction_jamaah AS pj')
         ->join('paket_transaction AS p', 'pj.paket_transaction_id=p.id', 'inner')
         ->where('pj.company_id', $this->company_id)
         ->where('p.paket_id', $paket_id)
         ->where('pj.jamaah_id', $jamaah_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function get_info_pool($jamaah_id)
   {
      $this->db->select('id, fee_keagenan_id')
         ->from('pool')
         ->where('company_id', $this->company_id)
         ->where('jamaah_id', $jamaah_id)
         ->where('active', 'active');
      $q = $this->db->get();
      $pool_id = 0;
      $fee_keagenan_id = 0;
      $handover_facilities = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $pool_id = $rows->id;
            $fee_keagenan_id = $rows->fee_keagenan_id;

            $this->db->select('invoice, facilities_id, officer, receiver_name, receiver_identity, date_transaction')
               ->from('pool_handover_facilities')
               ->where('company_id', $this->company_id)
               ->where('pool_id', $rows->id);
            $r = $this->db->get();
            if ($r->num_rows() > 0) {
               foreach ($r->result() as $rowr) {
                  $handover_facilities[] = array(
                     'invoice' => $rowr->invoice,
                     'facilities_id' => $rowr->facilities_id,
                     'officer' => $rowr->officer,
                     'receiver_name' => $rowr->receiver_name,
                     'receiver_identity' => $rowr->receiver_identity,
                     'date_transaction' => $rowr->date_transaction
                  );
               }
            }
         }
      }
      return array('pool_id' => $pool_id, 'fee_keagenan_id' => $fee_keagenan_id, 'handover_facilities' => $handover_facilities);
   }

   function info_pembayaran_deposit_jamaah($jamaah_id)
   {
      $this->db->select('dt.debet, dt.kredit')
         ->from('pool_deposit_transaction AS pdt')
         ->join('deposit_transaction AS dt', 'pdt.deposit_transaction_id=dt.id', 'inner')
         ->join('pool AS p', 'pdt.pool_id=p.id', 'inner')
         ->where('pdt.company_id', $this->company_id)
         ->where('p.active', 'active')
         ->where('p.jamaah_id', $jamaah_id);
      $q = $this->db->get();
      // $total_deposit = 0;
      $debet = 0;
      $kredit = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $debet = $debet + $rows->debet;
            $kredit = $kredit + $rows->kredit;
         }
      }
      return ($debet - $kredit);
   }

   # check jumlah deposit
   function check_deposit_jamaah($paket_id, $paket_type_id, $jamaah_id)
   {
      # harga paket
      $harga_paket = $this->getPaketPricePerType($paket_id, $paket_type_id);
      # total deposit
      $total_deposit = $this->info_pembayaran_deposit_jamaah($jamaah_id);
      # filter
      if ($total_deposit >= $harga_paket) {
         return true;
      } else {
         return false;
      }
   }


   function get_personal_id_by_jamaah_id($jamaah_id)
   {
      $this->db->select('j.personal_id')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('j.id', $jamaah_id)
         ->where('j.company_id', $this->company_id);
      $q = $this->db->get();
      $personal_id = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $personal_id = $rows->personal_id;
         }
      }
      return $personal_id;
   }

   // check pendidikan
   function check_pendidikan($pendidikan_id){
      $this->db->select('id_pendidikan')
         ->from('mst_pendidikan')
         ->where('id_pendidikan', $pendidikan_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      } else{
         return false;
      }
   }

   // check pekerjaan
   function check_pekerjaan($pekerjaan_id){
      $this->db->select('id')
         ->from('mst_pekerjaan')
         ->where('id', $pekerjaan_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      } else{
         return false;
      }
   }

   // personal id
   function get_info_paket_transaction_by_paket_transaction_id($paket_transaction_id){
      $this->db->select('p.personal_id, pt.fee_keagenan_id, pt.no_register')
         ->from('paket_transaction AS pt')
         ->join('paket_transaction_jamaah AS ptj', 'pt.id=ptj.paket_transaction_id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('pt.id', $paket_transaction_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         $row = $q->row();   
         $list['personal_id'] = $row->personal_id;
         $list['fee_keagenan_id'] = $row->fee_keagenan_id;
         $list['no_register'] = $row->no_register;
      }
      return $list;   
   }

   // get member not jamaah
   function get_member_not_jamaah(){
      // get personal is jamaah
      $this->db->select('personal_id')
         ->from('jamaah')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $is_jamaah = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $is_jamaah[] = $rows->personal_id;
         }
      }
      // get member 
      $this->db->select('personal_id, fullname, identity_number')
         ->from('personal')
         ->where('company_id', $this->company_id);
      if( count($is_jamaah) > 0 ){
         $this->db->where_not_in('personal_id', $is_jamaah);
      }   
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list[$rows->personal_id] = $rows->fullname.' -> ('.$rows->identity_number.')';
         }
      }   
      return $list;
   }

   // checking member id exist
   function check_member_id_exist($id){
      $this->db->select('personal_id')
         ->from('personal')
         ->where('company_id', $this->company_id)
         ->where('personal_id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   // get member info
   function get_member_info($member_id) {
      $this->db->select('personal_id, fullname, identity_number, gender, photo, birth_date, birth_place, address, email, nomor_whatsapp')
         ->from('personal')
         ->where('company_id', $this->company_id)
         ->where('personal_id', $member_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list['personal_id'] = $rows->personal_id;
            $list['fullname'] = $rows->fullname;
            $list['identity_number'] = $rows->identity_number;
            $list['gender'] = $rows->gender;

            $photo = 'default.png';
            if ($rows->photo != '') {
               $src = FCPATH . 'image/personal/' . $rows->photo;
               if (file_exists($src)) {
                  $photo = $rows->photo;
               }
            }

            $list['photo'] = $photo;
            $list['birth_date'] = $this->date_ops->change_date($rows->birth_date);
            $list['birth_place'] = $rows->birth_place;
            $list['address'] = $rows->address;
            $list['email'] = $rows->email;
            $list['nomor_whatsapp'] = $rows->nomor_whatsapp;
         }
      }
      return $list; 
   }

}
