<?php
/**
 * Main Elementor ListingBusinessHours Class
 *
 * ListingBusinessHours main class
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
use RtclElb\Widgets\WidgetSettings\ListingBusinessHoursSettings;
use RtclElb\Traits\ELTempleateBuilderTraits;

/**
 * ListingBusinessHours class
 */
class ListingBusinessHours extends ListingBusinessHoursSettings {


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
		$this->rtcl_name = __( 'Business Hours', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rt-listing-business-hours';
		parent::__construct( $data, $args );
	}
	/**
	 * Time formate.
	 *
	 * @param [string] $formate time formate.
	 * @return string
	 */
	public function addons_time_format( $formate ) {
		$settings = $this->get_settings();
		if ( '24' === $settings['date_formate'] ) {
			$formate = 'H:i';
		}
		return $formate;
	}
	/**
	 * Business hours display options
	 *
	 * @param [type] $options Business hours display options.
	 * @return array
	 */
	public function business_hours_display_options( $options ) {
		$settings = $this->get_settings();
		if ( $settings['open_status_text'] ) {
			$options['open_status_text'] = $settings['open_status_text'];
		}
		if ( $settings['close_status_text'] ) {
			$options['close_status_text'] = $settings['close_status_text'];
		}
		$options['show_open_status'] = boolval( $settings['rtcl_show_open_status'] );
		return $options;
	}

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		// Change data by filter.
		add_filter( 'rtcl_time_format', [ $this, 'addons_time_format' ] );
		add_filter( 'rtcl_business_hours_display_options', [ $this, 'business_hours_display_options' ] );
		$settings = $this->get_settings();
		// $this->hooks( $settings );
		$template_style = 'single/business-hours';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_listing_page_actions_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );

		// Reset Filter .
		remove_filter( 'rtcl_time_format', [ $this, 'addons_time_format' ] );
		remove_filter( 'rtcl_business_hours_display_options', [ $this, 'business_hours_display_options' ] );

	}
}

