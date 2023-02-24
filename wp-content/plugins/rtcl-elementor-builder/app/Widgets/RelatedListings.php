<?php
/**
 * Main Elementor RelatedListings Class.
 *
 * RelatedListings main class
 *
 * @author  RadiusTheme
 *
 * @since   2.0.10
 *
 * @version 1.2
 *
 * @var Rtcl\Models\Listing $listing
 */

namespace RtclElb\Widgets;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

use Rtcl\Controllers\Hooks\AppliedBothEndHooks;
use RtclElb\Widgets\WidgetSettings\RelatedListingsSettings;
use RtclStore\Helpers\Functions as StoreFunctions;

/**
 * RelatedListings class.
 */
class RelatedListings extends RelatedListingsSettings {

	/**
	 * Construct function.
	 *
	 * @param array $data some data
	 * @param [type] $args some arg
	 */
	public function __construct($data = [], $args = null) {
		$this->rtcl_name = __('Related Listings', 'rtcl-elementor-builder');
		$this->rtcl_base = 'rt-listing-related-listing';
		parent::__construct($data, $args);
	}

	/**
	 * Overwrite related listing data.
	 *
	 * @param [type] $data related listing data
	 *
	 * @return array
	 */
	public function related_listings_data($data) {
		$settings                          = $this->get_settings();
		$settings['rtcl_thumb_image_size'] = $this->image_size();
		$template_style                    = 'single/related-listing/related-listing';
		$data                              = array_merge(
			$data,
			[
				'template'              => $template_style,
				'instance'              => $settings,
				'view'                  => 'grid',
				'listing'               => $this->listing,
				'default_template_path' => rtclElb()->get_plugin_template_path(),
			]
		);

		return $data;
	}

	/**
	 * Overwrite related listing data.
	 *
	 * @param [type] $data related listing data
	 *
	 * @return array
	 */
	public function related_listing_query_arg($data) {
		$settings                = $this->get_settings();
		$listings_filter = !empty($settings['rtcl_listings_filter']) ? $settings['rtcl_listings_filter'] : ['category'];
		if( ! in_array( 'category', $listings_filter ) ){
			unset( $data['tax_query']  );
		}
		$related_post_per_page = 6 ;
		if( !empty($settings['rtcl_listings_per_page']) ){
			$related_post_per_page = $settings['rtcl_listings_per_page'];
		}
		$data['posts_per_page'] = $related_post_per_page;
		if ( in_array('author', $listings_filter)) {
			$store = false;
			$author_id = $this->listing->get_author_id();
			if (class_exists('RtclStore')) {
				$store = StoreFunctions::get_user_store( $author_id );
				if( $store ){
					$author_id = $store->owner_id();
				}
			}
			$data['author__in'] = $author_id;
		}
		if( in_array( 'location', $listings_filter ) ){
			$the_tax                       = wp_get_object_terms($this->listing->get_id(), rtcl()->location);
			$terms                         = !empty($the_tax) ? end($the_tax)->term_id : 0;
			$data['tax_query']['relation'] = 'AND';
			$data['tax_query'][]           = [
				[
					'taxonomy'         => rtcl()->location,
					'field'            => 'term_id',
					'terms'            => $terms,
				],
			];
		}
		
		if( in_array( 'listing_type', $listings_filter ) ){
			$data['meta_key']   = 'ad_type';
			$data['meta_value'] = $this->listing->get_ad_type();
		}
		
		return $data;
	}

	/**
	 * Widget excerpt_limit.
	 *
	 * @param array $length default limit
	 *
	 * @return init
	 */
	public function excerpt_limit($length) {
		$settings = $this->get_settings();
		$length   = !empty($settings['rtcl_content_limit']) ? $settings['rtcl_content_limit'] : $length;

		return $length;
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
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings = $this->get_settings();
		add_filter('excerpt_length', [$this, 'excerpt_limit']);

		if (empty($settings['rtcl_show_price_unit'])) {
			remove_filter('rtcl_price_meta_html', [AppliedBothEndHooks::class, 'add_price_unit_to_price'], 10, 3);
		}
		if (empty($settings['rtcl_show_price_type'])) {
			remove_filter('rtcl_price_meta_html', [AppliedBothEndHooks::class, 'add_price_type_to_price'], 20, 3);
		}
		add_filter('rtcl_loop_item_listable_fields', [$this, 'listable_fields_arg'], 10, 1);

		add_filter('rtcl_related_listing_query_arg', [$this, 'related_listing_query_arg']);
		add_filter('rtcl_related_listings_data', [$this, 'related_listings_data']);
        
		$this->listing->the_related_listings();

		if (empty($settings['rtcl_show_price_unit'])) {
			add_filter('rtcl_price_meta_html', [AppliedBothEndHooks::class, 'add_price_unit_to_price'], 10, 3);
		}
		if (empty($settings['rtcl_show_price_type'])) {
			add_filter('rtcl_price_meta_html', [AppliedBothEndHooks::class, 'add_price_type_to_price'], 20, 3);
		}
		$this->edit_mode_script();
	}
}
