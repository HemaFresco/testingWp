<?php
/**
 * Modal
 *
 * @author     RadiusTheme
 * @package    classified-listing-store/templates
 * @version    1.0.0
 *
 * @var Store  $store
 * @var string $store_oh_type
 * @var array  $store_oh_hours
 * @var string $today
 */

use Rtcl\Helpers\Functions;
use RtclStore\Models\Store;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$store_oh_type  = get_post_meta( $store->get_id(), 'oh_type', true );
$store_oh_hours = get_post_meta( $store->get_id(), 'oh_hours', true );
$store_oh_hours = is_array( $store_oh_hours ) ? $store_oh_hours : ( $store_oh_hours ? (array) $store_oh_hours : [] );
$today          = strtolower( date( 'l' ) );
?>
<div class="store-hours-list-wrap store-hours-list">
	<?php if ( 'selected' === $store_oh_type ) : ?>
		<?php if ( is_array( $store_oh_hours ) && ! empty( $store_oh_hours ) ) : ?>
			<?php foreach ( $store_oh_hours as $hKey => $oh_hour ) : ?>
				<div class="store-hour<?php echo esc_attr( ( $hKey == $today ) ? ' current-store-hour' : '' ); ?>">
					<div class=" hour-day"><?php echo esc_html( $hKey ); ?> 
					</div>
					<div class="oh-hours-wrap">
						<?php if ( isset( $oh_hour['active'] ) ) : ?>
							<div class="oh-hours">
								<span class="open-hour"><?php echo isset( $oh_hour['open'] ) ? esc_html( $oh_hour['open'] ) : ''; ?></span>
								<span class="close-hour"><?php echo isset( $oh_hour['close'] ) ? esc_html( $oh_hour['close'] ) : ''; ?></span>
							</div>
						<?php else : ?>
							<span class="off-day"><?php esc_html_e( 'Closed', 'rtcl-elementor-builder' ); ?></span>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="always-open"><?php esc_html_e( 'Permanently Close', 'rtcl-elementor-builder' ); ?></div>
		<?php endif; ?>
	<?php elseif ( 'always' === $store_oh_type ) : ?>
		<div class="always-open"><?php esc_html_e( 'Always Open', 'rtcl-elementor-builder' ); ?></div>
	<?php endif; ?>
</div>

