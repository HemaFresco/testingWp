<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */
use Rtcl\Helpers\Functions;

add_action('rtcl_listing_can_show_new_badge_settings', '__return_true');
add_action('rtcl_listing_can_show_featured_badge_settings', '__return_true');
add_action('rtcl_listing_can_show_top_badge_settings', '__return_true');
add_action('rtcl_listing_can_show_bump_up_badge_settings', '__return_true');

$desktop  = !empty($instance['rtcl_listings_column']) ? absint($instance['rtcl_listings_column']) : 3;
$tablet = !empty($instance['rtcl_listings_column_tablet']) ? absint($instance['rtcl_listings_column_tablet']) : $desktop;
$mobile = !empty($instance['rtcl_listings_column_mobile']) ? absint($instance['rtcl_listings_column_mobile']) : $tablet;

$cssstyle = null;
$rand     = rand();
$classes  = " rtin-unique-class-$rand ";
if ( $instance['slider_dots'] ) {
	$classes .= ' rtcl-slider-pagination-' . $instance['rtcl_button_dot_style'];
}
if ( $instance['slider_nav'] ) {
	$classes .= ' rtcl-slider-btn-' . $instance['rtcl_button_arrow_style'];
}
// if ( $instance['slider_rtl'] ) {
// $classes .= ' rtcl-slider-rtl';
// }
// slider_rtl
$margin_right = absint( $instance['slider_space_between'] );

// css variable for jumping issue
// Jumping Issue Reduce

$width     = 100 / $desktop;
$cssstyle .= "--xl-width: calc( {$width}% - {$margin_right}px );";
$width     = 100 / $tablet;
$cssstyle .= "--md-width:calc( {$width}% - {$margin_right}px );";

$width     = 100 / $mobile;
$cssstyle .= "--mb-width:calc( {$width}% - {$margin_right}px );";


if ( isset( $instance['slider_space_between'] ) ) {
	$cssstyle .= '--margin-right: ' . $margin_right . 'px;';
	$cssstyle .= '--nagative-margin-right: -' . $margin_right . 'px;';
}
$style = $instance['rtcl_listings_grid_style'];
?>

<div class="rtcl rtcl-listings-sc-wrapper rtcl-elementor-widget rtcl-el-slider-wrapper rtcl-listings-slider <?php echo esc_html( $classes ); ?>" style="<?php echo esc_attr( $cssstyle ); ?>">
	<div class="rtcl-listings-wrapper">

		<?php
		$class  = '';
		$class .= ! empty( $view ) ? 'rtcl-' . $view . '-view ' : 'rtcl-list-view ';
		$class .= ! empty( $style ) ? 'rtcl-' . $style . '-view ' : 'rtcl-style-1-view ';
		?>
		<?php
			$auto_height    = $instance['rtcl_auto_height'] ? $instance['rtcl_auto_height'] : '0';
			$loop           = $instance['slider_loop'] ? $instance['slider_loop'] : '0';
			$autoplay       = $instance['slider_autoplay'] ? $instance['slider_autoplay'] : '0';
			$stop_on_hover  = $instance['slider_stop_on_hover'] ? $instance['slider_stop_on_hover'] : '0';
			$delay          = $instance['slider_delay'] ? $instance['slider_delay'] : '5000';
			$autoplay_speed = $instance['slider_autoplay_speed'] ? $instance['slider_autoplay_speed'] : '200';
			// $per_group      = $instance['slide_per_group'] ? $instance['slide_per_group'] : '1';
			$dots = $instance['slider_dots'] ? $instance['slider_dots'] : '0';
			$nav  = $instance['slider_nav'] ? $instance['slider_nav'] : '0';
			// $rtl           = $instance['slider_rtl'] ? $instance['slider_rtl'] : '0';
			$space_between = isset( $instance['slider_space_between'] ) ? $instance['slider_space_between'] : '20';

		
			$autoplay   = boolval( $autoplay ) ? [
				'delay'                => absint( $delay ),
				'pauseOnMouseEnter'    => boolval( $stop_on_hover ),
				'disableOnInteraction' => false,
			] : boolval( $autoplay );
			$pagination = boolval( $dots ) ? [
				'el'        => ".rtin-unique-class-$rand .rtcl-slider-pagination",
				'clickable' => true,
				'type'      => 'bullets',
			] : boolval( $dots );
			$navigation = boolval( $nav ) ? [
				'nextEl' => ".rtin-unique-class-$rand .button-right",
				'prevEl' => ".rtin-unique-class-$rand .button-left",
			] : boolval( $nav );

			$break_0    = [
				'slidesPerView'  => $mobile,
				'slidesPerGroup' => $mobile ,
			];
			$break_767  = [
				'slidesPerView'  => $tablet,
				'slidesPerGroup' => $tablet,
			];
			$break_1024  = [
				'slidesPerView'  => $desktop,
				'slidesPerGroup' => $desktop,
			];

			$swiper_data = [
				// Optional parameters
				'slidesPerView'  => $desktop,
				'slidesPerGroup' => $desktop,
				'spaceBetween'   => absint( $space_between ),
				'loop'           => boolval( $loop ),
				// If we need pagination
				'slideClass'     => 'swiper-slide-customize',
				'autoplay'       => $autoplay,
				// If we need pagination
				'pagination'     => $pagination,
				'speed'          => absint( $autoplay_speed ),
				// allowTouchMove: true,
				// Navigation arrows
				'navigation'     => $navigation,
				'autoHeight'     => boolval( $auto_height ),
				'breakpoints'    => [
					0    => $break_0,
					767  => $break_767,
					1024 => $break_1024,
				],
			];
			$swiper_data = wp_json_encode( $swiper_data );


			?>
		<div class="rtcl-listings rtcl-listings-slider-container swiper <?php echo esc_attr( $class ); ?> rtcl-carousel-slider " data-options="<?php echo esc_attr( $swiper_data ); ?>"  <?php // echo $rtl ? ' dir="rtl" ' : ''; ?> >
			<div class="rtcl-swiper-lazy-preloader">
				<svg class="spinner" viewBox="0 0 50 50">
					<circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
				</svg>
			</div>
			<div class="swiper-wrapper">
				<?php
				while ( $rtcl_related_query->have_posts() ) :
					$rtcl_related_query->the_post();

					$content_data = [
						'template'              => 'single/related-listing/grid/' . $style,
						'instance'              => $instance,
						'style'                 => $style,
						'item_class'            => '',
						'default_template_path' => rtclElb()->get_plugin_template_path(),
					];
					$content_data = apply_filters( 'rtcl_el_listing_archive_content_data', $content_data );
					Functions::get_template( $content_data['template'], $content_data, '', $content_data['default_template_path'] );
					endwhile;
				?>
				<?php wp_reset_postdata(); ?>

			</div> <!-- End wiper-wrapper -->
		</div>  <!-- End rtcl-listings swiper -->
		<?php if ( $instance['slider_nav'] ) { ?>
			<!-- If we need navigation buttons -->
			<span class="rtcl-slider-btn button-left rtcl-icon-angle-left"></span>
			<span class="rtcl-slider-btn button-right rtcl-icon-angle-right"></span>
		
		<?php } ?>
		<?php if ( $instance['slider_dots'] ) { ?>
			<!-- If we need pagination -->
			<div class="rtcl-slider-pagination"></div>
		<?php } ?>
	</div>
</div>

