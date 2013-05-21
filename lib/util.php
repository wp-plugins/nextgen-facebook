<?php
/*
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/

This script is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This script is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details at
http://www.gnu.org/licenses/.
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbUtil' ) ) {

	class ngfbUtil {

		private $ngfb;		// ngfbPlugin
		private $urls_found = array();

		public function __construct( &$ngfb_plugin ) {

			$this->ngfb =& $ngfb_plugin;
		}

		public function is_assoc( $arr ) {
			if ( ! is_array( $arr ) ) return false;
			return is_numeric( implode( array_keys( $arr ) ) ) ? false : true;
		}

		public function get_urls_found() {
			return $this->urls_found;
		}

		public function is_uniq_url( $url = '' ) {

			if ( empty( $url ) ) return false;

			if ( ! preg_match( '/[a-z]+:\/\//i', $url ) )
				$this->ngfb->debug->push( 'incomplete url given: ' . $url );

			if ( empty( $this->urls_found[$url] ) ) {
				$this->urls_found[$url] = 1;
				return true;
			} else {
				$this->ngfb->debug->push( 'duplicate image rejected: ' . $url ); 
				return false;
			}
		}

		public function fix_relative_url( $url = '' ) {
			if ( ! empty( $url ) && ! preg_match( '/[a-z]+:\/\//i', $url ) ) {
				$this->ngfb->debug->push( 'relative url found = ' . $url );
				// if it starts with a slash, just add the home_url() prefix
				if ( preg_match( '/^\//', $url ) ) $url = home_url( $url );
				else $url = trailingslashit( $this->ngfb->get_sharing_url( 'noquery' ), false ) . $url;
				$this->ngfb->debug->push( 'relative url fixed = ' . $url );
			}
			return $url;
		}
	
		public function decode( $str ) {
			// if we don't have something to decode, return immediately
			if ( strpos( $str, '&#' ) === false ) return $str;

			// convert certain entities manually to something non-standard
			$str = preg_replace( '/&#8230;/', '...', $str );

			// if mb_decode_numericentity is not available, return the string un-converted
			if ( $this->ngfb->is_avail['mbdecnum'] != true ) return $str;

			return preg_replace( '/&#\d{2,5};/ue', 'ngfbUtil::decode_utf8_entity( \'$0\' )', $str );
		}

		private function decode_utf8_entity( $entity ) {
			$convmap = array( 0x0, 0x10000, 0, 0xfffff );
			return mb_decode_numericentity( $entity, $convmap, 'UTF-8' );
		}

		public function limit_text_length( $text, $textlen = 300, $trailing = '' ) {
			$text = preg_replace( '/<\/p>/i', ' ', $text);				// replace end of paragraph with a space
			$text = preg_replace( '/[\r\n\t ]+/s', ' ', $text );			// put everything on one line
			$text = $this->cleanup_html_tags( $text );				// remove any remaining html tags
			if ( strlen( $trailing ) > $textlen )
				$trailing = substr( $text, 0, $textlen );			// trim the trailing string, if too long
			if ( strlen( $text ) > $textlen ) {
				$text = substr( $text, 0, $textlen - strlen( $trailing ) );
				$text = trim( preg_replace( '/[^ ]*$/', '', $text ) );		// remove trailing bits of words
				$text = preg_replace( '/[,\.]*$/', '', $text );			// remove trailing puntuation
			} else $trailing = '';							// truncate trailing string if text is shorter than limit
			$text = esc_attr( $text ) . $trailing;					// trim and add trailing string (if provided)
			return $text;
		}

		public function cleanup_html_tags( $text, $strip_tags = true ) {
			$text = strip_shortcodes( $text );						// remove any remaining shortcodes
			$text = preg_replace( '/<\?.*\?>/i', ' ', $text);				// remove php
			$text = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/i', ' ', $text);		// remove javascript
			$text = preg_replace( '/<style\b[^>]*>(.*?)<\/style>/i', ' ', $text);		// remove inline stylesheets
			$text = preg_replace( '/<!--no-text-->(.*?)<!--\/no-text-->/im', ' ', $text);	// remove text between comment strings
			if ( $strip_tags == true ) $text = strip_tags( $text );				// remove remaining html tags
			return trim( $text );
		}

		public function cdn_rewrite( $url = '' ) {
			if ( $this->ngfb->is_avail['cdnlink'] == true ) {
				$rewriter = new CDNLinksRewriterWordpress();
				$url = '"'.$url.'"';	// rewrite function uses var reference, so pad here first
				$url = trim( $rewriter->rewrite( $url ), "\"" );
			}
			return $url;
		}

	}

}
?>
