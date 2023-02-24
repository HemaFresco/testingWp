<?php
/**
 * Plugin Name: Classified Listing – Elementor Archive & Single Page Builder Addon
 * Plugin URI: https://radiustheme.com/
 * Description: Provides RTCL Elementor Archive And Single Page Builder.
 * Author: RadiusTheme
 * Version: 1.1.1
 * Author URI: www.radiustheme.com
 * Text Domain: rtcl-elementor-builder
 * Domain Path: /languages
 *
 * @package  RTCL_Elementor_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Defining Constants.
 */
define( 'RTCL_ELB_VERSION', '1.1.1' );
define( 'RTCL_ELB_PLUGIN_ACTIVE_FILE_NAME', __FILE__ );
define( 'RTCL_ELB_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'RTCL_ELB_LANGUAGE_PATH', dirname( plugin_basename( __FILE__ ) ) . '/languages' );

require_once __DIR__ . '/vendor/autoload.php';

register_activation_hook( __FILE__, 'activate_rtcl_elb' );

register_deactivation_hook( __FILE__, 'deactivate_rtcl_elb' );

/**
 * Plugin activation action.
 *
 * Plugin activation will not work after "plugins_loaded" hook
 * that's why activation hooks run here.
 */
function activate_rtcl_elb() {
	\RtclElb\Helpers\Installer::activate();
}

/**
 * Plugin deactivation action.
 *
 * Plugin deactivation will not work after "plugins_loaded" hook
 * that's why deactivation hooks run here.
 */
function deactivate_rtcl_elb() {
	\RtclElb\Helpers\Installer::deactivate();
}

/**
 * App init.
 */

/**
 * Returns RtclElb.
 *
 * @return RtclElb
 */
function rtclElb() {
	return RtclElb\RtclElb::getInstance();
}
rtclElb()->init();
