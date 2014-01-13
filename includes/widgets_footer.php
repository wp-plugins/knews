			<div id="knewsWidgetCSS" style="display:none">
				<p>CSS for the widget:</p>
				<form method="get" action=".">
					<textarea name="knewsCustomCSS" rows="10" cols="40" style="width:100%; height:280px;"></textarea>
					<p style="text-align:right"><input type="button" value="<?php _e('Save','knews'); ?>" class="button-primary" onclick="knewsSaveCSS(this); return false" /></p>
				</form>
			</div>
			<div id="knewsWidgetIFRAME" style="display:none">
				<p>Paste this code into your remote site:</p>
				<form method="get" action=".">
					<textarea name="knewsIFRAME" rows="10" cols="40" style="width:100%; height:200px;"></textarea>
					<p>You can add your own CSS file also. Upload it into your WordPress /wp-uploads folder (for example myown.css file): In the Iframe URL add the param: &css=myown (without the .css extension in param)</p>
					<p style="text-align:right"><input type="button" value="<?php _e('Close','knews'); ?>" class="button-primary" onclick="tb_remove(); return false" /></p>
				</form>
			</div>
			<script type="text/javascript">
				var knewsCSShandler='';
				function knewsOpenCSS(id) {
					knewsCSShandler=id;
					tb_show('CSS for Knews Widget', "#TB_inline?height=400&width=700&inlineId=knewsWidgetCSS");
					cssContent = jQuery('#' + knewsCSShandler).val();
					if (cssContent=='') {
						cssContent="/* Write here your CSS classes. Please, use div.knews_add_user prefix to customize all Knews Subscription widgets at once, or #" + knewsCSShandler.substr(7, knewsCSShandler.length-17) + " prefix to customize this one. Example:  div.knews_add_user input { border: #e00 1px solid; } */";
					}
					jQuery('#TB_window textarea[name="knewsCustomCSS"]').val( cssContent );
				}
				function knewsSaveCSS(popupObj) {
					jQuery ('#' + knewsCSShandler).val( jQuery('#TB_window textarea[name="knewsCustomCSS"]').val() );
					tb_remove();
				}

				function knewsOpenIFRAME(popupObj) {
					popupObj = popupObj.substr(0, popupObj.length-9);
					var fields=new Array('subtitle','labelwhere', 'terms', 'requiredtext'<?php
					
					global $Knews_plugin;
					if (! $Knews_plugin->initialized) $Knews_plugin->init();			
					$extra_fields = $Knews_plugin->get_extra_fields();
					foreach ($extra_fields as $field) {
						echo ", '" . $field->name . "'";
					}
					?>);
					url = '<?php echo admin_url(); ?>admin-ajax.php?action=knewsRemote';
					for (var x=0; x<fields.length; x++) {
						val = ('#' + popupObj + fields[x]);
						url =url + '&' + fields[x] + '=' + jQuery('#' + popupObj + fields[x]).val();
					}
					
					code = '<iframe width="100%" scrolling="no" frameborder="0" title="Knews" src="' + url + '" allowtransparency="true" hspace="0" marginheight="0" marginwidth="0" style="border:0; height: 330px; visibility: visible;" tabindex="0" vspace="0"></iframe>';
					
					tb_show('IFRAME code for Knews Subscription Form for other sites', "#TB_inline?height=400&width=700&inlineId=knewsWidgetIFRAME");
					jQuery('#TB_window textarea[name="knewsIFRAME"]').val( code );
				}
			</script>
