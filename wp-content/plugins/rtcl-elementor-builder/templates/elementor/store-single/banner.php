<?php
/**
 * Store single content
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.3.21
 */

use Rtcl\Helpers\Functions;

?>
<div class="rtcl store-content-wrap">
	<div class="store-banner">
		<div class="banner"><?php $store->the_banner(); ?></div>
		<div class="store-name-logo">
			
			<?php if ( ! empty( $instance['rtcl_show_store_logo'] ) ) : ?>
				<div class="store-logo"><?php $store->the_logo(); ?></div>
			<?php endif; ?>
			<div class="store-info">
				<?php if ( ! empty( $instance['rtcl_show_store_name'] ) ) : ?>
				<div class="store-name"><h2><?php $store->the_title(); ?></h2></div>
				<?php endif; ?>
				<?php if ( ! empty( $instance['rtcl_show_category'] ) && $store->get_category() ) : ?>
					<div class="rtcl-store-cat">
						<i class="rtcl-icon rtcl-icon-tags"></i>
						<?php Functions::print_html( $store->get_category() ); ?>
					</div>
				<?php endif; ?>
				<?php
				if ( ! empty( $instance['rtcl_show_rating'] ) ) {
					$review_counts = $store->get_review_counts();
					if ( $store->is_rating_enable() && $review_counts ) :
						?>
					<div class="reviews-rating">
							<?php echo Functions::get_rating_html( $store->get_average_rating(), $review_counts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> 
						<span class="reviews-rating-count">(<?php echo absint( $review_counts ); ?>)</span>
					</div>
						<?php
				endif;
				}
				?>
			</div>
		</div>
	</div>
</div>
