<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbHead' ) ) {

	class ngfbHead {

		public $og;

		private $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
			$this->og = new ngfbOpenGraph( $ngfb_plugin );

			add_action( 'wp_head', array( &$this, 'add_header' ), NGFB_HEAD_PRIORITY );
		}

		// called by WP wp_head action
		public function add_header() {

			if ( $this->ngfb->debug->is_on() ) {
				$this->ngfb->debug->log( 'is_archive() = ' . ( is_archive() ? 'true' : 'false' ) );
				$this->ngfb->debug->log( 'is_attachment() = ' . ( is_attachment() ? 'true' : 'false' ) );
				$this->ngfb->debug->log( 'is_category() = ' . ( is_category() ? 'true' : 'false' ) );
				$this->ngfb->debug->log( 'is_home() = ' . ( is_home() ? 'true' : 'false' ) );
				$this->ngfb->debug->log( 'is_search() = ' . ( is_search() ? 'true' : 'false' ) );
				$this->ngfb->debug->log( 'is_singular() = ' . ( is_singular() ? 'true' : 'false' ) );
			}

			$this->html( $this->og->get() );

			if ( $this->ngfb->debug->is_on() ) {
				//$defined_constants = get_defined_constants( true );
				//$this->ngfb->debug->show_html( $this->ngfb->util->preg_grep_keys( '/^NGFB_/', $defined_constants['user'] ), 'NGFB Constants' );

				$opts = $this->ngfb->options;
				foreach ( array( 'ngfb_pro_tid', 'ngfb_googl_api_key', ) as $key ) $opts[$key] = '********';

				$this->ngfb->debug->show_html( $this->ngfb->is_avail, 'Available Features' );
				$this->ngfb->debug->show_html( null, 'Debug Log' );
				$this->ngfb->debug->show_html( $opts, 'NGFB Settings' );
			}
		}

		// called from the work/header.php template
		public function html( $arr = array() ) {
			global $post;
			$author_url = '';
		
			echo "\n<!-- ", $this->ngfb->fullname, " meta tags BEGIN -->\n";

			// show the array structure before the html block
			$this->ngfb->debug->show_html( print_r( $arr, true ), 'Open Graph Array' );
			$this->ngfb->debug->show_html( print_r( $this->ngfb->util->get_urls_found(), true ), 'URLs Found' );

			echo '<meta name="generator" content="', $this->ngfb->fullname, ' ', $this->ngfb->version, '" />', "\n";

			/*
			 * Meta Tags for Google
			 */
			if ( ! empty( $arr['link:publisher'] ) )
				echo '<link rel="publisher" href="', $arr['link:publisher'], '" />', "\n";
			elseif ( ! empty( $this->ngfb->options['link_publisher_url'] ) )
				echo '<link rel="publisher" href="', $this->ngfb->options['link_publisher_url'], '" />', "\n";

			if ( ! empty( $arr['link:author'] ) )
				echo '<link rel="author" href="', $arr['link:author'], '" />', "\n";
			else {
				if ( is_singular() ) {

					if ( ! empty( $post ) && $post->post_author )
						$author_url = $this->ngfb->user->get_author_url( $post->post_author, 
							$this->ngfb->options['link_author_field'] );

					elseif ( ! empty( $this->ngfb->options['link_def_author_id'] ) )
						$author_url = $this->ngfb->user->get_author_url( $this->ngfb->options['link_def_author_id'], 
							$this->ngfb->options['link_author_field'] );

				// check for default author info on indexes and searches
				} elseif ( ( ! is_singular() && ! is_search() && ! empty( $this->ngfb->options['link_def_author_on_index'] ) && ! empty( $this->ngfb->options['link_def_author_id'] ) )
					|| ( is_search() && ! empty( $this->ngfb->options['link_def_author_on_search'] ) && ! empty( $this->ngfb->options['link_def_author_id'] ) ) ) {

					$author_url = $this->ngfb->user->get_author_url( $this->ngfb->options['link_def_author_id'], 
						$this->ngfb->options['link_author_field'] );
				}
				if ( $author_url ) 
					echo '<link rel="author" href="', $author_url, '" />', "\n";
			}

			if ( ! empty( $this->ngfb->options['inc_description'] ) ) {
				if ( is_singular() && ! empty( $post ) )
					$link_desc = $this->ngfb->meta->get_options( $post->ID, 'link_desc' );
				if ( empty( $link_desc ) )
					$link_desc = $this->ngfb->webpage->get_description( $this->ngfb->options['link_desc_len'], '...' );
				if ( ! empty( $link_desc ) ) 
					echo '<meta name="description" content="', $link_desc, '" />', "\n";
			}

			/*
			 * Print the Multi-Dimensional Array as HTML
			 */
			foreach ( $arr as $d_name => $d_val ) {						// first-dimension array (associative)
				if ( is_array( $d_val ) ) {
					foreach ( $d_val as $dd_num => $dd_val ) {			// second-dimension array
						if ( $this->ngfb->util->is_assoc( $dd_val ) ) {
							ksort( $dd_val );
							foreach ( $dd_val as $ddd_name => $ddd_val ) {	// third-dimension array (associative)
								echo $this->get_meta_html( $ddd_name, $ddd_val, $d_name . ':' . ( $dd_num + 1 ) );
							}
							unset ( $ddd_name, $ddd_val );
						} else echo $this->get_meta_html( $d_name, $dd_val, $d_name . ':' . ( $dd_num + 1 ) );
					}
					unset ( $dd_num, $dd_val );
				} else echo $this->get_meta_html( $d_name, $d_val );
			}
			unset ( $d_name, $d_val );

			echo "<!-- ", $this->ngfb->fullname, " meta tags END -->\n";
		}

		private function get_meta_html( $name, $val = '', $cmt = '' ) {
			$meta_html = '';
			if ( ! empty( $this->ngfb->options['inc_' . $name] ) && 
				( ! empty( $val ) || ( ! empty( $this->ngfb->options['og_empty_tags'] ) && strpos( $name, 'og:' ) === 0 ) ) ) {

				$charset = get_bloginfo( 'charset' );
				$val = htmlentities( $this->ngfb->util->cleanup_html_tags( $this->ngfb->util->decode( $val ) ), 
					ENT_QUOTES, $charset, false );
				if ( $cmt ) $meta_html .= "<!-- $cmt -->";

				if ( strpos( $name, 'twitter:' ) === 0 )
					$meta_html .= '<meta name="' . $name . '" content="' . $val . '" />' . "\n";
				else
					$meta_html .= '<meta property="' . $name . '" content="' . $val . '" />' . "\n";
			}
			return $meta_html;
		}

	}

}
?>
