<?php
/**
 * Main Elementor ElementorMainController Class.
 *
 * The main class that initiates and runs the plugin.
 *
 * @since    1.0.0
 */

namespace RtclElb\Controllers;

use Rtcl\Controllers\SocialProfilesController;
use RtclElb\Traits\ELTempleateBuilderTraits;
use RtclElb\Traits\Singleton;
use RtclElb\Widgets\ListingActions;
use RtclElb\Widgets\ListingArchive;
use RtclElb\Widgets\ListingBadge;
use RtclElb\Widgets\ListingBusinessHours;
use RtclElb\Widgets\ListingCustomFields;
use RtclElb\Widgets\ListingDescription;
use RtclElb\Widgets\ListingImage;
use RtclElb\Widgets\ListingMap;
use RtclElb\Widgets\ListingMeta;
use RtclElb\Widgets\ListingPageHeader;
use RtclElb\Widgets\ListingPrice;
use RtclElb\Widgets\ListingReview;
use RtclElb\Widgets\ListingSellerInformation;
use RtclElb\Widgets\ListingSocialProfiles;
use RtclElb\Widgets\ListingTitle;
use RtclElb\Widgets\ListingVideo;
use RtclElb\Widgets\RelatedListings;

use RtclElb\Widgets\StoreName;
use RtclElb\Widgets\StoreBanner;
use RtclElb\Widgets\StoreListings;
use RtclElb\Widgets\StoreDescription;
use RtclElb\Widgets\StoreSlogan;
use RtclElb\Widgets\StoreContactInfo;
use RtclElb\Widgets\StoreOpeningHours;
use RtclPro\Controllers\Hooks\TemplateHooks;
use RtclPro\Controllers\Hooks\TemplateLoader;

use RtclElb\Controllers\Hooks\ActionHooks;

/**
 * Main Elementor ElementorMainController Class.
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
class ElementorController {

	/*
	 * Singleton Function.
	 */
	use Singleton;

	/*
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;

	/**
	 * Initialize all hooks function.
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'rtcl_el_widget_for_classified_listing', [ $this, 'el_widget_for_classified_listing' ], 10 );
		add_filter( 'rtcl_elementor_widgets_category_lists', [ $this, 'add_new_categories' ], 9, 1 );
		ActionHooks::init();
		add_action(
			'elementor/widgets/widgets_registered',
			function () {
				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
					TemplateHooks::init();
					add_action( 'init', [ TemplateLoader::class, 'init' ] );
					add_action( 'rtcl_single_listing_social_profiles', [ SocialProfilesController::class, 'display_social_profiles' ] );
				}
			}
		);
	}

	/**
	 * Elementor category list.
	 *
	 * @param [array] $el_categories category list
	 *
	 * @return array
	 */
	public function add_new_categories( $el_categories ) {
		$categories = [];
		if ( self::is_builder_page_single() ) {
			$categories['rtcl-elementor-single-widgets'] = [
				'title' => __( 'Single Listing', 'rtcl-elementor-builder' ),
				'icon'  => 'fa fa-plug',
			];
		}
		if ( self::is_builder_page_archive() ) {
			$categories['rtcl-elementor-archive-widgets'] = [
				'title' => __( 'Archive Listing', 'rtcl-elementor-builder' ),
				'icon'  => 'fa fa-plug',
			];
		}
		if ( self::is_store_page_builder() ) {
			$categories['rtcl-elementor-store-single'] = [
				'title' => __( 'Store Single', 'rtcl-elementor-builder' ),
				'icon'  => 'fa fa-plug',
			];
		}

		return array_merge( $categories, $el_categories );
	}

	/**
	 * Undocumented function.
	 *
	 * @param [type] $class_list main data.
	 *
	 * @return array
	 */
	public function el_widget_for_classified_listing( $class_list ) {
		$el_classes = [];
		if ( self::is_builder_page_archive() || self::is_builder_page_single() ) {
			$el_classes[] = ListingPageHeader::class;
		}
		if ( self::is_builder_page_archive() ) {
			$el_classes[] = ListingArchive::class;
		}
		if ( self::is_builder_page_single() ) {
			$el_classes[] = ListingTitle::class;
			$el_classes[] = ListingImage::class;
			$el_classes[] = ListingDescription::class;
			$el_classes[] = ListingMeta::class;
			$el_classes[] = ListingBadge::class;
			$el_classes[] = ListingPrice::class;
			$el_classes[] = ListingCustomFields::class;
			$el_classes[] = ListingActions::class;
			$el_classes[] = ListingMap::class;
			$el_classes[] = ListingSellerInformation::class;
			$el_classes[] = ListingBusinessHours::class;
			$el_classes[] = ListingVideo::class;
			$el_classes[] = ListingSocialProfiles::class;
			$el_classes[] = RelatedListings::class;
			$el_classes[] = ListingReview::class;
		}
		if ( self::is_store_page_builder() ) {
			$el_classes[] = StoreName::class;
			$el_classes[] = StoreBanner::class;
			$el_classes[] = StoreDescription::class;
			$el_classes[] = StoreSlogan::class;
			$el_classes[] = StoreOpeningHours::class;
			$el_classes[] = StoreContactInfo::class;
			$el_classes[] = StoreListings::class;
		}
		$class_list = array_merge(
			$class_list,
			$el_classes
		);

		return $class_list;
	}
}
