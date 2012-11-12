<?php
/**
 * Base class. Has to be loaded first.
 *
 * @package    T5 Rewrite
 * @subpackage Rewrite Tags
 * @version    2012.11.12
 * @since      2012.11.12
 */

/**
 * Base class for new rewrite tags.
 *
 * Call the constructor of the child class before 'init'
 */
abstract class T5_Rewrite_Tag
{
	/**
	 * The placeholder.
	 *
	 * Examples:
	 * - '%tag%',
	 * - '%custom%',
	 * - '%color%'
	 *
	 * @type string
	 */
	protected $tag;

	/**
	 * The regex WordPress should use to catch the tag.
	 *
	 * Examples:
	 * - '(\d+)-(\d+)',
	 * - '(\d\d\d\d)-(\d\d)-(\d\d)'
	 *
	 * @type string
	 */
	protected $regex = '([^/]+)';

	/**
	 * The filters.
	 *
	 * Possible values:
	 * - 'post'        post
	 * - 'page'        page
	 * - 'attachment'  attachment
	 * - 'post_type'   custom post type
	 *
	 * @type string|array
	 */
	protected $filter = 'post';

	/**
	 * Tag description for the help tab.
	 *
	 * @type string
	 */
	protected $description = '';

	/**
	 * Output examples for the help tab.
	 *
	 * @type string
	 */
	protected $examples = '';

	/**
	 * Constructor.
	 *
	 * Registers the init action.
	 */
	public function __construct()
	{
		add_action( 'init', array ( $this, 'init' ) );
		add_filter( 't5_rewrite_tag_help_table', array ( $this, 'add_help') );
	}

	/**
	 * Add tag and register link filter.
	 *
	 * @wp-hook init
	 * @return  void
	 */
	public function init()
	{
		add_rewrite_tag( $this->tag, $this->regex );

		if ( is_string( $this->filter ) )
			return $this->register_filter( $this->filter );

		foreach ( $this->filter as $filter )
			$this->register_filter( $filter );
	}

	/**
	 * Find the filter for the current post type and hook in.
	 *
	 * @wp-hook init
	 * @param   string   $filter
	 * @return  boolean
	 */
	protected function register_filter( $filter )
	{
		$callback = array ( $this, 'filter_link' );

		return add_filter( "{$filter}_link", $callback, 10, 2 );
	}

	/**
	 * Parse post link and replace the placeholder.
	 *
	 * @param   string $link
	 * @param   object $post
	 * @return  string
	 */
	public function filter_link( $link, $post )
	{
		static $cache = array (); // Don't repeat yourself.

		if ( isset ( $cache[ $post->ID ] ) )
			return $cache[ $post->ID ];

		if ( ! $this->has_tag( $link ) )
		{
			$cache[ $post->ID ] = $link;
			return $link;
		}

		$cache[ $post->ID ] = $this->get_link_replacement( $link, $post );

		return $cache[ $post->ID ];
	}

	/**
	 * Test if tag is in $link.
	 *
	 * @param  string $link
	 * @return boolean
	 */
	protected function has_tag( $link )
	{
		return FALSE !== strpos( $link, $this->tag );
	}

	/**
	 * Add our tga to the help table.
	 *
	 * @wp-hook t5_rewrite_tag_table
	 * @param   array $tags
	 * @return  array
	 */
	public function add_help( $tags )
	{
		$tags[] = array (
			'tag'         => trim( $this->tag, '%' ),
			'description' => $this->get_description(),
			'examples'    => $this->get_examples()
		);

		return $tags;
	}

	/**
	 * The tag description.
	 *
	 * @return string
	 */
	protected function get_description()
	{
		return $this->description;
	}

	/**
	 * The tag examples.
	 *
	 * @return string
	 */
	protected function get_examples()
	{
		return $this->examples;
	}

	/**
	 * The real working code has to be implemented by the child class.
	 *
	 * @param  string $link
	 * @param  object $post
	 * @return string
	 */
	abstract protected function get_link_replacement( $link, $post );
}