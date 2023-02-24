<?php
/**
 * Main Elementor ListingActionsSettings Class
 *
 * ListingActionsSettings main class
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
use \Elementor\{
	Controls_Manager,
	Group_Control_Border,
	Group_Control_Typography
};

/**
 * ListingActionsSettings class
 */
class ListingActionsSettings extends ElementorSingleListingBase {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		// $fields = array_merge(
		// $this->content_visibility()
		// );
		return $this->content_visibility();
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->text_style(),
			$this->social_style()
		);
		return $fields;
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function content_visibility() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_content_visibility',
				'label' => __( 'Content Visibility', 'rtcl-elementor-builder' ),
			],
			[
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_favourites',
				'label'     => __( 'Show Favourites', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'On', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Off', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
				'description'   => __('Switch to Show Favourites', 'classified-listing'),
			],
			[
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_report_abuse',
				'label'     => __( 'Show Report Abuse', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'On', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Off', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
				'description'   => __('Switch to Report Abuse', 'classified-listing'),
			],
			[
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_social_share',
				'label'     => __( 'Show Social Share', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'On', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Off', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
				'description'   => __('Switch to Show Social Share Button', 'classified-listing'),
			],
			[
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_inline_style',
				'label'     => __( 'Inline Style', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'On', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Off', 'rtcl-elementor-builder' ),
				'default'   => '',
				'description'   => __('Switch to inline view', 'classified-listing'),
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
	public function text_style() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'listing_action',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Action', 'rtcl-elementor-builder' ),
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_description_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon .list-group-item:not(.rtcl-sidebar-social)',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_text_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .list-group-item:not(.rtcl-sidebar-social) a' => 'color: {{VALUE}};',
				],
			],
			[
				'type'    => Controls_Manager::CHOOSE,
				'mode'      => 'responsive',
				'id'      => 'icon_alignment',
				'label'   => __( 'Alignment', 'rtcl-elementor-builder' ),
				'options' => $this->alignment_options(),
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .list-group.rtcl-single-listing-action' => 'text-align: {{VALUE}};justify-content:{{VALUE}}',
					'{{WRAPPER}} .el-single-addon .rtcl-single-listing-action .rtcl-sidebar-social' => 'justify-content: {{VALUE}};',
				],
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'           => 'group',
				'id'             => 'rtcl_listing_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '0',
							'right'    => '0',
							'bottom'   => '1',
							'left'     => '0',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => 'rgba(0, 0, 0, 0.125)',
					],
				],
				'selector'       => '{{WRAPPER}} .el-single-addon .list-group-item:not(.rtcl-sidebar-social)',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_field_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .list-group-item:not(.rtcl-sidebar-social)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
	public function social_style() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'social_style',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Social', 'rtcl-elementor-builder' ),
			],
			[
				'mode' => 'tabs_start',
				'id'   => 'promotion_tabs_start',
			],
			// Tab For normal view.
			[
				'mode'  => 'tab_start',
				'id'    => 'rtcl_promotion_normal',
				'label' => esc_html__( 'Normal', 'rtcl-elementor-builder' ),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-sidebar-social .rtcl-icon' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_bg_color',
				'label'     => __( 'Backgound Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-sidebar-social .rtcl-icon' => 'background: {{VALUE}};',
				],
			],
			[
				'mode' => 'tab_end',
			],
			// Tab For Hover view.
			[
				'mode'  => 'tab_start',
				'id'    => 'rtcl_promotion_hover',
				'label' => esc_html__( 'Hover', 'rtcl-elementor-builder' ),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_color_hover',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-sidebar-social a:hover .rtcl-icon' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_bg_color_hover',
				'label'     => __( 'Backgound Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-sidebar-social a:hover .rtcl-icon' => 'background: {{VALUE}};',
				],
			],
			[
				'mode' => 'tab_end',
			],
			[
				'mode' => 'tabs_end',
			],

			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_social_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-sidebar-social' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_social_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-sidebar-social' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'social_size',
				'label'      => esc_html__( 'Social Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-sidebar-social .rtcl-icon' => ' width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'icon_size',
				'label'      => esc_html__( 'Font Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-sidebar-social .rtcl-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'icon_gap',
				'label'      => esc_html__( 'Icon Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-sidebar-social' => 'gap: {{SIZE}}{{UNIT}};',
				],
			],

			[
				'mode' => 'section_end',
			],
		];
		return $fields;
	}


}

