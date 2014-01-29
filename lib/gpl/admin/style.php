<?php
/*
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Copyright 2012-2014 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbAdminStyle' ) ) {

	class NgfbAdminStyle {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'style_sharing_rows' => 2,
				'style_excerpt_rows' => 2,
				'style_content_rows' => 2,
				'style_sidebar_rows' => 2,
				'style_shortcode_rows' => 2,
				'style_widget_rows' => 2,
				'style_admin_edit_rows' => 2,
			) );
		}

		private function filter_style_common_rows( &$rows, &$form, $idx ) {
			$rows[] = '<td colspan="2" align="center">'.$this->p->msgs->get( 'pro-feature-msg' ).'</td>';
			$rows[] = '<td class="textinfo">'.$this->p->msgs->get( 'style-'.$idx.'-info' ).'</td>'.
			'<td class="blank large code">'.$form->get_hidden( 'buttons_css_'.$idx ).
				$this->p->options['buttons_css_'.$idx].'</td>';
			return $rows;
		}

		public function filter_style_sharing_rows( $rows, $form ) {
			return $this->filter_style_common_rows( $rows, $form, 'sharing' );
		}

		public function filter_style_excerpt_rows( $rows, $form ) {
			return $this->filter_style_common_rows( $rows, $form, 'excerpt' );
		}

		public function filter_style_content_rows( $rows, $form ) {
			return $this->filter_style_common_rows( $rows, $form, 'content' );
		}

		public function filter_style_sidebar_rows( $rows, $form ) {
			$rows = array_merge( $rows, $this->filter_style_common_rows( $rows, $form, 'sidebar' ) );
			$rows[] = $this->p->util->th( 'Sidebar Javascript', null, 'buttons_js_sidebar' ).
			'<td class="blank medium code">'.$form->get_hidden( 'buttons_js_sidebar' ).
				$this->p->options['buttons_js_sidebar'].'</td>';
			return $rows;
		}

		public function filter_style_shortcode_rows( $rows, $form ) {
			return $this->filter_style_common_rows( $rows, $form, 'shortcode' );
		}

		public function filter_style_widget_rows( $rows, $form ) {
			return $this->filter_style_common_rows( $rows, $form, 'widget' );
		}

		public function filter_style_admin_edit_rows( $rows, $form ) {
			return $this->filter_style_common_rows( $rows, $form, 'admin_edit' );
		}
	}
}

?>
