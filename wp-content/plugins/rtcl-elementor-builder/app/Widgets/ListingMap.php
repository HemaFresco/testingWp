<?php
/**
 * Main Elementor ListingMap Class
 *
 * ListingMap main class
 *
 * @author  RadiusTheme
 * @since   2.0.10
 * @package  RTCL_Elementor_Builder
 * @version 1.2
 */

namespace RtclElb\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Rtcl\Helpers\Functions;
use RtclElb\Widgets\WidgetSettings\ListingMapSettings;
use RtclElb\Traits\ELTempleateBuilderTraits;

/**
 * ListingMap class
 */
class ListingMap extends ListingMapSettings {

	/**
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;
	/**
	 * Construct function
	 *
	 * @param array  $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Listing Map', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rt-listing-map';
		parent::__construct( $data, $args );
	}

	/**
	 * Undocumented function
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return [ 'rtcl-map' ];
	}

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings       = $this->get_settings();
		$template_style = 'single/map';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_listing_page_map_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
		$this->edit_mode_script();
	}
	/**
	 * Elementor Edit mode need some extra js for isotop reinitialize
	 *
	 * @return mixed
	 */
	public function edit_mode_script() {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$selector = $this->get_unique_selector() . ' .rtcl-map';
			?>
			<script>
				// render single map
				jQuery('<?php echo esc_attr( $selector ); ?>').each(function () {
					rtcl_render_map( this );
				});
			</script>
			<?php
		}
	}
}

