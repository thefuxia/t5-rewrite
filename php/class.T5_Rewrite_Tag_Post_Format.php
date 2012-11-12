<?php
/**
 * Extending class. Has to be loaded after T5_Rewrite_Tag.
 *
 * @package    T5 Rewrite
 * @subpackage Rewrite Tags
 * @version    2012.11.12
 * @since      2012.11.12
 */

/**
 * Adds '%postformat%' as rewrite tag (placeholder) for permalinks.
 */
class T5_Rewrite_Tag_Post_Format extends T5_Rewrite_Tag
{
	protected $tag = '%postformat%';

	/**
	 * Use the post format as replacement for the rewrite tag.
	 *
	 * @see T5_Rewrite_Tag::get_link_replacement()
	 */
	protected function get_link_replacement( $link, $post )
	{
		$format = get_post_format( $post->ID );

		if ( ! $format ) // insert a fallback value
			return str_replace( $this->tag, 'standard', $link );

		return str_replace( $this->tag, $format, $link );
	}

	/**
	 * (non-PHPdoc)
	 * @see T5_Rewrite_Tag::get_description()
	 */
	protected function get_description()
	{
		return __( 'A sanitized version of the post format of the post.', 'plugin_t5_rewrite' );
	}

	/**
	 * (non-PHPdoc)
	 * @see T5_Rewrite_Tag::get_examples()
	 */
	protected function get_examples()
	{
		return join( array_keys( get_post_format_strings() ), ', ' );
	}
}