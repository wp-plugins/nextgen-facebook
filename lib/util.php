<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbUtil' ) ) {

	class NgfbUtil {

		private $goo;	// ngfbGoogl
		private $bit;	// ngfbBitly
		private $urls_found = array();

		protected $p;

		public $rewriter;

		// executed by ngfbUtilPro() as well
		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
			$this->setup_vars();
			$this->add_actions();
		}

		private function setup_vars() {
			if ( $this->p->is_avail['curl'] == true && ! empty( $this->p->options['twitter_shortener'] ) ) {
				switch ( $this->p->options['twitter_shortener'] ) {
					case 'googl' :
						require_once ( NGFB_PLUGINDIR.'lib/ext/googl.php' );
						if ( class_exists( 'ngfbGoogl' ) ) {
							$api_key = empty( $this->p->options['plugin_googl_api_key'] ) ?  
								'' : $this->p->options['plugin_googl_api_key'];
							$this->goo = new ngfbGoogl( $api_key, $this->p->debug );
						}
						break;
					case 'bitly' :
						require_once ( NGFB_PLUGINDIR.'lib/ext/bitly.php' );
						if ( class_exists( 'ngfbBitly' ) ) {
							$login = empty( $this->p->options['plugin_bitly_login'] ) ?  
								'' : $this->p->options['plugin_bitly_login'];
							$api_key = empty( $this->p->options['plugin_bitly_api_key'] ) ?  
								'' : $this->p->options['plugin_bitly_api_key'];
							$this->bit = new ngfbBitly( $login, $api_key, $this->p->debug );
						}
						break;
				}
			}
		}

		protected function add_actions() {
			add_action( 'wp_scheduled_delete', array( &$this, 'delete_expired_transients' ) );
			add_action( 'wp_scheduled_delete', array( &$this, 'delete_expired_file_cache' ) );
		}

		public function is_assoc( $arr ) {
			if ( ! is_array( $arr ) ) return false;
			return is_numeric( implode( array_keys( $arr ) ) ) ? false : true;
		}

		public function preg_grep_keys( $preg, $arr, $invert = false, $replace = false ) {
			if ( ! is_array( $arr ) ) 
				return false;
			$invert = $invert == false ? 
				null : PREG_GREP_INVERT;
			$match = preg_grep( $preg, array_keys( $arr ), $invert );
			$found = array();
			foreach ( $match as $key ) {
				if ( $replace !== false ) {
					$fixed = preg_replace( $preg, $replace, $key );
					$found[$fixed] = $arr[$key]; 
				} else $found[$key] = $arr[$key]; 
			}
			return $found;
		}

		public function restore_checkboxes( &$opts ) {
			// unchecked checkboxes are not provided, so re-create them here based on hidden values
			$checkbox = $this->preg_grep_keys( '/^is_checkbox_/', $opts, false, '' );
			foreach ( $checkbox as $key => $val ) {
				if ( ! array_key_exists( $key, $opts ) )
					$opts[$key] = 0;	// add missing checkbox as empty
				unset ( $opts['is_checkbox_'.$key] );
			}
			return $opts;
		}

		public function reset_urls_found() {
			$this->urls_found = array();
			return;
		}

		public function get_urls_found() {
			return $this->urls_found;
		}

		public function is_uniq_url( $url = '' ) {
			if ( empty( $url ) ) 
				return false;
			if ( ! preg_match( '/[a-z]+:\/\//i', $url ) )
				$this->p->debug->log( 'incomplete url given: '.$url );
			if ( empty( $this->urls_found[$url] ) ) {
				$this->urls_found[$url] = 1;
				return true;
			} else {
				$this->p->debug->log( 'duplicate url rejected: '.$url ); 
				return false;
			}
		}

		// $use_post = false when used for Open Graph meta tags and buttons in widget
		// $use_post = true when buttons are added to individual posts on an index webpage
		public function get_sharing_url( $strip_query = 'notrack', $url = '', $use_post = false, $src_id = '' ) {
			if ( ! empty( $url ) )
				$url = $this->fix_relative_url( $url );
			else {
				global $post;
				$is_nggalbum = false;
				// check for ngg pre-v2 album/gallery query strings and an [nggalbum] shortcode
				if ( is_singular() ) {
					global $wp_query;
					// sanitize query values
					$ngg_album = empty( $wp_query->query['album'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['album'] );
					$ngg_gallery = empty( $wp_query->query['gallery'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['gallery'] );
					if ( ( ! empty( $ngg_album ) || ! empty( $ngg_gallery ) ) && ! empty( $post ) && 
						preg_match( '/\[(nggalbum|album)(| [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*| [^\]]*)\]/im', $post->post_content ) ) {

						$this->p->debug->log( 'is_singular with nggalbum shortcode and query' );
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
					$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				}
			}
			switch ( $strip_query ) {
				case 'noquery' :
					if ( strpos( $url, '?' ) !== false ) {
						$url_arr = explode( '?', $url );
						$url = reset( $url_arr );
					}
					break;
				case 'notrack' :
					// strip out tracking query arguments by facebook, google, etc.
					$url = preg_replace( '/([\?&])(fb_action_ids|fb_action_types|fb_source|fb_aggregation_id|utm_source|utm_medium|utm_campaign|utm_term|gclid|pk_campaign|pk_kwd)=[^&]*&?/i', '$1', $url );
					break;
			}
			return apply_filters( $this->p->cf['lca'].'_sharing_url', $url, $src_id );
		}

		public function get_cache_url( $url ) {
			// make sure the cache expiration is greater than 0 hours
			if ( empty( $this->p->cache->file_expire ) ) 
				return $url;
			// facebook javascript does not work when hosted locally
			if ( preg_match( '/:\/\/connect.facebook.net/', $url ) ) 
				return $url;
			return ( $this->p->util->rewrite_url( $this->p->cache->get( $url ) ) );
		}

		public function get_short_url( $long_url, $shortener = '' ) {
			if ( empty( $shortener ) || 
				$this->p->is_avail['curl'] == false || 
				strlen( $long_url ) < $this->p->options['plugin_min_shorten'] ||
				( defined( 'NGFB_CURL_DISABLE' ) && NGFB_CURL_DISABLE ) ) 
					return apply_filters( $this->p->cf['lca'].'_short_url', false, $long_url );

			$short_url = false;

			if ( defined( 'NGFB_TRANSIENT_CACHE_DISABLE' ) && NGFB_TRANSIENT_CACHE_DISABLE )
				$this->p->debug->log( 'transient cache is disabled' );
			else {
				$cache_salt = __METHOD__.'(url:'.$long_url.')';
				$cache_id = $this->p->cf['lca'].'_'.md5( $cache_salt );
				$cache_type = 'object cache';
				$this->p->debug->log( $cache_type.': short_url transient salt '.$cache_salt );
				$short_url = get_transient( $cache_id );
			}

			if ( $short_url !== false ) {
				$this->p->debug->log( $cache_type.': short_url retrieved from transient '.$cache_id );
				return apply_filters( $this->p->cf['lca'].'_short_url', $short_url, $long_url );
			} else {
				switch ( $shortener ) {
					case 'googl' :
						if ( is_object( $this->goo ) )
							$short_url = $this->goo->shorten( $long_url );
						break;
					case 'bitly' :
						if ( is_object( $this->bit ) ) {
							$short_ret = $this->bit->shorten( $long_url );
							$short_url = empty( $short_ret['url'] ) ? '' : $short_ret['url'];
						}
						break;
					default :
						$this->p->debug->log( 'invalid shortener requested ('.$shortener.')' );
						$short_url = false;
						break;
				}
				if ( empty( $short_url ) )
					$this->p->debug->log( 'failed to shorten url = '.$long_url );
				else {
					$this->p->debug->log( 'url successfully shortened = '.$short_url );
					if ( ! defined( 'NGFB_TRANSIENT_CACHE_DISABLE' ) || ! NGFB_TRANSIENT_CACHE_DISABLE ) {
						set_transient( $cache_id, $short_url, $this->p->cache->object_expire );
						$this->p->debug->log( $cache_type.': short_url saved to transient '.
							$cache_id.' ('.$this->p->cache->object_expire.' seconds)' );
					}
					return apply_filters( $this->p->cf['lca'].'_short_url', $short_url, $long_url );
				}
			}
			return apply_filters( $this->p->cf['lca'].'_short_url', $short_url, $long_url );
		}

		public function fix_relative_url( $url = '' ) {
			if ( ! empty( $url ) && ! preg_match( '/[a-z]+:\/\//i', $url ) ) {
				$this->p->debug->log( 'relative url found = '.$url );
				// if it starts with a slash, just add the home_url() prefix
				if ( preg_match( '/^\//', $url ) ) 
					$url = home_url( $url );
				else $url = trailingslashit( $this->get_sharing_url( 'noquery' ), false ).$url;
				$this->p->debug->log( 'relative url fixed = '.$url );
			}
			return $url;
		}
	
		public function decode( $str ) {
			// if we don't have something to decode, return immediately
			if ( strpos( $str, '&#' ) === false ) return $str;

			// convert certain entities manually to something non-standard
			$str = preg_replace( '/&#8230;/', '...', $str );

			// if mb_decode_numericentity is not available, return the string un-converted
			if ( $this->p->is_avail['mbdecnum'] != true ) return $str;

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
			$text = esc_attr( $text ).$trailing;					// trim and add trailing string (if provided)
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

		public function rewrite_url( $url = '' ) {
			if ( is_object( $this->rewriter ) && method_exists( $this->rewriter, 'html' ) ) {
				$url = '"'.$url.'"';	// rewrite method uses reference
				$url = trim( $this->rewriter->html( $url ), '"' );
			}
			return apply_filters( $this->p->cf['lca'].'_rewrite_url', $url );
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
				'Nature',
				'News',
				'Nostalgia',
				'Parenting',
				'Pets',
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
			$website_topics = apply_filters( $this->p->cf['lca'].'_topics', $website_topics );			// since wp 0.71 
			natsort( $website_topics );
			// after sorting the array, put 'none' first
			$website_topics = array_merge( array( 'none' ), $website_topics );
			return $website_topics;
		}

		public function parse_readme( $expire_secs = 0 ) {
			$this->p->debug->args( array( 'expire_secs' => $expire_secs ) );
			$readme = '';
			$use_local = false;
			$plugin_info = array();

			if ( defined( 'NGFB_TRANSIENT_CACHE_DISABLE' ) && NGFB_TRANSIENT_CACHE_DISABLE ) {
				$this->p->debug->log( 'transient cache is disabled' );
				$use_local = true;
			} else {
				$cache_salt = __METHOD__.'(file:'.$this->p->cf['url']['readme'].')';
				$cache_id = $this->p->cf['lca'].'_'.md5( $cache_salt );
				$cache_type = 'object cache';
				$this->p->debug->log( $cache_type.': plugin_info transient salt '.$cache_salt );
				$plugin_info = get_transient( $cache_id );
				if ( is_array( $plugin_info ) ) {
					$this->p->debug->log( $cache_type.': plugin_info retrieved from transient '.$cache_id );
					return $plugin_info;
				}
			}

			// get remote readme.txt file
			if ( $use_local == false )
				$readme = $this->p->cache->get( $this->p->cf['url']['readme'], 'raw', 'file', $expire_secs );

			// fallback to local readme.txt file
			if ( empty( $readme ) && $fh = @fopen( NGFB_PLUGINDIR.'readme.txt', 'rb' ) ) {
				$use_local = true;
				$readme = fread( $fh, filesize( NGFB_PLUGINDIR.'readme.txt' ) );
				fclose( $fh );
			}

			if ( ! empty( $readme ) ) {
				$parser = new ngfbParseReadme( $this->p->debug );
				$plugin_info = $parser->parse_readme_contents( $readme );
				// remove possibly inaccurate information from local file
				if ( $use_local == true ) {
					foreach ( array( 'stable_tag', 'upgrade_notice' ) as $key )
						if ( array_key_exists( $key, $plugin_info ) )
							unset( $plugin_info[$key] );
				}
			}

			// save the parsed readme (aka $plugin_info) to the transient cache
			if ( ! defined( 'NGFB_TRANSIENT_CACHE_DISABLE' ) || ! NGFB_TRANSIENT_CACHE_DISABLE ) {
				set_transient( $cache_id, $plugin_info, $this->p->cache->object_expire );
				$this->p->debug->log( $cache_type.': plugin_info saved to transient '.$cache_id.' ('.$this->p->cache->object_expire.' seconds)');
			}
			return $plugin_info;
		}

		public function get_admin_url( $submenu = '', $link_text = '' ) {
			$query = '';
			$hash = '';
			if ( strpos( $submenu, '#' ) !== false )
				list( $submenu, $hash ) = explode( '#', $submenu );
			if ( strpos( $submenu, '?' ) !== false )
				list( $submenu, $query ) = explode( '?', $submenu );
			if ( $submenu == '' ) {
				$current = $_SERVER['REQUEST_URI'];
				if ( preg_match( '/^.*\?page='.$this->p->cf['lca'].'-([^&]*).*$/', $current, $match ) )
					$submenu = $match[1];
				else $submenu = key( $this->p->cf['lib']['setting'] );
			}
			$page = 'admin.php?page='.$this->p->cf['lca'].'-'.$submenu;
			if ( array_key_exists( $submenu, $this->p->cf['lib']['setting'] ) )
				$url = admin_url( $page );
			elseif ( array_key_exists( $submenu, $this->p->cf['lib']['network_setting'] ) )
				$url = network_admin_url( $page );

			if ( ! empty( $query ) ) 
				$url .= '&'.$query;

			if ( ! empty( $hash ) ) 
				$url .= '#'.$hash;

			if ( empty( $link_text ) ) 
				return $url;
			else return '<a href="'.$url.'">'.$link_text.'</a>';
		}

		public function delete_expired_transients( $all = false ) { 
			global $wpdb, $_wp_using_ext_object_cache;
			if ( $_wp_using_ext_object_cache ) 
				return; 
			$deleted = 0;
			$time = isset ( $_SERVER['REQUEST_TIME'] ) ? (int) $_SERVER['REQUEST_TIME'] : time() ; 
			$dbquery = 'SELECT option_name FROM '.$wpdb->options.' WHERE option_name LIKE \'_transient_timeout_'.$this->p->cf['lca'].'_%\'';
			$dbquery .= $all === true ? ';' : ' AND option_value < '.$time.';'; 
			$expired = $wpdb->get_col( $dbquery ); 
			foreach( $expired as $transient ) { 
				$key = str_replace('_transient_timeout_', '', $transient);
				delete_transient( $key );
				$deleted++;
			}
			return $deleted;
		}

		public function delete_expired_file_cache( $all = false ) {
			$deleted = 0;
			if ( $dh = opendir( NGFB_CACHEDIR ) ) {
				while ( $fn = readdir( $dh ) ) {
					if ( ! preg_match( '/^(\.|index\.php)/', $fn ) && is_file( NGFB_CACHEDIR.$fn ) && 
						( $all === true || filemtime( NGFB_CACHEDIR.$fn ) < time() - $this->p->cache->file_expire ) ) {
						unlink( NGFB_CACHEDIR.$fn );
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
			return $this->slice_max( $dst, $num );	// returns true or false
		}

		public function slice_max( &$arr, $num = 0 ) {
			if ( ! is_array( $arr ) ) return false;
			$has = count( $arr );
			if ( $num > 0 ) {
				if ( $has == $num ) {
					$this->p->debug->log( 'max values reached ('.$has.' == '.$num.')' );
					return true;
				} elseif ( $has > $num ) {
					$this->p->debug->log( 'max values reached ('.$has.' > '.$num.') - slicing array' );
					$arr = array_slice( $arr, 0, $num );
					return true;
				}
			}
			return false;
		}

		public function is_maxed( &$arr, $num = 0 ) {
			if ( ! is_array( $arr ) ) return false;
			if ( $num > 0 && count( $arr ) >= $num ) return true;
			return false;
		}

		// table header with optional tooltip text
		public function th( $title = '', $class = '', $id = '', $tooltip_text = '' ) {
			$tooltip_class = 'sucom_tooltip';
			$html = '<th'.( empty( $class ) ? '' : ' class="'.$class.'"' ).
				( empty( $id ) ? '' : ' id="'.$id.'"' ).'><p>'.$title;
			if ( ! empty( $tooltip_text ) )
				$html .= '<img src="'.NGFB_URLPATH.'images/question-mark.png" 
					class="'.$tooltip_class.'" alt="'.esc_attr( $tooltip_text ).'" />';
			$html .= '</p></th>'."\n";
			return $html;
		}

		public function do_tabs( $prefix = '', $tabs = array(), $tab_rows = array(), $scroll_to = '' ) {
			$tab_keys = array_keys( $tabs );
			$default_tab = reset( $tab_keys );
			$prefix = empty( $prefix ) ? '' : '_'.$prefix;
			$class_tabs = 'sucom-metabox-tabs'.( empty( $prefix ) ? '' : ' sucom-metabox-tabs'.$prefix );
			$class_link = 'sucom-tablink'.( empty( $prefix ) ? '' : ' sucom-tablink'.$prefix );
			$class_tab = 'sucom-tab';
			echo '<script type="text/javascript">jQuery(document).ready(function(){ 
				sucomTabs(\'', $prefix, '\', \'', $default_tab, '\', \'', $scroll_to, '\'); });</script>
			<div class="', $class_tabs, '">
			<ul class="', $class_tabs, '">';
			foreach ( $tabs as $key => $title ) {
				$href_key = $class_tab.$prefix.'_'.$key;
				echo '<li class="', $href_key, '"><a class="', $class_link, '" href="#', $href_key, '">', $title, '</a></li>';
			}
			echo '</ul>';
			foreach ( $tabs as $key => $title ) {
				$href_key = $class_tab.$prefix.'_'.$key;
				echo '<div class="', $class_tab, ( empty( $prefix ) ? '' : ' '.$class_tab.$prefix ), ' ', $href_key, '">';
				echo '<table class="sucom-settings">';
				if ( ! empty( $tab_rows[$key] ) && is_array( $tab_rows[$key] ) )
					foreach ( $tab_rows[$key] as $row ) 
						echo '<tr>'.$row.'</tr>';
				echo '</table>';
				echo '</div>';
			}
			echo '</div>';
		}

		public function tweet_max_len( $long_url ) {
			$short_url = $this->get_short_url( $long_url, $this->p->options['twitter_shortener'] );
			if ( empty( $short_url ) ) $short_url = $long_url;	// fallback to long url in case of error
			$twitter_cap_len = $this->p->options['twitter_cap_len'] - strlen( $short_url ) - 1;
			if ( ! empty( $this->p->options['tc_site'] ) && ! empty( $this->p->options['twitter_via'] ) )
				$twitter_cap_len = $twitter_cap_len - strlen( preg_replace( '/^@/', '', 
					$this->p->options['tc_site'] ) ) - 5;	// include 'via' and 2 spaces
			return $twitter_cap_len;
		}

		public function get_src_id( $src_name, $atts = array() ) {
			global $post;
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			$src_id = $src_name.( empty( $atts['css_id'] ) ? '' : '-'.preg_replace( '/^ngfb-/','', $atts['css_id'] ) );
			if ( $use_post == true && ! empty( $post ) ) 
				$src_id = $src_id.'-post-'.$post->ID;
			return $src_id;
		}

		public function flush_post_cache( $post_id ) {
			switch ( get_post_status( $post_id ) ) {
				case 'draft' :
				case 'pending' :
				case 'private' :
				case 'publish' :
					$lang = get_locale();
					$name = is_page( $post_id ) ? 'Page' : 'Post';
					$cache_type = 'object cache';
					$sharing_url = $this->p->util->get_sharing_url( 'none', get_permalink( $post_id ) );
					foreach ( array(
						'og array' => 'ngfbOpenGraph::get(lang:'.$lang.'_sharing_url:'.$sharing_url.')',
						'the_excerpt html' => 'ngfbSocial::filter(lang:'.$lang.'_post:'.$post_id.'_type:the_excerpt)',
						'the_content html' => 'ngfbSocial::filter(lang:'.$lang.'_post:'.$post_id.'_type:the_content)',
						'admin_sharing html' => 'ngfbSocial::filter(lang:'.$lang.'_post:'.$post_id.'_type:admin_sharing)',
					) as $cache_origin => $cache_salt ) {
						$cache_id = $this->p->cf['lca'].'_'.md5( $cache_salt );
						$this->p->debug->log( $cache_type.': '.$cache_origin.' transient salt '.$cache_salt );
						if ( delete_transient( $cache_id ) ) {
							$this->p->debug->log( $cache_type.': '.$cache_origin.' transient deleted '.$cache_id );
							// duplicate notices are ignored, so only one notice message will be shown
							$this->p->notice->inf( 'WordPress object cache flushed for '.$name.' ID #'.$post_id, true );
						}
					}
					break;
			}
		}

		public function get_lang( $lang = '' ) {
			$ret = array();
			switch ( $lang ) {
				case 'fb' :
				case 'facebook' :
					$ret = array(
						'af_ZA' => 'Afrikaans',
						'sq_AL' => 'Albanian',
						'ar_AR' => 'Arabic',
						'hy_AM' => 'Armenian',
						'az_AZ' => 'Azerbaijani',
						'eu_ES' => 'Basque',
						'be_BY' => 'Belarusian',
						'bn_IN' => 'Bengali',
						'bs_BA' => 'Bosnian',
						'bg_BG' => 'Bulgarian',
						'ca_ES' => 'Catalan',
						'zh_HK' => 'Chinese (Hong Kong)',
						'zh_CN' => 'Chinese (Simplified)',
						'zh_TW' => 'Chinese (Traditional)',
						'hr_HR' => 'Croatian',
						'cs_CZ' => 'Czech',
						'da_DK' => 'Danish',
						'nl_NL' => 'Dutch',
						'en_GB' => 'English (UK)',
						'en_PI' => 'English (Pirate)',
						'en_UD' => 'English (Upside Down)',
						'en_US' => 'English (US)',
						'eo_EO' => 'Esperanto',
						'et_EE' => 'Estonian',
						'fo_FO' => 'Faroese',
						'tl_PH' => 'Filipino',
						'fi_FI' => 'Finnish',
						'fr_CA' => 'French (Canada)',
						'fr_FR' => 'French (France)',
						'fy_NL' => 'Frisian',
						'gl_ES' => 'Galician',
						'ka_GE' => 'Georgian',
						'de_DE' => 'German',
						'el_GR' => 'Greek',
						'he_IL' => 'Hebrew',
						'hi_IN' => 'Hindi',
						'hu_HU' => 'Hungarian',
						'is_IS' => 'Icelandic',
						'id_ID' => 'Indonesian',
						'ga_IE' => 'Irish',
						'it_IT' => 'Italian',
						'ja_JP' => 'Japanese',
						'km_KH' => 'Khmer',
						'ko_KR' => 'Korean',
						'ku_TR' => 'Kurdish',
						'la_VA' => 'Latin',
						'lv_LV' => 'Latvian',
						'fb_LT' => 'Leet Speak',
						'lt_LT' => 'Lithuanian',
						'mk_MK' => 'Macedonian',
						'ms_MY' => 'Malay',
						'ml_IN' => 'Malayalam',
						'ne_NP' => 'Nepali',
						'nb_NO' => 'Norwegian (Bokmal)',
						'nn_NO' => 'Norwegian (Nynorsk)',
						'ps_AF' => 'Pashto',
						'fa_IR' => 'Persian',
						'pl_PL' => 'Polish',
						'pt_BR' => 'Portuguese (Brazil)',
						'pt_PT' => 'Portuguese (Portugal)',
						'pa_IN' => 'Punjabi',
						'ro_RO' => 'Romanian',
						'ru_RU' => 'Russian',
						'sk_SK' => 'Slovak',
						'sl_SI' => 'Slovenian',
						'es_LA' => 'Spanish',
						'es_ES' => 'Spanish (Spain)',
						'sr_RS' => 'Serbian',
						'sw_KE' => 'Swahili',
						'sv_SE' => 'Swedish',
						'ta_IN' => 'Tamil',
						'te_IN' => 'Telugu',
						'th_TH' => 'Thai',
						'tr_TR' => 'Turkish',
						'uk_UA' => 'Ukrainian',
						'vi_VN' => 'Vietnamese',
						'cy_GB' => 'Welsh',
					);
					break;
				case 'gplus' :
				case 'google' :
					$ret = array(
						'af'	=> 'Afrikaans',
						'am'	=> 'Amharic',
						'ar'	=> 'Arabic',
						'eu'	=> 'Basque',
						'bn'	=> 'Bengali',
						'bg'	=> 'Bulgarian',
						'ca'	=> 'Catalan',
						'zh-HK'	=> 'Chinese (Hong Kong)',
						'zh-CN'	=> 'Chinese (Simplified)',
						'zh-TW'	=> 'Chinese (Traditional)',
						'hr'	=> 'Croatian',
						'cs'	=> 'Czech',
						'da'	=> 'Danish',
						'nl'	=> 'Dutch',
						'en-GB'	=> 'English (UK)',
						'en-US'	=> 'English (US)',
						'et'	=> 'Estonian',
						'fil'	=> 'Filipino',
						'fi'	=> 'Finnish',
						'fr'	=> 'French',
						'fr-CA'	=> 'French (Canadian)',
						'gl'	=> 'Galician',
						'de'	=> 'German',
						'el'	=> 'Greek',
						'gu'	=> 'Gujarati',
						'iw'	=> 'Hebrew',
						'hi'	=> 'Hindi',
						'hu'	=> 'Hungarian',
						'is'	=> 'Icelandic',
						'id'	=> 'Indonesian',
						'it'	=> 'Italian',
						'ja'	=> 'Japanese',
						'kn'	=> 'Kannada',
						'ko'	=> 'Korean',
						'lv'	=> 'Latvian',
						'lt'	=> 'Lithuanian',
						'ms'	=> 'Malay',
						'ml'	=> 'Malayalam',
						'mr'	=> 'Marathi',
						'no'	=> 'Norwegian',
						'fa'	=> 'Persian',
						'pl'	=> 'Polish',
						'pt-BR'	=> 'Portuguese (Brazil)',
						'pt-PT'	=> 'Portuguese (Portugal)',
						'ro'	=> 'Romanian',
						'ru'	=> 'Russian',
						'sr'	=> 'Serbian',
						'sk'	=> 'Slovak',
						'sl'	=> 'Slovenian',
						'es'	=> 'Spanish',
						'es-419'	=> 'Spanish (Latin America)',
						'sw'	=> 'Swahili',
						'sv'	=> 'Swedish',
						'ta'	=> 'Tamil',
						'te'	=> 'Telugu',
						'th'	=> 'Thai',
						'tr'	=> 'Turkish',
						'uk'	=> 'Ukrainian',
						'ur'	=> 'Urdu',
						'vi'	=> 'Vietnamese',
						'zu'	=> 'Zulu',
					);
					break;
				case 'twitter' :
					$ret = array(
						'ar'	=> 'Arabic',
						'ca'	=> 'Catalan',
						'cs'	=> 'Czech',
						'da'	=> 'Danish',
						'de'	=> 'German',
						'el'	=> 'Greek',
						'en'	=> 'English',
						'en-gb'	=> 'English UK',
						'es'	=> 'Spanish',
						'eu'	=> 'Basque',
						'fa'	=> 'Farsi',
						'fi'	=> 'Finnish',
						'fil'	=> 'Filipino',
						'fr'	=> 'French',
						'gl'	=> 'Galician',
						'he'	=> 'Hebrew',
						'hi'	=> 'Hindi',
						'hu'	=> 'Hungarian',
						'id'	=> 'Indonesian',
						'it'	=> 'Italian',
						'ja'	=> 'Japanese',
						'ko'	=> 'Korean',
						'msa'	=> 'Malay',
						'nl'	=> 'Dutch',
						'no'	=> 'Norwegian',
						'pl'	=> 'Polish',
						'pt'	=> 'Portuguese',
						'ro'	=> 'Romanian',
						'ru'	=> 'Russian',
						'sv'	=> 'Swedish',
						'th'	=> 'Thai',
						'tr'	=> 'Turkish',
						'uk'	=> 'Ukrainian',
						'ur'	=> 'Urdu',
						'xx-lc'	=> 'Lolcat',
						'zh-tw'	=> 'Traditional Chinese',
						'zh-cn'	=> 'Simplified Chinese',

					);
					break;
			}
			asort( $ret );
			return $ret;
		}
	}
}

?>
