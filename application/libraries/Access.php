<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access {

   private $access;

   function __construct()
	{
		$this->access = &get_instance();
	}

   function subscribtion_tab_access( $company_id ){
      $this->access->db->select('id, tab_id, start_date_subscribtion, end_date_subscribtion')
         ->from('subscribtion_tab_access')
         ->where('company_id', $company_id)
         ->where('end_date_subscribtion >=', date('Y-m-d'));
      $q = $this->access->db->get();
      $list = array();
      if( $q->num_rows() > 0){
         foreach ( $q->result() as $rows ) {
            $list[] = $rows->tab_id;
         }
      }
      return $list;
   }

   /**
    * get all modul access
    */
   public function modul_access( $type, $level, $data_company, $group_access = '' ) {

      # filter
      $modul_access = array();
      $submodul_access = array();
      if( $group_access != '' ) {
         $list = unserialize($group_access);
         $modul_access = $list['modul'];
         $submodul_access = $list['submodul'];
      }

      $subscribtion_tab = $this->subscribtion_tab_access( $data_company['id'] );

      // get all tab
      $this->access->db->select('id, name, icon, path, description, subscibtion_type')
               ->from('base_tab');
      $s = $this->access->db->get();
      $tab = array();
      if( $s->num_rows() > 0 ) {
         foreach ( $s->result() as $rows ) {
            if( $type != 'unlimited' ) {
              if( $rows->subscibtion_type == 'default' ) {
                 $tab[$rows->id] = array('name' => $rows->name, 'icon' => $rows->icon, 'path' => $rows->path, 'description' => $rows->description);
              } else {
                 if( in_array( $rows->id, $subscribtion_tab ) ) {
                    $tab[$rows->id] = array('name' => $rows->name, 'icon' => $rows->icon, 'path' => $rows->path, 'description' => $rows->description);
                 }
              }
            } else {
               $tab[$rows->id] = array('name' => $rows->name, 'icon' => $rows->icon, 'path' => $rows->path, 'description' => $rows->description);
            }
         }
      }

      // get modul
      $this->access->db->select('modul_id, modul_name, modul_path, modul_icon, tab')
         ->from('base_modules');
      if( $level == 'staff') {
         $this->access->db->where('modul_id != ', 8);
      }
      # filter group access
      if( $group_access != '' ) {
         if( count($modul_access) > 0 ) {
            $this->access->db->where_in('modul_id', $modul_access);
         }
      }
      $q = $this->access->db->get();
      $modul_access = array();
      if( $q->num_rows() > 0 ) {
         $i = 1;
         foreach ( $q->result() as $rowq ) {
            // tab module
            if( $rowq->tab != '' ) {
               $modul_tab = array();
               foreach ( unserialize( $rowq->tab ) as $key => $value ) {
                  if( $type != 'unlimited' ) {
                     if ( array_key_exists( $value, $tab ) ) {
                        $modul_tab[] = array('name' => $tab[$value]['name'],
                                             'icon' => $tab[$value]['icon'],
                                             'path' => $tab[$value]['path'],
                                             'description' => $tab[$value]['description']);
                     }
                  }else{
                     $modul_tab[] = array('name' => $tab[$value]['name'],
                                          'icon' => $tab[$value]['icon'],
                                          'path' => $tab[$value]['path'],
                                          'description' => $tab[$value]['description']);
                  }
               }
               # filter module
               if( count($modul_tab) > 0  ) {
                  $modul_access[ $rowq->modul_id ]['modul_id'] = $rowq->modul_id;
                  $modul_access[ $rowq->modul_id ]['modul_name'] = $rowq->modul_name;
                  $modul_access[ $rowq->modul_id ]['modul_path'] = $rowq->modul_path;
                  $modul_access[ $rowq->modul_id ]['modul_icon'] = $rowq->modul_icon;
                  $modul_access[ $rowq->modul_id ]['tab'] = $modul_tab;
               }
            }elseif (  $rowq->tab == '' && $rowq->modul_path == '#' ) {
               $this->access->db->select('submodules_id, submodules_name, submodules_path, tab')
                        ->from('base_submodules')
                        ->where('modul_id', $rowq->modul_id);
               if( $group_access != '' ) {
                  if( count( $submodul_access ) > 0 ) {
                     $this->access->db->where_in('submodules_id', $submodul_access);
                  }
               }
               $r = $this->access->db->get();
               $submodul = array();
               if( $r->num_rows() > 0 ) {
                  foreach ( $r->result() as $rowr ) {
                     if( $rowr->tab != '' ) {
                        $submodul_tab = array();
                        $tabUn = unserialize( $rowr->tab );
                        foreach ( $tabUn  as $key_sub_tab => $value_sub_tab ) {
                           if( $type != 'unlimited' ) {
                              if ( array_key_exists($value_sub_tab,$tab) ) {
                                 $submodul_tab[] = array('name' => $tab[$value_sub_tab]['name'],
                                                       'icon' => $tab[$value_sub_tab]['icon'],
                                                       'path' => $tab[$value_sub_tab]['path'],
                                                       'description' => $tab[$value_sub_tab]['description']);
                              }
                           }else{
                              $submodul_tab[] = array('name' => $tab[$value_sub_tab]['name'],
                                                   'icon' => $tab[$value_sub_tab]['icon'],
                                                   'path' => $tab[$value_sub_tab]['path'],
                                                   'description' => $tab[$value_sub_tab]['description']);
                           }
                        }
                        if( count($submodul_tab) > 0  ){
                           $submodul[ $rowr->submodules_id ]['submodul_id'] = $rowr->submodules_id;
                           $submodul[ $rowr->submodules_id ]['submodules_name'] = $rowr->submodules_name;
                           $submodul[ $rowr->submodules_id ]['submodules_path'] = $rowr->submodules_path;
                           $submodul[ $rowr->submodules_id ]['tab'] = $submodul_tab;
                        }
                     }
                  }
                  # filter submodule
                  if( count($submodul) > 0  ){
                     $modul_access[ $rowq->modul_id ]['modul_id'] = $rowq->modul_id;
                     $modul_access[ $rowq->modul_id ]['modul_name'] = $rowq->modul_name;
                     $modul_access[ $rowq->modul_id ]['modul_path'] = $rowq->modul_path;
                     $modul_access[ $rowq->modul_id ]['modul_icon'] = $rowq->modul_icon;
                     $modul_access[ $rowq->modul_id ]['submodul'] = $submodul;
                  }
               }
            }
         }
      }
      return $modul_access;
   }

}
