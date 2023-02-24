<?php
/**
 * Main Elementor ListingTitleSettings Class
 *
 * ListingTitleSettings main class
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
use \Elementor\Group_Control_Typography;

/**
 * ListingTitleSettings class
 */
class ListingTitleSettings extends ElementorSingleListingBase {

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
	public function widget_style_fields(): array {
		return $this->title_fields();
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function general_fields(): array {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_content_visibility',
				'label' => __( 'Title Settings', 'rtcl-elementor-builder' ),
			],
			[
				'type'    => Controls_Manager::SELECT,
				'id'      => 'header_size',
				'label'   => __( 'HTML Tag', 'rtcl-elementor-builder' ),
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
				'default' => 'h2',
			],
			[
				'type'    => Controls_Manager::CHOOSE,
				'mode'      => 'responsive',
				'id'      => 'title_alignment',
				'label'   => __( 'Text Alignment', 'rtcl-elementor-builder' ),
				'options' => $this->alignment_options(),
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-listings-header-title' => 'text-align: {{VALUE}};',
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
	 *
	 * @return array
	 */
	public function title_fields() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'sec_style_type',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Title', 'rtcl-elementor-builder' ),
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Spacing', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_title_spacing',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.listing-title .rtcl-listings-header-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon.listing-title .rtcl-listings-header-title',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'title_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.listing-title .rtcl-listings-header-title'   => 'color: {{VALUE}}',
				],
			],
			[
				'mode' => 'section_end',
			],
		];
		return $fields;
	}

}

