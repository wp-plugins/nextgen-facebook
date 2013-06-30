<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbSettingsSocialStyle' ) && class_exists( 'ngfbAdmin' ) ) {

	class ngfbSettingsSocialStyle extends ngfbAdmin {

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
			add_meta_box( $this->pagehook . '_style', 'Social Styles', array( &$this, 'show_metabox_style' ), $this->pagehook, 'normal' );

			foreach ( $this->ngfb->css_names as $css_id => $css_name ) {
				add_meta_box( $this->pagehook . '_' . $css_id, $css_name, 
					array( &$this, 'show_metabox_' . $css_id ), $this->pagehook, 'normal' );
			}
			$this->ngfb->user->reset_metaboxes( $this->pagehook, array_keys( $this->ngfb->css_names ) );
		}

		public function show_metabox_style() {
			echo '<table class="ngfb-settings"><tr>';
			echo $this->ngfb->util->th( 'Use Social StyleSheets', 'highlight', null, '
				Add the following styles to all webpages (default is unchecked).
				All styles will be minimized into a single stylesheet with the URL of ' . $this->ngfb->style->social_css_min_url . '. 
				The stylesheet is created or removed depending whether this option is checked or unchecked.' ); 
			echo '<td>', $this->ngfb->admin->form->get_checkbox( 'buttons_link_css' ), '</td>';
			echo '</tr></table>';
		}

		public function show_metabox_excerpt() {
			?>
			<table class="ngfb-settings"><tr>
			<td class="textinfo">
			<p>Social sharing buttons, added to the excerpt text from the 
			<?php echo $this->ngfb->util->get_admin_url( 'social', 'Social Sharing settings page' ); ?>, 
			are assigned the 'ngfb-excerpt-buttons' class, which itself contains the 'ngfb-buttons' class -- 
			a common class for all the social buttons (see the Social Buttons Style section on this page).</p>

			<p>Example:</p>
<pre>
.ngfb-excerpt-buttons 
	.ngfb-buttons
		.facebook-button { }
</pre>
			</td>
			<?php
			echo '<td>', $this->ngfb->admin->form->get_textarea( 'buttons_css_excerpt', 'large' ), '</td>';
			echo '</tr></table>';
		}

		public function show_metabox_content() {
			?>
			<table class="ngfb-settings">
			<td class="textinfo">
			<p>Social sharing buttons, added to the content text from the 
			<?php echo $this->ngfb->util->get_admin_url( 'social', 'Social Sharing settings page' ); ?>, 
			are assigned the 'ngfb-content-buttons' class, which itself contains the 'ngfb-buttons' class -- 
			a common class for all the social buttons (see the Social Buttons Style section on this page).</p>

			<p>Example:</p>
<pre>
.ngfb-content-buttons 
	.ngfb-buttons
		.facebook-button { }
</pre>
			</td>
			<?php
			echo '<td>', $this->ngfb->admin->form->get_textarea( 'buttons_css_content', 'large' ), '</td>';
			echo '</tr></table>';
		}

		public function show_metabox_shortcode() {
			?>
			<table class="ngfb-settings">
			<td class="textinfo">
			<p>Social sharing buttons added from a shortcode are assigned the 'ngfb-shortcode-buttons' class, 
			which itself contains the 'ngfb-buttons' class -- a common class for all the social buttons 
			(see the Social Buttons Style section on this page).</p>

			<p>Example:</p>
<pre>
.ngfb-shortcode-buttons 
	.ngfb-buttons
		.facebook-button { }
</pre>
			</td>
			<?php
			echo '<td>', $this->ngfb->admin->form->get_textarea( 'buttons_css_shortcode', 'large' ), '</td>';
			echo '</tr></table>';
		}

		public function show_metabox_widget() {
			?>
			<table class="ngfb-settings"><tr>
			<td class="textinfo">
			<p>Social sharing buttons within the '<?php echo ngfbWidgetSocialSharing::$fullname; ?>' widget 
			are assigned the 'ngfb-widget-buttons' class, which itself contains the 'ngfb-buttons' class -- 
			a common class for all the social buttons (see the Social Buttons Style section on this page).</p>
			<p>Example:</p>
<pre>
.ngfb-widget-buttons 
	.ngfb-buttons
		.facebook-button { }
</pre>
			<p>The widget also has an id of 'ngfb-widget-buttons-{#}', and the buttons have an id of 
			'{name}-ngfb-widget-buttons-{#}'.</p>
			<p>Example:</p>
<pre>
#facebook-widget-buttons-2 { }
</pre>
			</td>
			<?php
			echo '<td>', $this->ngfb->admin->form->get_textarea( 'buttons_css_widget', 'large' ), '</td>';
			echo '</tr></table>';
		}

		public function show_metabox_social() {
			?>
			<table class="ngfb-settings">
			<td class="textinfo">
			<p><?php echo $this->ngfb->fullname; ?> . ' uses the 'ngfb-buttons' class to wrap all social buttons, 
			and each button has it's own individual class name as well. 
			Refer to the <a href="http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/" 
			target="_blank">Other Notes</a> webpage for additional stylesheet information, 
			including how to hide the social buttons for specific Posts, Pages, categories, tags, etc.</p>
			</td>
			<?php
			echo '<td>', $this->ngfb->admin->form->get_textarea( 'buttons_css_social', 'large' ), '</td>';
			echo '</tr></table>';
		}

	}
}

?>
