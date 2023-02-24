<?php
/**
 * The template to display the Social profile
 *
 * @author  RadiousTheme
 *
 * @var Rtcl\Models\Listing $listing
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use RtclPro\Helpers\Fns;

$total_comments = \Elementor\Plugin::$instance->editor->is_edit_mode() ? 2 : get_option('comments_per_page');
// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
$comments  = get_comments(
	[
		'post_id' => $listing->get_id(),
		'number'  => $total_comments,
		'status'  => 'approve', // Change this to the type of comments to be displayed.
	]
); // Return array.
$classes[] = !empty($instance['rtcl_show_comment_list']) ? 'displayed-comment-list' : 'no-comment-list';
$classes[] = !empty($instance['rtcl_show_comment_form']) ? 'displayed-comment-form' : 'no-comment-form';
?>
<div class="el-single-addon rtcl-Reviews rtcl <?php echo esc_attr(implode(' ', $classes)); ?>">
	<?php if ($instance['rtcl_show_comment_list']) { ?>
	<div id="comments">
		<?php $has_header_content = false;
		if (!empty($instance['rtcl_show_review_title']) ||
			!empty($instance['rtcl_review_meta']) ||
			!empty($instance['rtcl_show_leave_review_button'])
		) {
			$has_header_content = true;
		}
		?>
		<?php if ($has_header_content) { ?>
		<div class="rtcl-reviews-meta">
			<?php if (!empty($instance['rtcl_show_review_title'])) { ?>
				<h4 class="rtcl-single-listing-section-title">
					<?php echo esc_html($instance['rtcl_review_title_text']); ?>
				</h4>
			<?php } ?>
			<?php
			if (count($comments) && !empty($instance['rtcl_review_meta'])) {
				$average      = $listing->get_average_rating();
				$rating_count = $listing->get_rating_count(); ?>
				<!-- Single Listing Review / Meta -->
				<div class="listing-meta">
					<!-- Listing / Rating -->
					<div class="listing-meta-rating"><?php echo esc_html($average); ?></div>
					<div class="reviews-rating">
						<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo Fns::get_rating_html($average, $rating_count); ?>
						<span class="reviews-rating-count">(<?php echo absint($rating_count); ?>)</span>
					</div>
				</div>
			<?php
			} ?>
			<?php if (!empty($instance['rtcl_show_leave_review_button'])) { ?>
			<div class="rtcl-reviews-meta-action">
				<a class="rtcl-animate" href="#respond"><?php echo esc_html($instance['rtcl_leave_review_button_text']); ?><i class="rtcl-icon-level-down"></i></a>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
		<?php if (count($comments)) { ?>
			<?php
				$comment_list_style = !empty($instance['comment_list_style']) ? 'list-style-'.$instance['comment_list_style'].' ' : '';
				// $comment_list_style .= !empty($instance['comment_list_style_tablet']) ? 'list-style-tab-'.$instance['comment_list_style_tablet'].' ' : ' ';
				// $comment_list_style .= !empty($instance['comment_list_style_mobile']) ? 'list-style-mobile-'.$instance['comment_list_style_mobile'].' ' : '';

			?>
			<ol class="comment-list <?php echo esc_attr($comment_list_style); ?>">
				<?php
					wp_list_comments(
						apply_filters(
							'rtcl_listing_review_list_args',
							[
								'callback' => [
									Fns::class,
									'comments',
								],
							]
						),
						$comments
					);
				?>
			</ol>

			<?php
			if (get_comment_pages_count() > 1 && get_option('page_comments')) {
				echo '<nav class="rtcl-pagination">';
				paginate_comments_links(
					apply_filters(
						'rtcl_comment_pagination_args',
						[
							'prev_text' => '&larr;',
							'next_text' => '&rarr;',
							'type'      => 'list',
						]
					)
				);
				echo '</nav>';
			}
			?>

		<?php } else { ?>
			<p class="rtcl-noreviews"><?php esc_html_e('There are no reviews yet.', 'rtcl-elementor-builder'); ?></p>
		<?php } ?>
		
	</div>
	<?php } ?>
	<?php if ($instance['rtcl_show_comment_form']) { ?>
	<div id="review-form-wrapper">
		<div id="review-form">
			<?php
			$comment_form_title = isset( $instance['rtcl_comment_form_title_text'] ) ? $instance['rtcl_comment_form_title_text'] : '' ;
			$commenter = wp_get_current_commenter();

			$comment_form     = [
				// translators: %s: Listing Litle
				'title_reply'         => $comments ? $comment_form_title : sprintf(__('Be the first to review &ldquo;%s&rdquo;', 'rtcl-elementor-builder'), get_the_title()),
				// translators: %s: Autor Name
				'title_reply_to'      => __('Leave a Reply to %s', 'rtcl-elementor-builder'),
				'title_reply_before'  => '<h4 id="reply-title" class="comment-reply-title">',
				'title_reply_after'   => '</h4>',
				'comment_notes_after' => '',
				'fields'              => [
					'author' => '<div class="comment-form-author form-group"><label for="author">'.esc_html__('Name', 'rtcl-elementor-builder').'&nbsp;<span class="required">*</span></label> '.
								'<input id="author" class="form-control" name="author" type="text" value="'.esc_attr($commenter['comment_author']).'" size="30" aria-required="true" required /></div>',
					'email'  => '<div class="comment-form-email form-group"><label for="email">'.esc_html__('Email', 'rtcl-elementor-builder').'&nbsp;<span class="required">*</span></label> '.
								'<input id="email" name="email" class="form-control" type="email" value="'.esc_attr($commenter['comment_author_email']).'" size="30" aria-required="true" required /></div>',
				],
				'label_submit'        => esc_html__('Submit', 'rtcl-elementor-builder'),
				'class_submit'        => 'btn btn-primary',
				'logged_in_as'        => '',
				'comment_field'       => '',
			];
			$account_page_url = Link::get_my_account_page_link();
			if ($account_page_url) {
				// translators: %s: Account page url
				$comment_form['must_log_in'] = '<p class="must-log-in">'.sprintf(__('You must be <a href="%s">logged in</a> to post a review.', 'rtcl-elementor-builder'), esc_url($account_page_url)).'</p>';
			}

				$comment_form['comment_field'] = '<div class="comment-form-title  form-group"><label for="title">'.esc_html__('Review title', 'rtcl-elementor-builder').'&nbsp;<span class="required">*</span></label><input type="text" class="form-control" name="title" id="title"  aria-required="true" required/></div>';
			if (Functions::get_option_item('rtcl_moderation_settings', 'enable_review_rating', false, 'checkbox') && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
				$comment_form['comment_field'] .= '<div class="comment-form-rating  form-group"><label for="rating">'.esc_html__('Your rating', 'rtcl-elementor-builder').'<span class="required">*</span></label><select name="rating" id="rating" class="form-control" aria-required="true" required>
								<option value="">'.esc_html__('Rate&hellip;', 'rtcl-elementor-builder').'</option>
								<option value="5">'.esc_html__('Perfect', 'rtcl-elementor-builder').'</option>
								<option value="4">'.esc_html__('Good', 'rtcl-elementor-builder').'</option>
								<option value="3">'.esc_html__('Average', 'rtcl-elementor-builder').'</option>
								<option value="2">'.esc_html__('Not that bad', 'rtcl-elementor-builder').'</option>
								<option value="1">'.esc_html__('Very poor', 'rtcl-elementor-builder').'</option>
							</select></div>';
			}

			$comment_form['comment_field'] .= '<div class="comment-form-comment  form-group"><label for="comment">'.esc_html__('Your review', 'rtcl-elementor-builder').'&nbsp;<span class="required">*</span></label><textarea id="comment" class="form-control" name="comment" cols="45" rows="8" aria-required="true" required></textarea></div>';

			comment_form(apply_filters('rtcl_listing_review_comment_form_args', $comment_form), $listing->get_id());
			?>
		</div>
	</div>
	<?php } ?>

</div>

