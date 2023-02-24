<?php
/**
 * Traits Elementor builder
 *
 * The Elementor builder.
 *
 * @package  RTCL_Elementor_Builder
 * @since    2.0.10
 */

namespace RtclElb\Traits;

use RtclElb\Helpers\Fns;
use Rtcl\Helpers\Functions;

trait ELTempleateBuilderTraits {

	/**
	 * Elementor Templeate builder post type
	 *
	 * @var string
	 */
	public static $post_type_tb = 'rtcl_builder';
	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static $template_meta = 'rtcl_tb_template';

	/**
	 * Page builder id.
	 *
	 * @param [type] $type builder type.
	 * @return init
	 */
	public static function builder_page_id( $type ) {
		return get_option( self::option_name( $type ) );
	}
	/**
	 * Page builder Page for.
	 *
	 * @return array
	 */
	public static function builder_page_types() {
		$default = [
			'single'  => esc_html__( 'Single', 'rtcl-elementor-builder' ),
			'archive' => esc_html__( 'Archive', 'rtcl-elementor-builder' ),
		];
		if ( Fns::is_has_store() ) {
			$default['store-single'] = esc_html__( 'Store single', 'rtcl-elementor-builder' );
		}
		return $default;
	}
	/**
	 * Option name.
	 *
	 * @param [type] $type Builder type.
	 * @return string
	 */
	public static function option_name( $type ) {
		return self::$template_meta . '_default_' . $type;
	}

	/**
	 * Get builder type.
	 *
	 * @param [type] $post_id Post id.
	 * @return string
	 */
	public static function builder_type( $post_id ) {
		return get_post_meta( $post_id, self::template_type_meta_key(), true );
	}
	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static function is_builder_page_archive() {
		$builder_id   = self::builder_page_id( 'archive' );
		$builder_type = self::builder_type( $builder_id );
		$is_archive   = 'archive' === $builder_type; // Ticket issue.
		if ( 'archive' === self::builder_type( get_the_ID() ) || ( $is_archive && ( is_post_type_archive( rtcl()->post_type ) || Functions::is_listing_taxonomy() ) ) ) {
			return true;
		}
		return false;
	}
	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static function is_builder_page_single() {
		if ( self::is_single_page_builder( 'single', rtcl()->post_type ) ) {
			return true;
		}
		return false;
	}
	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static function is_store_page_builder() {
		if ( Fns::is_has_store() && self::is_single_page_builder( 'store-single', rtclStore()->post_type ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Single Page builder Detection
	 *
	 * @param [type] $type Builder type.
	 * @param [type] $singuler_post_type post type.
	 * @return boolean
	 */
	public static function is_single_page_builder( $type, $singuler_post_type ) {
		$builder_id   = self::builder_page_id( $type );
		$builder_type = self::builder_type( $builder_id );
		$is_single    = $type === $builder_type; // Ticket issue.
		if ( self::builder_type( get_the_ID() ) === $type || ( $is_single && is_singular( $singuler_post_type ) ) ) {
			return true;
		}
		return false;
	}
	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static function template_type_meta_key() {
		return self::$template_meta . '_type';
	}
	/**
	 * Get builder content function
	 *
	 * @param [type]  $template_id builder Template id.
	 * @param boolean $with_css with css.
	 * @return mixed
	 */
	public static function get_builder_content( $template_id, $with_css = false ) {
		return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, $with_css );
	}
}
