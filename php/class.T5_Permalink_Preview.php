<?php  # -*- coding: utf-8 -*-
/**
 * Permalink preview.
 *
 * @package    T5 Rewrite
 * @subpackage Tools
 * @version    2013.03.23
 * @since      2013.03.23
 */
class T5_Permalink_Preview
{
	/**
	 * Constructor.
	 *
	 * @wp-hook plugins_loaded
	 */
	public function __construct()
	{
		add_action( 'wp_ajax_permalink_preview', array ( $this, 'fetch_preview' ) );
		add_action(
			'admin_footer-options-permalink.php',
			array ( $this, 'print_footer_script' )
		);
		// admin_print_scripts-options-permalink.php
		// admin_print_footer_scripts

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
	function current_struct()
	{
		return esc_html( $_POST['struct'] );
	}

	/**
	 * Print preview script.
	 *
	 * @wp-hook admin_footer-options-permalink.php
	 * @return  void
	 */
	public function print_footer_script()
	{
		?>
	<script>
	jQuery( function( $ ) {

		var previewText = {
			'preview':   '<?php
				_ex( 'Preview', 'permalink preview', 'plugin_t5_rewrite' );
			?>',
			'noPreview': '<?php
				_ex( 'No preview available.',
					'permalink preview',
					'plugin_t5_rewrite'
					);
			?>'
		};

		$( 'form[action="options-permalink.php"] table:first' )
			.append( '<tr><th>' + previewText.preview + '</th><td id="t5preview" class="code"></td></tr>' );

		var previewCell = $( '#t5preview' ),
		    tagInput    = $( '#permalink_structure' ),
		    $stored     = [];

		var handleResult = function( response ) {
			$( previewCell ).html( response )
				.animate( { opacity: 0.25 }, 500, "linear", function() {
					$(this).animate( { opacity: 1 }, 500 );
				});
			$stored[ $val ] = response;
		},
		updatePreview = function() {
			$val = $.trim( $( tagInput ).val() );

			if ( $stored[ $val ] ) {
				$( previewCell ).html( $stored[ $val ] );
				return;
			}

			if ( '' == $val ) {
				$( previewCell ).html( '<i>' + previewText.noPreview + '</i>' );
				return;
			}

		    var postParams = {
		        action: 'permalink_preview',
		        struct: $val
		    };

			$.post( ajaxurl, postParams, handleResult );
		};
		$( tagInput ).on( 'change paste keyup mouseleave', updatePreview );
		$( window ).on( 'load', updatePreview );
	});
	</script>
	<?php
		}
}