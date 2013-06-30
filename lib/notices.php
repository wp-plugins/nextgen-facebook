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
		private $log = array(
			'err' => array(),
			'inf' => array(),
			'nag' => array(),
		);

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();

			add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		}

		public function err( $msg = '' ) { $this->log( 'err', $msg ); }

		public function inf( $msg = '' ) { $this->log( 'inf', $msg ); }

		public function nag( $msg = '' ) { $this->log( 'nag', $msg ); }

		public function log( $type, $msg = '' ) {
			if ( ! empty( $msg ) && ! in_array( $msg, $this->log[$type] ) ) 
				$this->log[$type][] = $msg;
		}

		public function admin_notices() {
			if ( ! empty( $this->log['nag'] ) ) {
				echo '
				<style type="text/css">
					.ngfb-update-nag {
						color:#333;
						background:#eeeeff;
						background-image: -webkit-gradient(linear, left bottom, left top, color-stop(7%, #eeeeff), color-stop(77%, #ddddff));
						background-image: -webkit-linear-gradient(bottom, #eeeeff 7%, #ddddff 77%);
						background-image:    -moz-linear-gradient(bottom, #eeeeff 7%, #ddddff 77%);
						background-image:      -o-linear-gradient(bottom, #eeeeff 7%, #ddddff 77%);
						background-image: linear-gradient(to top, #eeeeff 7%, #ddddff 77%);
						border:1px dashed #ccc;
						padding:10px 40px 10px 40px;
						overflow:hidden;
						line-height:1.3em;
					}
					.ngfb-update-nag p {
						margin:5px 0 5px 0;
					}
				</style>';
				foreach ( $this->log['nag'] as $msg )
					echo '<div class="update-nag ngfb-update-nag">', $msg, '</div>', "\n";
			}

			if ( ! empty( $this->log['err'] ) ) {
				foreach ( $this->log['err'] as $msg )
					echo '<div class="error">
						<div style="float:left;"><p><b>', $this->ngfb->acronym_uc, ' Warning</b> :</p></div>
						<p>', $msg, '</p></div>', "\n";
			}

			if ( ! empty( $this->log['inf'] ) ) {
				foreach ( $this->log['inf'] as $msg )
					echo '<div class="updated fade">
						<div style="float:left;"><p><b>', $this->ngfb->acronym_uc, ' Info</b> :</p></div>
						<p>', $msg, '</p></div>', "\n";
			}
		}

	}

}
?>
