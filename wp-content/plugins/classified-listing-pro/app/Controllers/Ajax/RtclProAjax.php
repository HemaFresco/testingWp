<?php

namespace RtclPro\Controllers\Ajax;

use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;

class RtclProAjax {


	/**
	 * Initialize ajax hooks
	 *
	 * @return void
	 */
	public static function init() {
		if ( Fns::is_enable_mark_as_sold() ) {
			add_action( 'wp_ajax_rtcl_mark_as_sold_unsold', [ __CLASS__, 'rtcl_mark_as_sold_unsold' ] );
		}
		add_action( 'wp_ajax_rtcl_update_user_online_status', [ __CLASS__, 'update_user_online_status' ] );
		add_action( 'wp_ajax_nopriv_rtcl_update_user_online_status', [ __CLASS__, 'update_user_online_status' ] );
	}

	public static function update_user_online_status() {
		$listing_id = isset( $_POST['listing_id'] ) ? absint( $_POST['listing_id'] ) : 0;
		$success    = false;

		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), 'online_status', current_time( 'timestamp' ) + (int) apply_filters( 'rtcl_user_online_status_seconds', 300 ) );
		}

		if ( $listing_id ) {
			$listing = rtcl()->factory->get_listing( $listing_id );
			if ( Fns::is_online( $listing->get_owner_id() ) ) {
				$success = true;
			}
		}

		$response = [
			'success' => $success
		];
		wp_send_json( $response );
	}

	static function rtcl_mark_as_sold_unsold() {
		$listing  = '';
		$post_id  = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$listing  = rtcl()->factory->get_listing( $post_id );
		$agent_id = get_post_meta( $listing->get_id(), '_rtcl_manager_id', true );

		if ( ! Functions::verify_nonce() ) {
			wp_send_json_error( esc_html__( 'Session expired!!', 'classified-listing-pro' ) );
		}

		if ( ! $post_id || ! $listing || ( ! $agent_id && $listing->get_author_id() != get_current_user_id() ) || ( $agent_id && $agent_id != get_current_user_id() ) ) {
			wp_send_json_error( esc_html__( 'Unauthorized action', 'classified-listing-pro' ) );
		}

		if ( absint( get_post_meta( $listing->get_id(), '_rtcl_mark_as_sold', true ) ) ) {
			delete_post_meta( $listing->get_id(), '_rtcl_mark_as_sold' );
			$data = [
				'text' => esc_html__( 'Mark as sold', 'classified-listing-pro' ),
				'type' => 'unsold',
			];
		} else {
			update_post_meta( $listing->get_id(), '_rtcl_mark_as_sold', 1 );
			$data = [
				'text' => esc_html__( 'Mark as unsold', 'classified-listing-pro' ),
				'type' => 'sold',
			];
		}
		$data['listing_id'] = $listing->get_id();
		wp_send_json_success( apply_filters( 'rtcl_mark_as_sold_ajax_response_data', $data ) );
	}

}
