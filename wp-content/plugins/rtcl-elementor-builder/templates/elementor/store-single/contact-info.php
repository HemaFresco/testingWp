<?php
/**
 * Store single content
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.3.21
 */

use RtclElb\Helpers\Fns;
use Rtcl\Helpers\Functions;
use RtclStore\Controllers\Hooks\TemplateHooks;

if ( empty( $instance['rtcl_show_store_status'] ) ) {
	remove_action( 'rtcl_single_store_information', [ TemplateHooks::class, 'store_hours' ], 10 );
}
if ( empty( $instance['rtcl_show_store_address'] ) ) {
	remove_action( 'rtcl_single_store_information', [ TemplateHooks::class, 'store_address' ], 20 );
}
if ( empty( $instance['rtcl_show_store_phone'] ) ) {
	remove_action( 'rtcl_single_store_information', [ TemplateHooks::class, 'store_phone' ], 30 );
}
if ( empty( $instance['rtcl_show_store_social_media'] ) ) {
	remove_action( 'rtcl_single_store_information', [ TemplateHooks::class, 'store_social_media' ], 40 );
}
if ( empty( $instance['rtcl_show_store_email'] ) ) {
	remove_action( 'rtcl_single_store_information', [ TemplateHooks::class, 'store_social_email' ], 50 );
}


?>
<div class="store-information <?php echo \Elementor\Plugin::$instance->editor->is_edit_mode() ? 'edit-mode' : ''; ?>">
	<div class="store-info">
		<?php do_action( 'rtcl_single_store_information', $store ); ?>
	</div>
</div>
<?php

if ( empty( $instance['rtcl_show_store_status'] ) ) {
	add_action( 'rtcl_single_store_information', [ TemplateHooks::class, 'store_hours' ], 10 );
}
if ( empty( $instance['rtcl_show_store_address'] ) ) {
	add_action( 'rtcl_single_store_information', [ TemplateHooks::class, 'store_address' ], 20 );
}
if ( empty( $instance['rtcl_show_store_phone'] ) ) {
	add_action( 'rtcl_single_store_information', [ TemplateHooks::class, 'store_phone' ], 30 );
}
if ( empty( $instance['rtcl_show_store_social_media'] ) ) {
	add_action( 'rtcl_single_store_information', [ TemplateHooks::class, 'store_social_media' ], 40 );
}
if ( empty( $instance['rtcl_show_store_email'] ) ) {
	add_action( 'rtcl_single_store_information', [ TemplateHooks::class, 'store_social_email' ], 50 );
}

