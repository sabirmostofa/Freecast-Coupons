<?php

/*
  Plugin Name: WP-Freecast-Coupons
  Plugin URI: http://sabirul-mostofa.blogspot.com
  Description: Generate and Export Coupons
  Version: 1.0
  Author: Sabirul Mostofa
  Author URI: http://sabirul-mostofa.blogspot.com
 */


$wpFreecastCoupons= new wpFreecastCoupons();

class wpFreecastCoupons{
    
    public $ar = array();
    public $prevs = array();
    public $single_coupons = array();

    function __construct() {
        $this -> ar= array_merge(range(1,9), range('A','Z'));
        $this -> prevs = (get_option('freecast_coupon_lots'))?get_option('freecast_coupon_lots'):array(); 
        add_action('admin_menu', array($this, 'CreateMenu'), 50);
    }
    
    function generate_coupons($amt, $val){
       $uniq_id = $this -> get_uniq_id();
       $date = date('Y-m-d H:i:s');
     
    }
    function get_uniq_id(){                     
        $lot_id = $this -> ar[rand(0,34)];
        $lot_id .= $this -> ar[rand(0,34)];
        $lot_id .= $this -> ar[rand(0,34)];
        if($this -> id_exists($lot_id))return $this ->get_uniq_id();        
       return $lot_id;     
    }
    function id_exists($id){       
          foreach($this-> prevs as $lot):            
            if($lot[0] == id)return true;
        endforeach;
        
    }
    
    function generate_single_coupon(){
        $coup_id = $this -> ar[rand(0,34)];
        $coup_id .= $this -> ar[rand(0,34)];
        $coup_id .= $this -> ar[rand(0,34)];
        $coup_id .= $this -> ar[rand(0,34)];
        
        
        
    }
    
    function coupon_exists(){
        if(in_array($coupon, $this -> single_coupons))return true;
    }

    function CreateMenu() {
        add_submenu_page('options-general.php', 'Coupon Settings', 'Coupon Settings', 'activate_plugins', 'wpFreecastCoupons', array($this, 'OptionsPage'));
    }

    function OptionsPage() {
        include 'options-page.php';
    }

    

}
