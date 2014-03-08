<?php
/*
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Copyright 2012-2014 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbSubmenuSharingManagewp' ) && class_exists( 'NgfbSubmenuSharing' ) ) {

	class NgfbSubmenuSharingManagewp extends NgfbSubmenuSharing {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		protected function get_rows( $metabox, $key ) {
			return array(
				$this->p->util->th( 'Show Button in', 'short' ) . '<td>' . 
				( $this->show_on_checkboxes( 'managewp' ) ).'</td>',

				$this->p->util->th( 'Preferred Order', 'short' ) . '<td>' . 
				$this->form->get_select( 'managewp_order', 
					range( 1, count( $this->p->admin->submenu['sharing']->website ) ), 
						'short' ) . '</td>',

				$this->p->util->th( 'JavaScript in', 'short' ) . '<td>' . 
				$this->form->get_select( 'managewp_js_loc', $this->js_locations ) . '</td>',

				$this->p->util->th( 'Button Type', 'short' ) . '<td>' . 
				$this->form->get_select( 'managewp_counter', 
					array( 
						'small' => 'Small',
						'big' => 'Big',
					)
				) . '</td>',
			);
		}
	}
}

if ( ! class_exists( 'NgfbSharingManagewp' ) && class_exists( 'NgfbSharing' ) ) {

	class NgfbSharingManagewp {

		private static $cf = array(
			'opt' => array(				// options
				'defaults' => array(
					'managewp_on_content' => 0,
					'managewp_on_excerpt' => 0,
					'managewp_on_admin_edit' => 1,
					'managewp_on_sidebar' => 0,
					'managewp_order' => 5,
					'managewp_js_loc' => 'header',
					'managewp_type' => 'small',
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
			$use_post = array_key_exists( 'use_post', $atts ) ? $atts['use_post'] : true;
			$source_id = $this->p->util->get_source_id( 'managewp', $atts );
			$atts['add_page'] = array_key_exists( 'add_page', $atts ) ? $atts['add_page'] : true;	// get_sharing_url argument
			$atts['url'] = empty( $atts['url'] ) ? 
				$this->p->util->get_sharing_url( $use_post, $atts['add_page'], $source_id ) : 
				apply_filters( $this->p->cf['lca'].'_sharing_url', $atts['url'], 
					$use_post, $atts['add_page'], $source_id );
			$js_url = $this->p->util->get_cache_url( 'http://managewp.org/share.js' ).'#http://managewp.org/share';

			if ( empty( $atts['title'] ) ) 
				$atts['title'] = $this->p->webpage->get_title( null, null, $use_post);

			$html = '<!-- ManageWP Button --><div '.$this->p->sharing->get_css( 'managewp', $atts ).'>';
			$html .= '<script src="'.$js_url.'" data-url="'.$atts['url'].'" data-title="'.$atts['title'].'"';
			$html .= empty( $opts['managewp_type'] ) ? '' : ' data-type="'.$opts['managewp_type'].'"';
			$html .= '></script></div>';
			$this->p->debug->log( 'returning html ('.strlen( $html ).' chars)' );
			return $html;
		}
	}
}

?>
