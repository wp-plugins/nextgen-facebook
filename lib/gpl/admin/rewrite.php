<?php
/*
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Copyright 2012-2014 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbAdminRewrite' ) ) {

	class NgfbAdminRewrite {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
			$this->p->util->add_plugin_filters( $this, array( 
				'plugin_tabs' => 1,
				'plugin_rewrite_rows' => 2,
			) );
		}

		public function filter_plugin_tabs( $tabs ) {
			$tabs['rewrite'] = 'URL Rewrite';
			return $tabs;
		}

		public function filter_plugin_rewrite_rows( $rows, $form ) {
			$rows[] = '<td colspan="2" align="center">'.$this->p->msgs->get( 'pro-feature-msg' ).'</td>';

			$rows[] = $this->p->util->th( 'URL Length to Shorten', null, 'plugin_min_shorten' ). 
			'<td class="blank">'.$form->get_hidden( 'plugin_min_shorten' ).
				$this->p->options['plugin_min_shorten'].' characters</td>';

			$rows[] = $this->p->util->th( 'Static Content URL(s)', 'highlight', 'plugin_cdn_urls' ). 
			'<td class="blank">'.$form->get_hidden( 'plugin_cdn_urls' ). 
				$this->p->options['plugin_cdn_urls'].'</td>';

			$rows[] = $this->p->util->th( 'Include Folders', null, null, 'plugin_cdn_folders' ).
			'<td class="blank">'.$form->get_hidden( 'plugin_cdn_folders' ). 
				$this->p->options['plugin_cdn_folders'].'</td>';

			$rows[] = $this->p->util->th( 'Exclude Patterns', null, 'plugin_cdn_excl' ).
			'<td class="blank">'.$form->get_hidden( 'plugin_cdn_excl' ).
				$this->p->options['plugin_cdn_excl'].'</td>';

			$rows[] = $this->p->util->th( 'Not when Using HTTPS', null, 'plugin_cdn_not_https' ).
			'<td class="blank">'.$form->get_fake_checkbox( 'plugin_cdn_not_https' ).'</td>';

			$rows[] = $this->p->util->th( 'www is Optional', null, 'plugin_cdn_www_opt' ). 
			'<td class="blank">'.$form->get_fake_checkbox( 'plugin_cdn_www_opt' ).'</td>';

			return $rows;
		}
	}
}

?>
