<?php
/*
License: Single Website
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/pro.txt
Copyright 2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbSeoAllinOneSEOPack' ) ) {

	class ngfbSeoAllinOneSEOPack {

		private $ngfb;
		private $opts;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
			if ( ! empty( $this->ngfb->is_avail['aioseop'] ) ) {
				$this->set_opts();
				$this->add_filters();
			}
		}

		private function set_opts() {
			$this->opts = get_option( 'aioseop_options' );
		}

		private function add_filters() {
			add_filter( 'ngfb_title_seed', array( &$this, 'filter_title_seed' ), 10, 1 );
			add_filter( 'ngfb_description_seed', array( &$this, 'filter_description_seed' ), 10, 1 );
		}

		public function filter_title_seed( $title ) {
			if ( is_front_page() && ! empty( $this->opts['aiosp_home_title' ] ) )
				$title = $this->opts['aiosp_home_title'];
			return $title;
		}

		public function filter_description_seed( $description ) {
			if ( is_front_page() && ! empty( $this->opts['aiosp_home_description' ] ) )
				$description = $this->opts['aiosp_home_description'];
			return $description;
		}

	}
}

?>
