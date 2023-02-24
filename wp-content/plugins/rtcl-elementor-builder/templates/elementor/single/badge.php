<?php
/**
 * The badge list
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var Rtcl\Models\Listing $listing
 */

use Rtcl\Controllers\Hooks\TemplateHooks;
use RtclPro\Controllers\Hooks\TemplateHooks as RtclProTemplateHooks;

add_action( 'rtcl_listing_can_show_new_badge_settings', '__return_true' );
add_action( 'rtcl_listing_can_show_featured_badge_settings', '__return_true' );
add_action( 'rtcl_listing_can_show_top_badge_settings', '__return_true' );
add_action( 'rtcl_listing_can_show_bump_up_badge_settings', '__return_true' );

if ( ! $instance['rtcl_hide_new'] ) {
	remove_action( 'rtcl_listing_badges', [ TemplateHooks::class, 'listing_new_badge' ], 10 );
}
if ( ! $instance['rtcl_hide_featured'] ) {
	remove_action( 'rtcl_listing_badges', [ TemplateHooks::class, 'listing_featured_badge' ], 20 );
}
if ( ! $instance['rtcl_hide_popular'] ) {
	remove_action( 'rtcl_listing_badges', [ RtclProTemplateHooks::class, 'listing_popular_badge' ], 30 );
}
if ( ! $instance['rtcl_hide_top'] ) {
	remove_action( 'rtcl_listing_badges', [ RtclProTemplateHooks::class, 'listing_top_badge' ], 40 );
}
if ( ! $instance['rtcl_hide_bump_up'] ) {
	remove_action( 'rtcl_listing_badges', [ RtclProTemplateHooks::class, 'listing_bump_up_badge' ], 50 );
}
?>

<div class="rtcl el-single-addon single-listing-meta-wrap">
	<?php $listing->the_badges(); ?>
</div>

<?php
if ( ! $instance['rtcl_hide_new'] ) {
	add_action( 'rtcl_listing_badges', [ TemplateHooks::class, 'listing_new_badge' ], 10 );
}
if ( ! $instance['rtcl_hide_featured'] ) {
	add_action( 'rtcl_listing_badges', [ TemplateHooks::class, 'listing_featured_badge' ], 20 );
}
if ( ! $instance['rtcl_hide_popular'] ) {
	add_action( 'rtcl_listing_badges', [ RtclProTemplateHooks::class, 'listing_popular_badge' ], 30 );
}
if ( ! $instance['rtcl_hide_top'] ) {
	add_action( 'rtcl_listing_badges', [ RtclProTemplateHooks::class, 'listing_top_badge' ], 40 );
}
if ( ! $instance['rtcl_hide_bump_up'] ) {
	add_action( 'rtcl_listing_badges', [ RtclProTemplateHooks::class, 'listing_bump_up_badge' ], 50 );
}
