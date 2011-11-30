<?php
global $wpdb;
$theads = array('Delete', 'ID', 'Vendor', 'User', 'Description', 'Generation Date', 'Expires', 'Amount', 'Used Amount', 'Export');
$fields = array('value', 'coupon_amt', 'description', 'expire_dt');
global $wpFreecastCoupons;
if (isset($_POST['main-submit'])):
    $_POST = array_map(create_function('$a', 'return trim($a);'), $_POST);
    $error = 0;
    foreach ($fields as $field):
        if (!isset($_POST[$field]))
            if ($field != 'expire_dt')
                $error = 1;
    endforeach;




    if (!$error) {
        $uniq_id = $wpFreecastCoupons->generate_coupons($_POST);
        if ($uniq_id)
            echo"<div class='updated'>Coupons Generated Successfully. Coupon Id is $uniq_id</div>";
    }else
        echo '<div class="updated">Some field is missing</div>';
endif;

//if Vendor file uploaded
if (isset($_POST['file_upload'])):



    $file = $_FILES["vendor_file"]["tmp_name"];
    $uploads = wp_upload_dir();
    $dir = $uploads['basedir'];
    if (!is_dir($dir . '/vendor-csv'))
        mkdir($dir . '/vendor-csv');
    $t = time();
    $name = $_FILES["vendor_file"]["name"];
    $s = preg_replace('/([^.]+)/', "\${1}--$t", $name, 1);
    move_uploaded_file($file, $dir . '/vendor-csv/' . $s);
    $file = $dir . '/vendor-csv/' . $s;

    //uploading to database
    if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
            $data_real = $data;
            $data = array_map(create_function('$a', 'return "\"" . mysql_real_escape_string( trim($a) ) . "\"";'), $data);
            if (!$this->vendor_exists($data_real[0]) && strlen($data[0]))
                $wpdb->query("insert into {$wpdb->prefix}coupon_vendors(vendor) values('$data[0]')");
        }

        //var_dump($query);      
        ///exit;
        //  mysql_query($query) or die(mysql_error());
    }
    fclose($handle);



endif;

//If single vendor
if (isset($_POST['vendor_single'])):
    $vendor = mysql_real_escape_string(trim($_POST['vendor_single']));
    if (!$this->vendor_exists(trim($_POST['vendor_single'])) && strlen($vendor)) {
        $wpdb->query("insert into {$wpdb->prefix}coupon_vendors(vendor) values('$vendor')");
        echo"<div class='updated'>Vendor added</div>";
    } else {
        echo"<div class='updated'>Already in Database or at least a character needed</div>";
    }

endif;
?>

<div class="wrap">
    <h3>Add vendors using the csv file</h3>
    <form action ="" method ="post" enctype="multipart/form-data">
        <input type="file" name="vendor_file" >
        <input type ="submit" name ="file_upload" value="Add vendors">
    </form>
    <h3>Add single vendor</h3>
    <form action ="" method ="post" >
        <input type="text"  name="vendor_single" />
        <input type ="submit" value="Add vendor">
    </form>

    <form action ='' method='post'>
        <h3>Generate and Export Coupons</h3>

        <b>Value(In Percentage)</b>
        <br/>
        <input style="width:40%" type='text' name='value' value="50%"/>
        <br/>
        <b>Number of coupons to generate in a lot(Default is 1000)</b>
        <br/>
        <input style="width:40%" type='text' name='coupon_amt' value="1000"/>
        <br/>
        <b>Description:</b>
        <br/>
        <textarea name="description" rows="8" cols="100">
        </textarea>
        <br/>
        <b>Expire Date(Optional):</b>
        <br/>

        <input style="width:40%" id="datepicker" type='text' name='expire_dt' />
        <br/>
        <br/>
        Select the user for whom you are generating the coupons:
        <br/>
        <select name ="user_to_assign">
            <?php
            $all_users = $wpdb->get_col("select user_nicename from $wpdb->users ");
            foreach ($all_users as $user):
                echo "<option>$user</option>";
            endforeach;
            ?>
        </select>
        <br/>
        Select the vendor for whom you are generating the coupons:

        <select name ="vendor_to_assign">
            <?php
            $all_vendors = $wpdb->get_col("select vendor from {$wpdb->prefix}coupon_vendors");
            foreach ($all_vendors as $vendor):
                echo "<option>$vendor</option>";
            endforeach;
            ?>
        </select>
        <br/>
        <input class='button-primary' type='submit' name="main-submit" value='Generate Coupons'/> 
    </form>

    <br/>
    <br/>
    <h3>Coupon Status</h3>
    <table class ="widefat">
        <thead>
            <tr>
                <?php foreach ($theads as $head): ?>
                    <th>
                        <?php echo $head ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $ids = get_option('freecast_coupon_ids');

            if (!$ids)
                $ids = array();
            $coupon_ids = array_reverse($ids);
            $del_image = plugins_url('/', __FILE__) . 'images/b_drop.png';
            $final_results = array();

            foreach ($coupon_ids as $single):
                $vendors = array();
                $res = $wpdb->get_results("select * from {$wpdb->prefix}mgm_coupons as a inner join {$wpdb->prefix}coupon_relations as b on a.id=b.coupon_id where a.name like '$single-%'  ");
                if (empty($res))
                    continue;
                foreach ($res as $s):
                    $final_results[$single][$s->vendor_id][] = $s;
                endforeach;
            endforeach;
            


            foreach ($final_results as $key => $single):
                foreach ($single as $key_deep => $single_deep):
                 $which_user =get_user_by('id', $single_deep[0]->user_id);
            $which_id = $which_id = $which_user->user_login;
                    $vendor_name = $wpdb->get_var("select vendor from {$wpdb->prefix}coupon_vendors where id=$key_deep");
                     $coup_id =$single_deep[0]-> coupon_id ;
                    $export_link = home_url() . '?export-coupon-lot=' . $key.'&vendor-id='.$key_deep;
                    $amount = count($single_deep);
                    $description = $wpdb->get_var("select description from {$wpdb->prefix}mgm_coupons where id=$coup_id");
                    $gen_date = $wpdb->get_var("select create_dt from {$wpdb->prefix}mgm_coupons where id=$coup_id");
                    $valid =  $wpdb->get_var("select expire_dt from {$wpdb->prefix}mgm_coupons where id=$coup_id");
$valid = ($valid)? $valid:'None';
$used = 0;
foreach($single_deep as $check):
    if($wpdb->get_var("select used_count from {$wpdb->prefix}mgm_coupons where id=$check->coupon_id"))
   $used++;
endforeach;
                    echo "<tr>
        <td><a class='delete-fc-coupon' href='#'><img src='$del_image'/></a></td>
        <td>$key</td>
                <td>$vendor_name</td>
                <td>$which_id</td>
                <td>$description</td>
                <td>$gen_date</td>
                <td>$valid</td>
                    <td>$amount</td>
        <td>$used</td>
        <td><a target='_blank' href='$export_link'>Export</td>
        </tr>";
                endforeach;
            endforeach;
            ?>
        </tbody>
    </table>
</div>
