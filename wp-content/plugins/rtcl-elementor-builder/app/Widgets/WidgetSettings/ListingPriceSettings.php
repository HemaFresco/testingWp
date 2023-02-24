<?php
/**
 * Main Elementor ListingPriceSettings Class
 *
 * ListingPriceSettings main class
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
 * ListingPriceSettings class
 */
class ListingPriceSettings extends ElementorSingleListingBase {

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
		$fields = array_merge(
			$this->text_style(),
			$this->price_label_control()
		);
		return $fields;
	}
	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function general_fields() {

		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_general',
				'label' => __( 'General', 'rtcl-elementor-builder' ),
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_price_style',
				'label'   => __( 'Style', 'rtcl-elementor-builder' ),
				'options' => $this->price_style(),
				'default' => 'style-1',
				'description'   => __('Select Price Style', 'classified-listing'),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_price_unit',
				'label'     => __( 'Show Price Unit', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'Hide', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Show', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
				'description'   => __('Switch to Show Price Unit', 'classified-listing'),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_price_type',
				'label'     => __( 'Show Price Type', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'Hide', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Show', 'rtcl-elementor-builder' ),
				'default'   => 'yes',
				'description'   => __('Switch to Show Price Type', 'classified-listing'),
			),
			array(
				'mode' => 'section_end',
			),

		);

		return $fields;
	}

	/**
	 * Listings view function
	 *
	 * @return array
	 */
	public function price_style() {
		return array(
			'style-1' => __( 'Style 1', 'rtcl-elementor-builder' ),
			'style-2' => __( 'Style 2', 'rtcl-elementor-builder' ),
		);
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function text_style() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'Price',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Price', 'rtcl-elementor-builder' ),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_price_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-price',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_price_bg_color',
				'label'     => __( 'Background Color', 'rtcl-elementor-builder' ),
				'selectors' => array(
					'{{WRAPPER}} .el-single-addon .rtcl-price' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'rtcl_price_style' => array( 'style-2' ) ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_price_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => array(
					'{{WRAPPER}} .el-single-addon .rtcl-price' => 'color: {{VALUE}};',
				),
			),
			[
				'type'    => Controls_Manager::CHOOSE,
				'mode'      => 'responsive',
				'id'      => 'text_alignment',
				'label'   => __( 'Price Alignment', 'rtcl-elementor-builder' ),
				'options' => $this->alignment_options(),
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-price, {{WRAPPER}} .el-single-addon.item-price.style-2' => 'justify-content: {{VALUE}};',
				],
			],
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
	public function price_label_control() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'price_label',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Price Label', 'rtcl-elementor-builder' ),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'rtcl_show_price_unit',
							'operator' => '===',
							'value'    => 'yes',
						],
						[
							'name'     => 'rtcl_show_price_type',
							'operator' => '===',
							'value'    => 'yes',
						],
					],
				],
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_price_label_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-price .rtcl-price-meta > span',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_price_label_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => array(
					'{{WRAPPER}} .el-single-addon .rtcl-price .rtcl-price-meta > span' => 'color: {{VALUE}};',
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}



}

