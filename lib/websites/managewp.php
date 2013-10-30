<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'ngfbSettingsManageWP' ) && class_exists( 'ngfbSettingsSocialSharing' ) ) {

	class ngfbSettingsManageWP extends ngfbSettingsSocialSharing {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get_rows() {
			return array(
				$this->p->util->th( 'Show Button in', 'short' ) . '<td>' . 
				'Content '.$this->p->admin->form->get_checkbox( 'managewp_on_the_content' ).'&nbsp;'.
				'Excerpt '.$this->p->admin->form->get_checkbox( 'managewp_on_the_excerpt' ).'&nbsp;'.
				'Edit Post/Page '.$this->p->admin->form->get_checkbox( 'managewp_on_admin_sharing' ). 
				'</td>',

				$this->p->util->th( 'Preferred Order', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'managewp_order', 
					range( 1, count( $this->p->admin->settings['social']->website ) ), 'short' ) . '</td>',

				$this->p->util->th( 'JavaScript in', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'managewp_js_loc', $this->js_locations ) . '</td>',

				$this->p->util->th( 'Button Type', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'managewp_counter', 
					array( 
						'small' => 'Small',
						'big' => 'Big',
					)
				) . '</td>',
			);
		}
	}
}

if ( ! class_exists( 'ngfbSocialManageWP' ) && class_exists( 'ngfbSocial' ) ) {

	class ngfbSocialManageWP {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get_html( $atts = array(), $opts = array() ) {
			$this->p->debug->mark();
			if ( empty( $opts ) ) 
				$opts = $this->p->options;
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			$src_id = $this->p->util->get_src_id( 'managewp', $atts );
			$js_url = $this->p->util->get_cache_url( 'http://managewp.org/share.js' );

			$atts['url'] = empty( $atts['url'] ) ? 
				$this->p->util->get_sharing_url( 'notrack', null, $use_post, $src_id ) : 
				$this->p->util->get_sharing_url( 'asis', $atts['url'], null, $src_id );

			if ( empty( $atts['title'] ) ) 
				$atts['title'] = $this->p->webpage->get_title( null, null, $use_post);

			$html = '<!-- ManageWP Button --><div '.$this->p->social->get_css( 'managewp', $atts ).'>';
			$html .= '<script src="'.$js_url.'"';
			$html .= ' data-url="'.$atts['url'].'"';
			$html .= ' data-title="'.$atts['title'].'"';

			if ( ! empty( $opts['managewp_type'] ) ) 
				$html .= ' data-type="'.$opts['managewp_type'].'"';

			$html .= '></script></div>';
			$this->p->debug->log( 'returning html ('.strlen( $html ).' chars)' );
			return $html;
		}
	}
}
?>
