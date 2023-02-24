<?php
/**
 * Main Elementor ListingCustomFields Class
 *
 * ListingCustomFields main class
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
use RtclElb\Widgets\WidgetSettings\ListingCustomFieldsSettings;

/**
 * ListingCustomFields class
 */
class ListingCustomFields extends ListingCustomFieldsSettings {

	/**
	 * Construct function
	 *
	 * @param array  $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Custom Fields', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rt-listing-custom-fields';
		parent::__construct( $data, $args );
	}
	/**
	 * Custom field group id
	 *
	 * @param [array] $group_id Goup id list.
	 * @return array
	 */
	public function custom_field_group_ids( $group_id ) {
		$settings = $this->get_settings();
		if ( ! empty( $settings['custom_field_group_list'] ) && is_array( $settings['custom_field_group_list'] ) ) {
			$ids = array_filter( $settings['custom_field_group_list'] );
			if ( count( $ids ) ) {
				$group_id = $ids;
			}
		}
		return $group_id;
	}
	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {

		add_filter( 'rtcl_listing_get_custom_field_group_ids', [ $this, 'custom_field_group_ids' ], 10, 1 );
		$settings = $this->get_settings();

		$template_style = 'single/custom-fields';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_listing_page_overview_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );

		remove_filter( 'rtcl_listing_get_custom_field_group_ids', [ $this, 'custom_field_group_ids' ], 10, 1 );

	}

}

