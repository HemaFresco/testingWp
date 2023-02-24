<?php
/**
 * Main Elementor ListingReviewSettings Class
 *
 * ListingReviewSettings main class
 *
 * @author  RadiusTheme
 *
 * @since   2.0.10
 *
 * @version 1.2
 */

namespace RtclElb\Widgets\WidgetSettings;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use RtclElb\Abstracts\ElementorSingleListingBase;

/**
 * ListingReviewSettings class
 */
class ListingReviewSettings extends ElementorSingleListingBase {
	/**
	 * Set style controlls
	 */
	public function widget_general_fields(): array {
		return $this->general_section();
	}

	/**
	 * Set style controlls
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->general_style(),
			$this->header_section_style(),
			$this->leave_comment_style(),
			$this->list_style(),
			$this->form_style(),
			$this->form_button()
		);

		return $fields;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function general_section() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_content_visibility',
				'label' => __('General', 'rtcl-elementor-builder'),
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_show_comment_list',
				'label'         => __('Comment List', 'rtcl-elementor-builder'),
				'label_on'      => __('On', 'rtcl-elementor-builder'),
				'label_off'     => __('Off', 'rtcl-elementor-builder'),
				'default'       => 'yes',
				'description'   => __('Switch to Show Comment List', 'classified-listing'),
			],
			[
				'type'       => Controls_Manager::SELECT,
				'id'         => 'comment_list_style',
				'label'      => __('Comment List Style', 'rtcl-elementor-builder'),
				'options'    => [
					'inline'  => 'Inline',
					'newline' => 'New line',
				],
				'default'    => 'inline',
				'condition'  => ['rtcl_show_comment_list' => 'yes'],
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_show_review_title',
				'label'         => __('Review Section Title ', 'rtcl-elementor-builder'),
				'label_on'      => __('On', 'rtcl-elementor-builder'),
				'label_off'     => __('Off', 'rtcl-elementor-builder'),
				'default'       => 'yes',
				'description'   => __('Switch to Show Review Section Title', 'classified-listing'),
				'condition'     => ['rtcl_show_comment_list' => ['yes']],
			],
			[
				'type'      => Controls_Manager::TEXT,
				'id'        => 'rtcl_review_title_text',
				'label'     => __('Review Title Text', 'rtcl-elementor-builder'),
				'default'   => __('Reviews', 'rtcl-elementor-builder'),
				'condition' => ['rtcl_show_review_title' => ['yes']],
			],

			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_show_leave_review_button',
				'label'         => __('Leave Review Button', 'rtcl-elementor-builder'),
				'label_on'      => __('On', 'rtcl-elementor-builder'),
				'label_off'     => __('Off', 'rtcl-elementor-builder'),
				'default'       => 'yes',
				'condition'     => ['rtcl_show_comment_list' => ['yes']],
				'description'   => __('Switch to Show Leave Review Button', 'classified-listing'),
			],
			[
				'type'      => Controls_Manager::TEXT,
				'id'        => 'rtcl_leave_review_button_text',
				'label'     => __('Leave Review Button Text', 'rtcl-elementor-builder'),
				'default'   => __('Leave Review', 'rtcl-elementor-builder'),
				'condition' => ['rtcl_show_leave_review_button' => ['yes']],
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_review_meta',
				'label'         => __('Review Meta', 'rtcl-elementor-builder'),
				'label_on'      => __('On', 'rtcl-elementor-builder'),
				'label_off'     => __('Off', 'rtcl-elementor-builder'),
				'default'       => 'yes',
				'condition'     => ['rtcl_show_comment_list' => ['yes']],
				'description'   => __('Switch to Show Review Meta', 'classified-listing'),
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_show_comment_form',
				'label'         => __('Comment Form', 'rtcl-elementor-builder'),
				'label_on'      => __('On', 'rtcl-elementor-builder'),
				'label_off'     => __('Off', 'rtcl-elementor-builder'),
				'default'       => 'yes',
				'description'   => __('Switch to Show Review Form', 'classified-listing'),
			],
			[
				'type'      => Controls_Manager::TEXT,
				'id'        => 'rtcl_comment_form_title_text',
				'label'     => __('Comment form Title Text', 'rtcl-elementor-builder'),
				'default'   => __('Leave Review', 'rtcl-elementor-builder'),
				'condition' => ['rtcl_show_comment_form' => ['yes']],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function header_section_style() {
		$fields = [
			[
				'mode'      => 'section_start',
				'id'        => 'review_section_header',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __('Commet List Header Section', 'rtcl-elementor-builder'),
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'review_section_title_typo',
				'label'    => __('Section Title Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .el-single-addon.rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .rtcl-single-listing-section-title',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'section_title_color',
				'label'     => __('Title Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .rtcl-single-listing-section-title' => 'color: {{VALUE}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'avarage_ratings_typo',
				'label'    => __('Average Rating Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .el-single-addon.rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .listing-meta .listing-meta-rating',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'avarage_ratings_color',
				'label'     => __('Average Rating Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .listing-meta .listing-meta-rating' => 'color: {{VALUE}};',
				],
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'mode'           => 'group',
				'id'             => 'avarage_ratings_border',
				'label'          => __('Border', 'rtcl-elementor-builder'),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '2',
							'right'    => '2',
							'bottom'   => '2',
							'left'     => '2',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => '#39b449',
					],
				],
				'selector' => '{{WRAPPER}} .el-single-addon.rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .listing-meta .listing-meta-rating',
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function general_style() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_general_style',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __('General Style ', 'rtcl-elementor-builder'),
			],
			[
				'mode'      => 'group',
				'label'     => __('Background', 'rtcl-elementor-builder'),
				'id'        => 'rtcl_background',
				'type'      => Group_Control_Background::get_type(),
				'selector'  => '{{WRAPPER}} .el-single-addon.rtcl-Reviews.rtcl',
			],

			[
				'mode'       => 'responsive',
				'label'      => __('Padding', 'rtcl-elementor-builder'),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_wrapper_padding',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.rtcl-Reviews.rtcl' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __('Margin', 'rtcl-elementor-builder'),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_wrapper_spacing',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.rtcl-Reviews.rtcl' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function leave_comment_style() {
		$fields = [
			[
				'mode'      => 'section_start',
				'id'        => 'rtcl_leave_comment_button',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __('Header Button', 'rtcl-elementor-builder'),
				'condition' => ['rtcl_show_comment_list' => ['yes']],
			],
			[
				'mode'       => 'responsive',
				'label'      => __('Padding', 'rtcl-elementor-builder'),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_leave_comment_button_padding',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}}  .rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .rtcl-reviews-meta-action a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_leave_comment_button_typo',
				'label'    => __('Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .rtcl-reviews-meta-action a',
			],
			[
				'mode' => 'tabs_start',
				'id'   => 'button_tabs_start',
			],
			// Tab For Hover view.
			[
				'mode'  => 'tab_start',
				'id'    => 'rtcl_button_normal',
				'label' => esc_html__('Normal', 'rtcl-elementor-builder'),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'button_bg_color',
				'label'     => __('Background Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .rtcl-reviews-meta-action a' => 'background-color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'button_text_color',
				'label'     => __('Text Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .rtcl-reviews-meta-action a' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_border_color',
				'label'     => __('Border Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .rtcl-reviews-meta-action a' => 'border-color: {{VALUE}};',
				],
			],
			[
				'mode' => 'tab_end',
			],
			[
				'mode'  => 'tab_start',
				'id'    => 'rtcl_button_hover',
				'label' => esc_html__('Hover', 'rtcl-elementor-builder'),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'button_bg_color_hover',
				'label'     => __('Background Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .rtcl-reviews-meta-action a:hover' => 'background-color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'button_text_color_hover',
				'label'     => __('Text Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .rtcl-reviews-meta-action a:hover' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_button_border_color_hover',
				'label'     => __('Border Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} .rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .rtcl-reviews-meta-action a:hover' => 'border-color: {{VALUE}};',
				],
			],
			[
				'mode' => 'tab_end',
			],
			[
				'mode' => 'tabs_end',
			],

			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function form_button() {
		$fields = [
			[
				'mode'      => 'section_start',
				'id'        => 'rtcl_form_button',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __('Form Button', 'rtcl-elementor-builder'),
				'condition' => ['rtcl_show_comment_form' => ['yes']],
			],
			[
				'mode'       => 'responsive',
				'label'      => __('Padding', 'rtcl-elementor-builder'),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_form_button_padding',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} #review-form #respond .form-submit input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_form_button_typo',
				'label'    => __('Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} #review-form #respond .form-submit input',
			],
			[
				'mode' => 'tabs_start',
				'id'   => 'form_button_tabs_start',
			],
			// Tab For Hover view.
			[
				'mode'  => 'tab_start',
				'id'    => 'form_button_normal',
				'label' => esc_html__('Normal', 'rtcl-elementor-builder'),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'form_button_bg_color',
				'label'     => __('Background Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} #review-form #respond .form-submit input' => 'background-color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'form_button_text_color',
				'label'     => __('Text Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} #review-form #respond .form-submit input' => 'color: {{VALUE}};',
				],
			],
			[
				'type'     => Group_Control_Border::get_type(),
				'mode'     => 'group',
				'id'       => 'form_button_border',
				'label'    => __('Border', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} #review-form #respond .form-submit input',
			],
			[
				'mode' => 'tab_end',
			],
			[
				'mode'  => 'tab_start',
				'id'    => 'form_button_hover',
				'label' => esc_html__('Hover', 'rtcl-elementor-builder'),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'form_button_bg_color_hover',
				'label'     => __('Background Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} #review-form #respond .form-submit input:hover' => 'background-color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'form_button_text_color_hover',
				'label'     => __('Text Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} #review-form #respond .form-submit input:hover' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'form_button_border_hover',
				'label'     => __('Border Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} #review-form #respond .form-submit input:hover' => 'border-color: {{VALUE}};',
				],
			],
			[
				'mode' => 'tab_end',
			],
			[
				'mode' => 'tabs_end',
			],

			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function form_style() {
		$fields = [
			[
				'mode'      => 'section_start',
				'id'        => 'rtcl_form_style',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __('Form', 'rtcl-elementor-builder'),
				'condition' => ['rtcl_show_comment_form' => ['yes']],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_fields_background',
				'label'     => __('Fields Background', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}}  #review-form-wrapper .form-control' => 'background: {{VALUE}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_form_title_typo',
				'label'    => __('Title Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .rtcl-Reviews.rtcl #respond .comment-reply-title',
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_title_gap',
				'label'      => esc_html__('Form Title Gap', 'rtcl-elementor-builder'),
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-Reviews.rtcl #respond .comment-reply-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_form_label_typo',
				'label'    => __('Form Label Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .rtcl-Reviews.rtcl #respond .comment-form label',
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_field_height',
				'label'      => esc_html__('Field Height', 'rtcl-elementor-builder'),
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-Reviews.rtcl .form-control:not(#comment)' => 'height: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_comment_height',
				'label'      => esc_html__('Comment Field Height', 'rtcl-elementor-builder'),
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} #review-form-wrapper .form-control#comment' => 'height: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_field_border_color',
				'label'     => __('Fields Border Color', 'rtcl-elementor-builder'),
				'selectors' => [
					'{{WRAPPER}} #review-form-wrapper .form-control' => 'border-color: {{VALUE}} !important;',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function list_style() {
		$fields = [
			[
				'mode'      => 'section_start',
				'id'        => 'rtcl_list_style',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __('Comment List', 'rtcl-elementor-builder'),
				'condition' => ['rtcl_show_comment_list' => ['yes']],
			],
			[
				'mode'       => 'responsive',
				'label'      => __('Padding', 'rtcl-elementor-builder'),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_comment_list_padding',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-Reviews.rtcl #comments ol.comment-list li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __('Margin', 'rtcl-elementor-builder'),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_comment_list_spacing',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-Reviews.rtcl #comments ol.comment-list li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_review_author_typo',
				'label'    => __('Author Name Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .rtcl-Reviews.rtcl #comments ol.comment-list li .comment-container .rtcl-review-meta .rtcl-review-author',
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_review_author_rating_gap',
				'label'      => esc_html__('Rating Gap', 'rtcl-elementor-builder'),
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-Reviews.rtcl .star-rating' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_review_title_typo',
				'label'    => __('Title Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .rtcl-Reviews.rtcl #comments ol.comment-list li .comment-container .media-body .rtcl-review__title',
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_review_title_gap',
				'label'      => esc_html__('Title Gap', 'rtcl-elementor-builder'),
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-Reviews.rtcl #comments ol.comment-list li .comment-container .media-body .rtcl-review__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}
}
