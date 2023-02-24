<?php
/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
* @var Rtcl\Models\Listing $listing
 */

use Rtcl\Helpers\Functions;

?>

<div class="rtcl el-single-addon single-listing-meta-wrap">
	<ul class="rtcl-listing-meta-data">
		<?php
		if ( $instance['rtcl_show_types'] && $listing->get_ad_type() ) {
			$listing_types = Functions::get_listing_types();
			$types         = ! empty( $listing_types ) ? $listing_types[ $listing->get_ad_type() ] : '';
			if ( $types ) {
				?>
				<li class="rtin-type"><i class="rtcl-icon rtcl-icon-tags" aria-hidden="true"></i><?php echo esc_html( $types ); ?></li>
				<?php
			}
		}
		?>
		<?php if ( $instance['rtcl_show_date'] ) : ?>
			<li class="date"><i class="rtcl-icon rtcl-icon-clock"></i><?php $listing->the_time(); ?></li>
		<?php endif; ?>
		<?php if ( $instance['rtcl_show_user'] ) : ?>
			<li class="author"><i class="rtcl-icon rtcl-icon-user" aria-hidden="true"></i><?php esc_html_e( 'by ', 'rtcl-elementor-builder' ); ?>
				<?php $listing->the_author(); ?>
			</li>
		<?php endif; ?>
		<?php
		if ( $instance['rtcl_show_category'] && $listing->has_category() ) :
			$category = $listing->get_categories();
			$category = end( $category );
			?>
			<li class="rt-categories">
				<i class="rtcl-icon rtcl-icon-tags"></i>
				<?php echo esc_html( $category->name ); ?>
			</li>
		<?php endif; ?>
		<?php
		if ( $instance['rtcl_show_location'] && $listing->has_location() ) :
			?>
			<li class="rt-location"><i class="rtcl-icon rtcl-icon-location"></i> <?php $listing->the_locations(); ?>
			</li>
		<?php endif; ?>
		<?php if ( $instance['rtcl_show_views'] ) : ?>
			<li class="rt-views"><i class="rtcl-icon rtcl-icon-eye"> </i>
				<?php echo sprintf( _n( '%s view', '%s views', $listing->get_view_counts(), 'rtcl-elementor-builder' ), number_format_i18n( $listing->get_view_counts() ) ); ?>
			</li>
		<?php endif; ?>
		
	</ul>
</div>
