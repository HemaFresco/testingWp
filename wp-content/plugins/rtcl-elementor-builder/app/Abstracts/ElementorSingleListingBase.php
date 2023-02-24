<?php
/**
 * Main Elementor ElementorSingleListingBase Class
 *
 * ElementorSingleListingBase main class
 *
 * @author  RadiusTheme
 * @since   2.0.10
 * @package  RTCL_Elementor_Builder
 * @version 1.2
 */

namespace RtclElb\Abstracts;

use Rtcl\Abstracts\ElListingsWidgetBase;
use Rtcl\Traits\Addons\ListingItem;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ElementorSingleListingBase class
 */
abstract class ElementorSingleListingBase extends ElListingsWidgetBase {
	/**
	 * Prepared listing item
	 */
	use ListingItem;
	/**
	 * Widget Listings.
	 *
	 * @var object
	 */
	protected $listing;
	/**
	 * Undocumented function
	 *
	 * @param array $data default data.
	 * @param array $args default arg.
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->listing       = rtcl()->factory->get_listing( $this->listing_id() );
		$this->rtcl_category = 'rtcl-elementor-single-widgets'; // Category /@dev.
	}
	/**
	 * Set style controlls
	 *
	 * @return int
	 */
	public function listing_id(): int {
		$_id = ListingItem::get_prepared_listing_id();
		return absint( $_id );
	}

}
