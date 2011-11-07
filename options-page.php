<?php
$theads=array('Coupon Lot ID', 'Generation Date', 'Amount', 'Used Amount');
global $wpFreecastCoupons;
if(isset($_POST['main-submit'])):
	$_POST = array_map( create_function('$a', 'return trim($a);'), $_POST);
	extract($_POST);        
        $coupons = $wpFreecastCoupons -> generate_coupons($coupon_amt);
        if($coupons)echo'Coupons Generated Successfully';
        endif;
     
?>

<div class="wrap">
    
    <form action ='' method='post'>
         <h3>Generate and Export Coupons</h3>
 Number of coupons to generate in a lot(Default is 1000)
  <br/>
 <input style="width:40%" type='text' name='coupon_amt' value="1000"/>
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
            $prevs = (get_option('freecast_coupon_lots'))?get_option('freecast_coupon_lots'):array();
            foreach($prevs as $single):
                echo "<tr><td>$single[0]</td><td>$single[1]</td><td>$single[2]</td><td>$single[3]</td></tr>";
            endforeach;
            ?>
        </tbody>
    </table>
</div>
