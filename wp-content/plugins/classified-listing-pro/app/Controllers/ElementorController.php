<?php
/**
 * Main Elementor ElementorMainController Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 * @since    1.0.0
 */

namespace RtclPro\Controllers;

use RtclPro\Controllers\Elementor\Hooks\ELFilterHooksPro;
use RtclPro\Controllers\Elementor\Widgets;

/**
 * Main Elementor ElementorMainController Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
class ElementorController {

	/**
	 * Initialize all hooks function
	 *
	 * @return void
	 */
	public static function init() {
		
		add_filter( 'rtcl_el_widget_for_classified_listing', array( __CLASS__, 'el_widget_for_classified_listing' ), 10 );
		
		ELFilterHooksPro::init();
	}
	/**
	 * Undocumented function
	 *
	 * @param [type] $class_list main data.
	 *
	 * @return array
	 */
	public static function el_widget_for_classified_listing( $class_list ) {
		$el_classes = array(
			Widgets\ListingCategorySlider::class,
			Widgets\ListingSlider::class,
			Widgets\PricingTable::class
		);
		if (  class_exists( 'Rtcl\Abstracts\ElementorWidgetBaseV2' ) ){
			 $el_classes[] = Widgets\ListingSearchSortableForm::class;
		}
		return array_merge(
			$class_list,
			$el_classes
		);
	}

}
