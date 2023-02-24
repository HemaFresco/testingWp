<?php
/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
* @var \Rtcl\Models\Listing $listing
 */

?>
<div class="rtcl el-single-addon item-price <?php echo esc_attr( $instance['rtcl_price_style'] ); ?>">
	<?php echo $listing->get_price_html(); ?>
</div>
