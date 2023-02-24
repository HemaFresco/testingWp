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
class StoreBannerSettings extends SingleStoreWidgetBase {
	/**
	 * Set Query controlls
	 */
	public function widget_general_fields(): array {
		$fields = array_merge(
			$this->banner_control(),
			$this->logo_control(),
			$this->content_visibility(),
		);
		return $fields;
	}
	/**
	 * Set style controlls
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->banner_store_control(),
			$this->store_logo_control()
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
				'id'          => 'rtcl_show_store_logo',
				'label'       => __( 'Show Store Logo', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Store Logo', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_store_name',
				'label'       => __( 'Show Store Name', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Store Name', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_category',
				'label'       => __( 'Show Category', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Category', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_rating',
				'label'       => __( 'Show Rating', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Rating', 'rtcl-elementor-builder' ),
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
	public function banner_control() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_banner',
				'label' => __( 'General', 'rtcl-elementor-builder' ),
			],
			[
				'id'              => 'banner_height',
				'mode'            => 'responsive',
				'label'           => esc_html__( 'Banner Height (px)', 'rtcl-elementor-builder' ),
				'type'            => Controls_Manager::SLIDER,
				'size_units'      => [ 'px' ],
				'range'           => [
					'px' => [
						'min' => 150,
						'max' => 500,
					],
				],
				'devices'         => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 250,
					'unit' => 'px',
				],
				'tablet_default'  => [
					'size' => 200,
					'unit' => 'px',
				],
				'mobile_default'  => [
					'size' => 150,
					'unit' => 'px',
				],
				'selectors'       => [
					'{{WRAPPER}} .store-banner .banner' => 'height: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'id'              => 'image_width',
				'mode'            => 'responsive',
				'label'           => esc_html__( 'Banner Image Width (%)', 'rtcl-elementor-builder' ),
				'type'            => Controls_Manager::SLIDER,
				'size_units'      => [ '%' ],
				'range'           => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'devices'         => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'tablet_default'  => [
					'size' => 100,
					'unit' => '%',
				],
				'mobile_default'  => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors'       => [
					'{{WRAPPER}} .store-banner .banner img' => 'width: {{SIZE}}{{UNIT}};',
				],
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
	public function logo_control() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_logo',
				'label' => __( 'Logo', 'rtcl-elementor-builder' ),
			],
			[
				'id'         => 'logo_height',
				'mode'       => 'responsive',
				'label'      => esc_html__( 'logo Wrapper Height (px)', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 150,
						'max' => 500,
					],
				],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'selectors'  => [
					'{{WRAPPER}} .store-banner .store-name-logo .store-logo' => 'height: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'id'         => 'logo_wight',
				'mode'       => 'responsive',
				'label'      => esc_html__( 'logo wrapper wight (px)', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 150,
						'max' => 500,
					],
				],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'selectors'  => [
					'{{WRAPPER}} .store-banner .store-name-logo .store-logo' => 'width: {{SIZE}}{{UNIT}};',
				],
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
	public function banner_store_control() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_banner_store',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'General', 'rtcl-elementor-builder' ),
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'name_typo',
				'label'    => __( 'Name Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .store-name-logo .store-name h2',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_name_color',
				'label'     => __( 'Name Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .store-name-logo .store-name h2' => 'color: {{VALUE}};',
				],
			],
			[
				'mode'      => 'group',
				'type'      => Group_Control_Typography::get_type(),
				'id'        => 'category_typo',
				'label'     => __( 'Category Typography', 'rtcl-elementor-builder' ),
				'condition' => [ 'rtcl_show_category' => [ 'yes' ] ],
				'selector'  => '{{WRAPPER}} .store-info .rtcl-store-cat',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_cat_color',
				'label'     => __( 'Category Color', 'rtcl-elementor-builder' ),
				'condition' => [ 'rtcl_show_category' => [ 'yes' ] ],
				'selectors' => [
					'{{WRAPPER}} .store-info .rtcl-store-cat' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_cat_icon_color',
				'label'     => __( 'Category icon Color', 'rtcl-elementor-builder' ),
				'condition' => [ 'rtcl_show_category' => [ 'yes' ] ],
				'selectors' => [
					'{{WRAPPER}} .store-info .rtcl-store-cat i' => 'color: {{VALUE}};',
				],
			],
			[
				'mode'      => 'group',
				'type'      => Group_Control_Typography::get_type(),
				'id'        => 'rtcl_review_typo',
				'label'     => __( 'Review Typography', 'rtcl-elementor-builder' ),
				'condition' => [ 'rtcl_show_rating' => [ 'yes' ] ],
				'selector'  => '{{WRAPPER}} .store-name-logo .reviews-rating',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_review_count_color',
				'label'     => __( 'Review count Color', 'rtcl-elementor-builder' ),
				'condition' => [ 'rtcl_show_rating' => [ 'yes' ] ],
				'selectors' => [
					'{{WRAPPER}} .store-name-logo .reviews-rating' => 'color: {{VALUE}};',
				],
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
	public function store_logo_control() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_banner_logo',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Logo', 'rtcl-elementor-builder' ),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_logo_background',
				'label'     => __( 'Wrapper background', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .store-banner .store-name-logo .store-logo' => 'background-color: {{VALUE}};',
				],
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Logo Border', 'rtcl-elementor-builder' ),
				'mode'           => 'group',
				'id'             => 'rtcl_logo_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '5',
							'right'    => '5',
							'bottom'   => '5',
							'left'     => '5',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => '#fff',
					],
				],
				'selector'       => '{{WRAPPER}} .store-name-logo .store-logo img',
			],
			[
				'id'              => 'logo_width',
				'mode'            => 'responsive',
				'label'           => esc_html__( 'Logo Image Width (px)', 'rtcl-elementor-builder' ),
				'type'            => Controls_Manager::SLIDER,
				'size_units'      => [ 'px' ],
				'range'           => [
					'px' => [
						'min' => 150,
						'max' => 300,
					],
				],
				'devices'         => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 150,
					'unit' => 'px',
				],
				'tablet_default'  => [
					'size' => 140,
					'unit' => 'px',
				],
				'mobile_default'  => [
					'size' => 130,
					'unit' => 'px',
				],
				'selectors'       => [
					'{{WRAPPER}} .store-name-logo .store-logo img' => 'width: {{SIZE}}{{UNIT}};',
				],
			],

			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}


}
