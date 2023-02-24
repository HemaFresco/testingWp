<?php
/**
 * Singleton Trait.
 *
 * @package RtclElb
 */

namespace RtclElb\Traits;

/**
 * Singleton Trait.
 */
trait Singleton {
	/**
	 * Store the singleton object.
	 *
	 * @var boolean||object
	 */
	private static $singleton = false;

	/**
	 * Create an inaccessible constructor.
	 */
	private function __construct() {
		$this->__init();
	}
	/**
	 * Init Constructor
	 *
	 * @return void
	 */
	protected function __init() {
	}

	/**
	 * Fetch an instance of the class.
	 */
	final public static function getInstance() {
		if ( false === self::$singleton ) {
			self::$singleton = new self();
		}

		return self::$singleton;
	}

	/**
	 * Prevent cloning.
	 */
	final public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'rtcl-elementor-builder' ), '1.0' );
	}

	/**
	 * Prevent unserializing.
	 */
	final public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'rtcl-elementor-builder' ), '1.0' );
	}
}
