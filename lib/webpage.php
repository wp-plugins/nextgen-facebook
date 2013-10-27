<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'ngfbWebPage' ) ) {

	class ngfbWebPage {

		private $p;
		private $shortcode = array();

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
			$this->setup_vars();
		}

		private function setup_vars() {
			foreach ( $this->p->cf['lib']['shortcode'] as $id => $name ) {
				$classname = $this->p->cf['lca'].'ShortCode'.preg_replace( '/ /', '', $name );
				if ( class_exists( $classname ) )
					$this->shortcode[$id] = new $classname( $this->p );
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
			$quote = $this->p->util->cleanup_html_tags( $quote, false );		// remove shortcodes, etc., but don't strip html tags
			return apply_filters( $this->p->cf['lca'].'_quote', $quote );		// since wp 0.71 
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
					$caption = $title.' : '.$this->get_description( $length - strlen( $title ) - 3, '...', $use_post );
					break;
			}
			return apply_filters( $this->p->cf['lca'].'_caption', $caption );
		}

		public function get_title( $textlen = 70, $trailing = '', $use_post = false ) {
			global $post, $page, $paged;
			$title = '';
			$parent_title = '';
			$page_num_suffix = '';

			// check for custom meta title
			if ( ( is_singular() && ! empty( $post ) ) || ( ! empty( $post ) && ! empty( $use_post ) ) ) {
				$title = $this->p->meta->get_options( $post->ID, 'og_title' );
				if ( $title != '' )
					$this->p->debug->log( 'custom meta title = "'.$title.'"' );
			}

			// get seed if no custom meta title
			if ( $title == '' ) {
				$title = apply_filters( $this->p->cf['lca'].'_title_seed', '' );
				if ( $title != '' )
					$this->p->debug->log( 'title seed = "'.$title.'"' );
			}
			
			// construct a title of our own
			if ( $title == '' ) {
				// we are on an index, but need individual titles from the posts (probably for social buttons)
				if ( ! is_singular() && ! empty( $post ) && ! empty( $use_post ) ) {	// since wp 1.5.0
	
					$this->p->debug->log( 'is_singular() = '.( is_singular() ? 'true' : 'false' ) );
					$this->p->debug->log( 'use_post = '.( $use_post  ? 'true' : 'false' ) );
	
					$title = get_the_title( $post->ID );	// since wp 0.71 
					$this->p->debug->log( 'get_the_title() = "'.$title.'"' );

					// add the parent's title if no seo package is installed
					if ( $this->p->is_avail['any_seo'] == false && ! empty( $post->post_parent ) ) {
						$parent_title = get_the_title( $post->post_parent );
						if ( $parent_title ) $title .= ' ('.$parent_title.')';
					}
	
				// by default, use the wordpress title if an seo plugin is available
				} elseif ( $this->p->is_avail['any_seo'] == true ) {

					// use separator on right for compatibility with aioseo
					$title = wp_title( $this->p->options['og_title_sep'], false, 'right' );
					$this->p->debug->log( 'seo wp_title() = "'.$title.'"' );
	
				// category title, with category parents
				} elseif ( is_category() ) { 
	
					$title = single_cat_title( '', false );		// since wp 0.71
					$this->p->debug->log( 'single_cat_title() = "'.$title.'"' );
					$cat_parents = get_category_parents( get_cat_ID( $title ), false, 
						' '.$this->p->options['og_title_sep'].' ', false );
	
					// use is_wp_error() to avoid "Object of class WP_Error could not be converted to string" error
					if ( is_wp_error( $cat_parents ) ) {
						$this->p->debug->log( 'get_category_parents() returned WP_Error object.' );
					} else {
						$this->p->debug->log( 'get_category_parents() = "'.$cat_parents.'"' );
						if ( ! empty( $cat_parents ) ) {
							$title = trim( $cat_parents, ' '.$this->p->options['og_title_sep'] );
							// special fix for category names that end with three dots
							$title = preg_replace( '/\.\.\. \\'.$this->p->options['og_title_sep'].' /', '... ', $title );
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
					$title = wp_title( $this->p->options['og_title_sep'], false, 'right' );
					$this->p->debug->log( 'wp_title() = "'.$title.'"' );
				}
	
				// just in case
				if ( $title == '' )
					$title = get_bloginfo( 'name', 'display' );
			}

			$title = $this->p->util->decode( $title );
			$title = $this->p->util->cleanup_html_tags( $title );
			$title = trim( $title, ' '.$this->p->options['og_title_sep'] );	// trim spaces and excess seperator

			// seo-like title modifications
			if ( $this->p->is_avail['any_seo'] == false ) {
				// append the parent's title 
				if ( is_singular() && ! empty( $post->post_parent ) ) {
					$parent_title = get_the_title( $post->post_parent );
					if ( ! empty( $parent_title ) ) 
						$title .= ' ('.$parent_title.')';
				}
				// add a page number
				if ( $paged >= 2 || $page >= 2 ) {
					if ( ! empty( $this->p->options['og_title_sep'] ) )
						$page_num_suffix .= ' '.$this->p->options['og_title_sep'];
					$page_num_suffix .= ' '.sprintf( 'Page %s', max( $paged, $page ) );
					$textlen = $textlen - strlen( $page_num_suffix );	// make room for the page number
				}
			}

			if ( $textlen > 0 ) 
				$title = $this->p->util->limit_text_length( $title, $textlen, $trailing );

			// append the text number after the trailing character string
			if ( ! empty( $page_num_suffix ) ) $title .= $page_num_suffix;

			return apply_filters( $this->p->cf['lca'].'_title', $title );
		}

		public function get_description( $textlen = NGFB_MIN_DESC_LEN, $trailing = '', $use_post = false, $use_cache = true ) {
			global $post;
			$desc = '';

			// check for custom meta description
			// og_desc meta is the fallback for all other description fields as well (link_desc, tc_desc, etc.)
			if ( ( is_singular() && ! empty( $post ) ) || ( ! empty( $post ) && ! empty( $use_post ) ) ) {
				$desc = $this->p->meta->get_options( $post->ID, 'og_desc' );
				if ( $desc != '' )
					$this->p->debug->log( 'custom meta description = "'.$desc.'"' );
			}

			// get seed if no custom meta description
			if ( $desc == '' ) {
				$desc = apply_filters( $this->p->cf['lca'].'_description_seed', '' );
				if ( $desc != '' )
					$this->p->debug->log( 'description seed = "'.$desc.'"' );
			}
			
			// if there's no custom description, and no pre-seed, then go ahead and generate the description value
			if ( $desc == '' ) {
				if ( is_singular() || ( ! empty( $post ) && ! empty( $use_post ) ) ) {
	
					$this->p->debug->log( 'use_post = '.( $use_post  ? 'true' : 'false' ) );
					$this->p->debug->log( 'is_singular() = '.( is_singular() ? 'true' : 'false' ) );
					$this->p->debug->log( 'has_excerpt() = '.( has_excerpt( $post->ID ) ? 'true' : 'false' ) );
	
					// use the excerpt, if we have one
					if ( has_excerpt( $post->ID ) ) {
						$desc = $post->post_excerpt;
						if ( ! empty( $this->p->options['plugin_filter_excerpt'] ) ) {
							$filter_removed = $this->p->social->remove_filter( 'the_excerpt' );
							$this->p->debug->log( 'calling apply_filters()' );
							$desc = apply_filters( 'the_excerpt', $desc );
							if ( ! empty( $filter_removed ) )
								$this->p->social->add_filter( 'the_excerpt' );
						}
					} 
			
					if ( empty( $desc ) )
						$desc = $this->get_content( $this->p->options['plugin_filter_content'], $use_cache );
			
					// ignore everything until the first paragraph tag if $this->p->options['og_desc_strip'] is true
					if ( $this->p->options['og_desc_strip'] ) 
						$desc = preg_replace( '/^.*?<p>/i', '', $desc );	// question mark makes regex un-greedy
			
				} elseif ( is_author() ) { 
			
					$this->p->debug->log( 'is_author() = true' );
					$author = get_query_var( 'author_name' ) ?  get_userdata( get_query_var( 'author' ) ) : get_user_by( 'slug', get_query_var( 'author_name' ) );
					$desc = empty( $author->description ) ? sprintf( 'Authored by %s', $author->display_name ) : $author->description;
			
				} elseif ( is_tag() ) {
			
					$this->p->debug->log( 'is_tag() = true' );
					$desc = tag_description();
					if ( empty( $desc ) )
						$desc = sprintf( 'Tagged with %s', single_tag_title( '', false ) );
			
				} elseif ( is_category() ) { 
			
					$this->p->debug->log( 'is_category() = true' );
					$desc = category_description();
					if ( empty( $desc ) )
						$desc = sprintf( '%s Category', single_cat_title( '', false ) ); 
				}
				elseif ( is_day() ) $desc = sprintf( 'Daily Archives for %s', get_the_date() );
				elseif ( is_month() ) $desc = sprintf( 'Monthly Archives for %s', get_the_date('F Y') );
				elseif ( is_year() ) $desc = sprintf( 'Yearly Archives for %s', get_the_date('Y') );
				else $desc = get_bloginfo( 'description', 'display' );
			}

			$desc = $this->p->util->decode( $desc );
			$desc = $this->p->util->cleanup_html_tags( $desc );

			if ( $textlen > 0 ) 
				$desc = $this->p->util->limit_text_length( $desc, $textlen, '...' );

			return apply_filters( $this->p->cf['lca'].'_description', $desc );
		}

		public function get_content( $filter_content = true, $use_cache = true ) {
			global $post;
			if ( empty( $post ) ) return;
			$this->p->debug->log( 'using content from post id '.$post->ID );
			$filter_name = $filter_content  ? 'filtered' : 'unfiltered';

			/***************************************************************************
			 * Retrieve the content                                                    *
			 ***************************************************************************/

			if ( defined( 'NGFB_OBJECT_CACHE_DISABLE' ) && NGFB_OBJECT_CACHE_DISABLE )
				$this->p->debug->log( 'object cache is disabled' );
			else {
				$cache_salt = __METHOD__.'(lang:'.get_locale().'_post:'.$post->ID.'_'.$filter_name.')';
				$cache_id = $this->p->cf['lca'].'_'.md5( $cache_salt );
				$cache_type = 'object cache';
				$this->p->debug->log( $cache_type.': '.$filter_name.' content wp_cache id salt "'.$cache_salt.'"' );
				$content = $use_cache === true ? wp_cache_get( $cache_id, __METHOD__ ) : false;
				if ( $content !== false ) {
					$this->p->debug->log( $cache_type.': '.$filter_name.' content retrieved from wp_cache for id "'.$cache_id.'"' );
					return $content;
				}
			}

			$content = apply_filters( $this->p->cf['lca'].'_content_seed', '' );
			if ( ! empty( $content ) )
				$this->p->debug->log( 'content seed = "'.$content.'"' );

			// exceptions for some woocommerce pages
			if ( empty( $content ) && ! empty( $this->p->is_avail['woocommerce'] ) ) {
				if ( is_cart() )
					$content = 'Shopping Cart';
				elseif ( is_checkout() )
					$content = 'Checkout Page';
				elseif ( is_account_page() )
					$content = 'Account Page';
			}

			if ( empty( $content ) )
				$content = $post->post_content;

			/***************************************************************************
			 * Modify the content                                                      *
			 ***************************************************************************/

			// save content length (for comparison) before making changes
			$content_strlen_before = strlen( $content );

			// remove singlepics, which we detect and use before-hand 
			$content = preg_replace( '/\[singlepic[^\]]+\]/', '', $content, -1, $count );
			if ( $count > 0 ) 
				$this->p->debug->log( $count.' [singlepic] shortcode(s) removed from content' );

			if ( $filter_content == true ) {

				// remove the social buttons filter, which would create a loop with this method
				if ( is_object( $this->p->social ) )
					$filter_removed = $this->p->social->remove_filter( 'the_content' );

				// remove all of our shortcodes
				foreach ( $this->p->cf['lib']['shortcode'] as $id => $name )
					if ( array_key_exists( $id, $this->shortcode ) && 
						is_object( $this->shortcode[$id] ) )
							$this->shortcode[$id]->remove();
				unset ( $id, $name );

				$this->p->debug->log( 'calling apply_filters()' );
				$content = apply_filters( 'the_content', $content );

				// cleanup for NGG pre-v2 album shortcode
				unset ( $GLOBALS['subalbum'] );
				unset ( $GLOBALS['nggShowGallery'] );

				// add the social buttons filter back, if it was removed
				if ( is_object( $this->p->social ) && ! empty( $filter_removed ) )
					$this->p->social->add_filter( 'the_content' );

				// add our shortcodes back
				foreach ( $this->p->cf['lib']['shortcode'] as $id => $name )
					if ( array_key_exists( $id, $this->shortcode ) && 
						is_object( $this->shortcode[$id] ) )
							$this->shortcode[$id]->add();
				unset ( $id, $name );
			}

			$content = preg_replace( '/[\r\n\t ]+/s', ' ', $content );	// put everything on one line
			$content = preg_replace( '/^.*<!--ngfb-content-->(.*)<!--\/ngfb-content-->.*$/', '$1', $content );
			$content = preg_replace( '/<a +rel="author" +href="" +style="display:none;">Google\+<\/a>/', ' ', $content );
			$content = str_replace( ']]>', ']]&gt;', $content );

			$content_strlen_after = strlen( $content );
			$this->p->debug->log( 'content strlen() before = '.$content_strlen_before.', after = '.$content_strlen_after );

			// apply filters before caching
			$content = apply_filters( $this->p->cf['lca'].'_content', $content );

			if ( ! defined( 'NGFB_OBJECT_CACHE_DISABLE' ) || ! NGFB_OBJECT_CACHE_DISABLE ) {
				wp_cache_set( $cache_id, $content, __METHOD__, $this->p->cache->object_expire );
				$this->p->debug->log( $cache_type.': '.$filter_name.' content saved to wp_cache for id "'.$cache_id.'" ('.$this->p->cache->object_expire.' seconds)');
			}
			return $content;
		}

		public function get_section() {
			global $post;
			$section = '';
			if ( is_singular() && ! empty( $post ) )
				$section = $this->p->meta->get_options( $post->ID, 'og_art_section' );
			if ( ! empty( $section ) ) 
				$this->p->debug->log( 'found custom meta section = "'.$section.'"' );
			else $section = $this->p->options['og_art_section'];
			if ( $section == 'none' ) $section = '';
			return apply_filters( $this->p->cf['lca'].'_section', $section );
		}

	}

}
?>
