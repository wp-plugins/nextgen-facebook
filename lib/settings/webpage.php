<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbSettingsWebpage' ) && class_exists( 'ngfbAdmin' ) ) {

	class ngfbSettingsWebpage extends ngfbAdmin {

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
			add_meta_box( $this->pagehook . '_general', 'General Settings', array( &$this, 'show_metabox_general' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_facebook', 'Facebook Settings', array( &$this, 'show_metabox_facebook' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_google', 'Google Settings', array( &$this, 'show_metabox_google' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_taglist', 'Meta Tag List', array( &$this, 'show_metabox_taglist' ), $this->pagehook, 'normal' );
		}

		public function show_metabox_general() {
			?>
			<table class="ngfb-settings">
			<tr>
				<th>Website Topic</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_select( 'og_art_section', $this->ngfb->util->get_topics() ); ?></td>
				<td><p>The topic name that best describes the Posts and Pages on your website. 
				This topic name will be used in the "article:section" Open Graph meta tag for all your Posts and Pages. 
				You can leave the topic name blank if you would prefer not to include an "article:section" meta tag.</p></td>
			</tr>
			<tr>
				<th>Article Author URL</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_select( 'og_author_field', $this->author_fields() ); ?></td>
				<td><p>Select the profile field to use for the "article:author" Open Graph property tag URL. 
				The URL should point to an author's <em>personal</em> website or social page. 
				This Open Graph meta tag is primarily used by Facebook, so the preferred value is the author's Facebook webpage URL. 
				See the "Google Settings" section below for an Author URL field for Google, and to define a common <em>publisher</em> URL for all webpages.</p></td>
			</tr>
			<tr>
				<th>Fallback to Author Index</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'og_author_fallback' ); ?></td>
				<td><p>If the value found in the Author URL profile field (and the Author Link URL in the "Google Settings" section below) is not a valid URL, 
				NGFB can fallback to using the Author Index webpage URL instead ("<?php echo trailingslashit( site_url() ), 'author/{username}'; ?>" for example). 
				Uncheck this option to disable this fallback feature (default is checked).</p></td>
			</tr>
			<tr>
				<th>Default Author</th>
				<td class="second"><?php
					$user_ids = array( '' );
					foreach ( get_users() as $user )
						$user_ids[$user->ID] = $user->display_name;
					echo $this->ngfb->admin->form->get_select( 'og_def_author_id', $user_ids, null, null, true );
				?></td>
				<td><p>A default author for webpages missing authorship information (for example, an index webpage without posts). 
				If you have several authors on your website, you should probably leave this option to <em>[none]</em> (the default).</p></td>
			</tr>
			<tr>
				<th>Default Author on Indexes</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'og_def_author_on_index' ); ?></td>
				<td><p>Check this option if you would like to force the Default Author on index webpages (homepage, archives, categories, author, etc.). 
				If the Default Author is <em>[none]</em> (the default value), then the index webpages will be labeled as a 'webpage'. If the option is checked, 
				index webpages will be labeled as a an 'article' with authorship attributed to the Default Author (default is unchecked).</p></td>
			</tr>
			<tr>
				<th>Default Author on Search Results</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'og_def_author_on_search' ); ?></td>
				<td><p>Check this option if you would like to force the Default Author on search result webpages as well. 
				If the Default Author is <em>[none]</em>, then the search results webpage will be labeled as a 'webpage' instead of an 'article' (default is unchecked).</p></td>
			</tr>
			<tr>
				<th>Image Size Name</th>
				<td class="second"><?php 
					echo $this->ngfb->admin->form->get_select_img_size( 'og_img_size' ); 
					$size_info = $this->ngfb->media->get_size_info( $this->ngfb->opt->get_defaults( 'og_img_size' ) );
					$size_desc = $size_info['width'] . 'x' . $size_info['height'] . ', ' . ( $size_info['crop'] == 1 ? '' : 'not ' ) . 'cropped';
				?></td>
				<td><p>The <a href="options-media.php">Media Settings</a> size name used for images in the Open Graph meta tags. 
				The default size name is "<?php echo $this->ngfb->opt->get_defaults( 'og_img_size' ); ?>" (currently defined as <?php echo $size_desc; ?>). 
				Select an image size name with a value between <?php echo NGFB_MIN_IMG_WIDTH, 'x', NGFB_MIN_IMG_HEIGHT; ?> and 1500x1500 in width and height - preferably cropped.
				You can use the <a href="http://wordpress.org/extend/plugins/simple-image-sizes/" target="_blank">Simple Image Size</a> plugin (or others) 
				to define your own custom sizes in the <a href="options-media.php">Media Settings</a>. 
				I suggest creating an "opengraph-thumbnail" image size to manage the Open Graph image sizes independently from those of your theme.</p></td>
			</tr>
			<tr>
				<th>Default Image ID</th>
				<td class="second"><?php 
					echo $this->ngfb->admin->form->get_input( 'og_def_img_id', 'short' );
					echo ' in the ';
					$id_pre = array( 'wp' => 'Media Library' );
					if ( $this->ngfb->is_avail['ngg'] == true )
						$id_pre['ngg'] = 'NextGEN Gallery';
					echo $this->ngfb->admin->form->get_select( 'og_def_img_id_pre', $id_pre, 'medium' );
				?></td>
				<td><p>The ID number and location of your default image (example: 123). 
				The Image ID number, for an image in the Media Library, can be determined from the URL when editing an image in the Media Library (post=123 in the URL, for example). 
				The NextGEN Gallery Image IDs are easier to find -- it's the number in the first column when viewing a Gallery.</p></td>
			</tr>
			<tr>
				<th>Default Image URL</th>
				<td colspan="2"><?php echo $this->ngfb->admin->form->get_input( 'og_def_img_url', 'wide' ); ?>
				<p>You can specify a Default Image URL (including the http:// prefix) instead of a Default Image ID. 
				This allows you to use an image outside of a managed collection (Media Library or NextGEN Gallery). 
				The image should be at least <?php echo NGFB_MIN_IMG_WIDTH, 'x', NGFB_MIN_IMG_HEIGHT; ?> or more in width and height. 
				If both the Default Image ID and URL are defined, the Default Image ID takes precedence.</p>
				</td>
			</tr>
			<tr>
				<th>Default Image on Indexes</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'og_def_img_on_index' ); ?></td>
				<td><p>Check this option if you would like to use the default image on index webpages (homepage, archives, categories, author, etc.). 
				If you leave this unchecked, <?php echo $this->ngfb->fullname; ?> will attempt to use image(s) from the first entry on the webpage (default is checked).</p></td>
			</tr>
			<tr>
				<th>Default Image on Search Results</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'og_def_img_on_search' ); ?></td>
				<td><p>Check this option if you would like to use the default image on search result webpages as well (default is checked).</p></td>
			</tr>
			<?php	if ( $this->ngfb->is_avail['ngg'] == true ) : ?>
			<tr>
				<th>Add Featured Image Tags</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'og_ngg_tags' ); ?></td>
				<td><p>If the <em>featured</em> image in a Post or Page is from a NextGEN Gallery (NGG), then add that image's tags to the Open Graph tag list (default is unchecked).</p></td>
			</tr>
			<?php	else : echo $this->ngfb->admin->form->get_hidden( 'og_ngg_tags' ); endif; ?>
			<tr>
				<th>Add Page Ancestor Tags</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'og_page_parent_tags' ); ?></td>
				<td><p>Add the WordPress tags from the Page ancestors (parent, parent of parent, etc.) to the Open Graph tag list.</p></td>
			</tr>
			<tr>
				<th>Add Page Title as Tag</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'og_page_title_tag' ); ?></td>
				<td><p>Add the title of the Page to the Open Graph tag list as well. If the "Add Page Ancestor Tags" option is checked, the titles of ancestor Pages will be added as well. 
				This option works well if the title of your Pages are short and subject-oriented.</p></td>
			</tr>
			<tr>
				<th>Maximum Images</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_select( 'og_img_max', range( 0, NGFB_MAX_IMG_OG ), 'short', null, true ); ?></td>
				<td><p>The maximum number of images to list in the Open Graph meta property tags -- this includes the <em>featured</em> or <em>attached</em> images, 
				and any images found in the Post or Page content. If you select "0", no images will be listed in the Open Graph meta tags.</p></td>
			</tr>
			<tr>
				<th>Maximum Videos</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_select( 'og_vid_max', range( 0, NGFB_MAX_VID_OG ), 'short', null, true ); ?></td>
				<td><p>The maximum number of videos, found in the Post or Page content, to include in the Open Graph meta property tags. 
				If you select "0", no videos will be listed in the Open Graph meta tags.</p></td>
			</tr>
			<tr>
				<th>Title Separator</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_input( 'og_title_sep', 'short' ); ?></td>
				<td><p>One or more characters used to separate values (category parent names, page numbers, etc.) within the Open Graph title string 
				(default is '<?php echo $this->ngfb->opt->get_defaults( 'og_title_sep' ); ?>').</p></td>
			</tr>
			<tr>
				<th>Maximum Title Length</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_input( 'og_title_len', 'short' ); ?> Characters</td>
				<td><p>The maximum length of text used in the Open Graph title tag (default is <?php echo $this->ngfb->opt->get_defaults( 'og_title_len' ); ?> characters).</p></td>
			</tr>
			<tr>
				<th>Maximum Description Length</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_input( 'og_desc_len', 'short' ); ?> Characters</td>
				<td><p>The maximum length of text, from your post/page excerpt or content, used in the Open Graph description tag. 
				The length must be <?php echo NGFB_MIN_DESC_LEN; ?> characters or more (default is <?php echo $this->ngfb->opt->get_defaults( 'og_desc_len' ); ?>).</p></td>
			</tr>
			<tr>
				<th>Content Begins at First Paragraph</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'og_desc_strip' ); ?></td>
				<td><p>For a Page or Post <em>without</em> an excerpt, if this option is checked, the plugin will ignore all text until the first &lt;p&gt; paragraph in the content. 
				If an excerpt exists, then the complete excerpt text is used instead.</p></td>
			</tr>
			</table>
			<?php
		}

		public function show_metabox_facebook() {
			?>
			<table class="ngfb-settings">
			<tr>
				<th>Facebook Admin(s)</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_input( 'og_admins' ); ?></td>
				<td><p>One or more Facebook account names (generally your own) separated with a comma. 
				When you are viewing your own Facebook wall, your account name is located in the URL (example: https://www.facebook.com/<b>account_name</b>). 
				Enter only the account names, not the URLs. The Facebook Admin names are used by Facebook to allow access to 
				<a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insight</a> data for those accounts.</p></td>
			</tr>
			<tr>
				<th>Facebook App ID</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_input( 'og_app_id' ); ?></td>
				<td><p>If you have a <a href="https://developers.facebook.com/apps" target="_blank">Facebook Application</a> ID for your website, enter it here. 
				Facebook Application IDs are used by Facebook to allow access to <a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insight</a> data 
				for accounts associated with the Application ID.</p></td>
			</tr>
			</table>
			<?php
		}

		public function show_metabox_google() {
			?>
			<table class="ngfb-settings">
			<tr>
				<th>Author Link URL</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_select( 'link_author_field', $this->author_fields() ); ?></td>
				<td><p><?php echo $this->ngfb->fullname; ?> can also include an <em>author</em> and <em>publisher</em> link in your webpage headers.
				These are not Open Graph meta property tags - they are used primarily by Google's search engine to associate Google+ profiles with their search results. 
				If you have a <a href="http://www.google.com/+/business/" target="_blank">Google+ business page for your website</a>, you may use it's URL as the Publisher Link. 
				For example, the Publisher Link URL for <a href="http://underwaterfocus.com/" target="_blank">Underwater Focus</a> (one of my websites) is 
				<a href="https://plus.google.com/b/103439907158081755387/103439907158081755387/posts" target="_blank">https://plus.google.com/b/103439907158081755387/103439907158081755387/posts</a>.
				The Publisher Link URL takes precedence over the Author Link URL in Google's search results.</p></td>
			</tr>
			<tr>
				<th>Publisher Link URL</th>
				<td colspan="2"><?php echo $this->ngfb->admin->form->get_input( 'link_publisher_url', 'wide' ); ?></td>
			</tr>
			</table>
			<?php
		}

		public function show_metabox_taglist() {
			?>
			<table class="ngfb-settings">
			<tr>
				<?php $og_cols = 4; ?>
				<?php echo '<td colspan="'.($og_cols * 2).'">'; ?>
				<p><?php echo $this->ngfb->fullname; ?> will add the following Facebook and Open Graph meta tags to your webpages. 
				If your theme, or another plugin, already generates one or more of these meta tags, you can uncheck them here to prevent 
				<?php echo $this->ngfb->fullname; ?> from adding duplicate meta tags (the "description" meta tag is popular with SEO plugins, for example).</p>
				</td>
			</tr>
			<?php
				$cells = array();
				$rows = array();
				foreach ( $this->ngfb->opt->get_defaults() as $opt => $val ) {
					if ( preg_match( '/^inc_(.*)$/', $opt, $match ) )
						$cells[] = '<th class="metatag">Include '.$match[1].' Meta Tag</th>
							<td>'. $this->ngfb->admin->form->get_checkbox( $opt ) . '</td>';
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
			?>
			<tr>
				<th>Include Empty og:* Meta Tags</th>
				<td><?php echo $this->ngfb->admin->form->get_checkbox( 'og_empty_tags' ); ?></td>
				<td colspan="<?php echo ( $og_cols * 2 ) - 2; ?>"><p>Include meta property tags of type og:* without any content (default is unchecked).</p></td>
			</tr>
			</table>
			<?php
		}

		private function author_fields() {
			return $this->ngfb->user->contactmethods( 
				array( 'none' => '', 'author' => 'Author Index', 'url' => 'Website' ) 
			);
		}

	}
}

?>
