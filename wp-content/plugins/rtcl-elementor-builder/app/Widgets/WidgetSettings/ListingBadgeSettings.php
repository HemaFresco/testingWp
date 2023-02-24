<?php
/**
 * Main Elementor ListingBadgeSettings Class
 *
 * ListingBadgeSettings main class
 *
 * @author  RadiusTheme
 * @since   2.0.10
 * @package  RTCL_Elementor_Builder
 * @version 1.2
 */

namespace RtclElb\Widgets\WidgetSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use RtclElb\Abstracts\ElementorSingleListingBase;
use \Elementor\Group_Control_Typography;

/**
 * ListingBadgeSettings class
 */
class ListingBadgeSettings extends ElementorSingleListingBase {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		return $this->badge_visibility();
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		// $fields = array_merge(
		// 	$this->badge_style(),
		// );
		return $this->badge_style();
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function badge_style() {
		$badge_style = $this->widget_style_badge_section();
		$badge_style = $this->remove_controls(
			array(
				'rtcl_badge_sold_typo',
				'rtcl_el_badge_sold_note',
				'rtcl_badge_sold_out_bg_color',
				'rtcl_badge_sold_out_text_color',
			),
			$badge_style
		);
		$the_array = [
			[
				'id'    => 'rtcl_badge_section',
				'unset' => [
					'condition',
				],
			],
			
		];
		$modified = $this->modify_controls($the_array, $badge_style);

		return $modified;
	}

}

