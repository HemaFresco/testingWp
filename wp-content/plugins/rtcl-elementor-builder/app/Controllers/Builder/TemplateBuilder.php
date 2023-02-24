<?php
/**
 * TemplateBuilder Class for Elementor builder
 *
 * The main TemplateBuilder Class.
 *
 * @package  RTCL_Elementor_Builder
 * @since    2.0.10
 */

namespace RtclElb\Controllers\Builder;

use RtclElb\Traits\ELTempleateBuilderTraits;

/**
 * RegisterPostType Class
 */
class TemplateBuilder {

	/**
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;
	/**
	 * Initialize function.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', [ __CLASS__, 'remove_submenus' ] );
		if ( did_action( 'elementor/loaded' ) ) {
			add_action( 'rtcl_after_register_post_type', [ __CLASS__, 'template_builder_post_type' ], 5 );
			add_filter( 'views_edit-' . self::$post_type_tb, [ __CLASS__, 'template_builder_tabs' ] );
			add_filter( 'parse_query', [ __CLASS__, 'query_filter' ] );
			add_filter( 'post_row_actions', [ __CLASS__, 'filter_post_row_actions' ], 11, 1 );
			// Admin column.
			add_filter( 'manage_edit-' . self::$post_type_tb . '_columns', [ __CLASS__, 'add_new_columns' ] );
			add_action( 'manage_' . self::$post_type_tb . '_posts_custom_column', [ __CLASS__, 'tb_custom_columns' ], 10, 2 );
			add_filter( 'manage_edit-' . self::$post_type_tb . '_sortable_columns', [ __CLASS__, 'register_sortable_columns' ] );
			add_action( 'pre_get_posts', [ __CLASS__, 'sortable_columns_query' ] );
			// Admin column End.
		}

	}

	/**
	 * Remove submenus function
	 *
	 * @return void
	 */
	public static function remove_submenus() {
		global $submenu;
		unset( $submenu['edit.php?post_type=rtcl_builder'][10] ); // Removes 'Add New'.
	}

	/**
	 * Register sortablecolumns
	 *
	 * @param [type] $columns column list.
	 * @return array
	 */
	public static function register_sortable_columns( $columns ) {
		$columns['type'] = 'type';
		return $columns;
	}
	/**
	 * Meta sortable function.
	 *
	 * @param object $query query object.
	 * @return void
	 */
	public static function sortable_columns_query( $query ) {
		if ( ! is_admin() || ! self::is_current_screen() ) {
			return;
		}
		$orderby = $query->get( 'orderby' );
		if ( 'type' === $orderby ) {
			$query->set( 'meta_key', self::template_type_meta_key() );
			$query->set( 'orderby', 'meta_value' );
		}

	}
	/**
	 * Add new columns to the post table
	 *
	 * @param Array $columns - Current columns on the list post.
	 */
	public static function add_new_columns( $columns ) {
		$column_meta = [
			'type'        => 'Type',
			'set_default' => 'Default',
		];
		$columns     = array_merge(
			array_slice( $columns, 0, 2 ),
			$column_meta,
			array_slice( $columns, 2 )
		);
		return $columns;

	}
	/**
	 * Display data in new columns
	 *
	 * @param string $column table column.
	 * @param string $post_id Post id.
	 * @return void
	 */
	public static function tb_custom_columns( $column, $post_id ) {
		$type = self::builder_type( $post_id ) ? self::builder_type( $post_id ) : array_key_first( self::builder_page_types() );
		switch ( $column ) {
			case 'type':
				$str = str_replace( [ '-', '-' ], ' ', $type );
				echo '<span style="font-weight: 600;">' . esc_html( ucwords( $str ) ) . '</span>';
				break;
			case 'set_default':
				$is_default = absint( self::builder_page_id( $type ) );
				?>
				<span class="rtcl-switch-wrapper page-type-<?php echo esc_attr( $type ); ?>">
					<label class="switch">
						<input type="hidden" class="template_type" name="template_type" value="<?php echo esc_attr( $type ); ?>">
						<input value="<?php echo absint( $post_id ); ?>" class="set_default" name="set_default" type="checkbox" <?php echo esc_attr( $post_id === $is_default ? 'checked' : '' ); ?> >
						<span class="slider round ">
							<span class="loader"></span>
						</span>
					</label>
				</span>
				<?php
				break;
		}
	}
	/**
	 * Document edit url.
	 *
	 * @param object $post Post object.
	 * Filters the document edit url.
	 */
	public static function get_edit_url( $post ) {
		$url = add_query_arg(
			[
				'post'   => $post->ID,
				'action' => 'elementor',
			],
			admin_url( 'post.php' )
		);

		return $url;
	}
	/**
	 * Post type function
	 *
	 * @return void
	 */
	public static function template_builder_post_type() {
		/**
		 * Elementor Template Builder start
		 */
		$tb_labels = [
			'name'                  => esc_html_x( 'Template Builder', 'Post Type General Name', 'rtcl-elementor-builder' ),
			'singular_name'         => esc_html_x( 'Template Builder', 'Post Type Singular Name', 'rtcl-elementor-builder' ),
			'menu_name'             => esc_html__( 'Template Builder', 'rtcl-elementor-builder' ),
			'name_admin_bar'        => esc_html__( 'Template Builder', 'rtcl-elementor-builder' ),
			'archives'              => esc_html__( 'Template Archives', 'rtcl-elementor-builder' ),
			'attributes'            => esc_html__( 'Template Attributes', 'rtcl-elementor-builder' ),
			'parent_item_colon'     => esc_html__( 'Parent Item:', 'rtcl-elementor-builder' ),
			'all_items'             => esc_html__( 'Template Builder', 'rtcl-elementor-builder' ),
			'add_new_item'          => esc_html__( 'Add New Template', 'rtcl-elementor-builder' ),
			'add_new'               => esc_html__( 'Add New', 'rtcl-elementor-builder' ),
			'new_item'              => esc_html__( 'New Template', 'rtcl-elementor-builder' ),
			'edit_item'             => esc_html__( 'Edit Template', 'rtcl-elementor-builder' ),
			'update_item'           => esc_html__( 'Update Template', 'rtcl-elementor-builder' ),
			'view_item'             => esc_html__( 'View Template', 'rtcl-elementor-builder' ),
			'view_items'            => esc_html__( 'View Templates', 'rtcl-elementor-builder' ),
			'search_items'          => esc_html__( 'Search Templates', 'rtcl-elementor-builder' ),
			'not_found'             => esc_html__( 'Not found', 'rtcl-elementor-builder' ),
			'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'rtcl-elementor-builder' ),
			'featured_image'        => esc_html__( 'Featured Image', 'rtcl-elementor-builder' ),
			'set_featured_image'    => esc_html__( 'Set featured image', 'rtcl-elementor-builder' ),
			'remove_featured_image' => esc_html__( 'Remove featured image', 'rtcl-elementor-builder' ),
			'use_featured_image'    => esc_html__( 'Use as featured image', 'rtcl-elementor-builder' ),
			'insert_into_item'      => esc_html__( 'Insert into Template', 'rtcl-elementor-builder' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this Template', 'rtcl-elementor-builder' ),
			'items_list'            => esc_html__( 'Templates list', 'rtcl-elementor-builder' ),
			'items_list_navigation' => esc_html__( 'Templates list navigation', 'rtcl-elementor-builder' ),
			'filter_items_list'     => esc_html__( 'Filter from list', 'rtcl-elementor-builder' ),
		];

		$tb_args = [
			'label'              => esc_html__( 'Template Builder', 'rtcl-elementor-builder' ),
			'description'        => esc_html__( 'Classified listing Template', 'rtcl-elementor-builder' ),
			'labels'             => $tb_labels,
			'supports'           => [ 'title', 'comments', 'editor', 'elementor', 'author', 'permalink' ],
			'menu_icon'          => RTCL_URL . '/assets/images/icon-20x20.png',
			'menu_position'      => 6,
			'hierarchical'       => false,
			'public'             => true,
			'show_ui'            => true,
			'show_in_admin_bar'  => false,
			'show_in_nav_menus'  => true,
			'can_export'         => true,
			'has_archive'        => false,
			'rewrite'            => [
				'slug'       => 'rtcl-template',
				'pages'      => false,
				'with_front' => true,
				'feeds'      => false,
			],
			'query_var'          => true,
			'publicly_queryable' => true,
			'capability_type'    => rtcl()->post_type,
			'show_in_rest'       => true,
			'rest_base'          => self::$post_type_tb,
		];

		$tb_args = apply_filters( 'rtcl_register_template_builder_args', $tb_args );

		register_post_type( self::$post_type_tb, $tb_args );
		\flush_rewrite_rules();
		/**
		 * Elementor Template Builder End
		 */

		$cpt_support = get_option( 'elementor_cpt_support' );
		if ( is_array( $cpt_support ) && ! in_array( self::$post_type_tb, $cpt_support, true ) ) {
			$cpt_support[] = self::$post_type_tb;
			update_option( 'elementor_cpt_support', $cpt_support );
		}
	}
	/**
	 * Print the tab for Template builder.
	 *
	 * @param object $views tabs.
	 * @return string
	 */
	public static function template_builder_tabs( $views ) {
		$template_type = isset( $_GET['template_type'] ) ? sanitize_key( wp_unslash( $_GET['template_type'] ) ) : '';
		?>
		<div id="rtcl-template-tabs-wrapper" class="nav-tab-wrapper" style="margin: 15px 0;">
			<a class="nav-tab <?php echo empty( $template_type ) ? 'nav-tab-active' : ''; ?>" href="edit.php?post_type=<?php echo esc_attr( self::$post_type_tb ); ?>"><?php esc_html_e( 'All', 'rtcl-elementor-builder' ); ?></a>
			<a class="nav-tab <?php echo 'defaults' === $template_type ? 'nav-tab-active' : ''; ?>" href="edit.php?post_type=<?php echo esc_attr( self::$post_type_tb ); ?>&amp;template_type=defaults"><?php esc_html_e( 'Defaults', 'rtcl-elementor-builder' ); ?></a>
			<?php
			$builder_page_types = self::builder_page_types();
			foreach ( $builder_page_types as $key => $value ) {
				?>
				<a class="nav-tab <?php echo $key === $template_type ? 'nav-tab-active' : ''; ?>" href="edit.php?post_type=<?php echo esc_attr( self::$post_type_tb ); ?>&amp;template_type=<?php echo esc_attr( $key ); ?>"> <?php echo esc_html( ucwords( $value ) ); ?></a>
			<?php } ?>	
		</div>
		<?php
		return $views;
	}

	/**
	 * Manage Template filter by template type
	 *
	 * @param \WP_Query $query WordPress main query.
	 * @return void
	 */
	public static function query_filter( \WP_Query $query ) {

		if ( ! is_admin() || ! self::is_current_screen() || ! empty( $query->query_vars['meta_key'] ) ) {
			return;
		}
		if ( isset( $_GET['template_type'] ) && ( '' !== $_GET['template_type'] || 'all' !== $_GET['template_type'] ) ) {
			$type = isset( $_GET['template_type'] ) ? sanitize_key( $_GET['template_type'] ) : '';
			if ( 'defaults' === $type ) {
				$defaults           = [];
				$builder_page_types = self::builder_page_types();
				foreach ( $builder_page_types as $key => $value ) {
					$defaults[] = absint( self::builder_page_id( $key ) );
				}
				if ( ! empty( $defaults ) ) {
					$query->query_vars['post__in'] = $defaults;
				}
			} else {
				$query->query_vars['meta_key']     = self::template_type_meta_key();
				$query->query_vars['meta_value']   = $type;
				$query->query_vars['meta_compare'] = '=';
			}
		}

	}
	/**
	 * Add/Remove edit link in dashboard.
	 *
	 * Add or remove an edit link to the post/page action links on the post/pages list table.
	 *
	 * Fired by `post_row_actions` and `page_row_actions` filters.
	 *
	 * @access public
	 *
	 * @param array $actions An array of row action links.
	 *
	 * @return array An updated array of row action links.
	 */
	public static function filter_post_row_actions( $actions ) {
		if ( self::is_current_screen() ) {
			global $post;
			unset( $actions['edit'] );
			$new_items['edit_with_elementor'] = sprintf(
				'<a href="%1$s" data-post-id="%3$s" >%2$s</a>',
				self::get_edit_url( $post ),
				__( 'Edit with Elementor', 'rtcl-elementor-builder' ),
				$post->ID
			);
			$actions                          = array_merge(
				$new_items,
				$actions
			);
		}
		return $actions;
	}
	/**
	 * Check template screen
	 *
	 * @return boolean
	 */
	public static function is_current_screen() {
		global $pagenow, $typenow;
		return 'edit.php' === $pagenow && self::$post_type_tb === $typenow;
	}

}
