<?php

/**
 *  -----------------------
 *	Date library
 *	Created by Muammar Kadafi
 *  -----------------------
 */


defined('BASEPATH') or exit('No direct script access allowed');

class Date_ops
{
	private $data 	= array();
	private $month 	= array();

	public function __construct()
	{
		$this->CI = &get_instance();
	}

	/**
	 * Checking is date
	 * @return  Boolean
	 * Input date
	 * Return Bolean
	 */
	public function is_date($date)
	{
		if (trim($date) != '') {
			if (strpos(':', $date) === true) {
				$y = explode(':', $date)[0];
			} else {
				$y = $date;
			}

			$x = explode('-', $y);

			return checkdate($x[1], $x[2], $x[0]);
		} else {
			return False;
		}
	}

	/**
	 * Convert month number to name of month
	 * default string space or null
	 * @return String
	 * Input : Month Int
	 * Return Month Name
	 */
	public function month($month = '')
	{
		$list = array(
			1 	=> "Januari",
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
			12 => "Desember"
		);
		if ($month != '') {
			$list = $list[$month];
		}
		return $list;
	}


	public function month_t2($month = '')
	{
		$list = array(
			1 	=> "Jan",
			2 	=> "Feb",
			3 	=> "Mar",
			4 	=> "Apr",
			5 	=> "Mei",
			6 	=> "Jun",
			7 	=> "Jul",
			8 	=> "Aug",
			9 	=> "Sept",
			10 => "Okt",
			11 => "Nov",
			12 => "Des"
		);
		if ($month != '') {
			$list = $list[$month];
		}
		return $list;
	}


	/**
	 * Convert date from format (Y-m-d H:i:s) to (d monthName Year)
	 * default now
	 * @return  String date
	 * Input Y-m-d H:i:s
	 * Return format xx xx xxxx
	 */
	public function change_date($datetime = '')
	{
		if ($this->is_date($datetime) == False) {
			$datetime = date('Y-m-d H:i:s');
		}

		$day = explode(" ", $datetime);
		$date = explode('-', $day[0]);

		if( $date[1] != '10' ){
			$month = $this->month(str_replace('0', '', $date[1]));
		}else{
			$month = $this->month($date[1]);
		}
		return $date[2] . ' ' . $month . ' ' . $date[0];
	}

	/**
	 * Convert date y-m-d example 01.01.1999
	 * default now
	 * @return  String date
	 * Input Y-m-d H:i:s
	 * Return format xx.xx.xxxx
	 */
	public function change_date_t2($datetime)
	{
		if ($this->is_date($datetime) == False) {
			$datetime = ('Y-m-d H:i:s');
		}

		$day = explode(" ", $datetime);
		$date = explode('-', $day[0]);

		return $date[2] . '.' . $date[1] . '.' . $date[0];
	}

	/**
	 * Convert date y-m-d example 01/01/1999
	 * default now
	 * @return  String date
	 * Input Y-m-d H:i:s
	 * Return format xx/xx/xxxx
	 */
	public function change_date_t3($datetime = '')
	{
		if ($this->is_date($datetime) == False) {
			$datetime = ('Y-m-d H:i:s');
		}

		$day = explode(" ", $datetime);
		$date = explode('-', $day[0]);

		return $date[2] . '/' . $date[1] . '/' . $date[0];
	}

	public function change_date_t4($datetime = '')
	{
		if ($this->is_date($datetime) == False) {
			$datetime = date('Y-m-d H:i:s');
		}

		$day = explode(" ", $datetime);
		$date = explode('-', $day[0]);
		if($date[1] != '10'){
			$month = $this->month_t2(str_replace('0', '', $date[1]));
		}else{
			$month = $this->month_t2($date[1]);
		}

		return $date[2] . ' ' . $month . ' ' . substr($date[0], 2);
	}

	public function change_date_t5($datetime = '')
	{
		$day = explode(" ", $datetime);
		$date = explode('-', $day[0]);
		// $month = $this->month_t2(str_replace('0', '', $date[1]));
		if($date[1] != '10'){
			$month = $this->month_t2(str_replace('0', '', $date[1]));
		}else{
			$month = $this->month_t2($date[1]);
		}

		return $date[2] . ' ' . $month . ' ' . substr($date[0], 2);
	}

	/**
	 * Convert date y-m-d example 01/01/1999
	 * default now
	 * @return  String date
	 * Input Y-m-d H:i:s
	 * Return format xx/xx/xxxx
	 */
	public function get_age($birth_date)
	{
		if ($birth_date != 'NULL') {
			$birthDate = explode("-", $birth_date);
			return (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md")
				? ((date("Y") - $birthDate[0]) - 1) : (date("Y") - $birthDate[0]));
		} else {
			return '0';
		}
	}

	/**
	 * Convert date y-m-d example 01/01/1999
	 * default now
	 * @return  String date
	 * Input Y-m-d H:i:s
	 * Return format xx/xx/xxxx
	 */
	public function get_age_until($birth_date, $year)
	{
		if ($birth_date != 'NULL') {
			$birthDate = explode("-", $birth_date);
			return (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md")
				? (($year - $birthDate[0]) - 1) : ($year - $birthDate[0]));
		} else {
			return '0';
		}
	}

	/**
	 * Convert date y-m-d example 01/01/1999
	 * default now
	 * @return  String date
	 * Input Y-m-d H:i:s
	 * Return format xx/xx/xxxx
	 */
	public function list_month($start_date, $end_date)
	{
		$list = array();
		$start = (new DateTime($startDate))->modify('first day of this month');
		$end = (new DateTime($endDate))->modify('first day of next month');
		$interval = DateInterval::createFromDateString('1 month');
		$period = new DatePeriod($start, $interval, $end);
		foreach ($period as $dt) {
			$list[] = $dt->format("Y-m");
		}
		return $list;
	}

	function get_umur($tgl_lahir)
	{
		if ($tgl_lahir != 'NULL') {
			$birthDate = explode("-", $tgl_lahir);
			return (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md")
				? ((date("Y") - $birthDate[0]) - 1) : (date("Y") - $birthDate[0]));
		} else {
			return '0';
		}
	}

	function last_date($start_date, $month){


	}
}
