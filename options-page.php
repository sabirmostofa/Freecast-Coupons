<?php
$theads=array('Coupon Lot ID', 'Generation Date', 'Amount', 'Discount', 'Used Amount');
global $wpFreecastCoupons;
if(isset($_POST['main-submit'])):
	$_POST = array_map( create_function('$a', 'return trim($a);'), $_POST);
	extract($_POST);        
        $coupons = $wpFreecastCoupons -> generate_coupons($coupon_amt, $coupon_val);
        
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

 Discount Amount(Default is 40%)
   <br/>
 <input style="width:40%" type='text' name='coupon_val' value="40"/>
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
        </tbody>
    </table>
</div>
