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
class StoreDescriptionSettings extends SingleStoreWidgetBase {
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
		return $this->store_description_control();
	}

	/**
	 * Undocumented function.
	 *
	 * @return array
	 */
	public function store_description_control() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_store_description',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Description', 'rtcl-elementor-builder' ),
			],

			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'typography',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .store-description-content p',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_name_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .store-description-content p' => 'color: {{VALUE}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}


}
