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
		#$tags = get_the_tags( $post->ID );
		$tag = $this->get_post_tag( $post->ID );

		if ( ! $tag ) // insert a fallback value
			return str_replace( $this->tag, 'tag', $link );

		return str_replace( $this->tag, $tag, $link );
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

	/**
	 * Make post tags sortable to assign the first tag to the permalink.
	 *
	 * (non-PHPdoc)
	 * @see T5_Rewrite_Tag::init()
	 */
	public function init()
	{
		global $wp_taxonomies;

		$wp_taxonomies['post_tag']->sort = true;

		parent::init();
	}

	/**
	 * Get the slug of first assigned tag.
	 *
	 * Solution by Christopher Davis.
	 * @link http://wordpress.stackexchange.com/a/72712
	 *
	 * @see   init()
	 * @param int $id          Post ID
	 * @return boolean|string  FALSE if no tags are found, the slug otherwise.
	 */
	protected function get_post_tag( $id )
	{
		$args = array ( 'orderby' => 'term_order' );
		$tags = wp_get_object_terms( $id, 'post_tag', $args );

		if ( ! $tags )
			return FALSE;

		return current( (array) $tags )->slug;
	}
}