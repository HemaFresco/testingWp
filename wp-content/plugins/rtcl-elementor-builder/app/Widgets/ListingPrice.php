<?php
/**
 * Main Elementor ListingPrice Class
 *
 * ListingPrice main class
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

use Rtcl\Controllers\Hooks\AppliedBothEndHooks;
use Rtcl\Helpers\Functions;
use RtclElb\Widgets\WidgetSettings\ListingPriceSettings;

/**
 * ListingPrice class
 */
class ListingPrice extends ListingPriceSettings {

	/**
	 * Construct function
	 *
	 * @param array  $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Listing Price', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rt-listing-price';
		parent::__construct( $data, $args );
	}

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings = $this->get_settings();
		if ( ! $settings['rtcl_show_price_unit'] ) {
			remove_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_unit_to_price' ], 10, 3 );
		}
		if ( ! $settings['rtcl_show_price_type'] ) {
			remove_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_type_to_price' ], 20, 3 );
		}

		$template_style = 'single/price';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_listing_page_price_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );

		if ( ! $settings['rtcl_show_price_unit'] ) {
			add_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_unit_to_price' ], 10, 3 );
		}
		if ( ! $settings['rtcl_show_price_type'] ) {
			add_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_type_to_price' ], 20, 3 );
		}
	}

}

