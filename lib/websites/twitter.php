<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'ngfbSettingsTwitter' ) && class_exists( 'ngfbSettingsSocialSharing' ) ) {

	class ngfbSettingsTwitter extends ngfbSettingsSocialSharing {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get_rows() {
			$ret = array();
			
			$ret[] = $this->p->util->th( 'Show Button in', 'short' ) . '<td>' . 
			'Content '.$this->p->admin->form->get_checkbox( 'twitter_on_the_content' ).'&nbsp;'.
			'Excerpt '.$this->p->admin->form->get_checkbox( 'twitter_on_the_excerpt' ).'&nbsp;'.
			'Edit Post/Page '.$this->p->admin->form->get_checkbox( 'twitter_on_admin_sharing' ). 
			'</td>';

			$ret[] = $this->p->util->th( 'Preferred Order', 'short' ) . '<td>' . 
			$this->p->admin->form->get_select( 'twitter_order', 
				range( 1, count( $this->p->admin->settings['social']->website ) ), 'short' ) . '</td>';

			$ret[] = $this->p->util->th( 'JavaScript in', 'short' ) . '<td>' . 
			$this->p->admin->form->get_select( 'twitter_js_loc', $this->js_locations ) . '</td>';

			$ret[] = $this->p->util->th( 'Default Language', 'short' ) . '<td>' . 
			$this->p->admin->form->get_select( 'twitter_lang', $this->p->util->get_lang( 'twitter' ) ) . '</td>';

			$ret[] = $this->p->util->th( 'Count Position', 'short' ) . '<td>' . 
			$this->p->admin->form->get_select( 'twitter_count', 
				array( 
					'none' => '',
					'horizontal' => 'Horizontal',
					'vertical' => 'Vertical',
				) 
			) . '</td>';

			$ret[] = $this->p->util->th( 'Button Size', 'short' ) . '<td>' . 
			$this->p->admin->form->get_select( 'twitter_size', array( 'medium' => 'Medium', 'large' => 'Large' ) ) . '</td>';

			$ret[] = $this->p->util->th( 'Tweet Text', 'short' ) . '<td>' . 
			$this->p->admin->form->get_select( 'twitter_caption', $this->captions ) . '</td>';

			$ret[] = $this->p->util->th( 'Text Length', 'short' ) . '<td>' . 
			$this->p->admin->form->get_input( 'twitter_cap_len', 'short' ) . ' Characters or less</td>';

			if ( $this->p->is_avail['aop'] == true )
				$ret[] = $this->p->util->th( 'Add via @username', 'short', null, 
				'Append the Website\'s @username (entered on the ' .
				$this->p->util->get_admin_url( 'general#ngfb-tab_pub_twitter', 'General / Twitter' ) . ' settings tab) to the Tweet.
				The Website @username will also be recommended for following after the Post / Page is shared.' ) . 
				'<td>' . $this->p->admin->form->get_checkbox( 'twitter_via' ) . '</td>';
			else
				$ret[] = $this->p->util->th( 'Add via @username', 'short', null,
				'Append the Website\'s @username (entered on the ' .
				$this->p->util->get_admin_url( 'general', 'General settings page\'s' ) . ' Twitter tab) to the Tweet.
				The Website @username will also be recommended for following after the Post / Page is shared.' ) . 
				'<td class="blank">' . $this->p->admin->form->get_hidden( 'twitter_via' ) . 
					$this->p->admin->form->get_fake_checkbox( $this->p->options['twitter_via'] ) . '</td>';

			$ret[] = $this->p->util->th( 'Recommend Author', 'short', null, 
			'Recommend following the Author\'s Twitter @username (from their profile) after sharing. 
			If the \'<em>Add via @username</em>\' option (above) is also checked, the Website\'s @username will be suggested first.' ) . 
			'<td>' . $this->p->admin->form->get_checkbox( 'twitter_rel_author' ) . '</td>';

			$ret[] = $this->p->util->th( 'Do Not Track', 'short', null,
			'Disable tracking for Twitter\'s tailored suggestions and tailored ads.' ) . 
			'<td>' . $this->p->admin->form->get_checkbox( 'twitter_dnt' ) . '</td>';

			$ret[] = $this->p->util->th( 'Shorten URLs with', 'short', null, '
			If you select a URL shortening service, you must also enter your API Key for that service on the '.
			$this->p->util->get_admin_url( 'advanced#ngfb-tab_plugin_shorten', 'Advanced / URL Shortening' ).' settings tab.' ) .
			'<td>' . $this->p->admin->form->get_select( 'twitter_shortener', 
				array( '' => 'none', 'googl' => 'Goo.gl', 'bitly' => 'Bit.ly' ), 'medium' ) . '&nbsp;&nbsp;' .
				$this->p->util->get_admin_url( 'advanced#ngfb-tab_plugin_shorten', 'Enter your API Keys' ) . '</td>';

			return $ret;
		}

	}
}

if ( ! class_exists( 'ngfbSocialTwitter' ) && class_exists( 'ngfbSocial' ) ) {

	class ngfbSocialTwitter {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get_html( $atts = array(), $opts = array() ) {
			$this->p->debug->mark();
			if ( empty( $opts ) ) $opts = $this->p->options;
			global $post; 
			$html = '';
			$prot = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			$src_id = $this->p->util->get_src_id( 'twitter', $atts );
			$long_url = empty( $atts['url'] ) ? 
				$this->p->util->get_sharing_url( 'notrack', null, $use_post, $src_id ) : 
				$this->p->util->get_sharing_url( 'asis', $atts['url'], null, $src_id );
			$short_url = $this->p->util->get_short_url( $long_url, $opts['twitter_shortener'] );
			if ( empty( $short_url ) ) $short_url = $long_url;	// fallback to long url in case of error

			if ( array_key_exists( 'tweet', $atts ) )
				$atts['caption'] = $atts['tweet'];

			if ( ! array_key_exists( 'caption', $atts ) ) {
				if ( ! empty( $post ) && $use_post == true ) 
					$atts['caption'] = $this->p->meta->get_options( $post->ID, 'twitter_desc' );

				if ( empty( $atts['caption'] ) ) {
					$cap_len = $this->p->util->tweet_max_len( $long_url );	// tweet_max_len() shortens -- don't shorten twice
					$atts['caption'] = $this->p->webpage->get_caption( $opts['twitter_caption'], $cap_len, $use_post );
				}
			}

			if ( ! array_key_exists( 'lang', $atts ) )
				$atts['lang'] = empty( $opts['twitter_lang'] ) ? 'en' : $opts['twitter_lang'];
			$atts['lang'] = apply_filters( $this->p->cf['lca'].'_lang', $atts['lang'], $this->p->util->get_lang( 'twitter' ) );

			if ( ! array_key_exists( 'via', $atts ) ) {
				if ( ! empty( $opts['twitter_via'] ) )
					$atts['via'] = preg_replace( '/^@/', '', $opts['tc_site'] );
			}

			if ( ! array_key_exists( 'related', $atts ) ) {
				if ( ! empty( $opts['twitter_rel_author'] ) && ! empty( $post ) && $use_post == true )
					$atts['related'] = preg_replace( '/^@/', '', 
						get_the_author_meta( $opts['plugin_cm_twitter_name'], $post->author ) );
				else
					$atts['related'] = '';
			}

			if ( ! array_key_exists( 'hashtags', $atts ) )
				$atts['hashtags'] = '';

			if ( ! array_key_exists( 'dnt', $atts ) ) 
				$atts['dnt'] = $opts['twitter_dnt'] ? 'true' : 'false';

			$html = '<!-- Twitter Button --><div '.$this->p->social->get_css( 'twitter', $atts ).'><a href="'.$prot.'twitter.com/share" class="twitter-share-button" data-lang="'. $atts['lang'].'" data-url="'.$short_url.'" data-counturl="'.$long_url.'" data-text="'.$atts['caption'].'" data-via="'.$atts['via'].'" data-related="'.$atts['related'].'" data-hashtags="'.$atts['hashtags'].'" data-count="'.$opts['twitter_count'].'" data-size="'.$opts['twitter_size'].'" data-dnt="'.$atts['dnt'].'">Tweet</a></div>'."\n";

			$this->p->debug->log( 'returning html ('.strlen( $html ).' chars)' );

			return $html;
		}
		
		public function get_js( $pos = 'id' ) {
			$this->p->debug->mark();
			$prot = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
			return '<script type="text/javascript" id="twitter-script-'.$pos.'">'.$this->p->cf['lca'].'_insert_js( "twitter-script-'.$pos.'", "'.$this->p->util->get_cache_url( $prot.'platform.twitter.com/widgets.js' ).'" );</script>'."\n";
		}

	}

}
?>
