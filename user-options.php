<?php
$theads = array('ID', 'Vendor', 'Amount');
global $wpdb;
$user = wp_get_current_user();
$users = $wpdb->get_col("select user_id from {$wpdb->prefix}coupon_relations");
$users = array_unique($users);

if (!in_array($user->ID, $users))
    exit('<div class="updated">No coupon is associated with Your username/id</div>');

$coupon_users = $wpdb->get_col("select coupon_id from {$wpdb->prefix}coupon_relations where user_id={$user->ID} ");

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
        $wpdb-> get_var();
            ?>
<tr><td><?php echo $key ?></td><td><?php echo $key_deep ?></td><td><?php echo count($single_deep) ?></td></tr>

            <?php
        endforeach;        
    endforeach;
    ?>
</tbody>
<tfoot>
</tfoot>
</table>
