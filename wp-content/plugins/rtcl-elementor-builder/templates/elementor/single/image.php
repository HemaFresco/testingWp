<?php
/**
 * @author     RadiusTheme
 *
 * @version    1.0.0
 *
 * @var object  $images
 * @var array[] $videos
 * @var string  $video_url
 */

use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;

$total_gallery_image  = count($images);
$total_gallery_videos = count($videos);
$total_gallery_item   = $total_gallery_image + $total_gallery_videos;
if ($total_gallery_item) {
	// $photoswipe_enabled = empty( $instance['rtcl_show_zoom_icon'] ) ? 'hide-zoom-icon' : 'show-zoom-icon';
	$enable_slider = !empty($instance['rtcl_enable_slider']) ? true : false;

	$slider_data = [
		'slider_enabled'     => $enable_slider,
		'zoom_enabled'       => !empty($instance['rtcl_enable_zoom']) ? true : false,
		'photoswipe_enabled' => !empty($instance['rtcl_show_lightbox_icon']) ? true : false,
	];
	// "zoom_enabled":""
	$slider_data = wp_json_encode($slider_data); ?>
	<div id="rtcl-slider-wrapper" class="el-single-addon rtcl-slider-wrapper " data-options='<?php echo esc_attr($slider_data); ?>' >
		<!-- Slider -->
		<div class="rtcl-slider <?php echo esc_attr($enable_slider ? '' : ' off'); ?>" >
			<?php
			if (!empty($instance['rtcl_show_badge']) && Fns::is_enable_mark_as_sold() && Fns::is_mark_as_sold($listing->get_id())) {
				echo '<span class="rtcl-sold-out">'.apply_filters('rtcl_sold_out_banner_text', esc_html__('Sold Out', 'rtcl-elementor-builder')).'</span>';
			} ?>
			<div class="swiper-wrapper">
				<?php
				if ($total_gallery_videos) {
					foreach ($videos as $index => $video_url) {
						?>
						<div class="swiper-slide rtcl-slider-item rtcl-slider-video-item">
							<iframe class="rtcl-lightbox-iframe" src="<?php echo Functions::get_sanitized_embed_url($video_url); ?>" style="width: 100%; height: 400px; margin: 0;padding: 0; background-color: #000" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
						</div>
						<?php
					}
				}
				if ($total_gallery_image) {
					foreach ($images as $index => $image) {
						$image_size       = !empty($instance['rtcl_thumb_image_size']) ? $instance['rtcl_thumb_image_size'] : 'rtcl-gallery';
						
						$image_attributes = wp_get_attachment_image_src($image->ID, $image_size);
						$image_full       = wp_get_attachment_image_src($image->ID, 'full'); ?>
										<div class="swiper-slide rtcl-slider-item">
											<img src="<?php echo esc_html($image_attributes[0]); ?>" data-src="<?php echo esc_attr($image_full[0]); ?>" data-large_image="<?php echo esc_attr($image_full[0]); ?>" data-large_image_width="<?php echo esc_attr($image_full[1]); ?>" data-large_image_height="<?php echo esc_attr($image_full[2]); ?>" alt="<?php echo get_the_title($image->ID); ?>" data-caption="<?php echo esc_attr(wp_get_attachment_caption($image->ID)); ?>"
												class="rtcl-responsive-img"/>
										</div>
									<?php
					}
				} ?>
			</div>
			<?php if ($instance['rtcl_show_arrow']) { ?>
			<div class="swiper-button-next"></div>
			<div class="swiper-button-prev"></div>
			<?php } ?>
		</div>
		<?php if ( !empty( $instance['rtcl_enable_thumb_slider'] ) && $enable_slider && $total_gallery_item > 1) { ?>
			<!-- Slider nav -->
			<div class="rtcl-slider-nav">
				<div class="swiper-wrapper">
					<?php
					if ($total_gallery_videos) {
						foreach ($videos as $index => $video_url) {
							?>
							<div class="swiper-slide rtcl-slider-thumb-item rtcl-slider-video-thumb">
								<img src="<?php echo Functions::get_embed_video_thumbnail_url($video_url); ?>" class="rtcl-gallery-thumbnail" alt=""/>
							</div>
							<?php
						}
					}
					if ($total_gallery_image) {
						foreach ($images as $index => $image) {
							?>
							<div class="swiper-slide rtcl-slider-thumb-item">
								<?php echo wp_get_attachment_image($image->ID, 'rtcl-gallery-thumbnail'); ?>
							</div>
							<?php
						}
					}
					?>
				</div>
				<?php if ($instance['rtcl_show_arrow']) { ?>
				<div class="swiper-button-next"></div>
				<div class="swiper-button-prev"></div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	<?php
} else { ?>
	<!-- <h3> Gallery is not set. </h3> -->
	<div class="el-single-addon placeholder-image">
		<img src="<?php echo esc_url(Functions::get_default_placeholder_url()); ?>" alt="">
	</div>
	
<?php }
