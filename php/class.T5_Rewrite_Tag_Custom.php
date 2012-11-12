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
 * Adds '%_custom_field%' as rewrite tag (placeholder) for permalinks.
 */
class T5_Rewrite_Tag_Custom extends T5_Rewrite_Tag
{
	protected $tag   = '%_custom_%';
	protected $regex = '%_custom_%\(([^/]+)\)';

	/**
	 * Post meta key.
	 *
	 * @type string
	 */
	protected $field = '';

	/**
	 * Use the oldest tag as replacement for the rewrite tag.
	 *
	 * @see T5_Rewrite_Tag::get_link_replacement()
	 */
	protected function get_link_replacement( $link, $post )
	{
		$meta = get_post_meta( $post->ID, $this->field, TRUE );

		if ( ! $meta )
			$meta = $this->get_fallback();

		return preg_replace( '~' . $this->regex . '~', $meta, $link );
	}

	/**
	 * (non-PHPdoc)
	 * @see T5_Rewrite_Tag::has_tag()
	 */
	protected function has_tag( $link )
	{
		$found = preg_match( '~' . $this->regex . '~', $link, $matches );

		if ( ! $found )
			return FALSE;

		$this->field = $matches[1];

		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see T5_Rewrite_Tag::get_description()
	 */
	protected function get_description()
	{
		$text = __(
			'A custom field of a post. The syntax is different here: use %1$s, where %2$s is the key. If there is no value for the key %3$s will be used as fallback.',
			'plugin_t5_rewrite'
		);

		return sprintf(
			$text,
			'<code>%_custom_%(fieldname)</code>',
			'<code>(fieldname)</code>',
			'<code>' . $this->get_fallback() . '</code>'
		);
	}

	/**
	 * Default string when the custom meta is not found.
	 *
	 * @return string
	 */
	protected function get_fallback()
	{
		return apply_filters(
			't5_rewrite_custom_field_fallback',
			'custom',
			$this->field
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see T5_Rewrite_Tag::get_examples()
	 */
	protected function get_examples()
	{
		return 'field <code>gender</code> = <code>/%_custom_%(gender)/%postname%</code>: <br>/male/john-malkovitch, <br>/female/madonna';
	}
}