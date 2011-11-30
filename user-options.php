<?php
//If file uploaded
global $wpdb;
$theads = array('ID', 'Vendor', 'Amount');

$user = wp_get_current_user();
$user_id = $user->ID;
$users = $wpdb->get_col("select user_id from {$wpdb->prefix}coupon_relations");
$users = array_unique($users);

if (!in_array($user->ID, $users))
    exit('<div class="updated">No coupon is associated with Your username/id</div>');




if (isset($_POST['file_upload'])):
    
if(isset($_POST['user_to_assign'])){
    $user= get_user_by('login', $_POST['user_to_assign']);
    $user_id = $user->ID;    
}



    $file = $_FILES["vendor_file"]["tmp_name"];
    $uploads = wp_upload_dir();
    $dir = $uploads['basedir'];
    if (!is_dir($dir . '/vendor-csv'))
        mkdir($dir . '/vendor-csv');
    $t = time();
    $name = $_FILES["vendor_file"]["name"];
    $s = preg_replace('/([^.]+)/', "\${1}--$t", $name, 1);
    move_uploaded_file($file, $dir . '/vendor-csv/' . $s);
    $file = $dir.'/vendor-csv/'.$s;
    
    $vendor = mysql_real_escape_string($_POST['vendor_to_assign']);
     $vendor_id = $wpdb -> get_var("select id from {$wpdb->prefix}coupon_vendors where vendor='$vendor' ");  

    //uploading to database
    if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
           $coupon_name = mysql_real_escape_string( $data[0]);         
            $coupon_id =  $wpdb->get_var("select id from {$wpdb->prefix}mgm_coupons where name ='$coupon_name' ");
          
            $insert_rel = array(                
                'vendor_id' => $vendor_id
            );
           
            $where = array(
                'coupon_id' =>$coupon_id,
                'user_id' => $user_id
            );   
         
            $wpdb->update($wpdb->prefix.'coupon_relations', $insert_rel,$where);
            
        }

        //var_dump($query);      
        ///exit;
        //  mysql_query($query) or die(mysql_error());
    }
    fclose($handle);



endif;


// Generating Final results
$user = wp_get_current_user();
$user_id = $user->ID;
$coupon_ids = get_option('freecast_coupon_ids');
$final_results = array();

foreach ($coupon_ids as $single):
    $vendors = array();
    $res = $wpdb->get_results("select * from {$wpdb->prefix}mgm_coupons as a inner join {$wpdb->prefix}coupon_relations as b on a.id=b.coupon_id where a.name like '$single-%' and b.user_id=$user->ID ");
    if (empty($res))
        continue;
    foreach ($res as $s):
        $final_results[$single][$s->vendor_id][] = $s;
    endforeach;
endforeach;






?>

<h3>Assign The coupons to another vendor</h3>
 <form action ="" method ="post" enctype="multipart/form-data">
     Upload the csv file containing rest of the coupons.
        <input type="file" name="vendor_file" >
        <br/>
        <br/>
        <?php
        if(current_user_can('administrator') ):            
   ?>
        Select the user for whom the coupons were generated:
                <select name ="user_to_assign">
            <?php
            $all_users = $wpdb->get_col("select user_nicename from $wpdb->users ");
            foreach ($all_users as $user):
                echo "<option>$user</option>";
            endforeach;
            ?>
        </select>
        <?php endif; ?>
        <br/>
        <br/>
        Select a Vendor:
        <br/>
        <select name="vendor_to_assign">
            <?php
           $vendors = $wpdb-> get_col("select vendor from {$wpdb->prefix}coupon_vendors");
            foreach($vendors as $single_vendor) echo "<option>$single_vendor</option>" ?>
        </select>
        <br/>
        <br/>

        <br/>
        
        
        <input class="button-primary" type ="submit" name ="file_upload" value="Assign to new vendor">
    </form>
<br/>
<br/>
<br/>

<!-- Table to show -->
<table class="widefat">
    <thead>
        <?php foreach ($theads as $head): ?>
        <th><?php echo $head; ?></th>
    <?php endforeach; ?>

</thead>
<tbody>
    <?php
    foreach ($final_results as $key=> $single):
        foreach ($single as $key_deep => $single_deep):
       $vendor_name =  $wpdb-> get_var("select vendor from {$wpdb->prefix}coupon_vendors where id=$key_deep");
            ?>
<tr><td><?php echo $key ?></td><td><?php echo $vendor_name ?></td><td><?php echo count($single_deep) ?></td></tr>

            <?php
        endforeach;        
    endforeach;
    ?>
</tbody>
<tfoot>
</tfoot>
</table>
