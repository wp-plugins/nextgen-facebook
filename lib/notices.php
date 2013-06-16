<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbNotices' ) ) {

	class ngfbNotices {

		private $ngfb;		// ngfbPlugin
		private $msgs = array(
			'err' => array(),
			'inf' => array(),
		);

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();

			add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		}

		public function err( $msg = '' ) {
			$this->log( 'err', $msg );
		}

		public function inf( $msg = '' ) {
			$this->log( 'inf', $msg );
		}

		public function log( $type, $msg = '' ) {
			if ( ! empty( $msg ) && ! in_array( $msg, $this->msgs[$type] ) ) 
				$this->msgs[$type][] = $msg;
		}

		public function admin_notices() {
			$p_start = '<p><b>' . $this->ngfb->acronym_uc . '</b>';
			$p_end = '</p>';

			if ( ! empty( $this->msgs['err'] ) ) {
				echo '<div id="message" class="error">';
				foreach ( $this->msgs['err'] as $msg ) echo $p_start, ' Warning : ', $msg, $p_end;
				echo '</div>', "\n";
			}

			if ( ! empty( $this->msgs['inf'] ) ) {
				echo '<div id="message" class="updated fade">';
				foreach ( $this->msgs['inf'] as $msg ) echo $p_start, ' : ', $msg, $p_end;
				echo '</div>', "\n";
			}
		}

	}

}
?>
