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
    public $single_coupons = array();

    function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'mgm_coupons';
        $this->ar = array_merge(range(1, 9), range('A', 'N'), range('P', 'Z'));
        add_action('plugins_loaded', array($this, 'export_csv'), 50);
        add_action('admin_menu', array($this, 'CreateMenu'), 50);
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_ajax_coupon_ajax_remove', array($this, 'coupon_ajax_remove'));
    }

    function export_csv() {
        global $wpdb;
        if (isset($_REQUEST['export-coupon-lot'])) {
            if (!current_user_can('administrator'))
                exit('Only Administrator Can view the contents');
            $id = $_REQUEST['export-coupon-lot'];
            $results = $wpdb->get_results("select name,expire_dt from $this->table where name like '$id-%' ");

            $str = "Coupon,EXPIRE_Date\r\n";

            foreach ($results as $res):
                $expire_dt = (strlen($res->expire_dt) > 2) ? $res->expire_dt : 'None';
                $str .= $res->name . ',' . $expire_dt . "\r\n";
            endforeach;
            header('Content-type: text/csv');
            header("Content-disposition: attachment;filename=Coupon-Lot-{$id}.csv");

            echo $str;
            exit;
        }
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

    function generate_coupons($post) {
        global $wpdb;
        extract($post);
        $uniq_id = $this->get_uniq_id();
        $date = date('Y-m-d H:i:s');
        $coupons = $this->generate_single_coupons($coupon_amt, $uniq_id);



        foreach ($coupons as $coupon):
            if (isset($expire_dt) && strlen($expire_dt) > 2) {
                $insert = array(
                    'name' => $coupon,
                    'value' => $value,
                    'description' => $description,
                    'use_limit' => 1,
                    'expire_dt' => $expire_dt,
                    'create_dt' => $date
                );
            } else {
                $insert = array(
                    'name' => $coupon,
                    'value' => $value,
                    'description' => $description,
                    'use_limit' => 1,
                    'create_dt' => $date
                );
            }

            $wpdb->insert($this->table, $insert);

        endforeach;
//       $option_ar = array($uniq_id, $date,$amt,0,$coupons);
        $prev_rec = get_option('freecast_coupon_ids');
        if (!$prev_rec)
            $prev_rec = array();
        $prev_rec[] = $uniq_id;
        update_option('freecast_coupon_ids', $prev_rec);
        return $uniq_id;
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
        $ids = get_option('freecast_coupon_ids');
        if (!$ids)
            $ids = array();
        foreach ($ids as $lot):
            if ($lot == $id)
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

    function return_coupon_data($id) {
        global $wpdb;
        $results = $wpdb->get_results("select * from $this->table where name like '$id-%'");

        $count = 0;
        foreach ($results as $res) {
            if ($res->used_count == 1)
                $count++;
        }
        $expire_dt = (strlen($res->expire_dt) > 2) ? $res->expire_dt : 'None';
        return array($id, $res->description, $res->create_dt, $expire_dt, count($results), $count);
    }

    function CreateMenu() {
        add_submenu_page('options-general.php', 'Coupon Settings', 'Coupon Settings', 'activate_plugins', 'wpFreecastCoupons', array($this, 'OptionsPage'));
    }

    function OptionsPage() {
        include 'options-page.php';
    }

    function coupon_ajax_remove() {
        global $wpdb;
        $id = $_REQUEST['key'];
        $prev_rec = get_option('freecast_coupon_ids');
        foreach ($prev_rec as $key => $val):
            if ($val == $id)
                unset($prev_rec[$key]);
        endforeach;
        $wpdb->query("delete from $this->table where name like '$id-%'");
        update_option('freecast_coupon_ids', $prev_rec);
        exit;
    }

}
