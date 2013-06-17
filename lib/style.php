<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbStyle' ) ) {

	class ngfbStyle {
	
		private $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
			$this->register_admin_styles();

			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_styles' ) );
		}

		public function register_admin_styles() {
			wp_register_style( $this->ngfb->acronym . '_admin_settings_page', NGFB_URLPATH . 'css/admin-settings-page.css', false, $this->ngfb->version );
			wp_register_style( $this->ngfb->acronym . '_table_settings', NGFB_URLPATH . 'css/table-settings.css', false, $this->ngfb->version );
		}

		public function enqueue_admin_styles( $hook ) {
			switch ( $hook ) {
				case 'post.php' :
				case 'page.php' :
					wp_enqueue_style( $this->ngfb->acronym . '_table_settings' );
					break;
				case ( preg_match( '/_page_' . $this->ngfb->acronym . '-/', $hook ) ? true : false ) :
					wp_enqueue_style( $this->ngfb->acronym . '_admin_settings_page' );
					wp_enqueue_style( $this->ngfb->acronym . '_table_settings' );
					break;
			}
		}

	}
}

?>
