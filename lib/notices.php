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

		public function nag( $msg = '', $store = false ) { $this->log( 'nag', $msg, $store ); }
		public function err( $msg = '', $store = false ) { $this->log( 'err', $msg, $store ); }
		public function inf( $msg = '', $store = false ) { $this->log( 'inf', $msg, $store ); }

		public function log( $type, $msg = '', $store = false ) {
			if ( empty( $msg ) ) return;
			if ( $store == true ) {
				$user_id = get_current_user_id();	// since wp 3.0
				$msg_opt = $this->ngfb->acronym . '_notices_' . $type;
				if ( ( $msg_arr = get_user_option( $msg_opt, $user_id ) ) === false )
					$msg_arr = array();
				if ( ! in_array( $msg, $msg_arr ) )
					$msg_arr[] = $msg;
				update_user_option( $user_id, $msg_opt, $msg_arr );
			} elseif ( ! in_array( $msg, $this->log[$type] ) )
				$this->log[$type][] = $msg;
		}

		public function admin_notices() {
			foreach ( array( 'nag', 'err', 'inf' ) as $type ) {
				$user_id = get_current_user_id();	// since wp 3.0
				$msg_opt = $this->ngfb->acronym . '_notices_' . $type;
				if ( ( $msg_arr = get_user_option( $msg_opt, $user_id ) ) === false )
					$msg_arr = $this->log[$type];
				else $msg_arr = array_merge( $msg_arr, $this->log[$type] );
				$this->log[$type] = array();
				delete_user_option( $user_id, $msg_opt );
				if ( ! empty( $msg_arr ) ) {
					if ( $type == 'nag' ) {
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
								line-height:1.4em;
							}
							.ngfb-update-nag p {
								margin:10px 0 10px 0;
							}
						</style>';
					}
					foreach ( $msg_arr as $msg ) {
						switch ( $type ) {
							case 'nag' :
								echo '<div class="update-nag ngfb-update-nag">', $msg, '</div>', "\n";
								break;
							case 'err' :
								echo '<div class="error"><div style="float:left;"><p><b>', 
									$this->ngfb->acronym_uc, ' Warning</b> :</p></div><p>', $msg, '</p></div>', "\n";
							case 'inf' :
								echo '<div class="updated fade"><div style="float:left;"><p><b>', 
									$this->ngfb->acronym_uc, ' Info</b> :</p></div><p>', $msg, '</p></div>', "\n";
						}
					}
				}
			}
		}

	}

}
?>
