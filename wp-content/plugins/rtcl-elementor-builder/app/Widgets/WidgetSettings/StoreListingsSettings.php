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
use Elementor\Group_Control_Typography;
use RtclElb\Abstracts\SingleStoreWidgetBase;

/**
 * ListingStoreSettings Class.
 */
class StoreListingsSettings extends SingleStoreWidgetBase {
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
		return $this->store_logo_control();
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
				'id'    => 'rtcl_sec_store_slogan',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Slogan', 'rtcl-elementor-builder' ),
			],

			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'slogan_typo',
				'label'    => __( 'Slogan Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .store-details .is-slogan',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'slogan_color',
				'label'     => __( 'Slogan Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .store-details .is-slogan' => 'color: {{VALUE}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}


}
