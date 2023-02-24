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

use RtclElb\Helpers\Fns;
use Rtcl\Abstracts\ElementorWidgetBase;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ElementorSingleListingBase class
 */
abstract class SingleStoreWidgetBase extends ElementorWidgetBase {

	/**
	 * Widget Listings.
	 *
	 * @var object
	 */
	protected $store;
	/**
	 * Undocumented function
	 *
	 * @param array $data default data.
	 * @param array $args default arg.
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->store         = rtclStore()->factory->get_store( Fns::last_store_id() );
		$this->rtcl_category = 'rtcl-elementor-store-single'; // Category /@dev.
	}

	/**
	 * Elementor Style Depands.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return [ 'rtcl-store-builder' ];
	}
	/**
	 * Elementor Script Depands.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return [ 'rtcl-store-public', 'rtcl-common' ];
	}


}
