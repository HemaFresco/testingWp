<?php
/**
 * @author  RadiusTheme
 *
 * @since   1.0
 *
 * @version 1.0
 */

namespace RtclElb\Widgets;

use Rtcl\Helpers\Functions;
use RtclElb\Widgets\WidgetSettings\StoreOpeningHoursSettings;

/**
 * ListingStore class
 */
class StoreOpeningHours extends StoreOpeningHoursSettings {
	/**
	 * ListingStore Init
	 *
	 * @param array  $data others data.
	 * @param [type] $args Others args.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Store Opening Hours', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rtcl-listing-store-opening-hours';
		parent::__construct( $data, $args );
	}

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings = $this->get_settings();

		$template_style = 'store-single/opening';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'store'                 => $this->store,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_store_opening_hours_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
	}
}
