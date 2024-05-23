<?php

/**
 *  -----------------------
 *	Text library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');


class Text_ops
{

	private $data = array();

	function __construct()
	{
		$this->text = &get_instance();
	}

	function has_duplicate($array)
	{
		return count($array) === count(array_unique($array)) ? false : true;
	}


	function explode_pln_msg($str){
		$exp = explode('SN :', $str);
		$exp1 = explode('/', $exp[1]);
		$arr = array();
		$arr['nama_pelanggan'] = trim($exp1[1]);
		$arr['pesan'] = $exp[0];
		$arr['serial_number'] = trim($exp1[0]);
		$arr['kwh'] = trim($exp1[2]) . ' ' . trim($exp1[3]). ' ' .trim($exp1[4]);

		return $arr;
   }


	function nominal_currency($n, $presisi=1) {
		if ($n < 900) {
			$format_angka = number_format($n, $presisi);
			$simbol = '';
		} else if ($n < 900000) {
			$format_angka = number_format($n / 1000, $presisi);
			$simbol = 'rb';
		} else if ($n < 900000000) {
			$format_angka = number_format($n / 1000000, $presisi);
			$simbol = 'jt';
		} else if ($n < 900000000000) {
			$format_angka = number_format($n / 1000000000, $presisi);
			$simbol = 'M';
		} else {
			$format_angka = number_format($n / 1000000000000, $presisi);
			$simbol = 'T';
		}

		if ( $presisi > 0 ) {
			$pisah = '.' . str_repeat( '0', $presisi );
			$format_angka = str_replace( $pisah, '', $format_angka );
		}

		return $format_angka . $simbol;
	}

	/**
	 * parsing text
	 * default number character is 20
	 * @return  String text
	 */
	public function parsing_text($field,  $number_character = 10)
	{
		$new_text = "";
		if (trim($field) != '' and strlen(trim($field)) != 0) {
			$explode_text = explode(' ', $field);
			for ($i = 0; $i < (count($explode_text) > $number_character ? $number_character : count($explode_text)); $i++) {
				if ($i == 0) {
					$new_text .= $explode_text[$i];
				} else {
					$new_text .= ' ' . $explode_text[$i];
				}
			}
			$new_text = $new_text . '...';
		}
		return $new_text;
	}

	/**
	 * parsing for long text
	 * default number character is 10
	 * @return  String text with <p></p> tag html
	 */
	public function parsing_long_text($field, $number_character = 10)
	{
		$new_text = "";
		if (trim($field) != '' and strlen(trim($field)) != 0) {
			// explode paragraph
			$explodeParagraph = explode("\r", $field);
			// explode text
			$explode_text = explode(' ', Strip_tags($explodeParagraph[0]));
			for ($i = 0; $i < (count($explode_text) > $number_character ? $number_character : count($explode_text)); $i++) {
				if ($i == 0) {
					$new_text .= $explode_text[$i];
				} else {
					$new_text .= ' ' . $explode_text[$i];
				}
			}
			$new_text = $new_text . '...';
		}
		return '<p>' . $new_text . '</p>';
	}

	/* hide currency from variable */
	function hide_currency($value)
	{
		if (strpos($value, 'Rp') !== false) {
			$value = str_replace("Rp", "", $value);
			/*$value = 'Pertama';*/
			if (strpos($value, '.') !== false) {
				// print("<br>2");
				$value = str_replace(".", "", $value);
				// print("<br>");
				// print( $value );
				if (strpos($value, ',') !== false) {
					$value = str_replace(",", "", $value);
				} elseif ($value == 0) {
					$value = 0;
				}
			} else {
				// print("<br>4");
				if (strpos($value, ',') !== false) {
					// print("<br>5");
					$value = str_replace(",", "", $value);
				}
			}
		} else {
			// print("<br>6");
			if (strpos($value, '.') !== false) {
				// print("<br>7");
				$value = str_replace(".", "", $value);
				if (strpos($value, ',') !== false) {
					// print("<br>8");
					$value = str_replace(",", "", $value);
				}
			} else {
				if (strpos($value, ',') !== false) {
					$value = str_replace(",", "", $value);
				}
			}
		}

		if (!is_numeric($value)) {
			$value = 0;
		}
		// if($value == ''){
		//
		// }
		return $value;
	}

	function checkSlugPaket($text)
	{
		$feedBack = false;
		$i = 0;
		do {
			if ($i != 0) {
				$text = $text . '-' . $i;
			}
			$this->text->db->select('slug')
				->from('paket')
				->where('slug', $text);
			$q = $this->text->db->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
			$i++;
		} while ($feedBack == false);
		return $text;
	}

	function createSlug($str, $delimiter = '-')
	{
		$slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
		return $slug;
	}

	function parsingSlung($slug)
	{
		if (strlen($slug) > 35) {
			$return = '';
			$n_slug = explode('-', $slug);
			for ($i = 0; $i < 3; $i++) {
				if ($i == 0) {
					$return .= $n_slug[$i];
				} else {
					$return .= "-" . $n_slug[$i];
				}
			}
			return $return . '...';
		} else {
			return $slug;
		}
	}

	// remove dot
	function removeDot($value)
	{
		if (strpos($value, '.') !== false) {
			$value = str_replace(".", "", $value);
		}
		return $value;
	}


	public function shortIDCurrency($n, $presisi = 1)
	{
		if ($n < 900) {
			$format_angka = number_format($n, $presisi);
			$simbol = '';
		} else if ($n < 900000) {
			$format_angka = number_format($n / 1000, $presisi);
			$simbol = 'rb';
		} else if ($n < 900000000) {
			$format_angka = number_format($n / 1000000, $presisi);
			$simbol = 'jt';
		} else if ($n < 900000000000) {
			$format_angka = number_format($n / 1000000000, $presisi);
			$simbol = 'M';
		} else {
			$format_angka = number_format($n / 1000000000000, $presisi);
			$simbol = 'T';
		}

		if ($presisi > 0) {
			$pisah = '.' . str_repeat('0', $presisi);
			$format_angka = str_replace($pisah, '', $format_angka);
		}

		return $format_angka . $simbol;
	}

	// function get_invoice_pindah_paket(){
	// 	$my_this =& get_instance();
	// 	$feedBack = false;
	// 	$rand = '';
	// 	do {
	// 		$rand = random_num(14);
	// 		$q = $my_this->db->select('invoice')
	// 						 ->from('handover_facilities')
	// 						 ->where('invoice', $rand)
	// 						 ->get();
	// 		if($q->num_rows() == 0){
	// 			$feedBack = true;
	// 		}
	// 	} while ($feedBack == false);
	// 	return $rand;
	// }

	function generated_invoice_kas()
	{
		$my_this = &get_instance();
		$feedBack = false;
		$rand = '';
		do {
			$rand = random_num(10);
			$q = $my_this->db->select('invoice')
				->from('kas_keluar_masuk')
				->where('invoice', $rand)
				->where('company_id', $my_this->session->userdata($my_this->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function generated_invoice_tiket()
	{
		$my_this = &get_instance();
		$feedBack = false;
		$rand = '';
		do {
			$rand = random_num(10);
			$q = $my_this->db->select('invoice')
				->from('tiket_transaction_history')
				->where('invoice', $rand)
				->where('company_id', $my_this->session->userdata($my_this->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function generated_register_tiket()
	{
		$my_this = &get_instance();
		$feedBack = false;
		$rand = '';
		do {
			$rand = random_num(10);
			$q = $my_this->db->select('no_register')
				->from('tiket_transaction')
				->where('no_register', $rand)
				->where('company_id', $my_this->session->userdata($my_this->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}


	function generated_verified_code()
	{
		$my_this = &get_instance();
		$feedBack = false;
		$rand = '';
		do {
			$rand = random_num(245);
			$q = $my_this->db->select('verified_code')
				->from('company')
				->where('verified_code', $rand)
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	# invoice fasilitas
	function get_invoice_fasilitas()
	{
		$my_this = &get_instance();
		$feedBack = false;
		$rand = '';
		do {
			$rand = random_num(14);
			$q = $my_this->db->select('invoice')
				->from('handover_facilities')
				->where('invoice', $rand)
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	# invoice handover
	function get_invoice_handover()
	{
		$my_this = &get_instance();
		$feedBack 	= false;
		$rand  		= '';
		do {
			$rand = random_num(14);
			$q = $my_this->db->select('invoice_handover')
				->from('handover_item')
				->where('invoice_handover', $rand)
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function get_invoice_returned()
	{
		$my_this = &get_instance();
		$feedBack 	= false;
		$rand  		= '';
		do {
			$rand = random_num(14);
			$q = $my_this->db->select('invoice_returned')
				->from('handover_item')
				->where('invoice_returned', $rand)
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function get_invoice_transaksi_paket()
	{
		$my_this = &get_instance();
		$feedBack 	= false;
		$rand  		= '';
		do {
			$rand = random_num(14);
			$q = $my_this->db->select('invoice')
				->from('paket_transaction_history')
				->where('invoice', $rand)
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}


	function get_invoice_transaksi_paket_cicilan()
	{
		$my_this = &get_instance();
		$feedBack 	= false;
		$rand  		= '';
		do {
			$rand = random_num(14);
			$q = $my_this->db->select('invoice')
				->from('paket_transaction_installement_history')
				->where('invoice', $rand)
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}


	function get_invoice_transaksi_paket_cash()
	{
		$my_this = &get_instance();
		$feedBack 	= false;
		$rand  		= '';
		do {
			$rand = random_num(14);
			$q = $my_this->db->select('invoice')
				->from('paket_transaction_history')
				->where('invoice', $rand)
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function get_no_register()
	{
		$my_this = &get_instance();
		$feedBack 	= false;
		$rand  		= '';
		do {
			$rand = random_num(14);
			$q = $my_this->db->select('no_register')
				->from('paket_transaction')
				->where('no_register', $rand)
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function random_num($size)
	{
		$alpha_key = '';
		$keys = range('A', 'Z');

		for ($i = 0; $i < 2; $i++) {
			$alpha_key .= $keys[array_rand($keys)];
		}

		$length = $size - 2;

		$key = '';
		$keys = range(0, 9);

		for ($i = 0; $i < $length; $i++) {
			$key .= $keys[array_rand($keys)];
		}

		return $alpha_key . $key;
	}

	function extract_group_concat_2level($string)
	{
		$exp = explode(';', $string);
		$feedBack = array();
		foreach ($exp as $key => $value) {
			$feedBack[] = explode('$', $value);
		}
		return $feedBack;
	}

	function change_to_date_time_local($datetime)
	{
		$expTanggal = explode(' ', $datetime);
		return $expTanggal[0] . 'T' . $expTanggal[1];
	}

	function penyebut($nilai)
	{
		$nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " " . $huruf[$nilai];
		} else if ($nilai < 20) {
			$temp = $this->penyebut($nilai - 10) . " belas";
		} else if ($nilai < 100) {
			$temp = $this->penyebut($nilai / 10) . " puluh" . $this->penyebut($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . $this->penyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = $this->penyebut($nilai / 100) . " ratus" . $this->penyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . $this->penyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = $this->penyebut($nilai / 1000) . " ribu" . $this->penyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = $this->penyebut($nilai / 1000000) . " juta" . $this->penyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = $this->penyebut($nilai / 1000000000) . " milyar" . $this->penyebut(fmod($nilai, 1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = $this->penyebut($nilai / 1000000000000) . " trilyun" . $this->penyebut(fmod($nilai, 1000000000000));
		}
		return $temp;
	}

	function terbilang($nilai)
	{
		if ($nilai < 0) {
			$hasil = "minus " . trim($this->penyebut($nilai));
		} else {
			$hasil = trim($this->penyebut($nilai));
		}
		return $hasil;
	}

	function random_alpha_numeric($size){

      $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
      //;
      $alpha_key = '';
      $keys = range('A', 'Z');
      for ($i = 0; $i < 2; $i++) {
         $alpha_key .= $keys[array_rand($keys)];
      }

      $length = $size;

      $key = '';
      $keys = range(0, 9);
      for ($i = 0; $i < $length; $i++) {
         $key .= $keys[array_rand($keys)];
      }

      return substr(str_shuffle($alpha_key . $key .$str_result),0, $size);

   }


}
