<?php
/*
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Copyright 2012-2014 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbAdminSharing' ) ) {

	class NgfbAdminSharing {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
			$this->p->util->add_plugin_filters( $this, array( 
				'plugin_cache_rows' => 2,
				'sharing_buttons_rows' => 2,
			) );
		}

		public function filter_plugin_cache_rows( $rows, $form ) {
			$rows[] = '<td colspan="2" align="center">'.$this->p->msgs->get( 'pro-feature-msg' ).'</td>';

			$rows[] = $this->p->util->th( 'Social File Cache Expiry', 'highlight', 'plugin_file_cache_hrs' ).
			'<td class="blank">'.$form->get_hidden( 'plugin_file_cache_hrs' ). 
			$this->p->options['plugin_file_cache_hrs'].' hours</td>';

			$rows[] = $this->p->util->th( 'Verify SSL Certificates', null, 'plugin_verify_certs' ).
			'<td class="blank">'.$form->get_fake_checkbox( 'plugin_verify_certs' ).'</td>';

			return $rows;
		}

		public function filter_sharing_buttons_rows( $rows, $form ) {
			$checkboxes = '';
			foreach ( $this->p->util->get_post_types( 'buttons' ) as $post_type )
				$checkboxes .= '<p>'.$form->get_fake_checkbox( 'buttons_add_to_'.$post_type->name ).' '.
					$post_type->label.' '.( empty( $post_type->description ) ? '' : '('.$post_type->description.')' ).'</p>';

			$rows[] = '<td colspan="2" align="center">'.$this->p->msgs->get( 'pro-feature-msg' ).'</td>';

			$rows[] = $this->p->util->th( 'Include on Post Types', null, 'buttons_add_to' ).
			'<td class="blank">'.$checkboxes.'</td>';

			return $rows;
		}
	}
}

?>
