<?php
/**
 * JP For TwentyTwentyOne TOC.
 *
 * @package jp-for-twentytwentyone
 */

/**
 * JP For TwentyTwentyOne TOC class.
 */
class JP_For_TwentyTwentyOne_TOC {

	public static function shortcode_toc( $atts ) {
		global $post;

		if ( ! isset( $post ) ) {
			return '';
		}

		$atts = shortcode_atts( array(
			'id'          => '',
			'class'       => 'toc',
			'title'       => __( 'Table of contents', 'jp-for-twentytwentyone' ),
			'toggle'      => true,
			'opentext'    => __( 'Open', 'jp-for-twentytwentyone' ),
			'closetext'   => __( 'Close', 'jp-for-twentytwentyone' ),
			'initialview' => true,
			'showcount'   => 2,
			'depth'       => 0,
			'toplevel'    => 2,
		), $atts, 'toc' );

		$atts['toggle'] = ( false !== $atts['toggle'] && 'false' !== $atts['toggle'] ) ? true : false;
		$atts['initialview'] = ( false !== $atts['initialview'] && 'false' !== $atts['initialview'] ) ? true : false;

		$content = $post->post_content;
		$content = function_exists( 'do_blocks' ) ? do_blocks( $content ) : $content;

		$split = preg_split( '/<!--nextpage-->/msuU', $content );
		$pages = array();
		$permalink = get_permalink( $post );
		$current_pgae = get_query_var( 'page' );
		$current_pgae = get_query_var( 'page' );
		if ( $current_pgae === 0 || $current_pgae === '' ) {
			$current_pgae = 1;
		}

		if ( is_array( $split ) ) {
			$page_number = 0;
			$counter = 0;
			$current_depth = 0;
			$prev_depth = 0;
			$top_level = intval( $atts['toplevel'] );
			if ( $top_level < 1 ) $top_level = 1;
			if ( $top_level > 6 ) $top_level = 6;
			$atts['toplevel'] = $top_level;
			$max_depth = ( ( $atts['depth'] == 0 ) ? 6 : intval( $atts['depth'] ) );

			$toc_list = '';

			foreach ( $split as $content ) {
				$headers = array();
				preg_match_all( '/<(h[1-6])(.*?)>(.*?)<\/h[1-6].*?>/u', $content, $headers );
				$header_count = count( $headers[0] );
				$page_number++;
				$counter_for_page = 0;

				for ( $i = 0; $i < $header_count; $i++ ) {
					$depth = 0;
					switch ( $headers[1][$i] ) {
						case 'h1': $depth = 1 - $top_level + 1; break;
						case 'h2': $depth = 2 - $top_level + 1; break;
						case 'h3': $depth = 3 - $top_level + 1; break;
						case 'h4': $depth = 4 - $top_level + 1; break;
						case 'h5': $depth = 5 - $top_level + 1; break;
						case 'h6': $depth = 6 - $top_level + 1; break;
					}
					if ( $depth >= 1 && $depth <= $max_depth ) {
						if ( $current_depth == $depth ) {
							$toc_list .= '</li>';
						}
						while ( $current_depth > $depth ) {
							$toc_list .= '</li></ul>';
							$current_depth--;
						}
						if ( $current_depth != $prev_depth ) {
							$toc_list .= '</li>';
						}
						if ( $current_depth < $depth ) {
							$attr = '';
							if ( $current_depth == 0 ) {
								$attr .= ' class="toc-list"';
								$attr .= ! $atts['initialview'] ? ' hidden=""' : '';
							}
							$toc_list .= "<ul{$attr}>";
							$current_depth++;
						}
						$counter_for_page++;
						$counter++;

						if ( preg_match( '/.*? id="([^"]*)"/u', $headers[2][$i], $m ) ) {
							$href = '#' . $m[1];
						} else {
							$href = '#toc' .  ( $i + 1 );
						}

						if ( $current_pgae !== $page_number ) {
							$href = trailingslashit( $permalink ) . $page_number . '/' . $href;
						}

						$toc_list .= '<li class="' . esc_attr( "number-per-page-{$counter_for_page}" ) . '">';
						$toc_list .= '<a href="' . esc_url( $href ) . '">' . strip_shortcodes( $headers[3][$i] ) . '</a>';

						$prev_depth = $depth;
					}
				}
			}

			while ( $current_depth >= 1 ) {
				$toc_list .= '</li></ul>';
				$current_depth--;
			}
		}

		$html = '';
		if ( $counter >= $atts['showcount'] ) {
			$toggle = '';
			if ( $atts['toggle'] ) {
				$toggle = ' <span class="toc-toggle">[<a class="internal" href="javascript:void(0);">' . ( $atts['initialview'] ? $atts['closetext'] : $atts['opentext'] ) . '</a>]</span>';
			}

			$html .= '<div' . ( $atts['id'] != '' ? ' id="' . $atts['id'] . '"' : '' ) . ' class="' . $atts['class'] . '">';
			$html .= '<p class="toc-title">' . $atts['title'] . $toggle . '</p>';
			$html .= $toc_list;
			$html .= '</div>' . "\n";
		}

		return $html;
	}
}
