<?php

/*
  Plugin Name: WP-Freecast-Coupons
  Plugin URI: http://sabirul-mostofa.blogspot.com
  Description: Generate and Export Coupons
  Version: 1.0
  Author: Sabirul Mostofa
  Author URI: http://sabirul-mostofa.blogspot.com
 */


$wpFreecastCoupons = new wpFreecastCoupons();

class wpFreecastCoupons {

    public $table = '';
    public $ar = array();
    public $prevs = array();
    public $single_coupons = array();

    function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'mgm_coupons';
        $this->ar = array_merge(range(1, 9), range('A', 'N'), range('P', 'Z'));
        add_action('admin_menu', array($this, 'CreateMenu'), 50);
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }

    function admin_scripts() {
        if (stripos($_SERVER['REQUEST_URI'], 'wpFreecastCoupons') !== false) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('fc_ui_script', plugins_url('/', __FILE__) . 'js/jquery.ui.core.min.js');
            wp_enqueue_script('fc_datepicker_script', plugins_url('/', __FILE__) . 'js/jquery.ui.datepicker.min.js');
            wp_enqueue_script('fc_admin_script', plugins_url('/', __FILE__) . 'js/script_admin.js');
            wp_register_style('fc_datepicker_css', plugins_url('/', __FILE__) . 'css/jquery.ui.datepicker.css', false, '1.0.0');
            wp_enqueue_style('fc_datepicker_css');
        }
    }

    function generate_coupons($amt) {
        global $wpdb;

        $uniq_id = $this->get_uniq_id();
        $date = date('Y-m-d H:i:s');
        $coupons = $this->generate_single_coupons($amt, $uniq_id);

        foreach ($coupons as $coupon):
            $wpdb->insert($this->table, array(
                'name' => $name,
                'value' => $value,
                'description' => $description,
                'use_limit' => 1,
                'expire_dt' => $expire,
                'create_dt' => $date
            ));

        endforeach;
//       $option_ar = array($uniq_id, $date,$amt,0,$coupons);
//       $prev_rec = get_option('freecast_coupon_lots');
//       if(!$prev_rec)$prev_rec= array();
//      $prev_rec[]=$option_ar;
//      update_option('freecast_coupon_lots', $prev_rec);
    }

    function get_uniq_id() {
        $lot_id = $this->ar[rand(0, 33)];
        $lot_id .= $this->ar[rand(0, 33)];
        $lot_id .= $this->ar[rand(0, 33)];
        if ($this->id_exists($lot_id))
            return $this->get_uniq_id();
        return $lot_id;
    }

    function id_exists($id) {
        foreach ($this->prevs as $lot):
            if ($lot[0] == id)
                return true;
        endforeach;
    }

    function generate_single_coupons($amt, $uniq_id) {
        $coupons = array();
        $counter = 0;
        for ($i = 0; $i < 34; $i++)
            for ($j = 0; $j < 34; $j++)
                for ($k = 0; $k < 34; $k++)
                    for ($m = 0; $m < 34; $m++) {
                        $coupons[] = $uniq_id . '-' . $this->ar[$i] . $this->ar[$j] . $this->ar[$k] . $this->ar[$m];
                        if (++$counter == $amt)
                            return $coupons;
                    }

        return $coupons;
    }

    function CreateMenu() {
        add_submenu_page('options-general.php', 'Coupon Settings', 'Coupon Settings', 'activate_plugins', 'wpFreecastCoupons', array($this, 'OptionsPage'));
    }

    function OptionsPage() {
        include 'options-page.php';
    }

}
