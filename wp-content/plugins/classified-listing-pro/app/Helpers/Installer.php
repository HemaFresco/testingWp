<?php

namespace RtclPro\Helpers;

class Installer {

	const MAX_INDEX_LENGTH = 191;
	/**
	 * DB updates and callbacks that need to be run per version.
	 *
	 * @var array
	 */
	private static array $db_updates = [
	];

	public static function init() {
		add_action( 'init', [ __CLASS__, 'check_version' ], 5 );
	}

	public static function check_version() {
		if ( version_compare( get_option( 'rtcl_pro_version' ), RTCL_PRO_VERSION, '<' ) ) {
			self::install();
			do_action( 'rtcl_pro_updated' );
		}
	}

	public static function install() {

		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'rtcl_pro_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'rtcl_pro_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		if ( ! get_option( 'rtcl_pro_version' ) ) {
			self::create_options();
		}
		self::create_tables();
		self::update_rtcl_version();
		self::maybe_update_db_version();

		delete_transient( 'rtcl_pro_installing' );

		do_action( 'rtcl_flush_rewrite_rules' );
		do_action( 'rtcl_pro_installed' );

	}


	/**
	 * Get list of DB update callbacks.
	 *
	 * @return array
	 * @since  3.0.0
	 */
	public static function get_db_update_callbacks(): array {
		return self::$db_updates;
	}

	/**
	 * Is a DB update needed?
	 *
	 * @return boolean
	 */
	public static function needs_db_update(): bool {
		$current_db_version = get_option( 'rtcl_pro_db_version', null );
		$updates            = self::get_db_update_callbacks();
		$update_versions    = array_keys( $updates );
		usort( $update_versions, 'version_compare' );

		return ! is_null( $current_db_version ) && version_compare( $current_db_version, end( $update_versions ), '<' );
	}

	/**
	 * See if we need to show or run database updates during install.
	 *
	 * @since 3.2.0
	 */
	private static function maybe_update_db_version() {
		if ( self::needs_db_update() ) {
			self::update();
		} else {
			self::update_db_version();
		}
	}

	private static function update() {
		$current_db_version = get_option( 'rtcl_pro_db_version' );
		$loop               = 0;

		foreach ( self::get_db_update_callbacks() as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					if ( is_callable( self::class, $update_callback ) ) {
						self::$update_callback();
					}
				}
			}
		}
	}

	/**
	 * Update DB version to current.
	 *
	 * @param string|null $version New WooCommerce DB version or null.
	 */
	public static function update_db_version( string $version = null ) {
		update_option( 'rtcl_pro_db_version', is_null( $version ) ? RTCL_PRO_VERSION : $version );
	}

	private static function create_options() {
		// General settings update
		$isDirty   = false;
		$gSettings = get_option( 'rtcl_general_settings' );
		if ( ! isset( $gSettings['compare_limit'] ) ) {
			$gSettings['compare_limit'] = 3;
			$isDirty                    = true;
		}
		if ( ! isset( $gSettings['default_view'] ) ) {
			$gSettings['default_view'] = 'list';
			$isDirty                   = true;
		}
		if ( ! isset( $gSettings['location_type'] ) ) {
			$gSettings['location_type'] = 'local';
			$isDirty                    = true;
		}
		if ( $isDirty ) {
			update_option( 'rtcl_general_settings', $gSettings );
		}

		// Moderation settings update
		$isDirty   = false;
		$mSettings = get_option( 'rtcl_moderation_settings' );
		if ( ! isset( $mSettings['listing_top_per_page'] ) ) {
			$mSettings['listing_top_per_page'] = 2;
			$isDirty                           = true;
		}
		if ( ! isset( $mSettings['popular_listing_threshold'] ) ) {
			$mSettings['popular_listing_threshold'] = 1000;
			$isDirty                                = true;
		}
		if ( $isDirty ) {
			update_option( 'rtcl_moderation_settings', $mSettings );
		}

		// Account settings update
		$isDirty   = false;
		$aSettings = get_option( 'rtcl_account_settings' );
		if ( ! isset( $aSettings['verify_max_resend_allowed'] ) ) {
			$aSettings['verify_max_resend_allowed'] = 5;
			$isDirty                                = true;
		}
		if ( ! isset( $aSettings['popular_listing_threshold'] ) ) {
			$aSettings['popular_listing_threshold'] = 1000;
			$isDirty                                = true;
		}
		if ( $isDirty ) {
			update_option( 'rtcl_account_settings', $aSettings );
		}

		// advanced settings update
		$advDirty    = false;
		$advSettings = get_option( 'rtcl_advanced_settings' );
		if ( ! isset( $advSettings['myaccount_chat_endpoint'] ) ) {
			$advSettings['myaccount_chat_endpoint'] = 'chat';
			$advDirty                               = true;
		}
		if ( ! isset( $advSettings['myaccount_verify'] ) ) {
			$advSettings['myaccount_verify'] = 'verify';
			$advDirty                        = true;
		}
		if ( $advDirty ) {
			update_option( 'rtcl_advanced_settings', $advSettings );
		}
	}

	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$tables = array_merge( self::get_chat_table_schema(), self::get_pushNotification_table_schema(), self::get_subscription_table_schema() );
		dbDelta( $tables );
	}

	/**
	 * @return array
	 */
	static function get_pushNotification_table_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}
		$push_notifications_table_name = $wpdb->prefix . "rtcl_push_notifications";
		$table_schema                  = [];
		$max_index_length              = self::MAX_INDEX_LENGTH;

		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $push_notifications_table_name ) ) !== $push_notifications_table_name ) {
			$table_schema[] = "CREATE TABLE $push_notifications_table_name (
                      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                      push_token varchar(255) NOT NULL,
                      user_id int(10) UNSIGNED DEFAULT NULL,
                      events longtext DEFAULT NULL,
                      created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      PRIMARY KEY (id),
                      UNIQUE KEY push_token (push_token($max_index_length))
                      ) $collate;";
		}

		return $table_schema;
	}

	/**
	 * @return array
	 */
	static function get_chat_table_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}
		$conversation_table_name         = $wpdb->prefix . "rtcl_conversations";
		$conversation_message_table_name = $wpdb->prefix . "rtcl_conversation_messages";
		$table_schema                    = [];

		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $conversation_table_name ) ) !== $conversation_table_name ) {
			$table_schema[] = "CREATE TABLE $conversation_table_name (
                          con_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                          listing_id BIGINT UNSIGNED NOT NULL,
                          sender_id int(10) UNSIGNED NOT NULL,
                          recipient_id int(10) UNSIGNED NOT NULL,
                          sender_delete tinyint(1) NOT NULL DEFAULT '0',
                          recipient_delete tinyint(1) NOT NULL DEFAULT '0',
                          last_message_id int(10) UNSIGNED DEFAULT NULL,
                          sender_review tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
                          recipient_review tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
                          invert_review tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
                          created_at timestamp NOT NULL,
                          PRIMARY KEY (con_id)
                        ) $collate;";
		}
		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $conversation_message_table_name ) ) !== $conversation_message_table_name ) {
			$table_schema[] = "CREATE TABLE $conversation_message_table_name (
                      message_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                      con_id bigint(20) unsigned NOT NULL,
                      source_id int(10) unsigned NOT NULL,
                      message longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                      is_read tinyint(1) NOT NULL DEFAULT '0',
                      created_at timestamp NOT NULL,
                      PRIMARY KEY (message_id),
                      KEY con_id (con_id)
                    ) $collate;";
		}

		return $table_schema;
	}

	/**
	 * @return array
	 */
	static function get_subscription_table_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}
		$subscriptions_table      = $wpdb->prefix . 'rtcl_subscriptions';
		$subscription_items_table = $wpdb->prefix . 'rtcl_subscription_items';
		$subscription_meta_table  = $wpdb->prefix . 'rtcl_subscription_meta';
		$table_schema             = [];

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $subscriptions_table ) ) !== $subscriptions_table ) {
			$table_schema[] = "CREATE TABLE `$subscriptions_table` (
                                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                user_id int(11) NOT NULL,
                                name varchar(191),
                                sub_id varchar(191),
                                occurrence int(191) DEFAULT 0,
                                gateway_id varchar(191),
                                status varchar(191),
                                product_id int(11) NOT NULL,
                                quantity int(191) NULL,
                                price varchar(191),
                                meta longtext,
                                expiry_at DATETIME NOT NULL,
                                created_at DATETIME NOT NULL,
                                updated_at DATETIME NOT NULL,
                                KEY user_id (user_id)
                            ) $collate;";
		}


//		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $subscription_items_table ) ) !== $subscription_items_table ) {
//			$table_schema[] = "CREATE TABLE `$subscription_items_table` (
//    							id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
//                                subscription_id bigint(20) unsigned NOT NULL
//                            ) $collate;";
//		}
//
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $subscription_meta_table ) ) !== $subscription_meta_table ) {

			$max_index_length = self::MAX_INDEX_LENGTH;
			$table_schema[]   = "CREATE TABLE `$subscription_meta_table` (
                                meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                subscription_id bigint(20) unsigned NOT NULL,
                                meta_key varchar(191) NOT NULL,
                                meta_value longtext,
                                KEY subscription_id (subscription_id),
                                KEY meta_key (meta_key($max_index_length))
                            ) $collate;";
		}

		return $table_schema;
	}


	private static function update_rtcl_version() {
		delete_option( 'rtcl_pro_version' );
		add_option( 'rtcl_pro_version', RTCL_PRO_VERSION );
	}

	public static function deactivate() {

	}
}