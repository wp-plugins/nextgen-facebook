<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbMessages' ) ) {

	class NgfbMessages {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get( $name = '' ) {
			$msg = '';
			switch ( $name ) {
				case 'pro_feature' :
					if ( $this->p->is_avail['aop'] == true ) {
						$msg = '<p class="pro_feature"><a href="'.$this->p->cf['url']['purchase'].'" target="_blank">Purchase 
						additional licence(s) to enable Pro version features</p>';
					} else
						$msg = '<p class="pro_feature"><a href="'.$this->p->cf['url']['purchase'].'" target="_blank">Upgrade 
						to the Pro version to enable the following features</a></p>';
					break;
				case 'pro_activate' :
					// in multisite, only show activation message on our own plugin pages
					if ( ! is_multisite() || ( is_multisite() && preg_match( '/^.*\?page='.$this->p->cf['lca'].'-/', $_SERVER['REQUEST_URI'] ) ) ) {
						$url = $this->p->util->get_admin_url( 'advanced' );
						$msg = '<p>The '.$this->p->cf['full'].' Authentication ID option value is empty.<br/>
						To activate Pro version features, and allow the plugin to authenticate itself for updates,<br/>
						<a href="'.$url.'">enter the unique Authenticaton ID you receive following your purchase
						on the Advanced Settings page</a>.</p>';
					}
					break;
				case 'pro_details' :
					$msg .= '
					<style type="text/css">.sucom-update-nag p { font-size:1.05em; }</style>
					<p>Have you considered encouraging the continued development and support of '.$this->p->cf['full'].
					' by purchasing the Pro version?</p>
					<p>'.$this->p->cf['full_pro'].' supports several types of Twitter Cards, including the <em>Gallery</em>, 
					<em>Player</em> and <em>Product</em> Twitter Cards, allows you to customize individual Post / Page meta tags, 
					and integrates with popular 3rd party plugins to improve Open Graph, Rich Pin, and Twitter Card meta tags.</p>
					<p><strong>Improve your social presence on Facebook, Twitter and Pinterest by providing your users with 
					better looking, more accurate and tailored posts</strong>.</p>
					<p>Upgrading to the Pro version is simple and takes just one or two minutes - <br/>
					<a href="'.$this->p->cf['url']['purchase'].'" target="_blank">purchase an '.$this->p->cf['full_pro'].' license right now</a>.</p>
					';
					break;
				case 'purchase_box' :
					$msg = '<p>Developing and supporting the '.$this->p->cf['full'].' plugin takes most of my work days (and week-ends).
					If you compare this plugin with others, I hope you\'ll agree that the result was worth all the effort and long hours.
					If you would like to show your appreciation, and access the full range of features this plugin has to offer, please purchase ';
					if ( $this->p->is_avail['aop'] == true )
						$msg .= 'a Pro version license.</p>';
					else $msg .= 'the Pro version.</p>';
					break;
				case 'thankyou' :
					$msg = '<p>Thank you for your purchase. I hope '.$this->p->cf['full'].' will exceed all of your expectations for many years to come.</p>';
					break;
				case 'help_boxes' :
					$msg = '<p>Individual option boxes (like this one) can be opened / closed by clicking on their title bar, 
					moved and re-ordered by dragging them, and removed / added from the <em>Screen Options</em> tab (top-right).
					Values in multiple tabs can be edited before clicking the \'Save All Changes\' button.</p>';
					break;
				case 'help_free' :
					$msg = '<p><strong>Need help with the GPL version?</strong>
					Review the <a href="'.$this->p->cf['url']['faq'].'" target="_blank">Frequently Asked Questions</a>, 
					the <a href="'.$this->p->cf['url']['notes'].'" target="_blank">Other Notes</a>, and / or visit the 
					<a href="'.$this->p->cf['url']['support'].'" target="_blank">Support Forum</a> on WordPress.org.</p>';
					break;
				case 'help_pro' :
					$msg = '<p><strong>Need help with the Pro version?</strong>
					Review the <a href="'.$this->p->cf['url']['pro_codex'].'" target="_blank">Plugin Codex</a>
					and / or <a href="'.$this->p->cf['url']['pro_ticket'].'" target="_blank">Submit a new Support Ticket</a>.</p>';
					break;
			}
			return $msg;
		}
	}
}

?>
