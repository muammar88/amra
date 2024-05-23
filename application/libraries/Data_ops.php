<?php

/**
 *  -----------------------
 *	Authentication library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Data_ops
{

   function __construct()
   {
      $this->CI = &get_instance();
   }

   function simple_get($q)
   {
      $list = array();
      foreach ($q->result() as $rows) {
         $arr = array();
         foreach ($q->list_fields() as $key => $value) {
            $arr[$value] = $rows->$value;
         }
         $list[] = $arr;
      }
      return $list;
   }
}
