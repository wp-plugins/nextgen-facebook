<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbAdminSocialLinkedin' ) && class_exists( 'NgfbAdminSocial' ) ) {

	class NgfbAdminSocialLinkedin extends NgfbAdminSocial {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get_rows() {
			return array(
				$this->p->util->th( 'Show Button in', 'short' ) . '<td>' . 
				'Content '.$this->p->admin->form->get_checkbox( 'linkedin_on_the_content' ).'&nbsp;'.
				'Excerpt '.$this->p->admin->form->get_checkbox( 'linkedin_on_the_excerpt' ).'&nbsp;'.
				'Edit Post/Page '.$this->p->admin->form->get_checkbox( 'linkedin_on_admin_sharing' ). 
				'</td>',

				$this->p->util->th( 'Preferred Order', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'linkedin_order', 
					range( 1, count( $this->p->admin->settings['social']->website ) ), 'short' ) . '</td>',

				$this->p->util->th( 'JavaScript in', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'linkedin_js_loc', $this->js_locations ) . '</td>',

				$this->p->util->th( 'Counter Mode', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'linkedin_counter', 
					array( 
						'none' => '',
						'right' => 'Horizontal',
						'top' => 'Vertical',
					)
				) . '</td>',

				$this->p->util->th( 'Zero in Counter', 'short' ) . '<td>' . 
				$this->p->admin->form->get_checkbox( 'linkedin_showzero' ) . '</td>',
			);
		}

	}
}

if ( ! class_exists( 'NgfbSocialLinkedin' ) && class_exists( 'NgfbSocial' ) ) {

	class NgfbSocialLinkedin {

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
			$src_id = $this->p->util->get_src_id( 'linkedin', $atts );
			$atts['url'] = empty( $atts['url'] ) ? 
				$this->p->util->get_sharing_url( 'notrack', null, $use_post, $src_id ) : 
				$this->p->util->get_sharing_url( 'asis', $atts['url'], null, $src_id );
			$html = '<!-- LinkedIn Button --><div '.$this->p->social->get_css( 'linkedin', $atts ).'><script type="IN/Share" data-url="'.$atts['url'].'"';

			if ( ! empty( $opts['linkedin_counter'] ) ) 
				$html .= ' data-counter="'.$opts['linkedin_counter'].'"';

			if ( ! empty( $opts['linkedin_showzero'] ) ) 
				$html .= ' data-showzero="true"';

			$html .= '></script></div>';
			$this->p->debug->log( 'returning html ('.strlen( $html ).' chars)' );
			return $html;
		}
		
		public function get_js( $pos = 'id' ) {
			$this->p->debug->mark();
			$prot = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
			$js_url = $this->p->util->get_cache_url( $prot.'platform.linkedin.com/in.js' );

			return  '<script type="text/javascript" id="linkedin-script-'.$pos.'">'.$this->p->cf['lca'].'_insert_js( "linkedin-script-'.$pos.'", "'.$js_url.'" );</script>';
		}

	}

}
?>
