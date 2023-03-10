<?php

namespace RtclPro\Gateways\Stripe\lib;

use Rtcl\Helpers\Functions;
use WP_Error;
use WP_User;

class StripeCustomer {

	/**
	 * Stripe customer ID
	 *
	 * @var string
	 */
	private $id = '';

	/**
	 * WP User ID
	 *
	 * @var integer
	 */
	private $user_id = 0;

	/**
	 * Data from API
	 *
	 * @var array
	 */
	private $customer_data = [];

	/**
	 * Constructor
	 *
	 * @param int $user_id The WP user ID
	 */
	public function __construct( $user_id = 0 ) {
		if ( $user_id ) {
			$this->set_user_id( $user_id );
			$this->set_id( $this->get_id_from_meta( $user_id ) );
		}
	}

	/**
	 * Get Stripe customer ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set Stripe customer ID.
	 *
	 * @param [type] $id [description]
	 */
	public function set_id( $id ) {
		// Backwards compat for customer ID stored in array format. (Pre 3.0)
		if ( is_array( $id ) && isset( $id['customer_id'] ) ) {
			$id = $id['customer_id'];

			$this->update_id_in_meta( $id );
		}

		$this->id = Functions::clean( $id );
	}

	/**
	 * User ID in WordPress.
	 *
	 * @return int
	 */
	public function get_user_id() {
		return absint( $this->user_id );
	}

	/**
	 * Set User ID used by WordPress.
	 *
	 * @param int $user_id
	 */
	public function set_user_id( $user_id ) {
		$this->user_id = absint( $user_id );
	}

	/**
	 * Get user object.
	 *
	 * @return WP_User|bool
	 */
	protected function get_user() {
		return $this->get_user_id() ? get_user_by( 'id', $this->get_user_id() ) : false;
	}

	/**
	 * Store data from the Stripe API about this customer
	 */
	public function set_customer_data( $data ) {
		$this->customer_data = $data;
	}

	/**
	 * Generates the customer request, used for both creating and updating customers.
	 *
	 * @param array $args Additional arguments (optional).
	 *
	 * @return array
	 */
	protected function generate_customer_request( $args = [] ) {
		$billing_email = isset( $_POST['billing_email'] ) ? filter_var( wp_unslash( $_POST['billing_email'] ), FILTER_SANITIZE_EMAIL ) : '';
		$user          = $this->get_user();

		if ( $user ) {
			$billing_first_name = get_user_meta( $user->ID, '_billing_first_name', true );
			$billing_last_name  = get_user_meta( $user->ID, '_billing_last_name', true );

			// If billing first name does not exists try the user first name.
			if ( empty( $billing_first_name ) ) {
				$billing_first_name = get_user_meta( $user->ID, 'first_name', true );
			}

			// If billing last name does not exists try the user last name.
			if ( empty( $billing_last_name ) ) {
				$billing_last_name = get_user_meta( $user->ID, 'last_name', true );
			}

			// translators: %1$s First name, %2$s Second name, %3$s Username.
			$description = sprintf( __( 'Name: %1$s %2$s, Username: %3$s', 'classified-listing-pro' ), $billing_first_name, $billing_last_name, $user->user_login );

			$defaults = [
				'email'       => $user->user_email,
				'description' => $description,
			];

			$billing_full_name = trim( $billing_first_name . ' ' . $billing_last_name );
			if ( ! empty( $billing_full_name ) ) {
				$defaults['name'] = $billing_full_name;
			}
		} else {
			$billing_first_name = isset( $_POST['billing_first_name'] ) ? filter_var( wp_unslash( $_POST['billing_first_name'] ), FILTER_SANITIZE_STRING ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			$billing_last_name  = isset( $_POST['billing_last_name'] ) ? filter_var( wp_unslash( $_POST['billing_last_name'] ), FILTER_SANITIZE_STRING ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

			// translators: %1$s First name, %2$s Second name.
			$description = sprintf( __( 'Name: %1$s %2$s, Guest', 'classified-listing-pro' ), $billing_first_name, $billing_last_name );

			$defaults = [
				'email'       => $billing_email,
				'description' => $description,
			];

			$billing_full_name = trim( $billing_first_name . ' ' . $billing_last_name );
			if ( ! empty( $billing_full_name ) ) {
				$defaults['name'] = $billing_full_name;
			}
		}

		$metadata                      = [];
		$defaults['metadata']          = apply_filters( 'rtcl_stripe_customer_metadata', $metadata, $user );
		$defaults['preferred_locales'] = $this->get_customer_preferred_locale( $user );

		return wp_parse_args( $args, $defaults );
	}

	/**
	 * Create a customer via API.
	 *
	 * @param array $args
	 *
	 * @return WP_Error|int
	 * @throws StripeException
	 */
	public function create_customer( $args = [] ) {
		$args     = $this->generate_customer_request( $args );
		$stripe   = new StripeAPI();
		$response = $stripe->request( apply_filters( 'rtcl_stripe_create_customer_args', $args ), 'customers' );

		if ( ! empty( $response->error ) ) {
			throw new StripeException( print_r( $response, true ), $response->error->message );
		}

		$this->set_id( $response->id );
		$this->clear_cache();
		$this->set_customer_data( $response );

		if ( $this->get_user_id() ) {
			$this->update_id_in_meta( $response->id );
		}

		do_action( 'rtcl_stripe_add_customer', $args, $response );

		return $response->id;
	}

	/**
	 * Updates the Stripe customer through the API.
	 *
	 * @param array $args     Additional arguments for the request (optional).
	 * @param bool  $is_retry Whether the current call is a retry (optional, defaults to false). If true, then an exception will be thrown instead of further retries on error.
	 *
	 * @return string Customer ID
	 *
	 * @throws StripeException
	 */
	public function update_customer( $args = [], $is_retry = false ) {
		if ( empty( $this->get_id() ) ) {
			throw new StripeException( 'id_required_to_update_user', __( 'Attempting to update a Stripe customer without a customer ID.', 'classified-listing-pro' ) );
		}
		$stripe   = new StripeAPI();
		$args     = $this->generate_customer_request( $args );
		$args     = apply_filters( 'rtcl_stripe_update_customer_args', $args );
		$response = $stripe->request( $args, 'customers/' . $this->get_id() );

		if ( ! empty( $response->error ) ) {
			if ( $this->is_no_such_customer_error( $response->error ) && ! $is_retry ) {
				// This can happen when switching the main Stripe account or importing users from another site.
				// If not already retrying, recreate the customer and then try updating it again.
				$this->recreate_customer();

				return $this->update_customer( $args, true );
			}

			throw new StripeException( print_r( $response, true ), $response->error->message );
		}

		$this->clear_cache();
		$this->set_customer_data( $response );

		do_action( 'rtcl_stripe_update_customer', $args, $response );

		return $this->get_id();
	}

	/**
	 * Checks to see if error is of invalid request
	 * error and it is no such customer.
	 *
	 * @param array $error
	 *
	 * @since 4.1.2
	 */
	public function is_no_such_customer_error( $error ) {
		return (
			$error &&
			'invalid_request_error' === $error->type &&
			preg_match( '/No such customer/i', $error->message )
		);
	}

	/**
	 * Checks to see if error is of invalid request
	 * error and it is no such customer.
	 *
	 * @param array $error
	 *
	 * @return bool
	 * @since 4.5.6
	 */
	public function is_source_already_attached_error( $error ) {
		return (
			$error &&
			'invalid_request_error' === $error->type &&
			preg_match( '/already been attached to a customer/i', $error->message )
		);
	}

	/**
	 * Add a source for this stripe customer.
	 *
	 * @param string $source_id
	 *
	 * @return WP_Error|int
	 */
	public function add_source( $source_id ) {
		$stripe   = new StripeAPI();
		$response = $stripe->retrieve( 'sources/' . $source_id );

		if ( ! empty( $response->error ) || is_wp_error( $response ) ) {
			return $response;
		}

		// Add token to WooCommerce.
		$wc_token = false;

		if ( $this->get_user_id() && class_exists( 'WC_Payment_Token_CC' ) ) {
			if ( ! empty( $response->type ) ) {
				switch ( $response->type ) {
					case 'alipay':
						break;
					case 'sepa_debit':
						$wc_token = new WC_Payment_Token_SEPA();
						$wc_token->set_token( $response->id );
						$wc_token->set_gateway_id( 'stripe_sepa' );
						$wc_token->set_last4( $response->sepa_debit->last4 );
						break;
					default:
						if ( 'source' === $response->object && 'card' === $response->type ) {
							$wc_token = new WC_Payment_Token_CC();
							$wc_token->set_token( $response->id );
							$wc_token->set_gateway_id( 'stripe' );
							$wc_token->set_card_type( strtolower( $response->card->brand ) );
							$wc_token->set_last4( $response->card->last4 );
							$wc_token->set_expiry_month( $response->card->exp_month );
							$wc_token->set_expiry_year( $response->card->exp_year );
						}
						break;
				}
			} else {
				// Legacy.
				$wc_token = new WC_Payment_Token_CC();
				$wc_token->set_token( $response->id );
				$wc_token->set_gateway_id( 'stripe' );
				$wc_token->set_card_type( strtolower( $response->brand ) );
				$wc_token->set_last4( $response->last4 );
				$wc_token->set_expiry_month( $response->exp_month );
				$wc_token->set_expiry_year( $response->exp_year );
			}

			$wc_token->set_user_id( $this->get_user_id() );
			$wc_token->save();
		}

		$this->clear_cache();

		do_action( 'rtcl_stripe_add_source', $this->get_id(), $wc_token, $response, $source_id );

		return $response->id;
	}

	/**
	 * Attaches a source to the Stripe customer.
	 *
	 * @param string $source_id The ID of the new source.
	 *
	 * @return object|WP_Error Either a source object, or a WP error.
	 */
	public function attach_source( $source_id ) {
		if ( ! $this->get_id() ) {
			$this->set_id( $this->create_customer() );
		}
		$stripe   = new StripeAPI();
		$response = $stripe->request(
			[
				'source' => $source_id,
			],
			'customers/' . $this->get_id() . '/sources'
		);

		if ( ! empty( $response->error ) ) {
			// It is possible the WC user once was linked to a customer on Stripe
			// but no longer exists. Instead of failing, lets try to create a
			// new customer.
			if ( $this->is_no_such_customer_error( $response->error ) ) {
				$this->recreate_customer();

				return $this->attach_source( $source_id );
			} elseif ( $this->is_source_already_attached_error( $response->error ) ) {
				return $stripe->request( [], 'sources/' . $source_id, 'GET' );
			} else {
				return $response;
			}
		} elseif ( empty( $response->id ) ) {
			return new WP_Error( 'error', __( 'Unable to add payment source.', 'classified-listing-pro' ) );
		} else {
			return $response;
		}
	}

	/**
	 * Attaches a source to the Stripe customer.
	 *
	 * @param $paymentMethodId
	 *
	 * @return object|WP_Error Either a source object, or a WP error.
	 * @throws StripeException
	 */
	public function attach_payment_method( $paymentMethodId, $makeAsDefault = false ) {
		if ( ! $this->get_id() ) {
			$this->set_id( $this->create_customer() );
		}
		$stripe   = new StripeAPI();
		$response = $stripe->request(
			[
				'customer' => $this->get_id()
			],
			'payment_methods/' . $paymentMethodId . '/attach'
		);

		if ( ! empty( $response->error ) ) {
			// It is possible the WC user once was linked to a customer on Stripe
			// but no longer exists. Instead of failing, lets try to create a
			// new customer.
			if ( $this->is_no_such_customer_error( $response->error ) ) {
				$this->recreate_customer();
				$pmObj = $this->attach_payment_method( $paymentMethodId, $makeAsDefault );
				if ( $makeAsDefault ) {

					$customer = $this->paymentMethodMakeAsDefault( $paymentMethodId );
					if ( is_wp_error( $customer ) ) {
						return $customer;
					}
				}

				return $pmObj;
			} elseif ( $this->is_source_already_attached_error( $response->error ) ) {

				$pmObj = $stripe->request( [], 'payment_methods/' . $paymentMethodId, 'GET' );
				if ( $makeAsDefault ) {
					$customer = $this->paymentMethodMakeAsDefault( $paymentMethodId );
					if ( is_wp_error( $customer ) ) {
						return $customer;
					}
				}

				return $pmObj;
			} else {
				return $response;
			}
		} elseif ( empty( $response->id ) ) {
			return new WP_Error( 'rtcl_stripe_error', __( 'Unable to add payment method.', 'classified-listing-pro' ) );
		} else {
			$customer = $this->paymentMethodMakeAsDefault( $paymentMethodId );
			if ( is_wp_error( $customer ) ) {
				return $customer;
			}

			return $response;
		}
	}


	public function paymentMethodMakeAsDefault( $pm_id ) {
		try {
			$stripe = new StripeAPI();

			return $stripe->request( [
				'invoice_settings' => [
					'default_payment_method' => $pm_id
				]
			],
				'customers/' . $this->get_id() );
		} catch ( StripeException $exception ) {
			return new WP_Error( 'rtcl_stripe_error', $exception->getMessage() );
		}
	}


	/**
	 * Get a customers saved sources using their Stripe ID.
	 *
	 * @return array
	 * @throws StripeException
	 */
	public function get_sources() {
		if ( ! $this->get_id() ) {
			return [];
		}

		$sources = get_transient( 'stripe_sources_' . $this->get_id() );

		if ( false === $sources ) {
			$stripe   = new StripeAPI();
			$response = $stripe->request(
				[
					'limit' => 100,
				],
				'customers/' . $this->get_id() . '/sources',
				'GET'
			);

			if ( ! empty( $response->error ) ) {
				return [];
			}

			if ( is_array( $response->data ) ) {
				$sources = $response->data;
			}

			set_transient( 'stripe_sources_' . $this->get_id(), $sources, DAY_IN_SECONDS );
		}

		return empty( $sources ) ? [] : $sources;
	}

	/**
	 * Delete a source from stripe.
	 *
	 * @param string $source_id
	 */
	public function delete_source( $source_id ) {
		if ( ! $this->get_id() ) {
			return false;
		}
		$stripe   = new StripeAPI();
		$response = $stripe->request( [], 'customers/' . $this->get_id() . '/sources/' . sanitize_text_field( $source_id ), 'DELETE' );

		$this->clear_cache();

		if ( empty( $response->error ) ) {
			do_action( 'wc_stripe_delete_source', $this->get_id(), $response );

			return true;
		}

		return false;
	}

	/**
	 * Set default source in Stripe
	 *
	 * @param string $source_id
	 *
	 * @return bool
	 * @throws StripeException
	 */
	public function set_default_source( $source_id ) {
		$stripe   = new StripeAPI();
		$response = $stripe->request(
			[
				'default_source' => sanitize_text_field( $source_id ),
			],
			'customers/' . $this->get_id(),
			'POST'
		);

		$this->clear_cache();

		if ( empty( $response->error ) ) {
			do_action( 'wc_stripe_set_default_source', $this->get_id(), $response );

			return true;
		}

		return false;
	}

	/**
	 * Deletes caches for this users cards.
	 */
	public function clear_cache() {
		delete_transient( 'stripe_sources_' . $this->get_id() );
		delete_transient( 'stripe_customer_' . $this->get_id() );
		$this->customer_data = [];
	}

	/**
	 * Retrieves the Stripe Customer ID from the user meta.
	 *
	 * @param int $user_id The ID of the WordPress user.
	 *
	 * @return string|bool  Either the Stripe ID or false.
	 */
	public function get_id_from_meta( int $user_id ) {
		return get_user_option( '_stripe_customer_id', $user_id );
	}

	/**
	 * Updates the current user with the right Stripe ID in the meta table.
	 *
	 * @param string $id The Stripe customer ID.
	 */
	public function update_id_in_meta( $id ) {
		update_user_option( $this->get_user_id(), '_stripe_customer_id', $id, false );
	}

	/**
	 * Deletes the user ID from the meta table with the right key.
	 */
	public function delete_id_from_meta() {
		delete_user_option( $this->get_user_id(), '_stripe_customer_id', false );
	}

	/**
	 * Recreates the customer for this user.
	 *
	 * @return string ID of the new Customer object.
	 */
	private function recreate_customer() {
		$this->delete_id_from_meta();

		return $this->create_customer();
	}

	/**
	 * Get the customer's preferred locale based on the user or site setting.
	 *
	 * @param object $user The user being created/modified.
	 *
	 * @return array The matched locale string wrapped in an array, or empty default.
	 */
	public function get_customer_preferred_locale( $user ) {
		$locale = $this->get_customer_locale( $user );

		// Options based on Stripe locales.
		// https://support.stripe.com/questions/language-options-for-customer-emails
		$stripe_locales = [
			'ar'    => 'ar-AR',
			'da_DK' => 'da-DK',
			'de_DE' => 'de-DE',
			'en'    => 'en-US',
			'es_ES' => 'es-ES',
			'es_CL' => 'es-419',
			'es_AR' => 'es-419',
			'es_CO' => 'es-419',
			'es_PE' => 'es-419',
			'es_UY' => 'es-419',
			'es_PR' => 'es-419',
			'es_GT' => 'es-419',
			'es_EC' => 'es-419',
			'es_MX' => 'es-419',
			'es_VE' => 'es-419',
			'es_CR' => 'es-419',
			'fi'    => 'fi-FI',
			'fr_FR' => 'fr-FR',
			'he_IL' => 'he-IL',
			'it_IT' => 'it-IT',
			'ja'    => 'ja-JP',
			'nl_NL' => 'nl-NL',
			'nn_NO' => 'no-NO',
			'pt_BR' => 'pt-BR',
			'sv_SE' => 'sv-SE',
		];

		$preferred = isset( $stripe_locales[ $locale ] ) ? $stripe_locales[ $locale ] : 'en-US';

		return [ $preferred ];
	}

	/**
	 * Gets the customer's locale/language based on their setting or the site settings.
	 *
	 * @param object $user The user we're wanting to get the locale for.
	 *
	 * @return string The locale/language set in the user profile or the site itself.
	 */
	public function get_customer_locale( $user ) {
		// If we have a user, get their locale with a site fallback.
		return ( $user ) ? get_user_locale( $user->ID ) : get_locale();
	}
}
