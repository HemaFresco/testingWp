<?php
/**
 * @author  RadiusTheme
 *
 * @since   1.0
 *
 * @version 1.0
 */

namespace RtclElb\Widgets\WidgetSettings;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use RtclElb\Abstracts\SingleStoreWidgetBase;

/**
 * ListingStoreSettings Class.
 */
class StoreContactInfoSettings extends SingleStoreWidgetBase {
	/**
	 * Set Query controlls
	 */
	public function widget_general_fields(): array {
		$fields = array_merge(
			$this->content_visibility()
		);
		return $fields;
	}

	/**
	 * Set style controlls
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->store_info_control(),
			$this->button_style()
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
				'label' => __( 'Content Visibility ', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_store_status',
				'label'       => __( 'Show Status', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show/Hide Store Status', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_store_address',
				'label'       => __( 'Show Address', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show/Hide Store Address', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_store_phone',
				'label'       => __( 'Show Phone', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show/Hide Store Phone', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_store_social_media',
				'label'       => __( 'Show Social Media', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show/Hide Store Social Media', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_store_email',
				'label'       => __( 'Show Store Email', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show/Hide Store Social Media', 'rtcl-elementor-builder' ),
			],
			[
				'mode' => 'section_end',
			],

		];
		return $fields;
	}

	/**
	 * Undocumented function.
	 *
	 * @return array
	 */
	public function store_info_control() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_store_contact',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Contact', 'rtcl-elementor-builder' ),
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'contact-typography',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .store-information .store-info .store-info-item',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_info_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .store-information .store-info .store-info-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_info_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .store-information .store-info .store-info-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'id'              => 'social_icon_size',
				'mode'            => 'responsive',
				'label'           => esc_html__( 'Social Icon Width (px)', 'rtcl-elementor-builder' ),
				'type'            => Controls_Manager::SLIDER,
				'size_units'      => [ 'px' ],
				'range'           => [
					'px' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'devices'         => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 36,
					'unit' => 'px',
				],
				'tablet_default'  => [
					'size' => 36,
					'unit' => 'px',
				],
				'mobile_default'  => [
					'size' => 36,
					'unit' => 'px',
				],
				'selectors'       => [
					'{{WRAPPER}} .store-information .store-info .store-social-media .rtcl-icon' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'id'              => 'social_icon_font_size',
				'mode'            => 'responsive',
				'label'           => esc_html__( 'Social Icon font size (px)', 'rtcl-elementor-builder' ),
				'type'            => Controls_Manager::SLIDER,
				'size_units'      => [ 'px' ],
				'range'           => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'devices'         => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 14,
					'unit' => 'px',
				],
				'tablet_default'  => [
					'size' => 14,
					'unit' => 'px',
				],
				'mobile_default'  => [
					'size' => 14,
					'unit' => 'px',
				],
				'selectors'       => [
					'{{WRAPPER}} .store-information .store-info .store-social-media .rtcl-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'id'              => 'social_message_input',
				'mode'            => 'responsive',
				'label'           => esc_html__( 'Form Field Height (px)', 'rtcl-elementor-builder' ),
				'type'            => Controls_Manager::SLIDER,
				'size_units'      => [ 'px' ],
				'range'           => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'devices'         => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 40,
					'unit' => 'px',
				],
				'tablet_default'  => [
					'size' => 40,
					'unit' => 'px',
				],
				'mobile_default'  => [
					'size' => 40,
					'unit' => 'px',
				],
				'selectors'       => [
					'{{WRAPPER}} .store-information input.form-control' => 'height: {{SIZE}}{{UNIT}};',
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
				'label' => __( 'Button', 'rtcl-elementor-builder' ),
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_button_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} #store-email-area button.sc-submit',
			],
			
			[
				'mode'       => 'responsive',
				'label'      => __( 'Button Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_button_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #store-email-area button.sc-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			
			[
				'mode' => 'tabs_start',
				'id'   => 'button_start',
			],
			// Tab For normal view.
			[
				'mode'  => 'tab_start',
				'id'    => 'rtcl_button_normal',
				'label' => esc_html__( 'Normal', 'rtcl-elementor-builder' ),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_bg_color',
				'label'     => __( 'Background', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} #store-email-area button.sc-submit' => 'background: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_color',
				'label'     => __( 'Text Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} #store-email-area button.sc-submit' => 'color: {{VALUE}};',
				],
			],
			[
				'type'     => Group_Control_Border::get_type(),
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'id'       => 'message_button',
				'selector' => '{{WRAPPER}} #store-email-area button.sc-submit',
			],
			[
				'mode' => 'tab_end',
			],
			// Tab For Hover view.
			[
				'mode'  => 'tab_start',
				'id'    => 'rtcl_button_hover',
				'label' => esc_html__( 'Hover', 'rtcl-elementor-builder' ),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_bg_hover_color',
				'label'     => __( 'Hover Background', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} #store-email-area button.sc-submit:hover' => 'background: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_color_hover',
				'label'     => __( 'Hover Text Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} #store-email-area button.sc-submit:hover' => 'color: {{VALUE}};',
				],
			],
			[
				'type'     => Group_Control_Border::get_type(),
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'id'       => 'message_button_hover',
				'selector' => '{{WRAPPER}} #store-email-area button.sc-submit:hover',
			],
			[
				'mode' => 'tab_end',
			],
			[
				'mode' => 'tabs_end',
			],







			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

}
