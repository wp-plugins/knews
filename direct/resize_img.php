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

	$Knews_plugin->security_for_direct_pages();

	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	$url_img= $Knews_plugin->get_safe('urlimg');
	$width= intval($Knews_plugin->get_safe('width'));
	$height= intval($Knews_plugin->get_safe('height'));

	echo knews_get_url_img($url_img, $width, $height);
}

function knews_get_url_img($img_url, $width, $height, $cut = true) {
    if ($img_url != '') {
		
		$absolute_dir = substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], 'wp-content'));

		// cut the url
		$url_imatge = substr($img_url, strpos($img_url, 'wp-content'));
		$url=$url_imatge;

		$url_imatge = str_replace('.jpg', '-' . $width . 'x' . $height .'.jpg', $url_imatge);
		$url_imatge = str_replace('.jpeg', '-' . $width . 'x' . $height .'.jpeg', $url_imatge);
		$url_imatge = str_replace('.gif', '-' . $width . 'x' . $height .'.gif', $url_imatge);
		$url_imatge = str_replace('.png', '-' . $width . 'x' . $height .'.png', $url_imatge);

		$url_imatge = str_replace('.JPG', '-' . $width . 'x' . $height .'.JPG', $url_imatge);
		$url_imatge = str_replace('.JPEG', '-' . $width . 'x' . $height .'.JPEG', $url_imatge);
		$url_imatge = str_replace('.GIF', '-' . $width . 'x' . $height .'.GIF', $url_imatge);
		$url_imatge = str_replace('.PNG', '-' . $width . 'x' . $height .'.PNG', $url_imatge);
				
		if (is_file($absolute_dir . $url_imatge)) {
			return get_bloginfo('wpurl') . '/' . $url_imatge;
	
		} else {
	
			// resize the image
			$thumb = image_resize($absolute_dir . $url, $width, $height, $cut, $width.'x'.$height);
			if (is_string($thumb)) {

				$thumb = substr($thumb, strpos($thumb, 'wp-content'));
				return get_bloginfo('wpurl') . '/' .  $thumb;
	
			} else {
				if (is_file($absolute_dir . $url)) {
					return get_bloginfo('wpurl') . '/' . $url;
				} else {
					return 'error';
				}
			}
		}

	} else {
		return 'error';
	}
}

?>