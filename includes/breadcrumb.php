<?php
/**
 * JP For TwentyTwentyOne breadcrumb.
 *
 * @package jp-for-twentytwentyone
 */

/**
 * JP For TwentyTwentyOne breadcrumb class.
 */
class JP_For_TwentyTwentyOne_Breadcrumb {
	/**
	 * Get an array of breadcrumb items.
	 *
	 * @since 1.3.0
	 *
	 * @param array $args Arguments to retrieve breadcrumb.
	 * @return array Breadcrumb items.
	 */
	function get_breadcrumb_items( $args ) {
		global $wp_query;
		global $post;

		$items = array();

		if ( is_front_page() ) {
			if ( $args['home'] ) {
				$items[] = array( 'title' => $args['home_template'], 'link' => false );
			}
			return $items;
		}

		if ( $args['home'] ) {
			$items[] = array( 'title' => $args['home_template'], 'link' => home_url( '/' ) );
		}

		$post_type = get_post_type();

		if ( $args['blog'] ) {
			if ( 'post' === $post_type && ! is_home() && ! is_search() ) {
				if ( $blog_id = get_option( 'page_for_posts' ) ) {
					$items[] = array( 'title' => get_the_title( $blog_id ), 'link' => get_page_link( $blog_id ) );
				}
			} else {
				if ( is_post_type_archive() ) {
					$items[] = array( 'title' => post_type_archive_title( null, false ), 'link' => false );
				}
			}
		}

		if ( is_home() ) {
			/*
			 * Blog homepage page.
			 */
			if ( $args['blog'] ) {
				$items[] = array( 'title' => wp_title( '', false ), 'link' => false );
			}
		} elseif ( is_single() ) {
			/*
			 * Single Post page.
			 */
			$post_type = get_post_type( $post->ID );
			if ( 'post' === $post_type ) {
				/*
				 * Post page.
				 */
				if ( $cats = get_the_category( $post->ID ) ) {
					$current_cat = null;
					foreach ( $cats as $cat ) {
						if ( ! $current_cat || cat_is_ancestor_of( $current_cat, $cat ) ) {
							$current_cat = $cat;
						}
					}
					if ( $current_cat->parent ) {
						$ancestor_cat_ids = array_reverse( get_ancestors( $current_cat->cat_ID, 'category' ) );
						foreach ( $ancestor_cat_ids as $cat_id ) {
							$items[] = array( 'title' => get_cat_name( $cat_id ), 'link' => get_category_link( $cat_id ) );
						}
					}
					$items[] = array( 'title' => get_cat_name( $current_cat->cat_ID ), 'link' => get_category_link( $current_cat->cat_ID ) );
				}
			} else {
				/*
				 * Custom Post Type page.
				 */
				$post_type_object = get_post_type_object( $post->post_type );
				if ( false !== $post_type_object->has_archive ) {
					$items[] = array( 'title' => $post_type_object->labels->name, 'link' => get_post_type_archive_link( get_post_type() ) );
				}
			}
			$strtitle = the_title( '', '', false );
			if ( ! isset( $strtitle ) || $strtitle == '' ) {
				$strtitle = $post->ID;
			}
			$items[] = array( 'title' => $strtitle, 'link' => false );
		} elseif ( is_category() ) {
			/*
			 * Category archive page.
			 */
			$cat = get_queried_object();
			if ( $cat->parent ) {
				$ancestor_cat_ids = array_reverse( get_ancestors( $cat->cat_ID, 'category' ) );
				foreach ( $ancestor_cat_ids as $cat_id ) {
					if ( ! in_array( $cat_id, $args['exclude_categorys'] ) ) {
						$items[] = array( 'title' => get_cat_name( $cat_id ), 'link' => get_category_link( $cat_id ) );
					}
				}
			}
			$items[] = array( 'title' => $cat->cat_name, 'link' => false );
		} else if ( is_tag() ) {
			/*
			 * Tag archive page.
			 */
			$tag_id = get_query_var( 'tag_id' );
			$tag = get_tag( $tag_id );
			if ( $tag ) {
				$items[] = array( 'title' => $tag->name, 'link' => false );
			}
		} elseif ( is_tax() ) {
			/*
			 * Taxonomy archive page.
			 */
			$query_obj = get_queried_object();
			$post_types = get_taxonomy( $query_obj->taxonomy )->object_type;
			$cpt = $post_types[0];
			$items[] = array( 'title' => get_post_type_object( $cpt )->label, 'link' => get_post_type_archive_link( $cpt ) );

			$taxonomy = get_query_var( 'taxonomy' );
			$term = get_term_by( 'slug', get_query_var( 'term' ), $taxonomy );
			if ( is_taxonomy_hierarchical( $taxonomy ) && $term->parent != 0 ) {
				$ancestors = array_reverse( get_ancestors( $term->term_id, $taxonomy ) );
				foreach ( $ancestors as $ancestor_id ) {
					$ancestor = get_term( $ancestor_id, $taxonomy );
					$items[] = array( 'title' => $ancestor->name, 'link' => get_term_link( $ancestor, $term->slug ) );
				}
			}
			$items[] = array( 'title' => $term->name, 'link' => false );
		} elseif ( is_year() ) {
			/*
			 * Year archive page.
			 */
			$items[] = array( 'title' => get_the_time( _x( 'Y', 'breadcrumb year format', 'jp-for-twentytwentyone' ) ), 'link' => false );
		} elseif ( is_month() ) {
			/*
			 * Month archive page.
			 */
			$items[] = array( 'title' => get_the_time( _x( 'Y', 'breadcrumb year format', 'jp-for-twentytwentyone' ) ), 'link' => get_year_link( get_the_time( 'Y' ) ) );
			$items[] = array( 'title' => get_the_time( _x( 'F', 'breadcrumb month format', 'jp-for-twentytwentyone' ) ), 'link' => false );
		} elseif ( is_date() ) {
			/*
			 * Date archive page.
			 */
			$items[] = array( 'title' => get_the_time( _x( 'Y', 'breadcrumb year format', 'jp-for-twentytwentyone' ) ), 'link' => get_year_link( get_the_time( 'Y' ) ) );
			$items[] = array( 'title' => get_the_time( _x( 'F', 'breadcrumb month format', 'jp-for-twentytwentyone' ) ), 'link' => get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) );
			$items[] = array( 'title' => get_the_time( _x( 'j', 'breadcrumb day format', 'jp-for-twentytwentyone' ) ), 'link' => false );
		} elseif ( is_page() ) {
			/*
			 * Single page.
			 */
			$post = $wp_query->get_queried_object();
			if ( $post->post_parent == 0 ) {
				$items[] = array( 'title' => get_the_title( '', '', true ), 'link' => false );
			} else {
				$ancestors = array_reverse( get_post_ancestors( $post->ID ) );
				array_push( $ancestors, $post->ID );
				foreach ( $ancestors as $ancestor ) {
					$strtitle = get_the_title( $ancestor );
					if ( ! isset( $strtitle ) || $strtitle == '' ) {
						$strtitle = $post->ID;
					}
					if ( $ancestor != end( $ancestors ) ) {
						$items[] = array( 'title' => strip_tags( apply_filters( 'single_post_title', $strtitle ) ), 'link' => get_permalink( $ancestor ) );
					} else {
						$items[] = array( 'title' => strip_tags( apply_filters( 'single_post_title', $strtitle ) ), 'link' => false );
					}
				}
			}
		} elseif ( is_search() ) {
			/*
			 * Search page.
			 */
			$items[] = array( 'title' => __( 'Search Results', 'jp-for-twentytwentyone' ), 'link' => false );
		} elseif ( is_404() ) {
			/*
			 * 404 page.
			 */
			$items[] = array( 'title' => __( '404', 'jp-for-twentytwentyone' ), 'link' => false );
		}

		return $items;
	}

	/**
	 * Get the breadcrumb.
	 *
	 * @since 1.3.0
	 *
	 * @param array $args Optional. Arguments to retrieve breadcrumb.
	 * @return string Breadcrumb html.
	 */
	function get_breadcrumb( $args = array() ) {
		$defaults = array(
			'before'            => '<nav id="breadcrumb" role="navigation" class="alignwide">',
			'after'             => '</nav>',
			'home'              => true,
			'home_template'     => 'HOME',
			'blog'              => true,
			'exclude_categorys' => array(),
		);
		$args = wp_parse_args( $args, $defaults );

		$items = $this->get_breadcrumb_items( $args );

		$html = $args['before'];
		$html .= '<ul itemscope itemtype="https://schema.org/BreadcrumbList" class="breadcrumb">';
		foreach ( $items as $index => $item ) {
			$html .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
			if ( $item['link'] ) {
				$html .= '<a itemprop="item" href="' . esc_attr( $item['link'] ) . '"><span itemprop="name">' . esc_html( $item['title'] ) . '</span></a>';
			} else {
				$html .= '<span itemprop="name">' . esc_html( $item['title'] ) . '</span>';
			}
			$html .= '<meta itemprop="position" content="' . ( $index + 1 ) . '" />';
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= $args['after'] . "\n";

		return $html;
	}
}
