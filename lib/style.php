<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbStyle' ) ) {

	class ngfbStyle {
	
		private $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
		}

		public function admin_page() {
			?>
			<style type="text/css">
				.wrap div.updated, 
				.wrap div.error { 
					padding:0 5px 0 5px; 
					margin:5px 0 10px 0; 
				}
				.wrap div.updated p, 
				.wrap div.error p { margin:5px; }
				.wrap div.updated p strong { font-weight:normal; }
				.wrap { 
					font-size:1em; 
					line-height:1.3em; 
				}
				.wrap h2 { 
					margin:0 0 15px 0; 
				}
				.wrap p { 
					text-align:justify; 
					line-height:1.2em; 
					margin:10px 0 10px 0;
				}
				#wpbody-content .metabox-holder {
					padding:0;
				}
				.btn_wizard_column { 
					white-space:nowrap;
				}
				.btn_wizard_example { 
					display:inline-block; 
					width:155px; 
				}
				.postbox {
					-webkit-border-radius:5px;
					border-radius:5px;
					border:1px solid transparent;
					margin:0 0 10px 0;
				}
				.postbox p {
					color:#333;
				}
				.postbox_website {
					height:440px;
					min-width:500px;
					overflow-y:auto;
				}
				.closed {
					height:auto;
				}
				.inside {
					padding:2px 14px 2px 12px	!important;
				}
				.save-all-button { 
					text-align:center;
					margin:15px 0 0 0;
				}
				.check-updates-button { 
					text-align:center;
					margin:5px 0 10px 0;
				}
				.install-now-button { 
					text-align:center;
					margin:0 0 5px 0;
				}
				.postbox_purchase_side {
					color:#333;
					background:#eeeeff;
					background-image: -webkit-gradient(linear, left bottom, left top, color-stop(7%, #eeeeff), color-stop(77%, #ddddff));
					background-image: -webkit-linear-gradient(bottom, #eeeeff 7%, #ddddff 77%);
					background-image:    -moz-linear-gradient(bottom, #eeeeff 7%, #ddddff 77%);
					background-image:      -o-linear-gradient(bottom, #eeeeff 7%, #ddddff 77%);
					background-image: linear-gradient(to top, #eeeeff 7%, #ddddff 77%);
					border:1px dashed #ccc;
				}
				#toplevel_page_ngfb-about table {
					table-layout:fixed;
				}
				.rss-manager table.ngfb-settings,
				.rss-manager table.ngfb-settings td {
					margin:0;
					padding:0;
				}
				.rss-manager table.ngfb-settings p {
					text-align:justify;
					margin:5px 0 5px 0;
				}
				.rss-manager p.tags,
				.rss-manager p.categories {
					font-size:0.8em;
					font-style:italic;
					margin:5px 0 5px 0;
				}
				.rss-manager p.tags,
				.rss-manager p.categories {
					display:none;
				}
				.rss-manager img {
					border:1px solid #ddd;
					background-color:#ffffff;
					padding:4px;
				}
				.support_feed .description {
					font-size:0.9em;
				}
				.support_feed .description p {
					margin:5px 0 5px 20px;
				}
				.website-col-1 {
					float:left;
					width:50%;
				}
				.website-col-2 {
					float:right;
					width:50%;
				}
				.sig {
					font-family:cursive;
					font-size:1.2em;
				}
			</style>
			<?php
		}

		public function settings() {
			?>
			<style type="text/css">
				table.ngfb-settings .pro_feature {
					font-style:normal;
					margin:6px;
				}
				table.ngfb-settings { 
					width:100%;
				}
				table.ngfb-settings h3 { 
					padding:0		!important;
					margin:20px 0 10px 0	!important;
					background:none;
				}
				table.ngfb-settings pre {
					padding:5px;
					background-color:#eee;
					white-space:pre;
					overflow:auto;
				}
				table.ngfb-settings pre code {
					background-color:#eee;
				}
				table.ngfb-settings ul { 
					margin-left:20px;
					list-style-type:circle;
				}
				table.ngfb-settings tr { 
					vertical-align:top;
				}
				table.ngfb-settings th { 
					text-align:right;
					white-space:nowrap; 
					padding:0 10px 0 4px; 
					min-width:190px;
				}
				table.ngfb-settings th.short { 
					min-width:120px;
				}
				table.ngfb-settings th.side { 
					padding:0 2px 0 2px; 
					min-width:60px;
					width:60px;
				}
				table.ngfb-settings th.social { 
					font-weight:bold; 
					text-align:left; 
					padding:2px 10px 2px 10px; 
					background-color:#eee; 
					border:1px solid #ddd;
					width:50%;
				}
				table.ngfb-settings td { 
					padding:0 4px 0 4px;
				}
				table.ngfb-settings td p { 
					color:#666;
					margin:0 0 10px 0;
				}
				table.ngfb-settings td.textarea { 
					height:42px;
				}
				table.ngfb-settings td.second { 
					width:250px;
				}
				table.ngfb-settings td.blank { 
					min-width:100px;
					background-color:#eee; 
					border:1px dashed #ccc;
				}
				table.ngfb-settings td.blank p { 
					margin:8px;
				}
				table.ngfb-settings td#latest_notice { 
					padding:0;
				}
				table.ngfb-settings td#latest_notice p { 
					margin:10px 0 5px 0;
				}
				table.ngfb-settings td select,
				table.ngfb-settings td input { margin:0 0 5px 0; }
				table.ngfb-settings td input[type=text] { width:250px; }
				table.ngfb-settings td input[type=text].short { width:50px; }
				table.ngfb-settings td input[type=text].wide { width:100%; }
				table.ngfb-settings td input[type=radio] { 
					vertical-align:top; 
					margin:4px 4px 4px 0;
				}
				table.ngfb-settings td textarea { padding:2px; }
				table.ngfb-settings td textarea.wide { 
					width:100%; 
					height:5em; 
				}
				table.ngfb-settings td select { width:250px; }
				table.ngfb-settings td select.short { width:100px; }
				table.ngfb-settings td select.medium { width:160px; }
			</style>
			<?php
		}

	}
}

?>
