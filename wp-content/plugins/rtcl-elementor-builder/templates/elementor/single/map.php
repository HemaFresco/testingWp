<?php
/**
 * @author     RadiusTheme
 *
 * @version    1.0.0
 *
* @var Rtcl\Models\Listing $listing
 */

use Rtcl\Helpers\Functions;

add_filter('rtcl_single_listing_map_settings', function ($settings) use ( $instance ) {
	$new_settings = [
		'zoom' => !empty( $instance['zoom_label']['size'] ) ? $instance['zoom_label']['size'] : 14,
	];
	if( !empty( $instance['image_icon']['url'] ) ){ 
		$map_type = Functions::get_map_type();
		if ( 'google' === $map_type){
			$new_settings['icon'] = esc_url($instance['image_icon']['url']);
		} else if ('osm' === $map_type) {
			$new_settings['icon'] = [
				'iconUrl'=> esc_url($instance['image_icon']['url']),
			];
		}
	}
	$settings['map_options'] = array_merge( $settings['map_options'], $new_settings );
	return $settings;
});

?>
<div class="rtcl el-single-addon rtin-content-area ">
	<?php do_action('rtcl_single_listing_display_map', $listing); ?>
</div>
