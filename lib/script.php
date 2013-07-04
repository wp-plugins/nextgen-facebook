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
			wp_register_script( $this->ngfb->acronym . '_postmeta', NGFB_URLPATH . 'js/postmeta.js', false, $this->ngfb->version, true );
			wp_register_script( $this->ngfb->acronym . '_tooltips', NGFB_URLPATH . 'js/tooltips.js', false, $this->ngfb->version, true );
			wp_register_script( 'jquery-qtip', NGFB_URLPATH . 'js/jquery.qtip.min.js', array( 'jquery' ), '1.0.0-RC3', true );
		}

		public function admin_enqueue_scripts( $hook ) {
			// don't load our javascript where we don't need it
			switch ( $hook ) {
				case 'post.php' :
				case 'post-new.php' :
					wp_enqueue_script( $this->ngfb->acronym . '_postmeta' );
					wp_enqueue_script( $this->ngfb->acronym . '_tooltips' );
					wp_enqueue_script( 'jquery-qtip' );
					break;
				case ( preg_match( '/_page_' . $this->ngfb->acronym . '-/', $hook ) ? true : false ) :
					wp_enqueue_script( $this->ngfb->acronym . '_tooltips' );
					wp_enqueue_script( 'jquery-qtip' );
					break;
			}
		}

	}
}

?>
