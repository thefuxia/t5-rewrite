<?php  # -*- coding: utf-8 -*-
/**
 * Permalink preview.
 *
 * Shows how the new rules work when applied to the latest post.
 * Not exactly an MVC pattern. :)
 *
 * @package    T5 Rewrite
 * @subpackage Tools
 * @version    2013.03.24
 * @since      2013.03.23
 */
class T5_Permalink_Preview
{
	/**
	 * Constructor.
	 *
	 * @wp-hook plugins_loaded && is_admin()
	 */
	public function __construct()
	{
		add_action( 'wp_ajax_permalink_preview', array ( $this, 'fetch_preview' ) );

		if ( isset ( $GLOBALS['pagenow'] )
			&& 'options-permalink.php' === $GLOBALS['pagenow']
		)
		{
			add_action( 'admin_enqueue_scripts', array ( $this, 'load_js' ) );
		}

		/* We have to do it here already, because when the AJAX callback fires,
		 * the global $wp_rewrite has the trailing slash already set, and
		 * get_permalink() fetches 'permalink_structure', but it is asking
		 * $wp_rewrite for the slash.
		 * This is stupid, I hate it, and it sucks. Just saying.
		 */
		if ( ! empty ( $_POST['struct'] ) && defined( 'DOING_AJAX' ) && DOING_AJAX )
		{
			add_filter(
				'pre_option_permalink_structure',
				array ( $this, 'current_struct' )
			);
		}
	}

	/**
	 * Get the permalink of the latest post with the new structure.
	 *
	 * @wp-hook wp_ajax_permalink_preview
	 * @return  void
	 */
	public function fetch_preview()
	{
		$args  = array (
			'numberposts'         => 1,
			'ignore_sticky_posts' => TRUE,
			'post_status'         => 'publish',
			// If not specified, attachments are searched too.
			'post_type'           => 'post'
		);

		// An array or FALSE.
		$posts = get_posts( $args );

		if ( ! empty ( $posts ) && is_array( $posts ) )
			print get_permalink( $posts[0]->ID );
		else
			_e( 'No post for preview found.', 'plugin_t5_rewrite' );

		exit;
	}

	/**
	 * Permalink structure typed in by the user.
	 *
	 * @wp-hook pre_option_permalink_structure
	 * @return  string
	 */
	public function current_struct()
	{
		if ( '_' === $_POST['struct'] )
			return "";

		return esc_html( $_POST['struct'] );
	}

	/**
	 * Enqueue JavaScript and set localzed parameters.
	 *
	 * @wp-hook admin_enqueue_scripts
	 * @return  void
	 */
	public function load_js()
	{
		$handle   = 't5_permalink_preview';

		wp_register_script(
			$handle,
			$this->get_js_url(),
			array ( 'jquery' ),
			NULL,
			TRUE
		);
		wp_enqueue_script( $handle );
		wp_localize_script( $handle, 't5PermalinkPreview', $this->get_js_vars() );
	}

	/**
	 * URL to preview JavaScript.
	 *
	 * @wp-hook admin_enqueue_scripts
	 * @return string
	 */
	protected function get_js_url()
	{
		/* Prepend with a / to test minified script in debug mode.
		$min      = defined( 'WP_DEBUG') && WP_DEBUG ? '' : '.min';
		/*/
		$min      = defined( 'WP_DEBUG') && WP_DEBUG ? '.min' : '';
		/**/
		return plugins_url( "js/preview$min.js", dirname( __FILE__ ) );
	}

	/**
	 * Localized JavaScript parameters.
	 *
	 * @wp-hook admin_enqueue_scripts
	 * @return  array
	 */
	protected function get_js_vars()
	{
		return array (
			'label' => _x(
				'Preview',
				'permalink preview',
				'plugin_t5_rewrite'
			),
			'error' => _x(
				'No preview available.',
				'permalink preview',
				'plugin_t5_rewrite'
			)
		);
	}
}