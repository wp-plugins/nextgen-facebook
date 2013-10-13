<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbSettingsPinterest' ) && class_exists( 'ngfbSettingsSocialSharing' ) ) {

	class ngfbSettingsPinterest extends ngfbSettingsSocialSharing {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get_rows() {
			return array(
				$this->p->util->th( 'Show Button in', 'short', null,
				'The Pinterest "Pin It" button appears only on Posts and Pages with a <em>featured</em> or <em>attached</em> image.' ) . '<td>' . 
				$this->p->admin->form->get_checkbox( 'pin_on_the_content' ) . ' Content&nbsp; ' . 
				$this->p->admin->form->get_checkbox( 'pin_on_the_excerpt' ) . ' Excerpt&nbsp; ' . 
				$this->p->admin->form->get_checkbox( 'pin_on_admin_sharing' ) . ' Admin Sharing' . 
				'</td>',

				$this->p->util->th( 'Preferred Order', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'pin_order', 
					range( 1, count( $this->p->admin->settings['social']->website ) ), 'short' ) . '</td>',

				$this->p->util->th( 'JavaScript in', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'pin_js_loc', $this->js_locations ) . '</td>',

				$this->p->util->th( 'Pin Count Layout', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'pin_count_layout', 
					array( 
						'none' => '',
						'horizontal' => 'Horizontal',
						'vertical' => 'Vertical',
					)
				) . '</td>',

				$this->p->util->th( 'Image Size to Share', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select_img_size( 'pin_img_size' ) . '</td>',

				$this->p->util->th( 'Image Caption Text', 'short' ) . '<td>' . 
				$this->p->admin->form->get_select( 'pin_caption', $this->captions ) . '</td>',

				$this->p->util->th( 'Caption Length', 'short' ) . '<td>' . 
				$this->p->admin->form->get_input( 'pin_cap_len', 'short' ) . ' Characters or less</td>',
			);
		}

	}
}

if ( ! class_exists( 'ngfbSocialPinterest' ) && class_exists( 'ngfbSocial' ) ) {

	class ngfbSocialPinterest {

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
			$query = '';
			$prot = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			$src_id = $this->p->util->get_src_id( 'pinterest', $atts );
			$atts['url'] = empty( $atts['url'] ) ? 
				$this->p->util->get_sharing_url( 'notrack', null, $use_post, $src_id ) : 
				$this->p->util->get_sharing_url( 'asis', $atts['url'], null, $src_id );
			if ( empty( $atts['size'] ) ) $atts['size'] = $opts['pin_img_size'];
			if ( empty( $atts['photo'] ) ) {
				// get the pid
				if ( empty( $atts['pid'] ) ) {
					// allow on index pages only if in content (not a widget)
					if ( ! empty( $post ) && $use_post == true ) {
						$pid = $this->p->meta->get_options( $post->ID, 'og_img_id' );
						$pre = $this->p->meta->get_options( $post->ID, 'og_img_id_pre' );
						if ( ! empty( $pid ) ) 
							$atts['pid'] = $pre == 'ngg' ? 'ngg-' . $pid : $pid;
						elseif ( $this->p->is_avail['postthumb'] == true && has_post_thumbnail( $post->ID ) )
							$atts['pid'] = get_post_thumbnail_id( $post->ID );
						else $atts['pid'] = $this->p->media->get_first_attached_image_id( $post->ID );
					}
				}
				if ( ! empty( $atts['pid'] ) ) {
					// if the post thumbnail id has the form 'ngg-' then it's a NextGEN image
					if ( is_string( $atts['pid'] ) && substr( $atts['pid'], 0, 4 ) == 'ngg-' ) {
						list( $atts['photo'], $atts['width'], $atts['height'], 
							$atts['cropped'] ) = $this->p->media->ngg->get_image_src( $atts['pid'], $atts['size'], false );
					} else {
						list( $atts['photo'], $atts['width'], $atts['height'],
							$atts['cropped'] ) = $this->p->media->get_attachment_image_src( $atts['pid'], $atts['size'], false );
					}
				}
			}
			if ( empty( $atts['photo'] ) ) return;

			if ( empty( $atts['pin_count_layout'] ) ) 
				$atts['pin_count_layout'] = $opts['pin_count_layout'];

			if ( empty( $atts['caption'] ) && $use_post == true ) 
				$atts['caption'] = $this->p->meta->get_options( $post->ID, 'pin_desc' );

			if ( empty( $atts['caption'] ) ) 
				$atts['caption'] = $this->p->webpage->get_caption( $opts['pin_caption'], 
					$opts['pin_cap_len'], $use_post );

			$query .= 'url=' . urlencode( $atts['url'] );
			$query .= '&amp;media='. urlencode( $atts['photo'] );
			$query .= '&amp;description=' . urlencode( $this->p->util->decode( $atts['caption'] ) );

			$html = '<!-- Pinterest Button --><div '.$this->p->social->get_css( 'pinterest', $atts ).'><a href="'.$prot.'pinterest.com/pin/create/button/?'.$query.'" class="pin-it-button" count-layout="'.$atts['pin_count_layout'].'" title="Share on Pinterest"><img border="0" alt="Pin It" src="'.$this->p->util->get_cache_url( $prot.'assets.pinterest.com/images/PinExt.png' ).'" /></a></div>'."\n";
			$this->p->debug->log( 'returning html ('.strlen( $html ).' chars)' );
			return $html;
		}

		public function get_js( $pos = 'id' ) {
			$this->p->debug->mark();
			$prot = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
			return '<script type="text/javascript" id="pinterest-script-'.$pos.'">ngfb_header_js( "pinterest-script-'.$pos.'", "'.$this->p->util->get_cache_url( $prot.'assets.pinterest.com/js/pinit.js' ).'" );</script>'."\n";
		}
		
	}

}
?>
