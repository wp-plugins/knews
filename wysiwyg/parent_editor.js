// JavaScript Document

var magic_offset=60;

var io='';

var ratoliX;
var ratoliY;
var scroll_frame_y;

var mouseBtn=false;
var move_item=null;
var move_preview=false;
var droppable_over=null;

var referer_module;
var post_number_module;

var col_picker_ambit;
var col_picker_number;
var col_picker_referer;
var col_picker_obj;

var font_picker_referer;
var font_picker_ambit;
var font_picker_number;

var referer_image;
var referer_delete;
var saved_range='';

var referer_image_size='';
var resizing_image='';
var resizing_image_style='';
var resizing_image_x='';
var resizing_image_y='';
var resizing_image_w='';
var resizing_image_h='';
var resizing_image_handler_x;
var resizing_image_handler_y;
var resizing_image_t;
var resizing_image_l;
var resizing_image_w_undo;
var resizing_image_h_undo;

var to_shitty_ie8='';

var drag_toolbox=false;
var resize_toolbox=false;
var ratoliX_tb=0;
var ratoliY_tb=0;
var ratoli_offset_left=0;
function confirmExit() { return unsaved_message; }
function not_saved() { document.getElementById('knews_editor').contentWindow.window.onbeforeunload = confirmExit; }
function saved() { document.getElementById('knews_editor').contentWindow.window.onbeforeunload = null; }

function dontstart () { return false; }

jQuery(window).load(function() {

	if (jQuery.browser.opera) alert(opera_no);

	io=jQuery('#knews_editor').contents();

	jQuery(io).ready( function () {
		
		//document.getElementById('knews_editor').contentWindow.start();
		document.getElementById('knews_editor').contentWindow.test_browser();
		document.getElementById('knews_editor').contentWindow.browserize_html(jQuery('div.wysiwyg_toolbar')[0]);
		
		jQuery('span.handler').html('<span class="move" title="' + move_handler + '"></span><span class="delete" title="'+delete_handler+'"></span>');
		jQuery('span.handler', io).html('<span class="move" title="' + move_handler + '"></span><span class="delete" title="'+delete_handler+'"></span>');
		
		jQuery('.draggable', io).each( function() {
			document.getElementById('knews_editor').contentWindow.listen_module(this);
		});

		document.getElementById('knews_editor').contentWindow.listen_module();

		resize_frame();
		
		jQuery(io)
			.mousemove(function(e){
				child_scroll = document.getElementById('knews_editor').contentWindow.look_scroll();

				ratoliX=e.pageX - child_scroll[0];
				ratoliY=e.pageY - child_scroll[1]+magic_offset;
				scroll_frame_y=jQuery(io).scrollTop();

				update_preview();
							
				//if (move_preview) e.preventDefault();
			})
			.mousedown(function(){
				mouseBtn=true;
			})
			.mouseup(function(){
				editor_mouseup();
			});
			
		jQuery(document)
			.mousemove(function(e){
				ratoliX=e.pageX - ratoli_offset_left;
				ratoliY=e.pageY - 100;
				
				update_preview();
							
				//if (move_preview) e.preventDefault();
			})
			.mousedown(function(){
				ratoli_offset_left = parseInt(jQuery('#wpcontent').css('marginLeft'), 10);
				mouseBtn=true;
			})
			.mouseup(function(){
				editor_mouseup();
			})
		
		jQuery('div.insertable img')
			.mousedown( function(e) {
				copy_item=jQuery('div.html_content', jQuery(this).parent() );
				jQuery(this).clone().appendTo(jQuery('div.drag_preview'));
		
				move_preview=true; 
				update_preview();
		
				zone=look_zone(jQuery(copy_item).children(':first'));
				if (zone != 0) {
					jQuery('body', io).addClass('doing_drag');
					jQuery('body').addClass('doing_drag');
					jQuery('.droppable_empty', io).hide();
					jQuery('.container_zone_' + zone + ' .droppable_empty', io).show();
				} else {
					jQuery('body', io).addClass('doing_drag');
					jQuery('body').addClass('doing_drag');
				}
		
				//e.returnValue = false;
				//e.cancelBubble=true; 
				//window.event.returnValue = false;
				//window.event.cancelBubble = true;
				if (jQuery.browser.msie  && parseInt(jQuery.browser.version, 10) === 8) {
					this.attachEvent("ondragstart", dontstart );
				} else {
					e.preventDefault();
				}
				//

			});

		jQuery('div.wysiwyg_editor .droppable', io).each(function () {
	
			colors_globals = look_colours('local', this);
			fonts_locals = look_fonts('local', this);
	
			for (x=1; x <= colors_globals.length; x++) {
				jQuery('span.handler', this).append('<a href="#" class="sel_color" title="' + colors_globals[x-1][1] + '" onclick="parent.sel_col(\'local\',' + x + ', this); return false;" style="background-color:' + colors_globals[x-1][0] + '"></a>');
			}
			for (x=1; x <= fonts_locals.length; x++) {
				jQuery('span.handler', this).append('<a href="#" class="sel_font" title="' + fonts_locals[x-1][4] + '" onclick="parent.sel_fon(\'local\',' + x + ', this); return false;"></a>');
			}
	
			if (!look_posts(this)) {
				//El fem editable si s'escau
				jQuery('span.content_editable', this).attr('contenteditable','true');
			}
		});
	
		jQuery('div.wysiwyg_editor span.content_editable', io).not('div.wysiwyg_editor .droppable span.content_editable', io).attr('contenteditable','true');
		
		jQuery('div.wysiwyg_toolbar a.toggle_handlers', io).click(function() {
			jQuery('body', io).toggleClass('hide_handler');
			jQuery('a.toggle_handlers', io).toggleClass('toggle_handlers_off');
			return false;
		});
			
		look_images(jQuery('div.wysiwyg_editor', io)[0]);
		
		colors_globals = look_colours('global', io);
		fonts_globals = look_fonts('global', io);

		for (x=1; x <= colors_globals.length; x++) {
			jQuery('div.wysiwyg_toolbar div.tools span.clear').before('<a href="#" class="sel_color" title="' + colors_globals[x-1][1] + '" style="background-color:' + colors_globals[x-1][0] + '" onclick="sel_col(\'global\',' + x + ',this); return false;"></a>');
		}
		for (x=1; x <= fonts_globals.length; x++) {
			jQuery('div.wysiwyg_toolbar div.tools span.clear').before('<a href="#" class="sel_font" title="' + fonts_globals[x-1][4] + '" onclick="sel_fon(\'global\',' + x + ',this); return false;"></a>');
		}
		
		jQuery('div.wysiwyg_toolbar div.tools a:last').addClass('last');
		
		jQuery('div.wysiwyg_toolbar div.html_content table').addClass('draggable');
		
		jQuery('div.wysiwyg_toolbar a.minimize').click(function() {
			jQuery("div.wysiwyg_toolbar div.plegable").animate({height: "toggle"}, 500);
		});
	
		jQuery('div.wysiwyg_toolbar a.move')
			.mousedown(function() {
				if (!drag_toolbox) {
					drag_toolbox = true;
					
					ratoliX_tb = ratoliX - parseInt(jQuery('div.wysiwyg_toolbar').offset().left, 10) + parseInt(jQuery(window).scrollLeft(), 10) + parseInt(jQuery(parent.window).scrollLeft(), 10) ;
					ratoliY_tb = ratoliY - parseInt(jQuery('div.wysiwyg_toolbar').offset().top, 10) + parseInt(jQuery(document).scrollTop(), 10) + parseInt(jQuery(parent.document).scrollTop(), 10);
	
					return false;
				}
			})
			.mouseup(function() {
				drag_toolbox=false;
				return false;
			});
	
		jQuery('div.wysiwyg_toolbar div.resize a')
			.mousedown(function() {
				if (!resize_toolbox) {
					
					resize_toolbox = true;
					
					//ratoliY_tb = parseInt(jQuery('div.wysiwyg_toolbar').offset().top, 10) + parseInt(jQuery(document).scrollTop(), 10) + parseInt(jQuery(parent.document).scrollTop(), 10);
					ratoliY_tb = ratoliY - parseInt(jQuery('div.wysiwyg_toolbar div.plegable').css('height'), 10);
	
					return false;
				}
			})
			.mouseup(function() {
				resize_toolbox=false;
				return false;
			});
	
		jQuery(parent.window).resize(function() {
			resize_frame();
		});
	
	});
});

function resize_frame() {
	//160+65=225
	//minH=parseInt(jQuery('div.wysiwyg_editor', io).innerHeight(), 10)+100;
	//Si, llegeixo l'alçada del pare!!!
	winH=parseInt(jQuery('body').height(), 10)-230;

	//if (winH > minH) minH = winH;
	
	//if (minH < 500) minH = 500;
	
	jQuery('iframe.knews_editor').css('height', winH);
}

function update_preview() {
	if (move_preview) {
		jQuery('div.drag_preview').css('left', ratoliX + 10)
		jQuery('div.drag_preview').css('top', ratoliY + 10)
	}
	
	if (drag_toolbox) {
		jQuery('div.wysiwyg_toolbar', io)
			.css('left', ratoliX - ratoliX_tb)
			.css('top', ratoliY - ratoliY_tb);
	}
	
	if (resize_toolbox) {
		tb_height = ratoliY - ratoliY_tb;
		if (tb_height < 200) tb_height=200;

		jQuery('div.wysiwyg_toolbar div.plegable').css('height',tb_height);
	}
	
	if (resizing_image!='') {
		ww = parseInt(jQuery(referer_image_size).attr('width'), 10);
		hh = parseInt(jQuery(referer_image_size).attr('height'), 10);
		tt = parseInt(jQuery(referer_image_size).offset().top, 10);
		ll = parseInt(jQuery(referer_image_size).offset().left, 10);

		if (resizing_image=='s' || resizing_image=='se' || resizing_image=='sw') {
			pos_y_hand = resizing_image_handler_y + (ratoliY - resizing_image_y);
			height_img = 4 + pos_y_hand - tt;
			if (height_img > 0) {
				jQuery(referer_image_size).attr('height', height_img);
				hh=height_img;
			}
		}

		if (resizing_image=='n' || resizing_image=='ne' || resizing_image=='nw') {
			//alert(resizing_image_h + ' ' + resizing_image_handler_y + ' ' + (ratoliY-magic_offset));
			height_img = resizing_image_h + resizing_image_handler_y - (ratoliY + scroll_frame_y - magic_offset);
			if (height_img > 0) {
				hh=height_img;
				tt=resizing_image_t - height_img + resizing_image_h;

				jQuery(referer_image_size).attr('height', height_img);
				jQuery(referer_image_size).css('top', tt);
			}
		}

		if (resizing_image=='ne' || resizing_image=='e' || resizing_image=='se') {
			pos_x_hand = resizing_image_handler_x + (ratoliX - resizing_image_x);
			width_img = 4 + pos_x_hand - ll;
			if (width_img > 0) {
				jQuery(referer_image_size).attr('width', width_img);
				ww=width_img;
			}
		}

		if (resizing_image=='nw' || resizing_image=='w' || resizing_image=='sw') {
			width_img = resizing_image_w + resizing_image_handler_x - ratoliX;
			if (width_img > 0) {
				ww=width_img;
				ll=resizing_image_l - width_img + resizing_image_w;

				jQuery(referer_image_size).attr('width', width_img);
				jQuery(referer_image_size).css('left', ll);
			}
		}

		document.getElementById('knews_editor').contentWindow.move_resize_handlers(ww, hh, tt, ll);
	}
}

function editor_mouseup() {
	mouseBtn=false; resize_toolbox=false;
	jQuery('body', io).removeClass('doing_drag');
	jQuery('body').removeClass('doing_drag');
	//jQuery('.droppable_empty', io).show();

	if (droppable_over && move_item) {
		not_saved();
		//Copiem el contingut

		move_item.clone().appendTo(jQuery(droppable_over).children());
		
		document.getElementById('knews_editor').contentWindow.listen_module(droppable_over);

		//Traiem el class del TR
		jQuery(droppable_over).removeClass('droppable_empty_hover').removeClass('droppable_empty').addClass('droppable');
		
		//Traiem el class del TR
		jQuery(move_item).closest('.droppable').removeClass('droppable').addClass('droppable_empty');

		//El fem editable si s'escau
		// no cal: if (jQuery('span.chooser a', droppable_over).length == 0) jQuery('span.content_editable', droppable_over).attr('contenteditable','true');

		//esborrem el contingut antic
		move_item.remove();
		
		//renovem els droppables
		redraw_droppables();
		
	} else if (droppable_over && copy_item) {
		not_saved();
		//Copiem el contingut
		copy_item.children().clone().appendTo(jQuery(droppable_over).children());
		
		//Traiem el class del TR
		jQuery(droppable_over).removeClass('droppable_empty_hover').removeClass('droppable_empty').addClass('droppable');

		look_images(droppable_over);
		
		document.getElementById('knews_editor').contentWindow.listen_module(droppable_over);

		colors_globals = look_colours('local', droppable_over);
		fonts_locals = look_fonts('local', droppable_over);

		for (x=1; x <= colors_globals.length; x++) {
			jQuery('span.handler', droppable_over).append('<a href="#" class="sel_color" title="' + colors_globals[x-1][1] + '" onclick="parent.sel_col(\'local\',' + x + ', this); return false;" style="background-color:' + colors_globals[x-1][0] + '"></a>');
		}
		for (x=1; x <= fonts_locals.length; x++) {
			jQuery('span.handler', droppable_over).append('<a href="#" class="sel_font" title="' + fonts_locals[x-1][4] + '" onclick="parent.sel_fon(\'local\',' + x + ', this); return false;"></a>');
		}

		if (!look_posts(droppable_over)) {
			//El fem editable si s'escau
			jQuery('span.content_editable', droppable_over).attr('contenteditable','true');
		}

		//renovem els droppables
		redraw_droppables();
	
	} else {
		redraw_droppables();
	}

	//Esborrem el preview
	jQuery('div.drag_preview').html('');
	
	//Restaurem els objectes de control
	move_preview=false;
	droppable_over=null;
	move_item=null;
	copy_item=null;
	
	parent.resizing_image='';
	return false;
}
function sel_col(ambit, ncolour, obj) {
	col_picker_ambit = ambit;
	col_picker_number = ncolour;
	col_picker_obj = obj;
	if (ambit=='local') col_picker_referer = jQuery(obj).closest('.draggable');
	tb_show('Color Picker', url_plugin + '/direct/color_picker.php?hex=' + rgb2hex(jQuery(col_picker_obj).css('backgroundColor')) + '&amp;TB_iframe=true&amp;width=545&amp;height=330');
}

function sel_fon(ambit, nfont, obj) {
	font_picker_ambit=ambit;
	font_picker_number=nfont;
	if (ambit=='local') {
		font_picker_referer = jQuery(obj).closest('.draggable');
		found_obj=jQuery('.local_font_' + (nfont), font_picker_referer);
		//alert('.local_font_' + (nfont-1));
		//alert(jQuery('.local_font_' + (nfont), font_picker_referer).html());
		ff = jQuery(found_obj).attr('face');
		fs = jQuery(found_obj).attr('size');

		ss = 0; if (look_for_css_property(jQuery(found_obj).attr('style'),'font-size')) 
			ss = parseInt(jQuery(found_obj).css('fontSize'), 10);

		lh = 0; if (look_for_css_property(jQuery(found_obj).attr('style'),'line-height')) 
			lh = parseInt(jQuery(found_obj).css('lineHeight'), 10);
		
		tb_show('Font Picker', url_plugin + '/wysiwyg/fontpicker/index.php?ff=' + escape(ff) + '&amp;fs=' + fs + '&amp;ss=' + ss + '&amp;lh=' + lh + '&amp;TB_iframe=true&amp;width=545&amp;height=420');
	} else {
		//alert(fonts_globals[nfont-1][3]);
		tb_show('Font Picker', url_plugin + '/wysiwyg/fontpicker/index.php?ff=' + escape(fonts_globals[nfont-1][0]) + '&amp;fs=' + fonts_globals[nfont-1][1] + '&amp;ss=' + fonts_globals[nfont-1][2] + '&amp;lh=' + fonts_globals[nfont-1][3] + '&amp;TB_iframe=true&amp;width=545&amp;height=420');
	}
}

function tb_dialog(title, message, button1, button2, where) {
	
	tb_show(title, "#TB_inline?height=100&width=400&inlineId=modalDiv");
	
	content = '<p>' + message + '</p>';
	
	if (button1 != '' || button2 != '') content = content + '<p style="text-align:center; margin-bottom:0; padding-bottom:0;">';
	if (button1 != '') content = content + '<input type="button" value="' + button1 + '" onclick="tb_dialog_click(true, \'' + where + '\')">';
	if (button2 != '') content = content + '&nbsp;<input type="button" value="' + button2 + '" onclick="tb_dialog_click(false, \'' + where + '\')">';
	if (button1 != '' || button2 != '') content = content + '</p>';

	jQuery('#TB_ajaxContent').html(content);
}

function tb_dialog_click (what, where) {

	tb_remove();
	
	if (where == 'saveok' && what) {
		document.location=submit_news;
	}
	if (where == 'saveok' && !what) {
		window.location.reload();
	}
	
	if (where == 'deleteModule' && what) {
		not_saved();
		jQuery(referer_delete).closest('.droppable').addClass('droppable_empty').removeClass('droppable');
		jQuery(referer_delete).closest('.draggable').remove();
		redraw_droppables();
	}
}

function rgb2hex(rgb) {
	if(rgb.indexOf('rgb') == -1) {
		if (rgb.indexOf('#')==0) rgb = rgb.substr(1);
		return rgb;
	}
	
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ("0" + parseInt(x).toString(16)).slice(-2);
    }
    return hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

function redraw_droppables() {
	jQuery('.droppable_empty_hidden', io).removeClass('droppable_empty_hidden');
	
	jQuery('.droppable', io).each(function () {
		if (jQuery(this).prev().attr('class') != 'droppable_empty') {
			jQuery(this).before(droppable_code);
		}
		if (jQuery(this).next().attr('class') != 'droppable_empty') {
			jQuery(this).after(droppable_code);
		}		
	});
	jQuery('.droppable_empty', io).each(function () {
		if (jQuery(this).prev().attr('class') == 'droppable_empty') {
			jQuery(this).remove();
		}
	});
	resize_frame();
}

function look_images(obj) {
	jQuery('img.editable', obj).each(function(index) {
    	jQuery(this).before('<span class="img_handler"><a href="#" class="change_image" title="' + edit_image + '"></a></span>');
		//<a href="#" class="properties_image" title="' + properties_image + '"></a>
	});
}

function look_colours(ambit, obj) {
	found=true;
	colours=0;
	colours_array=Array();
	no_crash=-1;
	
	while (found) {
		if (no_crash == colours) {
			alert("Error in template: check colours definitions");
			break;
		} else {
			no_crash = colours;
		}
		iterate_object = obj;
		found=false;
		
		if (jQuery('.' + ambit + '_colour_bg_' + (colours+1), obj).length !=0) found=true;

		if (!found && ambit=='global') {
			iterate_object = jQuery('div.wysiwyg_toolbar');
			if (jQuery('.' + ambit + '_colour_bg_' + (colours+1), iterate_object).length !=0) found=true;
		}

		if (found) {
			found_obj=jQuery('.' + ambit + '_colour_bg_' + (colours+1), iterate_object)[0];
			
			//alert(jQuery(jQuery('.' + ambit + '_colour_bg_' + (colours+1), obj)[0]).parent().html());

			if (jQuery(found_obj).attr('bgcolor') !== undefined) {
				colours_array[colours]=Array();
				colours_array[colours][0] = jQuery(found_obj).attr('bgcolor');
				colours_array[colours][1] = look_for_caption(jQuery(found_obj).attr('class'), 'color');
				colours++;

			} else if (look_for_css_property(jQuery(found_obj).attr('style'), 'background-color')) {
				colours_array[colours]=Array();
				colours_array[colours][0] = jQuery(found_obj).css('background-color');
				colours_array[colours][1] = look_for_caption(jQuery(found_obj).attr('class'), 'color');
				colours++;
			}
		} else {
			iterate_object = obj;
			if (jQuery('.' + ambit + '_colour_' + (colours+1), iterate_object).length !=0) {
				found=true;
			}
			if (!found && ambit=='global') {
				iterate_object = jQuery('div.wysiwyg_toolbar');

				if (jQuery('.' + ambit + '_colour_' + (colours+1), iterate_object).length !=0) {
					found=true;
				}
			}
	
			if (found) {
				//alert(jQuery('.' + ambit + '_colour_' + (colours+1), iterate_object).html());
				found_obj=jQuery('.' + ambit + '_colour_' + (colours+1), iterate_object)[0];
	
				if (jQuery(found_obj).attr('color') !== undefined) {
					colours_array[colours]=Array();
					colours_array[colours][0] = jQuery(found_obj).attr('color');
					colours_array[colours][1] = look_for_caption(jQuery(found_obj).attr('class'), 'color');
					colours++;
				} else if (look_for_css_property(jQuery(found_obj).attr('style'),'color')) {
					colours_array[colours]=Array();
					colours_array[colours][0] = jQuery(found_obj).css('color');
					colours_array[colours][1] = look_for_caption(jQuery(found_obj).attr('class'), 'color');
					colours++;
				}
			}
		}
	}
	
	return colours_array;
}

function look_fonts(ambit, obj) {
	found=true;
	fonts=0;
	fonts_array=Array();
	no_crash=-1;
	
	while (found) {
		if (no_crash == fonts) {
			alert("Error in template: check font definitions");
			break;
		} else {
			no_crash = fonts;
		}
		iterate_object = obj;
		found=false;
		if (jQuery('.' + ambit + '_font_' + (fonts+1), obj).length !=0) found=true;

		if (!found && ambit=='global') {
			iterate_object = jQuery('div.wysiwyg_toolbar');
			if (jQuery('.' + ambit + '_font_' + (fonts+1), iterate_object).length !=0) {
				found=true;
			}
		}

		if (found) {

			found=true;
			found_obj=jQuery('.' + ambit + '_font_' + (fonts+1), iterate_object)[0];
			fonts_array[fonts]=Array();
			fonts_array[fonts][0] = jQuery(found_obj).attr('face');
			fonts_array[fonts][1] = parseInt(jQuery(found_obj).attr('size'), 10);
			
			fonts_array[fonts][2] = 0; 
			if (look_for_css_property(jQuery(found_obj).attr('style'),'font-size')) 
				fonts_array[fonts][2] = parseInt(jQuery(found_obj).css('fontSize'), 10);

			fonts_array[fonts][3] = 0; 
			if (look_for_css_property(jQuery(found_obj).attr('style'),'line-height')) 
				fonts_array[fonts][3] = parseInt(jQuery(found_obj).css('lineHeight'), 10);
				
			fonts_array[fonts][4] = look_for_caption(jQuery(found_obj).attr('class'), 'font');
			fonts++;
		}
	}
	return fonts_array ;
}

function look_zone(obj) {
	for (var x=0; x<11; x++) {
		if (jQuery(obj).hasClass('zone_' + x)) return x;
	}
	return 0;
}

function look_posts(obj) {
	
	codi=jQuery(obj).html();
	for (var x=1; x<11; x++) {
		found=false;
		//alert(codi.indexOf("%the_permalink_" + x + "%") != -1);
		if (codi.indexOf("%the_permalink_" + x + "%") != -1) found=true;
		if (codi.indexOf("%the_title_" + x + "%") != -1) found=true;
		if (codi.indexOf("%the_excerpt_" + x + "%") != -1) found=true;
		
		//alert(found);
		if (!found) break;
	}
	if (x>1) {
		for (var xx=1; xx<x; xx++) {
			jQuery('span.chooser', obj).append('<a href="#" class="insert_post insert_post_' + xx + '" onclick="parent.insert_post(this, ' + xx + '); return false;" title="' + post_handler + ' (' + xx + ')"></a>' + 
				'<a href="#" class="free_text free_text_' + xx + '" onclick="parent.free_text(this, ' + xx + '); return false;" title="' + free_handler + ' (' + xx + ')"></a>');			
		}		
		return true;

	} else {
		return false;
	}
}

function absoluteReplace(string, strfind, strreplace) {
	return string.split(strfind).join(strreplace);
}

function CallBackPost(n, lang) {
	not_saved();
	tb_remove();

	if (jQuery.browser.msie  && parseInt(jQuery.browser.version, 10) === 8 && to_shitty_ie8=='') {
		//alert("callback_img('" + html + "')");
		to_shitty_ie8 = setTimeout("CallBackPost('" + n + "','" + lang + "')", 2000);
		//alert("ie8b");
	}

	jQuery.ajax({
		data: "ajaxid=" + n + "&lang=" + lang,
		type: "GET",
		dataType: "json",
		url: url_plugin + "/direct/select_post.php",
		cache: false,
		success: function(data) {
			
			if (to_shitty_ie8 != '' && to_shitty_ie8 != 'x') {
				clearTimeout(to_shitty_ie8);
			}
			to_shitty_ie8='x';

			module = jQuery(referer_module).closest('.droppable');
			codi = jQuery(module).html();
		
			codi = absoluteReplace(codi, '%the_permalink_' + post_number_module + '%', data.permalink);
			codi = absoluteReplace(codi, '%the_title_' + post_number_module + '%', data.title);
			codi = absoluteReplace(codi, '%the_excerpt_' + post_number_module + '%', data.excerpt);
			codi = absoluteReplace(codi, '%the_content_' + post_number_module + '%', data.content);
			
			jQuery(module).html(codi);
			jQuery('span.chooser a.free_text_' + post_number_module, module).remove();
			jQuery('span.chooser a.insert_post_' + post_number_module, module).remove();
		
			if (jQuery('span.chooser a', module).length == 0) {
		
				jQuery('span.chooser', module).remove();
				jQuery('span.content_editable', module).attr('contenteditable','true');
			}
			document.getElementById('knews_editor').contentWindow.listen_module(module);
		},
		complete: function(msg){                        
		},

		error: function(request, status, error) {           
			if (to_shitty_ie8 != '' && to_shitty_ie8 != 'x') {
				clearTimeout(to_shitty_ie8);
			}
			to_shitty_ie8='x';
		   //alert(request.responseText);
		   //alert("Error, returned: " + request);
		   //alert("Error, returned: " + status);
		   alert("Error in CallBackPost(), returned: " + error);
		}

	});
}



window.send_to_editor = function(html) {
	callback_img(html);
}

function callback_img(html) {
	not_saved();
	tb_remove();
	
	img_x = jQuery(referer_image).attr('width');
	img_y = jQuery(referer_image).attr('height');
	
	img_url=html.split('src="');
	img_url=img_url[1].split('"');
	img_url=img_url[0];

	if (jQuery.browser.msie  && parseInt(jQuery.browser.version, 10) === 8 && to_shitty_ie8=='') {
		//alert("callback_img('" + html + "')");
		to_shitty_ie8 = setTimeout("callback_img('" + html + "')", 2000);
		//alert("ie8b");
	}

	jQuery.ajax({
		data: "urlimg=" + img_url + "&width=" + img_x + "&height=" + img_y,
		type: "GET",
		dataType: "html",
		url: url_plugin + "/direct/resize_img.php",
		cache: false,
		success: function(data) {
			if (to_shitty_ie8 != '' && to_shitty_ie8 != 'x') {
				clearTimeout(to_shitty_ie8);
			}
			to_shitty_ie8='x';
			//alert('success');
			if (data=='error') {
				tb_dialog('Knews', error_resize, button_continue_editing, '', '');
			} else {
				jQuery(referer_image).attr('src', data);
			}
		},
		beforeSend: function(request, settings) {
			//alert('Beginning ' + settings.dataType + ' request: ' + settings.url);
		},
		complete: function(request, status) {
			//alert('Request complete: ' + status);
		},
		error: function(request, status, error) {
			if (to_shitty_ie8 != '' && to_shitty_ie8 != 'x') {
				clearTimeout(to_shitty_ie8);
			}
			to_shitty_ie8='x';
			//alert(request.responseText);
			//alert("Error, returned: " + request);
			//alert("Error, returned: " + status);
			tb_dialog('Knews', error_resize + ": (" + error + ")", button_continue_editing, '', '');
		}
		
	});
}

function look_for_css_property(chain, property) {

	if (chain===undefined || chain==null) return false;
	properties=chain.split(';');

	for (var x=0; x<properties.length; x++) {
		txt=properties[x];
		if (txt.indexOf(':') != -1) txt=txt.substring(0, txt.indexOf(':'));
		txt=txt.replace(/^\s+|\s+$/g,""); //JS Trim, http://www.somacon.com/p355.php
		if (txt.toLowerCase() == property.toLowerCase()) {
			return true;
		}
	}
	return false;
}

function look_for_caption(chain, what) {
	if (chain===undefined || chain==null) return '';
	classes=chain.split(' ');
	for (var x=0; x<classes.length; x++) {
		txt=classes[x];
		if (what=='font') {
			if (txt.substring(0,10)=='_fcaption_') {
				txt=txt.substring(10);
				return txt.replace(/_/g, ' ');
			}
		} else {
			if (txt.substring(0,9)=='_caption_') {
				txt=txt.substring(9);
				return txt.replace(/_/g, ' ');
			}
		}
	}
	return '';
}

function CallBackColour(hex) {
	not_saved();
	//alert(hex);
	parent.tb_remove();

	if (col_picker_ambit=='global') {
		col_picker_referer = io;
	} else {
		col_picker_referer = jQuery(col_picker_referer).parent();
	}

	jQuery('.' + col_picker_ambit + '_colour_bg_' + col_picker_number, col_picker_referer).each(function () {
		if (jQuery(this).attr('bgcolor') !== undefined) jQuery(this).attr('bgcolor', hex);

		if (look_for_css_property(jQuery(this).attr('style'), 'background-color')) {
			jQuery(this).css('backgroundColor', hex);
		} else {
			if (look_for_css_property(jQuery(this).attr('style'), 'background')) jQuery(this).css('backgroundColor', hex);
		}
	});
	jQuery('.' + col_picker_ambit + '_colour_' + col_picker_number, col_picker_referer).each(function () {
		if (jQuery(this).attr('color') !== undefined) jQuery(this).attr('color', hex);
		if (look_for_css_property(jQuery(this).attr('style'), 'color')) jQuery(this).css('color', hex);
	});
	
	jQuery(col_picker_obj).css('backgroundColor',hex);

}

function CallBackFont(ff, fs, ss, lh) {
	not_saved();
	parent.tb_remove();

	if (font_picker_ambit=='global') {
		font_picker_referer = io;
	} else {
		font_picker_referer = jQuery(font_picker_referer).parent();
	}

	jQuery('.' + font_picker_ambit + '_font_' + font_picker_number, font_picker_referer).each(function () {

		if (jQuery(this).attr('face') !== undefined) jQuery(this).attr('face', ff);
		if (jQuery(this).attr('size') !== undefined) jQuery(this).attr('size', fs);

		if (look_for_css_property(jQuery(this).attr('style'),'font-size')) jQuery(this).css('fontSize', ss + 'px');
		if (look_for_css_property(jQuery(this).attr('style'),'line-height')) jQuery(this).css('lineHeight', lh + 'px');
	});
}

function insert_post(obj, n) {
	referer_module=obj;
	post_number_module=n;
	tb_show('', url_plugin + '/direct/select_post.php?TB_iframe=true&amp;width=640&amp;height=' + (parseInt(jQuery(parent.window).height(), 10)-100));
}

function free_text(obj, n) {
	module = jQuery(obj).closest('.droppable');
	//jQuery(obj).parent().remove();

	jQuery('span.chooser a.free_text_' + n, module).remove();
	jQuery('span.chooser a.insert_post_' + n, module).remove();
	
	code=jQuery(module).html();
	code = absoluteReplace(code, '%the_permalink_' + n + '%', '#');
	code = absoluteReplace(code, '%the_title_' + n + '%', 'The title');
	code = absoluteReplace(code, '%the_excerpt_' + n + '%', 'The content');
	code = absoluteReplace(code, '%the_content_' + n + '%', 'The content');
	jQuery(module).html(code);

	if (jQuery('span.chooser a', module).length == 0) {
		jQuery('span.chooser', module).remove();
		jQuery('span.content_editable', module).attr('contenteditable','true');
	}
	document.getElementById('knews_editor').contentWindow.listen_module(module);
}


save_news_sem=false;
function save_news () {

	if (!save_news_sem) {
		save_news_sem=true;

		document.getElementById('knews_editor').contentWindow.normalize_html();

		jQuery('span.img_handler', io).remove();
		jQuery('span.handler a, span.handler span, span.chooser a', io).remove();
		jQuery('span.handler', io).removeAttr('style');

		jQuery('span.content_editable', io)
			.removeAttr('contenteditable');

		savecode=jQuery('div.wysiwyg_editor', io).html();

		jQuery.ajax({
			data: { code: savecode,
					title: jQuery('input#title').val(),
					idnews: id_news },
	
			type: "POST",
			cache: false,
			dataType: "html",
			url: url_plugin + "/direct/save_news.php",
			success: function(data) { 

				if (data=='error') {
					tb_dialog('Knews', error_save, button_continue_editing, '', '');
					//alert(error_save);
					save_news_sem=false;
				} else {

					saved();
					//alert(ok_save);
					tb_dialog('Knews', ok_save, button_submit_newsletter, button_continue_editing, 'saveok');
				}
			},

			complete: function(msg){
				save_news_sem=false;
			},

			error: function(request, status, error) {           
				//alert(request.responseText);
				//alert("Error, returned: " + request);
				//alert("Error, returned: " + status);
				tb_dialog('Knews', error_save + ": (" + error + ")", button_continue_editing, '', '');
			}

		});
	}
}

function selecttag(n) {
	document.getElementById('knews_editor').contentWindow.selecttag_n(n);
}
function b_simple(what) {
	not_saved();
	document.getElementById('knews_editor').contentWindow.b_simple(what);
}
function b_link() {
	not_saved();
	document.getElementById('knews_editor').contentWindow.b_link();
}
function b_del_link() {
	not_saved();
	document.getElementById('knews_editor').contentWindow.b_del_link();
}
function b_justify(j) {
	not_saved();
	document.getElementById('knews_editor').contentWindow.b_justify(j);
}
function b_color() {
	saved_range=document.getElementById('knews_editor').contentWindow.saveSelection();
	not_saved();
	if (document.getElementById('knews_editor').contentWindow.inside_editor) {
		tb_show('Color Picker', url_plugin + '/direct/color_picker.php?hex=' + rgb2hex(jQuery('#botonera a.color').css('backgroundColor')) + '&amp;editor=1&amp;TB_iframe=true&amp;width=545&amp;height=330');
	}
}
function CallBackColourEditor(hex) {
	document.getElementById('knews_editor').contentWindow.restoreSelection(saved_range);
	not_saved();
	parent.tb_remove();
	document.getElementById('knews_editor').contentWindow.b_color(hex);	
}
