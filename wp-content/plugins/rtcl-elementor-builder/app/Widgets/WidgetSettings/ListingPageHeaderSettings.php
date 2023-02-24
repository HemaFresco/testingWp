<?php
/**
 * Main Elementor ListingPageHeaderSettings Class
 *
 * ListingPageHeaderSettings main class
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

use Rtcl\Abstracts\ElementorWidgetBase;
use Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;

/**
 * ListingPageHeaderSettings class
 */
class ListingPageHeaderSettings extends ElementorWidgetBase {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		$fields = array_merge(
			$this->general_fields(),
		);
		return $fields;
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->title_fields(),
			$this->breadcrumb(),
		);
		return $fields;
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
				'label' => __( 'Header Settings', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_page_title',
				'label'       => __( 'Show Title', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Title. Default: On', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_breadcrumb',
				'label'       => __( 'Show Breadcrumb', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Breadcrumb. Default: On', 'rtcl-elementor-builder' ),
			],
			[
				'type'       => Controls_Manager::SELECT,
				'id'         => 'rtcl_header_style',
				'label'      => __( 'Breadcrumb Position', 'rtcl-elementor-builder' ),
				'options'    => [
					'style-1' => __( 'Bottom', 'rtcl-elementor-builder' ),
					'style-2' => __( 'Top', 'rtcl-elementor-builder' ),
					'style-3' => __( 'Right', 'rtcl-elementor-builder' ),
				],
				'default'    => 'style-1',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_show_breadcrumb',
									'operator' => '=',
									'value'    => 'yes',
								],
								[
									'name'     => 'rtcl_show_page_title',
									'operator' => '=',
									'value'    => 'yes',
								],
							],
						],
					],
				],
			],
			[
				'type'      => Controls_Manager::SELECT,
				'id'        => 'header_size',
				'label'     => __( 'Title HTML Tag', 'rtcl-elementor-builder' ),
				'options'   => [
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
				'default'   => 'h1',
				'condition' => [ 'rtcl_show_page_title' => [ 'yes' ] ],

			],
			[
				'type'      => Controls_Manager::CHOOSE,
				'id'        => 'title_alignment',
				'label'     => __( 'Title Alignment', 'rtcl-elementor-builder' ),
				'options'   => $this->alignment_options(),
				'default'   => 'left',
				'condition' => [
					'rtcl_show_page_title' => [ 'yes' ],
					'rtcl_header_style!'   => 'style-3',
				],

			],
			[
				'type'      => Controls_Manager::CHOOSE,
				'id'        => 'breadcrumb_alignment',
				'label'     => __( 'Breadcrumb Alignment', 'rtcl-elementor-builder' ),
				'options'   => $this->alignment_options(),
				'default'   => 'left',
				'condition' => [
					'rtcl_show_breadcrumb' => [ 'yes' ],
					'rtcl_header_style!'   => 'style-3',
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
				'mode'      => 'section_start',
				'id'        => 'sec_style_type',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Title', 'rtcl-elementor-builder' ),
				'condition' => [ 'rtcl_show_page_title' => [ 'yes' ] ],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Spacing', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_title_spacing',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-inner-wrapper .page-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .header-inner-wrapper .page-title',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'title_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .header-inner-wrapper .page-title'   => 'color: {{VALUE}}',
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
	public function breadcrumb() {
		$fields = [
			[
				'mode'      => 'section_start',
				'id'        => 'sec_style_color',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Breadcrumb', 'rtcl-elementor-builder' ),
				'condition' => [ 'rtcl_show_breadcrumb' => [ 'yes' ] ],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Spacing', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_breadcrumb_spacing',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-breadcrumb' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'breadcrumb_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .rtcl-breadcrumb',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'breadcrumb_color',
				'label'     => __( 'Text Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-breadcrumb' => 'color: {{VALUE}}',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'breadcrumb_link_color',
				'label'     => __( 'Link Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-breadcrumb a' => 'color: {{VALUE}}',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'link_hover_color',
				'label'     => __( 'Link Hover Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-breadcrumb a:hover' => 'color: {{VALUE}}',
				],
			],
			[
				'mode' => 'section_end',
			],
		];
		return $fields;
	}
}

