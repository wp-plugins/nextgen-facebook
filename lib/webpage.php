<?php
/*
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/

This script is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This script is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details at
http://www.gnu.org/licenses/.
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbWebPage' ) ) {

	class ngfbWebPage {

		private $ngfb;
		private $shortcode = array();

		public function __construct( &$ngfb_plugin ) {

			$this->ngfb =& $ngfb_plugin;

			foreach ( $this->ngfb->shortcode_class_names as $id => $name ) {
				$classname = 'ngfbShortCode' . $name;
				$this->shortcode[$id] = new $classname( $ngfb_plugin );
			}
			unset ( $id, $name );
		}

		// called from Tumblr class
		public function get_quote() {
			global $post;
			if ( empty( $post ) ) return;
			if ( has_excerpt( $post->ID ) ) $content = get_the_excerpt( $post->ID );
			else $content = $post->post_content;					// fallback to regular content
			$content = $this->ngfb->util->cleanup_html_tags( $content, false );	// remove shortcodes, etc., but don't strip html tags
			return $content;
		}

		// called from Tumblr, Pinterest, and Twitter classes
		public function get_caption( $type = 'title', $length = 300, $use_post = true ) {
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
			return $caption;
		}

		public function get_title( $textlen = 100, $trailing = '', $use_post = false ) {
			global $post, $page, $paged;
			$title = '';
			$page_num = '';
			$parent_title = '';

			if ( is_category() ) { 

				$title = single_cat_title( '', false );
				$this->ngfb->debug->push( 'single_cat_title() = "' . $title . '"' );
				$cat_parents = get_category_parents( get_cat_ID( $title ), false, ' ' . $this->ngfb->options['og_title_sep'] . ' ', false );

				// use is_wp_error() to avoid "Object of class WP_Error could not be converted to string" error
				if ( is_wp_error( $cat_parents ) ) {
					$this->ngfb->debug->push( 'get_category_parents() returned WP_Error object.' );
				} else {
					$this->ngfb->debug->push( 'get_category_parents() = "' . $cat_parents . '"' );
					if ( ! empty( $cat_parents ) ) {
						$title = trim( $cat_parents, ' ' . $this->ngfb->options['og_title_sep'] );
						// beautify title with category names that end with three dots
						$title = preg_replace( '/\.\.\. \\' . $this->ngfb->options['og_title_sep'] . ' /', '... ', $title );
					}
				}
				unset ( $cat_parents );

			} elseif ( ! is_singular() && ! empty( $post ) && ! empty( $use_post ) ) {

				$this->ngfb->debug->push( '$use_post = ' . ( $use_post ? 'true' : 'false' ) );
				$title = get_the_title();
				$this->ngfb->debug->push( 'get_the_title() = "' . $title . '"' );
				if ( $post->post_parent ) {
					$parent_title = get_the_title( $post->post_parent );
					if ( $parent_title ) $title .= ' (' . $parent_title . ')';
				}

			} else {
				/* The title text depends on the query:
				 *	Single post = the title of the post 
				 *	Date-based archive = the date (e.g., "2006", "2006 - January") 
				 *	Category = the name of the category 
				 *	Author page = the public name of the user 
				 */
				$title = trim( wp_title( $this->ngfb->options['og_title_sep'], false, 'right' ), ' ' . $this->ngfb->options['og_title_sep'] );
				$this->ngfb->debug->push( 'wp_title() = "' . $title . '"' );
			}

			// just in case
			if ( ! $title ) {
				$title = get_bloginfo( 'name', 'display' );
				$this->ngfb->debug->push( 'get_bloginfo() = "' . $title . '"' );
			}

			// add a page number if necessary
			if ( $paged >= 2 || $page >= 2 ) {
				$page_num = ' ' . $this->ngfb->options['og_title_sep'] . ' ' . sprintf( 'Page %s', max( $paged, $page ) );
				$textlen = $textlen - strlen( $page_num );	// make room for the page number
			}

			$title = $this->ngfb->util->decode( $title );

			if ( ! empty( $this->ngfb->options['ngfb_filter_title'] ) ) {
				$title = apply_filters( 'the_title', $title );
				$this->ngfb->debug->push( 'apply_filters() = "' . $title . '"' );
			}

			$title = $this->ngfb->util->cleanup_html_tags( $title );
			$this->ngfb->debug->push( 'this->ngfb->util->cleanup_html_tags() = "' . $title . '"' );

			// append the text number after the trailing character string
			if ( $textlen > 0 ) $title = $this->ngfb->util->limit_text_length( $title, $textlen, $trailing );

			return $title . $page_num;
		}

		public function get_description( $textlen = 300, $trailing = '', $use_post = false ) {
			global $post;
			$desc = '';
			if ( is_singular() || ( ! empty( $post ) && ! empty( $use_post ) ) ) {

				$this->ngfb->debug->push( 'is_singular() = ' . ( is_singular() ? 'true' : 'false' ) );
				$this->ngfb->debug->push( 'use_post = ' . ( $use_post  ? 'true' : 'false' ) );

				// use the excerpt, if we have one
				if ( has_excerpt( $post->ID ) ) {
					$this->ngfb->debug->push( 'has_excerpt() = true' );
					$desc = $post->post_excerpt;
					if ( ! empty( $this->ngfb->options['ngfb_filter_excerpt'] ) )
						$desc = apply_filters( 'the_excerpt', $desc );
		
				// if there's no excerpt, then use WP-WikiBox for page content (if wikibox is active and og_desc_wiki option is true)
				} elseif ( is_page() && ! empty( $this->ngfb->options['og_desc_wiki'] ) && $this->ngfb->is_avail['wikibox'] == true ) {
					$this->ngfb->debug->push( 'is_page() && options["og_desc_wiki"] = 1 && is_avail["wikibox"] = true' );
					$desc = $this->get_wiki_summary();
				} 
		
				if ( empty( $desc ) ) {
					$this->ngfb->debug->push( 'calling this->get_content_filtered()' );
					$desc = $this->get_content_filtered( $this->ngfb->options['ngfb_filter_content'] );
				}
		
				// ignore everything until the first paragraph tag if $this->ngfb->options['og_desc_strip'] is true
				if ( $this->ngfb->options['og_desc_strip'] ) $desc = preg_replace( '/^.*?<p>/i', '', $desc );	// question mark makes regex un-greedy
		
			} elseif ( is_author() ) { 
		
				$this->ngfb->debug->push( 'is_author() = true' );
				the_post();
				$desc = sprintf( 'Authored by %s', get_the_author_meta( 'display_name' ) );
				$author_desc = preg_replace( '/[\r\n\t ]+/s', ' ', get_the_author_meta( 'description' ) );	// put everything on one line
				if ( $author_desc ) $desc .= ' : '.$author_desc;		// add the author's profile description, if there is one
		
			} elseif ( is_tag() ) {
		
				$this->ngfb->debug->push( 'is_tag() = true' );
				$desc = sprintf( 'Tagged with %s', single_tag_title( '', false ) );
				$tag_desc = preg_replace( '/[\r\n\t ]+/s', ' ', tag_description() );	// put everything on one line
				if ( $tag_desc ) $desc .= ' : '.$tag_desc;			// add the tag description, if there is one
		
			} elseif ( is_category() ) { 
		
				$this->ngfb->debug->push( 'is_category() = true' );
				$desc = sprintf( '%s Category', single_cat_title( '', false ) ); 
				$cat_desc = preg_replace( '/[\r\n\t ]+/', ' ', category_description() );	// put everything on one line
				if ($cat_desc) $desc .= ' : '.$cat_desc;			// add the category description, if there is one
			}
			elseif ( is_day() ) $desc = sprintf( 'Daily Archives for %s', get_the_date() );
			elseif ( is_month() ) $desc = sprintf( 'Monthly Archives for %s', get_the_date('F Y') );
			elseif ( is_year() ) $desc = sprintf( 'Yearly Archives for %s', get_the_date('Y') );
			else $desc = get_bloginfo( 'description', 'display' );

			$desc = $this->ngfb->util->cleanup_html_tags( $desc );

			if ( $textlen > 0 ) 
				$desc = $this->ngfb->util->limit_text_length( $desc, $textlen, '...' );

			return $desc;
		}

		public function get_content_filtered( $filter_content = true ) {
			global $post;
			if ( empty( $post ) ) return;
			$this->ngfb->debug->push( 'using content from post id ' . $post->ID );
			$cache_salt = __METHOD__ . '(post:' . $post->ID . ( $filter_content  ? '_filtered' : '_unfiltered' ) . ')';
			$cache_id = NGFB_SHORTNAME . '_' . md5( $cache_salt );
			$cache_type = 'object cache';
			$content = wp_cache_get( $cache_id, __METHOD__ );
			$this->ngfb->debug->push( $cache_type . ': filtered content wp_cache id salt "' . $cache_salt . '"' );

			if ( $content !== false ) {
				$this->ngfb->debug->push( $cache_type . ': filtered content retrieved from wp_cache for id "' . $cache_id . '"' );
				return $content;
			} 
			$content = $post->post_content;
			$content_strlen_before = strlen( $content );

			// remove singlepics, which we detect and use before-hand 
			$content = preg_replace( '/\[singlepic[^\]]+\]/', '', $content, -1, $count );
			if ( $count > 0 ) $this->ngfb->debug->push( $count . ' [singlepic] shortcode(s) removed from content' );

			if ( $filter_content == true ) {

				$filter_removed = $this->ngfb->social->remove_filter( 'the_content' );
				foreach ( $this->ngfb->shortcode_class_names as $id => $name )
					$this->shortcode[$id]->remove();

				$this->ngfb->debug->push( 'calling apply_filters()' );
				$content = apply_filters( 'the_content', $content );

				// cleanup for NGG album shortcode
				unset ( $GLOBALS['subalbum'] );
				unset ( $GLOBALS['nggShowGallery'] );

				if ( ! empty( $filter_removed ) )
					$this->ngfb->social->add_filter( 'the_content' );

				foreach ( $this->ngfb->shortcode_class_names as $id => $name )
					$this->shortcode[$id]->add();
				unset ( $id, $name );
			}
			$content = preg_replace( '/<a +rel="author" +href="" +style="display:none;">Google\+<\/a>/', ' ', $content );
			$content = preg_replace( '/[\r\n\t ]+/s', ' ', $content );	// put everything on one line
			$content = str_replace( ']]>', ']]&gt;', $content );
			$content_strlen_after = strlen( $content );
			$this->ngfb->debug->push( 'content strlen() before = ' . $content_strlen_before . ', after = ' . $content_strlen_after );

			wp_cache_set( $cache_id, $content, __METHOD__, $this->ngfb->cache->object_expire );
			$this->ngfb->debug->push( $cache_type . ': filtered content saved to wp_cache for id "' . $cache_id . '" (' . $this->ngfb->cache->object_expire . ' seconds)');

			return $content;
		}

		public function is_excluded() {
			global $post;
			if ( is_page() && $post->ID && $this->ngfb->is_avail['expages'] == true && empty( $this->ngfb->options['buttons_on_ex_pages'] ) ) {
				$excluded_ids = ep_get_excluded_ids();
				$delete_ids = array_unique( $excluded_ids );
				if ( in_array( $post->ID, $delete_ids ) ) return true;
			}
			return false;
		}

		// called from the view/gallery-uwf.php template
		public function get_wiki_summary() {
			global $post;
			$desc = '';
			if ( $this->is_avail['wikibox'] !== true ) return $desc;
			$tag_prefix = $this->options['og_wiki_tag'];
			$tags = wp_get_post_tags( $post->ID, array( 'fields' => 'names') );
			$this->debug->push( 'post tags = ' . implode( ', ', $tags ) );
			foreach ( $tags as $tag_name ) {
				if ( $tag_prefix ) {
					if ( preg_match( "/^$tag_prefix/", $tag_name ) ) {
						$tag_name = preg_replace( "/^$tag_prefix/", '', $tag_name );
						if ( $tag_name == 'NoWikiText' ) return $desc;
					}
					else continue;	// skip tags that don't have the prefix
				}
				$desc .= wikibox_summary( $tag_name, 'en', false ); 
				$this->debug->push( 'wikibox_summary("' . $tag_name . '") = ' . $desc );
			}
			if ( empty( $desc ) ) {
				$title = the_title( '', '', false );
				$desc .= wikibox_summary( $title, 'en', false );
				$this->debug->push( 'wikibox_summary("' . $title . '") = ' . $desc );
			}
			return $desc;
		}

	}

}
?>
