<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

function sesi($tipe){
	$my_this =& get_instance();
	return $my_this->session->userdata($tipe);
}

function first_sample_post($paragraf){
	if( strpos( $paragraf, '<p>' ) !== false )
	{
		$q = explode('</p>', $paragraf);
		return $q[0];
	} else
	{
		return $paragraf;
	}
}

function notification(){
	$my_this =& get_instance();
	$date_today = date('Y-m-d');
	$q 		=	$my_this->db->select(' pengumuman_id, exp_date, judul_pengumuman, reader ')
						 ->from('pengumuman')
						 ->where('exp_date >="'.$date_today.'"')
						 ->get();
	$num 	= $q->num_rows();
	$list 	= array();
	$i = 0;
	if( $num > 0 )
	{
		foreach ($q->result() as $rowr) 
		{
			if( $rowr->reader != '' ){
				$reader = unserialize( $rowr->reader );
				
				if( is_array( $reader ) ){
					if( count($reader) > 0 ){
						if( ! in_array( $my_this->session->userdata('orangtua_id'),  $reader ) )
						{
							$i++;
						}
					}	
				}
				
			}else{
				$i++;
			}

			$list[$rowr->pengumuman_id] = array('title' => $rowr->judul_pengumuman );
		}
	}				 
	return array('status' 	=> ($num > 0 ? True : False),
				 'num'		=>	$i,
				 'list' 	=> $list);
}

function spp_santri($santri_id){

	$my_this =& get_instance();

	$spp 			= '';
	$tgl_masuk 		= '';

	// informasi santri
	$my_this->db->select('santri_id, nama, tahun_masuk, tgl_masuk, masuk_kelas, nis');
	$my_this->db->from('santri');
	$my_this->db->where('santri_id', $santri_id);
	$q = $my_this->db->get();
	if( $q->num_rows() > 0 )
	{
		foreach ($q->result() as $rows)
		{
			$tgl_masuk 		= $rows->tgl_masuk;
		}
	}

	// beasiswa santri
	$my_this->db->select('b.biaya_beasiswa, b.beasiswa_bulan_mulai, b.beasiswa_bulan_akhir, bp.beasiswa_date');
	$my_this->db->from('beasiswa_peserta AS bp');
	$my_this->db->join('beasiswa AS b', 'bp.beasiswa_id=b.beasiswa_id', 'inner');
	$my_this->db->where('bp.santri_id', $santri_id);
	$a = $my_this->db->get();
	$beasiswa = array();
	if( $a->num_rows() > 0 )
	{
		foreach ($a->result() as $rowa)
		{
			$exp_date 	= explode('-', $rowa->beasiswa_date);
			$start_date = $exp_date[0].'-'.$exp_date[1].'-01';
			$s 			= list_month_between_to_date($start_date, $rowa->beasiswa_bulan_akhir);
			foreach ($s as $keys => $values)
			{
				$tanggal = substr($values , 0, 4).'-'.substr($values , 4, 6);
				$beasiswa[$tanggal] =  $rowa->biaya_beasiswa;
			}
		}
	}

	// get spp
	$r_spp 	=	$my_this->db->select('biaya_spp, dimulai_tgl')
				 			 ->from('spp')
				 			 ->order_by('dimulai_tgl', 'ASC')
				 			 ->get();
	$riwayat_spp = array();
	$first 		 = array();
	$last 		 = array();
	$i 			 = 0;
	$num 		= $r_spp->num_rows();
	if( $num > 0 )
	{
		foreach ($r_spp->result() as $row_spp) 
		{
			if( $i == 0 )
			{
				$first 		= array('biaya' 		=> $row_spp->biaya_spp,
									'dimulai_tgl'	=> transformDate($row_spp->dimulai_tgl));
			}
			if( $num > 1 )
			{
				$last 		= array('biaya' 		=> $row_spp->biaya_spp,
									'dimulai_tgl'	=> transformDate($row_spp->dimulai_tgl));
			}

			$riwayat_spp[] 	= array('biaya' 		=> $row_spp->biaya_spp,
								    'dimulai_tgl'	=> transformDate($row_spp->dimulai_tgl));
			$i++;
		}

		// list tahun
		$tahun 			= date('Y');
		$end_tahun 		= $tahun + 2;
		$temp_exp 		= explode('-', $tgl_masuk);
		$start 			= $temp_exp[0].'-'.$temp_exp[1].'-01';
    	$end 			= $end_tahun.'-12-01';
    	$listb 			= list_month_between_to_date($start, $end);
    	$range_count 	= 0;
    	$feedBack  		= '';

	   	foreach ($listb as $key => $valuess) 
    	{
    		if( $num > 1 )
    		{
    			$biayas = '';
    			if( $valuess < $first['dimulai_tgl'] )
    			{
    				$biayas = $first['biaya'];
    			}else
    			{
    				if( isset($riwayat_spp[$range_count + 1 ]))
    				{
    					if( $valuess >= $riwayat_spp[$range_count]['dimulai_tgl'] AND $valuess < $riwayat_spp[$range_count + 1 ]['dimulai_tgl'] )
    					{
    						$biayas = $riwayat_spp[$range_count]['biaya'];
    					}else{
    						$biayas = $riwayat_spp[$range_count + 1]['biaya'];
    						$range_count++;
    					}
    				}else{
						$biayas = $riwayat_spp[$range_count]['biaya'];
    				}
    			}
	    		if( date('Ym') == $valuess )
	    		{

	    			$spp = IDRcurrency( $biayas );
	    		}
    		}else
    		{
	    		if( date('Ym') == $valuess )
	    		{
	    			$spp = IDRcurrency($first['biaya']);
	    		}
    		}
    	}
 	}

	return $spp;
}


function kelas($santri_id){

	$my_this =& get_instance();

	$q 	=	$my_this->db->select('dk.nama_kelas')
						->from('santri_kelas AS sk')
						->join('kelas AS k', 'sk.kelas_id=k.kelas_id', 'inner')
						->join('daftar_kelas AS dk', 'k.daftar_kelas_id=dk.daftar_kelas_id', 'inner')
						->where('k.tahun_ajaran', get_tahun_ajaran())
						->where('sk.santri_id', $santri_id)
						->get();
	$nama_kelas = '';					
	if( $q->num_rows() >  0 )
	{
		foreach ($q->result() as $row) {
			$nama_kelas = $row->nama_kelas;
		}
	}					
	return $nama_kelas;

}


function gender(){
	$my_this =& get_instance();
	$gender = array('Perempuan', 'Laki-laki');
	if( $my_this->session->userdata('separate_by_sex') == 1 )
	{
		$gender = $my_this->session->userdata('gender') == 0 ? array('Perempuan')	: array( 0 => 'Laki-laki');
	}
	return $gender;
}

function exchangeDate($date){
	$exp =	explode('-', $date);
	return $exp[2].'-'.$exp[1].'-'.$exp[0];
}

function transformDate($date){
	$exp =	explode('-', $date);
	return $exp[0].$exp[1];
}

function get_surat_name($id){
	$my_this =& get_instance();
	$q 	=	$my_this->db->select('nama_surat')
			 		 ->from('surat_alquran')
			 		 ->where('surat_id', $id)
			 		 ->get();

	if( $q->num_rows() > 0 )
	{
		return $q->row()->nama_surat;
	}else
	{
		return 'Nama Surat Tidak Ditemukan';
	}
}

function get_tahun_ajaran(){
	return date('Y');
	
}

function IDRcurrency($val){
	return 'Rp. '.number_format($val);
}
/*return date('n') <= 6 ? date('Y')-1 : date('Y');*/

function hari($index){
	$arr = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu');
	return $arr[$index];
}

/* generate invoice spp */
function generated_invoice_spp(){
	$my_this =& get_instance();
	$feedBack 	= false;
	$rand  		= '';

	do {
		$rand = random_num(14);
		$my_this->db->select('invoice');
		$my_this->db->from('transaksi_spp');
		$my_this->db->where('invoice', $rand);
		$q = $my_this->db->get();
		if($q->num_rows() == 0){
			$feedBack = true;
		}
	} while ($feedBack == false);
	return $rand;
}

/* extract bulan */
function extract_month($value){
	$bln = substr($value,-2);
	$thn = substr($value,0,4);

	return bulan((int)$bln).' '.$thn;
}

function generated_invoice_daftar_ulang(){

	$my_this =& get_instance();
	$feedBack 	= false;
	$rand  		= '';

	do {
		$rand = random_num(14);
		$my_this->db->select('nomor_invoice');
		$my_this->db->from('daftar_ulang');
		$my_this->db->where('nomor_invoice', $rand);
		$q = $my_this->db->get();
		if($q->num_rows() == 0){
			$feedBack = true;
		}
	} while ($feedBack == false);
	return $rand;
}

function list_month_between_to_date($start_date, $end_date){
	$i = date("Ym", strtotime($start_date));
	$seed = array();
	while($i <= date("Ym", strtotime($end_date))){
	    $seed[] = $i;
	    if(substr($i, 4, 2) == "12")
	        $i = (date("Y", strtotime($i."01")) + 1)."01";
	    else
	        $i++;
	}
	return $seed;
}

/* Menghitung jumlah bulan diantra dua tanggal */
function count_between_two_month($start_date, $end_date){

	$time 	= strtotime( $start_date );
	$ts1 	= strtotime( date('Y-m-d', strtotime("+1 month", $time)) );
	$ts2 	= strtotime( date($end_date) );
	$year1 	= date( 'Y', $ts1 );
	$year2 	= date( 'Y', $ts2 );
	$month1 = date( 'm', $ts1 );
	$month2 = date( 'm', $ts2 );
	$total_bulan = ( ( $year2 - $year1 ) * 12 ) + ( $month2 - $month1 ) + 1 ;

	return $total_bulan + 1;
}

function clearlyul( $data ){
	$lv1 = trim(str_replace("<ul>", "", $data));
	$lv2 = trim(str_replace("</ul>", "", $lv1));
	$lv3 = trim(str_replace("<li>", "", $lv2));
	$lv4 = trim(str_replace("</li>", "|", $lv3));
	$lv5 = array_filter(explode('|', $lv4 ));
	$filter_array = array();
	foreach ($lv5 as $key => $value) {
		if( $value != '' OR $value != ' ' OR ! empty( $value ) ){
			$filter_array[] = $value;
		}
	}	
	return $filter_array;	
}

function generated_nis(){
	$my_this =& get_instance();
	$my_this->db->select('nomor_urut');
	$my_this->db->from('daftar_ulang');
	$my_this->db->where('tahun_masuk', date('Y'));
	$my_this->db->order_by('daftar_ulang_id', 'DESC');
	$q = $my_this->db->get();
	if( $q->num_rows() > 0 ){
		$last_nmbr = '';
		foreach ($q->result() as $rows) {
			$last_nmbr = $rows->nomor_urut + 1;
		}
		$return = convertToSmallFrontZeroNumber( $last_nmbr );
	}else{
		$return = '001';
	}

	$id = array('nis' 			=> date('y').$return,
				'nomor_urut' 	=> $return)	;
	return $id;
}

function generated_nomor_ujian(){
	$my_this =& get_instance();
	$my_this->db->select('nomor_ujian');
	$my_this->db->from('registration');
	$my_this->db->where('status', '0');
	$my_this->db->order_by('registration_id', 'DESC');
	$q = $my_this->db->get();
	if( $q->num_rows() > 0 ){
		$last_nmbr = '';
		foreach ($q->result() as $rows) {
			$last_nmbr = $rows->nomor_ujian + 1;
		}
		$return = convertToFrontZeroNumber( $last_nmbr );
	}else{
		$return = '00000001';
	}
	return $return;
}


function convertToSmallFrontZeroNumber( $number ){
	$strln 	= strlen( $number );
	switch ( $strln ) {
		case '1':
			$return = '00'.$number;
			break;
		case '2':
			$return = '0'.$number;
			break;
		case '3':
			$return = $number;
			break;		
		default:
			$return = '';
			break;
	}
	return $return;
}

function convertToFrontZeroNumber( $number ){
	$strln 	= strlen( $number );
	switch ( $strln ) {
		case '1':
			$return = '0000000'.$number;
			break;
		case '2':
			$return = '000000'.$number;
			break;
		case '3':
			$return = '00000'.$number;
			break;		
		case '4':
			$return = '0000'.$number;
			break;	
		case '5':
			$return = '000'.$number;
			break;
		case '6':
			$return = '00'.$number;
			break;	
		case '7':
			$return = '0'.$number;
			break;	
		case '8':
			$return = $number;
			break;		
		default:
			$return = '';
			break;
	}
	return $return;
}


function trans($text){
	$return = '';
	$x = explode('_', $text);
	$n = 0;
	foreach ($x as $key => $vx) {
		if( $n == 0  ){
			$return .= strtoupper( $vx );
		}else{
			$return .= ' '.strtoupper( $vx );
		}
		$n++;
	}
	return $return;
}

function modul_guru_access(){
	return array('beranda', 'kelas');
}

function modul_santri_access(){
	return array('beranda', 'santri', 'riwayat_perizinan');
}

function modul_ortu_access(){
	return array('beranda', 'santri', 'riwayat_perizinan');
}

function modul_admin_access($group_id){
	$mythis 		=& get_instance();
	$feedBack 	= array();	
	$q 	= 	$mythis->db->select('group_access')
			   		 ->from('base_groups')
			 		 ->where('group_id', $group_id)
			 		 ->get();
	if( $q->num_rows() > 0 )
	{
		foreach ($q->result() as $row) 
		{
			$feedBack 	= 	unserialize($row->group_access) ;
		}
	}		 		 

	return $feedBack;
}


function titleName(){
	$my_this =& get_instance();
	$segmen = $my_this->uri->segment(1);
	if( $segmen == ''){
		$title = 'Atra TaSharE';
	}else{
		$sgmn = explode('_', $segmen);
		$n 		= 1;	
		$title 	= '';
		foreach ( $sgmn as $key => $value ) {
			if($n == 1){
				$title .= ucfirst($value);
			}else{
				$title .= ' '.ucfirst($value);
			}	
			$n++;
		}
	}
	return $title;
}

function convert_date($date){
	$tgl = explode('/',$date);
	return $tgl[2].'-'.$tgl[1].'-'.$tgl[0].' 00:00:00';
}


function array_to_list($data_array){
	$return = '<ul>';
	if( count( $data_array) > 0 ){
		foreach ($data_array as $key => $value) {
			$return .= '<li>'.$value['nama_biaya'].' : Rp.'.number_format($value['biaya']).'</li>';
		}
	}
	$return = '</ul>';	
	return $return;
}

function explode_to_list($delimeter, $data_string){
	$return = '';
	if($data_string != '')	{
		$data_string = explode($delimeter, $data_string);
		$return = '<ul style="padding-left: 24px;">';
		foreach ($data_string as $key => $value) {
			$return .= '<li style="text-align: left;">'.$value.'</li>';
		}
		$return .= '</ul>';
	}
	return $return;
}

function random_num($size) {
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


function get_no_register_paket(){
	$my_this =& get_instance();
	$feedBack 	= false;
	$rand  		= '';

	do {
		$rand = random_num(14);
		$my_this->db->select('no_register');
		$my_this->db->from('transaksi');
		$my_this->db->where('no_register', $rand);
		$q = $my_this->db->get();
		if($q->num_rows() == 0){
			$feedBack = true;
		}
	} while ($feedBack == false);
	return $rand;
}

function get_no_invoice_gen_transaction(){
	$my_this =& get_instance();
	$feedBack 	= false;
	$rand  		= '';

	do {
		$rand = random_num(14);
		$my_this->db->select('invoice');
		$my_this->db->from('transaksi_general');
		$my_this->db->where('invoice', $rand);
		$q = $my_this->db->get();
		if($q->num_rows() == 0){
			$feedBack = true;
		}
	} while ($feedBack == false);
	return $rand;
}


function get_no_invoice_paket_la(){
	$my_this =& get_instance();
	$feedBack 	= false;
	$rand  		= '';

	do {
		$rand = random_num(14);
		$my_this->db->select('invoice');
		$my_this->db->from('paket_la_histori');
		$my_this->db->where('invoice', $rand);
		$q = $my_this->db->get();
		if($q->num_rows() == 0){
			$feedBack = true;
		}
	} while ($feedBack == false);
	return $rand;
}


function get_no_invoice_paket(){
	$my_this =& get_instance();
	$feedBack 	= false;
	$rand  		= '';

	do {
		$rand = random_num(14);
		$my_this->db->select('invoice');
		$my_this->db->from('transaksi_histori');
		$my_this->db->where('invoice', $rand);
		$q = $my_this->db->get();
		if($q->num_rows() == 0){
			$feedBack = true;
		}
	} while ($feedBack == false);
	return $rand;
}


function get_alphanumber(){
	$my_this =& get_instance();
	$feedBack 	= false;
	$rand  		= '';

	do {
		$rand = random_num(9);
		$my_this->db->select('invoice');
		$my_this->db->from('ticketing_transaksi');
		$my_this->db->where('invoice', $rand);
		$q = $my_this->db->get();
		if($q->num_rows() == 0){
			$feedBack = true;
		}
	} while ($feedBack == false);
	return $rand;
}


function satuDigit($number){

	$feedBack = array(	'0' => 'Nol',
						'1' => 'Satu',
						'2' => 'Dua',
						'3' => 'Tiga',
						'4' => 'Empat',
						'5' => 'Lima',
						'6' => 'Enam',
						'7' => 'Tujuh',
						'8' => 'Delapan',
						'9' => 'Sembilan');
	return $feedBack[$number];
}

function tigaDigit($number){
	$feedBack = '';
	$arr = $number;

	switch (strlen($number)) {
		case '1':
			$feedBack  .= satuDigit($arr[0]);
			break;
		case '2':
			if($arr[0] == '1'){
				if($arr[1] == '0'){	
					$feedBack  .= 'Sepuluh';
				}else{
					if($arr[1] == '1'){
						$feedBack .= 'Sebelas';
					}else{
						$feedBack .= satuDigit($arr[1]).' Belas';		
					}
				}
			}else{
				if($arr[0] == '0'){
					$feedBack .= satuDigit($arr[1]);
				}else{
					if($arr[1] == '0'){
						$feedBack .= satuDigit($arr[0]).' Puluh';
					}else{
						$feedBack .= satuDigit($arr[0]).' Puluh '.satuDigit($arr[1]);
					}
				}
			}
			break;
		case '3':
			if($arr[0] == '0'){
				$feedBack  .= '';
			}else{
				if($arr[0] == '1'){
					$feedBack .= ' Seratus ';
				}else{
					$feedBack .= satuDigit($arr[0]).' Ratus ';
				}
			}
			if($arr[1] == '0'){
				$feedBack .= '';
			}else{
				if($arr[1] == '1'){
					if($arr[2] == '0'){
						$feedBack  .= ' Sepuluh ';
					}else{
						if($arr[2] == '1'){
							$feedBack .= ' Sebelas ';
						}else{
							$feedBack .= satuDigit($arr[2]).' Belas';		
						}
					}
				}else{
					if($arr[1] == '0'){
						$feedBack .= satuDigit($arr[2]);
					}else{
						if($arr[2] == '0'){
							$feedBack .= satuDigit($arr[1]).' Puluh';
						}else{
							$feedBack .= satuDigit($arr[1]).' Puluh '.satuDigit($arr[2]);
						}
					}
				}
			}
			break;		
		default:
			$feedBack .= 'Tidak Valid';
			break;
	}	

	return $feedBack;
}

function currency_to_text($number) {

	$feedBack = '';
	if (strpos($number, '-') !== false) {
	    $feedBack = 'Minus ';
	    $number = str_replace("-","",$number);
	}

	$arr = explode(',', number_format($number));
	$s = count($arr);
	switch (count($arr)) {
		case '1':
			$feedBack .= tigaDigit( $arr[0] );
			break;
		case '2':
			$feedBack .= tigaDigit($arr[0] ).' Ribu ';
			$feedBack .= tigaDigit($arr[1] );
			break;
		case '3':
			$feedBack .= tigaDigit($arr[0] ).' Juta ';
			$feedBack .= tigaDigit($arr[1] ).' Ribu ';
			$feedBack .= tigaDigit($arr[2] );
			break;
		case '4':
			$feedBack .= tigaDigit($arr[0] ).' Miliar ';
			$feedBack .= tigaDigit($arr[1] ).' Juta ';
			$feedBack .= tigaDigit($arr[2] ).' Ribu ';
			$feedBack .= tigaDigit($arr[3] );
			break;			
		
		default:
			$feedBack .= 'Tidak Valid';
			break;
	}

	return $feedBack.' Rupiah ';
}


// function hide_currency($value){
// 	if( strpos( $value, 'Rp' ) !== false ) {
// 		$value = str_replace("Rp","",$value);
// 		/*$value = 'Pertama';*/
// 		if( strpos( $value, '.' ) !== false ) {
// 			$value = str_replace(".","",$value);
// 			if(strpos( $value, ',' ) !== false){
// 				$value = str_replace(",","",$value);
// 			}	
// 		}else{
// 			if(strpos( $value, ',' ) !== false){
// 				$value = str_replace(",","",$value);
// 			}	
// 		}
// 	}else{
// 		if( strpos( $value, '.' ) !== false ) {
// 			$value = str_replace(".","",$value);
// 			if(strpos( $value, ',' ) !== false){
// 				$value = str_replace(",","",$value);
// 			}	
// 		}else{
// 			if(strpos( $value, ',' ) !== false){
// 				$value = str_replace(",","",$value);
// 			}	
// 		}
// 	}
// 	return $value;
// }

function currencytodes($cur = ''){
	if($cur != ''){
		if(strpos($cur, ',')){
			$cur = str_replace(",","", explode(' ', $cur)[1] );
		}
	}else{
		$cur = 0;	
	}
	return $cur;
}

// function change_date($datetime){
// 	$tgl 		= explode(" ", $datetime);
// 	$tanggal 	= explode('-', $tgl[0]);

// 	$thn = $tanggal[0];
// 	$bln = bulan(str_replace('0', '', $tanggal[1]));
// 	$hr  = $tanggal[2];

// 	return $hr.' '.$bln.' '.$thn ;
// }

function change_date_keberangkatan($date){
	$tgl = explode('-', $date);
	$thn = $tgl[0];
	$bln = bulan(str_replace('0', '', $tgl[1])) ;
	return $bln.' '.$thn; 
}


function parting_description($description, $default_len = 100){
	if (strlen($description) > $default_len){
		$return = substr($description, 0,100).'...';
	}else{
		$return = $description;
	}
	return $return;
}

function parting_title($title, $file_name ){
	$ext = explode('.', $file_name)[1];
	if (strlen($title) > 40){
		$return = str_replace(" ","_",trim( substr($title, 0,30) )) .'...'.$ext;
	}else{
		$return = $title.'.'.$ext;
	}
	return $return;
}	

function createSlug($str, $delimiter = '-'){
    $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
    return $slug;
}

function level_name($var){
	switch ($var) {
		case '1':
			return 'Pengendali Teknis';
			break;
		case '2':
			return 'Ketua Tim';
			break;	
		case '3':
			return 'Anggota';
			break;		
		
		default:
			return 'Level Tidak Ditemukan';
			break;
	}
}

function currencyHide($string){
	return str_replace(",","",str_replace("Rp","",$string));
}

function romanic_number($integer, $upcase = true) 
{ 
    $table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1); 
    $return = ''; 
    while($integer > 0) 
    { 
        foreach($table as $rom=>$arb) 
        { 
            if($integer >= $arb) 
            { 
                $integer -= $arb; 
                $return .= $rom; 
                break; 
            } 
        } 
    } 
    return $return; 
} 

function sasaranKertasKerjaStringToList($string, $pkpt_id){
	$my_this =& get_instance();
	$return = '<ul>';
	$list = explode(',', $string);
	foreach ($list as $key => $value) {
		$sasaran = explode('-',$value);
		// $sasaran 	= explode('-',$value)[1];
		$return    .=  '<li 	data-toggle="modal" 
							data-target="#myModal" 
							onClick="formProgramKerjaKertasKerja(\''.$pkpt_id.'\', \''.$sasaran[0].'\')">
							'.$my_this->Model_program_kerja->CheckProgramKerja($sasaran, $pkpt_id).'
						</li>';
	}
	$return .= '</ul>';
	return $return ;
}

function stringConvertToListPKP($sasaran, $pkpt_id){
	$my_this =& get_instance();
	$exp_l1 = explode(',', $sasaran);
	$return = '<ul style="padding-left: 20px;">';
	foreach ($exp_l1 as $key => $value) {
		$exp_l2 = explode('-', $value);
		$return	.= '<li style="margin: 10px 0px;" 
						data-toggle="modal" 
						data-target="#myModal" 
						onClick="form_daftar_program(\''.$pkpt_id.'\',  \'DAFTAR PROGRAM KERJA PENGAWASAN\', \''.$exp_l2[0].'\')" >
						'.$my_this->Model_program_kerja->CheckProgramKerja($exp_l2, $pkpt_id).'
					</li>';
	}
	$return .= '</ul>';

	return $return;
}

function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();
    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }
        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }
        foreach ($sortable_array as $k => $v) {
            $new_array[] = $array[$k];
        }
    }
    return $new_array;
}


function sort_number($array){
	$sort_array_alphabet 	= array();
	$new_sort_array 		= array();
	$last_array 			= array(); 
	if (count($array) > 0) {
		foreach ($array as $key => $value) {
			$alphabet = $value['alphabet'];
			if (!in_array($alphabet, $sort_array_alphabet)) {
				$sort_array_alphabet[] = $value['alphabet'];
			}
		}
	}
	foreach ($sort_array_alphabet as $ksort => $vsort) {
		$sort_sort 		= array();
		$sort_number 	= array();
		foreach ($array as $karry => $varry) {
			if($varry['alphabet'] == $vsort){
				$sort_sort[$karry] 	 = $varry;
				$sort_number[$karry] = $varry['nbr'];
			}
		}
		asort($sort_number);
		foreach ($sort_number as $key => $value) {
			$last_array[] = $sort_sort[$key];
		}
	}
	return $last_array;
}

function alphabet($pkpt_id, $sasaran_id, $alp=''){
	$my_this =& get_instance();
	$return = array();
	$alphabet_exist = $my_this->Model_program_kerja->CheckNumberAlphabet($pkpt_id, $sasaran_id);
	if($alp != ''){
		unset($alphabet_exist[$alp]); 
	}
	$range = range("A", "Z");
	for ($i=1; $i<=100; $i++) {
	    foreach ($range as $letter) {
	      	if(!in_array($letter, $alphabet_exist )){
	      		$return[$letter] = $letter;
	      	}
	    }
	}
	return $return;
}


function number($program_kerja_id, $nmbr=''){
	$my_this =& get_instance();
	$number = $my_this->Model_program_kerja->CheckNumber($program_kerja_id);
	if($nmbr != ''){
		unset($number[$nmbr]); 
	}	
	$return = array();
	for ($char = 1; $char <= 100; $char++) {
	    if(!in_array($char, $number )){
	      	$return[$char] = $char;
	    }
	}
	return $return;
}


function expToArrayGroupConcat($string, $del=','){
	if($string != ''){
		$exp = explode( $del, $string );
	}else{
		$exp = '';
	}
	return $exp;
}

function parsingAuditor($auditor){
	$explode_l1 = explode('|', $auditor);
	$return = '<ul>';
	foreach ($explode_l1 as $key => $value) {
		$explode_l2 = explode('-', $value);
		$return .= '<li>'.$explode_l2[1].'</li>';
	}
	$return .= '</ul>';
	return $return;
}

function expPath($path){
	$exp = explode('_', $path);
	$return = '';
	$count = 0;
	foreach ($exp as $key => $value) {
		if($count == 0){
			$return .= $value;	
		}else{
			$return .= ' '.$value;	
		}
		$count++;
	}
	return $return;
}	

function stringConvertToList($string, $del=','){
	$exp = explode($del, $string);
	$return = '<ul style="padding-left: 20px;margin-bottom: 0px;">'; 
	foreach ($exp as $key => $value) {
		$return .= '<li>'.$value.'</li>';
	}
	$return .= '</ul>';
	return $return;
}

function stringConvertToListPDFKertasKerja($string, $del=','){
	$exp = explode($del, $string);
	$return = '<ul style="padding-left: 0px;list-style-type:none;margin:0px;">'; 
	foreach ($exp as $key => $value) {
		$return .= '<li>'.$value.'</li>';
	}
	$return .= '</ul>';
	return $return;
}


function HitAnggaran($array){
	$dk_h 		=  $array['dk_h'];
	$dk_gol_3 	=  $array['dk_gol_3'];
	$dk_lump_3 	=  $array['dk_lump_3']; 
	$dk_gol_4 	=  $array['dk_gol_4'];
	$dk_lump_4 	=  $array['dk_lump_4']; 
	$lk_h 		=  $array['lk_h'];
	$lk_gol_3 	=  $array['lk_gol_3'];
	$lk_lump_3 	=  $array['lk_lump_3']; 
	$lk_gol_4 	=  $array['lk_gol_4'];
	$lk_lump_4 	=  $array['lk_lump_4']; 

	$pengali 			= $array['pengali'];
	$penginapan_gol_3 	= $array['penginapan_gol_3'];
	$penginapan_gol_4 	= $array['penginapan_gol_4'];

	$tot_lump_dk 		= ($dk_h * $dk_gol_3 * currencytodes($dk_lump_3) * $pengali) + 
						  ($dk_h * $dk_gol_4 * currencytodes($dk_lump_4) * $pengali);
	$tot_lump_lk 		= ($lk_h * $lk_gol_3 * currencytodes($lk_lump_3) * $pengali) + 
						  ($lk_h * $lk_gol_4 * currencytodes($lk_lump_4) * $pengali);
	$total_penginapan 	= (($lk_h-1) * $lk_gol_3 * currencytodes($penginapan_gol_3)) + 
						  (($lk_h-1) * $lk_gol_4 * currencytodes($penginapan_gol_4));
	$total_biaya 		= "Rp. ".number_format($tot_lump_dk + $tot_lump_lk + $total_penginapan);

	return $total_biaya;
}

function bulan($bulan=''){
	$month = array(	 1 	=> "Januari",
					 2 	=> "Februari",
					 3 	=> "Maret",
					 4 	=> "April",
					 5 	=> "Mei",
					 6 	=> "Juni",
					 7 	=> "Juli",
					 8 	=> "Agustus",
					 9 	=> "September",
					 10 => "Oktober",
					 11 => "November",
					 12 => "Desember" );
	if($bulan != ''){
		$month = $month[$bulan];
	}
	return $month;
}

function  exp_tgl($tgl){
	$return = explode("-", $tgl);
	return $return[2].' '.bulan((int)$return[1]).' '.$return[0];
}

function myModal($idModal, $headerModal, $contentModal = '' , $id_modal_body='', $width="50%", $idbrand="modalAddBrandLabel", $exitButton = True){
	$modal =   '<div class="modal fade" id="'.$idModal.'" role="dialog" aria-labelledby="'. $idbrand.'" aria-hidden="true" >
				    <div class="modal-dialog modal-lg" style="width: '.$width.';">
				        <div class="modal-content" >
				            <div class="modal-header" style="color: white;height: 49px;background-image: linear-gradient(141deg, #1a84b8 0%, #20bfd6f2 75%) !important;padding-top: 12px;">
				                 <h4 class="modal-title" id="'.$idbrand.'" >'.$headerModal.'</h4>';
			                 if($exitButton == True){
			                 	$modal .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			                 }
	$modal .=			    '</div>
				            <div class="modal-body" id="'.$id_modal_body.'">
				            	'.$contentModal.'
				            </div>
				        </div>
				    </div>
				</div>';
	return $modal;
}

function base_form($type, $action, $attribute_form, $array_input, $script=''){
	$my_this =& get_instance();
	
	$form = '';
	switch ( $type ) {
		case 'single':
			$form .= $my_this->my_form->form_open($action, $attribute_form);
			break;
		case 'multiple':
			$form .= $my_this->my_form->form_open_multiple($action, $attribute_form);
			break;	
		default:
			break;
	}
	
	$form .= $my_this->my_form->myRecord($array_input);
	$form .= $my_this->my_form->mySubmitInput('Batalkan', 'Simpan');
	$form .= $my_this->my_form->form_close();
	$form .= '<script>'.$script.'</script>';
	
	return $form;
}

function form_base($type, $action, $attribute_form, $array_input) {

	$my_this =& get_instance();
	$form = '';
	switch ( $type ) {
		case 'default':
			$form .= $my_this->my_form->form_open($action, $attribute_form);
			break;
		case 'multiple':
			$form .= $my_this->my_form->form_open_multiple($action, $attribute_form);
			break;	
		default:

			break;
	}
	$form .= $my_this->my_form->myInput($array_input);
	$form .= $my_this->my_form->mySubmitInput('Batalkan', 'Simpan');
	$form .= $my_this->my_form->form_close();

	return $form;
}

function is_url_exist($url){
	$ch = curl_init($url);    
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if($code == 200){
	   $status = true;
	}else{
	  	$status = false;
	}
	curl_close($ch);
	return $status;
}


function parsingSlung($slug){
	if(strlen($slug) > 35){
		$return = '';
		$n_slug = explode('-', $slug);
		for( $i=0; $i< 3; $i++ ){
			if($i == 0){
				$return .= $n_slug[$i];
			}else{
				$return .= "-".$n_slug[$i];
			}
		}
		return $return.'...';
	}else{
		return $slug;
	}

}


function select($data, $name, $attribute=''){
	$form = '<select class="chosen-select" name="'.$name.'" style="width:100%;" '.$attribute.'>';
	foreach ($data as $key => $value) {
		$form .= '<option value="'.$key.'">'.$value.'</option>';
	}
	$form .= '</select>';
	return $form;
}

function myModal_to($idModal, $headerModal, $contentModal = '' , $id_modal_body='', $width="50%", $idbrand="modalAddBrandLabel", $exitButton = True){
	$modal 	= 	'<div class="modal fade"  role="dialog" id="'.$idModal.'" role="dialog" aria-labelledby="'. $idbrand.'" aria-hidden="true" >
		            <div class="modal-dialog modal-lg" role="document" style="width: '.$width.';">
		                <div class="modal-content">
		                    <div class="modal-header">
		                    	<h4 class="modal-title" id="'.$idbrand.'" >'.$headerModal.'</h4>
		                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                            <span aria-hidden="true">×</span>
		                        </button>
		                    </div>
		                    <div class="modal-body row" id="'.$id_modal_body.'">
		                        '.$contentModal.'
		                    </div>
		                </div>
		            </div>
		        </div>';
	return $modal;
}