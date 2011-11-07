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
        $this -> ar= array_merge(range(1,9), range('A','N'),range('P','Z'));
        $this -> prevs = (get_option('freecast_coupon_lots'))?get_option('freecast_coupon_lots'):array(); 
        add_action('admin_menu', array($this, 'CreateMenu'), 50);
    }
    
    function generate_coupons($amt){
       $uniq_id = $this -> get_uniq_id();
       $date = date('Y-m-d H:i:s');       
       $coupons = $this ->generate_single_coupons($amt,$uniq_id);
       $option_ar = array($uniq_id, $date,$amt,0,$coupons);
       $prev_rec = get_option('freecast_coupon_lots');
       if(!$prev_rec)$prev_rec= array();
      $prev_rec[]=$option_ar;
      update_option('freecast_coupon_lots', $prev_rec);
      return $prev_rec;
      
    }
    function get_uniq_id(){                     
        $lot_id = $this -> ar[rand(0,33)];
        $lot_id .= $this -> ar[rand(0,33)];
        $lot_id .= $this -> ar[rand(0,33)];
        if($this -> id_exists($lot_id))return $this ->get_uniq_id();        
       return $lot_id;     
    }
    function id_exists($id){       
          foreach($this-> prevs as $lot):            
            if($lot[0] == id)return true;
        endforeach;
        
    }
    
    function generate_single_coupons($amt,$uniq_id){
        $coupons = array();
        $counter=0;
     for($i=0;$i<34;$i++)
        for($j=0;$j<34;$j++)
            for($k=0;$k<34;$k++)
                for($m=0;$m<34;$m++){
                    $coupons[] = $uniq_id.'-'.$this -> ar[$i].$this -> ar[$j].$this -> ar[$k].$this -> ar[$m];
                    if(++$counter == $amt )return $coupons;
                }
                
                return  $coupons;  
    }
    


    function CreateMenu() {
        add_submenu_page('options-general.php', 'Coupon Settings', 'Coupon Settings', 'activate_plugins', 'wpFreecastCoupons', array($this, 'OptionsPage'));
    }

    function OptionsPage() {
        include 'options-page.php';
    }

    

}
