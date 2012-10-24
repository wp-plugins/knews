<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

global $wpdb;

if (version_compare(get_option('knews_version','0.0.0'), '1.1.0') < 0) {
	//The 1.1.0 added fields & tables
	
	$sql =	"ALTER TABLE " .KNEWS_NEWSLETTERS . " ADD COLUMN lang varchar(3) NOT NULL DEFAULT ''";
	$wpdb->query($sql);
	$sql =	"ALTER TABLE " .KNEWS_NEWSLETTERS . " ADD COLUMN automated varchar(1) NOT NULL DEFAULT 0";
	$wpdb->query($sql);

	if (!$this->tableExists(KNEWS_NEWSLETTERS_SUBMITS)) {
	
		$sql =	"CREATE TABLE " . KNEWS_NEWSLETTERS_SUBMITS . " (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				blog_id bigint(20) UNSIGNED NOT NULL DEFAULT " . $this->KNEWS_MAIN_BLOG_ID . ",
				newsletter int(11) NOT NULL,
				finished tinyint(1) NOT NULL,
				paused tinyint(1) NOT NULL,
				start_time timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
				end_time timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
				users_total int(11) NOT NULL,
				users_ok int(11) NOT NULL,
				users_error int(11) NOT NULL,
				priority tinyint(4) NOT NULL,
				strict_control varchar(100) NOT NULL,
				emails_at_once int(11) NOT NULL,
				special varchar(32) NOT NULL,
				UNIQUE KEY id (id)
			   )$charset_collate;";
		
		dbDelta($sql);

	} else {

		$sql = "SHOW COLUMNS FROM " . KNEWS_NEWSLETTERS_SUBMITS . " LIKE 'blog_id'";
		$exists = $wpdb->get_results($sql);
		if (count($exists)==0) {
	
			$sql =	"ALTER TABLE " .KNEWS_NEWSLETTERS_SUBMITS . " ADD COLUMN blog_id bigint(20) UNSIGNED NOT NULL DEFAULT " . $this->KNEWS_MAIN_BLOG_ID;
			$wpdb->query($sql);
	
		}
	}
	
	if ($wpdb->prefix != $wpdb->base_prefix) {
		if ($this->tableExists($wpdb->prefix . 'knewsubmits')) {
	
			$query = "SELECT * FROM " . $wpdb->prefix . "knewsubmits";
			$submit_pend = $wpdb->get_results( $query );
			
			foreach ($submit_pend as $sp) {
				
				$query = 'INSERT INTO ' . KNEWS_NEWSLETTERS_SUBMITS . ' (blog_id, newsletter, finished, paused, start_time, users_total, users_ok, users_error, priority, strict_control, emails_at_once, special, end_time) VALUES (' . get_current_blog_id() . ', ' . $sp->newsletter . ', ' . $sp->finished . ', ' . $sp->paused . ', \'' . $sp->start_time . '\', ' . $sp->users_total . ', ' . $sp->users_ok . ', ' . $sp->users_error . ', ' . $sp->priority . ', \'' . $sp->strict_control . '\', ' . $sp->emails_at_once . ', \'' . $sp->special . '\', \'' . $sp->end_time . '\')';
				$results = $wpdb->query( $query );
				$submit_confirmation_id=$wpdb->insert_id; $submit_confirmation_id2=mysql_insert_id(); if ($submit_confirmation_id==0) $submit_confirmation_id=$submit_confirmation_id2;

				if ($submit_confirmation_id != 0) {
					$query  = "UPDATE " . KNEWS_NEWSLETTERS_SUBMITS_DETAILS . " SET submit=" . $submit_confirmation_id . " WHERE submit=" . $sp->id;
					$results = $wpdb->query( $query );
				}
			}
		}
	}	
}

if (version_compare(get_option('knews_version','0.0.0'), '1.2.2') < 0) {
	//Missing field in the 1.2.0 and 1.2.1 installations bug
	if ($this->tableExists(KNEWS_AUTOMATED)) {
		
		if (strcasecmp($wpdb->get_var("show columns from " . KNEWS_AUTOMATED . " like 'last_run'"), 'last_run') != 0) {
			$sql =	"ALTER TABLE " .KNEWS_AUTOMATED . " ADD COLUMN last_run datetime NOT NULL";
			$wpdb->query($sql);
		}
	}
}

if (version_compare(get_option('knews_version','0.0.0'), '1.2.0') < 0) {
	//The 1.2.0 added fields & tables

	$sql =	"CREATE TABLE " .KNEWS_AUTOMATED . " (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			name varchar(100) NOT NULL,
			selection_method varchar(1) NOT NULL,
			target_id bigint(20) UNSIGNED NOT NULL,
			newsletter_id bigint(20) UNSIGNED NOT NULL,
			lang varchar(3) NOT NULL,
			paused varchar(1) NOT NULL,
			auto varchar(1) NOT NULL,
			every_mode int(11) NOT NULL,
			every_time int(11) NOT NULL,
			what_dayweek int(11) NOT NULL,
			every_posts int(11) NOT NULL,
			last_run datetime NOT NULL,
			UNIQUE KEY id (id)
		   )$charset_collate;";
		   
	dbDelta($sql);

	$sql =	"CREATE TABLE " .KNEWS_AUTOMATED_POSTS . " (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			id_automated bigint(20) UNSIGNED NOT NULL,
			id_post bigint(20) UNSIGNED NOT NULL,
			id_news bigint(20) UNSIGNED NOT NULL,
			when_scheduled datetime NOT NULL,
			UNIQUE KEY id (id)
		   )$charset_collate;";
		   
	dbDelta($sql);

	$this->knews_admin_messages = sprintf("Knews updated the database successfully. Welcome to %s version.", KNEWS_VERSION);
}

if (version_compare(get_option('knews_version','0.0.0'), '1.2.3') < 0) {
	//The 1.2.3 added fields & tables
	$sql =	"ALTER TABLE " . KNEWS_LISTS . " ADD COLUMN orderlist int(11) NOT NULL DEFAULT 0";
	$wpdb->query($sql);
}

update_option('knews_version', KNEWS_VERSION);
update_option('knews_advice_time', 0);

function knews_update_hooks() {
	//Reset hooks (bug in 1.2.0 - 1.2.3 versions)
	if (wp_next_scheduled('knews_wpcron_function_hook')) wp_clear_scheduled_hook('knews_wpcron_function_hook');
	if (wp_next_scheduled('knews_wpcron_automate_hook')) wp_clear_scheduled_hook('knews_wpcron_automate_hook');

	if (!wp_next_scheduled('knews_wpcron_function_hook')) wp_schedule_event( time(), 'knewstime', 'knews_wpcron_function_hook');
	if (!wp_next_scheduled('knews_wpcron_automate_hook')) wp_schedule_event( time(), 'hourly', 'knews_wpcron_automate_hook');
}
add_action('wp', 'knews_update_hooks');

?>