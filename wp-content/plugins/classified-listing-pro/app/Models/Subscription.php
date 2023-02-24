<?php

namespace RtclPro\Models;

use DateTime;
use Rtcl\Helpers\Cache;
use WP_Error;

class Subscription {

	protected int $id;
	protected string $subId;
	protected string $gateway_id;
	protected ?string $meta;
	protected array $statusList = [ 'active', 'expired', 'suspended', 'canceled', 'terminated', 'trialing' ];
	const STATUS_ACTIVE = 'active';
	const STATUS_EXPIRED = 'expired';
	const STATUS_EXPIRING = 'expiring';
	const STATUS_FAILED = 'failed';
	const STATUS_SUSPENDED = 'suspended';
	const STATUS_CANCELED = 'canceled';
	const STATUS_TERMINATED = 'terminated';
	const DATETIME_FORMAT = 'Y-m-d H:i:s';
	/**
	 * @var string
	 */
	private string $table;
	protected int $userId;
	protected string $status;
	protected int $productId;
	protected ?string $expiry_at = null;
	private ?string $name = null;
	private string $table_meta;

	public function __construct( $sub ) {
		if ( is_numeric( $sub ) ) {
			$this->setSubscriptionBySubId( $sub );
		} elseif ( $sub && is_object( $sub ) ) {
			$this->setSubscription( $sub );
		}
	}

	public function setSubscriptionBySubId( $subId ) {
		$sub = ( new Subscriptions() )->findOneBySubId( $subId );
		if ( $sub ) {
			$this->setSubscription( $sub );
		}
	}

	public function setSubscription( $sub ): Subscription {
		global $wpdb;
		$this->table      = $wpdb->prefix . "rtcl_subscriptions";
		$this->table_meta = $wpdb->prefix . "rtcl_subscription_meta";
		$this->id         = $sub->id;
		$this->name       = $sub->name;
		$this->subId      = $sub->sub_id;
		$this->gateway_id = $sub->gateway_id;
		$this->userId     = $sub->user_id;
		$this->productId  = $sub->product_id;
		$this->status     = $sub->status;
		$this->expiry_at  = $sub->expiry_at;
		$this->meta       = $sub->meta;

		return $this;
	}

	/**
	 * @param array $data
	 *
	 * @return Subscription|WP_Error
	 */
	public function update( array $data ) {
		if ( empty( $data ) ) {
			return new WP_Error( 'rtcl_subscription_update_error', __( 'Update data cannot be empty', 'classified-listing-pro' ) );
		}

		$updatedData = [];
		foreach ( $data as $_key => $_value ) {
			if ( 'name' === $_key ) {
				$updatedData[ $_key ] = sanitize_text_field( $_value );
			} else if ( 'productId' === $_key ) {
				$updatedData[ $_key ] = absint( $_value );
			} else if ( 'status' === $_key ) {
				if ( in_array( $_value, $this->statusList ) && $this->status != $_value ) {
					$updatedData[ $_key ] = $_value;
				}
			} else if ( 'expiry_at' === $_key ) {
				if ( is_a( $_value, DateTime::class ) ) {
					$updatedData[ $_key ] = $_value->format( 'Y-m-d H:i:s' );
				} else if ( is_string( $_value ) ) {
					$format = 'Y-m-d H:i:s';
					$d      = DateTime::createFromFormat( $format, $_value );
					if ( $d && $d->format( $_value ) === $_value ) {
						$updatedData[ $_key ] = $d->format( $format );
					}
				}
			} else if ( 'meta' === $_key ) {
				if ( ! empty( $_value ) && is_array( $_value ) ) {
					$oldMeta              = $this->getMeta();
					$meta                 = wp_parse_args( $_value, $oldMeta );
					$updatedData[ $_key ] = wp_json_encode( $meta );
				} else if ( null == $_value ) {
					$updatedData[ $_key ] = null;
				}
			}
		}
		if ( empty( $updatedData ) ) {
			return new WP_Error( 'rtcl_subscription_update_error', __( 'Update data cannot be empty', 'classified-listing-pro' ) );
		}


		global $wpdb;
		$update = $wpdb->update(
			$this->table,
			$updatedData,
			[
				'id'      => $this->id,
				'sub_id'  => $this->subId,
				'user_id' => $this->userId
			]
		);

		if ( ! $update ) {
			return new WP_Error( 'rtcl_subscription_update_error', __( 'Error while updating subscription data.', 'classified-listing-pro' ) );
		}

		foreach ( $updatedData as $_k => $_v ) {
			$this->$_k = $_v;
		}
		do_action( 'rtcl_subscription_update', $this, $updatedData );

		return $this;
	}

	/**
	 * @param string $status
	 *
	 * @return Subscription|WP_Error
	 */
	public function updateStatus( string $status ) {

		if ( ! in_array( $status, $this->statusList ) ) {
			return new WP_Error( 'rtcl_subscription_update_error', __( 'Unknown status is given', 'classified-listing-pro' ) );
		}
		if ( $this->status === $status ) {
			return new WP_Error( 'rtcl_subscription_update_error', __( 'No change at status.', 'classified-listing-pro' ) );
		}
		$oldStatus = $this->status;

		global $wpdb;
		$update = $wpdb->update(
			$this->table,
			[ 'status' => $status ],
			[
				'id'      => $this->id,
				'sub_id'  => $this->subId,
				'user_id' => $this->userId
			]
		);
		if ( ! $update ) {
			return new WP_Error( 'rtcl_subscription_update_error', __( 'Error while updating subscription status.', 'classified-listing-pro' ) );
		}
		$this->status = $status;
		do_action( 'rtcl_subscription_update_status', $this, $status, $oldStatus );

		return $this;
	}

	public function updatePayment( $paymentData ): Subscription {
		$oldMeta = $this->getMeta();

		$meta = wp_parse_args( $paymentData, $oldMeta );

		global $wpdb;
		$update = $wpdb->update(
			$this->table,
			[ 'meta' => wp_json_encode( $meta ) ],
			[
				'id'      => $this->id,
				'sub_id'  => $this->subId,
				'user_id' => $this->userId
			]
		);
		if ( $update ) {
			do_action( 'rtcl_subscription_update_payment', $this, $meta, $oldMeta );
		}

		return $this;
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getStatus(): string {
		return $this->status;
	}

	/**
	 * @return string
	 */
	public function getGatewayId(): string {
		return $this->gateway_id;
	}

	/**
	 * @return string
	 */
	public function getSubId(): string {
		return $this->subId;
	}


	/**
	 * @param $meta_key
	 * @param $meta_value
	 * @param $prev_value
	 *
	 * @return bool|int
	 */
	public function update_meta( $meta_key, $meta_value, $prev_value = null ) {

		if ( ! $this->id || ! $meta_key ) {
			return false;
		}

		// expected_slashed ($meta_key)
		$raw_meta_key = $meta_key;
		$meta_key     = wp_unslash( $meta_key );
		$passed_value = $meta_value;
		$meta_value   = wp_unslash( $meta_value );
		$meta_value   = sanitize_meta( $meta_key, $meta_value, 'post' );

		// Compare existing value to new value if no prev value given and the key exists only once.
		if ( empty( $prev_value ) ) {
			$old_value = $this->get_meta( $meta_key );
			if ( count( $old_value ) == 1 ) {
				if ( $old_value[0] === $meta_value ) {
					return false;
				}
			}
		}
		global $wpdb;
		$meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM {$this->table_meta} WHERE meta_key = %s AND subscription_id = %d", $meta_key, $this->id ) );
		if ( empty( $meta_ids ) ) {
			return $this->add_meta( $raw_meta_key, $passed_value );
		}

		$meta_value = maybe_serialize( $meta_value );

		$result = $wpdb->update(
			$this->table_meta,
			compact( 'meta_value' ),
			[ 'subscription_id' => $this->id, 'meta_key' => $meta_key ]
		);
		if ( ! $result ) {
			return false;
		}

		$this->cacheClear();

		return true;
	}

	public function add_meta( $meta_key, $meta_value, $unique = false ) {

		if ( ! $this->id || ! $meta_key ) {
			return false;
		}

		// expected_slashed ($meta_key)
		$meta_key   = wp_unslash( $meta_key );
		$meta_value = wp_unslash( $meta_value );
		$meta_value = sanitize_meta( $meta_key, $meta_value, 'post' );
		global $wpdb;
		// Only unique
		if ( $unique && $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table_meta} WHERE meta_key = %s AND subscription_id = %d",
				$meta_key, $this->id ) ) ) {
			return false;
		}

		$meta_value = maybe_serialize( $meta_value );

		$result = $wpdb->insert( $this->table_meta, [
			'subscription_id' => $this->id,
			'meta_key'        => $meta_key,
			'meta_value'      => $meta_value
		] );

		if ( ! $result ) {
			return false;
		}

		$this->cacheClear();

		return $wpdb->insert_id;
	}

	public function get_meta( $meta_key, $single = false ) {
		if ( ! $this->id || ! $meta_key ) {
			return false;
		}
		global $wpdb;
		$metas = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT meta_value FROM {$this->table_meta} 
                                    WHERE  subscription_id = %d
                                    AND meta_key = %s",
				$this->id,
				$meta_key
			)
		);
		if ( $metas ) {
			if ( $single ) {
				return maybe_unserialize( $metas[0] );
			} else {
				return array_map( 'maybe_unserialize', $metas );
			}
		}


		if ( $single ) {
			return '';
		} else {
			return [];
		}

	}

	/**
	 * @param        $meta_key
	 * @param string $meta_value
	 *
	 * @return bool
	 */
	public function delete_meta( $meta_key, string $meta_value = '' ): bool {

		if ( ! $this->id || ! $meta_key ) {
			return false;
		}

		$meta_key   = wp_unslash( $meta_key );
		$meta_value = wp_unslash( $meta_value );
		$meta_value = maybe_serialize( $meta_value );
		global $wpdb;
		$query = $wpdb->prepare( "SELECT meta_id FROM {$this->table_meta} 
                                              WHERE subscription_id = %d
                                              AND meta_key = %s",
			$this->id,
			$meta_key
		);
		if ( '' !== $meta_value && null !== $meta_value && false !== $meta_value ) {
			$query .= $wpdb->prepare( " AND meta_value = %s", $meta_value );
		}
		$meta_ids = $wpdb->get_col( $query );
		if ( ! count( $meta_ids ) ) {
			return false;
		}
		$query = "DELETE FROM {$this->table_meta} WHERE meta_id IN( " . implode( ',', $meta_ids ) . " )";

		$count = $wpdb->query( $query );
		if ( ! $count ) {
			return false;
		}

		$this->cacheClear();

		return true;

	}

	/**
	 * @return string|null
	 */
	public function getExpiryAt(): ?string {
		return $this->expiry_at !== '0000-00-00 00:00:00' ? $this->expiry_at : null;
	}

	/**
	 * @return object
	 */
	public function getMeta() {
		$meta = $this->meta;
		if ( empty( $meta ) ) {
			return new \stdClass();
		}
		$meta = stripslashes( $meta );

		return json_decode( $meta );
	}

	/**
	 * @return int
	 */
	public function getProductId(): int {
		return absint( $this->productId );
	}

	/**
	 * @return int
	 */
	public function getUserId(): int {
		return $this->userId;
	}

	public function cacheClear() {
		$cache_key = Cache::get_cache_prefix( 'subscription' ) . 'subscription_' . $this->id;
		wp_cache_delete( $cache_key, 'subscription' );
	}


}