<?php
	require_once( KNEWS_DIR . '/includes/knews_util.php');

	global $wpdb, $Knews_plugin;

	$langs_code = array();
	$langs_name = array();
	
	if (KNEWS_MULTILANGUAGE) {
		
		$languages = $Knews_plugin->getLangs();
		
		if(!empty($languages)){
			foreach($languages as $l){
				$langs_code[] = $l['language_code'];
				$langs_name[] = $l['native_name'];
			}
		}
	}

	$section = $Knews_plugin->get_safe('section');
	$id_edit = intval($Knews_plugin->get_safe('idnews'));

	if ($section=='' && $Knews_plugin->post_safe('action')=='add_news') {

		$name = mysql_real_escape_string($Knews_plugin->post_safe('new_news'));
		
		if ($name != '') {

			$query = "SELECT * FROM " . KNEWS_NEWSLETTERS . " WHERE name='" . $name . "'";
			$results = $wpdb->get_results( $query );
			
			if (count($results)==0) {
				
				$template = mysql_real_escape_string($Knews_plugin->post_safe('template'));

				if ($template != '') {

					$fileTemplate = KNEWS_DIR . '/templates/' . $_POST['template'] . '/template.html';
					$fh = fopen($fileTemplate, 'r');
					$codeTemplate = fread($fh, filesize($fileTemplate));
					fclose($fh);
	
					$codeTemplate = str_replace('  ', ' ', $codeTemplate);
					$codeTemplate = str_replace('<!-- ', '<!--', $codeTemplate);
					$codeTemplate = str_replace(' -->', '-->', $codeTemplate);
					$codeTemplate = str_replace('<!--[ ', '<!--[', $codeTemplate);
					$codeTemplate = str_replace(' ]-->', ']-->', $codeTemplate);
	
					$codeTemplate = str_replace('<!--[start_editable_content]-->', '<span class="content_editable">', $codeTemplate);
					$codeTemplate = str_replace('<!--[end_editable_content]-->', '</span>', $codeTemplate);
	
					$headTemplate = substr($codeTemplate, 0, strpos($codeTemplate, '</head>')+7);

					$bodyTemplate = cut_code('<body>', '</body>', $codeTemplate, true);

					$bodyTemplate = str_replace('"images/', '"' . KNEWS_URL . '/templates/' . $_POST['template'] . '/images/', $bodyTemplate);
					$bodyTemplate = str_replace('url(images', 'url(' . KNEWS_URL . '/templates/' . $_POST['template'] . '/images/', $bodyTemplate);
	
					$count_modules=0; $found_module=true; $codeModule='';
					while ($found_module) {
						$found_module=false;
	
						if (strpos($bodyTemplate, '[start module ' . ($count_modules + 1) . ']') !== false) {
							$found_module=true;

							$codeModule .= '<div class="insertable"><img src="' . KNEWS_URL . '/templates/' . $_POST['template'] . '/modules/module_' . ($count_modules + 1) . '.jpg" width="220" height="90" alt="" /><div class="html_content">';
							$codeModule .= cut_code('<!--[start module ' . ($count_modules + 1) . ']-->', '<!--[end module ' . ($count_modules + 1) . ']-->', $bodyTemplate, true);
							$codeModule .= '</div></div>';

							$bodyTemplate = extract_code('<!--[start module ' . ($count_modules + 1) . ']-->', '<!--[end module ' . ($count_modules + 1) . ']-->', $bodyTemplate, true);
							$count_modules++;
						}
					}
					
					$containerModulesTemplate =	cut_code('<!--[open_insertion_container_start]-->', '<!--[close_insertion_container_start]-->', $bodyTemplate, true) .
												cut_code('<!--[open_insertion_container_end]-->', '<!--[close_insertion_container_end]-->', $bodyTemplate, true);
					
					$bodyTemplate = iterative_extract_code('<!--[open_ignore_code]-->', '<!--[close_ignore_code]-->', $bodyTemplate, true);
					$bodyTemplate = iterative_extract_code('<!--', '-->', $bodyTemplate, true);
					$codeTemplate = str_replace('  ', ' ', $codeTemplate);
	
					$date = $Knews_plugin->get_mysql_date();
					
					if (!is_utf8($bodyTemplate)) $bodyTemplate=utf8_encode($bodyTemplate);
					if (!is_utf8($headTemplate)) $headTemplate=utf8_encode($headTemplate);
					if (!is_utf8($codeModule)) $codeModule=utf8_encode($codeModule);

					$bodyTemplate = mysql_real_escape_string($Knews_plugin->htmlentities_corrected($bodyTemplate));
					$headTemplate = mysql_real_escape_string($Knews_plugin->htmlentities_corrected($headTemplate));
					$codeModule = mysql_real_escape_string($Knews_plugin->htmlentities_corrected($codeModule));

					$sql = "INSERT INTO " . KNEWS_NEWSLETTERS . "(name, created, modified, template, html_mailing, html_head, html_modules, html_container, subject) VALUES ('" . $name . "', '" . $date . "', '" . $date . "','" . $template . "','" . $bodyTemplate . "','" . $headTemplate . "','" . $codeModule . "','" . $containerModulesTemplate . "','')";

					if ($wpdb->query($sql)) {
						$id_edit=$wpdb->insert_id; $id_edit2=mysql_insert_id(); if ($id_edit==0) $id_edit=$id_edit2;
						
						$section='add_news';
						echo '<div class="updated"><p>' . __('The newsletter has been created successfully','knews') . '</p></div>';
					} else {
						echo '<div class="error"><p><strong>' . __('Error','knews') . ': </strong>' . __('Failed to create the Newsletter','knews') . ' : ' . $wpdb->last_error . '</p></div>';
					}
				} else {
					echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __('You must choose a template!','knews') . '</p></div>';
				}
			} else {
				echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __('There is already another newsletter with the same name!','knews') . '</p></div>';
			}
		} else {
			echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __('You must choose a name for the new newsletter!','knews') . '</p></div>';
		}

		
	}

	if ($section=='edit') {
		require( KNEWS_DIR . '/admin/knews_admin_news_edit.php');
	} else if ($section=='send') {
		require( KNEWS_DIR . '/admin/knews_admin_news_send.php');		
	} else if ($section=='add_news') {
		?>
		<script type="text/javascript">
			function goto_editor() {
				location.href = '<?php bloginfo('url')?>/wp-admin/admin.php?page=knews_news&section=edit&idnews=<?php echo $id_edit; ?>';
			}
			jQuery(document).ready ( function() {
				setTimeout ('goto_editor()', 1000); // 1 second
			});
		</script>
		<p><a href="<?php bloginfo('url')?>/wp-admin/admin.php?page=knews_news&section=edit&idnews=<?php echo $id_edit; ?>"><?php _e('Redirecting to editor...','knews'); ?></a></p>
		<?
	} else {
		require( KNEWS_DIR . '/admin/knews_admin_news_list.php');
	}
?>
