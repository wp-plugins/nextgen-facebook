<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbPostMeta' ) ) {

	class ngfbPostMeta {

		protected $p;

		// executed by ngfbPostMetaPro() as well
		// children executing this __construct() should have an empty add_actions() method
		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
			$this->add_actions();
		}

		protected function add_actions() {
			if ( is_admin() ) {
				add_action( 'add_meta_boxes', array( &$this, 'add_metaboxes' ) );
				add_action( 'save_post', array( &$this, 'save_options' ) );
				add_action( 'edit_attachment', array( &$this, 'save_options' ) );
			}
		}

		public function add_metaboxes() {
			foreach ( get_post_types( array( 'show_ui' => true ), 'objects' ) as $post_type )
				if ( ! empty( $this->p->options[ 'plugin_add_to_'.$post_type->name ] ) ) {
					add_meta_box( NGFB_META_NAME, $this->p->menuname.' Custom Settings', 
						array( &$this->p->meta, 'show_metabox' ), $post_type->name, 'advanced', 'high' );
					add_meta_box( '_'.$this->p->acronym.'_share', $this->p->menuname.' Sharing', 
						array( &$this->p->meta, 'show_sharing' ), $post_type->name, 'side', 'high' );
				}
		}

		public function show_sharing( $post ) {
			$post_type = get_post_type_object( $post->post_type );	// since 3.0
			$post_type_name = ucfirst( $post_type->name );
			echo '<table class="ngfb-settings side"><tr><td>';
			if ( get_post_status( $post->ID ) == 'publish' ) {
				$content = '';
				$opts = array_merge( $this->p->options, $this->p->opt->admin_sharing );
				echo $this->p->social->filter( $content, 'admin_sharing', $opts );
			} else echo '<p class="centered">In order to share this '.$post_type_name.', it must first be published with public visibility.</p>';
			echo '</td></tr></table>';
		}

		public function show_metabox( $post ) {
			$show_tabs = array( 
				'header' => 'Webpage Header', 
				'social' => 'Social Sharing', 
				'tools' => 'Validation Tools',
			);
			$tab_rows = array();
			foreach ( $show_tabs as $key => $title )
				$tab_rows[$key] = $this->get_rows( $key, $post );
			$this->p->util->do_tabs( 'meta', $show_tabs, $tab_rows, '#'.$this->p->acronym.'_meta' );
		}

		protected function get_rows( $id, $post ) {
			$ret = array();
			$post_type = get_post_type_object( $post->post_type );	// since 3.0
			$post_type_name = ucfirst( $post_type->name );
			$twitter_cap_len = $this->p->util->tweet_max_len( get_permalink( $post->ID ) );
			$pid = '';
			$vid_url = '';

			if ( empty( $pid ) ) {
				if ( $this->p->is_avail['postthumb'] == true && has_post_thumbnail( $post->ID ) )
					$pid = get_post_thumbnail_id( $post->ID );
				else $pid = $this->p->media->get_first_attached_image_id( $post->ID );
				if ( empty( $vid_url ) ) {
					$videos = array();
					$videos = $this->p->media->get_content_videos( 1, false );	// get the first video, if any
					if ( ! empty( $videos[0]['og:video'] ) ) $vid_url = $videos[0]['og:video'];
				}
			}

			switch ( $id ) {

				case 'header' :

					$ret[] = '<td colspan="2" align="center">' . $this->p->msg->get( 'pro_feature' ) . '</td>';

					$ret[] = $this->p->util->th( 'Topic', 'medium', null, 
					'A custom topic for this '.$post_type_name.', different from the default Website Topic chosen in the General Settings.' ) .
					'<td class="blank">'.$this->p->options['og_art_section'].'</td>';

					$ret[] = $this->p->util->th( 'Default Title', 'medium', null, 
					'A custom title for the Open Graph meta tags, Twitter Card meta tags (all Twitter Card formats), 
					and possibly the Pinterest, Tumblr, and Twitter sharing caption / text, depending on some option 
					settings. The default title value is refreshed when the (draft or published) '.$post_type_name.' is saved.' ) .
					'<td class="blank">'.$this->p->webpage->get_title( $this->p->options['og_title_len'], '...', true ).'</td>';
		
					$ret[] = $this->p->util->th( 'Default Description', 'medium', null, 
					'A custom description for the Open Graph meta tags, and the fallback description 
					for all other meta tags and social sharing buttons.
					The default description value is based on the content, or excerpt if one is available, 
					and is refreshed when the (draft or published) ' . $post_type_name . ' is saved.
					Update and save this description to change the default value of all other meta tag and 
					social sharing button descriptions.' ) .
					'<td class="blank">'.$this->p->webpage->get_description( $this->p->options['og_desc_len'], '...', true, false ).'</td>';
		
					$ret[] = $this->p->util->th( 'Google Description', 'medium', null, 
					'A custom description for the Google Search description meta tag.
					The default description value is refreshed when the ' . $post_type_name . ' is saved.' ) .
					'<td class="blank">'.$this->p->webpage->get_description( $this->p->options['meta_desc_len'], '...', true ).'</td>';
		
					$ret[] = $this->p->util->th( 'Twitter Card Description', 'medium', null, 
					'A custom description for the Twitter Card description meta tag (all Twitter Card formats).
					The default description value is refreshed when the ' . $post_type_name . ' is saved.' ) .
					'<td class="blank">'.$this->p->webpage->get_description( $this->p->options['tc_desc_len'], '...', true ).'</td>';
		
					$ret[] = $this->p->util->th( 'Image ID', 'medium', null, 
					'A custom Image ID to include (or list first) in the Open Graph meta tags, 
					\'Large Image Summary\' Twitter Card meta tag, Pinterest and Tumblr social
					sharing buttons (this is the image they will share).' ) .
					'<td class="blank">&nbsp;</td>';
		
					$ret[] = $this->p->util->th( 'Image URL', 'medium', null, 
					'A custom image URL, instead of an Image ID, to include (or list first)
					in the Open Graph and \'Large Image Summary\' Twitter Card meta tags.' ) .
					'<td class="blank">&nbsp;</td>';
		
					$ret[] = $this->p->util->th( 'Video URL', 'medium', null, 
					'A custom video URL, from YouTube or Vimeo, to include (or list first) in the 
					Open Graph meta tags, \'Player\' Twitter Card meta tag, and the Tumblr social 
					sharing button (this is the video that will be shared).' ) .
					'<td class="blank">&nbsp;</td>';
		
					$ret[] = $this->p->util->th( 'Maximum Images', 'medium', null, 
					'The maximum number of images to include in the Open Graph meta tags for this ' . $post_type_name . '.' ) .
					'<td class="blank">'.$this->p->options['og_img_max'].'</td>';
		
					$ret[] = $this->p->util->th( 'Maximum Videos', 'medium', null, 
					'The maximum number of embedded videos to include in the Open Graph meta tags for this ' . $post_type_name . '.' ) .
					'<td class="blank">'.$this->p->options['og_vid_max'].'</td>';
		
					$ret[] = $this->p->util->th( 'Disable Social Buttons', 'medium', null, 
					'Disable all social sharing buttons (content, excerpt, widget, shortcode) for this ' . $post_type_name . '.' ) .
					'<td class="blank">&nbsp;</td>';
		
					break;

				case 'social' :

					$ret[] = '<td colspan="2" align="center">' . $this->p->msg->get( 'pro_feature' ) . '</td>';

					$th = $this->p->util->th( 'Pinterest Image Caption', 'medium', null, 
					'A custom caption text, used by the Pinterest social sharing button, 
					for the custom Image ID, attached or featured image.' );
					if ( ! empty( $pid ) )
						$ret[] = $th . '<td class="blank">'.$this->p->webpage->get_caption( $this->p->options['pin_caption'], 
							$this->p->options['pin_cap_len'], true ).'</td>';
					else $ret[] = $th . '<td class="blank"><em>No custom Image ID, featured or attached image found.</em></td>';

					$th = $this->p->util->th( 'Tumblr Image Caption', 'medium', null, 
					'A custom caption, used by the Tumblr social sharing button, 
					for the custom Image ID, attached or featured image.' );
					if ( ! empty( $pid ) )
						$ret[] = $th . '<td class="blank">'.$this->p->webpage->get_caption( $this->p->options['tumblr_caption'], 
							$this->p->options['tumblr_cap_len'], true ).'</td>';
					else $ret[] = $th . '<td class="blank"><em>No custom Image ID, featured or attached image found.</em></td>';

					$th = $this->p->util->th( 'Tumblr Video Caption', 'medium', null, 
					'A custom caption, used by the Tumblr social sharing button, 
					for the custom Video URL or embedded video.' );
					if ( ! empty( $vid_url ) )
						$ret[] = $th . '<td class="blank">'.$this->p->webpage->get_caption( $this->p->options['tumblr_caption'], 
							$this->p->options['tumblr_cap_len'], true ).'</td>';
					else $ret[] = $th . '<td class="blank"><em>No custom Video URL or embedded video found.</em></td>';

					$ret[] = $this->p->util->th( 'Tweet Text', 'medium', null, 
					'A custom Tweet text for the Twitter social sharing button. 
					This text is in addition to any Twitter Card description.' ) .
					'<td class="blank">'.$this->p->webpage->get_caption( $this->p->options['twitter_caption'], $twitter_cap_len ).'</td>';

					break;

				case 'tools' :	

					$ret = $this->get_rows_tools( $post );

					break; 

			}
			return $ret;
		}

		protected function get_rows_tools( $post ) {
			$tools = array();
			$post_type = get_post_type_object( $post->post_type );	// since 3.0
			$post_type_name = ucfirst( $post_type->name );

			if ( get_post_status( $post->ID ) == 'publish' ) {

				$tools[] = $this->p->util->th( '<a href="https://developers.facebook.com/tools/debug/og/object?q=' . urlencode( get_permalink( $post->ID ) ) . '" 
					target="_blank">Facebook Debugger</a>' ) .
					'<td><p>Verify the Open Graph meta tags and refresh the Facebook cache for this ' . $post_type_name . '.</p></td>';
	
				$tools[] = $this->p->util->th( '<a href="http://www.google.com/webmasters/tools/richsnippets?q=' . urlencode( get_permalink( $post->ID ) ) . '" 
					target="_blank">Google Structured Data Testing Tool</a>' ) .
					'<td><p>Check that Google can correctly parse your structured data markup and display it in search results.</p></td>';
	
				$tools[] = $this->p->util->th( '<a href="http://developers.pinterest.com/rich_pins/validator/?link=' . urlencode( get_permalink( $post->ID ) ) . '" 
					target="_blank">Pinterest Rich Pin Validator</a>' ) .
					'<td><p>Validate the Rich Pins meta tags and apply to get them on Pinterest.</p></td>';
	
				$tools[] = $this->p->util->th( '<a href="https://dev.twitter.com/docs/cards/validation/validator" 
					target="_blank">Twitter Card Validator</a>' ) .
					'<td><p>The Twitter Card Validator does not accept query arguments. Copy-paste the following URL into the validation input field.</p>' . 
					ngfbForm::get_text( get_permalink( $post->ID ), 'wide' ) . '</td>';

			} else $tools[] = '<td><p class="centered">In order to access the Validation Tools, the '.$post_type_name.' must first be published with public visibility.</p></td>';

			return $tools;
		}

                public function get_options( $post_id, $idx = '' ) {
			if ( ! empty( $idx ) ) return false;
			else return array();
		}

		public function save_options( $post_id ) {
			$this->p->util->flush_post_cache( $post_id );
		}
	}
}

?>
