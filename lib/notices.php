<?php
/*
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/

This script is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This script is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details at
http://www.gnu.org/licenses/.
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbNotices' ) ) {

	class ngfbNotices {

		private $ngfb;		// ngfbPlugin
		private $msgs = array();

		public function __construct( &$ngfb_plugin ) {

			$this->ngfb =& $ngfb_plugin;

			add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		}

		public function err( $msg = '' ) {
			$this->log( 'err', $msg );
		}

		public function inf( $msg = '' ) {
			$this->log( 'inf', $msg );
		}

		public function log( $type, $msg = '' ) {
			if ( ! empty( $msg ) ) $this->msgs[$type][] = $msg;
		}

		public function admin_notices() {
			$p_start = '<p style="padding:0;margin:5px;"><a href="' . $this->ngfb->get_options_url() . '">' . NGFB_ACRONYM . '</a>';
			$p_end = '</p>';

			if ( ! empty( $this->msgs['err'] ) ) {
				echo '<div id="message" class="error">';
				foreach ( $this->msgs['err'] as $msg ) echo $p_start, ' Warning : ', $msg, $p_end;
				echo '</div>';
			}

			if ( ! empty( $this->msgs['inf'] ) ) {
				echo '<div id="message" class="updated fade">';
				foreach ( $this->msgs['inf'] as $msg ) echo $p_start, ' Notice : ', $msg, $p_end;
				echo '</div>';
			}
		}

	}

}
?>
