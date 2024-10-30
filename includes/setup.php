<?php
/**
 * JP For TwentyTwentyOne setup.
 *
 * @package jp-for-twentytwentyone
 */

/**
 * JP For TwentyTwentyOne setup class.
 */
class JP_For_TwentyTwentyOne_Setup {
	private $parent;
	private $sidebar;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
	}

	/**
	 * Loaded after setting the theme.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	function after_setup_theme() {
		if ( 'twentytwentyone' !== get_template() ) return;

		require_once( plugin_dir_path( __DIR__ ) . 'includes/scroll-top.php' );
		new JP_For_TwentyTwentyOne_Scroll_Top( $this->parent );

		if ( is_customize_preview() ) {
			require_once( plugin_dir_path( __DIR__ ) . 'admin/customizer.php' );
			new JP_For_TwentyTwentyOne_Admin();
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_script' ) );
		add_action( 'wp', array( $this, 'wp' ) );
		add_filter( 'twenty_twenty_one_content_width', array( $this, 'content_width' ) );
		add_action( 'body_class', array( $this, 'body_class' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ), 9 );
	}

	/**
	 * Fires once the WordPress environment has been set up.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function wp() {
		$this->parent->customize = get_option( 'jp_for_twentytwentyone_customize' );

		if ( $this->parent->customize['social_icon'] ?? true ) {
			add_filter( 'twenty_twenty_one_svg_icons_social', array( $this, 'svg_icons_social' ) );
			add_filter( 'twenty_twenty_one_social_icons_map', array( $this, 'social_icons_map' ) );
		}

		add_action( 'template_redirect', array( $this, 'template_redirect' ) );

		if ( $this->parent->customize['enable_toc'] ?? true ) {
			add_shortcode( 'toc', array( 'JP_For_TwentyTwentyOne_TOC', 'shortcode_toc' ) );
			add_filter( 'the_content', array( $this, 'insert_header_id' ) );
			add_filter( 'the_content', array( $this, 'the_content' ) );
		}
	}

	/**
	 * Has the sidebar.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	private function has_sidebar() {
		if ( ! isset( $this->parent->customize['show_sidebar'] ) ) {
			return false;
		}

		$has = false;
		if ( is_front_page() ) {
			$has = in_array( 'home', $this->parent->customize['show_sidebar'] );
		} elseif ( is_single() ) {
			$has = in_array( 'post', $this->parent->customize['show_sidebar'] );
		} elseif ( is_page() ) {
			$has = in_array( 'page', $this->parent->customize['show_sidebar'] );
		} elseif ( is_home() || is_archive() || is_search() ) {
			$has = in_array( 'archive', $this->parent->customize['show_sidebar'] );
		}
		return $has;
	}

	/**
	 * Add the class name to the body element.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	function body_class( $classes ) {
		if ( $this->has_sidebar() )  {
			$classes[] = 'has-sidebar';
			if ( 'left' === ( $this->parent->customize['sidebar_position'] ?? 'right' ) )  {
				$classes[] = 'has-left-sidebar';
			} else {
				$classes[] = 'has-right-sidebar';
			}
		}

		if ( isset( $this->parent->customize['header_image'] ) && ! empty( $this->parent->customize['header_image'] ) ) {
			$classes[] = 'jp-custom-header';
		}

		return $classes;
	}

	/**
	 * Get the Content width.
	 *
	 * @since 1.2.0
	 *
	 * @param int $context_width
	 * @return int
	 */
	function content_width( $content_width ) {
		return (int) ( $this->parent->customize['content_width'] ?? 750 );
	}

	/**
	 * Generate font family style.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $context Can be "editor" or null.
	 * @return string
	 */
	function generate_custom_font_family_style( $context = null ) {
		$css = 'editor' === $context ? ':root .editor-styles-wrapper{' : ':root{';
		$css .= '--global--font-primary: var(--font-headings, "Noto Sans JP", "Helvetica Neue", "Helvetica", "Hiragino Sans", "Hiragino Kaku Gothic ProN", Arial, "Yu Gothic", Meiryo, sans-serif);';
		$css .= '--global--font-secondary: var(--font-base, "Noto Sans JP", "Helvetica Neue", "Helvetica", "Hiragino Sans", "Hiragino Kaku Gothic ProN", Arial, "Yu Gothic", Meiryo, sans-serif);';
		$css .= '}';
		return $css;
	}

	/**
	 * Generate responsive default width.
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	function generate_responsive_default_width_style() {
		$width = (int) $this->parent->customize['content_normal_width'] ?? 610;

		$css  = '@media only screen and (min-width: 482px) {';
		$css .= ':root {';
		$css .= '--responsive--aligndefault-width: min(calc(100vw - 4 * var(--global--spacing-horizontal)), ' . $width . 'px);';
		$css .= '}';
		$css .= '}';

		$css .= '@media only screen and (min-width: 822px) {';
		$css .= ':root {';
		$css .= '--responsive--aligndefault-width: min(calc(100vw - 8 * var(--global--spacing-horizontal)), ' . $width . 'px);';
		$css .= '}';
		$css .= '}';
	
		return $css;
	}

	/**
	 * Generate social icons color style.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function generate_social_icon_color_style() {
		$css  = '.footer-navigation a[href*="amazon.com"] svg,';
		$css .= '.footer-navigation a[href*="amazon.cn"] svg,';
		$css .= '.footer-navigation a[href*="amazon.in"] svg,';
		$css .= '.footer-navigation a[href*="amazon.fr"] svg,';
		$css .= '.footer-navigation a[href*="amazon.de"] svg,';
		$css .= '.footer-navigation a[href*="amazon.it"] svg,';
		$css .= '.footer-navigation a[href*="amazon.nl"] svg,';
		$css .= '.footer-navigation a[href*="amazon.es"] svg,';
		$css .= '.footer-navigation a[href*="amazon.co"] svg,';
		$css .= '.footer-navigation a[href*="amazon.ca"] svg { fill: #ff9900; }';
		$css .= '.footer-navigation a[href*="behance.net"] svg { fill: #1769ff; }';
		$css .= '.footer-navigation a[href*="codepen.io"] svg { fill: #000000; }';
		$css .= '.footer-navigation a[href*="deviantart.com"] svg { fill: #05cc47; }';
		$css .= '.footer-navigation a[href*="dribbble.com"] svg { fill: #f46899; }';
		$css .= '.footer-navigation a[href*="dropbox.com"] svg { fill: #007ee5; }';
		$css .= '.footer-navigation a[href*="facebook.com"] svg,';
		$css .= '.footer-navigation a[href*="fb.me/"] svg { fill: #3b5998; }';
		$css .= '.footer-navigation a[href*="flickr.com"] svg { fill: #ff0084; }';
		$css .= '.footer-navigation a[href*="foursquare.com"] svg { fill: #0072b1; }';
		$css .= '.footer-navigation a[href*="plus.google.com"] svg { fill: #dd4b39; }';
		$css .= '.footer-navigation a[href*="github.com"] svg { fill: #000000; }';
		$css .= '.footer-navigation a[href*="instagram.com"] svg { fill: #d93175; }';
		$css .= '.footer-navigation a[href*="linkedin.com"] svg { fill: #0077B5; }';
		$css .= '.footer-navigation a[href*="medium.com"] svg { fill: #000000; }';
		$css .= '.footer-navigation a[href*="meetup.com"] svg { fill: #ed1c40; }';
		$css .= '.footer-navigation a[href*="pinterest.com"] svg { fill: #bd081c; }';
		$css .= '.footer-navigation a[href*="getpocket.com"] svg { fill: #ee4256; }';
		$css .= '.footer-navigation a[href*="reddit.com"] svg { fill: #ff4500; }';
		$css .= '.footer-navigation a[href*="skype.com"] svg { fill: #00aff0; }';
		$css .= '.footer-navigation a[href*="skype:"] svg { fill: #00aff0; }';
		$css .= '.footer-navigation a[href*="snapchat.com"] svg { fill: #fffc00; }';
		$css .= '.footer-navigation a[href*="soundcloud.com"] svg { fill: #ff5419; }';
		$css .= '.footer-navigation a[href*="spotify.com"] svg { fill: #00e461; }';
		$css .= '.footer-navigation a[href*="tumblr.com"] svg { fill: #35465c; }';
		$css .= '.footer-navigation a[href*="twitch.tv"] svg { fill: #6441a4; }';
		$css .= '.footer-navigation a[href*="twitter.com"] svg { fill: #55acee; }';
		$css .= '.footer-navigation a[href*="vimeo.com"] svg { fill: #aad450; }';
		$css .= '.footer-navigation a[href*="vine.co"] svg { fill: #00bf8f; }';
		$css .= '.footer-navigation a[href*="vk.com"] svg { fill: #45668e; }';
		$css .= '.footer-navigation a[href*="wordpress.org"] svg { fill: #464646; }';
		$css .= '.footer-navigation a[href*="wordpress.com"] svg { fill: #21759b; }';
		$css .= '.footer-navigation a[href*="yelp.com"] svg { fill: #d32323; }';
		$css .= '.footer-navigation a[href*="youtube.com"] svg { fill: #cd201f; }';
		$css .= '.footer-navigation a[href^="mailto:"] svg { fill: var(--footer--color-link); }';
		$css .= '.footer-navigation a[href*="hatena.ne.jp/"] svg { fill: #5279e7; }';
		$css .= '.footer-navigation a[href*="line.me/"] svg { fill: #00c300; }';
		$css .= '.footer-navigation a[href*="lin.ee/"] svg { fill: #00c300; }';
		$css .= '.footer-navigation a[href*="rakuten.co.jp/"] svg { fill: #bf0000; }';
		return $css;
	}

	/**
	 * Generate font size style.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $context Can be "editor" or null.
	 * @return string
	 */
	function generate_font_size_style( $context = null ) {
		$r = $this->parent->customize['font_size_rate'] ?? 100;
		if ( 100 === $r ) return;
		$r = $r / 100;

		$css = 'editor' === $context ? ':root .editor-styles-wrapper{' : ':root{';
		$css .= sprintf( '--global--font-size-base: %.2frem;', $r * 1.25 );
		$css .= sprintf( '--global--font-size-xs: %.2frem;',   $r * 1 );
		$css .= sprintf( '--global--font-size-sm: %.2frem;',   $r * 1.125 );
		$css .= sprintf( '--global--font-size-md: %.2frem;',   $r * 1.25 );
		$css .= sprintf( '--global--font-size-lg: %.2frem;',   $r * 1.5 );
		$css .= sprintf( '--global--font-size-xl: %.2frem;',   $r * 2.25 );
		$css .= sprintf( '--global--font-size-xxl: %.2frem;',  $r * 4 );
		$css .= sprintf( '--global--font-size-xxxl: %.2frem;', $r * 5 );
		$css .= '--global--font-size-page-title: var(--global--font-size-xxl);';
		$css .= '--global--letter-spacing: normal;';
		$css .= '}';

		$css .= '@media only screen and (min-width: 652px) {';
		$css .= 'editor' === $context ? ':root .editor-styles-wrapper{' : ':root{';
		$css .= sprintf( '--global--font-size-xl: %.2frem;',   $r * 2.5 );
		$css .= sprintf( '--global--font-size-xxl: %.2frem;',  $r * 6 );
		$css .= sprintf( '--global--font-size-xxxl: %.2frem;', $r * 9 );
		$css .= sprintf( '--heading--font-size-h3: %.2frem;',  $r * 2 );
		$css .= sprintf( '--heading--font-size-h2: %.2frem;',  $r * 3 );
		$css .= '}';
		$css .= '}';

		return $css;
	}

	/**
	 * Enqueue Noto Sans font styles.
	 *
	 * @since 1.0.0
	 *
	 * @return string handple.
	 */
	private function enqueue_noto_sans_font() {
		$weight = $this->parent->customize['font_weight'] ?? 400;
		if ( ! in_array( $weight, array( 100, 300, 400, 500, 700, 900 ) ) ) {
			$weight = 400;
		}

		$handle = 'noto-sans-jp';
		$url = sprintf( '//fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@%s&display=swap', $weight );

		wp_enqueue_style( $handle, $url );

		return $handle;
	}

	/**
	 * Generates header styles.
	 *
	 * @since 1.8.0
	 *
	 * @return string handple.
	 */
	private function header_style() {
		$style = '';

		$url = $this->parent->customize['header_image'] ?? false;
		if ( $url ) {
			$style .= '#masthead { background-image: url("' . esc_url( $url ). '"); }';
		}

		$height = $this->parent->customize['header_height'] ?? false;
		if ( $height ) {
			$style .= '#masthead { min-height: ' . $height . 'px; }';
		}
		return $style;
	}

	/**
	 * Enqueue styles and scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function enqueue_scripts() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'jp-for-twentytwentyone', plugins_url( 'css/style.css', __DIR__ ), array( 'twenty-twenty-one-style' ), JP_FOR_TWENTYTWENTYONE_VERSION );
		if ( $header_style = $this->header_style() ) {
			wp_add_inline_style( 'jp-for-twentytwentyone', $header_style );
		}

		$css = $this->generate_font_size_style();

		if ( $this->parent->customize['social_icon_color'] ?? true ) {
			$css .= $this->generate_social_icon_color_style();
		}

		if ( 610 != $this->parent->customize['content_normal_width'] ?? 610 ) {
			$css .= $this->generate_responsive_default_width_style();
		}

		wp_add_inline_style( 'jp-for-twentytwentyone', $css );

		if ( $skin = $this->parent->customize['skin'] ?? false ) {
			$path = plugin_dir_path( __DIR__ ) . "skins/{$skin}/style.css";
			if ( file_exists( $path ) ) {
				wp_enqueue_style( 'jp-for-twentytwentyone-skin', plugins_url( "skins/{$skin}/style.css", __DIR__ ), array( 'twenty-twenty-one-style' ), JP_FOR_TWENTYTWENTYONE_VERSION );
			}
		}

		if ( $this->parent->customize['noto_sans_jp'] ?? false ) {
			$handle = $this->enqueue_noto_sans_font();
			wp_add_inline_style( $handle, $this->generate_custom_font_family_style() );
		}

		if ( $this->parent->customize['enable_toc'] ?? true ) {
			wp_enqueue_script( 'jp-for-twentytwentyone-toc', plugins_url( "js/toc{$min}.js", __DIR__ ), array(), JP_FOR_TWENTYTWENTYONE_VERSION, true );

			$customize = array(
				'toc_open_text' => $this->parent->customize['toc_open_text'] ?? __( 'Open', 'jp-for-twentytwentyone' ),
				'toc_close_text' => $this->parent->customize['toc_close_text'] ?? __( 'Close', 'jp-for-twentytwentyone' ),
				'toc_scroll' => $this->parent->customize['toc_scroll'] ?? 'smooth',
			);
			wp_localize_script( 'jp-for-twentytwentyone-toc', 'jp_for_twentytwentyone_toc', $customize );
		}
	}

	/**
	 * Enqueue block editor script.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function block_editor_script() {
		$this->parent->customize = get_option( 'jp_for_twentytwentyone_customize' );

		wp_enqueue_style( 'jp-for-twentytwentyone-editor',  plugins_url( 'admin/css/style-editor.css', __DIR__ ), array(),  JP_FOR_TWENTYTWENTYONE_VERSION );

		$css = $this->generate_font_size_style( 'editor' );

		if ( $this->parent->customize['social_icon_color'] ?? true ) {
			$css .= $this->generate_social_icon_color_style( 'editor' );
		}

		if ( 610 != $this->parent->customize['content_normal_width'] ?? 610 ) {
			$css .= $this->generate_responsive_default_width_style();
		}

		wp_add_inline_style( 'jp-for-twentytwentyone-editor', $css );

		if ( $this->parent->customize['noto_sans_jp'] ?? false ) {
			$handle = $this->enqueue_noto_sans_font();
			wp_add_inline_style( $handle, $this->generate_custom_font_family_style( 'editor' ) );
		}
	}

	/**
	 * Register the widgets.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	function widgets_init() {
		register_sidebar( array(
			'name'          => __( 'Sidebar', 'jp-for-twentytwentyone' ),
			'id'            => 'sidebar-post-1',
			'description'   => __( 'Widgets in this area are displayed in the sidebar.', 'jp-for-twentytwentyone' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );

		register_sidebar( array(
			'name'          => __( 'Sidebar (Sticky)', 'jp-for-twentytwentyone' ),
			'id'            => 'sidebar-post-2',
			'description'   => __( 'Widgets in this area are displayed in the sidebar. Follows the scroll.', 'jp-for-twentytwentyone' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );

		register_sidebar( array(
			'name'          => __( 'Page Sidebar', 'jp-for-twentytwentyone' ),
			'id'            => 'sidebar-page-1',
			'description'   => __( 'Widgets in this area are displayed in the sidebar of the page.', 'jp-for-twentytwentyone' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );

		register_sidebar( array(
			'name'          => __( 'Page Sidebar (Sticky)', 'jp-for-twentytwentyone' ),
			'id'            => 'sidebar-page-2',
			'description'   => __( 'Widgets in this area are displayed in the sidebar of the page. Follows the scroll.', 'jp-for-twentytwentyone' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}

	/**
	 * Twenty Twenty-Ones's array of icons.
	 *
	 * @since 1.0.0
	 *
	 * @param array $social_icons Array of default social icons.
	 * @return array
	 */
	function svg_icons_social( $social_icons ) {
		if ( ! isset( $social_icons['hatena'] ) ) {
			$social_icons['hatena'] = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 24 24" height="24" width="24"><path d="m 19.065497,2 c 1.626649,0 2.944653,1.3180031 2.944653,2.9446524 V 19.075646 c 0,1.626649 -1.318004,2.944653 -2.944653,2.944653 H 4.934503 c -1.6266501,0 -2.9446531,-1.318004 -2.9446531,-2.944653 V 4.9446524 C 1.9898499,3.3180031 3.3078528,2 4.934503,2 Z m -3.090634,12.070572 c -0.650659,0 -1.176192,0.525532 -1.176192,1.176193 0,0.65066 0.525533,1.179529 1.176192,1.179529 0.65066,0 1.176193,-0.538045 1.176193,-1.188705 0,-0.650659 -0.525533,-1.176193 -1.176193,-1.176193 z m -6.80273,2.316515 c 1.001015,0 1.715072,-0.03504 2.152182,-0.100101 0.438778,-0.07007 0.814159,-0.185189 1.101117,-0.343682 0.37538,-0.193529 0.650659,-0.470477 0.850862,-0.825837 0.200203,-0.355361 0.300305,-0.763274 0.300305,-1.234585 0,-0.650659 -0.175178,-1.170353 -0.525533,-1.559915 -0.350356,-0.400405 -0.825837,-0.612287 -1.451472,-0.662338 0.550558,-0.150152 0.964311,-0.375381 1.214565,-0.675685 0.262766,-0.286958 0.387893,-0.687364 0.387893,-1.1878727 0,-0.400405 -0.08592,-0.7382475 -0.250253,-1.0510646 C 12.776621,8.4457021 12.540548,8.2079611 12.215218,8.0202708 11.927426,7.8576059 11.602096,7.7575044 11.201691,7.6824282 10.814632,7.620699 10.1256,7.5823267 9.1379311,7.5823267 H 6.706299 v 8.7472023 h 2.465834 z m 0.613956,-3.491039 c 0.588096,0 0.988502,0.07341 1.201218,0.218555 0.225227,0.150152 0.325329,0.412918 0.325329,0.775787 0,0.337842 -0.112614,0.575582 -0.350354,0.713223 -0.225229,0.150151 -0.638147,0.211882 -1.201219,0.211882 H 8.921879 v -1.91611 h 0.875888 z m 7.220655,0.588929 V 7.5956736 h -2.052082 v 5.8893034 h 2.050413 z M 9.434899,9.5743463 c 0.592268,0 0.988502,0.06673 1.194544,0.200202 0.204375,0.133469 0.306144,0.3628687 0.306144,0.6923697 0,0.316989 -0.108443,0.538881 -0.32533,0.67068 -0.221057,0.128463 -0.623131,0.19353 -1.2112269,0.19353 H 8.9235471 V 9.5743463 h 0.513021 z"></path></svg>';
		}
		if ( ! isset( $social_icons['line'] ) ) {
			$social_icons['line'] = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 24 24" height="24" width="24"><path d="m 18.524755,10.146597 c 0.305316,0 0.551143,0.249327 0.551143,0.552018 0,0.301817 -0.245827,0.551143 -0.551143,0.551143 h -1.535326 v 0.984184 h 1.535326 c 0.305316,0 0.551143,0.247577 0.551143,0.551143 0,0.300941 -0.245827,0.550268 -0.551143,0.550268 h -2.087344 c -0.301816,0 -0.548518,-0.249327 -0.548518,-0.550268 V 8.6112709 c 0,-0.3018163 0.246702,-0.5511428 0.551143,-0.5511428 h 2.087344 c 0.302691,0 0.548518,0.2493265 0.548518,0.5511428 0,0.3053157 -0.245827,0.5511429 -0.551143,0.5511429 h -1.535326 v 0.9841832 z m -3.372469,2.638488 c 0,0.236204 -0.15222,0.446163 -0.377926,0.521398 -0.05599,0.01837 -0.116353,0.02712 -0.174092,0.02712 -0.184589,0 -0.342058,-0.07874 -0.446163,-0.218707 l -2.137209,-2.901811 v 2.572 c 0,0.300941 -0.244078,0.550268 -0.552018,0.550268 -0.302691,0 -0.547644,-0.249327 -0.547644,-0.550268 V 8.6112709 c 0,-0.2362041 0.151346,-0.4461632 0.376177,-0.5205238 0.05249,-0.020121 0.118977,-0.028869 0.169717,-0.028869 0.170592,0 0.328061,0.090982 0.433041,0.2222068 L 14.05,11.197268 V 8.6112709 c 0,-0.3018163 0.246702,-0.5511428 0.551143,-0.5511428 0.301817,0 0.551143,0.2493265 0.551143,0.5511428 z m -5.022399,0 c 0,0.300941 -0.2467016,0.550268 -0.5520173,0.550268 -0.3018163,0 -0.5485183,-0.249327 -0.5485183,-0.550268 V 8.6112709 c 0,-0.3018163 0.246702,-0.5511428 0.5511428,-0.5511428 0.3026912,0 0.5493928,0.2493265 0.5493928,0.5511428 z M 7.9725568,13.335353 H 5.8852126 c -0.3018163,0 -0.5511429,-0.249327 -0.5511429,-0.550268 V 8.6112709 c 0,-0.3018163 0.2493266,-0.5511428 0.5511429,-0.5511428 0.3044408,0 0.5511429,0.2493265 0.5511429,0.5511428 v 3.6226711 h 1.5362013 c 0.3044408,0 0.550268,0.247577 0.550268,0.551143 0,0.300941 -0.246702,0.550268 -0.550268,0.550268 M 22.579592,10.541146 c 0,-4.6987118 -4.710959,-8.5225935 -10.497959,-8.5225935 -5.787,0 -10.4979592,3.8238817 -10.4979592,8.5225935 0,4.208807 3.7355238,7.735246 8.7789182,8.405366 0.342059,0.07174 0.807468,0.225706 0.92557,0.516149 0.10498,0.263324 0.06911,0.67012 0.03324,0.944817 l -0.143472,0.892326 c -0.03937,0.263324 -0.20996,1.037549 0.917696,0.564266 1.129406,-0.471534 6.050324,-3.567557 8.254896,-6.101939 1.508206,-1.652554 2.229066,-3.34535 2.229066,-5.220985"></path></svg>';
		}
		if ( ! isset( $social_icons['rakuten'] ) ) {
			$social_icons['rakuten'] = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 24 24" height="24" width="24"><path d="M 21.487221,19.79568 5.3590661,22.047516 2.6760444,19.79568 Z M 8.41239,18.107636 H 5.3590661 V 2.0311897 h 5.0474509 a 5.5486925,5.5486925 0 0 1 5.54619,5.5428545 c 0,1.8631864 -0.924087,3.5061928 -2.334404,4.5186858 l 4.518686,6.014072 H 14.313036 L 10.571651,13.120235 H 8.41239 Z m 0,-8.039891 h 1.994961 A 2.4953687,2.4953687 0 0 0 12.900217,7.5748782 2.4970367,2.4970367 0 0 0 10.407351,5.0845135 H 8.41239 Z"/></svg>';
		}
		return $social_icons;
	}

	/**
	 * Twenty Twenty-Ones's array of domain mappings for social icons.
	 *
	 * @since 1.0.0
	 *
	 * @param array $social_icons_map Array of default social icons.
	 * @return array
	 */
	function social_icons_map( $social_icons_map ) {
		if ( ! isset( $social_icons_map['hatena'] ) ) {
			$social_icons_map['hatena'] = array(
				'b.hatena.ne.jp',
			);
		}
		if ( ! isset( $social_icons_map['line'] ) ) {
			$social_icons_map['line'] = array(
				'line.me',
				'lin.ee',
			);
		}
		if ( ! isset( $social_icons_map['rakuten'] ) ) {
			$social_icons_map['rakuten'] = array(
				'rakuten.co.jp',
			);
		}
		return $social_icons_map;
	}

	/**
	 * Fires before determining which template to load.
	 *
	 * @since 1.1.1
	 *
	 * @return void
	 */
	function template_redirect() {
		$this->sidebar = $this->get_sidebar();
		if (
			( $this->parent->customize['enable_breadcrumb'] ?? false ) || 
			( $this->parent->customize['enable_powered_by'] ?? false ) ||
			! empty( $this->sidebar )
		) {
			ob_start( array( $this, 'filter_html' ) );
		}
	}

	/**
	 * Get the Powered by text.
	 *
	 * @since 1.4.2
	 *
	 * @return string
	 */
	private function get_powered_by_text() {
		if ( ! isset( $this->parent->customize['powered_by'] ) ) {
			return '';
		}
		$search = array( '%copy%', '%year%', '%site_url%' );
		$replace = array( '&copy;', date_i18n( 'Y' ), esc_url( home_url( '/' ) ) );
		return str_replace( $search, $replace, $this->parent->customize['powered_by'] );
	}

	/**
	 * Get the Table of Contents widget.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	private function get_toc_widget() {
		$top_level = $this->parent->customize['toc_top_level'] ?? 2;
		$depth     = $this->parent->customize['toc_depth'] ?? 3;

		$shortcode = sprintf( '[toc id="jftto-toc" showcount="%d" title="%s" opentext="%s" closetext="%s" toplevel="%d" depth="%d" initialview="%s"]', 
			$this->parent->customize['toc_show_when'] ?? 4,
			$this->parent->customize['toc_title'] ?? __( 'Table of contents', 'jp-for-twentytwentyone' ),
			$this->parent->customize['toc_open_text'] ?? __( 'Open', 'jp-for-twentytwentyone' ),
			$this->parent->customize['toc_close_text'] ?? __( 'Close', 'jp-for-twentytwentyone' ),
			$top_level,
			min( $depth, 6 - $top_level ),
			( $this->parent->customize['toc_initial_view'] ?? true ) ? 'true' : 'false'
		);

		$html  = '<section id="toc-1" class="widget widget_toc">';
		$html .= '<div class="tocwidget">';
		$html .= do_shortcode( $shortcode );
		$html .= '</div>';
		$html .= '</section>';
		return $html;
	}

	/**
	 * Get the sidebar.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	private function get_sidebar() {
		$html = '';

		$toc_position = $this->parent->customize['toc_position'] ?? 0;
		if ( 5 <= $toc_position ) {
			if ( is_singular() ) {
				if ( is_front_page() ) {
					if ( ! ( $this->parent->customize['toc_homepage'] ?? false ) ) {
						$toc_position = 0;
					}
				} else {
					$post_types = $this->parent->customize['toc_auto_post_types'] ?? array();
					$post_type = get_post_type();
					if ( ! in_array( $post_type, $post_types ) ) {
						$toc_position = 0;
					}
				}
			} else {
				$toc_position = 0;
			}
		}

		if ( $this->has_sidebar() ) {
			$html .= '<aside id="sidebar" role="complementary">';
			if ( is_page() ) {
				$sidebar = ( 5 == $toc_position ) ? $this->get_toc_widget() : '';
				if ( is_active_sidebar( 'sidebar-page-1' ) ) {
					ob_start();
					dynamic_sidebar( 'sidebar-page-1' );
					$sidebar .= ob_get_clean();
				}
				$sidebar .= ( 6 == $toc_position ) ? $this->get_toc_widget() : '';
				if ( ! empty( $sidebar ) ) {
					$html .= "<div class=\"sidebar-1 sidebar-widget-area\">{$sidebar}</div>";
				}

				$sidebar = ( 7 == $toc_position ) ? $this->get_toc_widget() : '';
				if ( is_active_sidebar( 'sidebar-page-2' ) ) {
					ob_start();
					dynamic_sidebar( 'sidebar-page-2' );
					$sidebar .= ob_get_clean();
				}
				$sidebar .= ( 8 == $toc_position ) ? $this->get_toc_widget() : '';
				if ( ! empty( $sidebar ) ) {
					$html .= "<div class=\"sidebar-2 sidebar-widget-area\">{$sidebar}</div>";
				}
			} else {
				$sidebar = ( 5 == $toc_position ) ? $this->get_toc_widget() : '';
				if ( is_active_sidebar( 'sidebar-post-1' ) ) {
					ob_start();
					dynamic_sidebar( 'sidebar-post-1' );
					$sidebar .= ob_get_clean();
				}
				$sidebar .= ( 6 == $toc_position ) ? $this->get_toc_widget() : '';
				if ( ! empty( $sidebar ) ) {
					$html .= "<div class=\"sidebar-1 sidebar-widget-area\">{$sidebar}</div>";
				}

				$sidebar = ( 7 == $toc_position ) ? $this->get_toc_widget() : '';
				if ( is_active_sidebar( 'sidebar-post-2' ) ) {
					ob_start();
					dynamic_sidebar( 'sidebar-post-2' );
					$sidebar .= ob_get_clean();
				}
				$sidebar .= ( 8 == $toc_position ) ? $this->get_toc_widget() : '';
				if ( ! empty( $sidebar ) ) {
					$html .= "<div class=\"sidebar-2 sidebar-widget-area\">{$sidebar}</div>";
				}
			}
			$html .= '</aside>';
		}
		return $html;
	}

	/**
	 * Filter HTML.
	 *
	 * @since 1.1.1
	 *
	 * @param string $buffer HTML.
	 * @return string HTML.
	 */
	function filter_html( $buffer ) {

		libxml_use_internal_errors( true );

		$doc = new DOMDocument();
		$doc->loadHTML( '<?xml encoding="UTF-8">' . $buffer );
		$doc->formatOutput = false;
		$doc->preserveWhiteSpace = false;

		$update = false;

		if ( ! empty( $this->sidebar ) ) {
			if ( $content_node = $doc->getElementById( 'content' ) ) {
				$html = mb_convert_encoding( $this->sidebar, 'HTML-ENTITIES', 'utf-8' );

				$newdoc = new DOMDocument();
				$newdoc->loadHTML( $html );
				$newnode = $newdoc->getElementsByTagName( 'aside' )->item( 0 );
				$newnode = $doc->importNode( $newnode, true );

				$content_node->appendChild( $newnode );

				$update = true;
			}
		}

		if ( $this->parent->customize['enable_breadcrumb'] ?? false ) {
			$breadcrumb_exclude_homepage = $this->parent->customize['breadcrumb_exclude_homepage'] ?? false;
			if ( ! $breadcrumb_exclude_homepage || ! is_front_page() ) {
				if ( $page_node = $doc->getElementById( 'page' ) ) {
					$breadcrumb = new JP_For_TwentyTwentyOne_Breadcrumb();
					$breadcrumb_html = $breadcrumb->get_breadcrumb( array(
						'home' => $this->parent->customize['breadcrumb_home'] ?? true,
						'home_template' => $this->parent->customize['breadcrumb_home_template'] ?? '',
						'blog' => $this->parent->customize['breadcrumb_blog'] ?? true,
					));
					$breadcrumb_html = mb_convert_encoding( $breadcrumb_html, 'HTML-ENTITIES', 'utf-8' );

					$newdoc = new DOMDocument();
					$newdoc->loadHTML( $breadcrumb_html );
					$newnode = $newdoc->getElementsByTagName( 'nav' )->item( 0 );
					$newnode = $doc->importNode( $newnode, true );

					if ( $content_node = $doc->getElementById( 'content' ) ) {
						$page_node->insertBefore( $newnode, $content_node );
					}

					$update = true;
				}
			}
		}

		if ( $this->parent->customize['enable_powered_by'] ?? false ) {
			if ( $colophon_node = $doc->getElementById( 'colophon' ) ) {
				$path = new DomXPath( $doc );
				if ( $poweredby_node = $path->query( '//div[@class="powered-by"]', $colophon_node )->item( 0 ) ) {

					while ( $poweredby_node->childNodes->length ) {
						$poweredby_node->removeChild( $poweredby_node->firstChild );
					}

					$poweredby_html = $this->get_powered_by_text();
					$poweredby_html = mb_convert_encoding( $poweredby_html, 'HTML-ENTITIES', 'utf-8' );
					$newdoc = new DOMDocument();
					$newdoc->loadHTML( "<div>{$poweredby_html}</div>" );

					foreach ( $newdoc->getElementsByTagName( 'div' )->item( 0 )->childNodes as $node ) {
						$node = $poweredby_node->ownerDocument->importNode( $node, true );
						$poweredby_node->appendChild( $node );
					}

					$update = true;
				}
			}
		}

		if ( $update ) {
			$buffer = $doc->saveXML( $doc->doctype ) . $doc->saveHTML( $doc->documentElement );
		}

		libxml_clear_errors();

		return $buffer;
	}

	/**
	 * Insert the ID in the header tag.
	 *
	 * @since 1.8.6
	 * 
	 * @param string $content Content.
	 * @return string Content.
	 */
	function insert_header_id( $content ) {
		$elements = wp_html_split( $content );
		$toc_id = 1;
		foreach ( $elements as &$element ) {
			if ( 0 === strpos( $element, '<h' ) ) {
				if ( ! preg_match( '/<h[1-6](.*?) id="([^"]*)"/u', $element ) ) {
					$s = preg_replace( '/<(h[1-6])(.*?)>/u', '<${1} id="toc' . $toc_id . '" ${2}>', $element );
					if ( $element !== $s ) {
						$element = $s;
						$toc_id++;
					}
				}
			}
		}
		return join( $elements );
	}

	/**
	 * Insert the toc shortcode into the content.
	 *
	 * @since 1.4.0
	 * 
	 * @param string $content Content.
	 * @return string Content.
	 */
	function the_content( $content ) {
		if (
			! is_singular() ||
			( is_front_page() && ! ( $this->parent->customize['toc_homepage'] ?? false ) )
		) {
			return $content;
		}

		$post_types = $this->parent->customize['toc_auto_post_types'] ?? array();
		$post_type  = get_post_type();
		if ( ! in_array( $post_type, $post_types ) ) {
			return $content;
		}

		$top_level = $this->parent->customize['toc_top_level'] ?? 2;
		$depth     = $this->parent->customize['toc_depth'] ?? 3;

		$shortcode = sprintf( '[toc id="jftto-toc" showcount="%d" title="%s" opentext="%s" closetext="%s" toplevel="%d" depth="%d" initialview="%s"]', 
			$this->parent->customize['toc_show_when'] ?? 4,
			$this->parent->customize['toc_title'] ?? __( 'Table of contents', 'jp-for-twentytwentyone' ),
			$this->parent->customize['toc_open_text'] ?? __( 'Open', 'jp-for-twentytwentyone' ),
			$this->parent->customize['toc_close_text'] ?? __( 'Close', 'jp-for-twentytwentyone' ),
			$top_level,
			min( $depth, 6 - $top_level ),
			( $this->parent->customize['toc_initial_view'] ?? true ) ? 'true' : 'false'
		);

		switch ( $this->parent->customize['toc_position'] ?? 0 ) {
			case 1:
				// Before first heading
				$pattern = '/<h[1-6].*?>/i';
				if ( preg_match( $pattern, $content, $matches ) ) {
					$content = preg_replace( $pattern, $shortcode . $matches[0], $content, 1 );
				}
				break;
			case 2:
				// After first heading
				$pattern = '/<h[1-6].*?>.*?<\/h[1-6].*?>/i';
				if ( preg_match( $pattern, $content, $matches ) ) {
					$content = preg_replace( $pattern, $matches[0] . $shortcode, $content, 1 );
				}
				break;
			case 3:
				// Top
				$content = $shortcode . $content;
				break;
			case 4:
				// Bottom
				$content = $content . $shortcode;
				break;
		}

		return $content;
	}
}
