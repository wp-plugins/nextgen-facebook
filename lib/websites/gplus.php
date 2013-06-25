<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbSettingsGooglePlus' ) && class_exists( 'ngfbSettingsSocialSharing' ) ) {

	class ngfbSettingsGooglePlus extends ngfbSettingsSocialSharing {

		public $lang = array(
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

		protected $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
		}

		public function get_rows() {
			return array(
				'<th class="short">Add Button to</th><td>' . 
					$this->ngfb->admin->form->get_checkbox( 'gp_on_the_content' ) . ' the Content and / or ' . 
					$this->ngfb->admin->form->get_checkbox( 'gp_on_the_excerpt' ) . ' the Excerpt Text</td>',
				'<th class="short">Preferred Order</th><td>' . $this->ngfb->admin->form->get_select( 'gp_order', range( 1, count( $this->ngfb->social_prefix ) ), 'short' ) . '</td>',
				'<th class="short">JavaScript in</th><td>' . $this->ngfb->admin->form->get_select( 'gp_js_loc', $this->js_locations ) . '</td>',
				'<th class="short">Language</th><td>' . $this->ngfb->admin->form->get_select( 'gp_lang', $this->lang ) . '</td>',
				'<th class="short">Button Type</th><td>' . $this->ngfb->admin->form->get_select( 'gp_action', 
					array( 
						'plusone' => 'G +1', 
						'share' => 'G+ Share',
					) 
				) . '</td>',
				'<th class="short">Button Size</th><td>' . $this->ngfb->admin->form->get_select( 'gp_size', 
					array( 
						'small' => 'Small [ 15px ]',
						'medium' => 'Medium [ 20px ]',
						'standard' => 'Standard [ 24px ]',
						'tall' => 'Tall [ 60px ]',
					) 
				) . '</td>',
				'<th class="short">Annotation</th><td>' . $this->ngfb->admin->form->get_select( 'gp_annotation', 
					array( 
						'none' => '',
						'inline' => 'Inline',
						'bubble' => 'Bubble',
						'vertical-bubble' => 'Vertical Bubble',
					)
				) . '</td>',
			);
		}

	}
}

if ( ! class_exists( 'ngfbSocialGooglePlus' ) && class_exists( 'ngfbSocial' ) ) {

	class ngfbSocialGooglePlus extends ngfbSocial {

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
			$gp_class = $this->ngfb->options['gp_action'] == 'share' ? 'class="g-plus" data-action="share"' : 'class="g-plusone"';
			$html = '
				<!-- GooglePlus Button -->
				<div ' . $this->get_css( 'gplus', $atts, 'g-plusone-button' ) . '>
					<span '. $gp_class . ' 
						data-size="' . $this->ngfb->options['gp_size'] . '" 
						data-annotation="' . $this->ngfb->options['gp_annotation'] . '" 
						data-href="' . $atts['url'] . '"></span>
				</div>' . "\n";
			$this->ngfb->debug->log( 'returning html (' . strlen( $html ) . ' chars)' );
			return $html;
		}
		
		public function get_js( $pos = 'id' ) {
			return '<script type="text/javascript" id="gplus-script-' . $pos . '">
				ngfb_header_js( "gplus-script-' . $pos . '", "' . $this->ngfb->util->get_cache_url( 'https://apis.google.com/js/plusone.js' ) . '" );
			</script>' . "\n";
		}
		
	}

}
?>
