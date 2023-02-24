<?php
/**
 * Main Elementor ElbScripts Class
 *
 * The main class that initiates all scripts.
 *
 * @package  RTCL_Elementor_Builder
 * @since    1.0.0
 */

namespace RtclElb\Controllers;

use RtclElb\Traits\Singleton;
use RtclElb\Traits\ELTempleateBuilderTraits;

/**
 * Main Elementor ElbScripts Class
 */
class ElbScripts {
	/*
     * Template builder related traits.
     */
    use ELTempleateBuilderTraits;
	/**
	 * Singleton Function.
	 */
	use Singleton;
	/**
	 * Suffix string.
	 *
	 * @var string
	 */
	private $suffix;
	/**
	 * Plugin Version.
	 *
	 * @var string
	 */
	private $version;
	/**
	 * Ajax Url
	 *
	 * @var string
	 */
	/**
	 * Initial Function.
	 *
	 * @return void
	 */
	public function init() {
		$this->suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$this->version = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : RTCL_ELB_VERSION;
		add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_frontend_scripts' ] );
	}
	/**
	 * Admin related script.
	 *
	 * @return void
	 */
	public function register_admin_scripts() {

		wp_register_script( 'rtcl-elb-admin', rtclElb()->assets_url( 'js/admin' . $this->suffix . '.js' ), [ 'jquery', 'rtcl-common' ], $this->version, true );
		if ( !empty($_GET['post_type']) && $_GET['post_type'] == self::$post_type_tb ) {
			wp_localize_script('rtcl-elb-admin', 'rtcl_el_tb', [
				'ajaxurl'              => admin_url('admin-ajax.php'),
				'loading'              => esc_html__('Loading', 'classified-listing-pro'),
				rtcl()->nonceId        => wp_create_nonce(rtcl()->nonceText),
			]);
			wp_enqueue_script('rtcl-elb-admin');
		}
	}
	/**
	 * Admin related script.
	 *
	 * @return void
	 */
	public function register_frontend_scripts() {
		wp_register_style( 'rtcl-store-builder', rtclElb()->assets_url( 'css/rtcl-builder-store' . $this->suffix . '.css' ), [ 'rtcl-store-public' ], $this->version );
	}

}
