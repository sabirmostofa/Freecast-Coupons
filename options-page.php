<?php
$theads=array( 'ID', 'Description', 'Generation Date', 'Expires', 'Amount', 'Used Amount','Delete','Export');
$fields = array( 'value', 'coupon_amt', 'description', 'expire_dt');
global $wpFreecastCoupons;
if(isset($_POST['main-submit'])):
	$_POST = array_map( create_function('$a', 'return trim($a);'), $_POST);
$error=0;
foreach($fields as $field):
    if( !isset($_POST[$field]))
        if($field != 'expire_dt')
        $error=1;
endforeach;
if(!$error){
        $uniq_id = $wpFreecastCoupons -> generate_coupons($_POST);
        if($uniq_id)echo"<div class='updated'>Coupons Generated Successfully. Coupon Id is $uniq_id</div>";
}else echo '<div class="updated">Some field is missing</div>';
        endif;
     
?>

<div class="wrap">
    
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


  <input class='button-primary' type='submit' name="main-submit" value='Generate Coupons'/> 
    </form>
    
    <br/>
    <br/>
    <h3>Coupon Status</h3>
    <table class ="widefat">
        <thead>
            <tr>
                <?php foreach($theads as $head): ?>
            <th>
                <?php echo $head ?>
            </th>
            <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
                   $ids = get_option('freecast_coupon_ids');
                   if(!$ids)$ids= array();
                   $del_image = plugins_url('/', __FILE__) . 'images/b_drop.png';
                   
        foreach($ids as $id):
            $data = $wpFreecastCoupons ->return_coupon_data($id);
        $export_link=home_url().'?export-coupon-lot='.$id;

                echo "<tr><td>$data[0]</td><td>$data[1]</td><td>$data[2]</td><td>$data[3]</td><td>$data[4]</td><td>$data[5]</td>
        <td><a class='delete-fc-coupon' href='#'><img src='$del_image'/></a></td>
        <td><a target='_blank' href='$export_link'>Export</td>
        </tr>";
            endforeach;
            ?>
        </tbody>
    </table>
</div>
