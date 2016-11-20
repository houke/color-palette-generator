jQuery(document).on('ready', function() {
	var cpg_colorThief;

	jQuery(document).on('click', '.cpg-button-palette', function(e){
		if(!jQuery(this).attr('disabled') && !jQuery(this).hasClass('disabled')){
			e.preventDefault();
			var elem = jQuery(this);
			var img = new Image;
			var params = cpg_parseParams( elem.attr('href').split('?')[1] );
			elem.text(cpg.generating + '...').attr('disabled', 'disabled').addClass('disabled');
			img.src = elem.data('src');
			var id = params.post_id;
			var nonce = params._wpnonce;
			var colors = params.colors;
			img.onload = function(){
			    cpg_colorThief = new ColorThief();
			    var color = cpg_AddColorsForImage(elem, img, id, nonce, colors);
			};
		}
	});

	jQuery(document).on('click', '.cpg-button-palette-trash', function(e){
		e.preventDefault();
		var elem = jQuery(this);
		var params = cpg_parseParams( elem.attr('href').split('?')[1] );
		var r = confirm(cpg.confirm_trash);
	    if (r == true) {
	        cpg_RemoveColorsForImage(elem, params);
	    }
	});

	function cpg_AddColorsForImage(elem, image, id, nonce, colors) {
		colorThiefOutput = {};

		var color = new Promise(function(resolve, reject) {
		  resolve(cpg_colorThief.getColor(image));
		});

		var palette = color.then(function(value){
			colorThiefOutput.dominant = value;
			return new Promise(function(resolve, reject) {
				resolve(cpg_colorThief.getPalette(image, colors));
			});
		});

		palette.then(function(value){
			colorThiefOutput.palette = value;
		}).then(function(){
			var cpg_td = elem.parent();
			jQuery.ajax({
				url: ajaxurl,
	         	type: 'post',
	         	dataType: 'JSON',
	         	data: {
	         		action: 'cpg_add_palette',
	         		dominant: colorThiefOutput.dominant,
	         		palette: colorThiefOutput.palette,
	         		nonce: nonce,
	         		id: id
	         	},
	         	success: function(response) {
	         		cpg_td.html(response);
	         	},
	         	error: function (jqXHR, exception) {
			        var msg = cpg_showErrors(jqXHR, exception);
			        cpg_td.html('<span style="color:#f00;">'+msg+'</span>');
			    },
	        });
		});
	}

	function cpg_RemoveColorsForImage(elem, params){
		var cpg_td = elem.parents('.cpg_dominant_color_column');
		cpg_td.html(cpg.trashing + '...');
		jQuery.ajax({
			url: ajaxurl,
         	type: 'post',
         	dataType: 'JSON',
         	data: {
         		action: params.action,
         		id: params.post_id,
         		nonce: params._wpnonce
         	},
         	success: function(response) {
         		cpg_td.html(response);
         	},
         	error: function (jqXHR, exception) {
		        var msg = cpg_showErrors(jqXHR, exception);
		        cpg_td.html('<span style="color:#f00;">'+msg+'</span>');
		    },
        });
	}

});
