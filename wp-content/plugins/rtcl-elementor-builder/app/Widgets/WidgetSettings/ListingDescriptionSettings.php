<?php
/**
 * Main Elementor ListingDescriptionSettings Class
 *
 * ListingDescriptionSettings main class
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
use Elementor\Group_Control_Typography;
use RtclElb\Abstracts\ElementorSingleListingBase;

/**
 * ListingDescriptionSettings class
 */
class ListingDescriptionSettings extends ElementorSingleListingBase {
	/**
	 * Set style controlls
	 */
	public function widget_general_fields(): array {
		return $this->general_fields();
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
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_drop_cap',
				'label'     => __('Drop Cap', 'rtcl-elementor-builder'),
				'label_on'  => __('On', 'rtcl-elementor-builder'),
				'label_off' => __('Off', 'rtcl-elementor-builder'),
				'default'   => '',
				'description'   => __('Switch to set drop cap', 'classified-listing'),
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
		$fields = array_merge(
			$this->zoom_text_style(),
			$this->drop_cap_style(),
		);

		return $fields;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function drop_cap_style() {
		$fields = [
			[
				'tab'       => Controls_Manager::TAB_STYLE,
				'mode'      => 'section_start',
				'id'        => 'drop-cap',
				'label'     => __('Drop Cap', 'rtcl-elementor-builder'),
				'condition' => ['rtcl_drop_cap' => 'yes'],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'drop_cap_typo',
				'label'    => __('Typography', 'classified-listing'),
				'selector' => '{{WRAPPER}} .el-single-addon.enabled-drop-cap p:first-child:first-letter',
				'description'   => __('Switch to set drop cap', 'classified-listing'),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'drop_cap_color',
				'label'     => __('Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.enabled-drop-cap p:first-child:first-letter'   => 'color: {{VALUE}}',
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
	public function zoom_text_style() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'description',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __('Description', 'rtcl-elementor-builder'),
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_description_typo',
				'label'    => __('Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .rtcl-listing-description.el-single-addon p',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'description_color',
				'label'     => __('Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .rtcl-listing-description'   => 'color: {{VALUE}}',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'speace_pragraph',
				'label'      => esc_html__('Paragraph Speace', 'rtcl-elementor-builder'),
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-description p'            => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rtcl-listing-description p:last-child' => 'margin-bottom: 0;',
				],
			],
			[
				'type'      => Controls_Manager::CHOOSE,
				'mode'      => 'responsive',
				'id'        => 'text_alignment',
				'label'     => __('Text Alignment', 'rtcl-elementor-builder'),
				'options'   => array_merge($this->alignment_options(), [
					'justify' => [
						'title' => esc_html__('Justified', 'rtcl-elementor-builder'),
						'icon'  => 'eicon-text-align-justify',
					],
				]),
				'default'   => 'left',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.rtcl-listing-description, {{WRAPPER}} .el-single-addon.rtcl-listing-description.enabled-drop-cap p:first-child:first-letter' => 'text-align: {{VALUE}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}
}
