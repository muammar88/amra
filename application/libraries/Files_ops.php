<?php

/**
 *  -----------------------
 *	File ops library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

// /* Muammar Kadafi -- Start */
// // define path separator
// define('DS', DIRECTORY_SEPARATOR);

// // define slide path
// define('SLIDE', FCPATH.'image'.DS.'slider'.DS );

// // foto
// define('FOTO', FCPATH.'image'.DS.'foto'.DS );

// // default foto
// define('DEFOT', FCPATH.'image'.DS.'default.png');

// /* Muammar Kadafi -- End */

/**
 *	Files library
 *	Created by Muammar Kadafi
 *
 */
class Files_ops
{

	private $path;

	public function __construct()
	{
		$this->files = &get_instance();
	}

	/**
	 * define path slide
	 * @return  null
	 */
	private function slider($imageName)
	{
		$this->path = FCPATH . 'image/slider/' . $imageName;
	}

	/**
	 * define path foto
	 * @return  null
	 */
	private function foto($imageName)
	{
		$this->path = FCPATH . 'image/foto/' . $imageName;
	}




	/**
	 * checking files
	 * @return String file name
	 */
	public function checking_as_default($imageName, $folder)
	{

		if ($imageName != '' and !is_null($imageName)) {
			// print("masuk<br>");
			$this->$folder($imageName);
			if ($this->path != NULL) {
				if (file_exists($this->path)) {
					return 'image/' . $folder . '/' . basename($this->path);
				} else {
					return 'image/default.png';
				}
			}
		} else {
			return 'image/default.png';
		}
	}
}
