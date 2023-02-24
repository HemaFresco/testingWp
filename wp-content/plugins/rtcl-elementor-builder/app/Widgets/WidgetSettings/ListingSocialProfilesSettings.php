<?php
/**
 * Main Elementor ListingSocialProfilesSettings Class
 *
 * ListingSocialProfilesSettings main class
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
 * ListingSocialProfilesSettings class
 */
class ListingSocialProfilesSettings extends ElementorSingleListingBase {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		return array_merge(
			$this->general_fields(),
			$this->content_visibility()
		);
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
				'label' => __( 'General', 'rtcl-elementor-builder' ),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_hide_label',
				'label'     => __( 'Show Label?', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
			),
			array(
				'type'      => Controls_Manager::TEXT,
				'id'        => 'label_text',
				'label'     => __( 'Label Text', 'rtcl-elementor-builder' ),
				'default'   => __( 'Social Profiles:', 'rtcl-elementor-builder' ),
				'condition' => array(
					'rtcl_hide_label' => 'yes',
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
	public function content_visibility() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_content_visibility',
				'label' => __( 'Icon Visibility ', 'rtcl-elementor-builder' ),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_hide_facebook',
				'label'     => __( 'Show Facebook?', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_hide_twitter',
				'label'     => __( 'Show Twitter?', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_hide_youtube',
				'label'     => __( 'Show Youtube?', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_hide_instagram',
				'label'     => __( 'Show Instagram?', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_hide_linkedIn',
				'label'     => __( 'Show LinkedIn?', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_hide_pinterest',
				'label'     => __( 'Show Pinterest?', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_hide_reddit',
				'label'     => __( 'Show Reddit?', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
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
		return $this->icon_and_text_control();
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function icon_and_text_control() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'icon_style',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Style', 'rtcl-elementor-builder' ),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_label_typo',
				'label'    => __( 'Label Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon.social-profile .rtcl-social-profile-label',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_color',
				'label'     => __( 'Icon Color', 'rtcl-elementor-builder' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-social-profile-wrap .rtcl-social-profiles a' => 'color: {{VALUE}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_icon_color_hover',
				'label'     => __( 'Icon Hover Color', 'rtcl-elementor-builder' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-social-profile-wrap .rtcl-social-profiles a:hover' => 'color: {{VALUE}};',
				),
			),
			array(
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'icon_size',
				'label'      => esc_html__( 'Icon Size', 'rtcl-elementor-builder' ),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-social-profile-wrap .rtcl-social-profiles a' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'icon_gap',
				'label'      => esc_html__( 'Icon Speacing', 'rtcl-elementor-builder' ),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-social-profile-wrap .rtcl-social-profiles' => 'column-gap: {{SIZE}}{{UNIT}};',
				),
			),
			[
				'type'    => Controls_Manager::CHOOSE,
				'mode'      => 'responsive',
				'id'      => 'icon_alignment',
				'label'   => __( 'Icon Alignment', 'rtcl-elementor-builder' ),
				'options' => $this->alignment_options(),
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.social-profile .rtcl-social-profile-wrap' => 'justify-content: {{VALUE}};',
				],
			],
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}


}

