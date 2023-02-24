<?php
/**
 * @var number  $id    Random id
 * @var         $orientation
 * @var         $style [classic , modern]
 * @var array   $classes
 * @var int     $active_count
 * @var WP_Term $selected_location
 * @var WP_Term $selected_category
 * @var bool    $radius_search
 * @var bool    $can_search_by_location
 * @var bool    $can_search_by_category
 * @var array   $data
 * @var bool    $can_search_by_listing_types
 * @var bool    $can_search_by_price
 * @var bool    $controllers
 * @var bool    $widget_base
 * @var $repeater_id
 * @var $field_Label
 * @var $placeholder
 */

$is_vertical = 'vertical' === $orientation ;
?>
<?php if( $is_vertical ){ ?>
<div class="rtcl-flex price-fields-wrapper elementor-repeater-item-<?php echo esc_attr( $repeater_id ); ?>">
<?php } ?>
	<div class="form-group ws-item ws-price price-field rtcl-flex rtcl-flex-column elementor-repeater-item-<?php echo esc_attr( $repeater_id ); ?>">
		<?php if ( $controllers['fields_label'] ) { ?>
			<label for="rtcl-search-price-min"><?php esc_html_e( 'Min Price', 'classified-listing-pro' ); ?></label>
		<?php } ?>
		<?php $min_price = isset( $_GET['filters']['price']['min'] ) ? $_GET['filters']['price']['min'] : ''; ?>
		<input id='rtcl-search-price-min' type="text" name="filters[price][min]" class="form-control" placeholder="<?php esc_html_e( 'min', 'classified-listing-pro' ); ?>" value="<?php echo esc_attr( $min_price ); ?>">
	</div>
	<div class="form-group rtcl-flex rtcl-flex-column ws-item ws-price  price-field elementor-repeater-item-<?php echo esc_attr( $repeater_id ); ?>">
		<?php if ( $controllers['fields_label'] ) { ?>
			<label for="rtcl-search-price-max"><?php esc_html_e( 'Max Price', 'classified-listing-pro' ); ?></label>
		<?php } ?>
		<?php $max_price = isset( $_GET['filters']['price']['max'] ) ? $_GET['filters']['price']['max'] : ''; ?>
		<input id='rtcl-search-price-max' type="text" name="filters[price][max]" class="form-control" placeholder="<?php esc_html_e( 'max', 'classified-listing-pro' ); ?>" value="<?php echo esc_attr( $max_price ); ?>" >
	</div>
<?php if( $is_vertical ){ ?>
</div>
<?php } ?>