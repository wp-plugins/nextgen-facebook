<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbUtil' ) ) {

	class ngfbUtil {

		public $rewrite;

		protected $ngfb;

		private $goo;	// ngfbGoogl
		private $urls_found = array();

		// executed by ngfbUtilPro() as well
		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
			$this->setup_vars();
			$this->add_actions();
		}

		private function setup_vars() {
			if ( class_exists( 'ngfbGoogl' ) ) {
				$api_key = empty( $this->ngfb->options['ngfb_googl_api_key'] ) ? 
					'' : $this->ngfb->options['ngfb_googl_api_key'];
				$this->goo = new ngfbGoogl( $api_key, $this->ngfb->debug );
			}
		}

		protected function add_actions() {
			add_action( 'wp_scheduled_delete', array( &$this, 'delete_expired_transients' ) );
			add_action( 'wp_scheduled_delete', array( &$this, 'delete_expired_cache' ) );
		}

		public function is_assoc( $arr ) {
			if ( ! is_array( $arr ) ) return false;
			return is_numeric( implode( array_keys( $arr ) ) ) ? false : true;
		}

		public function preg_grep_keys( $preg, $arr, $invert = false, $trunc = false, $rep = '' ) {
			if ( ! is_array( $arr ) ) return false;
			$invert = $invert == false ? null : PREG_GREP_INVERT;
			$match = preg_grep( $preg, array_keys( $arr ), $invert );
			$found = array();
			foreach ( $match as $key ) {
				if ( $trunc == true ) {
					$fixed = preg_replace( $preg, $rep, $key );
					$found[$fixed] = $arr[$key]; 
				} else $found[$key] = $arr[$key]; 
			}
			return $found;
		}

		public function get_urls_found() {
			return $this->urls_found;
		}

		public function is_uniq_url( $url = '' ) {

			if ( empty( $url ) ) return false;

			if ( ! preg_match( '/[a-z]+:\/\//i', $url ) )
				$this->ngfb->debug->log( 'incomplete url given: ' . $url );

			if ( empty( $this->urls_found[$url] ) ) {
				$this->urls_found[$url] = 1;
				return true;
			} else {
				$this->ngfb->debug->log( 'duplicate image rejected: ' . $url ); 
				return false;
			}
		}

		// $use_post = false when used for Open Graph meta tags and buttons in widget
		// $use_post = true when buttons are added to individual posts on an index webpage
		public function get_sharing_url( $strip_query = 'notrack', $url = '', $use_post = false ) {
			if ( ! empty( $url ) )  {
				$url = $this->fix_relative_url( $url );
			} else {
				global $post;
				$is_nggalbum = false;

				// check for album/gallery query strings and an [nggalbum] shortcode
				if ( is_singular() ) {
					global $wp_query;
					// sanitize query values
					$ngg_album = empty( $wp_query->query['album'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['album'] );
					$ngg_gallery = empty( $wp_query->query['gallery'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['gallery'] );

					if ( ( ! empty( $ngg_album ) || ! empty( $ngg_gallery ) ) && ! empty( $post ) && 
						preg_match( '/\[(nggalbum|album)(| [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*| [^\]]*)\]/im', $post->post_content ) ) {
						$this->ngfb->debug->log( 'is_singular with nggalbum shortcode and query' );
						$is_nggalbum = true;
						$strip_query = 'notrack';	// keep the album/gallery query values
					}
				}

				// use permalink for singular pages (without nggalbum query info) or posts within a loop (use_post is true)
				if ( ( is_singular() && $is_nggalbum == false ) || ( $use_post && ! empty( $post ) ) ) {
					$url = get_permalink( $post->ID );
					$strip_query = 'none';	// don't modify the permalinks
				} else {
					$url = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
					$url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
				}
			}
			switch ( $strip_query ) {
				case 'noquery' :
					if ( strpos( $url, '?' ) !== false ) 
						$url = reset( explode( '?', $url ) );
					break;
				case 'notrack' :
					// strip out tracking query arguments by facebook, google, etc.
					$url = preg_replace( '/([\?&])(fb_action_ids|fb_action_types|fb_source|fb_aggregation_id|utm_source|utm_medium|utm_campaign|utm_term|gclid|pk_campaign|pk_kwd)=[^&]*&?/i', '$1', $url );
					break;
				// leave url as-is
				default :
					break;
			}
			return $url;
		}

		public function get_cache_url( $url ) {

			// make sure the cache expiration is greater than 0 hours
			if ( empty( $this->ngfb->cache->file_expire ) ) return $url;

			// facebook javascript sdk doesn't work when hosted locally
			if ( preg_match( '/connect.facebook.net/', $url ) ) return $url;

			return ( $this->ngfb->util->rewrite( $this->ngfb->cache->get( $url ) ) );
		}

		public function get_short_url( $url, $shorten = true ) {

			// return original URL if curl not installed or disabled
			// $shorten can be 'true' or '1', so test with empty()
			if ( ! function_exists( 'curl_init' ) || ! class_exists( 'ngfbGoogl' ) ||
				empty( $shorten ) || ( defined( 'NGFB_CURL_DISABLE' ) && NGFB_CURL_DISABLE ) ) 
					return $url;

			$cache_salt = __METHOD__ . '(url:' . $url . ')';
			$cache_id = $this->ngfb->acronym . '_' . md5( $cache_salt );
			$cache_type = 'object cache';
			$short_url = get_transient( $cache_id );
			$this->ngfb->debug->log( $cache_type . ': short_url transient id salt "' . $cache_salt . '"' );

			if ( $short_url !== false ) {
				$this->ngfb->debug->log( $cache_type . ': short_url retrieved from transient for id "' . $cache_id . '"' );
				$url = $short_url;
			} else {
				$short_url = $this->goo->shorten( $url );
				if ( empty( $short_url ) )
					$this->ngfb->debug->log( 'failed to shorten url = ' . $url );
				else {
					$this->ngfb->debug->log( 'url successfully shortened = ' . $short_url );
					set_transient( $cache_id, $short_url, $this->ngfb->cache->object_expire );
					$this->ngfb->debug->log( $cache_type . ': short_url saved to transient for id "' . 
						$cache_id . '" (' . $this->ngfb->cache->object_expire . ' seconds)' );
					$url = $short_url;
				}
			}
			return $url;
		}

		public function fix_relative_url( $url = '' ) {
			if ( ! empty( $url ) && ! preg_match( '/[a-z]+:\/\//i', $url ) ) {
				$this->ngfb->debug->log( 'relative url found = ' . $url );
				// if it starts with a slash, just add the home_url() prefix
				if ( preg_match( '/^\//', $url ) ) $url = home_url( $url );
				else $url = trailingslashit( $this->get_sharing_url( 'noquery' ), false ) . $url;
				$this->ngfb->debug->log( 'relative url fixed = ' . $url );
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
			$text = strip_shortcodes( $text );							// remove any remaining shortcodes
			$text = preg_replace( '/[\r\n\t ]+/s', ' ', $text );					// put everything on one line
			$text = preg_replace( '/<\?.*\?>/i', ' ', $text);					// remove php
			$text = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/i', ' ', $text);			// remove javascript
			$text = preg_replace( '/<style\b[^>]*>(.*?)<\/style>/i', ' ', $text);			// remove inline stylesheets
			$text = preg_replace( '/<!--ngfb-ignore-->(.*?)<!--\/ngfb-ignore-->/i', ' ', $text);	// remove text between comment strings
			if ( $strip_tags == true ) $text = strip_tags( $text );					// remove remaining html tags
			return trim( $text );
		}

		public function rewrite( $url = '' ) {
			if ( $this->ngfb->is_avail['aop'] == true ) {
				$url = '"' . $url . '"';	// rewrite function uses var reference
				$url = trim( $this->rewrite->html( $url ), '"' );
			}
			return $url;
		}

		public function get_topics() {
			// list from http://en.wikipedia.org/wiki/Category:Websites_by_topic
			$website_topics = array(
				'Animation',
				'Architecture',
				'Art',
				'Automotive',
				'Aviation',
				'Chat',
				'Children\'s',
				'Comics',
				'Commerce',
				'Community',
				'Dance',
				'Dating',
				'Digital Media',
				'Documentary',
				'Download',
				'Economics',
				'Educational',
				'Employment',
				'Entertainment',
				'Environmental',
				'Erotica and Pornography',
				'Fashion',
				'File Sharing',
				'Food and Drink',
				'Fundraising',
				'Genealogy',
				'Health',
				'History',
				'Humor',
				'Law Enforcement',
				'Legal',
				'Literature',
				'Medical',
				'Military',
				'News',
				'Nostalgia',
				'Parenting',
				'Photography',
				'Political',
				'Religious',
				'Review',
				'Reward',
				'Route Planning',
				'Satirical',
				'Science Fiction',
				'Science',
				'Shock',
				'Social Networking',
				'Spiritual',
				'Sport',
				'Technology',
				'Travel',
				'Vegetarian',
				'Webmail',
				'Women\'s',
			);
			natsort( $website_topics );

			// after sorting the array, put 'none' first
			$website_topics = array_merge( array( 'none' ), $website_topics );

			return $website_topics;
		}

		public function parse_readme( $url, $expire_secs = false ) {
			$plugin_info;
			$using_local = false;
			$readme = $this->ngfb->cache->get( $this->ngfb->urls['readme'], 'raw', 'file', $expire_secs );
			// fallback to local readme.txt file
			if ( empty( $readme ) && $fh = @fopen( NGFB_PLUGINDIR . 'readme.txt', 'rb' ) ) {
				$using_local = true;
				$readme = fread( $fh, filesize( NGFB_PLUGINDIR . 'readme.txt' ) );
				fclose( $fh );
			}
			if ( ! empty( $readme ) ) {
				$parser = new ngfb_parse_readme( $this->ngfb->debug );
				$plugin_info = $parser->parse_readme_contents( $readme );
				if ( $using_local == true ) {
					foreach ( array( 'stable_tag', 'upgrade_notice' ) as $key )
						if ( array_key_exists( $key, $plugin_info ) )
							unset( $plugin_info[$key] );
				}
			}
			return $plugin_info;
		}

		public function get_admin_url( $submenu = '', $link_text = '' ) {
			$query = '';
			if ( strpos( $submenu, '?' ) !== false )
				list( $submenu, $query ) = explode( '?', $submenu );
			if ( $submenu == '' ) {
				$current = $_SERVER['REQUEST_URI'];
				if ( preg_match( '/^.*\?page=' . $this->ngfb->acronym . '-([^&]*).*$/', $current, $match ) )
					$submenu = $match[1];
				else $submenu = 'general';
			} else {
				if ( ! array_key_exists( $submenu, $this->ngfb->setting_libs ) )
					$submenu = 'general';
			}
			$url = get_admin_url( null, 'admin.php?page=' . $this->ngfb->acronym . '-' . $submenu );
			if ( ! empty( $query ) ) $url .= '&' . $query;
			if ( empty( $link_text ) ) return $url;
			else return '<a href="' . $url . '">' . $link_text . '</a>';
		}

		public function delete_expired_transients( $clear_all = false ) { 
			global $wpdb, $_wp_using_ext_object_cache;
			$deleted = 0;
			if ( $_wp_using_ext_object_cache ) return; 
			$time = isset ( $_SERVER['REQUEST_TIME'] ) ? (int) $_SERVER['REQUEST_TIME'] : time() ; 
		
			$dbquery = 'SELECT option_name FROM ' . $wpdb->options . ' WHERE option_name LIKE \'_transient_timeout_' . $this->ngfb->acronym . '_%\'';
			$dbquery .= $clear_all === true ? ';' : ' AND option_value < ' . $time . ';'; 
			$expired = $wpdb->get_col( $dbquery ); 
			
			foreach( $expired as $transient ) { 
				$key = str_replace('_transient_timeout_', '', $transient);
				delete_transient( $key );
				$deleted++;
			}
			return $deleted;
		}

		public function delete_expired_cache( $clear_all = false ) {
			$deleted = 0;
			if ( $dh = opendir( NGFB_CACHEDIR ) ) {
				while ( $fn = readdir( $dh ) ) {
					if ( ! preg_match( '/^(\.|index\.php)/', $fn ) && is_file( NGFB_CACHEDIR . $fn ) && 
						( $clear_all === true || filemtime( NGFB_CACHEDIR . $fn ) < time() - $this->ngfb->cache->file_expire ) ) {

						unlink( NGFB_CACHEDIR . $fn );
						$deleted++;
					}
				}
				closedir( $dh );
			}
			return $deleted;
		}

		public function push_max( &$dst, &$src, $num = 0 ) {
			if ( ! is_array( $dst ) || ! is_array( $src ) ) return false;
			if ( ! empty( $src ) ) array_push( $dst, $src );
			return $this->slice_max( $dst, $num );
		}

		public function slice_max( &$arr, $num = 0 ) {
			if ( ! is_array( $arr ) ) return false;
			$has = count( $arr );
			if ( $num > 0 ) {
				if ( $has == $num ) {
					$this->ngfb->debug->log( 'max values reached (' . $has . ' == ' . $num . ')' );
					return true;
				} elseif ( $has > $num ) {
					$this->ngfb->debug->log( 'max values reached (' . $has . ' > ' . $num . ') - slicing array' );
					$arr = array_slice( $arr, 0, $num );
					return true;
				}
			}
			return false;
		}

		public function th( $title = '', $class = '', $id = '', $tooltip = '' ) {
			$html = '<th'.
				( empty( $class ) ? '' : ' class="'.$class.'"' ) .
				( empty( $id ) ? '' : ' id="'.$id.'"' ) . 
				'><p>' .  $title;
			if ( ! empty( $tooltip ) )
				$html .= '<img src="' . NGFB_URLPATH . 'images/question-mark.png" 
					class="ngfb_tooltip" alt="' . esc_attr( $tooltip ) . '" />';
			$html .= '</p></th>' . "\n";
			return $html;
		}

	}

}
?>
