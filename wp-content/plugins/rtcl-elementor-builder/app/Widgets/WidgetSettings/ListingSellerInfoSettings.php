<?php
/**
 * Main Elementor ListingSellerInfoSettings Class
 *
 * ListingSellerInfoSettings main class
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
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use RtclElb\Abstracts\ElementorSingleListingBase;

/**
 * ListingSellerInfoSettings class
 */
class ListingSellerInfoSettings extends ElementorSingleListingBase {
	/**
	 * Set style controlls
	 */
	public function widget_general_fields(): array {
		return $this->general_fields();
	}

	/**
	 * Set style controlls
	 */
	public function widget_style_fields(): array {
		return array_merge(
			$this->text_style(),
			$this->button_style()
		);
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function general_fields() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'general',
				'label' => __('General', 'rtcl-elementor-builder'),
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_show_author',
				'label'         => __('Show Author', 'rtcl-elementor-builder'),
				'label_on'      => __('Show', 'rtcl-elementor-builder'),
				'label_off'     => __('Hide', 'rtcl-elementor-builder'),
				'default'       => 'yes',
				'description'   => __('Switch to Show Author', 'classified-listing'),
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_show_author_image',
				'label'         => __('Show Author Image', 'rtcl-elementor-builder'),
				'label_on'      => __('Show', 'rtcl-elementor-builder'),
				'label_off'     => __('Hide', 'rtcl-elementor-builder'),
				'default'       => 'yes',
				'condition'     => ['rtcl_show_author' => ['yes']],
				'description'   => __('Switch to Show Author Image', 'classified-listing'),
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_show_location',
				'label'         => __('Show Location', 'rtcl-elementor-builder'),
				'label_on'      => __('Show', 'rtcl-elementor-builder'),
				'label_off'     => __('Hide', 'rtcl-elementor-builder'),
				'default'       => 'yes',
				'description'   => __('Switch to Show Location', 'classified-listing'),
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_show_contact',
				'label'         => __('Show Contact Number', 'rtcl-elementor-builder'),
				'label_on'      => __('Show', 'rtcl-elementor-builder'),
				'label_off'     => __('Hide', 'rtcl-elementor-builder'),
				'default'       => 'yes',
				'description'   => __('Switch to Show Contact Number', 'classified-listing'),
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_show_contact_form',
				'label'         => __('Show Contact Form', 'rtcl-elementor-builder'),
				'label_on'      => __('Show', 'rtcl-elementor-builder'),
				'label_off'     => __('Hide', 'rtcl-elementor-builder'),
				'default'       => 'yes',
				'description'   => __('Switch to Show Contact Form', 'classified-listing'),
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_show_seller_website',
				'label'         => __('Show Seller Website', 'rtcl-elementor-builder'),
				'label_on'      => __('Show', 'rtcl-elementor-builder'),
				'label_off'     => __('Hide', 'rtcl-elementor-builder'),
				'default'       => 'yes',
				'description'   => __('Switch to Show Seller Website', 'classified-listing'),
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
				'id'    => 'description',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __('Style', 'rtcl-elementor-builder'),
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_label_typo',
				'label'    => __('Label Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .el-single-addon.seller-information .list-group-item .media-body span,{{WRAPPER}} .el-single-addon .list-group-item .rtcl-icon',
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_value_typo',
				'label'    => __('Data Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .el-single-addon.seller-information .list-group-item .media-body div',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_label_color',
				'label'     => __('Label Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .media-body span,{{WRAPPER}} .el-single-addon .list-group-item .rtcl-icon' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_value_color',
				'label'     => __('Data Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .media-body div' => 'color: {{VALUE}};',
				],
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => __('Border', 'rtcl-elementor-builder'),
				'mode'           => 'group',
				'id'             => 'rtcl_listing_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => 'rgba(0, 0, 0, 0.125)',
					],
				],
				'selector'       => '{{WRAPPER}} .el-single-addon .list-group-item:not(.rtcl-website)',
			],
			[
				'mode'       => 'responsive',
				'label'      => __('Padding', 'rtcl-elementor-builder'),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_field_padding',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .list-group-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
	public function button_style() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'button_section',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __('Button', 'rtcl-elementor-builder'),
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_button_typo',
				'label'    => __('Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .el-single-addon.seller-information .list-group-item .btn,{{WRAPPER}} .el-single-addon.seller-information .list-group-item button',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_color',
				'label'     => __('Text Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .btn,{{WRAPPER}} .el-single-addon.seller-information .list-group-item button' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_color_hover',
				'label'     => __('Hover Text Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .btn:hover,{{WRAPPER}} .el-single-addon.seller-information .list-group-item button:hover' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_bg_color',
				'label'     => __('Background', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .btn,{{WRAPPER}} .el-single-addon.seller-information .list-group-item button' => 'background: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_bg_hover_color',
				'label'     => __('Hover Background', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .btn:hover,{{WRAPPER}} .el-single-addon.seller-information .list-group-item button:hover' => 'background: {{VALUE}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __('Button Padding', 'rtcl-elementor-builder'),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_button_padding',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .btn,{{WRAPPER}} .el-single-addon.seller-information .list-group-item button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}
}
