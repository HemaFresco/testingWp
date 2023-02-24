<?php
/**
 * The template to display the Builder content
 *
 * @author  RadiousTheme
 * @package classified-listing/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

\Elementor\Plugin::$instance->frontend->add_body_class( 'elementor-template-full-width' );

get_header( 'listing' );
?>
	<div class="rtcl builder-content content-invisible <?php echo implode( ' ', apply_filters( 'rtcl_builder_content_parent_class', [] ) ); ?>"> <!-- Removed jumping issue after loded -->
		<?php
		/**
		 * Before Header-Footer page template content.
		 *
		 * Fires before the content of Elementor Header-Footer page template.
		 *
		 * @since 2.0.0
		 */
		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		do_action( 'elementor/page_templates/header-footer/before_content' );
		if ( is_singular( 'rtcl_builder' ) ) {
			\Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' )->print_content();
		} else {
			do_action( 'el_builder_template_content' );
		}
		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		do_action( 'elementor/page_templates/header-footer/after_content' );
		?>
	</div>
<?php
get_footer( 'listing' );
