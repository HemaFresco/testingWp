<?php
/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
* @var Rtcl\Models\Listing $listing
 */

use Rtcl\Models\Listing;
use Rtcl\Traits\Addons\ListingItem;
use \Elementor\Utils;
?>
<div class="rtcl el-single-addon listing-title ">
	<?php
		$title_text = $listing->get_the_title();
		printf( '<%1$s class="rtcl-listings-header-title page-title" >%2$s</%1$s>', esc_html( Utils::validate_html_tag( $instance['header_size'] ) ), esc_html( $title_text ) );
	?>
</div>
