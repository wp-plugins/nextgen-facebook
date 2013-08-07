<?php
/*
License: Single Website
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/pro.txt
Copyright 2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbSeoWordPressSEO' ) ) {

	class ngfbSeoWordPressSEO {

		private $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
			if ( ! empty( $this->ngfb->is_avail['wpseo'] ) ) {
				$this->add_filters();
			}
		}

		private function add_filters() {
			add_filter( 'ngfb_description_seed', array( &$this, 'filter_description_seed' ), 10, 1 );
		}

		public function filter_description_seed( $description ) {
			global $wpseo_front;
			$descrition = $wpseo_front->metadesc( false );
			return $descrition;
		}

	}
}

?>
