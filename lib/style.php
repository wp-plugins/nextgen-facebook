<?php
/*
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/

This script is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This script is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details at
http://www.gnu.org/licenses/.
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
				.sig {
					font-family:cursive;
					font-size:1.2em;
				}
				.wrap { 
					font-size:1em; 
					line-height:1.3em; 
				}
				.wrap h2 { 
					margin:0 0 10px 0; 
				}
				.wrap p { 
					text-align:justify; 
					line-height:1.2em; 
					margin:10px 0 10px 0;
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
				.inside {
					padding:2px 14px 2px 12px	!important;
				}
				.save_button { 
					text-align:center;
					margin:15px 0 0 0;
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
				.postbox_website {
					height:440px;
					overflow-y:auto;
				}
				.closed {
					height:auto;
				}
				.rss-manager table {
				}
				.rss-manager p {
					text-align:justify;
					margin:7px 0 14px 0;
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
					white-space:pre;
					overflow:auto;
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
					width:200px;
				}
				table.ngfb-settings th.short { 
					width:120px;
				}
				table.ngfb-settings th.side { 
					padding:0 2px 0 2px; 
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
					width:140px;
				}
				table.ngfb-settings td.blank { 
					min-width:100px;
					background-color:#eee; 
					border:1px dashed #ccc;
				}
				table.ngfb-settings td.blank p { 
					margin:8px;
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
