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
class StoreOpeningHoursSettings extends SingleStoreWidgetBase {
	/**
	 * Set Query controlls
	 */
	public function widget_general_fields(): array {
		$fields = [];
		return $fields;
	}
	/**
	 * Set style controlls
	 */
	public function widget_style_fields(): array {
		// $fields = [];
		return $this->opening_hours_control();
	}

	/**
	 * Undocumented function.
	 *
	 * @return array
	 */
	public function opening_hours_control() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_opening_hours',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Opening Hours', 'rtcl-elementor-builder' ),
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Day\'s Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_field_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .store-hours-list .store-hour .hour-day, {{WRAPPER}} .store-hours-list .store-hour .oh-hours-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Day\'s Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_field_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .store-hours-list .store-hour' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'opening_hours_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .store-hours-list .store-hour',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'opening_hours_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .store-hours-list .store-hour' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'current_day_color',
				'label'     => __( 'Today\'s Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .store-hours-list .store-hour.current-store-hour' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'off_day_color',
				'label'     => __( 'Off Day Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .store-hours-list .store-hour .off-day' => 'color: {{VALUE}};',
				],
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'           => 'group',
				'id'             => 'opening_hours_settings',
				'fields_options' => [
					'border' => [
						'default' => '',
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
						'default' => '#fff',
					],
				],
				'selector'       => '{{WRAPPER}} .store-hours-list .store-hour',
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}


}
