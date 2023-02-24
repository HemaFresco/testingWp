<?php
/**
 * Main Elementor ActionHooks Class
 *
 * The main class that filter the functionality.
 *
 * @since 1.0.0
 */


namespace RtclElb\Controllers\Hooks;

use Rtcl\Controllers\Hooks\TemplateHooks;
use RtclElb\Helpers\Fns;
use RtclStore\Helpers\Functions as StoreFunctions;
use RtclStore\Controllers\Hooks\TemplateHooks as StoreTemplateHooks;

/**
 * ActionHooks class
 */
class ActionHooks {
	/**
	 * Initialize function.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'rtcl_single_listing_display_map', [ TemplateHooks::class, 'single_listing_map_content' ] );
		add_action( 'rtcl_listing_seller_information', [ __CLASS__, 'show_author' ], 1 );
		add_action( 'rtcl_after_store_hours_text', [ __CLASS__, 'after_store_hours' ], 1 );
		add_action( 'elementor/editor/init', [ __CLASS__, 'is_builder_page' ] );
	}

	/**
	 * Template redirect hooks
	 *
	 * @return void
	 */
	public static function after_store_hours() { ?>
		<div class="fade-anchor">
			<a href="#" class="fade-anchor-text">
				<?php esc_html_e( 'See all timings', 'rtcl-elementor-builder' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Template redirect hooks
	 *
	 * @return void
	 */
	public static function is_builder_page() {
		if ( Fns::is_store_page_builder() ) {
			StoreTemplateHooks::init();
		}
	}

	/**
	 * Show autor name.
	 *
	 * @param [obj] $listing functionality.
	 * @return mixed
	 */
	public static function show_author( $listing ) {
		$store = false;
		if ( class_exists( 'RtclStore' ) ) {
			$store = StoreFunctions::get_user_store( $listing->get_author_id() );
		}
		?>
		<div class="listing-author">
			<?php if ( $store ) : ?>
				<div class="author-logo-wrapper">
					<?php $store->the_logo(); ?>
				</div>
				<h4 class="author-name"><a href="<?php $store->the_permalink(); ?>"><?php $listing->the_author(); ?></a></h4>
			<?php else : ?>
				<div class="author-logo-wrapper">
					<?php $listing->the_author_logo(); ?>
				</div>
				<h4 class="author-name"><?php $listing->the_author(); ?></h4>
			<?php endif; ?>
		</div>
		<?php
	}

}
