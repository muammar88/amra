<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Akuntansi {

   private $access;

   function __construct()
	{
		$this->akuntansi = &get_instance();
	}

 	function iktisar_laba_rugi($periode_id, $company_id)
   {
      # akun primary
      $akun_primary = array(1, 2, 3);
      $salwo_awal = array();
      $this->akuntansi->db->select('akun_secondary_id, saldo')
         ->from('saldo')
         ->where('company_id', $company_id)
         ->where('periode', $periode_id);
      $q = $this->akuntansi->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $saldo_awal[$rows->akun_secondary_id] = $rows->saldo;
         }
      }
      // echo "<br>";
      // print_r($saldo_awal);
      // echo "<br>";
      // echo "-----------------------<br>";
      // echo '<pre>'; print_r($saldo_awal); echo '</pre>';
      // echo "-----------------------<br>";
      # jurnal
      $this->akuntansi->db->select('akun_debet, akun_kredit, saldo')
         ->from('jurnal')
         ->where('company_id', $company_id)
         ->where('periode_id', $periode_id);
      $q = $this->akuntansi->db->get();
      $akun_debet = array();
      $akun_kredit = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if (isset($akun_debet[$rows->akun_debet])) {
               $akun_debet[$rows->akun_debet] = $akun_debet[$rows->akun_debet] + $rows->saldo;
            } else {
               $akun_debet[$rows->akun_debet] = $rows->saldo;
            }
            if (isset($akun_kredit[$rows->akun_kredit])) {
               $akun_kredit[$rows->akun_kredit] = $akun_kredit[$rows->akun_kredit] + $rows->saldo;
            } else {
               $akun_kredit[$rows->akun_kredit] = $rows->saldo;
            }
         }
      }

      // echo "-----------------------<br>";
      // echo '<pre>'; print_r($akun_debet); echo '</pre>';
      // echo '<pre>'; print_r($akun_kredit); echo '</pre>';
      // echo "-----------------------<br>";
      # list
      // $list = array();
      $total = array();
      foreach ($akun_primary as $key => $value) {
         $this->akuntansi->db->select('as.id, as.nomor_akun_secondary, as.nama_akun_secondary, ap.sn')
            ->from('akun_secondary AS as')
            ->join('akun_primary AS ap', 'as.akun_primary_id=ap.id', 'inner')
            ->where('as.company_id', $company_id)
            ->where('as.akun_primary_id', $value)
            ->order_by('as.nomor_akun_secondary', 'asc');
         $q = $this->akuntansi->db->get();
         if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
               # get saldo
               $saldo = 0;
               if (isset($saldo_awal[$rows->id])) {
                  $saldo = $saldo + $saldo_awal[$rows->id];
               }
               if ($rows->sn == 'D') {
                  # akun debet
                  if (isset($akun_debet[$rows->nomor_akun_secondary])) {
                     // echo "-----1<br>";
                     $saldo = $saldo + $akun_debet[$rows->nomor_akun_secondary];
                  }
                  # akun kredit
                  if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
                     // echo "-----2<br>";
                     $saldo = $saldo - $akun_kredit[$rows->nomor_akun_secondary];
                  }
               } elseif ($rows->sn == 'K') {
                  # akun debet
                  if (isset($akun_debet[$rows->nomor_akun_secondary])) {
                     // echo "-----3<br>";
                     $saldo = $saldo - $akun_debet[$rows->nomor_akun_secondary];
                  }
                  # akun kredit
                  if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
                     // echo "-----4<br>";
                     $saldo = $saldo + $akun_kredit[$rows->nomor_akun_secondary];
                  }
               }

               if (isset($total[$value])) {
                  // echo "-----5<br>";
                  $total[$value] = $total[$value] + $saldo;
               } else {
                  // echo "-----6<br>";
                  // echo $value;
                  // echo "-----6<br>";
                  $total[$value] = $saldo;
               }
            }
         }
      }

      // echo "+++++++++++++++++<br>";
      // print_r($total);
      // print_r($total);
      // echo "+++++++++++++++++<br>";
      return ($total[1] - $total[2] - $total[3]);
   }


   function get_jurnal_by_periode($periode_id, $company_id) {
		$this->akuntansi->db->select('akun_debet, akun_kredit, saldo')
         ->from('jurnal')
         ->where('company_id', $company_id)
         ->where('periode_id', $periode_id);
      $q = $this->akuntansi->db->get();
      $akun_debet = array();
      $akun_kredit = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if (isset($akun_debet[$rows->akun_debet])) {
               $akun_debet[$rows->akun_debet] = $akun_debet[$rows->akun_debet] + $rows->saldo;
            } else {
               $akun_debet[$rows->akun_debet] = $rows->saldo;
            }
            if (isset($akun_kredit[$rows->akun_kredit])) {
               $akun_kredit[$rows->akun_kredit] = $akun_kredit[$rows->akun_kredit] + $rows->saldo;
            } else {
               $akun_kredit[$rows->akun_kredit] = $rows->saldo;
            }
         }
      }

      return array('akun_debet' => $akun_debet, 'akun_kredit' => $akun_kredit);
   }


   function saldo_awal($periode_id, $company_id) {
   	$saldo_awal = array();
      $this->akuntansi->db->select('akun_secondary_id, saldo')
         ->from('saldo')
         ->where('company_id', $company_id)
         ->where('periode', $periode_id);
      $q = $this->akuntansi->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $saldo_awal[$rows->akun_secondary_id] = $rows->saldo;
         }
      }
      return $saldo_awal;
   }

   function total_saldo($periode_id, $akun_debet, $akun_kredit, $company_id, $saldo_awal) {
   	# akun primary
      $akun_primary = array(1, 2, 3);
      # list
      $list = array();
      foreach ($akun_primary as $key => $value) {
         $this->akuntansi->db->select('as.id, as.nomor_akun_secondary, as.nama_akun_secondary, ap.sn')
            ->from('akun_secondary AS as')
            ->join('akun_primary AS ap', 'as.akun_primary_id=ap.id', 'inner')
            ->where('as.company_id', $company_id)
            ->where('as.akun_primary_id', $value)
            ->order_by('as.nomor_akun_secondary', 'asc');
         $q = $this->akuntansi->db->get();
         if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
               # get saldo
               $saldo = 0;
               if (isset($saldo_awal[$rows->id])) {
                  $saldo = $saldo + $saldo_awal[$rows->id];
               }
               if ($rows->sn == 'D') {
                  # akun debet
                  if (isset($akun_debet[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo + $akun_debet[$rows->nomor_akun_secondary];
                  }
                  # akun kredit
                  if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo - $akun_kredit[$rows->nomor_akun_secondary];
                  }
               } elseif ($rows->sn == 'K') {
                  # akun debet
                  if (isset($akun_debet[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo - $akun_debet[$rows->nomor_akun_secondary];
                  }
                  # akun kredit
                  if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo + $akun_kredit[$rows->nomor_akun_secondary];
                  }
               }


               // echo "Nomor Akun Secondary :" . $rows->nomor_akun_secondary. "<br>";

               // if ($rows->nomor_akun_secondary == '31000') {
                  
               //    echo "Saldo Modal : " . $saldo;  
               //    print_r($akun_debet);
               //    print_r($akun_kredit);

               // }

               if ($rows->nomor_akun_secondary == '33000') {
                  // echo "Periode :" . $periode_id. "<br>";
                  // echo "Saldo :" . $saldo. "<br>";
                  $saldo = $saldo + $this->iktisar_laba_rugi($periode_id, $company_id);
                  // echo "Saldo :" . $saldo. "<br>";
               }

               $list[$value][$rows->nama_akun_secondary] = array(
                  'nomor_akun' => $rows->nomor_akun_secondary,
                  'nama_akun_secondary' => $rows->nama_akun_secondary,
                  'saldo' => $saldo
               );
            }
         }
      }

      // echo("----------------------------");
      // echo '<pre>'; print_r($list); echo '</pre>';
      // echo("----------------------------");
      return $list;
   }


}