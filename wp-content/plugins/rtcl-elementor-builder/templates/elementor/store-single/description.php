<?php
/**
 * Store single content
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.3.21
 */

$store_description = $store->get_the_description();

if ( $store_description ) : ?>
	<div class="store-details store-description-content">
		<p><?php echo esc_html( $store_description ); ?></p>
	</div>
	<?php
endif;

