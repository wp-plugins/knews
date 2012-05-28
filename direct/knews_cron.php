<?php
if (!function_exists('add_action')) {
	$path='./';
	for ($x=1; $x<6; $x++) {
		$path .= '../';
		if (@file_exists($path . 'wp-config.php')) {
		    require_once($path . "wp-config.php");
			break;
		}
	}
}
if ($Knews_plugin) {
	
	global $knewsOptions;

	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	$cron_time = time();
	update_option('knews_cron_time', $cron_time);
	
	require ('knews_cron_do.php');
}
?>