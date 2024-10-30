<?php
/**
 * Scroll Top.
 *
 * @package jp-for-twentytwentyone
 */

/**
 * Scroll Top class.
 */
class JP_For_TwentyTwentyOne_Scroll_Top {
	private $parent;

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		add_action( 'enqueue_block_editor_assets', array( $this, 'editor_custom_color_variables' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
		add_action( 'wp_footer', array( $this, 'the_button' ), 11 );
	}

	/**
	 * Get enabled / disabled for scroll top.
	 *
	 * @since 1.5.0
	 *
	 * @return bool True if enabled. False if disabled.
	 */
	private function enable_scrolltop() {
		return isset( $this->parent->customize['enable_scrolltop'] ) ? $this->parent->customize['enable_scrolltop'] : true;
	}

	/**
	 * Editor custom color variables & scripts.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function editor_custom_color_variables() {
		if ( ! $this->enable_scrolltop() ) {
			return;
		}
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'jp-for-twentytwentyone-scrolltop',
			plugins_url( "js/scroll-top{$min}.js", __DIR__ ), array(), JP_FOR_TWENTYTWENTYONE_VERSION, true );

		$customize = array(
			'scrolltop_scroll' => $this->parent->customize['scrolltop_scroll'] ?? 'smooth',
		);
		wp_localize_script( 'jp-for-twentytwentyone-scrolltop', 'jp_for_twentytwentyone_scrolltop', $customize );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! $this->enable_scrolltop() ) {
			return;
		}
		wp_enqueue_style( 'jp-for-twentytwentyone-scroll-top',
			plugins_url( "css/scroll-top.css", __DIR__ ), array( 'jp-for-twentytwentyone' ), JP_FOR_TWENTYTWENTYONE_VERSION );
	}

	/**
	 * Add Scroll top button.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function the_button() {
		if ( ! $this->enable_scrolltop() ) {
			return;
		}

		// TODO: WP 5.8 Widget Block Editor bug protection.
		if ( 0 === did_action( 'template_redirect' ) ) {
			return;
		}

		$this->the_html();
		$this->the_script();
	}

	/**
	 * Print the scroll top button HTML.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function the_html() {
		$type = $this->parent->customize['scrolltop_type'] ?? 'image';
		$text = $this->parent->customize['scrolltop_text'] ?? '';

		echo '<button id="scroll-top-toggler" class="fixed-bottom" onClick="jpForTwentyTwentyOneScrollTop()">';
		if ( 'image' === $type ) {
			echo '<svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 24 24" height="24" width="24"><path d="M 4.9902344 1.9902344 A 1.2143151 1.2143151 0 0 0 3.7753906 3.203125 A 1.2143151 1.2143151 0 0 0 4.9902344 4.4179688 L 19.009766 4.4179688 A 1.2143151 1.2143151 0 0 0 20.224609 3.203125 A 1.2143151 1.2143151 0 0 0 19.009766 1.9902344 L 4.9902344 1.9902344 z" /><path d="m 11.962844,6.1089023 c -0.0063,4.766e-4 -0.01265,0.001 -0.01898,0.00158 -0.0095,4.148e-4 -0.01898,9.391e-4 -0.02846,0.00158 -0.01796,0.00118 -0.03588,0.00276 -0.05376,0.00474 -0.035,0.00376 -0.06982,0.00903 -0.104355,0.015811 -0.0037,0.00104 -0.0074,0.0021 -0.01107,0.00317 -0.01536,0.00339 -0.03064,0.00708 -0.04585,0.011068 -0.0011,-1.3e-6 -0.0021,-1.3e-6 -0.0032,0 -0.0053,0.00155 -0.01055,0.00312 -0.01581,0.00474 -0.0037,0.00104 -0.0074,0.0021 -0.01107,0.00317 -0.01698,0.0049 -0.03385,0.010173 -0.0506,0.015811 h -0.0016 c -0.0027,0.00104 -0.0053,0.0021 -0.0079,0.00317 -0.0016,5.229e-4 -0.0032,0.00105 -0.0048,0.00158 -0.01379,0.0045 -0.02749,0.00924 -0.04111,0.01423 -0.0042,0.00155 -0.0084,0.00314 -0.01265,0.00474 -0.02405,0.00978 -0.04778,0.020324 -0.07115,0.031623 -0.0058,0.00259 -0.01162,0.00523 -0.01739,0.00791 -0.0027,0.00157 -0.0053,0.00315 -0.0079,0.00474 -0.0133,0.00661 -0.02648,0.013462 -0.03953,0.020555 -0.0079,0.00413 -0.01586,0.00835 -0.02372,0.012649 l -0.0016,0.00158 c -0.01387,0.00816 -0.02757,0.016594 -0.04111,0.025298 -0.009,0.00568 -0.018,0.011477 -0.02688,0.017392 -0.0032,0.0021 -0.0063,0.0042 -0.0095,0.00632 -0.01554,0.01071 -0.03083,0.021781 -0.04585,0.033204 -0.008,0.0057 -0.01588,0.011499 -0.02372,0.017392 -0.01233,0.0103 -0.02446,0.020842 -0.03636,0.031623 -0.0016,0.00105 -0.0032,0.0021 -0.0048,0.00317 -0.0021,0.0021 -0.0042,0.00421 -0.0063,0.00632 -0.0032,0.00262 -0.0063,0.00525 -0.0095,0.00791 -0.0096,0.00881 -0.01912,0.017767 -0.02846,0.026879 -0.0074,0.00729 -0.01486,0.014663 -0.02213,0.022136 -0.0016,0.00158 -0.0032,0.00316 -0.0048,0.00474 -0.0146,0.014394 -0.02884,0.029157 -0.04269,0.044271 -0.0032,0.00419 -0.0063,0.00841 -0.0095,0.012649 -0.0086,0.00935 -0.01702,0.018842 -0.0253,0.02846 L 5.0200635,13.380559 c -0.4444102,0.502136 -0.3976923,1.269445 0.1043549,1.713955 0.502136,0.44441 1.2694442,0.397692 1.7139546,-0.104355 l 3.946523,-4.460393 v 10.266333 c 0,0.670648 0.543667,1.214315 1.214314,1.214315 0.670647,0 1.214314,-0.543667 1.214314,-1.214315 V 10.528185 l 3.948104,4.461974 c 0.44451,0.502047 1.211818,0.548765 1.713954,0.104355 0.502047,-0.44451 0.548765,-1.211819 0.104355,-1.713955 L 12.914689,6.5247418 c -0.0016,-0.00211 -0.0032,-0.00422 -0.0048,-0.00632 -0.0088,-0.0091 -0.01778,-0.01806 -0.02688,-0.026879 -0.0083,-0.00909 -0.01673,-0.018048 -0.0253,-0.026879 -0.0093,-0.00964 -0.01882,-0.019131 -0.02846,-0.02846 -0.0016,-0.00159 -0.0032,-0.00317 -0.0048,-0.00474 -0.0068,-0.00587 -0.01364,-0.011671 -0.02055,-0.017392 -0.01395,-0.012449 -0.02819,-0.024574 -0.0427,-0.036366 -0.0047,-0.00372 -0.0095,-0.00742 -0.01423,-0.011068 -0.0027,-0.00212 -0.0053,-0.00423 -0.0079,-0.00632 -0.01194,-0.00972 -0.02406,-0.019205 -0.03636,-0.02846 -0.0027,-0.00159 -0.0053,-0.00317 -0.0079,-0.00474 -0.0074,-0.00482 -0.0147,-0.00957 -0.02213,-0.01423 -0.0161,-0.010399 -0.03245,-0.020416 -0.04902,-0.030041 -0.02882,-0.017562 -0.05836,-0.033914 -0.08854,-0.049015 -0.0027,-0.00159 -0.0053,-0.00317 -0.0079,-0.00474 -0.0053,-0.00267 -0.01052,-0.00531 -0.01581,-0.00791 -0.01152,-0.00492 -0.02312,-0.00967 -0.03479,-0.01423 -0.0042,-0.0016 -0.0084,-0.00319 -0.01265,-0.00474 -0.0042,-0.00213 -0.0084,-0.00424 -0.01265,-0.00632 -0.0053,-0.00215 -0.01052,-0.00425 -0.01581,-0.00632 -0.01258,-0.00443 -0.02523,-0.00864 -0.03794,-0.012649 -0.0027,-0.00106 -0.0053,-0.00212 -0.0079,-0.00317 -0.0057,-0.00215 -0.01158,-0.00426 -0.01739,-0.00632 -0.0053,-0.00162 -0.01053,-0.0032 -0.01581,-0.00474 -0.0063,-0.00164 -0.01264,-0.00321 -0.01898,-0.00474 -0.0063,-0.00216 -0.01263,-0.00427 -0.01898,-0.00632 -0.0084,-0.0022 -0.01685,-0.00431 -0.0253,-0.00632 -0.01417,-0.00342 -0.0284,-0.00658 -0.04269,-0.00949 -0.0042,-0.00108 -0.0084,-0.00213 -0.01265,-0.00317 -0.0084,-0.00167 -0.01685,-0.00325 -0.0253,-0.00474 -0.0021,-5.5e-6 -0.0042,-5.5e-6 -0.0063,0 -0.0042,-0.00108 -0.0084,-0.00213 -0.01265,-0.00317 -0.0048,-5.541e-4 -0.0095,-0.00108 -0.01423,-0.00158 -0.01314,-0.00232 -0.02632,-0.00444 -0.03953,-0.00632 -0.0037,-5.432e-4 -0.0074,-0.00107 -0.01107,-0.00158 -0.0058,-5.679e-4 -0.01158,-0.00109 -0.01739,-0.00158 -0.0053,-5.607e-4 -0.01054,-0.00108 -0.01581,-0.00158 -0.0079,-6.036e-4 -0.01581,-0.00113 -0.02372,-0.00158 -0.0027,-8.6e-6 -0.0053,-8.6e-6 -0.0079,0 -0.01211,-7.078e-4 -0.02424,-0.00123 -0.03636,-0.00158 -0.0095,-1.112e-4 -0.01898,-1.112e-4 -0.02846,0 -0.01212,-1.815e-4 -0.02425,-1.815e-4 -0.03636,0 z" /></svg>';
		} else {
			echo esc_html( $text );
		}
		echo '<span class="screen-reader-text">' . esc_html__( 'Scroll Top', 'jp-for-twentytwentyone' ) . '</span>';
		echo '</button>';
		?>
		<style>
			#scroll-top-toggler svg {
				fill: var(--footer--color-link);
			}
			#scroll-top-toggler:hover svg,
			#scroll-top-toggler:focus svg {
				fill: var(--button--color-background-active);
			}
			<?php if ( is_admin() || wp_is_json_request() ) : ?>
				.components-editor-notices__pinned ~ .edit-post-visual-editor #scroll-top-toggler {
					z-index: 20;
				}
				.is-dark-theme.is-dark-theme #scroll-top-toggler:not(:hover):not(:focus) {
					color: var(--global--color-primary);
				}
				@media only screen and (max-width: 782px) {
					#scroll-top-toggler {
						margin-top: 32px;
					}
				}
			<?php endif; ?>
		</style>

		<?php
	}

	/**
	 * Print the scroll top button script.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function the_script() {
		$scroll = ( isset( $this->parent->customize['scrolltop_scroll'] ) && 'auto' == $this->parent->customize['scrolltop_scroll'] ) ? 'auto' : 'smooth';

		echo '<script>';
		echo 'var jp_for_twentytwentyone_scrolltop = {"scrolltop_scroll":"' . $scroll . '"};';
		include plugin_dir_path( __DIR__ ) . 'js/scroll-top.js';
		echo '</script>';
	}
}
