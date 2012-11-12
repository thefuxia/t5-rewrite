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
 * Adds '%tag%' as rewrite tag (placeholder) for permalinks.
 */
class T5_Rewrite_Tag_Tag extends T5_Rewrite_Tag
{
	protected $tag = '%tag%';

	/**
	 * Use the oldest tag as replacement for the rewrite tag.
	 *
	 * @see T5_Rewrite_Tag::get_link_replacement()
	 */
	protected function get_link_replacement( $link, $post )
	{
		$tags = get_the_tags( $post->ID );

		if ( ! $tags ) // insert a fallback value
			return str_replace( $this->tag, 'tag', $link );

		$first = current( (array) $tags );

		return str_replace( $this->tag, $first->slug, $link );
	}

	/**
	 * (non-PHPdoc)
	 * @see T5_Rewrite_Tag::get_description()
	 */
	protected function get_description()
	{
		return __(
			'A sanitized version of the oldest tag of the post.',
			'plugin_t5_rewrite'
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see T5_Rewrite_Tag::get_examples()
	 */
	protected function get_examples()
	{
		return 'cats, gifs';
	}
}