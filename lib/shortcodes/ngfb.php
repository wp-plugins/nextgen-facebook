<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbShortCodeNgfb' ) ) {

	class ngfbShortCodeNgfb {

		private $p;
		private $name = 'ngfb';

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
			$this->wpautop();
			$this->add();
		}

		public function wpautop() {
			// make sure wpautop() does not have a higher priority than 10, otherwise it will 
			// format the shortcode output (shortcodes filters are run at priority 11).
			if ( ! empty( $this->p->options[$this->name . '_enable_shortcode'] ) ) {
				$default_priority = 10;
				foreach ( array( 'the_excerpt', 'the_content' ) as $tag ) {
					$filter_priority = has_filter( $tag, 'wpautop' );
					if ( $filter_priority > $default_priority ) {
						remove_filter( 'the_content', 'wpautop' );
						add_filter( 'the_content', 'wpautop' , $default_priority );
						$this->p->debug->log( 'wpautop() priority changed from '.$filter_priority.' to '.$default_priority );
					}
				}
			}
		}

		public function add() {
			if ( ! empty( $this->p->options[$this->name . '_enable_shortcode'] ) ) {
        			add_shortcode( $this->name, array( &$this, 'shortcode' ) );
				$this->p->debug->log( '[' . $this->name . '] shortcode added' );
			}
		}

		public function remove() {
			if ( ! empty( $this->p->options[$this->name . '_enable_shortcode'] ) ) {
				remove_shortcode( $this->name );
				$this->p->debug->log( '[' . $this->name . '] shortcode removed' );
			}
		}

		public function shortcode( $atts, $content = null ) { 
			$atts = apply_filters( 'ngfb_shortcode', $atts, $content );
			global $post;
			$html = '';
			$atts['url'] = empty( $atts['url'] ) ? $this->p->util->get_sharing_url( 'notrack', null, true ) : $atts['url'];
			$atts['css_id'] = empty( $atts['css_id'] ) && ! empty( $post->ID ) ? 'shortcode' : $atts['css_id'];
			$atts['css_class'] = empty( $atts['css_class'] ) ? 'button' : $atts['css_class'];
			if ( ! empty( $atts['buttons'] ) ) {
				$atts['css_id'] .= '-buttons';
				$keys = implode( '|', array_keys( $atts ) );
				$vals = preg_replace( '/[, ]+/', '_', implode( '|', array_values( $atts ) ) );
				$cache_salt = __METHOD__.'(lang:'.get_locale().'_post:'.$post->ID.'_keys:'.$keys. '_vals:'.$vals.')';
				$cache_id = 'ngfb_' . md5( $cache_salt );
				$cache_type = 'object cache';
				$html = get_transient( $cache_id );
				$this->p->debug->log( $cache_type . ': shortcode transient id salt "' . $cache_salt . '"' );
				if ( $html !== false ) {
					$this->p->debug->log( $cache_type . ': html retrieved from transient for id "' . $cache_id . '"' );
				} else {
					if ( ! empty( $atts['buttons'] ) && $this->p->social->is_disabled() == false ) {
						$ids = array_map( 'trim', explode( ',', $atts['buttons'] ) );
						unset ( $atts['buttons'] );
						$html .= '<!-- '.$this->p->fullname.' '.$atts['css_id'].' BEGIN -->'.
							$this->p->social->get_js( 'pre-shortcode', $ids ).
							'<div class="'.$this->p->acronym.'-'.$atts['css_id'].'">'.
								$this->p->social->get_html( $ids, $atts ).'</div>'.
							$this->p->social->get_js( 'post-shortcode', $ids ).
							'<!-- '.$this->p->fullname.' '.$atts['css_id'].' END -->';
					}
					set_transient( $cache_id, $html, $this->p->cache->object_expire );
					$this->p->debug->log( $cache_type . ': html saved to transient for id "' . 
						$cache_id . '" (' . $this->p->cache->object_expire . ' seconds)');
				}
			}
			return $this->p->debug->get_html() . $html;
		}
	}
}

?>
