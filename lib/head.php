<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbHead' ) ) {

	class NgfbHead {

		private $p;

		public $og;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();

			add_action( 'wp_head', array( &$this, 'add_header' ), NGFB_HEAD_PRIORITY );
		}

		// called by WP wp_head action
		public function add_header() {
			if ( $this->p->debug->is_on() ) {
				foreach ( array( 
					'is_author',
					'is_archive',
					'is_category',
					'is_tag',
					'is_home',
					'is_search',
					'is_singular',
					'is_attachment',
					'is_product',
					'is_product_category',
					'is_product_tag',
					) as $func ) if ( function_exists( $func ) )
						$this->p->debug->log( $func.'() = '.( $func() ? 'true' : 'false' ) );
			}

			if ( method_exists( $this->p->og, 'get_array' ) )
				$this->show_html( $this->p->og->get_array() );

			if ( $this->p->debug->is_on() ) {
				$defined_constants = get_defined_constants( true );
				$defined_constants['user']['NGFB_NONCE'] = '********';
				$this->p->debug->show_html( $this->p->util->preg_grep_keys( '/^NGFB_/', $defined_constants['user'] ), 'ngfb constants' );

				$opts = $this->p->options;
				foreach ( array( 
					'plugin_tid', 
					'plugin_googl_api_key', 
					'plugin_bitly_api_key',
					'plugin_wistia_pwd',
				) as $key ) $opts[$key] = '********';

				$this->p->debug->show_html( print_r( $this->p->is_avail, true ), 'available features' );
				$this->p->debug->show_html( null, 'debug log' );
				$this->p->debug->show_html( $opts, 'ngfb settings' );
			}
		}

		// called from add_header() and the work/header.php template
		public function show_html( $meta_tags = array() ) {
			
			if ( ( $obj = $this->p->util->get_the_object() ) === false ) {
				$this->p->debug->log( 'exiting early: invalid object type' );
				return array();
			}

			$post_id = empty( $obj->ID ) ? 0 : $obj->ID;
			$author_url = '';
		
			echo "\n<!-- ".$this->p->cf['lca']." meta tags begin -->\n";
			if ( $this->p->is_avail['aop'] )
				echo "<!-- updates: ".$this->p->cf['url']['pro_update']." -->\n";

			// show the array structure before the html block
			$this->p->debug->show_html( print_r( $meta_tags, true ), 'open graph array' );
			$this->p->debug->show_html( print_r( $this->p->util->get_urls_found(), true ), 'media urls found' );

			echo '<meta name="generator" content="'.$this->p->cf['full'].' '.$this->p->cf['version'].'-';
			if ( $this->p->check->is_aop() ) echo 'L';
			elseif ( $this->p->is_avail['aop'] ) echo 'U';
			else echo 'G';
			echo '" />'."\n";

			/*
			 * Meta HTML Tags for Google
			 */
			$links = array();
			if ( array_key_exists( 'link:publisher', $meta_tags ) ) {
				$links['publisher'] = $meta_tags['link:publisher'];
				unset ( $meta_tags['link:publisher'] );
			} elseif ( ! empty( $this->p->options['link_publisher_url'] ) )
				$links['publisher'] = $this->p->options['link_publisher_url'];

			if ( array_key_exists( 'link:author', $meta_tags ) ) {
				$links['author'] = $meta_tags['link:author'];
				unset ( $meta_tags['link:author'] );
			} else {
				if ( is_singular() ) {
					if ( ! empty( $obj->post_author ) )
						$links['author'] = $this->p->user->get_author_url( $obj->post_author, 
							$this->p->options['link_author_field'] );
					elseif ( ! empty( $this->p->options['link_def_author_id'] ) )
						$links['author'] = $this->p->user->get_author_url( $this->p->options['link_def_author_id'], 
							$this->p->options['link_author_field'] );

				// check for default author info on indexes and searches
				} elseif ( ( ! is_singular() && ! is_search() && ! empty( $this->p->options['link_def_author_on_index'] ) && ! empty( $this->p->options['link_def_author_id'] ) )
					|| ( is_search() && ! empty( $this->p->options['link_def_author_on_search'] ) && ! empty( $this->p->options['link_def_author_id'] ) ) ) {

					$links['author'] = $this->p->user->get_author_url( $this->p->options['link_def_author_id'], 
						$this->p->options['link_author_field'] );
				}
			}
			$links = apply_filters( $this->p->cf['lca'].'_link', $links );
			foreach ( $links as $key => $val )
				if ( ! empty( $val ) )
					echo '<link rel="', $key, '" href="', $val, '" />', "\n";

			// the meta "description" html tag
			if ( ! empty( $this->p->options['inc_description'] ) ) {
				if ( ! array_key_exists( 'description', $meta_tags ) ) {
					if ( is_singular() && ! empty( $post_id ) )
						$meta_tags['description'] = $this->p->meta->get_options( $post_id, 'meta_desc' );
					if ( empty( $meta_tags['description'] ) )
						$meta_tags['description'] = $this->p->webpage->get_description( $this->p->options['meta_desc_len'], '...',
							false, true, false );	// use_post = false, use_cache = true, add_hashtags = false
				}
			}
			$meta_tags = apply_filters( $this->p->cf['lca'].'_meta', $meta_tags );

			/*
			 * Print the Multi-Dimensional Array as HTML
			 */
			$this->p->debug->log( count( $meta_tags ).' meta_tags to process' );
			foreach ( $meta_tags as $first_name => $first_val ) {			// 1st-dimension array (associative)
				if ( is_array( $first_val ) ) {
					if ( empty( $first_val ) ) {
						echo $this->get_meta_html( $first_name );	// possibly show an empty tag (depends on og_empty_tags value)
					} else {
						//$this->p->debug->log( 'foreach 1st-dimension element: '.$first_name.' (array)' );
						foreach ( $first_val as $second_num => $second_val ) {			// 2nd-dimension array
							if ( $this->p->util->is_assoc( $second_val ) ) {
								//$this->p->debug->log( 'foreach 2nd-dimension element: '.$second_num.' (array)' );
								ksort( $second_val );
								foreach ( $second_val as $third_name => $third_val ) {	// 3rd-dimension array (associative)
									//$this->p->debug->log( 'formatting 3rd-dimension element: '.$third_name );
									echo $this->get_meta_html( $third_name, $third_val, $first_name.':'.( $second_num + 1 ) );
								}
								unset ( $third_name, $third_val );
							} else {
								//$this->p->debug->log( 'formatting 2nd-dimension element: '.$second_num );
								echo $this->get_meta_html( $first_name, $second_val, $first_name.':'.( $second_num + 1 ) );
							}
						}
						unset ( $second_num, $second_val );
					}
				} else {
					//$this->p->debug->log( 'formatting 1st-dimension element: '.$first_name );
					echo $this->get_meta_html( $first_name, $first_val );
				}
			}
			unset ( $first_name, $first_val );

			echo "<!-- ", $this->p->cf['lca'], " meta tags end -->\n";
		}

		private function get_meta_html( $name, $val = '', $cmt = '' ) {
			$meta_html = '';

			if ( empty( $this->p->options['inc_'.$name] ) ) {
				$this->p->debug->log( 'meta '.$name.' is disabled (skipped)' );
				return $meta_html;
			} elseif ( $val === -1 ) {
				$this->p->debug->log( 'meta '.$name.' is -1 (skipped)' );
				return $meta_html;
			// ignore all empty non-open graph meta tags, 
			// and open-graph meta tags as well if the option allows
			} elseif ( $val === '' && 
				( preg_match( '/^description|fb:|twitter:/', $name ) || 
					empty( $this->p->options['og_empty_tags'] ) ) ) {

				$this->p->debug->log( 'meta '.$name.' is empty (skipped)' );
				return $meta_html;
			}

			$charset = get_bloginfo( 'charset' );
			$val = htmlentities( $val, ENT_QUOTES, $charset, false );	// double_encode = false

			$this->p->debug->log( 'meta '.$name.' = "'.$val.'"' );
			if ( $cmt ) $meta_html .= "<!-- $cmt -->";

			// by default, echo a <meta property="" content=""> html tag
			// the description and twitter card tags are exceptions
			if ( $name == 'description' || strpos( $name, 'twitter:' ) === 0 ) {

				$meta_html .= '<meta name="'.$name.'" content="'.$val.'" />'."\n";

			} elseif ( ( $name == 'og:image' || $name == 'og:video' ) && 
				strpos( $val, 'https:' ) === 0 && ! empty( $this->p->options['inc_'.$name] ) ) {

				$http_url = preg_replace( '/^https:/', 'http:', $val );
				$meta_html .= '<meta property="'.$name.'" content="'.$http_url.'" />'."\n";

				// add an additional secure_url meta tag
				if ( $cmt ) $meta_html .= "<!-- $cmt -->";
				$meta_html .= '<meta property="'.$name.':secure_url" content="'.$val.'" />'."\n";

			} else $meta_html .= '<meta property="'.$name.'" content="'.$val.'" />'."\n";

			return $meta_html;
		}
	}
}
?>
