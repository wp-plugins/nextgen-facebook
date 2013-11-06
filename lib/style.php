<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbStyle' ) ) {

	class NgfbStyle {

		private $p;

		public $social_css_min_file;
		public $social_css_min_url;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();

			$url_path = constant( $this->p->cf['uca'].'_URLPATH' );
			$this->social_css_min_url = $url_path.'cache/'.$this->p->cf['lca'].'-social-styles.min.css';
			$this->social_css_min_file = $url_path.'cache/'.$this->p->cf['lca'].'-social-styles.min.css';

			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_styles' ) );
		}

		public function admin_enqueue_styles( $hook ) {
			$url_path = constant( $this->p->cf['uca'].'_URLPATH' );
			wp_register_style( 'sucom_settings_pages', $url_path.'css/common/settings-pages.min.css', false, $this->p->cf['version'] );
			wp_register_style( 'sucom_table_settings', $url_path.'css/common/table-settings.min.css', false, $this->p->cf['version'] );
			wp_register_style( 'sucom_metabox_tabs', $url_path.'css/common/metabox-tabs.min.css', false, $this->p->cf['version'] );

			switch ( $hook ) {
				case 'post.php' :
				case 'post-new.php' :
					wp_enqueue_style( 'sucom_table_settings' );
					wp_enqueue_style( 'sucom_metabox_tabs' );
					break;
				case ( preg_match( '/_page_'.$this->p->cf['lca'].'-/', $hook ) ? true : false ) :
					wp_enqueue_style( 'sucom_settings_pages' );
					wp_enqueue_style( 'sucom_table_settings' );
					wp_enqueue_style( 'sucom_metabox_tabs' );
					break;
			}
		}

		public function wp_enqueue_styles( $hook ) {
			if ( ! empty( $this->p->options['buttons_link_css'] ) ) {
				wp_register_style( $this->p->cf['lca'].'_social_buttons', $this->social_css_min_url, false, $this->p->cf['version'] );
				if ( ! file_exists( $this->social_css_min_file ) ) 
					$this->update_social( $this->p->options );
				$this->p->debug->log( 'wp_enqueue_style = '.$this->p->cf['lca'].'_social_buttons' );
				wp_enqueue_style( $this->p->cf['lca'].'_social_buttons' );
			}
		}

		public function update_social( &$opts ) {
			if ( ! $fh = @fopen( $this->social_css_min_file, 'wb' ) ) {
				$this->p->notice->err( 'Error opening <u>'.$this->social_css_min_file.'</u> for writing.' );
			} else {
				$css_data = '';
				foreach ( $this->p->cf['css'] as $id => $name )
					$css_data .= $opts['buttons_css_'.$id];
				unset( $id, $name );
				require_once ( NGFB_PLUGINDIR.'lib/ext/compressor.php' );
				$css_data = ngfbMinifyCssCompressor::process( $css_data );
				fwrite( $fh, $css_data );
				fclose( $fh );
				$this->p->debug->log( 'updated css file '.$this->social_css_min_file );
			}
		}

		public function unlink_social() {
			if ( file_exists( $this->social_css_min_file ) ) {
				if ( ! @unlink( $this->social_css_min_file ) )
					add_settings_error( NGFB_OPTIONS_NAME, 'cssnotrm', 
						'<b>'.$this->p->cf['uca'].' Error</b> : Error removing minimized stylesheet. 
							Does the web server have sufficient privileges?', 'error' );
			}
		}
	}
}

?>
