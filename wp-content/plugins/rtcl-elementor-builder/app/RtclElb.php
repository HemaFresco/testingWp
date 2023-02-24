<?php
/**
 * Main initialization class.
 *
 * @package  RTCL_Elementor_Builder
 */

namespace RtclElb;

use RtclElb\Traits\Singleton;
use RtclElb\Helpers\Fns;
use RtclElb\Controllers\Ajax\ELProAjax;
use RtclElb\Controllers\Builder\TemplateBuilder;
use RtclElb\Controllers\Builder\TemplateBuilderFrontend;
use RtclElb\Controllers\Hooks\FilterHooks;

use RtclElb\Controllers\
{
	AdminNotices,
	ElbScripts,
	ElementorController
};

if ( ! class_exists( RtclElb::class ) ) {
	/**
	 * Main initialization class.
	 */
	final class RtclElb {

		use Singleton;

		/**
		 * Plugin path.
		 *
		 * @var string
		 */
		public $plugin_path;

		/**
		 * Class init.
		 *
		 * @return void
		 */
		public function init() {
			$dependence = AdminNotices::getInstance();
			if ( $dependence->check() ) {
				FilterHooks::init();
				\add_action( 'init', [ $this, 'init_services' ] );
				TemplateBuilderFrontend::init();
				TemplateBuilder::init();
				ELProAjax::init();
				add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
			}
		}

		/**
		 * Init services.
		 *
		 * @return void
		 */
		public function init_services() {
			$this->i18n();
			$this->singleton_controllers();
		}

		/**
		 * Internationalization.
		 *
		 * @return void
		 */
		public function i18n() {
			load_plugin_textdomain( 'rtcl-elementor-builder', false, RTCL_ELB_LANGUAGE_PATH );
		}

		/**
		 * Init Controllers.
		 *
		 * @return bool
		 */
		public function is_rtcl_active() {
			return class_exists( Rtcl::class );
		}

		/**
		 * Controllers.
		 *
		 * @return void
		 */
		public function singleton_controllers() {
			$controllers = [
				ElbScripts::class,
				ElementorController::class,
			];
			Fns::instances( $controllers );
		}

		/**
		 * Actions on Plugins Loaded.
		 *
		 * @return void
		 */
		public function on_plugins_loaded() {
			\do_action( 'rtcl_elb_loaded', $this );
		}

		/**
		 * Plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( RTCL_ELB_PLUGIN_ACTIVE_FILE_NAME ) );
		}

		/**
		 * Template path
		 *
		 * @return string
		 */
		public function get_plugin_template_path() {
			return apply_filters( 'rtcl_elb_template_path', $this->plugin_path() . '/templates/elementor' );
		}

		/**
		 * Assets URL.
		 *
		 * @param string $location file location.
		 * @return string
		 */
		public function assets_url( $location = '' ) {
			return esc_url( RTCL_ELB_PLUGIN_URL . '/assets/' . $location );
		}
	}

}
