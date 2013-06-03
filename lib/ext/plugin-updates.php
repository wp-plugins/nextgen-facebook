<?php
/*
	Plugin Update Class

	Copyright (c) 2012 by Rob Landry

	USE OF THIS SOFTWARE MEANS YOU AGREE WITH THE LICENSE.TXT PROVIDED
	WITH THE SOFTWARE.
*/


/******************************************************************************
/	Check for Plugin Updates
/*****************************************************************************/
if (!class_exists('plugin_check_for_updates')) {
class plugin_check_for_updates {


	#----------------------------------------------------------------------
	# Variables
	# Since: 1.0
	#----------------------------------------------------------------------
	public $json_url = '';
	public $file_name = '';
	public $slug = '';
	public $time_period = 12;
	public $update_info_option = '';
	public $sched_name = 'every12hours';


	#----------------------------------------------------------------------
	# The Constructor
	# Since: 1.0
	# A function to construct the class
	#----------------------------------------------------------------------
	function __construct($json_url, $file_name, $slug = '', $time_period = 12, $update_info_option = '') {
		if (empty($slug)) $slug = basename($file_name, '.php');
		if (empty($update_info_option))	$update_info_option = 'external_updates-' . $slug;
		$this->json_url = $json_url;
		$this->file_name = plugin_basename($file_name);
		$this->slug = $slug;
		$this->time_period = $time_period;
		$this->sched_name = 'every' . $this->time_period . 'hours';
		$this->update_info_option = $update_info_option;
		$this->install_hooks();
	}


	#----------------------------------------------------------------------
	# Install Hooks
	# Since: 1.0
	# A function to Install the Cron Hooks
	#----------------------------------------------------------------------
	function install_hooks() {
		$cron_hook = 'pcfu_updates-' . $this->slug;
		add_filter('plugins_api', array(&$this, 'inject_data'), 10, 3);
		add_filter('site_transient_update_plugins', array(&$this,'inject_update'));
		if ($this->time_period > 0) {
			add_filter('cron_schedules', array(&$this, 'pcfu_custom_schedule'));
			add_action($cron_hook, array(&$this, 'check_for_updates'));
			if (!wp_next_scheduled($cron_hook) && !defined('WP_INSTALLING'))
				wp_schedule_event(time(), $this->sched_name, $cron_hook);	
		} else { wp_clear_scheduled_hook($cron_hook); }
	}


	#----------------------------------------------------------------------
	# Inject Data Filter
	# Since: 1.0
	# A filter to inject our data into plugins_api
	#----------------------------------------------------------------------
	function inject_data($result, $action = null, $args = null) {
	    	$found = ($action == 'plugin_information') && isset($args->slug) && ($args->slug == $this->slug);
		if (!$found) return $result;
		$plugin_data = $this->get_json();
		if ($plugin_data) return $plugin_data->json_to_wp();
		return $result;
	}


	#----------------------------------------------------------------------
	# Inject Update Filter
	# Since: 1.0
	# A filter to inject our update data into WP
	#----------------------------------------------------------------------
	function inject_update($updates) {

		// remove existing WP entry - added by jsm@surniaulula.com 2013/06/03
		if ( ! empty( $updates->response[$this->file_name] ) )
			unset( $updates->response[$this->file_name] );

		$option_data = get_site_option($this->update_info_option);
		if (!empty($option_data) && isset($option_data->update) && !empty($option_data->update)) {
			if (version_compare($option_data->update->version, $this->get_installed_version(), '>')) {
				$updates->response[$this->file_name] = $option_data->update->json_to_wp();
			}
		}
		return $updates;
	}


	#----------------------------------------------------------------------
	# Add Custom Schedule Filter
	# Since: 1.0
	# A filter to add our custom schedule to WP
	#----------------------------------------------------------------------
	function pcfu_custom_schedule($schedule) {
		if ($this->time_period && ($this->time_period > 0)) {
			$schedule[$this->sched_name] = array(
				'interval' => $this->time_period * 3600,
				'display' => sprintf('Every %d hours', $this->time_period)
			);
		}
		return $schedule;
	}


	#----------------------------------------------------------------------
	# Check for Updates
	# Since: 1.0
	# A function to get the installed version and see if an update is needed.
	#----------------------------------------------------------------------
	function check_for_updates() {
		$option_data = get_site_option($this->update_info_option);
		if (empty($option_data)) {
			$option_data = new StdClass;
			$option_data->lastCheck = 0;
			$option_data->checkedVersion = '';
			$option_data->update = null;
		}
		$option_data->lastCheck = time();
		$option_data->checkedVersion = $this->get_installed_version();
		update_site_option($this->update_info_option, $option_data);
		$option_data->update = $this->get_update();
		update_site_option($this->update_info_option, $option_data);
	}


	#----------------------------------------------------------------------
	# Get Update
	# Since: 1.0
	# A function to get the update info
	#----------------------------------------------------------------------
	function get_update() {
		$plugin_data = $this->get_json(array('checking_for_updates' => '1'));
		if ($plugin_data == null) return null;
		return plugin_update::from_plugin_data($plugin_data);
	}


	#----------------------------------------------------------------------
	# Get Json
	# Since: 1.0
	# A function to get the JSON
	#----------------------------------------------------------------------
	function get_json($query = array()) {
		$query['installed_version'] = $this->get_installed_version();
		$query = apply_filters('pcfu_get_json_query-'.$this->slug, $query);
		$options = array('timeout' => 10, 'headers' => array('Accept' => 'application/json'));
		$options = apply_filters('pcfu_get_json_options-'.$this->slug, array());
		$url = $this->json_url;
		if (!empty($query)) $url = add_query_arg($query, $url);
		$result = wp_remote_get($url,$options);
		$plugin_data = null;
		if (!is_wp_error($result)
			&& isset($result['response']['code'])
			&& ($result['response']['code'] == 200)
			&& !empty($result['body'])) {
			$plugin_data = plugin_data::from_json($result['body']);
		}
		$plugin_data = apply_filters('pcfu_get_json_result-'.$this->slug, $plugin_data, $result);
		return $plugin_data;
	}


	#----------------------------------------------------------------------
	# Get installed version
	# Since: 1.0
	# A function to get the installed version
	#----------------------------------------------------------------------
	function get_installed_version() {
		$return = '';
		if (!function_exists('get_plugins')) require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		$plugins = get_plugins();
		if (array_key_exists($this->file_name, $plugins)
			&& array_key_exists('Version', $plugins[$this->file_name])) {
			$return = $plugins[$this->file_name]['Version'];
		}
		return $return;
	}


	#----------------------------------------------------------------------
	# Custom Filters
	# Since: 1.0
	# A function to provide custom filters
	#----------------------------------------------------------------------
	function pcfu_custom_filter($callback,$ftype){
		if ($ftype=='query') add_filter('pcfu_get_json_query-'.$this->slug, $callback);
		if ($ftype=='options') add_filter('pcfu_get_json_options-'.$this->slug, $callback);
		if ($ftype=='result') add_filter('pcfu_get_json_result-'.$this->slug, $callback, 10, 2);
	}
} # End Class
} # End If



/******************************************************************************
/	Check for Plugin Updates
/*****************************************************************************/
if (!class_exists('plugin_data')) {
class plugin_data {

 
	#----------------------------------------------------------------------
	# Variables
	# Since: 1.0
	# These Variables are the json fields
	#----------------------------------------------------------------------
	public $id = 0;
	public $name;
	public $slug;
	public $version;
	public $homepage;
	public $sections;
	public $download_url;
	public $author;
	public $author_homepage;
	public $requires;
	public $tested;
	public $upgrade_notice;
	public $rating;
	public $num_ratings;
	public $downloaded;
	public $last_updated;


	#----------------------------------------------------------------------
	# From JSON
	# Since: 1.0
	# A function to take plugin_data JSON and copy what we want
	#----------------------------------------------------------------------
	public static function from_json($json) {
		$json_data = json_decode($json);
		if (empty($json_data) || !is_object($json_data)) return null;
		$exists = isset($json_data->name) && !empty($json_data->name)
			&& isset($json_data->version) && !empty($json_data->version);
		if (!$exists) return null;
		$plugin_data = new plugin_data();
		foreach(get_object_vars($json_data) as $key => $value) {
			$plugin_data->$key = $value;
		}
		return $plugin_data;
	}


	#----------------------------------------------------------------------
	# JSON to WP
	# Since: 1.0
	# A function to convert our JSON to WP
	#----------------------------------------------------------------------
	public function json_to_wp(){
		$fields = array(
			'name', 'slug', 'version', 'requires', 'tested', 'rating', 'upgrade_notice',
			'num_ratings', 'downloaded', 'homepage', 'last_updated','download_url','author_homepage'
		);
		$data = new StdClass;
		foreach($fields as $field){
			if (isset($this->$field)) {
				if ($field == 'download_url') {
					$data->download_link = $this->download_url; }
				elseif ($field == 'author_homepage') {
					$data->author = sprintf('<a href="%s">%s</a>', $this->author_homepage, $this->author); }
				else { $data->$field = $this->$field; }
			} elseif ($field == 'author_homepage') { $data->author = $this->author; }
		}
		if (is_array($this->sections)) { $data->sections = $this->sections; }
		elseif (is_object($this->sections)) { $data->sections = get_object_vars($this->sections); }
		else { $data->sections = array('description' => ''); }
		return $data;
	}
} # End Class
} # End If



/******************************************************************************
/	Check for Plugin Updates
/*****************************************************************************/
if (!class_exists('plugin_update')) {
class plugin_update {


	#----------------------------------------------------------------------
	# Variables
	# Since: 1.0
	#----------------------------------------------------------------------
	public $id = 0;
	public $slug;
	public $version;
	public $homepage;
	public $download_url;
	public $upgrade_notice;


	#----------------------------------------------------------------------
	# From JSON
	# Since: 1.0
	# A function to take plugin_data JSON and copy what we want
	#----------------------------------------------------------------------
	public static function from_json($json){
		$plugin_data = plugin_data::from_json($json);
		if ($plugin_data != null) { return self::from_plugin_data($plugin_data);
		} else { return null; }
	}


	#----------------------------------------------------------------------
	# From Plugin Data
	# Since: 1.0
	# A function to copy some fields
	#----------------------------------------------------------------------
	public static function from_plugin_data($data){
		$plugin_update = new plugin_update();
		$fields = array('id', 'slug', 'version', 'homepage', 'download_url', 'upgrade_notice');
		foreach($fields as $field) { $plugin_update->$field = $data->$field; }
		return $plugin_update;
	}


	#----------------------------------------------------------------------
	# JSON to WP
	# Since: 1.0
	# A function to convert our JSON to WP
	#----------------------------------------------------------------------
	public function json_to_wp(){
		$plugin_data = new StdClass;
		$fields = array(
			'id'=>'id',
			'slug'=>'slug',
			'new_version'=>'version',
			'url'=>'homepage',
			'package'=>'download_url',
			'upgrade_notice'=>'upgrade_notice');
		foreach ($fields as $new_field => $old_field) {
			if (isset($this->$old_field)) {
				$plugin_data->$new_field = $this->$old_field;
			}
		}

		return $plugin_data;
	}
} # End Class
} # End If

?>
