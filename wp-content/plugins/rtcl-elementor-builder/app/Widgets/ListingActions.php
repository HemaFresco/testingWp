<?php
/**
 * Main Elementor ListingActions Class
 *
 * ListingActions main class
 *
 * @author  RadiusTheme
 * @since   2.0.10
 * @package  RTCL_Elementor_Builder
 * @version 1.2
 */

namespace RtclElb\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Rtcl\Helpers\Functions;
use RtclElb\Widgets\WidgetSettings\ListingActionsSettings;
use RtclElb\Traits\ELTempleateBuilderTraits;

/**
 * ListingActions class
 */
class ListingActions extends ListingActionsSettings {

	/**
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;
	/**
	 * Construct function
	 *
	 * @param array  $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Actions & Social Share', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rt-listing-actions-social';
		parent::__construct( $data, $args );
	}
	/**
	 * Enable listing details
	 *
	 * @param bool   $is_listing_details display true or false.
	 * @param object $post current post object.
	 * @return bool
	 */
	public function social_for_listing_details( $is_listing_details, $post ) {
		if ( self::$post_type_tb === $post->post_type ) {
			$is_listing_details = true;
		}
		return $is_listing_details;
	}
	/**
	 * Return actions list.
	 *
	 * @param [array] $the_actions the actions list.
	 * @return array
	 */
	public function the_listing_actions( $the_actions ) {
		$settings = $this->get_settings();
		if ( ! $settings['rtcl_show_social_share'] ) {
			$the_actions['social'] = false;
		} else {
			$the_actions['social'] = $the_actions['social'];
		}
		$the_actions['can_add_favourites'] = boolval( $settings['rtcl_show_favourites'] );
		$the_actions['can_report_abuse']   = boolval( $settings['rtcl_report_abuse'] );
		return $the_actions;
	}
	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		add_filter( 'rtcl_listing_is_social_share_for_single', [ $this, 'social_for_listing_details' ], 10, 2 );
		add_filter( 'rtcl_listing_the_actions', [ $this, 'the_listing_actions' ] );
		$settings       = $this->get_settings();
		$template_style = 'single/actions';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_listing_page_actions_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
		remove_filter( 'rtcl_listing_the_actions', [ $this, 'the_listing_actions' ] );
	}

}

