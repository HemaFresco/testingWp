<?php
/**
 * Modal
 *
 * @author     RadiusTheme
 * @package    rtcl-elementor-builder/templates
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
if ( empty( $store ) ) {
	global $store;
}

$store_oh_type  = get_post_meta( $store->get_id(), 'oh_type', true );
$store_oh_hours = get_post_meta( $store->get_id(), 'oh_hours', true );
$store_oh_hours = is_array( $store_oh_hours ) ? $store_oh_hours : ( $store_oh_hours ? (array) $store_oh_hours : [] );
$today          = strtolower( date( 'l' ) );
?>
<!-- Modal -->
<div class="modal fade rtcl-bs-modal" id="store-details-modal" tabindex="-1" role="dialog" aria-labelledby="store-details-modal-label" aria-hidden="true" >
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title" id="store-details-modal-label">
					<?php // $store->the_title(); ?>
                    <?php esc_html_e( 'Open Hours', 'rtcl-elementor-builder' ); ?>
				</h2>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="store-more-details">
					<div class="more-item store-hours-list-wrap">
						<div class="store-hours-list">
							<?php if ( $store_oh_type == 'selected' ) : ?>
								<?php if ( is_array( $store_oh_hours ) && ! empty( $store_oh_hours ) ) : ?>
									<?php foreach ( $store_oh_hours as $hKey => $oh_hour ) : ?>
										<div class="row store-hour<?php echo esc_attr( ( $hKey == $today ) ? ' current-store-hour' : '' ); ?>">
											<div class="col-4">
												<span class="hour-day"><?php echo esc_html( $hKey ); ?></span>
											</div>
											<div class="col-8 oh-hours-wrap">
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
							<?php elseif ( $store_oh_type == 'always' ) : ?>
								<div class="always-open"><?php esc_html_e( 'Always Open', 'rtcl-elementor-builder' ); ?></div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
