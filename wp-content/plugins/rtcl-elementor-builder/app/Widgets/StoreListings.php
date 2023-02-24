<?php
/**
 * @author  RadiusTheme
 *
 * @since   1.0
 *
 * @version 1.0
 */

namespace RtclElb\Widgets;

use RtclElb\Helpers\Fns;
use Rtcl\Helpers\Functions;
use RtclElb\Widgets\WidgetSettings\StoreListingsSettings;
use Rtcl\Controllers\Elementor\Widgets\ListingItems;
/**
 * ListingStore class
 */
class StoreListings extends ListingItems {
	/**
	 * Widget Listings.
	 *
	 * @var object
	 */
	protected $store;
	/**
	 * ListingStore Init
	 *
	 * @param array  $data others data
	 * @param [type] $args Others args
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->rtcl_name     = __( 'Store Listings', 'rtcl-elementor-builder' );
		$this->rtcl_base     = 'rtcl-listing-store-listings';
		$this->rtcl_category = 'rtcl-elementor-store-single';
		$this->store         = rtclStore()->factory->get_store( Fns::last_store_id() );
	}
	/**
	 * Argument Setings.
	 *
	 * @return array
	 */
	public function widget_query_args() {
		$args           = parent::widget_query_args();
		$args['author'] = $this->store->owner_id();
		return $args;
	}

}
