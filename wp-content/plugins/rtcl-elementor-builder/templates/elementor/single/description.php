<?php
/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var Rtcl\Models\Listing $listing
 */
?>
<!-- Description -->
<div class="rtcl rtcl-listing-description el-single-addon <?php echo !empty( $instance['rtcl_drop_cap'] ) ? 'enabled-drop-cap' : ''; ?> ">
	<?php echo wpautop( get_the_content( null, false, $listing->get_id() ) ); ?>
</div>
