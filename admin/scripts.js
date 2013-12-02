
// Admin config
function knews_conf(w) {
	if (w=='gmail') {
		user='youremail@gmail.com';
		host='smtp.gmail.com';
		port='587';
		secure='ssl';
		comnn='0';
	} else if (w=='1and1') {
		user='';
		host='';
		port='';
		secure='';
		comnn='1';
	} else if (w=='godaddy') {
		user='your@email.com';
		host='relay-hosting.secureserver.net';
		port='25';
		secure='';
		comnn='0';
	} else if (w=='yahoo') {
		user='youryahooname';
		host='smtp.mail.yahoo.com';
		port='465';
		secure='ssl';
		comnn='0';
	}
	jQuery('#smtp_host_knews').val(host);
	jQuery('#smtp_port_knews').val(port);
	jQuery('#smtp_user_knews').val(user);
	jQuery('#smtp_secure_knews').val(secure);
	jQuery('#is_sendmail_knews').val(comnn);			
}

function view_lang(n_custom, n_lang) {
	jQuery('div.pestanyes_'+n_custom+' a').removeClass('on');
	jQuery('a.link_'+n_custom+'_'+n_lang).addClass('on');

	target='div.pregunta_'+n_custom+' textarea.on';
	save_height=jQuery(target).innerHeight() + parseInt(jQuery(target).css('marginTop'), 10) + parseInt(jQuery(target).css('marginBottom'), 10);
	
	save_width=jQuery(target).innerWidth() + parseInt(jQuery(target).css('marginLeft'), 10) + parseInt(jQuery(target).css('marginRight'), 10);
		
	jQuery('div.pregunta_'+n_custom+' textarea').css('display','none').removeClass('on');
	jQuery('textarea.custom_lang_'+n_custom+'_'+n_lang).css({display:'block', height:save_height, width:save_width}).addClass('on');
}	

// Cooltabs
jQuery(document).ready(function() {
	jQuery('div.knews_cooltabs a').click(function() {
		jQuery('div.knews_cooltabs a').removeClass('active');
		jQuery(this).addClass('active');
		n = jQuery('div.knews_cooltabs a').index(this);
		jQuery('div.tabbed_content').hide();
		jQuery('div.tabbed_content').eq(n).show();
		jQuery('#subtab').val(n+1);
		return false;
	});
});

// Custom checkboxes
jQuery.fn.moveBackgroundX = function( pixelsX, duration ) {
	pixelsY = jQuery(this).css('backgroundPosition');
	pixelsY = parseInt( pixelsY.split(' ')[1], 10);
	return this.animate( { pixelsX: pixelsX }, { step: function(now,fx) {
		jQuery(this).css({ backgroundPosition: now + 'px ' + pixelsY + 'px' });
	}, duration: duration, complete: function() {} }, 'swing');
};

jQuery(document).ready(function() {
	jQuery('input.knews_on_off, input.knews_open_close').each(function () {
		current_class='knews_on_off'; offsetX=50;
		if (jQuery(this).hasClass('knews_open_close')) { current_class='knews_open_close'; offsetX=30; }
		extraclass=''; if (jQuery(this).hasClass('align_left')) extraclass=current_class + '_left';
		jQuery(this).before('<span class="' + current_class + ' ' + extraclass + '">&nbsp;</span>');
		jQuery(this).addClass('knews_processed');
		next_label = jQuery(this).next().addClass('knews_processed ' + extraclass);
		if (!jQuery(this).is(':checked')) jQuery(this).prev().moveBackgroundX(-1 * offsetX, 0);
		jQuery(this).prev().click(function() {
			offsetX=50; if (jQuery(this).hasClass('knews_open_close')) offsetX=30;
			state = !jQuery(this).next().prop('checked');
			jQuery(this).next().prop('checked', state);
			if (state) {
				jQuery(this).stop().moveBackgroundX(0, 500);
			} else {
				jQuery(this).stop().moveBackgroundX(-1 * offsetX, 500);
			}
		});
	});
});