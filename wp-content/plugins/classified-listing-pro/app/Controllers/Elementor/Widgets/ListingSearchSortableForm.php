<?php
/**
 * Main Elementor ListingCategoryBox Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 * @since 1.0.0
 */

namespace RtclPro\Controllers\Elementor\Widgets;


use Elementor\Controls_Manager;
use Rtcl\Abstracts\ElementorWidgetBaseV2;
use Rtcl\Helpers\Functions;
use Rtcl\Controllers\Elementor\WidgetSettings;
/**
 * ListingCategoryBox Class
 */
class ListingSearchSortableForm extends ElementorWidgetBaseV2 {

	/**
	 * Undocumented function
	 *
	 * @param array $data default array.
	 * @param mixed $args default arg.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->rtcl_name = __( 'Search Form - Sortable', 'classified-listing-pro' );
		$this->rtcl_base = 'rtcl-listing-search-sortable-form';
		parent::__construct( $data, $args );
	}

	/**
	 * Search from style
	 *
	 * @return array
	 */
	public function search_style() {
		$style = apply_filters(
			'rtcl_el_search_style',
			array(
				'dependency' => esc_html__( 'Dependency Selection', 'classified-listing-pro' ),
			)
		);
		return $style;
	}

	/**
	 * Search from style
	 *
	 * @return array
	 */
	public function search_oriantation() {
		$style = apply_filters(
			'rtcl_el_search_oriantation',
			array(
				'inline'   => __( 'Inline', 'classified-listing-pro' ),
				'vertical' => __( 'Vertical', 'classified-listing-pro' ),
			)
		);
		return $style;
	}

	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function widget_general_fields() : array {
		$form_fields = [
			'keyword_field'       => esc_html__( 'Keywords', 'classified-listing-pro' ),
			'location_field'       => esc_html__( 'Location', 'classified-listing-pro' ),
			'category_field'       => esc_html__( 'Category', 'classified-listing-pro' ),
			'types_field'       => esc_html__( 'Types', 'classified-listing-pro' ),
			'price_field'       => esc_html__( 'Price', 'classified-listing-pro' ),
		];
		$form_fields_default = [];
		foreach( $form_fields as $key => $value){
			$form_fields_default[] = [
				'sortable_form_fields' => $key,
				'sortable_form_field_Label' => $value,
			];
		}
		$sortable_form = [
			'sortable_form_fields'                 => [
				'label'     => esc_html__( 'Field\'s', 'classified-listing-pro' ),
				'type'      => 'select',
				'separator' => 'default',
				'default'   => 'keyword_field',
				'options'   => $form_fields,
			],
			'sortable_form_field_Label'         => [
				'label'     => esc_html__( 'Label', 'classified-listing-pro' ),
				'type'      => 'text',
				'separator' => 'default',
			],
			'sortable_form_field_placeholder'         => [
				'label'     => esc_html__( 'Placeholder', 'classified-listing-pro' ),
				'type'      => 'text',
				'separator' => 'default',
				'condition' => [
					'sortable_form_fields' => [ 'keyword_field' ]
				],
			],
			'sortable_field_width'             => [
				'label'     => esc_html__( 'Width', 'classified-listing' ),
				'type'      => 'slider',
				'size_units' => [ 'px', '%' ],
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 1500,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rtcl-widget-search-sortable-inline .form-group{{CURRENT_ITEM}}' => 'width:{{SIZE}}{{UNIT}}; flex: 0 0 {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rtcl-widget-search-sortable-vertical .form-group{{CURRENT_ITEM}}' => 'width:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'sortable_form_fields!' => [ 'price_field' ]
				],
			],
			'sortable_min_max_price_field_width'             => [
				'label'     => esc_html__( 'Min & Max Field Width', 'classified-listing' ),
				'type'      => 'slider',
				'size_units' => [ 'px', '%' ],
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rtcl-widget-search-sortable .form-group.price-field{{CURRENT_ITEM}}' => 'flex: 0 0 {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'sortable_form_fields' => [ 'price_field' ]
				],
			],
			
		];
		
		if ('geo' === Functions::location_type()) {
			$sortable_form['geo_location_range'] = [
				'type'      => 'switch',
				'label'     => __('Radius Search', 'classified-listing-pro'),
				'label_on'  => __('On', 'classified-listing-pro'),
				'label_off' => __('Off', 'classified-listing-pro'),
				'default'   => '',
				'condition' => [
					'sortable_form_fields' => 'location_field',
				],
			];
		}
		
		$fields = array(
			'rtcl_sec_general' => array(
				'mode'  => 'section_start',
				'label' => __( 'General', 'classified-listing-pro' ),
			),
			'search_style' => array(
				'type'    => 'select',
				'label'   => __( 'Style', 'classified-listing-pro' ),
				'options' => $this->search_style(),
				'default' => 'dependency',
			),
			'search_oriantation' => array(
				'type'    => 'select',
				'label'   => __( 'Oriantation', 'classified-listing-pro' ),
				'options' => $this->search_oriantation(),
				'default' => 'inline',
			),
			'fields_label' => array(
				'type'      => 'switch',
				'label'     => __( 'Show fields Label', 'classified-listing-pro' ),
				'label_on'  => __( 'On', 'classified-listing-pro' ),
				'label_off' => __( 'Off', 'classified-listing-pro' ),
				'default'   => 'yes',
			),
			
			'sortable_form'          => [
				'type'        => 'repeater',
				'mode'        => 'repeater',
				'label'       => esc_html__( 'Field Types', 'classified-listing-pro' ),
				'fields'      => $sortable_form,
				'default'     => $form_fields_default ,
				'title_field' => '{{{ sortable_form_field_Label }}}',
			],

			'button_icon_alignment'      => [
				'label'       => esc_html__( 'Icon Alignment', 'classified-listing-pro' ),
				'type'        => 'choose',
				'options'     => [
					'left' => [
						'title' => esc_html__( 'Left', 'classified-listing-pro' ),
						'icon'  => 'fas fa-angle-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'classified-listing-pro' ),
						'icon'  => 'fas fa-angle-right',
					],
				],
				'default'     => 'right',
			],
			'button_icon'    => [
				'label'            => esc_html__( 'Button Icon', 'classified-listing-pro' ),
				'type'             => 'icons',
				'separator'        => 'default',
			],
			'button_text'    => [
				'label'            => esc_html__( 'Button Text', 'classified-listing-pro' ),
				'type'             => 'text',
				'separator'        => 'default',
				'default' => esc_html__( 'Search', 'classified-listing-pro' ),
			],
			'rtcl_sec_general_end' => array(
				'mode' => 'section_end',
			),

		);
		return apply_filters( 'rtcl/elementor/widgets/controls/general/' . $this->rtcl_base , $fields, $this );

	}
	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		$button = WidgetSettings\ButtonSettings::style_settings();
		$form_fields = WidgetSettings\FormFieldSettings::fields_settings();
		$icons_settings = WidgetSettings\IconSettings::style_settings();
		
		$new_fields             = [
			'sortable_field_gap' => [
				'label'     => esc_html__( 'Field\'s Gap', 'classified-listing' ),
				'type'      => 'slider',
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'default'    => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .rtcl-widget-search-sortable .rtcl-widget-search-sortable-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		];
		
		$button['button_width']['selectors'] = [
			'{{WRAPPER}} .rtcl-widget-search-sortable .form-group.ws-button' => 'width: {{SIZE}}{{UNIT}};max-width:{{SIZE}}{{UNIT}};',
		];
		

		$form_fields = $this->insert_controls( 'fields_text_typo', $form_fields, $new_fields, true );

		$fields = array_merge(
			$form_fields,
			$button,
			$icons_settings
		);
		return apply_filters( 'rtcl/elementor/widgets/controls/style/' . $this->rtcl_base , $fields, $this );
	}
	
	/**
	 * Display Output.
	 *
	 * @return void
	 */
	protected function render() {
		$controllers = $this->get_settings();
		$search_style       = $controllers['search_style'] ?? 'dependency';
		$search_oriantation = $controllers['search_oriantation'] ?? 'inline';

		$data = array(
			'template'              => 'elementor/search/search-sortable',
			'id'                    => wp_rand(),
			'controllers'              => $controllers,
			'style'                 => $search_style,
			'orientation'           => $search_oriantation,
			'widget_base'     => $this->rtcl_base,
			'selected_category'     => false,
			'selected_location'     => false,
			'classes'               => array(
				'rtcl-widget-search-sortable',
				'rtcl-widget-search-sortable-' . $search_oriantation,
				'rtcl-widget-search-sortable-style-' . $search_style,
			),
			'default_template_path' => rtclPro()->get_plugin_template_path(),
		);
		$data = apply_filters( 'rtcl/elementor/search/data/' . $this->rtcl_base, $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
	}
}
