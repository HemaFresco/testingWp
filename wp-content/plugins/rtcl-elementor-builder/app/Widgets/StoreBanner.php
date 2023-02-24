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
use RtclElb\Widgets\WidgetSettings\StoreBannerSettings;

/**
 * ListingStore class
 */
class StoreBanner extends StoreBannerSettings {
	/**
	 * ListingStore Init
	 *
	 * @param array  $data others data
	 * @param [type] $args Others args
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Store Banner', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rtcl-listing-store-banner';
		parent::__construct( $data, $args );
	}

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings = $this->get_settings();

		$template_style = 'store-single/banner';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'store'              	=> $this->store,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_store_banner_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
	}
}
