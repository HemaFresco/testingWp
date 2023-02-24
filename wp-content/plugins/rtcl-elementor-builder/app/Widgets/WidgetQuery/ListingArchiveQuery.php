<?php

/**

 * Main Elementor ListingArchiveQuery Class

 *

 * ListingArchiveQuery main class

 *

 * @author  RadiusTheme

 * @since   2.0.10

 * @package  RTCL_Elementor_Builder

 * @version 1.2

 */



namespace RtclElb\Widgets\WidgetQuery;



// TODO:: Need Stor Archive.

// TODO:: Others template need add.

// TODO:: Settings Shuould Check.



if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly.

}



use Rtcl\Helpers\Functions;

use Rtcl\Helpers\Pagination;

use Rtcl\Resources\Options;

use Rtcl\Traits\Addons\TopQueryTrait;

use WP_Query;

use RtclElb\Widgets\WidgetSettings\ListingArchiveSettings;



/**

 * ListingArchiveQuery class

 */

class ListingArchiveQuery extends ListingArchiveSettings {

	/**

	 * Top query related function.

	 */

	use TopQueryTrait;

	/**

	 * Posts per page for archive function

	 *

	 * @return number

	 */

	public function posts_per_page() {

		$listings_per_page = Functions::get_option_item( 'rtcl_general_settings', 'listings_per_page' );

	
		return apply_filters( 'rtcl_loop_listing_per_page', $listings_per_page );

	}

	/**

	 * Query Orderby.

	 *

	 * @return array

	 */

	public function widget_query_orderby() {

		$orderby = Functions::get_option_item( 'rtcl_general_settings', 'orderby' );

		$order   = Functions::get_option_item( 'rtcl_general_settings', 'order' );

		if ( isset( $_GET['orderby'] ) ) { // phpcs:ignore 

			$orderby = sanitize_key( $_GET['orderby'] ); // phpcs:ignore

		}

		$the_args = array();

		if ( ! empty( $order ) && ! empty( $orderby ) ) {

			switch ( $orderby ) {

				case 'title-asc':

					$the_args['orderby'] = 'title';

					$the_args['order']   = 'asc';

					break;

				case 'title-desc':

					$the_args['orderby'] = 'title';

					$the_args['order']   = 'desc';

					break;

				case 'date-desc':

					$the_args['orderby'] = 'date';

					$the_args['order']   = 'desc';

					break;

				case 'date-asc':

					$the_args['orderby'] = 'date';

					$the_args['order']   = 'asc';

					break;

				case 'views-desc':

					$the_args['meta_key'] = '_views';

					$the_args['orderby']  = 'meta_value_num';

					$the_args['order']    = 'desc';

					break;

				case 'views-asc':

					$the_args['meta_key'] = '_views';

					$the_args['orderby']  = 'meta_value_num';

					$the_args['order']    = 'asc';

					break;

				case 'price-asc':

					$the_args['meta_key'] = 'price';

					$the_args['orderby']  = 'meta_value_num';

					$the_args['order']    = 'asc';

					break;

				case 'price-desc':

					$the_args['meta_key'] = 'price';

					$the_args['orderby']  = 'meta_value_num';

					$the_args['order']    = 'desc';

					break;

				case 'price':

					$the_args['meta_key'] = $orderby;

					$the_args['orderby']  = 'meta_value_num';

					$the_args['order']    = $order;

					break;

				case 'views':

					$the_args['meta_key'] = '_views';

					$the_args['orderby']  = 'meta_value_num';

					$the_args['order']    = $order;

					break;

				case 'rand':

					$the_args['orderby'] = $orderby;

					break;

				default:

					$the_args['orderby'] = $orderby;

					$the_args['order']   = $order;

			}

		}

		return $the_args;

	}



	/**

	 * Filter related query function

	 *

	 * @return array

	 */

	public function widget_filter_search_query() {

		$the_args     = array();

		$meta_queries = array();

		$filters      = isset( $_GET['filters'] ) ? (array) $_GET['filters'] : array(); // phpcs:ignore

		if ( isset( $filters['ad_type'] ) && ! empty( $filters['ad_type'] ) ) {

			$meta_queries[] = array(

				'key'     => 'ad_type',

				'value'   => sanitize_key( $filters['ad_type'] ),

				'compare' => '=',

			);

		}

		if ( isset( $filters['price']['min'] ) && ! empty( $filters['price']['min'] ) ) {

			$meta_queries[] = array(

				'key'     => 'price',

				'value'   => absint( $filters['price']['min'] ),

				'type'    => 'NUMERIC',

				'compare' => '>=',

			);

		}

		if ( isset( $filters['price']['max'] ) && ! empty( $filters['price']['max'] ) ) {

			$meta_queries[] = array(

				'key'     => 'price',

				'value'   => absint( $filters['price']['max'] ),

				'type'    => 'NUMERIC',

				'compare' => '<=',

			);

		}



		if (!empty($filters['rating'])) {

			$rating       = (float) $filters['rating'];

			// error_log( print_r( $rating , true ), 3, __DIR__ . '/log.txt' );

			$meta_queries[] = [

				'key'     => '_rtcl_average_rating',

				'value'   => $rating,

				'compare' => '>=',

			];

		}

		/** Added CodeHR **/
		$cf = array_filter($filters);
		if (!empty($cf)) {
			$cf_meta_query = [];
			foreach ($cf as $key => $values) {
				$field_id = absint(str_replace("_field_", '', $key));
				$field = rtcl()->factory->get_custom_field($field_id);
				
				if ($field) {
					if (is_array($values)) {
						if ($field->getType() === 'number') {
							$values = array_filter($values);
							if ($n = count($values)) {
								if (2 == $n) {
									$cf_meta_query[] = array(
										'key'     => $key,
										'value'   => array_map('intval', array_values($values)),
										'type'    => 'NUMERIC',
										'compare' => 'BETWEEN'
									);
								} else {
									if (empty($values['min'])) {
										$cf_meta_query[] = array(
											'key'     => $key,
											'value'   => (int)$values['max'],
											'type'    => 'NUMERIC',
											'compare' => '<='
										);
									} else {
										$cf_meta_query[] = array(
											'key'     => $key,
											'value'   => (int)$values['min'],
											'type'    => 'NUMERIC',
											'compare' => '>='
										);
									}
								}

							}
						} else if (in_array($field->getType(), array('checkbox', 'select', 'radio'))) {
							if (count($values) > 1) {

								$sub_meta_queries = array();

								foreach ($values as $value) {
									$sub_meta_queries[] = array(
										'key'     => $key,
										'value'   => sanitize_text_field($value),
										'compare' => 'LIKE'
									);
								}

								$meta_query_relation = array_merge(array('relation' => 'AND'), $sub_meta_queries);

								$cf_meta_query[] = apply_filters('rtcl_cf_sub_meta_queries', $meta_query_relation, $field);

							} else {
								$cf_meta_query[] = array(
									'key'     => $key,
									'value'   => sanitize_text_field($values[0]),
									'compare' => 'LIKE'
								);
							}
						}
					} else {
						if ($field->getType() === 'date') {
							$date_type = $field->getDateType();
							$search_type = $field->getDateSearchableType();
							$type = $date_type == 'date_time' || $date_type == 'date_time_range' ? 'DATETIME' : 'DATE';
							if ($date_type == 'date' || $date_type == 'date_time') {
								$meta_key = $field->getMetaKey();

								if ($search_type == 'single') {
									$cf_meta_query[] = array(
										'key'     => $meta_key,
										'value'   => $field->sanitize_date_field($values, ['range' => false]),
										'compare' => '=',
										'type'    => $type
									);
								} else {
									$dates = $field->sanitize_date_field($values, ['range' => true]);
									$start_date = $dates['start'];
									$end_date = $dates['end'];
									$cf_meta_query[] = array(
										'key'     => $meta_key,
										'value'   => array($start_date, $end_date),
										'compare' => 'BETWEEN',
										'type'    => $type
									);
								}

							} else if ($date_type == 'date_range' || $date_type == 'date_range_time') {
								$start_meta_key = $field->getDateRangeMetaKey('start');
								$end_meta_key = $field->getDateRangeMetaKey('end');

								if ($search_type == 'single') {
									$start_date = $end_date = $field->sanitize_date_field($values, ['range' => false]);
								} else {
									$dates = $field->sanitize_date_field($values, ['range' => true]);
									$start_date = $dates['start'];
									$end_date = $dates['end'];
								}
								if ($start_date) {
									$cf_meta_query[] = array(
										'key'     => $start_meta_key,
										'value'   => $start_date,
										'compare' => $search_type == 'single' ? '<=' : '>=',
										'type'    => $type
									);
								}
								if ($end_date) {
									$cf_meta_query[] = array(
										'key'     => $end_meta_key,
										'value'   => $end_date,
										'compare' => $search_type == 'single' ? '>=' : '<=',
										'type'    => $type
									);
								}
							}

						} else {
							$operator = (in_array($field->getType(), array(
								'text',
								'textarea',
								'url'
							))) ? 'LIKE' : '=';
							$cf_meta_query[] = array(
								'key'     => $key,
								'value'   => sanitize_text_field($values),
								'compare' => $operator
							);
						}
					}
				}
			}

			$cf_meta_query = apply_filters('rtcl_listing_custom_fields_meta_query', $cf_meta_query);
				
			$meta_queries = array_merge( $meta_queries, $cf_meta_query );
		}
		/** END **/

		

		$count_meta_queries = count( $meta_queries );


		if ( $count_meta_queries ) {

			$the_args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ),   $meta_queries ) : $meta_queries; // phpcs:ignore

		}

		if ( isset( $_GET['q'] ) ) { // phpcs:ignore

			$keyword       = (string) Functions::clean( wp_unslash( $_GET['q'] ) ); // phpcs:ignore

			$the_args['s'] = $keyword;

		}

		//echo '<pre>'; print_r($the_args);

		return $the_args;



	}



	/**

	 * Meta query.

	 *

	 * @return array

	 */

	public function widget_tax_query() {

		$the_args = array();

		if ( Functions::is_listing_taxonomy() ) {

			$queried_object = get_queried_object();

			$queried_tax    = '';

			if ( $queried_object && isset( $queried_object->taxonomy ) ) {

				$queried_tax = $queried_object->taxonomy;

			}

			switch ( $queried_tax ) {

				case 'rtcl_location':

					$location = $queried_object->slug;

					$category = get_query_var( 'rtcl_category' );

					break;

				case 'rtcl_category':

					$category = $queried_object->slug;

					$location = get_query_var( 'rtcl_location' );

					break;

				default:

					$category = get_query_var( 'rtcl_category' );

					$location = get_query_var( 'rtcl_location' );

					break;

			}

			if ( $category ) {

				$the_args['tax_query'][] = array(

					'taxonomy' => rtcl()->category,

					'terms'    => $category,

					'field'    => 'slug',

					'operator' => 'IN',

				);

			}

			if ( $location ) {

				$the_args['tax_query'][] = array(

					'taxonomy' => rtcl()->location,

					'terms'    => $location,

					'field'    => 'slug',

					'operator' => 'IN',

				);

			}

		}

		return $the_args;

	}







	/**

	 * Main query argument.

	 *

	 * @param array $more_arg extra argument.

	 * @return array

	 */

	public function widget_query_args( $more_arg = array() ) {



		$the_args          = array(

			'post_type'      => rtcl()->post_type,

			'posts_per_page' => $this->posts_per_page(),

			'post_status'    => 'publish',

		);

		$the_args['paged'] = Pagination::get_page_number();

		$the_args          = array_merge(

			$the_args,

			$this->widget_query_orderby(),

			$this->widget_tax_query(),

			$this->widget_filter_search_query(),

			$more_arg

		);



		return $the_args;

	}





	/**

	 * Undocumented function

	 *

	 * @param [array] $query main query.

	 * @return mixed

	 */

	public function add_geo_query( $query ) {

		$distance       = ! empty( $_GET['distance'] ) ? absint( $_GET['distance'] ) : 0; // phpcs:ignore

		$rtcl_geo_query = $query->get( 'rtcl_geo_query', array() );



		if ( $distance ) {

			$current_user_id = get_current_user_id();

			$lat             = ! empty( $_GET['center_lat'] ) ? trim( wp_unslash( $_GET['center_lat'] ) ) : get_user_meta( $current_user_id, '_rtcl_latitude', true ); // phpcs:ignore

			$lan             = ! empty( $_GET['center_lng'] ) ? trim( wp_unslash( $_GET['center_lng'] ) ) : get_user_meta( $current_user_id, '_rtcl_longitude', true ); // phpcs:ignore



			if ( $lat && $lan ) {

				$rs_data        = Options::radius_search_options();

				$rtcl_geo_query = array(

					'lat_field' => 'latitude',

					'lng_field' => 'longitude',

					'latitude'  => $lat,

					'longitude' => $lan,

					'distance'  => $distance,

					'units'     => $rs_data['units'],

				);

			}

		}

		$query->set( 'rtcl_geo_query', $rtcl_geo_query );

	}



	/**

	 * Widget result.

	 *

	 * @return array

	 */

	public function widget_results() {

		$top_listing = $this->top_listing_query_prepared();

		$more_arg    = array(

			'post__not_in' => $top_listing['top_items'],

		);

		$args        = $this->widget_query_args( $more_arg );

		add_action( 'pre_get_posts', array( $this, 'add_geo_query' ) );

		$loop_obj = new WP_Query( $args );

		remove_action( 'pre_get_posts', array( $this, 'add_geo_query' ) );

		wp_reset_postdata();

		return array(

			'loop_obj'      => $loop_obj,

			'top_query_obj' => $top_listing['top_query'],

		);

	}





}



