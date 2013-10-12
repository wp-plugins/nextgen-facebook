<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbSettingsGooglePlus' ) && class_exists( 'ngfbSettingsSocialSharing' ) ) {

	class ngfbSettingsGooglePlus extends ngfbSettingsSocialSharing {

		protected $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
		}

		public function get_rows() {
			return array(
				$this->ngfb->util->th( 'Show Button in', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_checkbox( 'gp_on_the_content' ) . ' Content&nbsp; ' . 
				$this->ngfb->admin->form->get_checkbox( 'gp_on_the_excerpt' ) . ' Excerpt&nbsp; ' . 
				$this->ngfb->admin->form->get_checkbox( 'gp_on_admin_sharing' ) . ' Admin Sharing' . 
				'</td>',

				$this->ngfb->util->th( 'Preferred Order', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_select( 'gp_order', 
					range( 1, count( $this->ngfb->admin->settings['social']->website ) ), 'short' ) . '</td>',

				$this->ngfb->util->th( 'JavaScript in', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_select( 'gp_js_loc', $this->js_locations ) . '</td>',

				$this->ngfb->util->th( 'Default Language', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_select( 'gp_lang', $this->ngfb->util->get_lang( 'gplus' ) ) . '</td>',

				$this->ngfb->util->th( 'Button Type', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_select( 'gp_action', 
					array( 
						'plusone' => 'G +1', 
						'share' => 'G+ Share',
					) 
				) . '</td>',

				$this->ngfb->util->th( 'Button Size', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_select( 'gp_size', 
					array( 
						'small' => 'Small [ 15px ]',
						'medium' => 'Medium [ 20px ]',
						'standard' => 'Standard [ 24px ]',
						'tall' => 'Tall [ 60px ]',
					) 
				) . '</td>',

				$this->ngfb->util->th( 'Annotation', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_select( 'gp_annotation', 
					array( 
						'none' => '',
						'inline' => 'Inline',
						'bubble' => 'Bubble',
						'vertical-bubble' => 'Vertical Bubble',
					)
				) . '</td>',

				$this->ngfb->util->th( 'Expand to', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_select( 'gp_expandto', 
					array( 
						'none' => '',
						'top' => 'Top',
						'bottom' => 'Bottom',
						'left' => 'Left',
						'right' => 'Right',
						'top,left' => 'Top Left',
						'top,right' => 'Top Right',
						'bottom,left' => 'Bottom Left',
						'bottom,right' => 'Bottom Right',
					)
				) . '</td>',
			);
		}

	}
}

if ( ! class_exists( 'ngfbSocialGooglePlus' ) && class_exists( 'ngfbSocial' ) ) {

	class ngfbSocialGooglePlus {

		protected $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
		}

		public function get_html( $atts = array(), $opts = array() ) {
			if ( empty( $opts ) ) $opts = $this->ngfb->options;
			global $post; 
			$html = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			$src_id = $this->ngfb->util->get_src_id( 'gplus', $atts );
			$atts['url'] = empty( $atts['url'] ) ? 
				$this->ngfb->util->get_sharing_url( 'notrack', null, $use_post, $src_id ) : 
				$this->ngfb->util->get_sharing_url( 'asis', $atts['url'], null, $src_id );
			$gp_class = $opts['gp_action'] == 'share' ? 'class="g-plus" data-action="share"' : 'class="g-plusone"';
			$html = '<!-- GooglePlus Button --><div '.$this->ngfb->social->get_css( 'gplus', $atts, 'g-plusone-button' ).'><span '.$gp_class;
			$html .= ' data-size="'.$opts['gp_size'].'" data-annotation="'.$opts['gp_annotation'].'" data-href="'.$atts['url'].'"';
			$html .= empty( $opts['gp_expandto'] ) ? '' : ' data-expandTo="'.$opts['gp_expandto'].'"';
			$html .= '></span></div>'."\n";
			$this->ngfb->debug->log( 'returning html ('.strlen( $html ).' chars)' );
			return $html;
		}
		
		public function get_js( $pos = 'id' ) {
			$prot = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
			return '<script type="text/javascript" id="gplus-script-'.$pos.'">ngfb_header_js( "gplus-script-'.$pos.'", "'.$this->ngfb->util->get_cache_url( $prot.'apis.google.com/js/plusone.js' ).'" );</script>'."\n";
		}
		
	}

}
?>
