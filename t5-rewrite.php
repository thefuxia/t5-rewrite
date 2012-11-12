<?php
/**
 * Text Domain: plugin_t5_rewrite
 * Domain Path: /languages
 * Plugin Name: T5 Rewrite
 * Description: Adds special rewrite tags.
 * Plugin URI:  https://github.com/toscho/t5-rewrite
 * Version:     2012.11.12
 * Author:      Thomas Scholz
 * Author URI:  http://toscho.de
 * Licence:     MIT
 * License URI: http://opensource.org/licenses/MIT
 */

add_action(
	'plugins_loaded',
	array ( T5_Rewrite::get_instance(), 'plugin_setup' )
);

class T5_Rewrite
{
	/**
	 * Plugin instance.
	 *
	 * @see get_instance()
	 * @type object
	 */
	protected static $instance = NULL;

	/**
	 * URL to this plugin's directory.
	 *
	 * @type string
	 */
	public $plugin_url = '';

	/**
	 * Path to this plugin's directory.
	 *
	 * @type string
	 */
	public $plugin_path = '';

	/**
	 * Access this plugin’s working instance
	 *
	 * @wp-hook plugins_loaded
	 * @return  object of this class
	 */
	public static function get_instance()
	{
		NULL === self::$instance and self::$instance = new self();

		return self::$instance;
	}

	/**
	 * Used for regular plugin work.
	 *
	 * @wp-hook plugins_loaded
	 * @return  void
	 */
	public function plugin_setup()
	{
		$this->plugin_url  = plugins_url( '/', __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );

		is_admin() && $this->load_language( 'plugin_t5_rewrite' );

		$fix_codestyling_localization_bug = __(
			'Adds special rewrite tags.',
			'plugin_t5_rewrite'
		);

		$this->load_rewrite_tags();
		add_action( 'in_admin_header', array ( $this, 'load_help' ) );
	}

	public function load_rewrite_tags()
	{
		$this->load_class( 'T5_Rewrite_Tag', FALSE );
		$this->load_class( 'T5_Rewrite_Tag_Post_Format' );
		$this->load_class( 'T5_Rewrite_Tag_Tag' );
		$this->load_class( 'T5_Rewrite_Tag_Custom' );

		do_action( 't5_rewrite_base_classes_loaded' );
	}

	public function load_help()
	{
		$this->load_class( 'T5_Rewrite_Tags_Help' );
	}

	protected function load_class( $class, $instance = TRUE )
	{
		$path = $this->plugin_path . "php/class.$class.php";
		class_exists( $class ) || require_once( $path );
		$instance && new $class;

		return $this;
	}

	/**
	 * Loads translation file.
	 *
	 * Accessible to other classes to load different language files (admin and
	 * front-end for example).
	 *
	 * @wp-hook wp_loaded
	 * @param   string $domain
	 * @since   2012.10.23
	 * @return  void
	 */
	public function load_language( $domain )
	{
		load_plugin_textdomain(
			$domain,
			FALSE,
			plugin_basename( dirname( __FILE__ ) ) . '/languages'
		);
	}
}