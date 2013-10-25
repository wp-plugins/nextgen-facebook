<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'ngfbSettingsGooglePlus' ) && class_exists( 'ngfbSettingsSocialSharing' ) ) {

	class ngfbSettingsGooglePlus extends ngfbSettingsSocialSharing {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get_rows() {
			return array(
				$this->p->util->th( 'Show Button in', 'short' ) . '<td>' . 
				'Content '.$this->p->admin->form->get_checkbox( 'gp_on_the_content' ).'&nbsp;'.
				'Excerpt '.$this->p->admin->form->get_checkbox( 'gp_on_the_excerpt' ).'&nbsp;'.
				'Edit Post/Page '.$this->p->admin->form->get_checkbox( 'gp_on_admin_sharing' ). 
				'</td>',

				$this->p->util->th( 'Preferred Order', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'gp_order', 
					range( 1, count( $this->p->admin->settings['social']->website ) ), 'short' ) . '</td>',

				$this->p->util->th( 'JavaScript in', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'gp_js_loc', $this->js_locations ) . '</td>',

				$this->p->util->th( 'Default Language', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'gp_lang', $this->p->util->get_lang( 'gplus' ) ) . '</td>',

				$this->p->util->th( 'Button Type', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'gp_action', 
					array( 
						'plusone' => 'G +1', 
						'share' => 'G+ Share',
					) 
				) . '</td>',

				$this->p->util->th( 'Button Size', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'gp_size', 
					array( 
						'small' => 'Small [ 15px ]',
						'medium' => 'Medium [ 20px ]',
						'standard' => 'Standard [ 24px ]',
						'tall' => 'Tall [ 60px ]',
					) 
				) . '</td>',

				$this->p->util->th( 'Annotation', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'gp_annotation', 
					array( 
						'none' => '',
						'inline' => 'Inline',
						'bubble' => 'Bubble',
						'vertical-bubble' => 'Vertical Bubble',
					)
				) . '</td>',

				$this->p->util->th( 'Expand to', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'gp_expandto', 
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

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get_html( $atts = array(), $opts = array() ) {
			$this->p->debug->mark();
			if ( empty( $opts ) ) $opts = $this->p->options;
			global $post; 
			$html = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			$src_id = $this->p->util->get_src_id( 'gplus', $atts );
			$atts['url'] = empty( $atts['url'] ) ? 
				$this->p->util->get_sharing_url( 'notrack', null, $use_post, $src_id ) : 
				$this->p->util->get_sharing_url( 'asis', $atts['url'], null, $src_id );
			$gp_class = $opts['gp_action'] == 'share' ? 'class="g-plus" data-action="share"' : 'class="g-plusone"';
			$html = '<!-- GooglePlus Button --><div '.$this->p->social->get_css( 'gplus', $atts, 'g-plusone-button' ).'><span '.$gp_class;
			$html .= ' data-size="'.$opts['gp_size'].'" data-annotation="'.$opts['gp_annotation'].'" data-href="'.$atts['url'].'"';
			$html .= empty( $opts['gp_expandto'] ) || $opts['gp_expandto'] == 'none' ? '' : ' data-expandTo="'.$opts['gp_expandto'].'"';
			$html .= '></span></div>'."\n";
			$this->p->debug->log( 'returning html ('.strlen( $html ).' chars)' );
			return $html;
		}
		
		public function get_js( $pos = 'id' ) {
			$this->p->debug->mark();
			$prot = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
			return '<script type="text/javascript" id="gplus-script-'.$pos.'">'.$this->p->acronym.'_insert_js( "gplus-script-'.$pos.'", "'.$this->p->util->get_cache_url( $prot.'apis.google.com/js/plusone.js' ).'" );</script>'."\n";
		}
		
	}

}
?>
