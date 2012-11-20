<?php
/**
 * Help tab.
 *
 * @package    T5 Rewrite
 * @subpackage Rewrite Tags
 * @version    2012.11.16
 * @since      2012.11.12
 */

/**
 * Adds a table of all rewrite tags to the permalink settings page.
 */
class T5_Rewrite_Tags_Help
{
	/**
	 * Constructor.
	 *
	 * @wp-hook in_admin_header
	 */
	public function __construct()
	{
		if ( ! $this->is_help_screen() )
			return;

		get_current_screen()->add_help_tab(
			array (
				'id'       => 'alltags',
				'title'    => __( 'All Rewrite Tags', 'plugin_t5_rewrite' ),
				'callback' => array ( $this, 'render' )
			)
		);
	}

	/**
	 * Check if we are on the permalink settings page.
	 *
	 * Can be filtered.
	 *
	 * @return boolean
	 */
	protected function is_help_screen()
	{
		$return = 'options-permalink' === get_current_screen()->base;
		$return = apply_filters( 't5_rewrite_show_help_screen', $return );

		return $return;
	}

	/**
	 * Print rewrite tag help content.
	 *
	 * Called in wp-admin/admin-header.php after 'in_admin_header' and before
	 * 'admin_notices'.
	 *
	 * @return void
	 */
	function render()
	{
		$tags = $this->get_tag_list();

		if ( empty ( $tags ) )
		{
			return _e(
				'No tags available. Someone has broken this tab.',
				'plugin_t5_rewrite'
			);
		}

		print '<table class="widefat" style="margin:20px auto">'
			. $this->get_table_header();

		foreach ( $tags as $tag )
			print $this->get_tag_row( $tag );

		print '</table>';
	}

	/**
	 * A single row in the help table.
	 *
	 * @param  array $tag
	 * @return string
	 */
	protected function get_tag_row( array $tag )
	{
		return sprintf(
			'<tr>
				<td style="white-space:nowrap" class="code">%1$s</td>
				<td>%2$s</td>
				<td>%3$s</td>
			</tr>',
			'%' . $tag['tag'] . '%',
			$tag['description'],
			$tag['examples']
		);
	}

	/**
	 * <thead> and <tfoot>
	 *
	 * @return string
	 */
	protected function get_table_header()
	{
		$row = sprintf(
			'<tr>
				<th scope="col">%1$s</th>
				<th scope="col">%2$s</th>
				<th scope="col">%3$s</th>
			</tr>',
			__( 'Tag',         'plugin_t5_rewrite' ),
			__( 'Description', 'plugin_t5_rewrite' ),
			__( 'Examples',    'plugin_t5_rewrite' )
		);

		return sprintf( '<thead>%1$s</thead><tfoot>%1$s</tfoot>', $row );
	}

	/**
	 * Array of available tags.
	 *
	 * @return array
	 */
	protected function get_tag_list()
	{
		$tags = array (
			array (
				'tag'         => 'year',
				'description' => __(
					'The year of the post in four digits.',
					'plugin_t5_rewrite'
					),
				'examples'    => '2004, 2020'
			),
			array (
				'tag'         => 'monthnum',
				'description' => __(
					'Month of the year in two digits.',
					'plugin_t5_rewrite'
					),
				'examples'    => '01, 12'
			),
			array (
				'tag'         => 'day',
				'description' => __(
					'The day of the month in two digits.',
					'plugin_t5_rewrite'
					),
				'examples'    => '07, 31'
			),
			array (
				'tag'         => 'hour',
				'description' => __(
					'The hour of the day in two digits.',
					'plugin_t5_rewrite'
					),
				'examples'    => '04, 23'
			),
			array (
				'tag'         => 'minute',
				'description' => __(
					'The minute of the hour in two digits.',
					'plugin_t5_rewrite'
					),
				'examples'    => '04, 59'
			),
			array (
				'tag'         => 'second',
				'description' => __(
					'The second of the minute in two digits.',
					'plugin_t5_rewrite'
					),
				'examples'    => '05, 58'
			),
			array (
				'tag'         => 'post_id',
				'description' => __(
					'The unique ID # of the post. A number.',
					'plugin_t5_rewrite'
					),
				'examples'    => '1485, 200058'
			),
			array (
				'tag'         => 'postname',
				'description' => __(
					'A sanitized version of the title of the post (post slug field on Edit Post/Page panel).',
					'plugin_t5_rewrite'
					),
				'examples'    => 'this-is-a-great-post'
			),
			array (
				'tag'         => 'category',
				'description' => __(
					'A sanitized version of the category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI.',
					'plugin_t5_rewrite'
					),
				'examples'    => 'uncategorized, food/tomatoes'
			),
			array (
				'tag'         => 'author',
				'description' => __(
					'A sanitized version of the author name.',
					'plugin_t5_rewrite'
					),
				'examples'    => 'diana, john'
			),
		);

		return apply_filters( 't5_rewrite_tag_help_table', $tags );
	}
}