<?php
/*
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Copyright 2012-2014 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbSubmenuStyle' ) && class_exists( 'NgfbAdmin' ) ) {

	class NgfbSubmenuStyle extends NgfbAdmin {

		public function __construct( &$plugin, $id, $name ) {
			$this->p =& $plugin;
			$this->menu_id = $id;
			$this->menu_name = $name;
			$this->p->util->add_plugin_filters( $this, array( 
				'messages' => 2,		// default messages filter
			) );
		}

		public function filter_messages( $text, $idx ) {
			switch ( $idx ) {
				/*
				 * 'Social Style' settings
				 */
				case 'tooltip-buttons_link_css':
					$text = 'Add the following CSS to all webpages (default is checked).
					The CSS in each Style tab will be <strong>minimized</strong> and saved to a single 
					stylesheet with the URL of <u>'.$this->p->sharing->sharing_css_min_url.'</u>.';
					break;

				case 'style-sharing-info':
					$text = '<p>'.$this->p->cf['full'].' uses the \'ngfb-buttons\' class to wrap all its 
					sharing buttons, and each button has it\'s own individual class name as well. 
					Refer to the <a href="'.$this->p->cf['url']['notes'].'" target="_blank">Notes</a> 
					webpage for additional stylesheet information, including how to hide the sharing 
					buttons for specific Posts, Pages, categories, tags, etc.</p>';
					break;

				case 'style-excerpt-info':
					$text = '<p>Social sharing buttons, enabled / added to the excerpt text from the '.
					$this->p->util->get_admin_url( 'sharing', 'Social Sharing settings page' ).
					', are assigned the \'ngfb-excerpt-buttons\' class, which itself contains the 
					\'ngfb-buttons\' class -- a common class for all the sharing buttons 
					(see the Buttons Style tab).</p> 
					<p>Example:</p><pre>
.ngfb-excerpt-buttons 
    .ngfb-buttons
        .facebook-button { }</pre>';
					break;

				case 'style-content-info':
					$text = '<p>Social sharing buttons, enabled / added to the content text from the '.
					$this->p->util->get_admin_url( 'sharing', 'Social Sharing settings page' ).
					', are assigned the \'ngfb-content-buttons\' class, which itself contains the 
					\'ngfb-buttons\' class -- a common class for all the sharing buttons 
					(see the Buttons Style tab).</p> 
					<p>Example:</p><pre>
.ngfb-content-buttons 
    .ngfb-buttons
        .facebook-button { }</pre>';
					break;

				case 'style-shortcode-info':
					$text = '<p>Social sharing buttons added from a shortcode are assigned the 
					\'ngfb-shortcode-buttons\' class, which itself contains the 
					\'ngfb-buttons\' class -- a common class for all the sharing buttons 
					(see the Buttons Style tab).</p> 
					<p>Example:</p><pre>
.ngfb-shortcode-buttons 
    .ngfb-buttons
        .facebook-button { }</pre>';
					break;

				case 'style-widget-info':
					$text = '<p>Social sharing buttons within the '.$this->p->cf['menu'].
					' Social Sharing widget are assigned the 
					\'ngfb-widget-buttons\' class, which itself contains the 
					\'ngfb-buttons\' class -- a common class for all the sharing buttons 
					(see the Buttons Style tab).</p> 
					<p>Example:</p><pre>
.ngfb-widget-buttons 
    .ngfb-buttons
        .facebook-button { }</pre>
					<p>The '.$this->p->cf['menu'].' Social Sharing widget also has an id of 
					\'ngfb-widget-buttons-<em>#</em>\', and the buttons have an id of 
					\'<em>name</em>-ngfb-widget-buttons-<em>#</em>\'.</p>
					<p>Example:</p><pre>
#ngfb-widget-buttons-2
    .ngfb-buttons
        #facebook-ngfb-widget-buttons-2 { }</pre>';
					break;
			}
			return $text;
		}

		protected function add_meta_boxes() {
			// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
			add_meta_box( $this->pagehook.'_style', 'Social Styles', array( &$this, 'show_metabox_style' ), $this->pagehook, 'normal' );
		}

		public function show_metabox_style() {
			echo '<table class="sucom-setting"><tr>';
			echo $this->p->util->th( 'Use the Social Stylesheet', 'highlight', 'buttons_link_css' );
			echo '<td>', $this->form->get_checkbox( 'buttons_link_css' ), '</td>';
			echo '</tr></table>';

			$metabox = 'style';
			$tabs = apply_filters( $this->p->cf['lca'].'_'.$metabox.'_tabs', $this->p->cf['style'] );
			$rows = array();
			foreach ( $tabs as $key => $title )
				$rows[$key] = array_merge( $this->get_rows( $metabox, $key ), 
					apply_filters( $this->p->cf['lca'].'_'.$metabox.'_'.$key.'_rows', array(), $this->form ) );
			$this->p->util->do_tabs( $metabox, $tabs, $rows );
		}

		protected function get_rows( $metabox, $key ) {
			return array();
		}
	}
}

?>
