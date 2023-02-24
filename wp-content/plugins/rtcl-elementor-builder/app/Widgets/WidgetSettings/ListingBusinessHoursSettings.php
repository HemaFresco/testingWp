<?php
/**
 * Main Elementor ListingBusinessHoursSettings Class
 *
 * ListingBusinessHoursSettings main class
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
 * ListingBusinessHoursSettings class
 */
class ListingBusinessHoursSettings extends ElementorSingleListingBase {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		// $fields = array_merge(
		// $this->general_fields()
		// );
		return $this->general_fields();
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		// $fields = array_merge(
		// $this->text_style(),
		// );
		return $this->text_style();
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
				'label' => __( 'General', 'rtcl-elementor-builder' ),
			],
			[
				'type'    => Controls_Manager::SELECT,
				'id'      => 'date_formate',
				'label'   => __( 'Date Formate', 'rtcl-elementor-builder' ),
				'options' => [
					'12' => esc_html__( '12 Hours', 'rtcl-elementor-builder' ),
					'24' => esc_html__( '24 Hours', 'rtcl-elementor-builder' ),
				],
				'default' => '12',
				
			],
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_open_status',
				'label'     => __( 'Show Open status?', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'Yes', 'rtcl-elementor-builder' ),
				'label_off' => __( 'No', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
				'description'   => __('Switch to Show Open status', 'rtcl-elementor-builder'),
			),
			[
				'type'  => Controls_Manager::TEXT,
				'id'    => 'open_status_text',
				'label' => __( 'Open Status Text', 'rtcl-elementor-builder' ),
				'condition' => [ 'rtcl_show_open_status' => [ 'yes' ] ],
			],
			[
				'type'  => Controls_Manager::TEXT,
				'id'    => 'close_status_text',
				'label' => __( 'Close Status Text', 'rtcl-elementor-builder' ),
				'condition' => [ 'rtcl_show_open_status' => [ 'yes' ] ],
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
				'id'    => 'general_style',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'General Style', 'rtcl-elementor-builder' ),
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_open_status_typo_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Open Status', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_open_status_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon .rtclbh-block .rtclbh-status-open',
				'condition' => [ 'rtcl_show_open_status' => [ 'yes' ] ],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_open_status_color',
				'label'     => __( 'Open status text Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtclbh-status-open' => 'color: {{VALUE}};',
				],
				'condition' => [ 'rtcl_show_open_status' => [ 'yes' ] ],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_close_status_color',
				'label'     => __( 'Close status text Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtclbh-status-closed' => 'color: {{VALUE}};',
				],
				'condition' => [ 'rtcl_show_open_status' => [ 'yes' ] ],
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_close_status_typo_note',
				'separator'       => 'before',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Table', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_label_typo',
				'label'    => __( 'Label Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon.business-hours th',
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_value_typo',
				'label'    => __( 'Data Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon.business-hours td',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_label_color',
				'label'     => __( 'Label Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.business-hours th' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_value_color',
				'label'     => __( 'Data Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.business-hours td' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_border_color',
				'label'     => __( 'Border Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon th, {{WRAPPER}} .el-single-addon td' => 'border-color: {{VALUE}};',
				],

			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_field_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.business-hours th, {{WRAPPER}} .el-single-addon.business-hours td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];
		return $fields;
	}



}

