<?php
/**
 * Main Elementor ListingArchiveSettings Class
 *
 * ListingArchiveSettings main class
 *
 * @author  RadiusTheme
 * @since   2.0.10
 * @package  RTCL_Elementor_Builder
 * @version 1.2
 */

namespace RtclElb\Widgets\WidgetSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Rtcl\Abstracts\ElListingsWidgetBase;
use Rtcl\Controllers\Elementor\ELWidgetsTraits\{
	ListingStyleTrait,
	ArchiveGeneralTrait
};
/**
 * ListingArchiveSettings class
 */
class ListingArchiveSettings extends ElListingsWidgetBase {
	use ListingStyleTrait;
	/**
	 * Action General Section.
	 */
	use ArchiveGeneralTrait;
	/**
	 * Undocumented function
	 *
	 * @param array $data default data.
	 * @param array $args default arg.
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->rtcl_category = 'rtcl-elementor-archive-widgets'; // Category.
	}
	/**
	 * Set field controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		$fields = array_merge(
			$this->archive_general_fields(),
			$this->listing_layout_control(),
			$this->listing_content_visibility_fields(),
			// $this->listing_responsive_control()
		);
		return $fields;
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->widget_listing_wrapper(),
			$this->listing_promotion_section(),
			$this->widget_style_image_wrapper(),
			$this->widget_style_sec_title(),
			$this->widget_style_sec_meta(),
			$this->widget_style_sec_description(),
			$this->widget_style_sec_price(),
			$this->widget_style_badge_section(),
			$this->widget_style_action_button(),
			$this->widget_style_sec_pagination()
		);
		return apply_filters('rtcl_el_archive_listing_widget_style_field', $fields, $this);
	}
	/**
	 * Set Layout controlls
	 *
	 * @return array
	 */
	public function listing_layout_control() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_layout_general',
				'label' => __( 'Layout', 'rtcl-elementor-builder' ),
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_layout_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Default View', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			[
				'type'    => 'rtcl-image-selector',
				'id'      => 'rtcl_listings_view',
				'options' => $this->listings_view(),
				'default' => 'list',
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'listings_style_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'List Style', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
				'conditions'      => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '===',
									'value'    => 'list',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '!==',
									'value'    => 'list',
								],
								[
									'name'     => 'rtcl_archive_view_switcher',
									'operator' => '===',
									'value'    => 'yes',
								],
							],
						],
					],
				],
			],
			[
				'type'       => 'rtcl-image-selector',
				'id'         => 'rtcl_listings_style',
				'options'    => $this->list_style(),
				'default'    => 'style-1',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '===',
									'value'    => 'list',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '!==',
									'value'    => 'list',
								],
								[
									'name'     => 'rtcl_archive_view_switcher',
									'operator' => '===',
									'value'    => 'yes',
								],
							],
						],
					],
				],
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'listings_grid_style_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Grid Style', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
				'conditions'      => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '===',
									'value'    => 'grid',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '!==',
									'value'    => 'grid',
								],
								[
									'name'     => 'rtcl_archive_view_switcher',
									'operator' => '===',
									'value'    => 'yes',
								],
							],
						],
					],
				],
			],
			[
				'type'       => 'rtcl-image-selector',
				'id'         => 'rtcl_listings_grid_style',
				'options'    => $this->grid_style(),
				'default'    => 'style-1',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '===',
									'value'    => 'grid',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '!==',
									'value'    => 'grid',
								],
								[
									'name'     => 'rtcl_archive_view_switcher',
									'operator' => '===',
									'value'    => 'yes',
								],
							],
						],
					],
				],
			],
			[
				'type'       => Controls_Manager::SELECT,
				'mode'       => 'responsive',
				'id'         => 'rtcl_listings_column',
				'label'      => __( 'Grid Column', 'rtcl-elementor-builder' ),
				'options'    => $this->column_number(),
				'default'    => '3',
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '===',
									'value'    => 'grid',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '!==',
									'value'    => 'grid',
								],
								[
									'name'     => 'rtcl_archive_view_switcher',
									'operator' => '===',
									'value'    => 'yes',
								],
							],
						],
					],
				],
			],
			[
				'mode' => 'section_end',
			],

		];

		return $fields;
	}


}

