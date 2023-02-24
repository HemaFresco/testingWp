<?php
/**
 * Helpers class.
 */

namespace RtclElb\Helpers;

use RtclElb\Traits\ELTempleateBuilderTraits;

/**
 * Helpers class.
 */
class Fns {
	/*
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;

	/**
	 * Classes instatiation.
	 *
	 * @param array $classes classes to init
	 *
	 * @return void
	 */
	public static function instances( array $classes ) {
		if ( empty( $classes ) ) {
			return;
		}

		foreach ( $classes as $class ) {
			$service = $class::getInstance();
			if ( method_exists( $service, 'init' ) ) {
				$service->init();
			}
		}
	}
	/**
	 * Is Active Store.
	 *
	 * @return bool
	 */
	public static function is_has_store() {
		if ( class_exists( 'RtclStore' ) ) {
			return true;
		}
		return false;
	}
	/**
	 * Is Active Store.
	 *
	 * @return bool
	 */
	public static function last_store_id() {
		if ( ! self::is_has_store() ) {
			return 0;
		}
		if ( is_singular( rtclStore()->post_type ) ) {
			return get_the_ID();
		}
		global $wpdb;
		$cache_key = 'rtcl_last_store_id';
		$_post_id  = wp_cache_get( $cache_key );
		if ( false === $_post_id || 'publish' !== get_post_status( $_post_id ) ) {
			$_post_id = $wpdb->get_var(
				$wpdb->prepare( "SELECT MAX(ID) FROM {$wpdb->prefix}posts WHERE post_type =  %s AND post_status = %s", rtclStore()->post_type, 'publish' )
			);
			wp_cache_set( $cache_key, $_post_id );
		}

		return $_post_id;

	}
}
