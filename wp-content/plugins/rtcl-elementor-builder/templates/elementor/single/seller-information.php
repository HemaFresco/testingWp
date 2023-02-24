<?php
/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var Rtcl\Models\Listing $listing
 */

use Rtcl\Controllers\Hooks\TemplateHooks;
use RtclPro\Controllers\Elementor\Hooks\ELFilterHooksPro;
// Listing seller contact
add_filter('rtcl_display_location_details_page', '__return_true');
add_filter('rtcl_display_address_details_page', '__return_true');
add_filter('rtcl_display_zipcode_details_page', '__return_true');

if ( ! $instance['rtcl_show_author'] ) {
	remove_action( 'rtcl_listing_seller_information', [ ELFilterHooksPro::class, 'show_author' ], 1 );
}
if ( ! $instance['rtcl_show_location'] ) {
	remove_action( 'rtcl_listing_seller_information', [ TemplateHooks::class, 'seller_location' ], 10 );
}

if ( ! $instance['rtcl_show_contact'] ) {
	remove_action( 'rtcl_listing_seller_information', [ TemplateHooks::class, 'seller_phone_whatsapp_number' ], 20 );
}
if ( ! $instance['rtcl_show_contact_form'] ) {
	remove_action( 'rtcl_listing_seller_information', [ TemplateHooks::class, 'seller_email' ], 30 );
}
if ( ! $instance['rtcl_show_seller_website'] ) {
	remove_action( 'rtcl_listing_seller_information', [ TemplateHooks::class, 'seller_website' ], 50 );
}

?>
<!-- Seller / User Information -->
<div class="rtcl el-single-addon seller-information <?php echo !empty( $instance['rtcl_show_author_image'] ) ? 'show-author-image' : 'hide-author-image' ;?>">
	<div class="rtcl-listing-user-info">
		<div class="list-group">
			<?php do_action( 'rtcl_listing_seller_information', $listing ); ?>
		</div>
	</div>
</div>
<?php
if ( ! $instance['rtcl_show_author'] ) {
	add_action( 'rtcl_listing_seller_information', [ ELFilterHooksPro::class, 'show_author' ], 1 );
}
if ( ! $instance['rtcl_show_location'] ) {
	add_action( 'rtcl_listing_seller_information', [ TemplateHooks::class, 'seller_location' ], 10 );
	remove_filter('rtcl_display_location_details_page', '__return_true');
	remove_filter('rtcl_display_address_details_page', '__return_true');
	remove_filter('rtcl_display_zipcode_details_page', '__return_true');
}
if ( ! $instance['rtcl_show_contact'] ) {
	add_action( 'rtcl_listing_seller_information', [ TemplateHooks::class, 'seller_phone_whatsapp_number' ], 20 );
}
if ( ! $instance['rtcl_show_contact_form'] ) {
	add_action( 'rtcl_listing_seller_information', [ TemplateHooks::class, 'seller_email' ], 30 );
}
if ( ! $instance['rtcl_show_seller_website'] ) {
	add_action( 'rtcl_listing_seller_information', [ TemplateHooks::class, 'seller_website' ], 50 );
}

