<?php

/**
 *  -----------------------
 *	Model akun
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_akun extends CI_Model
{
   private $company_id;
   private $status;
   private $content;
   private $error;
   private $write_log;
   private $kurs;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      $this->kurs = $this->session->userdata($this->config->item('apps_name'))['kurs'];
      $this->error = 0;
      $this->write_log = 1;
   }

   # get total daftar akun
   function get_total_daftar_akun($filter)
   {
      $this->db->select('ap.id,
                        (SELECT GROUP_CONCAT( id SEPARATOR \';\' ) FROM akun_secondary
                        WHERE company_id="' . $this->company_id . '" AND akun_primary_id=ap.id) AS akun_secondary')
         ->from('akun_primary AS ap');
      if ($filter != '' or $filter != null or $filter != 0 or !empty($filter)) {
         $this->db->group_start()
            ->like('ap.nomor_akun', $filter)
            ->group_end();
      }
      $q = $this->db->get();
      $num = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $num++;
            foreach (explode(';', $rows->akun_secondary) as $key => $value) {
               $num++;
            }
         }
      }
      return $num;
   }

   // check akun company
   function _check_akun_company(){
      // default_akun_secondary
      $not_in_akun_secondary = array();
      $this->db->select('akun_primary_id, nomor_akun_secondary, nama_akun_secondary, tipe_akun, path')
         ->from('default_akun_secondary');
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            // akun secondary
            $this->db->select('id')
               ->from('akun_secondary')
               ->where('company_id', $this->company_id)
               ->where('akun_primary_id', $rows->akun_primary_id)
               ->where('nomor_akun_secondary', $rows->nomor_akun_secondary);
            $r = $this->db->get();
            if( $r->num_rows() == 0 ) {
               $not_in_akun_secondary[] = array('company_id' => $this->company_id, 
                                                'akun_primary_id' => $rows->akun_primary_id, 
                                                'nomor_akun_secondary' => $rows->nomor_akun_secondary,
                                                'nama_akun_secondary' => $rows->nama_akun_secondary, 
                                                'tipe_akun' => $rows->tipe_akun, 
                                                'path' => $rows->path);
            }
         }
      }
      // filter
      if( count( $not_in_akun_secondary ) > 0 ) {
         // default_akun_primary
         foreach ( $not_in_akun_secondary as $key => $value) {
            # Starting Transaction
            $this->db->trans_start();
            # update data akun
            $this->db->insert('akun_secondary', $value);
            # Transaction Complete
            $this->db->trans_complete();
            # Filter Status
            if ($this->db->trans_status() === FALSE) {
               # Something Went Wrong.
               $this->db->trans_rollback();
            } else {
               # Transaction Commit
               $this->db->trans_commit();
            }
         }
      }
   }

   function hit_saldo_sekarang($sn, $nomor_akun){
      // debet
      $this->db->select('saldo')
         ->from('jurnal')
         ->where('akun_debet', $nomor_akun)
         ->where('company_id', $this->company_id)
         ->where('periode_id', 0);
      $q = $this->db->get();
      $debet = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $debet = $debet +  $rows->saldo;
         }
      }
      // kredit
      $this->db->select('saldo')
         ->from('jurnal')
         ->where('akun_kredit', $nomor_akun)
         ->where('company_id', $this->company_id)
         ->where('periode_id', 0);
      $q = $this->db->get();
      $kredit = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $kredit = $kredit +  $rows->saldo;
         }
      }
      // feed
      $feed = 0;
      if( $sn == 'D' ){
         $feed = $debet - $kredit;
      }elseif( $sn == 'K' ){
         $feed = $kredit - $debet;
      }

      return $feed;
   }

   # get index daftar akun
   function get_index_daftar_akun($limit = 6, $start = 0, $filter = '')
   {
      // check akun default 
      $this->_check_akun_company();      

      $this->db->select('ap.id, ap.nomor_akun, ap.nama_akun, ap.sn,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', id, nomor_akun_secondary, nama_akun_secondary, tipe_akun, path ) SEPARATOR \';\' )
                           FROM akun_secondary
                           WHERE company_id="' . $this->company_id . '" AND akun_primary_id=ap.id) AS akun_secondary,
                           (SELECT id FROM jurnal_periode WHERE company_id="' . $this->company_id . '" ORDER BY id DESC LIMIT 1 ) AS periode_aktif')
         ->from('akun_primary AS ap');
      if ($filter != '' or $filter != null or $filter != 0 or !empty($filter)) {
         $this->db->group_start()
            ->like('ap.nomor_akun', $filter)
            ->group_end();
      }
      $this->db->order_by('ap.nomor_akun', 'asc')->limit(200, 0);
      $q = $this->db->get();
      $list = array();
      $list_saldo = array();
      if ($q->num_rows() > 0) {
         $n = 0;
         foreach ($q->result() as $row) {
            if (count($list_saldo) == 0) {
                $list_saldo = $this->list_saldo($row->periode_aktif);
            }
            $list[$n] = array(
               'id' => $row->id,
               'nomor_akun' => $row->nomor_akun,
               'nama_akun' => $row->nama_akun,
               'tipe' => '',
               'link' => '',
               'level' => 'primary'
            );
            $n_header = $n;
            $total = 0;
            $akun_secondary = array();
            foreach (explode(";", $row->akun_secondary) as $key => $value) {
               if ($value != '') {
                  $exp = explode("$", $value);
                  if (count($exp) > 0) {
                     $total = $total +  (isset($list_saldo[$exp[0]]) ? $list_saldo[$exp[0]] : 0);

                     $saldo_akhir = $this->hit_saldo_sekarang($row->sn, $exp[1]) + (isset($list_saldo[$exp[0]]) ? $list_saldo[$exp[0]] : 0);
                     $akun_secondary[$exp[1]] = array(
                        'id' => $exp[0],
                        'nomor_akun' => $exp[1],
                        'nama_akun' => $exp[2],
                        'tipe' => $exp[3],
                        'level' => 'secondary',
                        'saldo_awal' => (isset($list_saldo[$exp[0]]) ?  $this->kurs . number_format($list_saldo[$exp[0]]) : $this->kurs . ' 0'),
                        'saldo_exist' => isset($list_saldo[$exp[0]]),
                        'saldo_akhir' => $saldo_akhir
                     );
                  }
               }
            }
            # sorting
            ksort($akun_secondary);
            # list loop
            foreach ($akun_secondary as $key => $value) {
               $n++;
               $list[$n] = $value;
            }
            $n++;
            $list[$n_header]['saldo_awal'] = $this->kurs. ' ' . number_format($total);
         }
      }

      return $list;
   }

   # get list
   function list_saldo($periode_id = 0)
   {
      // if( $periode_id == '' ) {
      //    $periode_id = 0;
      // }
      $this->db->select('saldo, akun_secondary_id')
         ->from('saldo')
         ->where('company_id', $this->company_id)
         ->where('periode', 0);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[$rows->akun_secondary_id] = $rows->saldo;
         }
      }
      return $list;
   }

   function check_akun_exist($new_akun, $id = '')
   {
      $this->db->select('id')
         ->from('akun_secondary')
         ->where('nomor_akun_secondary', $new_akun)
         ->where('company_id', $this->company_id);
      if ($id != '') {
         $this->db->where('id !=', $id);
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # check akun id exist
   function check_akun_id_exist($id)
   {
      $this->db->select('id')
         ->from('akun_secondary')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # check new akun
   function check_new_akun($nomor_akun, $id = '')
   {
      $this->db->select('id')
         ->from('akun_secondary')
         ->where('company_id', $this->company_id)
         ->where('nomor_akun_secondary', $nomor_akun);
      if ($id != '') {
         $this->db->where('id !=', $id);
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   // function get_info_akun($head_akun){
   //    $this->db->select('id, sn, pos')
   //       ->from('akun_primary')
   //       ->where('id', $head_akun);
   //
   // }

   function last_periode()
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

   # get nomor akun
   function get_nomor_akun($id)
   {
      $this->db->select('nomor_akun_secondary')
         ->from('akun_secondary')
         ->where('id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $nomor_akun = '';
      if ($q->num_rows() > 0) {
         $nomor_akun = $q->row()->nomor_akun_secondary;
      }
      return $nomor_akun;
   }

   function get_info_edit_akun($id)
   {
      $periode_id = $this->last_periode();
      $this->db->select('ap.nomor_akun, as.nomor_akun_secondary, as.nama_akun_secondary')
         ->from('akun_secondary AS as')
         ->join('akun_primary AS ap', 'as.akun_primary_id=ap.id', 'inner')
         // ->join('saldo AS s', 'as.id=s.akun_secondary_id', 'inner')
         ->where('as.company_id', $this->company_id)
         ->where('as.id', $id);
      // ->where('s.periode', $periode_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $saldo = 0;
         #  GET SALDO
         $this->db->select('saldo')
            ->from('saldo')
            ->where('akun_secondary_id', $id)
            ->where('periode', $periode_id);
         $r = $this->db->get();
         if ($r->num_rows() > 0) {
            $saldo = $r->row()->saldo;
         }
         $list = array(
            'nomor_akun_primary' => $row->nomor_akun,
            'value' => array(
               'id' => $id,
               'nomor_akun' => substr($row->nomor_akun_secondary, 1),
               'nama_akun' => $row->nama_akun_secondary,
               'saldo' => $saldo
            )
         );
      }
      return $list;
   }

   # check accepted akun
   function check_accepted_akun($id)
   {
      $this->db->select('id')
         ->from('akun_secondary')
         ->where('id', $id)
         ->where('tipe_akun', 'tambahan');
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get info saldo akun
   function get_info_saldo_akun($id)
   {
      $this->db->select('nomor_akun_secondary, nama_akun_secondary')
         ->from('akun_secondary')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $feedBack['nomor_akun_secondary'] = $row->nomor_akun_secondary;
         $feedBack['nama_akun_secondary'] = $row->nama_akun_secondary;
         # get saldo
         $this->db->select('saldo')
            ->from('saldo')
            ->where('company_id', $this->company_id)
            ->where('akun_secondary_id', $id);
         $r = $this->db->get();
         if ($r->num_rows() > 0) {
            $feedBack['saldo'] = $this->kurs . ' ' . number_format($r->row()->saldo);
         } else {
            $feedBack['saldo'] = $this->kurs . ' 0';
         }
      }
      return $feedBack;
   }

   # get info sn pos akun primary
   function get_info_sn_pos_akun_primary()
   {
      $this->db->select('id, sn, pos')
         ->from('akun_primary');
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[$rows->id] = array('sn' => $rows->sn, 'pos' => $rows->pos);
         }
      }
      return $list;
   }

   # get saldo jurnal
   function get_saldo_jurnal($periode_id, $info_sn_pos)
   {
      $this->db->select('saldo, akun_debet, akun_kredit')
         ->from('jurnal')
         ->where('company_id', $this->company_id)
         ->where('periode_id', $periode_id);
      $q = $this->db->get();
      $saldo_akun = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $header_akun_debet = substr($rows->akun_debet, 0, 1);
            $header_akun_kredit = substr($rows->akun_kredit, 0, 1);
            # akun debet
            if (isset($info_sn_pos[$header_akun_debet])) {
               if ($info_sn_pos[$header_akun_debet]['sn'] == 'D') {
                  if (isset($saldo_akun[$rows->akun_debet])) {
                     $saldo_akun[$rows->akun_debet] = $saldo_akun[$rows->akun_debet] + $rows->saldo;
                  } else {
                     $saldo_akun[$rows->akun_debet] = $rows->saldo;
                  }
               } elseif ($info_sn_pos[$header_akun_debet]['sn'] == 'K') {
                  if (isset($saldo_akun[$rows->akun_debet])) {
                     $saldo_akun[$rows->akun_debet] = $saldo_akun[$rows->akun_debet] - $rows->saldo;
                  } else {
                     $saldo_akun[$rows->akun_debet] = 0 - $rows->saldo;
                  }
               }
            }
            # akun kredit
            if (isset($info_sn_pos[$header_akun_kredit])) {
               if ($info_sn_pos[$header_akun_kredit]['sn'] == 'K') {
                  if (isset($saldo_akun[$rows->akun_kredit])) {
                     $saldo_akun[$rows->akun_kredit] = $saldo_akun[$rows->akun_kredit] + $rows->saldo;
                  } else {
                     $saldo_akun[$rows->akun_kredit] = $rows->saldo;
                  }
               } elseif ($info_sn_pos[$header_akun_kredit]['sn'] == 'D') {
                  if (isset($saldo_akun[$rows->akun_kredit])) {
                     $saldo_akun[$rows->akun_kredit] = $saldo_akun[$rows->akun_kredit] - $rows->saldo;
                  } else {
                     $saldo_akun[$rows->akun_kredit] = 0 - $rows->saldo;
                  }
               }
            }
         }
      }
      return $saldo_akun;
   }
}
