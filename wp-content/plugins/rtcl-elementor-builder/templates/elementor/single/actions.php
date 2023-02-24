<?php
/**
 * @author     RadiusTheme
 *
 * @version    1.0.0
 *
 * @var Rtcl\Models\Listing $listing
 */
?>
<div class="rtcl el-single-addon rtin-content-area <?php echo !empty($instance['rtcl_inline_style']) ? 'inline-style' : ''; ?>">
	<?php $listing->the_actions(); ?>
</div>
