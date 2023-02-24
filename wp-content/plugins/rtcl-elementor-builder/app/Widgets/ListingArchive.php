<?php

/**

 * Main Elementor ListingArchiveSettings Class

 *

 * ListingArchiveSettings main class

 *

 * @author  RadiusTheme

 * @since   2.0.10

 * @package  RTCL_Elementor_Builder

 * @version 1.2

 */



namespace RtclElb\Widgets;



// TODO:: Need Stor Archive.

// TODO:: Settings Shuould Check.



if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly.

}



use Rtcl\Helpers\Functions;

use Rtcl\Controllers\Hooks\AppliedBothEndHooks;

use Rtcl\Controllers\Hooks\TemplateHooks as RtclTemplateHooks;

use RtclElb\Widgets\WidgetQuery\ListingArchiveQuery;

use RtclPro\Controllers\Hooks\TemplateHooks as RtclProTemplateHooks;

/**

 * ListingArchive class

 */

class ListingArchive extends ListingArchiveQuery {

	/**

	 * Construct function

	 *

	 * @param array  $data Some data.

	 * @param [type] $args some arg.

	 */

	public function __construct( $data = [], $args = null ) {

		$this->rtcl_name = __( 'Archive Listing', 'rtcl-elementor-builder' );

		$this->rtcl_base = 'rt-archive-listing';

		parent::__construct( $data, $args );

	}



	/**

	 * Widget excerpt_limit.

	 *

	 * @param array $length default limit.

	 * @return init

	 */

	public function excerpt_limit( $length ) {

		$settings = $this->get_settings();

		$length   = ! empty( $settings['rtcl_content_limit'] ) ? $settings['rtcl_content_limit'] : $length;

		return $length;

	}

	/**

	 * Undocumented function

	 *

	 * @return array

	 */

	public function get_style_depends() {

		return [ 'rtcl-public', 'elementor-icons-shared-0', 'elementor-icons-fa-solid' ];

	}

	/**

	 * listable fields.

	 *

	 * @param [obj] $listing functionality.

	 * @return mixed

	 */

	public static function listable_fields_arg( $args ) {

		unset( $args['meta_query']);

		return $args;

	}

	/**

	 * Remove hooks

	 *

	 * @return void

	 */

	private function listings_hooks() {

		$settings = $this->get_settings();

		add_filter( 'excerpt_length', [ $this, 'excerpt_limit' ] );

		add_filter( 'excerpt_more', '__return_empty_string' );

		remove_action( 'rtcl_listing_badges', [ RtclTemplateHooks::class, 'listing_featured_badge' ], 20 );

		remove_action( 'rtcl_after_listing_loop', [ RtclTemplateHooks::class, 'pagination' ], 10 );

		if ( empty( $settings['rtcl_archive_result_count'] ) ) {

			remove_action( 'rtcl_listing_loop_action', [ RtclTemplateHooks::class, 'result_count' ], 10 );

		}

		if ( empty( $settings['rtcl_archive_catalog_ordering'] ) ) {

			remove_action( 'rtcl_listing_loop_action', [ RtclTemplateHooks::class, 'catalog_ordering' ], 20 );

		}

		if ( empty( $settings['rtcl_archive_view_switcher'] ) ) {

			remove_action( 'rtcl_listing_loop_action', [ RtclProTemplateHooks::class, 'view_switcher' ], 30 );

		}

		add_filter('rtcl_loop_item_listable_fields', [$this, 'listable_fields_arg'], 10, 1);



	}

	/**

	 * Display Output.

	 *

	 * @return mixed

	 */

	protected function render() {

		$this->listings_hooks();

		$settings = $this->get_settings();



		if ( ! $settings['rtcl_show_price_unit'] ) {

			remove_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_unit_to_price' ], 10, 3 );

		}

		if ( ! $settings['rtcl_show_price_type'] ) {

			remove_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_type_to_price' ], 20, 3 );

		}



		$the_query = $this->widget_results()['loop_obj'];

		
		$top_query = $this->top_listing_query_prepared()['top_query'];

		$view      = ! empty( $settings['rtcl_listings_view'] ) ? $settings['rtcl_listings_view'] : 'list';

		if ( isset( $_GET['view'] ) && $query_view = sanitize_key( $_GET['view'] ) ) { // phpcs:ignore

			if ( in_array( $query_view, array_keys( $this->listings_view() ), true ) ) {

				$view = $query_view;

			}

		}

		$style = 'style-1';

		if ( 'list' === $view ) {

			$style = $settings['rtcl_listings_style'] ? $settings['rtcl_listings_style'] : 'style-1';

			if ( ! in_array( $style, array_keys( $this->list_style() ) ) ) { // phpcs:ignore

				$style = 'style-1';

			}

		}

		if ( 'grid' === $view ) {

			$view  = 'grid';

			$style = $settings['rtcl_listings_grid_style'] ? $settings['rtcl_listings_grid_style'] : 'style-1';

			if ( ! in_array( $style, array_keys( $this->grid_style() ) ) ) { // phpcs:ignore

				$style = 'style-1';

			}

		}



		$settings['rtcl_thumb_image_size'] = $this->image_size();

		$template_style                    = 'listing-archive/listing-archive';

		$data                              = [

			'template'              => $template_style,

			'view'                  => $view,

			'style'                 => $style,

			'instance'              => $settings,

			'the_query'             => $the_query,

			'top_query'             => $top_query,

			'default_template_path' => rtclElb()->get_plugin_template_path(),

		];

		$data                              = apply_filters( 'rtcl_el_listing_archive_data', $data );

		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );



		if ( ! $settings['rtcl_show_price_unit'] ) {

			add_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_unit_to_price' ], 10, 3 );

		}

		if ( ! $settings['rtcl_show_price_type'] ) {

			add_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_type_to_price' ], 20, 3 );

		}



	}



}



