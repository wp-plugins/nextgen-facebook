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

if ( ! class_exists( 'ngfbAdminTwitter' ) && class_exists( 'ngfbAdmin' ) ) {

	class ngfbAdminTwitter extends ngfbAdmin {

		public $lang = array(
			'en'	=> 'English',
			'fr'	=> 'French',
			'de'	=> 'German',
			'it'	=> 'Italian',
			'es'	=> 'Spanish',
			'ko'	=> 'Korean',
			'ja'	=> 'Japanese',
		);

		private $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
		}

		public function get_rows() {
			return array(
				'<th colspan="2" class="social">Twitter</th>',
				'<td colspan="2" style="height:5px;"></td>',
				'<th>Add Button to Content</th><td>' . $this->ngfb->admin->form->get_checkbox( 'twitter_enable' ) . '</td>',
				'<th>Preferred Order</th><td>' . $this->ngfb->admin->form->get_select( 'twitter_order', range( 1, count( $this->ngfb->social_options_prefix ) ), 'short' ) . '</td>',
				'<th>JavaScript in</th><td>' . $this->ngfb->admin->form->get_select( 'twitter_js_loc', $this->js_locations ) . '</td>',
				'<th>Language</th><td>' . $this->ngfb->admin->form->get_select( 'twitter_lang', $this->lang ) . '</td>',
				'<th>Count Box Position</th><td>' . $this->ngfb->admin->form->get_select( 'twitter_count', 
					array( 
						'horizontal' => 'Horizontal',
						'vertical' => 'Vertical',
						'none' => 'None',
					) 
				) . '</td>',
				'<th>Button Size</th><td>' . $this->ngfb->admin->form->get_select( 'twitter_size', 
					array( 
						'medium' => 'Medium',
						'large' => 'Large',
					)
				) . '</td>',
				'<th>Tweet Text</th><td>' . $this->ngfb->admin->form->get_select( 'twitter_caption', $this->captions ) . '</td>',
				'<th>Maximum Text Length</th><td>' . $this->ngfb->admin->form->get_input( 'twitter_cap_len', 'short' ) . ' Characters</td>',
				'<th>Do Not Track</th><td>' . $this->ngfb->admin->form->get_checkbox( 'twitter_dnt' ) . '</td>',
				'<th>Shorten URLs</th><td>' . $this->ngfb->admin->form->get_checkbox( 'twitter_shorten' ) . '<p class="inline">See the Goo.gl API Key option in the Plugin Settings.</p></td>',
			);
		}

	}
}

if ( ! class_exists( 'ngfbSocialTwitter' ) && class_exists( 'ngfbSocial' ) ) {

	class ngfbSocialTwitter extends ngfbSocial {

		private $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
		}

		public function get_html( $atts = array() ) {
			global $post; 
			$html = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $this->ngfb->get_sharing_url( 'notrack', null, $use_post );
			if ( empty( $atts['caption'] ) ) 
				$atts['caption'] = $this->ngfb->get_caption( $this->ngfb->options['twitter_caption'], $this->ngfb->options['twitter_cap_len'], $use_post );

			$long_url = $atts['url'];
			$atts['url'] = $this->get_short_url( $atts['url'], $this->ngfb->options['twitter_shorten'] );
			$twitter_dnt = $this->ngfb->options['twitter_dnt'] ? 'true' : 'false';
			$lang = empty( $this->ngfb->options['twitter_lang'] ) ? 'en' : $this->ngfb->options['twitter_lang'];

			$html = '
				<!-- Twitter Button -->
				<!-- URL = ' . $long_url . ' -->
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
			$this->ngfb->debug->push( 'returning html (' . strlen( $html ) . ' chars)' );
			return $html;
		}
		
		public function get_js( $pos = 'id' ) {
			return '<script type="text/javascript" id="twitter-script-' . $pos . '">
				ngfb_header_js( "twitter-script-' . $pos . '", "' . $this->get_cache_url( 'https://platform.twitter.com/widgets.js' ) . '" );
			</script>' . "\n";
		}

	}

}
?>
