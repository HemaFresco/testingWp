<?php
/**
 * Main Elementor ListingSellerInformation Class
 *
 * ListingSellerInformation main class
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
use RtclElb\Widgets\WidgetSettings\ListingSellerInfoSettings;
use RtclElb\Traits\ELTempleateBuilderTraits;

/**
 * ListingSellerInformation class
 */
class ListingSellerInformation extends ListingSellerInfoSettings {


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
		$this->rtcl_name = __( 'Seller Information', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rt-listing-seller-information';
		parent::__construct( $data, $args );
	}

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings       = $this->get_settings();
		$template_style = 'single/seller-information';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_listing_page_actions_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
	}

}

