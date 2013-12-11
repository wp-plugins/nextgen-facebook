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
					$msg .= '<p style="font-size:1.2em;">Would you like to...</p>';
					$msg .= '<ul>';
					$msg .= '<li>Add support for <em>Gallery, Photo, Large Image, Player and Product</em> 
						<a href="https://dev.twitter.com/docs/cards" target="_blank">Twitter Cards</a>?</li>';
					$msg .= '<li>Customize Open Graph, Rich Pin and Twitter Card meta tags for <em>individual</em> Posts and Pages?</li>';
					$msg .= '<li>Integrate with
						<a href="http://wordpress.org/plugins/wordpress-seo/" target="_blank">WordPress SEO</a>,
						<a href="http://wordpress.org/plugins/all-in-one-seo-pack/" target="_blank">All-In-One SEO</a>,
						<a href="http://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>, 
						<a href="http://wordpress.org/plugins/wordpress-ecommerce/" target="_blank">MarketPress</a>, 
						<a href="http://wordpress.org/plugins/wp-e-commerce/" target="_blank">WP e-Commerce</a>, and 
						<a href="http://wordpress.org/plugins/bbpress/" target="_blank">bbPress</a>?</li>';
					$msg .= '<li>Speed-up page loads with file caching of remote social JavaScript and images?</li>';
					$msg .= '<li>Change the Facebook, Google+ and Twitter social button language dynamically?</li>';
					$msg .= '<li>Shorten URLs for Twitter and rewrite URLs for Content Delivery Networks (CDNs)?</li>';
					$msg .= '</ul>';

					$msg .= '<p style="font-size:1.2em;">Help support this plugin by <a href="'.$this->p->cf['url']['purchase'].'" 
					target="_blank">purchasing the Pro version today</a>.</p>';

					$msg .= '<p>Upgrading to the Pro version is simple and easy! Enter the unique <em>Authentication ID</em> 
					(that you\'ll receive by email) on the Advanced settings page, and update the plugin from within WordPress!</p>';
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
					$msg = '<p>Thank you for your purchase. I hope the '.$this->p->cf['full'].' plugin will exceed all of your expectations!</p>';
					break;
				case 'help_boxes' :
					$msg = '<p>Individual option boxes (like this one) can be opened / closed by clicking on their title bar, 
					moved and re-ordered by dragging them, and removed / added from the <em>Screen Options</em> tab (top-right).
					Values in multiple tabs can be edited before clicking the \'Save All Changes\' button.</p>';
					break;
				case 'help_free' :
					$msg = '<p>Need help with the <em>GPL</em> version? 
					See the <a href="'.$this->p->cf['url']['faq'].'" target="_blank">FAQ</a>, 
					the <a href="'.$this->p->cf['url']['notes'].'" target="_blank">Other Notes</a>, or visit the 
					<a href="'.$this->p->cf['url']['support'].'" target="_blank">Support Forum</a> on WordPress.org.</p>';
					break;
				case 'help_pro' :
					$msg = '<p>Need help with the Pro version? 
					See the <a href="'.$this->p->cf['url']['pro_faq'].'" target="_blank">Frequently Asked Questions (FAQ)</a>, 
					<a href="'.$this->p->cf['url']['pro_notes'].'" target="_blank">Other Notes</a>, 
					visit the <a href="'.$this->p->cf['url']['pro_forum'].'" target="_blank">Community Forums</a>, 
					or <a href="'.$this->p->cf['url']['pro_ticket'].'" target="_blank">Submit a new Support Ticket</a>.</p>';
					break;
			}
			return $msg;
		}
	}
}

?>
