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

			$classname = $this->p->cf['lca'].'OpenGraph';
			if ( class_exists( $classname ) )
				$this->og = new $classname( $plugin );

			add_action( 'wp_head', array( &$this, 'add_header' ), NGFB_HEAD_PRIORITY );
		}

		// called by WP wp_head action
		public function add_header() {

			if ( $this->p->debug->is_on() )
				foreach ( array( 
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

			if ( method_exists( $this->og, 'get' ) )
				$this->html( $this->og->get() );

			if ( $this->p->debug->is_on() ) {
				$defined_constants = get_defined_constants( true );
				$defined_constants['user']['NGFB_NONCE'] = '********';
				$this->p->debug->show_html( $this->p->util->preg_grep_keys( '/^NGFB_/', $defined_constants['user'] ), 'NGFB Constants' );

				$opts = $this->p->options;
				foreach ( array( 
					'plugin_pro_tid', 
					'plugin_googl_api_key', 
					'plugin_bitly_api_key',
				) as $key ) $opts[$key] = '********';

				$this->p->debug->show_html( $this->p->is_avail, 'Available Features' );
				$this->p->debug->show_html( null, 'Debug Log' );
				$this->p->debug->show_html( $opts, 'NGFB Settings' );
			}
		}

		// called from add_header() and the work/header.php template
		public function html( $meta_tags = array() ) {
			global $post;
			$author_url = '';
		
			echo "\n<!-- ".$this->p->cf['lca']." meta tags begin -->\n";
			if ( $this->p->is_avail['aop'] )
				echo "<!-- updates: ".$this->p->cf['url']['pro_update']." -->\n";

			// show the array structure before the html block
			$this->p->debug->show_html( print_r( $meta_tags, true ), 'Open Graph Array' );
			$this->p->debug->show_html( print_r( $this->p->util->get_urls_found(), true ), 'Media URLs Found' );

			echo '<meta name="generator" content="'.$this->p->cf['full'].' '.$this->p->cf['version'];
			if ( $this->p->check->pro_active() ) echo ' (Licensed)';
			elseif ( $this->p->is_avail['aop'] ) echo ' (Unlicensed)';
			else echo ' (GPL)';
			echo '" />'."\n";

			/*
			 * Meta Tags for Google
			 */
			$link_rel = array();
			if ( array_key_exists( 'link:publisher', $meta_tags ) ) {
				$link_rel['publisher'] = $meta_tags['link:publisher'];
				unset ( $meta_tags['link:publisher'] );
			} elseif ( ! empty( $this->p->options['link_publisher_url'] ) )
				$link_rel['publisher'] = $this->p->options['link_publisher_url'];

			if ( array_key_exists( 'link:author', $meta_tags ) ) {
				$link_rel['author'] = $meta_tags['link:author'];
				unset ( $meta_tags['link:author'] );
			} else {
				if ( is_singular() ) {
					if ( ! empty( $post ) && $post->post_author )
						$link_rel['author'] = $this->p->user->get_author_url( $post->post_author, 
							$this->p->options['link_author_field'] );
					elseif ( ! empty( $this->p->options['link_def_author_id'] ) )
						$link_rel['author'] = $this->p->user->get_author_url( $this->p->options['link_def_author_id'], 
							$this->p->options['link_author_field'] );
				// check for default author info on indexes and searches
				} elseif ( ( ! is_singular() && ! is_search() && ! empty( $this->p->options['link_def_author_on_index'] ) && ! empty( $this->p->options['link_def_author_id'] ) )
					|| ( is_search() && ! empty( $this->p->options['link_def_author_on_search'] ) && ! empty( $this->p->options['link_def_author_id'] ) ) ) {

					$link_rel['author'] = $this->p->user->get_author_url( $this->p->options['link_def_author_id'], 
						$this->p->options['link_author_field'] );
				}
			}
			$link_rel = apply_filters( $this->p->cf['lca'].'_link_rel', $link_rel );
			foreach ( $link_rel as $key => $val )
				if ( ! empty( $val ) )
					echo '<link rel="', $key, '" href="', $val, '" />', "\n";

			if ( ! empty( $this->p->options['inc_description'] ) ) {
				if ( ! array_key_exists( 'description', $meta_tags ) ) {
					if ( is_singular() && ! empty( $post ) )
						$meta_tags['description'] = $this->p->meta->get_options( $post->ID, 'meta_desc' );
					if ( empty( $meta_tags['description'] ) )
						$meta_tags['description'] = $this->p->webpage->get_description( $this->p->options['meta_desc_len'], '...' );
				}
				if ( ! empty( $meta_tags['description'] ) ) {
					// get_description is already decoded and html clean, so just encode html entities
					$charset = get_bloginfo( 'charset' );
					$meta_tags['description'] = htmlentities( $meta_tags['description'], ENT_QUOTES, $charset, false );
				}
			}
			$meta_tags = apply_filters( $this->p->cf['lca'].'_meta_tags', $meta_tags );

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
			if ( ! empty( $this->p->options['inc_'.$name] ) ) {
				if ( $val != "" || ( ! empty( $this->p->options['og_empty_tags'] ) && strpos( $name, 'og:' ) === 0 ) ) {
					$charset = get_bloginfo( 'charset' );
					$val = htmlentities( $this->p->util->cleanup_html_tags( $this->p->util->decode( $val ) ), 
						ENT_QUOTES, $charset, false );
					$this->p->debug->log( 'meta '.$name.' = "'.$val.'"' );
					if ( $cmt ) $meta_html .= "<!-- $cmt -->";

					// by default, echo a <meta property="" content=""> html tag
					if ( $name == 'description' || strpos( $name, 'twitter:' ) === 0 ) {
						$meta_html .= '<meta name="'.$name.'" content="'.$val.'" />'."\n";
					} elseif ( ( $name == 'og:image' || $name == 'og:video' ) && 
						strpos( $val, 'https:' ) === 0 && ! empty( $this->p->options['inc_'.$name] ) ) {

						$non_sec = preg_replace( '/^https:/', 'http:', $val );
						$meta_html .= '<meta property="'.$name.'" content="'.$non_sec.'" />'."\n";
						// add an additional secure_url meta tag
						if ( $cmt ) $meta_html .= "<!-- $cmt -->";
						$meta_html .= '<meta property="'.$name.':secure_url" content="'.$val.'" />'."\n";
					} else {
						$meta_html .= '<meta property="'.$name.'" content="'.$val.'" />'."\n";
					}
				} else $this->p->debug->log( 'meta '.$name.' is empty - skipping' );
			} else $this->p->debug->log( 'meta '.$name.' is disabled - skipping' );
			return $meta_html;
		}

	}

}
?>
