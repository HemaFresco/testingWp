<?php

namespace RtclPro\Models;


use WP_Error;

class Subscriptions {

	/**
	 * @var string
	 */
	private $table;

	public function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix . "rtcl_subscriptions";
	}


	/**
	 * @param      $id
	 * @param bool $user_id
	 *
	 * @return Subscription|[]
	 */
	public function findById( $id, bool $user_id = false ): ?Subscription {
		global $wpdb;
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$wpdb->hide_errors();
		$rawSub = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d AND user_id = %d", $id, $user_id )
		);
		if ( $rawSub ) {
			return new Subscription( $rawSub );
		}

		return null;
	}


	/**
	 * @param $user_id
	 *
	 * @return Subscription[]|[]
	 */
	public function findAllByUserId( $user_id ): array {
		global $wpdb;
		$wpdb->hide_errors();
		$rawSubs       = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$this->table} WHERE user_id = %d AND status != %s ORDER BY created_at DESC", $user_id, 'canceled' )
		);
		$subscriptions = [];
		if ( ! empty( $rawSubs ) ) {
			foreach ( $rawSubs as $rawSub ) {
				$subscriptions[] = new Subscription( $rawSub );
			}
		}

		return $subscriptions;
	}

	/**
	 * @param $subId
	 *
	 * @return Subscription
	 */
	public function findOneBySubId( $subId ): ?Subscription {
		global $wpdb;
		$wpdb->hide_errors();
		$subscription = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$this->table} WHERE sub_id = %d", $subId )
		);

		if ( $subscription ) {
			return new Subscription( $subscription );
		}

		return null;
	}

	/**
	 * @param int         $user_id
	 * @param string|null $gateway_id
	 *
	 * @return Subscription|void
	 */
	public function findOneByUserId( int $user_id, string $gateway_id = null ) {
		global $wpdb;
		$wpdb->hide_errors();
		$subscription = $wpdb->get_row(
			$gateway_id ? $wpdb->prepare( "SELECT * FROM {$this->table} WHERE user_id = %d AND status = %s AND gateway_id = %s ORDER BY created_at DESC", $user_id, 'active', $gateway_id ) :
				$wpdb->prepare( "SELECT * FROM {$this->table} WHERE user_id = %d AND status = %s ORDER BY created_at DESC", $user_id, 'active' )
		);

		if ( $subscription ) {
			return new Subscription( $subscription );
		}
	}


	/**
	 * @param array $data
	 *
	 * @return Subscription | WP_Error
	 */
	public function create( array $data = [] ) {
		if ( ! is_array( $data ) || empty( $data['name'] ) || empty( $data['sub_id'] ) || empty( $data['gateway_id'] ) ) {
			return new WP_Error( 'rtcl_subscription_error', __( 'Error while creating subscription missing subId, gateway_id, name', 'classified-listing-pro' ) );
		}
		try {
			global $wpdb;
			$wpdb->hide_errors();
			$current_date = new \DateTime( current_time( 'mysql' ) );
			$data         = array_merge( [
				'quantity'   => 1,
				'user_id'    => get_current_user_id(),
				'created_at' => $current_date->format( 'Y-m-d H:i:s' ),
				'updated_at' => $current_date->format( 'Y-m-d H:i:s' )
			], $data );
			$subId        = $wpdb->insert( $this->table, $data );
			if ( ! $subId ) {
				return new WP_Error( 'rtcl_subscription_error', __( 'Error while inserting subscription', 'classified-listing-pro' ) );
			}

			return $this->findById( $subId );
		} catch ( \Exception $e ) {
			error_log( 'Error while creating subscription: ' . $e->getMessage() );

			return new WP_Error( 'rtcl_subscription_error', $e->getMessage() );
		}
	}

}