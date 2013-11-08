<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbAdminSocialStumbleupon' ) && class_exists( 'NgfbAdminSocial' ) ) {

	class NgfbAdminSocialStumbleupon extends NgfbAdminSocial {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get_rows() {
			$prot = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
			$badge = '
				<style type="text/css">
					.badge { 
						display:block;
						background: url("'.$this->p->util->get_cache_url( 
							$prot.'b9.sustatic.com/7ca234_0mUVfxHFR0NAk1g' ).'") no-repeat transparent; 
						width:110px;
						margin:5px 0 5px 0;
					}
					.badge input[type=radio] {
					}
					.badge-col-left { display:inline-block; float:left; margin-right:20px; }
					.badge-col-right { display:inline-block; }
					#badge-1 { height:20px; background-position:25px 0px; }
					#badge-2 { height:20px; background-position:25px -100px; }
					#badge-3 { height:20px; background-position:25px -200px; }
					#badge-4 { height:60px; background-position:25px -300px; }
					#badge-5 { height:30px; background-position:25px -400px; }
					#badge-6 { height:20px; background-position:25px -500px; }
				</style>
			';

			$badge .= '<div class="badge-col-left">';
			foreach ( array( 1, 2, 3, 6 ) as $i ) {
				$badge .= '<div class="badge" id="badge-'.$i.'">';
				$badge .= '<input type="radio" name="'.$this->p->admin->form->options_name.'[stumble_badge]" 
					value="'.$i.'" '.checked( $i, $this->p->options['stumble_badge'], false ).'/>';
				$badge .= '</div>';
			}
			$badge .= '</div><div class="badge-col-right">';
			foreach ( array( 4, 5 ) as $i ) {
				$badge .= '<div class="badge" id="badge-'.$i.'">';
				$badge .= '<input type="radio" name="'.$this->p->admin->form->options_name.'[stumble_badge]" 
					value="'.$i.'" '.checked( $i, $this->p->options['stumble_badge'], false ).'/>';
				$badge .= '</div>';
			}
			$badge .= '</div>';

			return array(
				$this->p->util->th( 'Show Button in', 'short' ).'<td>'.
				'Content '.$this->p->admin->form->get_checkbox( 'stumble_on_the_content' ).'&nbsp;'.
				'Excerpt '.$this->p->admin->form->get_checkbox( 'stumble_on_the_excerpt' ).'&nbsp;'.
				'Edit Post/Page '.$this->p->admin->form->get_checkbox( 'stumble_on_admin_sharing' ). 
				'</td>',

				$this->p->util->th( 'Preferred Order', 'short' ).'<td>'.
				$this->p->admin->form->get_select( 'stumble_order', 
					range( 1, count( $this->p->admin->settings['social']->website ) ), 'short' ).'</td>',

				$this->p->util->th( 'JavaScript in', 'short' ).'<td>'.
				$this->p->admin->form->get_select( 'stumble_js_loc', $this->js_locations ).'</td>',

				$this->p->util->th( 'Button Style', 'short' ).'<td>'.$badge.'</td>',
			);
		}

	}
}

if ( ! class_exists( 'NgfbSocialStumbleupon' ) && class_exists( 'NgfbSocial' ) ) {

	class NgfbSocialStumbleupon {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get_html( $atts = array(), $opts = array() ) {
			$this->p->debug->mark();
			if ( empty( $opts ) ) 
				$opts = $this->p->options;
			$use_post = empty( $atts['is_widget'] ) || is_singular() || is_admin() ? true : false;
			$src_id = $this->p->util->get_src_id( 'stumbleupon', $atts );
			$atts['url'] = empty( $atts['url'] ) ? 
				$this->p->util->get_sharing_url( 'notrack', null, $use_post, $src_id ) : 
				$this->p->util->get_sharing_url( 'asis', $atts['url'], null, $src_id );
			if ( empty( $atts['stumble_badge'] ) ) $atts['stumble_badge'] = $opts['stumble_badge'];
			$html = '<!-- StumbleUpon Button --><div '.$this->p->social->get_css( 'stumbleupon', $atts, 'stumble-button' ).'><su:badge layout="'.$atts['stumble_badge'].'" location="'.$atts['url'].'"></su:badge></div>';
			$this->p->debug->log( 'returning html ('.strlen( $html ).' chars)' );
			return $html;
		}

		public function get_js( $pos = 'id' ) {
			$this->p->debug->mark();
			$prot = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
			$js_url = $this->p->util->get_cache_url( $prot.'platform.stumbleupon.com/1/widgets.js' );

			return '<script type="text/javascript" id="stumbleupon-script-'.$pos.'">'.$this->p->cf['lca'].'_insert_js( "stumbleupon-script-'.$pos.'", "'.$js_url.'" );</script>';
		}
	}
}

?>
