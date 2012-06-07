<!--[if lte IE 7]>
<script type="text/javascript">
alert('<?php _e("Warning! IE 6/7 can't edit newsletters! The editor uses HTML5 properties, you need upgrade at least to IE8, or use an modern Firefox, Chrome or Safari.",'knews');?>');
</script>
<![endif]-->

<script type="text/javascript" src="<?php echo KNEWS_URL; ?>/wysiwyg/parent_editor.js?ft=<?php echo filemtime(KNEWS_DIR . '/wysiwyg/parent_editor.js'); ?>"></script>

<link rel="stylesheet" href="<?php echo KNEWS_URL; ?>/wysiwyg/parent_editor.css" type="text/css" media="all" />
<?php
	$query = "SELECT * FROM ".KNEWS_NEWSLETTERS." WHERE id=" . $id_edit;
	$results_news = $wpdb->get_results( $query );
	if (count($results_news) == 0) {
?>

	<div class=wrap>
		<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div><h2><?php _e('Newsletters','knews'); ?></h2>
		<h3><?php echo __('Error','knews') . ': ' . __("Newsletter doesn't exists",'knews'); ?></h3>
	</div>
<?php
	} else {
?>
<script type="text/javascript">
	url_plugin = '<?php echo KNEWS_URL; ?>';
	droppable_code='<?php echo $results_news[0]->html_container; ?>';
	id_news='<?php echo $Knews_plugin->get_safe('idnews');?>';
	<?php
	$one_post = get_posts(array('numberposts' => 1) );
	if (count($one_post)!=1) $one_post = get_pages();
	echo 'one_post_id=' . intval($one_post[0]->ID) . ';';
	?>
	submit_news='<?php bloginfo('url');?>/wp-admin/admin.php?page=knews_news&section=send&id=<?php echo $Knews_plugin->get_safe('idnews');?>';
	
	must_apply_undo = "You are in image edition mode. You must press Apply or Undo image changes before.";
	edit_image= "<?php echo __('Edit image','knews'); ?>";
	sharp_image= "<?php echo __('Apply change and refresh image','knews'); ?>";
	undo_image= "<?php echo __('Undo image changes','knews'); ?>";
	properties_image= "<?php echo __('Properties image','knews'); ?>";
	post_handler= "<?php echo __('Insert post/page content','knews'); ?>";
	free_handler= "<?php echo __('Free text content','knews'); ?>";
	move_handler= "<?php echo __('Move module','knews'); ?>";
	delete_handler= "<?php echo __('Delete module','knews'); ?>";
	unsaved_message= "<?php echo __('If you leave now this page, the Newsletter changes will be lost. Please, cancel and press the \"Save\" button (blue coloured).','knews'); ?>";

	error_resize = "<?php echo __('Error','knews') . ': ' . __('Check the directory permissions for','knews'); ?> '/wp-content/uploads'";
	error_save = "<?php  echo __('Error saving','knews'); ?>";
	ok_save = "<?php  echo __('Newsletter saved','knews'); ?>";
	button_continue_editing = "<?php  echo __('Continue editing','knews'); ?>";
	button_submit_newsletter = "<?php  echo __('Submit newsletter','knews'); ?>";

	confirm_delete = "<?php echo __('Do you really want to delete this module?','knews'); ?>";
	button_yes = "<?php echo __('Yes','knews'); ?>";
	button_no = "<?php echo __('No','knews'); ?>";
	
	opera_no = "<?php echo __("Warning! Opera can't edit newsletters. You must use a modern Firefox, Chrome, Safari or at least Internet Explorer 8.",'knews'); ?>";
</script>
	<div class="wrap">
		<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div><h2><?php _e('Editing newsletter','knews'); ?>: <?php echo $results_news[0]->name; ?></h2>
		<div id="poststuff">
			<div id="titlediv">
				<?php _e('Subject','knews'); ?>:
				<input type="text" id="title" name="title" value="<?php echo $results_news[0]->subject; ?>" style="width:600px;" />
				<?php
				$lang_attr='';
				if ($Knews_plugin->get_safe('lang') != '') {
					$lang_attr='&lang=' . $Knews_plugin->get_safe('lang');
				}
				?>
				<div class="wysiwyg_toolbar">
					<?php /*
					<a href="#" class="move" title="move"></a>
					<a href="#" class="minimize" title="minimize"></a>
					<span class="clear"></span>*/?>
					<div class="tools">
						<?php /*<a href="#" class="toggle_handlers toggle_handlers_off" title="<?php _e('Show/hide handlers','knews'); ?>"></a>*/?>
						<span class="clear"></span>
					</div>
					<div class="save_button">
						<a href="#" class="button-primary" onClick="save_news(); return false;"><?php _e('Save','knews');?></a>
					</div>
					<div class="plegable">
					<?php 
					$query = "SELECT * FROM ".KNEWS_NEWSLETTERS." WHERE id=" . $id_edit;
					$results_news = $wpdb->get_results( $query );

					if (count($results_news) != 0)
						echo $results_news[0]->html_modules; 
					?>
					</div>
					<div class="resize">
						<a href="#" title="<?php _e('Resize toolbox','knews');?>">&nbsp;</a>
					</div>
				</div>
				<div class="editor_iframe">
					<div id="botonera">
						<div class="standard_buttons desactivada">
							<a href="#" title="bold" class="bold" onclick="b_simple('Bold'); return false;">B</a>
							<a href="#" title="italic" class="italic" onclick="b_simple('Italic'); return false;">I</a>
							<a href="#" title="strike-through" class="strike" onclick="b_simple('StrikeThrough'); return false;">St</a>
							<a href="#" title="link" class="link" onclick="b_link(); return false;">A</a>
							<a href="#" title="UN-link" class="no_link" onclick="b_del_link(); return false;">(A)</a>
						</div>
						<div class="justify_buttons desactivada">
							<a href="#" title="align: Left" class="just_l" onclick="b_justify('left'); return false;">L</a>
							<a href="#" title="align: Center" class="just_c" onclick="b_justify('center'); return false;">C</a>
							<a href="#" title="align: Right" class="just_r" onclick="b_justify('right'); return false;">R</a>
							<a href="#" title="align: Justify" class="just_j" onclick="b_justify('justify'); return false;">J</a>
						</div>
						<div class="standard_buttons desactivada">
							<a href="#" class="sup" title="superscript" onclick="b_simple('Superscript'); return false;">sp</a>
							<a href="#" class="sub" title="subscript" onclick="b_simple('Subscript'); return false;">sb</a>
							<a href="#" class="color" title="redo" onclick="b_color(); return false;">C</a>
						</div>
						<div class="do_undo_buttons">
							<a href="#" class="undo" title="undo" onclick="b_simple('undo'); return false;">U</a>
							<a href="#" class="redo" title="redo" onclick="b_simple('redo'); return false;">R</a>
						</div>

						<span class="clear"></span>
					</div>
					<iframe class="knews_editor" id="knews_editor" name="knews_editor" style="width:100%; height:100px" src="<?php echo KNEWS_URL . '/direct/edit_news.php?idnews=' . $id_edit . $lang_attr; ?>"></iframe>
					<div id="tagsnav"></div>
				</div>
				<div class="drag_preview"></div>
			</div>
		</div>
	</div>
<?php
	}
?>