<?php
/**
 * @author  RadiusTheme
 *
 * @since   1.0
 *
 * @version 1.0
 */

namespace RtclElb\Widgets;

use RtclElb\Helpers\Fns;
// use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Functions;
use RtclElb\Widgets\WidgetSettings\StoreContactInfoSettings;

/**
 * ListingStore class
 */
class StoreContactInfo extends StoreContactInfoSettings {
	/**
	 * ListingStore Init
	 *
	 * @param array  $data others data
	 * @param [type] $args Others args
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Store Contact Info', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rtcl-listing-store-contact';
		parent::__construct( $data, $args );
	}

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	public function details_modal( ) {
		$template_style = 'store-single/details-modal';
		$data           = [
			'template'              => $template_style,
			'store'                 => $this->store,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_store_contact_details_modal_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
	}

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings       = $this->get_settings();
		add_action( 'wp_footer', [ $this, 'details_modal' ] );
		$template_style = 'store-single/contact-info';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'store'                 => $this->store,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_store_contact_info_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
	}
}
