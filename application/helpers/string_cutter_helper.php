<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*   String Cutter
*   Created by Muhammad Ahsin (ahsin90@rocketmail.com)
*   2015
*
*   Tested on Codeigniter v.3
*
*/

function string_cutter($text, $maxchar, $end='...') {
    if (strlen($text) > $maxchar || $text == '') {
        $words = preg_split('/\s/', $text);      
        $output = '';
        $i      = 0;
        while (1) {
            $length = strlen($output)+strlen($words[$i]);
            if ($length > $maxchar) {
                break;
            } 
            else {
                $output .= " " . $words[$i];
                ++$i;
            }
        }
        $output .= $end;
    } 
    else {
        $output = $text;
    }
    return $output;
}