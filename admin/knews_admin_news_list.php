<?php
	//global $Knews_plugin;
	//require_once( KNEWS_DIR . '/includes/knews_util.php');

	if ($Knews_plugin->get_safe('da')=='rename') {
		$query = "UPDATE ".KNEWS_NEWSLETTERS." SET name='" . urldecode($Knews_plugin->get_safe('nn')) . "' WHERE id=" . intval($Knews_plugin->get_safe('nid'));
		$result=$wpdb->query( $query );
		echo '<div class="updated"><p>' . __('Newsletter name updated','knews') . '</p></div>';
	}

	if ($Knews_plugin->get_safe('da')=='delete') {
		$query="DELETE FROM " . KNEWS_NEWSLETTERS . " WHERE id=" . intval($Knews_plugin->get_safe('nid'));
		$results = $wpdb->query( $query );
		echo '<div class="updated"><p>' . __('Newsletter deleted','knews') . '</p></div>';
	}

	if ($Knews_plugin->post_safe('action')=='delete_news') {
		$query = "SELECT * FROM " . KNEWS_NEWSLETTERS;
		$results = $wpdb->get_results( $query );
		foreach ($results as $list) {
			if ($Knews_plugin->post_safe('batch_' . $list->id)=='1') {
				$query="DELETE FROM " . KNEWS_NEWSLETTERS . " WHERE id=" . $list->id;
				$results=$wpdb->query($query);
			}
		}
		echo '<div class="updated"><p>' . __('Newsletter list updated','knews') . '</p></div>';
	}

?>
<script type="text/javascript">
function enfocar() {
	setTimeout("jQuery('#new_news').focus();", 100);
}
</script>
	<div class=wrap>
			<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div><h2><?php _e('Newsletters','knews');?><a class="add-new-h2" href="#newnews" onclick="enfocar()"><?php _e('Create new newsletter','knews'); ?></a></h2>
				<?php
					$query = "SELECT id, name, created, modified, template FROM " . KNEWS_NEWSLETTERS . " ORDER BY modified DESC";
					$results = $wpdb->get_results( $query );
					if (count($results) != 0) {
				?>
					<script type="text/javascript">
					var save_link='';
					var save_id='';
					
					function rename(n) {
						if (save_id != '') rename_cancel();
						save_id = n;
						save_link = jQuery('td.name_' + n).html();
						
						jQuery('td.name_' + n).html('<input type="text" value="' + jQuery('td.name_' + n + ' a').html() + '"><input type="button" value="Rename" class="rename_do"><input type="button" value="Cancel" class="rename_cancel">');
						
						jQuery('td.name_' + n + ' input')[0].focus();

						jQuery('input.rename_cancel').click(function() {
							rename_cancel();
							return false;
						});

						jQuery('input.rename_do').click(function() {
							location.href="admin.php?page=knews_news&da=rename&nid=" + save_id + '&nn=' + encodeURIComponent(jQuery('td.name_' + save_id + ' input').val());
						});

						return false;
					}
					
					function rename_cancel() {
						if (save_id != '') {
							jQuery('td.name_' + save_id).html(save_link);
							save_id='';
						}
					}

					</script>
					<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					<table class="widefat">
						<thead>
							<tr>
								<th class="manage-column column-cb check-column"><input type="checkbox" /></th>
								<th align="left"><?php _e('Newsletter name','knews');?></th>
								<th><?php _e('Created','knews');?></th>
								<th><?php _e('Modified','knews');?></th>
								<th><?php _e('Template','knews');?></th>
								<th><?php _e('Submit','knews');?></th>
							</tr>
						</thead>
						<tbody>
				<?php
						$alt=false;
						foreach ($results as $list) {
							echo '<tr' . (($alt) ? ' class="alt"' : '') . '><th class="check-column"><input type="checkbox" name="batch_' . $list->id . '" value="1"></th>';
							echo '<td class="name_' . $list->id  . '"><strong><a href="admin.php?page=knews_news&section=edit&idnews=' . $list->id . '">' . $list->name . '</a></strong>';

							echo '<div class="row-actions"><span><a title="' . __('Edit this newsletter', 'knews') . '" href="admin.php?page=knews_news&section=edit&idnews=' . $list->id . '">' . __('Edit', 'knews') . '</a> | </span>';
							
							echo '<span><a href="#" title="' . __('Rename this newsletter', 'knews') . '" onclick="rename(' . $list->id . '); return false;">' . __('Rename', 'knews') . '</a> | </span>';
							echo '<span><a href="' . KNEWS_URL . '/direct/knews_read_email.php?id=' . $list->id . '" target="_blank" title="' . __('Open a preview in a new window', 'knews') . '">' . __('Preview', 'knews') . '</a> | </span>';
							echo '<span><a href="admin.php?page=knews_news&section=send&id=' . $list->id . '" title="' . __('Submit this newsletter', 'knews') . '">' . __('Submit', 'knews') . '</a> | </span>';
							
							echo '<span class="trash"><a href="admin.php?page=knews_news&da=delete&nid=' . $list->id . '" title="' . __('Delete definitely this newsletter', 'knews') . '" class="submitdelete">' . __('Delete', 'knews') . '</a></span></div></td>';

							echo '<td>' . $Knews_plugin->humanize_dates($list->created, 'mysql') . '</td>';
							echo '<td>' . $Knews_plugin->humanize_dates($list->modified, 'mysql') . '</td>';
							echo '<td>' . $list->template . '</td>';
							echo '<td><a href="admin.php?page=knews_news&section=send&id=' . $list->id . '">' . __('Submit', 'knews') . '</a></td>';
							//echo '<td><a href="admin.php?page=knews_news&section=edit&idnews=' . $list->id . '">' . __('Edit', 'knews') . '</a></td>';
							//echo '<td align="center"><input type="checkbox" value="1" name="' . $list->id . '_delete" id="' . $list->id . '_delete" /></td>';
							echo '</tr>';

							$alt=!$alt;
						}
				?>
						</tbody>
						<tfoot>
							<tr>
								<th class="manage-column column-cb check-column"><input type="checkbox" /></th>
								<th align="left"><?php _e('Newsletter name','knews');?></th>
								<th><?php _e('Created','knews');?></th>
								<th><?php _e('Modified','knews');?></th>
								<th><?php _e('Template','knews');?></th>
								<th><?php _e('Submit','knews');?></th>
							</tr>
						</tfoot>
					</table>
					<div class="submit">
						<select name="action">
							<option selected="selected" value=""><?php _e('Batch actions','knews'); ?></option>
							<option value="delete_news"><?php _e('Delete','knews'); ?></option>
						</select>
						<input type="submit" value="<?php _e('Apply','knews'); ?>">
					</div>
					</form>
				<?php
					} else {
						?>
							<p><?php _e('At the moment there is no newsletter, you can create new ones','knews'); ?></p>
						<?php
					}
				?>
					<hr />
					<a id="newnews"></a>
					<h2><?php _e('Create new newsletter','knews');?></h2>
					<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
						<input type="hidden" name="action" id="action" value="add_news" />
						<p><label for="new_list"><?php _e('Name','knews');?>: </label><input type="text" name="new_news" id="new_news" class="regular-text" /><p>
						<p><?php _e('Choose a template','knews');?>: </p>
						<?php
						chdir (KNEWS_DIR . '/templates');
						$folders = scandir( '.' );
						foreach ($folders as $folder) {
							if ($folder != '..' && $folder != '.' && is_dir($folder) && is_file(KNEWS_DIR . '/templates/' . $folder . '/info.xml') && is_file(KNEWS_DIR . '/templates/' . $folder . '/template.html')) {
								$xml_info = array (
									'shortname' => $folder,
									'fullname' => 'Not defined',
									'url' => '',
									'date' => 'Unknown',
									'author' => 'Unknown',
									'urlauthor' => '',
									'minver' => '1.0.0',
									'onlypro' => 'no',
									'description' => 'Not defined'
								);

								$xml = simplexml_load_file(KNEWS_DIR . '/templates/' . $folder . '/info.xml');

								foreach($xml->children() as $child) {
									$xml_info[$child->getName()] = $child;
								}
								
						?>
							<div style="padding:10px; float:left; width:250px;">
						<?php
								$selectable=false;
								if (version_compare( KNEWS_VERSION, $xml_info['minver'] ) >= 0) {
									if ($xml_info['onlypro'] != 'yes' || $Knews_plugin->im_pro()==true) {
										$selectable=true;
										
										echo '<div style="text-align:center"><a href="#" onclick="jQuery(\'input\', jQuery(this).parent().parent()).attr(\'checked\', true); return false;" title="' . __('Select this template','knews') . '">';
									}
								}
						?>
								<img src="<?php echo KNEWS_URL . '/templates/' . $folder; ?>/thumbnail.jpg" style="padding-right:20px;" />
								<?php if ($selectable) echo '</a>'; ?></div>
								<div>
									<h1 style="font-size:20px; padding:0 0 10px 0; margin:0">
									<?php
									if ($selectable) echo '<input type="radio" name="template" value="' . $folder . '" />';

									echo $xml_info['shortname'] . '</h1>';
									if (version_compare( KNEWS_VERSION, $xml_info['minver'] ) < 0) {
										echo '<p style="color:#e00; font-weight:bold;">';
										printf(__('This template requires Knews version %s you must update Knews before use this template'), $xml_info['minver'] . (($xml_info['onlypro'] == 'yes') ? ' Pro' : ''));
										echo '</p>';
									} else {
										if ($xml_info['onlypro'] == 'yes' && !$Knews_plugin->im_pro()) {
											echo '<p style="color:#e00; font-weight:bold;">';
											printf( __('This template requires the professional version of Knews. You can get it %s here','knews'),'<a href="http://www.knewsplugin.com" target="_blank">');
											echo '</a></p>';
										}
									}
									?>
									<h2 style="font-size:16px; padding:0 0 6px 0; margin:0; line-height:20px;"><?php echo $xml_info['fullname']; ?></h2>
									<p style="font-size:13px; padding:0 0 6px 0; margin:0"><strong><?php echo (($xml_info['urlauthor'] != '') ? '<a href="' . $xml_info['urlauthor'] . '" target="_blank">' : '') . $xml_info['author'] . (($xml_info['urlauthor'] != '') ? '</a>' : '') . '</strong> (' . $xml_info['date'] . ')'; ?></p>
									<?php
									if ($xml_info['url'] != '') {
									?>
									<p style="font-size:13px; padding:0 0 6px 0; margin:0"><a href="<?php echo $xml_info['url']; ?>" target="_blank"><?php _e('Go to template page','knews'); ?></a></p>
									<?php
									}
									?>
									<p style="margin:0; padding:0; font-size:11px; color:#333"><?php echo $xml_info['description']; ?></p>
								</div>
							</div>
						<?php
							}
						}
						?>
						<div style="clear:both;"></div>
						<div class="submit">
							<input type="submit" value="<?php _e('Add newsletter','knews');?>" class="button-primary" />
						</div>
					</form>
	</div>
