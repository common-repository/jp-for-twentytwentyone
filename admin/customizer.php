<?php
/**
 * JP For TwentyTwentyOne asmin.
 *
 * @package jp-for-twentytwentyone
 */

/**
 * JP For TwentyTwentyOne admin class.
 */
class JP_For_TwentyTwentyOne_Admin {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue_scripts' ) );
	}

	/**
	 * Fires once the Customizer preview has initialized and JavaScript settings have been printed.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	function customize_preview_init() {
		wp_enqueue_script( 'jp-for-twentytwentyone-customize-preview', plugins_url( 'js/customize-preview.js', __FILE__ ),
			array( 'customize-preview', 'customize-selective-refresh', 'jquery' ), JP_FOR_TWENTYTWENTYONE_VERSION, true );
	}

	/**
	 * Enqueue Customizer control scripts.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	function customize_controls_enqueue_scripts() {
		wp_enqueue_style( 'jp-for-twentytwentyone-customize-control', plugins_url( 'css/style-editor.css', __FILE__ ) );
		wp_enqueue_script( 'jp-for-twentytwentyone-customize-control', plugins_url( 'js/customize-controls.js', __FILE__ ), 
			array( 'customize-controls', 'jquery' ), JP_FOR_TWENTYTWENTYONE_VERSION, true );
	}

	/**
	 *  Add the customizer settings and controls.
	 *
	 * @since 1.0,0
	 * @since 1.3.0 Added the breadcrumbs.
	 * @since 1.4.0 Added the table of contents.
	 * @since 1.5.0 Added the Scroll Back to Top button.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 * @return void
	 */
	function customize_register( $wp_customize ) {
		include( 'customize-controls .php' );

		$valid_tag_description = '<code>a</code>, <code>br</code>, <code>i</code>, <code>span</code>, <code>strong</code>.';

		/*
		 * Settings for Japan
		 */
		$wp_customize->add_section( 'jp_for_twentytwentyone_customize_japan' , array(
			'title'    => __( 'Settings for Japan', 'jp-for-twentytwentyone' ),
			'priority' => 198,
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[noto_sans_jp]', array(
			'default' => false,
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_noto_sans_jp', array(
			'label'    => __( 'Use Noto Sans JP fonts', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_japan',
			'settings' => 'jp_for_twentytwentyone_customize[noto_sans_jp]',
			'type'     => 'checkbox',
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[font_weight]', array(
			'default' => '400',
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_font_weight', array(
			'label'    => __( 'Noto Sans JP Font Weight', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_japan',
			'settings' => 'jp_for_twentytwentyone_customize[font_weight]',
			'type'     => 'select',
			'choices'  => array(
				'100' => '100 - Thin',
				'300' => '300 - Light',
				'400' => '400 - Regular',
				'500' => '500 - Medium',
				'700' => '700 - Bold',
				'800' => '900 - Black',
			),
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[font_size_rate]', array(
			'default'           => 88,
			'type'              => 'option',
			'sanitize_callback' => array( $this, 'sanitize_number_range' ),
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[font_size_rate]', array(
			'label'       => __( 'Font size rate (%)', 'jp-for-twentytwentyone' ),
			'section'     => 'jp_for_twentytwentyone_customize_japan',
			'settings'    => 'jp_for_twentytwentyone_customize[font_size_rate]',
			'type'        => 'number',
			'input_attrs' => array( 'step' => 1, 'min' => 50, 'max' => 150 ),
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[social_icon]', array(
			'default' => true,
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_social_icon', array(
			'label'    => __( 'Add social icons', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_japan',
			'settings' => 'jp_for_twentytwentyone_customize[social_icon]',
			'type'     => 'checkbox',
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[social_icon_color]', array(
			'default' => true,
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_social_icon_color', array(
			'label'    => __( 'Color social icons', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_japan',
			'settings' => 'jp_for_twentytwentyone_customize[social_icon_color]',
			'type'     => 'checkbox',
		) );

		/*
		 * Breadcrumb Settings
		 */
		$wp_customize->add_section( 'jp_for_twentytwentyone_customize_breadcrumb' , array(
			'title'    => __( 'Breadcrumb', 'jp-for-twentytwentyone' ),
			'priority' => 198,
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[enable_breadcrumb]', array(
			'default' => false,
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_enable_breadcrumb', array(
			'label'    => __( 'Add Breadcrumb', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_breadcrumb',
			'settings' => 'jp_for_twentytwentyone_customize[enable_breadcrumb]',
			'type'     => 'checkbox',
		) );
		$wp_customize->selective_refresh->add_partial( 'jp_for_twentytwentyone_customize[enable_breadcrumb]', array(
			'selector'            => '#breadcrumb',
			'container_inclusive' => true,
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[breadcrumb_exclude_homepage]', array(
			'default' => false,
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_breadcrumb_exclude_homepage', array(
			'label'    => __( 'Exclude homepage', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_breadcrumb',
			'settings' => 'jp_for_twentytwentyone_customize[breadcrumb_exclude_homepage]',
			'type'     => 'checkbox',
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[breadcrumb_home]', array(
			'default' => false,
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_breadcrumb_home', array(
			'label'    => __( 'Home Breadcrumb', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_breadcrumb',
			'settings' => 'jp_for_twentytwentyone_customize[breadcrumb_home]',
			'type'     => 'checkbox',
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[breadcrumb_home_template]', array(
			'type'              => 'option',
			'sanitize_callback' => array( $this, 'sanitize_html' ),
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_breadcrumb_home_template', array(
			'label'       => __( 'Home Template', 'jp-for-twentytwentyone' ),
			'section'     => 'jp_for_twentytwentyone_customize_breadcrumb',
			'settings'    => 'jp_for_twentytwentyone_customize[breadcrumb_home_template]',
			'type'        => 'textarea',
			'description' => sprintf( '<p>%s</p><p>%s</p>', __( 'The following HTML tags can be used:', 'jp-for-twentytwentyone' ), $valid_tag_description ),
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[breadcrumb_blog]', array(
			'default' => true,
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_breadcrumb_blog', array(
			'label'    => __( 'Posts page Breadcrumb', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_breadcrumb',
			'settings' => 'jp_for_twentytwentyone_customize[breadcrumb_blog]',
			'type'     => 'checkbox',
		) );

		/*
		 * TOC Settings
		 */
		$wp_customize->add_section( 'jp_for_twentytwentyone_customize_toc' , array(
			'title'    => __( 'Table of contents', 'jp-for-twentytwentyone' ),
			'priority' => 198,
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[enable_toc]', array(
			'default' => true,
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_enable_toc', array(
			'label'    => __( 'Enable table of contents', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_toc',
			'settings' => 'jp_for_twentytwentyone_customize[enable_toc]',
			'type'     => 'checkbox',
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[toc_position]', array(
			'default'           => 1,
			'type'              => 'option',
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_toc_position', array(
			'label'       => __( 'Auto-insert position', 'jp-for-twentytwentyone' ),
			'description' => __( '"Sidebar" is valid only when the sidebar exists.', 'jp-for-twentytwentyone' ),
			'section'     => 'jp_for_twentytwentyone_customize_toc',
			'settings'    => 'jp_for_twentytwentyone_customize[toc_position]',
			'type'        => 'select',
			'choices'     => array(
				'0' => __( 'Without auto-insert', 'jp-for-twentytwentyone' ),
				'1' => __( 'Before first heading', 'jp-for-twentytwentyone' ),
				'2' => __( 'After first heading', 'jp-for-twentytwentyone' ),
				'3' => __( 'Top', 'jp-for-twentytwentyone' ),
				'4' => __( 'Bottom', 'jp-for-twentytwentyone' ),
				'5' => __( 'Top of the sidebar', 'jp-for-twentytwentyone' ),
				'6' => __( 'End of the sidebar', 'jp-for-twentytwentyone' ),
				'7' => __( 'Top of the Sidebar (Sticky)', 'jp-for-twentytwentyone' ),
				'8' => __( 'End of the Sidebar (Sticky)', 'jp-for-twentytwentyone' ),
			),
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[toc_auto_post_types]', array(
			'default'           => array( 'post', 'page' ),
			'type'              => 'option',
			'sanitize_callback' => array( $this, 'sanitize_multiple_checkbox' ),
		) );

		$choices = array();
		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
			if ( post_type_supports( $post_type->name, 'editor' ) ) {
				$choices[$post_type->name] = $post_type->label;
			}
		}

		$wp_customize->add_control(
			new JP_For_TwentyTwentyOne_Customize_Control_Multiple_Checkbox ( $wp_customize, 'jp_for_twentytwentyone_customize_toc_auto_post_types', array(
				'section'  => 'jp_for_twentytwentyone_customize_toc',
				'settings' => 'jp_for_twentytwentyone_customize[toc_auto_post_types]',
				'label'    => __( 'Auto-insert post types', 'jp-for-twentytwentyone' ),
				'choices'  => $choices,
			) )
		);

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[toc_homepage]', array(
			'default' => true,
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[toc_homepage]', array(
			'label'    => __( 'Automatically insert into homepage', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_toc',
			'settings' => 'jp_for_twentytwentyone_customize[toc_homepage]',
			'type'     => 'checkbox',
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[toc_show_when]', array(
			'default'           => 4,
			'type'              => 'option',
			'sanitize_callback' => array( $this, 'sanitize_number_range' ),
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[toc_show_when]', array(
			'label'       => __( 'Show when', 'jp-for-twentytwentyone' ),
			'section'     => 'jp_for_twentytwentyone_customize_toc',
			'settings'    => 'jp_for_twentytwentyone_customize[toc_show_when]',
			'type'        => 'number',
			'input_attrs' => array( 'step' => 1, 'min' => 2, 'max' => 10 ),
			'description' => __( 'Greater than or equal to:', 'jp-for-twentytwentyone' ),
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[toc_title]', array(
			'default'           => __( 'Table of contents', 'jp-for-twentytwentyone' ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[toc_title]', array(
			'label'       => __( 'Table of contents title', 'jp-for-twentytwentyone' ),
			'section'     => 'jp_for_twentytwentyone_customize_toc',
			'settings'    => 'jp_for_twentytwentyone_customize[toc_title]',
			'type'        => 'text',
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[toc_open_text]', array(
			'default'           => __( 'Open', 'jp-for-twentytwentyone' ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[toc_open_text]', array(
			'label'    => __( 'Open text', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_toc',
			'settings' => 'jp_for_twentytwentyone_customize[toc_open_text]',
			'type'     => 'text',
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[toc_close_text]', array(
			'default'           => __( 'Close', 'jp-for-twentytwentyone' ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[toc_close_text]', array(
			'label'    => __( 'Close text', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_toc',
			'settings' => 'jp_for_twentytwentyone_customize[toc_close_text]',
			'type'     => 'text',
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[toc_top_level]', array(
			'default'           => 2,
			'type'              => 'option',
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[toc_top_level]', array(
			'label'    => __( 'Top level', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_toc',
			'settings' => 'jp_for_twentytwentyone_customize[toc_top_level]',
			'type'     => 'select',
			'choices'  => array(
				'1' => __( 'Heading 1 (h1)', 'jp-for-twentytwentyone' ),
				'2' => __( 'Heading 2 (h2)', 'jp-for-twentytwentyone' ),
				'3' => __( 'Heading 3 (h3)', 'jp-for-twentytwentyone' ),
			),
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[toc_depth]', array(
			'default'           => 3,
			'type'              => 'option',
			'sanitize_callback' => array( $this, 'sanitize_number_range' ),
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[toc_depth]', array(
			'label'       => __( 'Number of layers', 'jp-for-twentytwentyone' ),
			'section'     => 'jp_for_twentytwentyone_customize_toc',
			'settings'    => 'jp_for_twentytwentyone_customize[toc_depth]',
			'type'        => 'number',
			'input_attrs' => array( 'step' => 1, 'min' => 1, 'max' => 6 ),
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[toc_initial_view]', array(
			'default' => true,
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[toc_initial_view]', array(
			'label'    => __( 'Display with the table of contents open', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_toc',
			'settings' => 'jp_for_twentytwentyone_customize[toc_initial_view]',
			'type'     => 'checkbox',
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[toc_scroll]', array(
			'default' => 'smooth',
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[toc_scroll]', array(
			'label'    => __( 'Scroll behavior', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_toc',
			'settings' => 'jp_for_twentytwentyone_customize[toc_scroll]',
			'type'     => 'select',
			'choices'  => array(
				'auto' => __( 'Auto', 'jp-for-twentytwentyone' ),
				'smooth' => __( 'Smooth', 'jp-for-twentytwentyone' ),
			),
		) );

		$wp_customize->selective_refresh->add_partial( 'jp_for_twentytwentyone_customize[enable_toc]', array(
			'selector'            => '#jftto-toc',
			'container_inclusive' => true,
		) );

		/*
		 * Sidebar Settings
		 */
		$wp_customize->add_section( 'jp_for_twentytwentyone_customize_sidebar' , array(
			'title'    => __( 'Sidebar', 'jp-for-twentytwentyone' ),
			'priority' => 198,
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[show_sidebar]', array(
			'default'           => array( 'post', 'page', 'archive' ),
			'type'              => 'option',
			'sanitize_callback' => array( $this, 'sanitize_multiple_checkbox' ),
		) );
		$wp_customize->add_control(
			new JP_For_TwentyTwentyOne_Customize_Control_Multiple_Checkbox ( $wp_customize, 'jp_for_twentytwentyone_customize_show_sidebar', array(
				'section'  => 'jp_for_twentytwentyone_customize_sidebar',
				'settings' => 'jp_for_twentytwentyone_customize[show_sidebar]',
				'label'    => __( 'Page to show sidebar', 'jp-for-twentytwentyone' ),
				'choices'  => array(
					'home' => __( 'Homepage', 'jp-for-twentytwentyone' ),
					'post' => __( 'Posts page', 'jp-for-twentytwentyone' ),
					'page' => __( 'Pages page', 'jp-for-twentytwentyone' ),
					'archive' => __( 'Archive page', 'jp-for-twentytwentyone' ),
				),
			) )
		);

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[sidebar_position]', array(
			'default'           => 'right',
			'type'              => 'option',
			'sanitize_callback' => function( $value ) { return 'left' === $value || 'right' === $value ? $value : 'right'; },
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[sidebar_position]', array(
			'label'    => __( 'Sidebar position', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_sidebar',
			'settings' => 'jp_for_twentytwentyone_customize[sidebar_position]',
			'type'     => 'radio',
			'choices'  => array(
				'left'  => __( 'Left', 'jp-for-twentytwentyone' ),
				'right' => __( 'Right', 'jp-for-twentytwentyone' ),
			),
		) );

		/*
		 * Header Settings
		 */
		$wp_customize->add_section( 'jp_for_twentytwentyone_customize_header' , array(
			'title'    => __( 'Header', 'jp-for-twentytwentyone' ),
			'priority' => 198,
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[header_image]', array(
			'type' => 'option',
		) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control( $wp_customize, 'jp_for_twentytwentyone_customize[header_image]', array(
				'section'  => 'jp_for_twentytwentyone_customize_header',
				'settings' => 'jp_for_twentytwentyone_customize[header_image]',
				'label'    => __( 'Header background image', 'jp-for-twentytwentyone' ),
			) )
		);

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[header_height]', array(
			'default'           => 0,
			'type'              => 'option',
			'sanitize_callback' => array( $this, 'sanitize_number_range' ),
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[header_height]', array(
			'label'       => __( 'Minimum height of header', 'jp-for-twentytwentyone' ),
			'section'     => 'jp_for_twentytwentyone_customize_header',
			'settings'    => 'jp_for_twentytwentyone_customize[header_height]',
			'type'        => 'number',
			'description' => __( '0 to 1000px (0: auto)', 'jp-for-twentytwentyone' ),
			'input_attrs' => array( 'step' => 1, 'min' => 0, 'max' => 1000 ),
		) );

		/*
		 * Content Settings
		 */
		$wp_customize->add_section( 'jp_for_twentytwentyone_customize_content' , array(
			'title'    => __( 'Content', 'jp-for-twentytwentyone' ),
			'priority' => 198,
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[content_normal_width]', array(
			'default'           => 610,
			'type'              => 'option',
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_content_normal_width', array(
			'label'    => __( 'Standard content maximum width', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_content',
			'settings' => 'jp_for_twentytwentyone_customize[content_normal_width]',
			'type'     => 'select',
			'choices'  => array(
				'610'  => '610px ' . __( '(Default)', 'jp-for-twentytwentyone' ),
				'640'  => '640px',
				'700'  => '700px',
				'720'  => '720px',
				'750'  => '750px',
				'760'  => '760px',
				'770'  => '770px',
				'790'  => '790px',
				'840'  => '840px',
				'880'  => '880px',
				'900'  => '900px',
				'920'  => '920px',
				'930'  => '930px',
				'940'  => '940px',
				'950'  => '950px',
				'960'  => '960px',
				'970'  => '970px',
				'980'  => '980px',
				'990'  => '990px',
				'1000' => '1000px',
				'1010' => '1010px',
				'1020' => '1020px',
				'1030' => '1030px',
				'1040' => '1040px',
				'1060' => '1060px',
				'1080' => '1080px',
				'1200' => '1200px',
				'1240' => '1240px ' . __( '(Wide)', 'jp-for-twentytwentyone' ),
			),
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[content_width]', array(
			'default'           => 750,
			'type'              => 'option',
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_content_width', array(
			'label'    => __( 'Content width', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_content',
			'settings' => 'jp_for_twentytwentyone_customize[content_width]',
			'type'     => 'select',
			'choices'  => array(
				'750'  => '750px ' . __( '(Default)', 'jp-for-twentytwentyone' ),
				'1000' => '1000px',
				'1240' => '1240px',
			),
			'description' => __( '"Standard content maximum width" or more is recommended', 'jp-for-twentytwentyone' ),
		) );

		/*
		 * Footer Settings
		 */
		$wp_customize->add_section( 'jp_for_twentytwentyone_customize_footer' , array(
			'title'    => __( 'Footer', 'jp-for-twentytwentyone' ),
			'priority' => 198,
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[enable_powered_by]', array(
			'default' => false,
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_enable_powered_by', array(
			'label'    => __( 'Enable change to Powered by', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_footer',
			'settings' => 'jp_for_twentytwentyone_customize[enable_powered_by]',
			'type'     => 'checkbox',
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[powered_by]', array(
			'type'              => 'option',
			'sanitize_callback' => array( $this, 'sanitize_html' ),
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_powered_by', array(
			'label'       => __( 'Powered by', 'jp-for-twentytwentyone' ),
			'section'     => 'jp_for_twentytwentyone_customize_footer',
			'settings'    => 'jp_for_twentytwentyone_customize[powered_by]',
			'type'        => 'textarea',
			'description' => sprintf( '<p>%s</p><p>%s</p><p>%s</p><p>%s</p>',
				__( 'The following HTML tags can be used:', 'jp-for-twentytwentyone' ),
				$valid_tag_description,
				__( 'You can also use the following special tags:', 'jp-for-twentytwentyone' ),
				__( '%copy%: Copyright sign.<br />%year%: Current year.<br />%site_url%: Site url.', 'jp-for-twentytwentyone' )
			),
		) );

		$wp_customize->selective_refresh->add_partial( 'jp_for_twentytwentyone_customize[enable_powered_by]', array(
			'selector'            => '#colophon .powered-by',
			'container_inclusive' => false,
		) );

		/*
		 * Scroll Top Settings
		 */
		$wp_customize->add_section( 'jp_for_twentytwentyone_customize_scrolltop' , array(
			'title'    => __( 'Scroll Top', 'jp-for-twentytwentyone' ),
			'priority' => 198,
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[enable_scrolltop]', array(
			'default' => true,
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize_enable_scrolltop', array(
			'label'    => __( 'Enable Scroll Top', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_scrolltop',
			'settings' => 'jp_for_twentytwentyone_customize[enable_scrolltop]',
			'type'     => 'checkbox',
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[scrolltop_type]', array(
			'default' => 'image',
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[scrolltop_type]', array(
			'label'    => __( 'Scroll top button type', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_scrolltop',
			'settings' => 'jp_for_twentytwentyone_customize[scrolltop_type]',
			'type'     => 'select',
			'choices'  => array(
				'image' => __( 'Image', 'jp-for-twentytwentyone' ),
				'text' => __( 'Text', 'jp-for-twentytwentyone' ),
			),
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[scrolltop_text]', array(
			'default'           => __( 'Scroll to Top', 'jp-for-twentytwentyone' ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[scrolltop_text]', array(
			'label'    => __( 'Scroll top button text', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_scrolltop',
			'settings' => 'jp_for_twentytwentyone_customize[scrolltop_text]',
			'type'     => 'text',
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[scrolltop_scroll]', array(
			'default' => 'smooth',
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[scrolltop_scroll]', array(
			'label'    => __( 'Scroll behavior', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_scrolltop',
			'settings' => 'jp_for_twentytwentyone_customize[scrolltop_scroll]',
			'type'     => 'select',
			'choices'  => array(
				'auto' => __( 'Auto', 'jp-for-twentytwentyone' ),
				'smooth' => __( 'Smooth', 'jp-for-twentytwentyone' ),
			),
		) );

		/*
		 * Design skin Settings
		 */
		$wp_customize->add_section( 'jp_for_twentytwentyone_customize_skin' , array(
			'title'    => __( 'Design skin', 'jp-for-twentytwentyone' ),
			'priority' => 198,
		) );

		$wp_customize->add_setting( 'jp_for_twentytwentyone_customize[skin]', array(
			'default' => '',
			'type'    => 'option',
		) );
		$wp_customize->add_control( 'jp_for_twentytwentyone_customize[skin]', array(
			'label'    => __( 'Design skin', 'jp-for-twentytwentyone' ),
			'section'  => 'jp_for_twentytwentyone_customize_skin',
			'settings' => 'jp_for_twentytwentyone_customize[skin]',
			'type'     => 'select',
			'choices'  => array(
				'' => __( 'None', 'jp-for-twentytwentyone' ),
				'light' => __( 'Light', 'jp-for-twentytwentyone' ),
			),
		) );
	}

	/**
	 * Sanitizes Number range.
	 *
	 * @since 1.4.0
	 *
	 * @param string $number
	 * @param WP_Customize_Setting $setting A WP_Customize_Setting derived object.
	 * @return int
	 */
	function sanitize_number_range( $number, $setting ) {
		$number = absint( $number );
		$atts   = $setting->manager->get_control( $setting->id )->input_attrs;
		$min    = $atts['min'] ?? $number;
		$max    = $atts['max'] ?? $number;
		$step   = $atts['step'] ?? 1;
		return ( $min <= $number && $number <= $max && is_int( $number / $step ) ? $number : $setting->default );
	}

	/**
	 * Sanitizes HTML.
	 *
	 * @since 1.3.0
	 *
	 * @param string $html
	 * @return string
	 */
	function sanitize_html( $html ) {
		$attr = array( 'id' => true, 'class' => true, 'style' => true );
		$allowed = array(
			'a'      => $attr + array( 'href' => true, 'title' => true ),
			'br'     => $attr,
			'i'      => $attr + array( 'aria-hidden' => true, 'data-icon' => true ),
			'span'   => $attr,
			'strong' => $attr,
		);
		return wp_kses( $html, $allowed );
	}

	/**
	 * Sanitizes multiple checkbox.
	 *
	 * @since 1.4.0
	 *
	 * @param string $values
	 * @return array
	 */
	function sanitize_multiple_checkbox( $values ) {
		$multi_values = ! is_array( $values ) ? explode( ',', $values ) : $values;
		return ! empty( $multi_values ) ? array_map( 'sanitize_text_field', $multi_values ) : array();
	}
}
