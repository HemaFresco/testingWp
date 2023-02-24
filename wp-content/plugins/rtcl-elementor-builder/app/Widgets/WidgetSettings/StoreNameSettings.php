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
class StoreNameSettings extends SingleStoreWidgetBase {
	/**
	 * Set Query controlls
	 */
	public function widget_general_fields(): array {
		return [];
	}
	/**
	 * Set style controlls
	 */
	public function widget_style_fields(): array {
		return $this->banner_store_control();
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
				'id'    => 'rtcl_sec_store_name',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'General', 'rtcl-elementor-builder' ),
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'name_typo',
				'label'    => __( 'Name Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .store-name h2',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_name_color',
				'label'     => __( 'Name Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .store-name h2' => 'color: {{VALUE}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

}
