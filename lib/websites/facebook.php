<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbSettingsFacebook' ) && class_exists( 'ngfbSettingsSocialSharing' ) ) {

	class ngfbSettingsFacebook extends ngfbSettingsSocialSharing {

		public $lang = array(
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

		protected $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
		}

		public function get_rows() {
			return array(
				'<th class="short">Add to Content Text</th><td>' . $this->ngfb->admin->form->get_checkbox( 'fb_on_the_content' ) . '</td>',
				'<th class="short">Add to Excerpt Text</th><td>' . $this->ngfb->admin->form->get_checkbox( 'fb_on_the_excerpt' ) . '</td>',
				'<th class="short">Preferred Order</th><td>' . $this->ngfb->admin->form->get_select( 'fb_order', range( 1, count( $this->ngfb->social_prefix ) ), 'short' ) . '</td>',
				'<th class="short">JavaScript in</th><td>' . $this->ngfb->admin->form->get_select( 'fb_js_loc', $this->js_locations ) . '</td>',
				'<th class="short">Language</th><td>' . $this->ngfb->admin->form->get_select( 'fb_lang', $this->lang ) . '</td>',
				'<th class="short">Markup Language</th><td>' . $this->ngfb->admin->form->get_select( 'fb_markup', 
					array( 
						'html5' => 'HTML5', 
						'xfbml' => 'XFBML',
					) 
				) . '</td>',
				'<th class="short">Include Send</th><td>' . $this->ngfb->admin->form->get_checkbox( 'fb_send' ) . '</td>',
				'<th class="short">Layout</th><td>' . $this->ngfb->admin->form->get_select( 'fb_layout', 
					array(
						'standard' => 'Standard',
						'button_count' => 'Button Count',
						'box_count' => 'Box Count',
					) 
				) . '</td>',
				'<th class="short">Default Width</th><td>' . $this->ngfb->admin->form->get_input( 'fb_width', 'short' ) . '</td>',
				'<th class="short">Show Faces</th><td>' . $this->ngfb->admin->form->get_checkbox( 'fb_show_faces' ) . ' (standard layout only)</td>',
				'<th class="short">Font</th><td>' . $this->ngfb->admin->form->get_select( 'fb_font', 
					array( 
						'arial' => 'Arial',
						'lucida grande' => 'Lucida Grande',
						'segoe ui' => 'Segoe UI',
						'tahoma' => 'Tahoma',
						'trebuchet ms' => 'Trebuchet MS',
						'verdana' => 'Verdana',
					) 
				) . '</td>',
				'<th class="short">Color Scheme</th><td>' . $this->ngfb->admin->form->get_select( 'fb_colorscheme', 
					array( 
						'light' => 'Light',
						'dark' => 'Dark',
					)
				) . '</td>',
				'<th class="short">Facebook Action Name</th><td>' . $this->ngfb->admin->form->get_select( 'fb_action', 
					array( 
						'like' => 'Like',
						'recommend' => 'Recommend',
					)
				) . '</td>',
			);
		}

	}
}

if ( ! class_exists( 'ngfbSocialFacebook' ) && class_exists( 'ngfbSocial' ) ) {

	class ngfbSocialFacebook extends ngfbSocial {

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
			$fb_send = $this->ngfb->options['fb_send'] ? 'true' : 'false';
			$fb_show_faces = $this->ngfb->options['fb_show_faces'] ? 'true' : 'false';

			switch ( $this->ngfb->options['fb_markup'] ) {
				case 'xfbml' :
					// XFBML
					$html = '
					<!-- Facebook Button -->
					<div ' . $this->get_css( 'facebook', $atts, 'fb-like' ) . '><fb:like 
						href="' . $atts['url'] . '" 
						send="' . $fb_send . '" 
						layout="' . $this->ngfb->options['fb_layout'] . '" 
						show_faces="' . $fb_show_faces . '" 
						font="' . $this->ngfb->options['fb_font'] . '" 
						action="' . $this->ngfb->options['fb_action'] . '" 
						colorscheme="' . $this->ngfb->options['fb_colorscheme'] . '"></fb:like></div>
					';
					break;
				case 'html5' :
				default :
					// HTML5
					$html = '
					<!-- Facebook Button -->
					<div ' . $this->get_css( 'facebook', $atts, 'fb-like' ) . '
						data-href="' . $atts['url'] . '"
						data-send="' . $fb_send . '" 
						data-layout="' . $this->ngfb->options['fb_layout'] . '" 
						data-width="' . $this->ngfb->options['fb_width'] . '" 
						data-show-faces="' . $fb_show_faces . '" 
						data-font="' . $this->ngfb->options['fb_font'] . '" 
						data-action="' . $this->ngfb->options['fb_action'] . '"
						data-colorscheme="' . $this->ngfb->options['fb_colorscheme'] . '"></div>
					';
					break;
			}
			$this->ngfb->debug->log( 'returning html (' . strlen( $html ) . ' chars)' );
			return $html;
		}
		
		public function get_js( $pos = 'id' ) {
			$lang = empty( $this->ngfb->options['fb_lang'] ) ? 'en_US' : $this->ngfb->options['fb_lang'];
			$app_id = empty( $this->ngfb->options['fb_app_id'] ) ? '' : $this->ngfb->options['fb_app_id'];
			return '<script type="text/javascript" id="facebook-script-' . $pos . '">
				ngfb_header_js( "facebook-script-' . $pos . '", "' . 
					$this->ngfb->util->get_cache_url( 'https://connect.facebook.net/' . 
					$lang . '/all.js#xfbml=1&appId=' . $app_id ) . '" );
			</script>' . "\n";
		}

	}

}
?>
