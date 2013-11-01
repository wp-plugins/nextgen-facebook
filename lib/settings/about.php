<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'ngfbSettingsAbout' ) && class_exists( 'ngfbAdmin' ) ) {

	class ngfbSettingsAbout extends ngfbAdmin {

		protected $p;
		protected $menu_id;
		protected $menu_name;
		protected $pagehook;

		public function __construct( &$plugin, $id, $name ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
			$this->menu_id = $id;
			$this->menu_name = $name;
		}

		protected function add_meta_boxes() {
			// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
			add_meta_box( $this->pagehook . '_description', 'Description', array( &$this, 'show_metabox_description' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_faq', 'FAQ', array( &$this, 'show_metabox_faq' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_remaining', 'Other Notes', array( &$this, 'show_metabox_remaining' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_changelog', 'Changelog', array( &$this, 'show_metabox_changelog' ), $this->pagehook, 'normal' );

			$this->p->user->reset_metabox_prefs( $this->pagehook, array( 'faq', 'remaining', 'changelog' ) );
		}

		protected function show_form() {
			do_meta_boxes( $this->pagehook, 'normal', null ); 
		}

		public function show_metabox_description() {
			?>
			<table class="ngfb-settings">
			<tr><td><?php echo empty( $this->p->admin->readme['sections']['description'] ) ? 
				'Content not Available' : $this->p->admin->readme['sections']['description']; ?></td></tr>
			</table>
			<?php
		}
		
		public function show_metabox_faq() {
			?>
			<table class="ngfb-settings">
			<tr><td><?php echo empty( $this->p->admin->readme['sections']['frequently_asked_questions'] ) ?
				'Content not Available' : $this->p->admin->readme['sections']['frequently_asked_questions']; ?></td></tr>
			</table>
			<?php
		}

		public function show_metabox_remaining() {
			?>
			<table class="ngfb-settings">
			<tr><td><?php echo empty( $this->p->admin->readme['remaining_content'] ) ?
				'Content not Available' : $this->p->admin->readme['remaining_content']; ?></td></tr>
			</table>
			<?php
		}

		public function show_metabox_changelog() {
			?>
			<table class="ngfb-settings">
			<tr><td><?php echo empty( $this->p->admin->readme['sections']['changelog'] ) ?
				'Content not Available' : $this->p->admin->readme['sections']['changelog']; ?></td></tr>
			</table>
			<?php
		}
	}
}

?>
