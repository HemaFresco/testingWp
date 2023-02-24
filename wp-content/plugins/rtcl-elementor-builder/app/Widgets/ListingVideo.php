<?php
/**
 * Main Elementor ListingVideo Class
 *
 * ListingVideo main class
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
use RtclElb\Widgets\WidgetSettings\ListingVideoSettings;

/**
 * ListingVideo class
 */
class ListingVideo extends ListingVideoSettings {

	/**
	 * Construct function
	 *
	 * @param array  $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Listing Video', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rt-listing-video';
		parent::__construct( $data, $args );
	}
	/**
	 * Listing Gallery
	 *
	 * @return array
	 */
	public function get_the_video() {
		$data       = [
			'videos' => [],
		];
		$video_urls = [];
		if ( ! Functions::is_video_urls_disabled() ) {
			$video_urls = get_post_meta( $this->listing->get_id(), '_rtcl_video_urls', true );
			$video_urls = ! empty( $video_urls ) && is_array( $video_urls ) ? $video_urls : [];
		}
		$data['videos'] = $video_urls;
		return $data;
	}
	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings = $this->get_settings();

		$template_style = 'single/video';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = array_merge(
			$data,
			$this->get_the_video()
		);
		$data           = apply_filters( 'rtcl_el_listing_page_video_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
	}


}

