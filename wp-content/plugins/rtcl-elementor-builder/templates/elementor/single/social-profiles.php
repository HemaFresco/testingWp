<?php
/**
 * The template to display the Social profile
 *
 * @author  RadiousTheme
 * @package classified-listing/Templates
 * @var Rtcl\Models\Listing $listing
 */

?>
<?php

add_filter(
	'rtcl_social_profile_label',
	function( $text ) use ( $instance ) {
		if ( ! $instance['rtcl_hide_label'] ) {
			$text = '';
		} elseif ( $instance['label_text'] ) {
			$text = $instance['label_text'];
		}
		return $text;
	}
);
add_filter(
	'rtcl_social_profiles_list',
	function( $options ) use ( $instance ) {
		if ( ! $instance['rtcl_hide_facebook'] ) {
			unset( $options['facebook'] );
		}
		if ( ! $instance['rtcl_hide_twitter'] ) {
			unset( $options['twitter'] );
		}
		if ( ! $instance['rtcl_hide_youtube'] ) {
			unset( $options['youtube'] );
		}
		if ( ! $instance['rtcl_hide_instagram'] ) {
			unset( $options['instagram'] );
		}
		if ( ! $instance['rtcl_hide_linkedIn'] ) {
			unset( $options['linkedin'] );
		}
		if ( ! $instance['rtcl_hide_pinterest'] ) {
			unset( $options['pinterest'] );
		}
		if ( ! $instance['rtcl_hide_reddit'] ) {
			unset( $options['reddit'] );
		}
		return $options;
	}
);

?>
<div class="rtcl el-single-addon social-profile">
	<?php do_action( 'rtcl_single_listing_social_profiles', $listing ); ?>
</div>
