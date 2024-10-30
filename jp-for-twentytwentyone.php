<?php
/**
 * Japanized for Twenty Twenty-One plugin for WordPress
 *
 * @package jp-for-twentytwentyone
 * @author  ishitaka
 * @license GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Japanized for Twenty Twenty-One
 * Plugin URI:        https://xakuro.com/wordpress/japanized-for-twenty-twenty-one/
 * Description:       Customize Twenty Twenty-One theme for Japanese.
 * Version:           1.9.2
 * Requires at least: 5.5
 * Requires PHP:      7.2
 * Author:            Xakuro
 * Author URI:        https://xakuro.com/
 * License:           GPL v2 or later
 * Text Domain:       jp-for-twentytwentyone
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JP_FOR_TWENTYTWENTYONE_VERSION', '1.9.2' );

/**
 * JP_For_TwentyTwentyOne class.
 *
 * @since 1.0.0
 */
class JP_For_TwentyTwentyOne {
	public $options;
	public $customize;
	public $setup;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'jp-for-twentytwentyone' );

		register_activation_hook( __FILE__, array( $this, 'activation' ) );

		$this->options = get_option( 'jp_for_twentytwentyone_options' );
		if ( false === $this->options || version_compare( $this->options['plugin_version'], JP_FOR_TWENTYTWENTYONE_VERSION, '<' ) ) {
			$this->activation();
			$this->options = get_option( 'jp_for_twentytwentyone_options' );
		}

		require_once( plugin_dir_path( __FILE__ ) . 'includes/setup.php' );
		$this->setup = new JP_For_TwentyTwentyOne_Setup( $this );
	}

	/**
	 * Gets the default value of the options.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_default_options() {
		return array(
			'plugin_version' => JP_FOR_TWENTYTWENTYONE_VERSION,
		);
	}

	/**
	 * Gets the default value of the customize.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_default_customize() {
		return array(
			'noto_sans_jp'      => false,
			'font_weight'       => 400,
			'font_size_rate'    => 90,
			'social_icon'       => true,
			'social_icon_color' => true,
			'enable_powered_by' => false,
			'powered_by'        => 'Proudly powered by <a href="https://ja.wordpress.org/">WordPress</a>',
			'content_normal_width' => 610,
			'content_width'     => 750,
			'enable_breadcrumb' => false,
			'breadcrumb_exclude_homepage' => false,
			'breadcrumb_home'   => true,
			'breadcrumb_home_template' => 'HOME',
			'breadcrumb_blog'   => true,
			'enable_toc'        => true,
			'toc_auto_post_types' => array( 'post', 'page' ),
			'toc_homepage'      => false,
			'toc_position'      => 1,   // 0 = Without,
										// 1 = Before first heading, 2 = After first heading, 3 = Top, 4 = Bottom,
										// 5 = Top of the sidebar, 6 = End of the sidebar,
										// 7 = Top of the sidebar (Sticky), 8 = End of the sidebar (Sticky),
			'toc_show_when'     => 2,
			'toc_title'         => __( 'Table of contents', 'jp-for-twentytwentyone' ),
			'toc_open_text'     => __( 'Open', 'jp-for-twentytwentyone' ),
			'toc_close_text'    => __( 'Close', 'jp-for-twentytwentyone' ),
			'toc_top_level'     => 2,
			'toc_depth'         => 3,
			'toc_initial_view'  => true,
			'toc_scroll'        => 'smooth', // 'smooth' or 'auto'
			'enable_scrolltop'  => true,
			'scrolltop_scroll'  => 'smooth', // 'smooth' or 'auto'
			'scrolltop_type'    => 'image', // 'image' or 'text'
			'scrolltop_text'    => __( 'Scroll to Top', 'jp-for-twentytwentyone' ),
			'show_sidebar'      => array( 'post', 'page', 'archive' ), // 'home', 'post', 'page' or 'archive'
			'sidebar_position'  => 'right', // 'left' or 'right'
			'skin'              => '', // '' or 'light'
			'header_image'      => '',
			'header_height'     => 0,
		);
	}

	/**
	 * Plugin activation.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function activation() {
		$options = get_option( 'jp_for_twentytwentyone_options' );
		if ( false === $options ) {
			add_option( 'jp_for_twentytwentyone_options', JP_For_TwentyTwentyOne::get_default_options() );
		} else {
			$options['plugin_version'] = JP_FOR_TWENTYTWENTYONE_VERSION;
			$options = wp_parse_args( $options, JP_For_TwentyTwentyOne::get_default_options() );
			update_option( 'jp_for_twentytwentyone_options', $options );
		}

		$customize = get_option( 'jp_for_twentytwentyone_customize' );
		if ( false === $customize ) {
			add_option( 'jp_for_twentytwentyone_customize', JP_For_TwentyTwentyOne::get_default_customize() );
		} else {
			$customize = wp_parse_args( $customize, JP_For_TwentyTwentyOne::get_default_customize() );
			update_option( 'jp_for_twentytwentyone_customize', $customize );
		}
	}

	/**
	 * Plugin deactivation.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function uninstall() {
		global $wpdb;

		if ( ! is_multisite() ) {
			delete_option( 'jp_for_twentytwentyone_options' );
			delete_option( 'jp_for_twentytwentyone_customize' );
		} else {
			$current_blog_id = get_current_blog_id();
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				delete_option( 'jp_for_twentytwentyone_options' );
				delete_option( 'jp_for_twentytwentyone_customize' );
			}
			switch_to_blog( $current_blog_id );
		}
	}
}

register_uninstall_hook( __FILE__, 'JP_For_TwentyTwentyOne::uninstall' );

global $jp_for_twentytwentyone;
$jp_for_twentytwentyone = new JP_For_TwentyTwentyOne();

require_once( plugin_dir_path( __FILE__ ) . 'includes/breadcrumb.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/toc.php' );

/**
 * Display or retrieve the breadcrumb.
 *
 * @since 1.3.0
 *
 * @param array $args Optional. Arguments to retrieve breadcrumb.
 * @return void
 */
function jp_for_twentytwentyone_breadcrumb( $args = null ) {
	$breadcrumb = new JP_For_TwentyTwentyOne_Breadcrumb();
	echo $breadcrumb->get_breadcrumb( $args ); // WPCS: XSS ok;
}
