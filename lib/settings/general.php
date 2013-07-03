<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbSettingsGeneral' ) && class_exists( 'ngfbAdmin' ) ) {

	class ngfbSettingsGeneral extends ngfbAdmin {

		protected $ngfb;
		protected $menu_id;
		protected $menu_name;
		protected $pagehook;

		public function __construct( &$ngfb_plugin, $id, $name ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
			$this->menu_id = $id;
			$this->menu_name = $name;
		}

		protected function add_meta_boxes() {
			// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
			add_meta_box( $this->pagehook . '_opengraph', 'Open Graph Settings', array( &$this, 'show_metabox_opengraph' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_facebook', 'Facebook Settings', array( &$this, 'show_metabox_facebook' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_google', 'Google Settings', array( &$this, 'show_metabox_google' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_twitter', 'Twitter Settings', array( &$this, 'show_metabox_twitter' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_taglist', 'Meta Tag List', array( &$this, 'show_metabox_taglist' ), $this->pagehook, 'normal' );
		}

		public function show_metabox_opengraph() {

			echo '<table class="ngfb-settings"><tr><td colspan="3"><h3>Title and Description Options</h3></td></tr><tr>';

			echo $this->ngfb->util->th( 'Website Topic', 'highlight', null, '
				The topic that best describes the Posts and Pages on your website.
				This name will be used in the \'article:section\' Open Graph meta tag. 
				Select \'[none]\' if you prefer to exclude the \'article:section\' meta tag.
				Aside from this global option, the Pro version also allows the selection of a 
				Topic for each individual Post and Page.' );
			echo '<td>', $this->ngfb->admin->form->get_select( 'og_art_section', $this->ngfb->util->get_topics() ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Title Separator', 'highlight', null, '
				One or more characters used to separate values (category parent names, page numbers, etc.) within the Open Graph title string 
				(default is \'' . $this->ngfb->opt->get_defaults( 'og_title_sep' ) . '\').' );
			echo '<td>', $this->ngfb->admin->form->get_input( 'og_title_sep', 'short' ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Title Length', null, null, '
				The maximum length of text used in the Open Graph title tag 
				(default is ' . $this->ngfb->opt->get_defaults( 'og_title_len' ) . ' characters).' );
			echo '<td>', $this->ngfb->admin->form->get_input( 'og_title_len', 'short' ), ' Characters or less</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Description Length', null, null, '
				The maximum length of text used in the Open Graph description tag. 
				The length should be at least ' . NGFB_MIN_DESC_LEN . ' characters or more, and the
				default is ' . $this->ngfb->opt->get_defaults( 'og_desc_len' ) . ' characters.
				' );
			echo '<td>', $this->ngfb->admin->form->get_input( 'og_desc_len', 'short' ), ' Characters or less</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Content Begins at First Paragraph', null, null, '
				For a Page or Post <em>without</em> an excerpt, if this option is checked, 
				the plugin will ignore all text until the first html paragraph tag in the content. 
				If an excerpt exists, then this option is ignored, and the complete text of that 
				excerpt is used instead.' );
			echo '<td>', $this->ngfb->admin->form->get_checkbox( 'og_desc_strip' ), '</td>';

			echo '</tr><tr><td colspan="3"><h3>Authorship Options</h3></td></tr><tr>';

			echo $this->ngfb->util->th( 'Author Profile URL', null, null, '
				Select the profile field to use for Posts and Pages in the \'article:author\' Open Graph meta tag.
				The URL should point to an author\'s <em>personal</em> website or social page.
				This Open Graph meta tag is primarily used by Facebook, so the preferred (and default) 
				value is the author\'s Facebook webpage URL.
				See the Google Settings below for an <em>Author Link URL</em> for Google, 
				and to define a common <em>publisher</em> URL for all webpages.' );
			echo '<td>', $this->ngfb->admin->form->get_select( 'og_author_field', $this->author_fields() ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Fallback to Author Index', null, null, '
				If the <em>Author Profile URL</em> (and the <em>Author Link URL</em> in the Google Settings below) 
				is not a valid URL, then ' . $this->ngfb->fullname . ' can fallback to using the author index on this 
				website (\'' . trailingslashit( site_url() ) . 'author/username\' for example). 
				Uncheck this option to disable the fallback feature (default is unchecked).' );
			echo '<td>', $this->ngfb->admin->form->get_checkbox( 'og_author_fallback' ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Default Author', null, null, '
				A default author for webpages missing authorship information (for example, an index webpage without posts). 
				If you have several authors on your website, you should probably leave this option set to <em>[none]</em> (the default).' );
			$user_ids = array( '' );
			foreach ( get_users() as $user ) $user_ids[$user->ID] = $user->display_name;
			echo '<td>', $this->ngfb->admin->form->get_select( 'og_def_author_id', $user_ids, null, null, true ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Default Author on Indexes', null, null, '
				Check this option if you would like to force the <em>Default Author</em> on index webpages 
				(homepage, archives, categories, author, etc.). 
				If this option is checked, index webpages will be labeled as a an \'article\' with authorship 
				attributed to the <em>Default Author </em> (default is unchecked).
				If the <em>Default Author</em> is <em>[none]</em>, then the index webpages will be labeled as a \'webpage\'.' );
			echo '<td>', $this->ngfb->admin->form->get_checkbox( 'og_def_author_on_index' ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Default Author on Search Results', null, null, '
				Check this option if you would like to force the <em>Default Author</em> on search result webpages as well.' );
			echo '<td>', $this->ngfb->admin->form->get_checkbox( 'og_def_author_on_search' ), '</td>';

			echo '</tr><tr><td colspan="3"><h3>Image and Video Options</h3></td></tr><tr>';

			echo $this->ngfb->util->th( 'Image Dimensions', 'highlight', null, '
				Enter the dimension of images used in the Open Graph meta tags. The width and height must be 
				between ' . NGFB_MIN_IMG_SIZE . 'x' . NGFB_MIN_IMG_SIZE . ' and 1500x1500, preferably cropped 
				(the defaults are ' . $this->ngfb->opt->get_defaults( 'og_img_width' ) . 'x' .
				$this->ngfb->opt->get_defaults( 'og_img_height' ) . ', ' .
				( $this->ngfb->opt->get_defaults( 'og_img_crop' ) == 0 ? 'not ' : '' ) . 'cropped). 
				Note that Facebook prefers larger images for use in backgrounds and banners.
				The default values are purposefully low in consideration of photography websites, 
				who may not want to share larger images of their work.' );
			echo '<td>Width ', $this->ngfb->admin->form->get_input( 'og_img_width', 'short' ), ' x ',
				'Height ', $this->ngfb->admin->form->get_input( 'og_img_height', 'short' ), 
				'Cropped ', $this->ngfb->admin->form->get_checkbox( 'og_img_crop' ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Default Image ID', 'highlight', null, '
				The ID number and location of your default image (example: 123). The <em>Default Image ID</em> 
				will be used as a fallback for Posts and Pages that do not have any images <em>featured</em>, 
				<em>attached</em>, or in their content. The Image ID number for images in the 
				WordPress Media Library can be found in the URL when editing an image (post=123 in the URL, for example). 
				The NextGEN Gallery Image IDs are easier to find -- it\'s the number in the first column when viewing a Gallery.' );
			$id_pre = array( 'wp' => 'Media Library' );
			echo '<td>', $this->ngfb->admin->form->get_input( 'og_def_img_id', 'short' ), ' in the ';
			if ( $this->ngfb->is_avail['ngg'] == true )
				$id_pre['ngg'] = 'NextGEN Gallery';
			echo $this->ngfb->admin->form->get_select( 'og_def_img_id_pre', $id_pre, 'medium' ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Default Image URL', null, null, '
				You can also specify a <em>Default Image URL</em> (including the http:// prefix) instead of choosing a 
				<em>Default Image ID</em>.
				This allows you to use an image outside of a managed collection (WordPress Media Library or NextGEN Gallery). 
				The image should be at least ' . NGFB_MIN_IMG_SIZE . 'x' . NGFB_MIN_IMG_SIZE . ' or more in width and height. 
				If both the <em>Default Image ID</em> and <em>Default Image URL</em> are defined, the <em>Default Image ID</em>
				will take precedence.' );
			echo '<td colspan="2">', $this->ngfb->admin->form->get_input( 'og_def_img_url', 'wide' ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Default Image on Indexes', null, null, '
				Check this option if you would like to use the default image on index webpages (homepage, archives, categories, author, etc.). 
				If you leave this unchecked, ' . $this->ngfb->fullname . ' will attempt to use image(s) from the first entry on the webpage 
				(default is checked).' );
			echo '<td>', $this->ngfb->admin->form->get_checkbox( 'og_def_img_on_index' ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Default Image on Search Results', null, null, '
				Check this option if you would like to use the default image on search result webpages as well (default is checked).' );
			echo '<td>', $this->ngfb->admin->form->get_checkbox( 'og_def_img_on_search' ), '</td>';

			echo '</tr>';

			if ( $this->ngfb->is_avail['ngg'] == true ) {
				echo '<tr>';
				echo $this->ngfb->util->th( 'Add Featured Image Tags', null, null, '
					If the <em>featured</em> image in a Post or Page is from a NextGEN Gallery, 
					then add that image\'s tags to the Open Graph tag list (default is unchecked).' );
				echo '<td>', $this->ngfb->admin->form->get_checkbox( 'og_ngg_tags' ), '</td>';
				echo '</tr>';
			} else {
				echo $this->ngfb->admin->form->get_hidden( 'og_ngg_tags' );
			}

			echo '<tr>';

			echo $this->ngfb->util->th( 'Add Page Ancestor Tags', null, null, '
				Add the WordPress tags from the Page ancestors (parent, parent of parent, etc.) to the Open Graph tag list.' );
			echo '<td>', $this->ngfb->admin->form->get_checkbox( 'og_page_parent_tags' ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Add Page Title as Tag', null, null, '
				Add the title of the Page to the Open Graph tag list as well. 
				If the <em>Add Page Ancestor Tags</em> option is checked, all the titles of the ancestor Pages will be added as well. 
				This option works well if the title of your Pages are short (one or two words) and subject-oriented.' );
			echo '<td>', $this->ngfb->admin->form->get_checkbox( 'og_page_title_tag' ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Maximum Images', 'highlight', null, '
				The maximum number of images to list in the Open Graph meta property tags -- 
				this includes the <em>featured</em> or <em>attached</em> images, 
				and any images found in the Post or Page content. 
				If you select \'0\', then no images will be listed in the Open Graph meta tags.' );
			echo '<td>', $this->ngfb->admin->form->get_select( 'og_img_max', range( 0, NGFB_MAX_IMG_OG ), 'short', null, true ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Maximum Videos', 'highlight', null, '
				The maximum number of videos, found in the Post or Page content, to include in the Open Graph meta property tags. 
				If you select \'0\', then no videos will be listed in the Open Graph meta tags.' );
			echo '<td>', $this->ngfb->admin->form->get_select( 'og_vid_max', range( 0, NGFB_MAX_VID_OG ), 'short', null, true ), '</td>';

			echo '</tr></table>';
		}

		public function show_metabox_facebook() {
		
			echo '<table class="ngfb-settings"><tr>';

			echo $this->ngfb->util->th( 'Facebook Admin(s)', 'highlight', null, '
				The <em>Facebook Admin(s)</em> user list is used by Facebook to allow access to 
				<a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insight</a> 
				data for those users. 
				Note that these are <em>user</em> account names, not Facebook <em>page</em> names.
				Enter one or more Facebook user names, separated with commas. 
				When viewing your own Facebook wall, your user name is located in the URL 
				(example: https://www.facebook.com/<b>user_name</b>). 
				Enter only the user user name(s), not the URL(s).' );
			echo '<td>', $this->ngfb->admin->form->get_input( 'fb_admins' ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Facebook Application ID', null, null, '
				If you have a <a href="https://developers.facebook.com/apps" target="_blank">Facebook Application</a> 
				ID for your website, enter it here. Facebook Application IDs are used by Facebook to allow 
				access to <a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insight</a> 
				data for <em>accounts associated with the Application ID</em>.' );
			echo '<td>', $this->ngfb->admin->form->get_input( 'fb_app_id' ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Language / Locale', null, null, '
				The language / locale for your website content. This option also controls the language of the 
				Facebook social sharing button.' ); 
			echo '<td>', $this->ngfb->admin->form->get_select( 'fb_lang', 
				$this->ngfb->admin->settings['social']->website['facebook']->lang ), '</td>';

			echo '<tr></table>';
		}

		public function show_metabox_google() {
		
			echo '<table class="ngfb-settings"><tr>';
			
			echo $this->ngfb->util->th( 'Description Length', null, null, '
				The maximum length of text used for the Google Search description meta tag.
				The length should be at least ' . NGFB_MIN_DESC_LEN . ' characters or more 
				(the default is ' . $this->ngfb->opt->get_defaults( 'link_desc_len' ) . ' characters).' );
			echo '<td>', $this->ngfb->admin->form->get_input( 'link_desc_len', 'short' ), ' Characters or less</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Author Link URL', null, null, '
				' . $this->ngfb->fullname . ' can include an <em>author</em> and <em>publisher</em> link in your webpage headers.
				These are not Open Graph meta property tags - they are used primarily by Google\'s search engine to associate Google+
				profiles with search results.' ); 
			echo '<td>', $this->ngfb->admin->form->get_select( 'link_author_field', $this->author_fields() ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Default Author', null, null, '
				A default author for webpages missing authorship information (for example, an index webpage without posts). 
				If you have several authors on your website, you should probably leave this option set to <em>[none]</em> (the default).
				This option is similar to the Open Graph <em>Default Author</em>, except that its applied to the Link meta tag instead.' );
			$user_ids = array( '' );
			foreach ( get_users() as $user ) $user_ids[$user->ID] = $user->display_name;
			echo '<td>', $this->ngfb->admin->form->get_select( 'link_def_author_id', $user_ids, null, null, true ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Default Author on Indexes', null, null, '
				Check this option if you would like to force the <em>Default Author</em> on index webpages 
				(homepage, archives, categories, author, etc.).' );
			echo '<td>', $this->ngfb->admin->form->get_checkbox( 'link_def_author_on_index' ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Default Author on Search Results', null, null, '
				Check this option if you would like to force the <em>Default Author</em> on search result webpages as well.' );
			echo '<td>', $this->ngfb->admin->form->get_checkbox( 'link_def_author_on_search' ), '</td>';

			echo '</tr><tr>';

			echo $this->ngfb->util->th( 'Publisher Link URL', 'highlight', null, '
				If you have a <a href="http://www.google.com/+/business/" target="_blank">Google+ business page for your website</a>, 
				you may use it\'s URL as the Publisher Link. 
				For example, the Publisher Link URL for <a href="http://underwaterfocus.com/" target="_blank">Underwater Focus</a> 
				(one of my websites) is <a href="https://plus.google.com/b/103439907158081755387/103439907158081755387/posts" 
				target="_blank">https://plus.google.com/b/103439907158081755387/103439907158081755387/posts</a>.
				The <em>Publisher Link URL</em> may take precedence over the <em>Author Link URL</em> in Google\'s search results.' ); 
			echo '<td>', $this->ngfb->admin->form->get_input( 'link_publisher_url', 'wide' ), '</td>';

			echo '</tr></table>';

		}

		public function show_metabox_twitter() {
			?>
			<table class="ngfb-settings">
			<?php foreach ( $this->get_more_twitter() as $row ) echo '<tr>' . $row . '</tr>'; ?>
			</table>
			<?php
		}

		protected function get_more_twitter() {
			return array(
				'<td colspan="2" align="center"><p>' . $this->ngfb->msg->get( 'pro_feature' ) . '</p></td>',

				$this->ngfb->util->th( 'Enable Twitter Cards', 'highlight', null, 
				'Add Twitter Card meta tags to all webpage headers (cards include Summary, Large Image, Photo, and Gallery).
				Note that your website must be \'authorized\' by Twitter for each type of Twitter Card. 
				See the <a href="http://surniaulula.com/extend/plugins/nextgen-facebook/other_notes/" target="_blank">Other Notes</a> 
				webpage for more information on the authorization process.' ) .
				'<td class="blank">' . $this->ngfb->admin->form->get_hidden( 'tc_enable' ) . '</td>',

				$this->ngfb->util->th( 'Description Length', null, null, '
				The maximum length of text used for the Twitter Card description.
				The length should be at least ' . NGFB_MIN_DESC_LEN . ' characters or more 
				(the default is ' . $this->ngfb->opt->get_defaults( 'tc_desc_len' ) . ' characters).' ) .
				'<td class="blank">' . $this->ngfb->admin->form->get_hidden( 'tc_desc_len' ) . '</td>',

				$this->ngfb->util->th( 'Website @username', 'highlight', null, 
				'The Twitter username for your website and / or company (not your personal Twitter username).
				As an example, the Twitter username for <a href="http://surniaulula.com/" target="_blank">Surnia Ulula</a> 
				is <a href="https://twitter.com/surniaululacom" target="_blank">@surniaululacom</a>.' ) .
				'<td class="blank">' . $this->ngfb->admin->form->get_hidden( 'tc_site' ) . '</td>',

				$this->ngfb->util->th( '\'Summary\' Card Image Size', null, null, 
				'The size of content images provided for the
				<a href="https://dev.twitter.com/docs/cards/types/summary-card" target="_blank">Summary Card</a>
				(should be at least 120x120, larger than 60x60, and less than 1MB).' ) .
				'<td class="blank">' . $this->ngfb->admin->form->get_hidden( 'tc_sum_size' ) . '</td>',

				$this->ngfb->util->th( '\'Large Image Summary\' Card Size', null, null, 
				'The size of Post Meta, Featured or Attached images provided for the
				<a href="https://dev.twitter.com/docs/cards/types/large-image-summary-card" target="_blank">Large Image Summary Card</a>
				(must be larger than 280x150 and less than 1MB).' ) .
				'<td class="blank">' . $this->ngfb->admin->form->get_hidden( 'tc_large_size' ) . '</td>',

				$this->ngfb->util->th( '\'Photo\' Card Image Size', 'highlight', null, 
				'The size of ImageBrowser or Attachment Page images provided for the 
				<a href="https://dev.twitter.com/docs/cards/types/photo-card" target="_blank">Photo Card</a> 
				(should be at least 560x750 and less than 1MB).' ) .
				'<td class="blank">' . $this->ngfb->admin->form->get_hidden( 'tc_photo_size' ) . '</td>',

				$this->ngfb->util->th( '\'Gallery\' Card Image Size', null, null, 
				'The size of NGG Gallery images provided for the
				<a href="https://dev.twitter.com/docs/cards/types/gallery-card" target="_blank">Gallery Card</a>.' ) . 
				'<td class="blank">' . $this->ngfb->admin->form->get_hidden( 'tc_gal_size' ) . '</td>',

				$this->ngfb->util->th( 'Minimum Images for Gallery', null, null, 
				'The minimum number of images found in a gallery to qualify for the
				<a href="https://dev.twitter.com/docs/cards/types/gallery-card" target="_blank">Gallery Card</a>.' ) .
				'<td class="blank">' . $this->ngfb->admin->form->get_hidden( 'tc_gal_min' ) . '</td>',

			);
		}

		public function show_metabox_taglist() {
			?>
			<table class="ngfb-settings" style="padding-bottom:0;">
			<tr>
				<td>
				<p><?php echo $this->ngfb->fullname; ?> will add the following Facebook and Open Graph meta tags to your webpages. 
				If your theme or another plugin already generates one or more of these meta tags, you can uncheck them here to prevent 
				<?php echo $this->ngfb->fullname; ?> from adding duplicate meta tags (the "description" meta tag is popular with SEO plugins, 
				for example, so it is unchecked by default).</p>
				</td>
			</tr>
			</table>

			<table class="ngfb-settings" style="padding-bottom:0;">
			<?php
			$og_cols = 5;
			$cells = array();
			$rows = array();
			foreach ( $this->ngfb->opt->get_defaults() as $opt => $val ) {
				if ( preg_match( '/^inc_(.*)$/', $opt, $match ) )
					$cells[] = '<td class="taglist">' . $this->ngfb->admin->form->get_checkbox( $opt ) . '</td>' .
						'<th class="taglist' . ( $opt == 'inc_description' ? ' highlight' : '' ) .
							'">' . $match[1] . '</th>' . "\n";
			}
			unset( $opt, $val );
			$per_col = ceil( count( $cells ) / $og_cols );
			foreach ( $cells as $num => $cell ) {
				if ( empty( $rows[ $num % $per_col ] ) )
					$rows[ $num % $per_col ] = '';	// initialize the array
				$rows[ $num % $per_col ] .= $cell;	// create the html for each row
			}
			unset( $num, $cell );
			foreach ( $rows as $num => $row ) 
				echo '<tr>', $row, '</tr>', "\n";
			unset( $num, $row );

			echo '<table class="ngfb-settings"><tr>';
			echo $this->ngfb->util->th( 'Include Empty og:* Meta Tags', null, null, '
				Include meta property tags of type og:* without any content (default is unchecked).' );
			echo '<td>', $this->ngfb->admin->form->get_checkbox( 'og_empty_tags' ), '</td>';
			echo '</tr></table>';

		}

		private function author_fields() {
			return $this->ngfb->user->add_contact_methods( 
				array( 'none' => '', 'author' => 'Author Index', 'url' => 'Website' ) 
			);
		}

	}
}

?>
