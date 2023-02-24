<?php
/**
 * Main Elementor ListingMapSettings Class
 *
 * ListingMapSettings main class
 *
 * @author  RadiusTheme
 *
 * @since   2.0.10
 *
 * @version 1.2
 */

namespace RtclElb\Widgets\WidgetSettings;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use RtclElb\Abstracts\ElementorSingleListingBase;

/**
 * ListingMapSettings class
 */
class ListingMapSettings extends ElementorSingleListingBase {
	/**
	 * Set style controlls
	 */
	public function widget_general_fields(): array {
		return $this->general_fields();
	}

	/**
	 * Set style controlls
	 */
	public function general_fields() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'gallery_general',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __('General Settings', 'rtcl-elementor-builder'),
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'zoom_label',
				'label'      => __('Zoom label', 'rtcl-elementor-builder'),
				'range'      => [
					'px' => [
						'min' => 8,
						'max' => 25,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 14,
				],
			],
			[
				'id'         => 'image_icon',
				'label' => esc_html__( 'Choose Map Icon', 'plugin-name' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

	/**
	 * Set style controlls
	 */
	public function widget_style_fields(): array {
		return [];
	}
}
