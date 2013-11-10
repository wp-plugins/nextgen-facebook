<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbPostMeta' ) ) {

	class NgfbPostMeta {

		protected $p;

		// executed by ngfbPostMetaPro() as well
		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
			$this->add_actions();
		}

		protected function add_actions() {
			if ( is_admin() ) {
				if ( ! $this->p->check->is_aop() )
					add_action( 'add_meta_boxes', array( &$this, 'add_metaboxes' ) );

				add_action( 'save_post', array( &$this, 'flush_cache' ), 20 );
				add_action( 'edit_attachment', array( &$this, 'flush_cache' ), 20 );
			}
		}

		public function add_metaboxes() {
			// is there at least one social button enabled?
			$enabled = false;
			foreach ( $this->p->cf['opt']['pre'] as $id => $pre ) {
				if ( ! empty( $this->p->options[$pre.'_on_admin_sharing'] ) ) {
					$enabled = true;
					break;
				}
			}
			foreach ( get_post_types( array( 'show_ui' => true, 'public' => true ), 'objects' ) as $post_type ) {
				if ( ! empty( $this->p->options[ 'plugin_add_to_'.$post_type->name ] ) ) {
					add_meta_box( NGFB_META_NAME, $this->p->cf['menu'].' Custom Settings', 
						array( &$this->p->meta, 'show_metabox' ), $post_type->name, 'advanced', 'high' );
					if ( $enabled == true )
						add_meta_box( '_'.$this->p->cf['lca'].'_share', $this->p->cf['menu'].' Sharing', 
							array( &$this->p->meta, 'show_sharing' ), $post_type->name, 'side', 'high' );
				}
			}
		}

		public function show_sharing( $post ) {
			$post_type = get_post_type_object( $post->post_type );	// since 3.0
			$post_type_name = ucfirst( $post_type->name );
			echo '<table class="sucom-settings side"><tr><td>';
			if ( get_post_status( $post->ID ) == 'publish' ) {
				$content = '';
				$opts = array_merge( $this->p->options, $this->p->opt->admin_sharing );
				$this->p->social->add_header();
				echo $this->p->social->filter( $content, 'admin_sharing', $opts );
				$this->p->social->add_footer();
			} else echo '<p class="centered">In order to share this '.$post_type_name.', it must first be published with public visibility.</p>';
			echo '</td></tr></table>';
		}

		public function show_metabox( $post ) {
			$opts = $this->get_options( $post->ID );	// sanitize when saving, not reading
			$def_opts = $this->get_defaults();
			$this->form = new SucomForm( $this->p, NGFB_META_NAME, $opts, $def_opts );
			wp_nonce_field( $this->get_nonce(), NGFB_NONCE );
			$show_tabs = array( 
				'header' => 'Webpage Header', 
				'social' => 'Social Sharing', 
				'tools' => 'Validation Tools',
			);
			$tab_rows = array();
			foreach ( $show_tabs as $key => $title )
				$tab_rows[$key] = $this->get_rows( $key, $post );
			$this->p->util->do_tabs( 'meta', $show_tabs, $tab_rows );
		}

		protected function get_rows( $id, $post ) {
			$ret = array();
			switch ( $id ) {
				case 'header' :
					$ret = $this->get_rows_header( $post );
					break;
				case 'social' :
					$ret = $this->get_rows_social( $post );
					break;
				case 'tools' :	
					$ret = $this->get_rows_tools( $post );
					break; 
			}
			return $ret;
		}

		protected function get_rows_header( $post ) {
			$ret = array();
			$post_type = get_post_type_object( $post->post_type );	// since 3.0
			$post_type_name = ucfirst( $post_type->name );

			$ret[] = '<td colspan="2" align="center">'.$this->p->msg->get( 'pro_feature' ).'</td>';

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
			and is refreshed when the (draft or published) '.$post_type_name.' is saved.
			Update and save this description to change the default value of all other meta tag and 
			social sharing button descriptions.' ) .
			'<td class="blank">'.$this->p->webpage->get_description( $this->p->options['og_desc_len'], '...', true, false ).'</td>';
	
			$ret[] = $this->p->util->th( 'Google Description', 'medium', null, 
			'A custom description for the Google Search description meta tag.
			The default description value is refreshed when the '.$post_type_name.' is saved.' ) .
			'<td class="blank">'.$this->p->webpage->get_description( $this->p->options['meta_desc_len'], '...', true, true, false ).'</td>';

			$ret[] = $this->p->util->th( 'Twitter Card Description', 'medium', null, 
			'A custom description for the Twitter Card description meta tag (all Twitter Card formats).
			The default description value is refreshed when the '.$post_type_name.' is saved.' ) .
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
			'The maximum number of images to include in the Open Graph meta tags for this '.$post_type_name.'.' ) .
			'<td class="blank">'.$this->p->options['og_img_max'].'</td>';

			$ret[] = $this->p->util->th( 'Maximum Videos', 'medium', null, 
			'The maximum number of embedded videos to include in the Open Graph meta tags for this '.$post_type_name.'.' ) .
			'<td class="blank">'.$this->p->options['og_vid_max'].'</td>';

			$ret[] = $this->p->util->th( 'Disable Social Buttons', 'medium', null, 
			'Disable all social sharing buttons (content, excerpt, widget, shortcode) for this '.$post_type_name.'.' ) .
			'<td class="blank">&nbsp;</td>';

			return $ret;
		}
		
		protected function get_rows_social( $post ) {
			$ret = array();
			$post_type = get_post_type_object( $post->post_type );	// since 3.0
			$post_type_name = ucfirst( $post_type->name );
			$twitter_cap_len = $this->p->util->tweet_max_len( get_permalink( $post->ID ) );
			$pid = $this->p->meta->get_options( $post->ID, 'og_img_id' );
			$vid_url = $this->p->meta->get_options( $post->ID, 'og_vid_url' );

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

			$ret[] = '<td colspan="2" align="center">'.$this->p->msg->get( 'pro_feature' ).'</td>';

			$th = $this->p->util->th( 'Pinterest Image Caption', 'medium', null, 
			'A custom caption text, used by the Pinterest social sharing button, 
			for the custom Image ID, attached or featured image.' );
			if ( ! empty( $pid ) )
				$ret[] = $th.'<td class="blank">'.
				$this->p->webpage->get_caption( $this->p->options['pin_caption'], $this->p->options['pin_cap_len'] ).'</td>';
			else $ret[] = $th.'<td class="blank"><em>No custom Image ID, featured or attached image found.</em></td>';

			$th = $this->p->util->th( 'Tumblr Image Caption', 'medium', null, 
			'A custom caption, used by the Tumblr social sharing button, 
			for the custom Image ID, attached or featured image.' );
			if ( ! empty( $pid ) )
				$ret[] = $th.'<td class="blank">'.
				$this->p->webpage->get_caption( $this->p->options['tumblr_caption'], $this->p->options['tumblr_cap_len'] ).'</td>';
			else $ret[] = $th.'<td class="blank"><em>No custom Image ID, featured or attached image found.</em></td>';

			$th = $this->p->util->th( 'Tumblr Video Caption', 'medium', null, 
			'A custom caption, used by the Tumblr social sharing button, 
			for the custom Video URL or embedded video.' );
			if ( ! empty( $vid_url ) )
				$ret[] = $th.'<td class="blank">'.
				$this->p->webpage->get_caption( $this->p->options['tumblr_caption'], $this->p->options['tumblr_cap_len'] ).'</td>';
			else $ret[] = $th.'<td class="blank"><em>No custom Video URL or embedded video found.</em></td>';

			$ret[] = $this->p->util->th( 'Tweet Text', 'medium', null, 
			'A custom Tweet text for the Twitter social sharing button. 
			This text is in addition to any Twitter Card description.' ) .
			'<td class="blank">'.$this->p->webpage->get_caption( $this->p->options['twitter_caption'], $twitter_cap_len,
				true, true, true ).'</td>';	// use_post = true, use_cache = true, add_hashtags = true

			return $ret;
		}
		
		protected function get_rows_tools( $post ) {
			$ret = array();
			$post_type = get_post_type_object( $post->post_type );	// since 3.0
			$post_type_name = ucfirst( $post_type->name );

			if ( get_post_status( $post->ID ) == 'publish' ) {

				$ret[] = $this->p->util->th( 'Facebook Debugger' ).'
				<td class="validate"><p>Verify the Open Graph and Rich Pin meta tags, and refresh the Facebook cache for this '.$post_type_name.'.</p></td>
				<td class="validate">'.$this->form->get_button( 'Validate Open Graph', 'button-primary', null, 
					'https://developers.facebook.com/tools/debug/og/object?q='.urlencode( get_permalink( $post->ID ) ), true ).'</td>';
	
				$ret[] = $this->p->util->th( 'Google Structured Data Testing Tool' ).'
				<td class="validate"><p>Check that Google can correctly parse your structured data markup and display it in search results.</p></td>
				<td class="validate">'.$this->form->get_button( 'Validate Data Markup', 'button-primary', null, 
					'http://www.google.com/webmasters/tools/richsnippets?q='.urlencode( get_permalink( $post->ID ) ), true ).'</td>';
	
				$ret[] = $this->p->util->th( 'Pinterest Rich Pin Validator' ).'
				<td class="validate"><p>Validate the Open Graph / Rich Pin meta tags, and apply to display them on Pinterest.</p></td>
				<td class="validate">'.$this->form->get_button( 'Validate Rich Pins', 'button-primary', null, 
					'http://developers.pinterest.com/rich_pins/validator/?link='.urlencode( get_permalink( $post->ID ) ), true ).'</td>';
	
				$ret[] = $this->p->util->th( 'Twitter Card Validator' ).'
				<td class="validate"><p>The Twitter Card Validator does not accept query arguments -- copy-paste the following URL into the validation input field.
				To enable the display of Twitter Card information in tweets you must submit a URL for each type of card for approval.</p>'.
				$this->form->get_text( get_permalink( $post->ID ), 'wide' ).'</td>
				<td class="validate">'.$this->form->get_button( 'Validate Twitter Card', 'button-primary', null, 
					'https://dev.twitter.com/docs/cards/validation/validator', true ).'</td>';

			} else $ret[] = '<td><p class="centered">In order to access the Validation Tools, the '.$post_type_name.' must first be published with public visibility.</p></td>';

			return $ret;
		}

                public function get_options( $post_id, $idx = '' ) {
			if ( ! empty( $idx ) ) return false;
			else return array();
		}

		public function get_defaults( $idx = '' ) {
			if ( ! empty( $idx ) ) return false;
			else return array();
		}

		public function flush_cache( $post_id ) {
			$this->p->util->flush_post_cache( $post_id );
		}

		protected function get_nonce() {
			return plugin_basename( __FILE__ );
		}
	}
}

?>
