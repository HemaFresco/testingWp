<?php
/**
 * TemplateBuilderFrontend Class for Elementor builder
 *
 * TemplateBuilderFrontend Class.
 *
 * @package  RTCL_Elementor_Builder
 * @since    2.0.10
 */

namespace RtclElb\Controllers\Builder;

use RtclElb\Traits\ELTempleateBuilderTraits;

/**
 * TemplateBuilderFrontend Class
 */
class TemplateBuilderFrontend {

	/**
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;

	/**
	 * Initialize function.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'template_include', [ __CLASS__, 'el_template_loader_default_file' ], 99 );
		add_action( 'el_builder_template_content', [ __CLASS__, 'display_template_content' ] , -1 );
	}

	/**
	 * Template Overider
	 *
	 * @param string $default_file file name.
	 * @return string
	 */
	public static function el_template_loader_default_file( $default_file ) {
		if ( self::is_builder_page_single() || self::is_builder_page_archive() || self::is_store_page_builder() ) {
			$default_file = rtclElb()->plugin_path() . '/templates/elementor/listing-fullwidth.php';
		}
		return $default_file;
	}

	/**
	 * Builder content display.
	 *
	 * @return mixed
	 */
	public static function display_template_content() {
		$builder_id = false;
		if ( self::is_builder_page_single() ) {
			$builder_id = self::builder_page_id( 'single' );
		} elseif ( self::is_builder_page_archive() ) {
			$builder_id = self::builder_page_id( 'archive' );
		} elseif ( self::is_store_page_builder() ) {
			$builder_id = self::builder_page_id( 'store-single' );
		}

		if ( $builder_id ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo self::get_builder_content( $builder_id );
		}
	}

}
