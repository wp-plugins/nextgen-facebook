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
					$msg = '<p style="font-weight:bold;font-size:1.1em;">Would you like to... 
					Add support for <a href="https://dev.twitter.com/docs/cards" target="_blank">Twitter Cards</a>,
					including <em>Gallery, Photo, Large Image, Player and Product</em> Cards?<br/>';
					$msg .= 'Customize Open Graph and Twitter Card meta tags for each <em>individual</em> Post and Page?<br/>';
					$msg .= 'Change the Facebook, Google+ and Twitter social button language as the webpage switches language?<br/>';
					$msg .= 'Add tighter integration with 3rd party plugins like WordPress SEO, All-In-One SEO and WooCommerce?<br/>';
					$msg .= 'Improve page load times with file caching for <em>external</em> social images and JavaScript?<br/>';
					$msg .= '<p style="font-size:1.2em;">Help support '.$this->p->cf['full'].' by <a href="'.$this->p->cf['url']['purchase'].'" 
					target="_blank">purchasing the Pro version today</a>.</p>';
					$msg .= '<p>Upgrading to the Pro version is easy and simple! Enter the unique <em>Authentication ID</em> 
					(that you\'ll receive by email) on the Advanced settings page, and update the plugin from within WordPress.</p>';
					break;
				case 'purchase_box' :
					$msg = '<p>'.$this->p->cf['full'].' has taken many, many months of long days to develop and fine-tune.
					If you compare this plugin with others, I think you\'ll agree that the result was worth the effort.
					<a href="'.$this->p->cf['url']['purchase'].'" target="_blank">Please show your appreciation by purchasing ';
					if ( $this->p->is_avail['aop'] == true )
						$msg .= 'a Pro version license</a>.</p>';
					else $msg .= 'the Pro version</a>.</p>';
					break;
				case 'thankyou' :
					$msg = '<p>Thank you for your purchase! I hope the '.$this->p->cf['full'].' plugin will exceed all of your expectations.</p>';
					break;
				case 'help_boxes' :
					$msg = '<p>Individual option boxes (like this one) can be opened / closed by clicking on their title bar, 
					moved and re-ordered by dragging them, and removed / added from the <em>Screen Options</em> tab (top-right).
					Values in multiple tabs can be edited before clicking the \'Save All Changes\' button.</p>';
					break;
				case 'help_free' :
					$msg = '<p><strong>Need help with the <em>GPL</em> version? 
					See the <a href="'.$this->p->cf['url']['faq'].'" target="_blank">FAQ</a>, 
					the <a href="'.$this->p->cf['url']['notes'].'" target="_blank">Other Notes</a>, or visit the 
					<a href="'.$this->p->cf['url']['support'].'" target="_blank">Support Forum</a> on WordPress.org</strong>.</p>';
					break;
				case 'help_pro' :
					$msg = '<p><strong>Need help with the Pro version? 
					See the <a href="'.$this->p->cf['url']['pro_faq'].'" target="_blank">FAQ</a>, 
					the <a href="'.$this->p->cf['url']['pro_notes'].'" target="_blank">Other Notes</a>, or 
					submit a <a href="'.$this->p->cf['url']['pro_request'].'" target="_blank">Support Request</a>.</p>';
					break;
			}
			return $msg;
		}

	}

}
?>
