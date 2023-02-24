<?php
/**
 * Main Elementor ListingPageHeader Class
 *
 * ListingPageHeader main class
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
use RtclElb\Widgets\WidgetSettings\ListingPageHeaderSettings;
use RtclElb\Traits\ELTempleateBuilderTraits;

/**
 * ListingPageHeader class
 */
class ListingPageHeader extends ListingPageHeaderSettings {

	/**
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;
	/**
	 * Construct function
	 *
	 * @param array  $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Page Header', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rt-listing-page-header';
		parent::__construct( $data, $args );
		if ( self::is_builder_page_single() ) {
			$this->rtcl_category = 'rtcl-elementor-single-widgets';
		}
		if ( self::is_builder_page_archive() ) {
			$this->rtcl_category = 'rtcl-elementor-archive-widgets';
		}
		// Category /@dev.
	}

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings       = $this->get_settings();
		$template_style = 'page-header/listing-page-header';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_listing_page_header_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
	}

}

