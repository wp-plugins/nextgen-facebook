<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbWebPage' ) ) {

	class ngfbWebPage {

		private $ngfb;
		private $shortcode = array();

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();

			foreach ( $this->ngfb->shortcode_libs as $id => $name ) {
				$classname = 'ngfbShortCode' . preg_replace( '/ /', '', $name );
				if ( class_exists( $classname ) )
					$this->shortcode[$id] = new $classname( $ngfb_plugin );
			}
			unset ( $id, $name );
		}

		// called from Tumblr class
		public function get_quote() {
			global $post;
			$quote = '';
			if ( empty( $post ) ) return $quote;
			if ( has_excerpt( $post->ID ) ) $quote = get_the_excerpt( $post->ID );	// since wp 2.3.0, since wp 0.71 
			else $quote = $post->post_content;					// fallback to regular content
			$quote = $this->ngfb->util->cleanup_html_tags( $quote, false );		// remove shortcodes, etc., but don't strip html tags
			if ( $this->ngfb->is_avail['aop'] ) 
				return apply_filters( 'ngfb_quote', $quote );			// since wp 0.71 
			else return $quote;
		}

		// called from Tumblr, Pinterest, and Twitter classes
		public function get_caption( $type = 'title', $length = 200, $use_post = true ) {
			$caption = '';
			switch( strtolower( $type ) ) {
				case 'title' :
					$caption = $this->get_title( $length, '...', $use_post );
					break;
				case 'excerpt' :
					$caption = $this->get_description( $length, '...', $use_post );
					break;
				case 'both' :
					$title = $this->get_title( null, null, $use_post);
					$caption = $title . ' : ' . $this->get_description( $length - strlen( $title ) - 3, '...', $use_post );
					break;
			}
			if ( $this->ngfb->is_avail['aop'] ) 
				return apply_filters( 'ngfb_caption', $caption );
			else return $caption;
		}

		public function get_title( $textlen = 70, $trailing = '', $use_post = false ) {
			global $post, $page, $paged;
			$title = '';
			$parent_title = '';
			$page_num_suffix = '';

			// check for custom meta title
			if ( ( is_singular() && ! empty( $post ) ) || ( ! empty( $post ) && ! empty( $use_post ) ) )
				$title = $this->ngfb->meta->get_options( $post->ID, 'og_title' );

			if ( ! empty( $title ) ) {
				$this->ngfb->debug->log( 'found custom meta title = "' . $title . '"' );

			// we are on an index, but need individual titles from the posts (probably for social buttons)
			} elseif ( ! is_singular() && ! empty( $post ) && ! empty( $use_post ) ) {	// since wp 1.5.0

				$this->ngfb->debug->log( 'is_singular() = ' . ( is_singular() ? 'true' : 'false' ) );
				$this->ngfb->debug->log( 'use_post = ' . ( $use_post  ? 'true' : 'false' ) );

				$title = get_the_title( $post->ID );	// since wp 0.71 
				$this->ngfb->debug->log( 'get_the_title() = "' . $title . '"' );
				// add the parent's title if no seo package is installed
				if ( $this->ngfb->is_avail['any_seo'] == false && ! empty( $post->post_parent ) ) {
					$parent_title = get_the_title( $post->post_parent );
					if ( $parent_title ) $title .= ' (' . $parent_title . ')';
				}

			// by default, use an seo title if an seo plugin is available
			} elseif ( $this->ngfb->is_avail['any_seo'] == true ) {

				$title = wp_title( '', false );
				$this->ngfb->debug->log( 'seo wp_title() = "' . $title . '"' );

			// category title, with category parents
			} elseif ( is_category() ) { 

				$title = single_cat_title( '', false );		// since wp 0.71
				$this->ngfb->debug->log( 'single_cat_title() = "' . $title . '"' );
				$cat_parents = get_category_parents( get_cat_ID( $title ), false, 
					' ' . $this->ngfb->options['og_title_sep'] . ' ', false );

				// use is_wp_error() to avoid "Object of class WP_Error could not be converted to string" error
				if ( is_wp_error( $cat_parents ) ) {
					$this->ngfb->debug->log( 'get_category_parents() returned WP_Error object.' );
				} else {
					$this->ngfb->debug->log( 'get_category_parents() = "' . $cat_parents . '"' );
					if ( ! empty( $cat_parents ) ) {
						$title = trim( $cat_parents, ' ' . $this->ngfb->options['og_title_sep'] );
						// special fix for category names that end with three dots
						$title = preg_replace( '/\.\.\. \\' . $this->ngfb->options['og_title_sep'] . ' /', '... ', $title );
					}
				}
				unset ( $cat_parents );

			} else {
				/* The title text depends on the query:
				 *	single post = the title of the post 
				 *	date-based archive = the date (e.g., "2006", "2006 - January") 
				 *	category = the name of the category 
				 *	author page = the public name of the user 
				 */
				$title = wp_title( $this->ngfb->options['og_title_sep'], false, 'right' );
				$this->ngfb->debug->log( 'wp_title() = "' . $title . '"' );
			}

			// just in case
			if ( ! $title ) {
				$title = get_bloginfo( 'name', 'display' );
				$this->ngfb->debug->log( 'get_bloginfo() = "' . $title . '"' );
			}

			$title = $this->ngfb->util->decode( $title );
			$title = $this->ngfb->util->cleanup_html_tags( $title );
			$title = trim( $title, ' ' . $this->ngfb->options['og_title_sep'] );	// trim spaces and excess seperator

			// seo-like title modifications
			if ( $this->ngfb->is_avail['any_seo'] == false ) {

				// apprent the parent's title 
				if ( is_singular() && ! empty( $post->post_parent ) ) {
					$parent_title = get_the_title( $post->post_parent );
					if ( $parent_title ) $title .= ' (' . $parent_title . ')';
				}
	
				// add a page number
				if ( $paged >= 2 || $page >= 2 ) {
					if ( ! empty( $this->ngfb->options['og_title_sep'] ) )
						$page_num_suffix .= ' ' . $this->ngfb->options['og_title_sep'];
					$page_num_suffix .= ' ' . sprintf( 'Page %s', max( $paged, $page ) );
					$textlen = $textlen - strlen( $page_num_suffix );	// make room for the page number
				}
			}

			if ( $textlen > 0 ) 
				$title = $this->ngfb->util->limit_text_length( $title, $textlen, $trailing );

			// append the text number after the trailing character string
			if ( ! empty( $page_num_suffix ) ) $title .= $page_num_suffix;

			if ( $this->ngfb->is_avail['aop'] ) 
				return apply_filters( 'ngfb_title', $title );
			else return $title;
		}

		public function get_description( $textlen = NGFB_MIN_DESC_LEN, $trailing = '', $use_post = false, $use_cache = true ) {
			global $post;
			$desc = '';

			// check for custom meta description
			// og_desc meta is the fallback for all other description fields as well (link_desc, tc_desc, etc.)
			if ( ( is_singular() && ! empty( $post ) ) || ( ! empty( $post ) && ! empty( $use_post ) ) )
				$desc = $this->ngfb->meta->get_options( $post->ID, 'og_desc' );

			if ( ! empty( $desc ) )
				$this->ngfb->debug->log( 'found custom meta description = "' . $desc . '"' );

			elseif ( is_singular() || ( ! empty( $post ) && ! empty( $use_post ) ) ) {

				$this->ngfb->debug->log( 'is_singular() = ' . ( is_singular() ? 'true' : 'false' ) );
				$this->ngfb->debug->log( 'use_post = ' . ( $use_post  ? 'true' : 'false' ) );

				// use the excerpt, if we have one
				if ( has_excerpt( $post->ID ) ) {
					$this->ngfb->debug->log( 'has_excerpt() = true' );
					$desc = $post->post_excerpt;
					if ( ! empty( $this->ngfb->options['ngfb_filter_excerpt'] ) ) {
						$filter_removed = $this->ngfb->social->remove_filter( 'the_excerpt' );
						$this->ngfb->debug->log( 'calling apply_filters()' );
						$desc = apply_filters( 'the_excerpt', $desc );
						if ( ! empty( $filter_removed ) )
							$this->ngfb->social->add_filter( 'the_excerpt' );
					}
				} 
		
				if ( empty( $desc ) ) {
					$this->ngfb->debug->log( 'calling this->get_content()' );
					$desc = $this->get_content( $this->ngfb->options['ngfb_filter_content'], $use_cache );
				}
		
				// ignore everything until the first paragraph tag if $this->ngfb->options['og_desc_strip'] is true
				if ( $this->ngfb->options['og_desc_strip'] ) $desc = preg_replace( '/^.*?<p>/i', '', $desc );	// question mark makes regex un-greedy
		
			} elseif ( is_author() ) { 
		
				$this->ngfb->debug->log( 'is_author() = true' );
				the_post();
				$desc = sprintf( 'Authored by %s', get_the_author_meta( 'display_name' ) );
				$author_desc = get_the_author_meta( 'description' );
				if ( $author_desc ) $desc .= ' : '.$author_desc;		// add the author's profile description, if there is one
		
			} elseif ( is_tag() ) {
		
				$this->ngfb->debug->log( 'is_tag() = true' );
				$desc = sprintf( 'Tagged with %s', single_tag_title( '', false ) );
				$tag_desc = tag_description();
				if ( $tag_desc ) $desc .= ' : '.$tag_desc;			// add the tag description, if there is one
		
			} elseif ( is_category() ) { 
		
				$this->ngfb->debug->log( 'is_category() = true' );
				$desc = sprintf( '%s Category', single_cat_title( '', false ) ); 
				$cat_desc = category_description();
				if ($cat_desc) $desc .= ' : '.$cat_desc;			// add the category description, if there is one
			}
			elseif ( is_day() ) $desc = sprintf( 'Daily Archives for %s', get_the_date() );
			elseif ( is_month() ) $desc = sprintf( 'Monthly Archives for %s', get_the_date('F Y') );
			elseif ( is_year() ) $desc = sprintf( 'Yearly Archives for %s', get_the_date('Y') );
			else $desc = get_bloginfo( 'description', 'display' );

			$desc = $this->ngfb->util->cleanup_html_tags( $desc );

			if ( $textlen > 0 ) 
				$desc = $this->ngfb->util->limit_text_length( $desc, $textlen, '...' );

			if ( $this->ngfb->is_avail['aop'] ) 
				return apply_filters( 'ngfb_description', $desc );
			else return $desc;
		}

		public function get_content( $filter_content = true, $use_cache = true ) {
			global $post;
			if ( empty( $post ) ) return;
			$filter_name = $filter_content  ? 'filtered' : 'unfiltered';
			$this->ngfb->debug->log( 'using content from post id ' . $post->ID );
			$cache_salt = __METHOD__ . '(post:' . $post->ID . '_' . $filter_name . ')';
			$cache_id = $this->ngfb->acronym . '_' . md5( $cache_salt );
			$cache_type = 'object cache';
			$content = $use_cache === true ? wp_cache_get( $cache_id, __METHOD__ ) : false;
			$this->ngfb->debug->log( $cache_type . ': ' . $filter_name . ' content wp_cache id salt "' . $cache_salt . '"' );

			if ( $content !== false ) {
				$this->ngfb->debug->log( $cache_type . ': ' . $filter_name . ' content retrieved from wp_cache for id "' . $cache_id . '"' );
				return $content;
			} 
			$content = $post->post_content;
			$content_strlen_before = strlen( $content );

			// remove singlepics, which we detect and use before-hand 
			$content = preg_replace( '/\[singlepic[^\]]+\]/', '', $content, -1, $count );
			if ( $count > 0 ) $this->ngfb->debug->log( $count . ' [singlepic] shortcode(s) removed from content' );

			if ( $filter_content == true ) {

				if ( is_object( $this->ngfb->social ) )
					$filter_removed = $this->ngfb->social->remove_filter( 'the_content' );
				foreach ( $this->ngfb->shortcode_libs as $id => $name )
					if ( array_key_exists( $id, $this->shortcode ) && is_object( $this->shortcode[$id] ) )
						$this->shortcode[$id]->remove();
				unset ( $id, $name );

				$this->ngfb->debug->log( 'calling apply_filters()' );
				$content = apply_filters( 'the_content', $content );

				// cleanup for NGG album shortcode
				unset ( $GLOBALS['subalbum'] );
				unset ( $GLOBALS['nggShowGallery'] );

				if ( is_object( $this->ngfb->social ) && ! empty( $filter_removed ) )
					$this->ngfb->social->add_filter( 'the_content' );

				foreach ( $this->ngfb->shortcode_libs as $id => $name )
					if ( array_key_exists( $id, $this->shortcode ) && is_object( $this->shortcode[$id] ) )
						$this->shortcode[$id]->add();
				unset ( $id, $name );
			}
			$content = preg_replace( '/[\r\n\t ]+/s', ' ', $content );	// put everything on one line
			$content = preg_replace( '/^.*<!--ngfb-content-->(.*)<!--\/ngfb-content-->.*$/', '$1', $content );
			$content = preg_replace( '/<a +rel="author" +href="" +style="display:none;">Google\+<\/a>/', ' ', $content );
			$content = str_replace( ']]>', ']]&gt;', $content );
			$content_strlen_after = strlen( $content );
			$this->ngfb->debug->log( 'content strlen() before = ' . $content_strlen_before . ', after = ' . $content_strlen_after );

			if ( $this->ngfb->is_avail['aop'] ) $content = apply_filters( 'ngfb_content', $content );
			wp_cache_set( $cache_id, $content, __METHOD__, $this->ngfb->cache->object_expire );
			$this->ngfb->debug->log( $cache_type . ': ' . $filter_name . ' content saved to wp_cache for id "' . $cache_id . '" (' . $this->ngfb->cache->object_expire . ' seconds)');
			return $content;
		}

		public function get_section() {
			global $post;
			$section = '';
			if ( is_singular() && ! empty( $post ) )
				$section = $this->ngfb->meta->get_options( $post->ID, 'og_art_section' );
			if ( ! empty( $section ) ) 
				$this->ngfb->debug->log( 'found custom meta section = "' . $section . '"' );
			else $section = $this->ngfb->options['og_art_section'];
			if ( $section == 'none' ) $section = '';
			if ( $this->ngfb->is_avail['aop'] ) 
				return apply_filters( 'ngfb_section', $section );
			else return $section;
		}

	}

}
?>
