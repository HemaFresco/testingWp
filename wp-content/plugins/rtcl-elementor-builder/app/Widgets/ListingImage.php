<?php
/**
 * Main Elementor ListingImage Class
 *
 * ListingImage main class
 *
 * @author  RadiusTheme
 * @since   2.0.10
 * @package  RTCL_Elementor_Builder
 * @version 1.2
 */

namespace RtclElb\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Rtcl\Helpers\Functions;
use RtclElb\Widgets\WidgetSettings\ListingImageSettings;

// TODO: Zoom, Popups, Thumbnail and Arrow need to disable option.
// TODO: Need to add data option. For all settings.
/**
 * ListingArchive class
 */
class ListingImage extends ListingImageSettings {

	/**
	 * Construct function
	 *
	 * @param array  $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Listing Image', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rt-listing-image';
		parent::__construct( $data, $args );
	}
	/**
	 * Listing Gallery
	 *
	 * @return array
	 */
	public function get_the_gallery() {
		$data     = [
			'images' => [],
			'videos' => [],
		];
		$settings = $this->get_settings();
		if ( ! Functions::is_gallery_disabled() ) {
			$video_urls = [];
			if ( $settings['rtcl_show_video'] && ! Functions::is_video_urls_disabled() && ! apply_filters( 'rtcl_disable_gallery_video', Functions::is_video_gallery_disabled() ) ) {
				$video_urls = get_post_meta( $this->listing->get_id(), '_rtcl_video_urls', true );
				$video_urls = ! empty( $video_urls ) && is_array( $video_urls ) ? $video_urls : [];
			}
			$data['images'] = $this->listing->get_images();
			$data['videos'] = $video_urls;
		}
		return $data;
	}
	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings = $this->get_settings();

		$template_style = 'single/image';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = array_merge(
			$data,
			$this->get_the_gallery()
		);
		$data           = apply_filters( 'rtcl_el_listing_page_gallery_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
		$this->edit_mode_script();
	}
	/**
	 * Elementor Edit mode need some extra js for isotop reinitialize
	 *
	 * @return mixed
	 */
	public function edit_mode_script() {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$selector = $this->get_unique_selector() . ' .rtcl-slider-wrapper';
			?>
			<script>
				jQuery('<?php echo esc_attr( $selector ); ?>').rtcl_listing_gallery();
			</script>
			<?php
		}
	}

}

