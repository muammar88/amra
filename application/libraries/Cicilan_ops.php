<?php

/**
 *  -----------------------
 *	Cicilan library
 *	Created by Muammar Kadafi
 *  -----------------------
 */


defined('BASEPATH') or exit('No direct script access allowed');

class Cicilan_ops
{

	public function __construct()
	{
		$this->CI = &get_instance();
	}

	# List skema pembayaran cicilan
	# Total pembayaran sampai dengan pembayaran sekarang
	# Pembayaran sekarang adalah pembayaran terakhir diminta
	public function term_cicilan( $list_skema, $total_pembayaran, $pembayaran_sekarang ) {
		$total_pembayaran_sebelumnya = $total_pembayaran - $pembayaran_sekarang;
		$total = 0;
		$term = array();
		foreach ( $list_skema as $key => $value ) {
			$total = $total + $value['amount'];
			if( $total > $total_pembayaran_sebelumnya ) {
				if( $total <= $total_pembayaran ) {
					$term[] = $value['term'];	
				}
			}
		}
		return $term;
	}

}