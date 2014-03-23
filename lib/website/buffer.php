<?php
/*
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Copyright 2012-2014 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbSubmenuSharingBuffer' ) && class_exists( 'NgfbSubmenuSharing' ) ) {

	class NgfbSubmenuSharingBuffer extends NgfbSubmenuSharing {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		protected function get_rows( $metabox, $key ) {
			$ret = array();
			
			$ret[] = $this->p->util->th( 'Show Button in', 'short' ).'<td>'.
			( $this->show_on_checkboxes( 'buffer' ) ).'</td>';

			$ret[] = $this->p->util->th( 'Preferred Order', 'short' ).'<td>'.
			$this->form->get_select( 'buffer_order', 
				range( 1, count( $this->p->admin->submenu['sharing']->website ) ), 
					'short' ).'</td>';

			$ret[] = $this->p->util->th( 'JavaScript in', 'short' ).'<td>'.
			$this->form->get_select( 'buffer_js_loc', $this->js_locations ).'</td>';

			$ret[] = $this->p->util->th( 'Count Position', 'short' ).'<td>'.
			$this->form->get_select( 'buffer_count', array( 'none' => '', 
			'horizontal' => 'Horizontal', 'vertical' => 'Vertical' ) ).'</td>';

			return $ret;
		}
	}
}

if ( ! class_exists( 'NgfbSharingBuffer' ) ) {

	class NgfbSharingBuffer {

		private static $cf = array(
			'opt' => array(				// options
				'defaults' => array(
					'buffer_on_content' => 0,
					'buffer_on_excerpt' => 0,
					'buffer_on_admin_edit' => 1,
					'buffer_on_sidebar' => 0,
					'buffer_order' => 6,
					'buffer_js_loc' => 'header',
					'buffer_count' => 'horizontal',
				),
			),
		);

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 'get_defaults' => 1 ) );
		}

		public function filter_get_defaults( $opts_def ) {
			return array_merge( $opts_def, self::$cf['opt']['defaults'] );
		}

		public function get_html( &$atts = array(), &$opts = array() ) {
			if ( empty( $opts ) ) 
				$opts =& $this->p->options;
			global $post; 
			$prot = empty( $_SERVER['HTTPS'] ) ? 'http:' : 'https:';
			$use_post = array_key_exists( 'use_post', $atts ) ? $atts['use_post'] : true;
			$source_id = $this->p->util->get_source_id( 'twitter', $atts );
			$atts['add_page'] = array_key_exists( 'add_page', $atts ) ? $atts['add_page'] : true;	// get_sharing_url argument
			$long_url = empty( $atts['url'] ) ? 
				$this->p->util->get_sharing_url( $use_post, $atts['add_page'], $source_id ) : 
				apply_filters( $this->p->cf['lca'].'_sharing_url', $atts['url'], 
					$use_post, $atts['add_page'], $source_id );
			$short_url = apply_filters( $this->p->cf['lca'].'_shorten_url', 
				$long_url, $opts['twitter_shortener'] );

			if ( array_key_exists( 'tweet', $atts ) )
				$atts['caption'] = $atts['tweet'];

			if ( ! array_key_exists( 'caption', $atts ) ) {
				if ( ! empty( $post->ID ) && $use_post == true ) 
					$atts['caption'] = $this->p->addons['util']['postmeta']->get_options( $post->ID, 'twitter_desc' );

				if ( empty( $atts['caption'] ) ) {
					$cap_len = $this->p->util->tweet_max_len( $long_url );	// tweet_max_len() shortens -- don't shorten twice
					$atts['caption'] = $this->p->webpage->get_caption( $opts['twitter_caption'], $cap_len, $use_post );
				}
			}

			if ( ! array_key_exists( 'via', $atts ) ) {
				if ( ! empty( $opts['twitter_via'] ) && 
					$this->p->check->is_aop() )
						$atts['via'] = preg_replace( '/^@/', '', $opts['tc_site'] );
				else $atts['via'] = '';
			}

			// hashtags are included in the caption instead
			if ( ! array_key_exists( 'hashtags', $atts ) )
				$atts['hashtags'] = '';

			$html = '<!-- Buffer Button --><div '.$this->p->sharing->get_css( 'buffer', $atts ).'>';
			$html .= '<a href="'.$prot.'//bufferapp.com/add" class="buffer-add-button" ';
			$html .= 'data-url="'.$long_url.'" data-text="'.$atts['caption'].'" ';
			$html .= 'data-via="'.$atts['via'].'" ';
			$html .= 'data-count="'.$opts['buffer_count'].'"></a></div>';
			$this->p->debug->log( 'returning html ('.strlen( $html ).' chars)' );
			return $html;
		}
		
		public function get_js( $pos = 'id' ) {
			$this->p->debug->mark();
			$prot = empty( $_SERVER['HTTPS'] ) ? 'http:' : 'https:';
			$js_url = $this->p->util->get_cache_url( $prot.'//d389zggrogs7qo.cloudfront.net/js/button.js' );
			return '<script type="text/javascript" id="buffer-script-'.$pos.'">'.$this->p->cf['lca'].'_insert_js( "buffer-script-'.$pos.'", "'.$js_url.'" );</script>';
		}
	}
}

?>
