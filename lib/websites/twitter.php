<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbSettingsTwitter' ) && class_exists( 'ngfbSettingsSocialSharing' ) ) {

	class ngfbSettingsTwitter extends ngfbSettingsSocialSharing {

		public $lang = array(
			'en'	=> 'English',
			'fr'	=> 'French',
			'de'	=> 'German',
			'it'	=> 'Italian',
			'es'	=> 'Spanish',
			'ko'	=> 'Korean',
			'ja'	=> 'Japanese',
		);

		protected $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
		}

		public function get_rows() {
			return array(
				$this->ngfb->util->th( 'Add Button to', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_checkbox( 'twitter_on_the_content' ) . ' the Content and / or ' . 
				$this->ngfb->admin->form->get_checkbox( 'twitter_on_the_excerpt' ) . ' the Excerpt Text</td>',

				$this->ngfb->util->th( 'Preferred Order', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_select( 'twitter_order', range( 1, count( $this->ngfb->social_prefix ) ), 'short' ) . '</td>',

				$this->ngfb->util->th( 'JavaScript in', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_select( 'twitter_js_loc', $this->js_locations ) . '</td>',

				$this->ngfb->util->th( 'Language', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_select( 'twitter_lang', $this->lang ) . '</td>',

				$this->ngfb->util->th( 'Count Position', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_select( 'twitter_count', 
					array( 
						'none' => '',
						'horizontal' => 'Horizontal',
						'vertical' => 'Vertical',
					) 
				) . '</td>',

				$this->ngfb->util->th( 'Button Size', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_select( 'twitter_size', 
					array( 
						'medium' => 'Medium',
						'large' => 'Large',
					)
				) . '</td>',

				$this->ngfb->util->th( 'Tweet Text', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_select( 'twitter_caption', $this->captions ) . '</td>',

				$this->ngfb->util->th( 'Text Length', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_input( 'twitter_cap_len', 'short' ) . ' Characters or less</td>',

				$this->ngfb->util->th( 'Do Not Track', 'short' ) . '<td>' . 
				$this->ngfb->admin->form->get_checkbox( 'twitter_dnt' ) . '</td>',

				$this->ngfb->util->th( 'Shorten URLs', 'short', null, '
					Don\'t forget to enter a <em>Goo.gl Simple API Access Key</em> value on the 
					<a href="' . $this->ngfb->util->get_admin_url( 'advanced' ) . '">Advanced settings page</a>.
					' ) . '<td>' .
				$this->ngfb->admin->form->get_checkbox( 'twitter_shorten' ) . '</td>',
			);
		}

	}
}

if ( ! class_exists( 'ngfbSocialTwitter' ) && class_exists( 'ngfbSocial' ) ) {

	class ngfbSocialTwitter extends ngfbSocial {

		protected $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
		}

		public function get_html( $atts = array() ) {
			global $post; 
			$html = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $this->ngfb->util->get_sharing_url( 'notrack', null, $use_post );
			$long_url = $atts['url'];
			$atts['url'] = $this->ngfb->util->get_short_url( $atts['url'], $this->ngfb->options['twitter_shorten'] );
			$cap_len = $this->ngfb->options['twitter_cap_len'] - strlen( $atts['url'] ) - 1;
			if ( ! empty( $atts['tweet'] ) ) 
				$atts['caption'] = $atts['tweet'];
			if ( empty( $atts['caption'] ) && $use_post == true ) 
				$atts['caption'] = $this->ngfb->meta->get_options( $post->ID, 'twitter_desc' );
			if ( empty( $atts['caption'] ) ) 
				$atts['caption'] = $this->ngfb->webpage->get_caption( $this->ngfb->options['twitter_caption'], $cap_len, $use_post );
			$twitter_dnt = $this->ngfb->options['twitter_dnt'] ? 'true' : 'false';
			$lang = empty( $this->ngfb->options['twitter_lang'] ) ? 'en' : $this->ngfb->options['twitter_lang'];
			$html = '
				<!-- Twitter Button -->
				<!-- url = ' . $long_url . ' -->
				<div ' . $this->get_css( 'twitter', $atts ) . '>
					<a href="https://twitter.com/share" 
						class="twitter-share-button"
						lang="'. $lang . '"
						data-url="' . $atts['url'] . '" 
						data-text="' . $atts['caption'] . '" 
						data-count="' . $this->ngfb->options['twitter_count'] . '" 
						data-size="' . $this->ngfb->options['twitter_size'] . '" 
						data-dnt="' . $twitter_dnt . '">Tweet</a>
				</div>' . "\n";
			$this->ngfb->debug->log( 'returning html (' . strlen( $html ) . ' chars)' );
			return $html;
		}
		
		public function get_js( $pos = 'id' ) {
			return '<script type="text/javascript" id="twitter-script-' . $pos . '">
				ngfb_header_js( "twitter-script-' . $pos . '", "' . $this->ngfb->util->get_cache_url( 'https://platform.twitter.com/widgets.js' ) . '" );
			</script>' . "\n";
		}

	}

}
?>
