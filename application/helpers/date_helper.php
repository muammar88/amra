<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

function countDayBetweenToDate($date1, $date2){
	$date = date_diff(date_create($date1), date_create($date2));
	return $date->format('%d');
}

function convertDateToName($date){
	$dt = explode('-', $date);
	return $dt[2].' '.month((int)$dt[1]).' '.$dt[0];
}

function month($mnth){
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
	if($mnth != ''){
		$month = $month[$mnth];
	}
	return $month;
}

function dateSingnature($MandY){
	$var = explode('-', $MandY);
	return bulan( (int)$var[0] ).' '.$var[1];
}