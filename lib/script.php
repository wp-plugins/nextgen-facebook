<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbScript' ) ) {

	class ngfbScript {

		private $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
			$this->register_scripts();

			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
		}

		public function register_scripts() {
			// wp_register_script( $handle, $src, $deps, $ver, $in_footer );
			wp_register_script( $this->ngfb->acronym . '_admin_metabox', NGFB_URLPATH . 'js/admin-metabox.js', false, $this->ngfb->version, true );
		}

		public function admin_enqueue_scripts( $hook ) {
			switch ( $hook ) {
				case 'post.php' :
				case 'page.php' :
					wp_enqueue_script( $this->ngfb->acronym . '_admin_metabox' );
					break;
			}
		}

	}
}

?>
