<?php
/**
 * Main Elementor ListingVideoSettings Class
 *
 * ListingVideoSettings main class
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

use RtclElb\Abstracts\ElementorSingleListingBase;
use Elementor\Controls_Manager;
/**
 * ListingVideoSettings class
 */
class ListingVideoSettings extends ElementorSingleListingBase {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		return $this->general_fields();

	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function general_fields(): array {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_general_fields',
				'label' => __( 'Video', 'rtcl-elementor-builder' ),
			),
			array(
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'video_width',
				'label'      => esc_html__( 'Width', 'rtcl-elementor-builder' ),
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 200,
						'max' => 2000,
					),
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => '100',
				),
				'selectors'  => array(
					'{{WRAPPER}} .listing-video-widget.el-single-addon .rtcl-lightbox-iframe' => ' width: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'video_height',
				'label'      => esc_html__( 'Height', 'rtcl-elementor-builder' ),
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 200,
						'max' => 2000,
					),
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => '400',
				),
				'selectors'  => array(
					'{{WRAPPER}} .listing-video-widget.el-single-addon .rtcl-lightbox-iframe' => ' height: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		return array();
	}


}

