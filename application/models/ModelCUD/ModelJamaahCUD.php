<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ModelJamaahCUD extends CI_Model
{

   private $status;
   private $content;
   private $error;

   public function __construct()
   {
      parent::__construct();
      $this->error = 0;
   }

   // add update jamaah
   function addUpdateJamaah($dataParam, $dataPersonal, $dataJamaah, $dataMahram)
   {

      foreach ($dataParam as $key => $value) {
         // define jamaah id
         if ($key == 'jamaah_id') {
            $jamaah_id = $value;
         }
         // define personal id
         if ($key == 'personal_id') {
            $personal_id = $value;
         }
      }

      $this->db->trans_start();

      if (isset($personal_id)) {
         // update
         $dataPersonal['last_update'] = date('Y-m-d');
         $this->db->where('personal_id', $personal_id);
         $update = $this->db->update('personal', $dataPersonal);
      } else {
         $dataPersonal['last_update'] = date('Y-m-d');
         $dataPersonal['input_date'] = date('Y-m-d');

         // insert
         $insert = $this->db->insert('personal', $dataPersonal);
         $personal_id = $this->db->insert_id();
      }

      $dataJamaah['personal_id'] = $personal_id;

      if (isset($jamaah_id)) {
         $dataJamaah['last_update'] = date('Y-m-d H:i:s');
         // update jamaah
         $this->db->where('id', $jamaah_id);
         $update = $this->db->update('jamaah', $dataJamaah);
         // delete mahram
         $this->db->where('jamaah_id', $jamaah_id);
         $delete = $this->db->delete('mahram');
      } else {
         $dataJamaah['input_date'] = date('Y-m-d H:i:s');
         $dataJamaah['last_update'] = date('Y-m-d H:i:s');
         // insert
         $insert = $this->db->insert('jamaah', $dataJamaah);
         $jamaah_id = $this->db->insert_id();
      }

      if ($dataMahram['mahram_id'] > 0) {
         $dataMahram['jamaah_id'] = $jamaah_id;
         $insert = $this->db->insert('mahram', $dataMahram);
      }

      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menambahkan data jamaah dengan info jamaah (Jamaah ID : ' . $jamaah_id . ', Nama Jamaah : ' . $dataPersonal['fullname'] . ', Nomor Identitas : ' . $dataPersonal['identity_number'] . ')';
      }
      return $this->status;
   }

   // delete jamaah
   function deleteJamaah($id, $fullname)
   {

      $this->db->trans_start();

      // delete mahram_id
      $this->db->where('jamaah_id', $id);
      $this->db->delete('mahram');

      // delete jamaah id
      $this->db->where('id', $id);
      $this->db->delete('jamaah');

      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menghapus data jamaah ID : ' . $id . ' dengan nama jamaah ' . $fullname . ' ';
      }
      return $this->status;
   }

   /* Write log master data*/
   public function __destruct()
   {
      if ($this->status == true) {
         if ($this->error == 0) {
            $this->syslog->write_log($this->content);
         }
      }
   }
}
