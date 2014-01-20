<?php
/*
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Copyright 2012-2014 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbStyle' ) && class_exists( 'SucomStyle' ) ) {

	class NgfbStyle extends SucomStyle {

		private $p;

		public $sharing_css_min_file;
		public $sharing_css_min_url;

		public function __construct( &$plugin ) {
			parent::__construct( $plugin );
			$this->p =& $plugin;
			$this->p->debug->mark();

			$this->sharing_css_min_file = constant( $this->p->cf['uca'].'_CACHEDIR' ).$this->p->cf['lca'].'-sharing-styles.min.css';
			$this->sharing_css_min_url = constant( $this->p->cf['uca'].'_CACHEURL' ).$this->p->cf['lca'].'-sharing-styles.min.css';

			add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_styles' ) );
		}

		public function wp_enqueue_styles( $hook ) {
			// only include sharing styles if option is checked and sharing features are not disabled
			if ( $this->p->is_avail['ssb'] && ! empty( $this->p->options['buttons_link_css'] ) ) {
				wp_register_style( $this->p->cf['lca'].'_sharing_buttons', $this->sharing_css_min_url, false, $this->p->cf['version'] );
				if ( ! file_exists( $this->sharing_css_min_file ) ) {
					$this->p->debug->log( 'updating '.$this->sharing_css_min_file );
					$this->update_sharing( $this->p->options );
				}
				$this->p->debug->log( 'wp_enqueue_style = '.$this->p->cf['lca'].'_sharing_buttons' );
				wp_enqueue_style( $this->p->cf['lca'].'_sharing_buttons' );
			}
		}

		public function update_sharing( &$opts ) {
			if ( $this->p->is_avail['ssb'] && ! empty( $this->p->options['buttons_link_css'] ) ) {
				if ( ! $fh = @fopen( $this->sharing_css_min_file, 'wb' ) )
					$this->p->debug->log( 'Error opening '.$this->sharing_css_min_file.' for writing.' );
				else {
					$css_data = '';
					$style_tabs = apply_filters( $this->p->cf['lca'].'_style_tabs', $this->p->cf['style'] );
					foreach ( $style_tabs as $id => $name )
						$css_data .= $opts['buttons_css_'.$id];
	
					require_once ( NGFB_PLUGINDIR.'lib/ext/compressor.php' );
					$css_data = SuextMinifyCssCompressor::process( $css_data );
					fwrite( $fh, $css_data );
					fclose( $fh );
					$this->p->debug->log( 'updated css file '.$this->sharing_css_min_file );
				}
			} else $this->unlink_sharing();
		}

		public function unlink_sharing() {
			if ( file_exists( $this->sharing_css_min_file ) ) {
				if ( ! @unlink( $this->sharing_css_min_file ) )
					add_settings_error( NGFB_OPTIONS_NAME, 'cssnotrm', 
						'<b>'.$this->p->cf['uca'].' Error</b> : Error removing minimized stylesheet. 
							Does the web server have sufficient privileges?', 'error' );
			}
		}
	}
}

?>
