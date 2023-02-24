<?php
/**
 * Main Elementor ELFilterHooks Class
 *
 * The main class that filter the functionality.
 *
 * @since 1.0.0
 */

namespace RtclElb\Controllers\Hooks;

use RtclElb\Helpers\Fns;

/**
 * ELFilterHooks class
 */
class FilterHooks {
	/**
	 * Initialize function.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'rtcl_licenses', [ __CLASS__, 'license' ], 15 );
		add_filter( 'rtcl_builder_content_parent_class', [ __CLASS__, 'builder_content_parent_class' ], 10 );
		add_filter( 'body_class', [ __CLASS__, 'body_classes' ] );
	}
	/**
	 * Add Body Class In Builder page
	 *
	 * @param [type] $classes body class.
	 * @return array
	 */
	public static function body_classes( $classes ) {
		if ( Fns::is_store_page_builder() ) {
			$new_classes = [ 'rtcl', 'store-page-by-elementor' ];
			$classes     = array_unique( array_merge( $classes, $new_classes ) );
		}
		return $classes;
	}
	/**
	 * Undocumented function
	 *
	 * @param [array] $licenses settings object.
	 * @return array
	 */
	public static function license( $licenses ) {
		$licenses[] = [
			'plugin_file' => RTCL_ELB_PLUGIN_ACTIVE_FILE_NAME,
			'api_data'    => [
				'key_name'    => 'license_rtclelb_key',
				'status_name' => 'license_rtclelb_status',
				'action_name' => 'rtclelb_manage_licensing',
				'product_id'  => 176717,
				'version'     => RTCL_ELB_VERSION,
			],
			'settings'    => [
				'title' => esc_html__( 'Elementor Builder plugin license key', 'rtcl-elementor-builder' ),
			],
		];
		return $licenses;
	}
	/**
	 * Builder Content Class list
	 *
	 * @param array $classes classes.
	 * @return array
	 */
	public static function builder_content_parent_class( $classes ) {
		if ( Fns::is_store_page_builder() ) {
			$classes[] = 'store-content-wrap';
		}
		return array_unique( $classes );
	}

}
