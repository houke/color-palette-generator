jQuery(document).on('ready', function() {
	var cpg_colorThief;

	jQuery(document).on('click', '.cpg-button-bulk', function(e){
		e.preventDefault();
		var elem = jQuery(this);
		if(!elem.hasClass('disabled')){
			elem.text(cpg.generating + '...').attr('disabled', 'disabled').addClass('disabled');
		}
		var src = elem.data('src');
		var params = cpg_parseParams( elem.attr('href').split('?')[1] );
		cpg_CreateImg(src, params.post_id, params._wpnonce, params.colors);
	});

	jQuery(document).on('click', '[data-add-color]', function(e){
		e.preventDefault();
		jQuery(this).parents('td').find('.cpg-color-table__colors').append('<div class="cpg-color-table__div"><input type="text" value="" class="cpg-color-picker"/><button class="cpg-delete-color">&times;</button></div>');
		//jQuery('.cpg-color-table__div > .cpg-color-picker').wpColorPicker();
	});

	jQuery(document).on('click', '.cpg-delete-color', function(e){
		e.preventDefault();
		jQuery(this).parents('.cpg-color-table__div').remove();
	});

	jQuery(document).on('click', '.cpg-color-table__add-row', function(e){
		e.preventDefault();
		jQuery('.cpg-color-table tbody').append('<tr>\
				<td>\
					<input type="text"class="cpg-color-picker"/><br/>\
					<a href="#">Edit</a>\
					<a href="#">Trash</a>\
				</td>\
				<td>\
					<input type="text" value="" placeholder="Color name" />\
				</td>\
				<td>\
					<div class="cpg-color-table__colors">\
					</div>\
					<div class="cpg-color-table__div"><button class="button tiny" data-add-color>Add color tint</button></div>\
				</td>\
			</tr>');
	})

	//jQuery('.cpg-color-picker').wpColorPicker();

	function cpg_CreateImg(src, id, nonce, colors){
		var img = new Image;
		img.src = src;
		img.onload = function(){
		    cpg_colorThief = new ColorThief();
		    var color = cpg_AddColorsForImage(img, id, nonce, colors);
		};
	}

	function cpg_AddColorsForImage(image, id, nonce, colors) {
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
			jQuery.ajax({
				url: ajaxurl,
	         	type: 'post',
	         	dataType: 'JSON',
	         	timeout: 30000,
	         	data: {
					action: 'cpg_bulk_add_palette',
	         		dominant: colorThiefOutput.dominant,
	         		palette: colorThiefOutput.palette,
					id: id,
					nonce: nonce
	         	},
	         	success: function(response) {
	         		if(response.more){
	         			cpg_CreateImg(response.src, response.id, response.nonce);
	         		}else{
	         			jQuery('.cpg__inside--btn').html(cpg.done);
	         			jQuery('.cpg-hndle small').remove();
	         		}
         			jQuery('[data-with]').html( parseInt(jQuery('[data-with]').html()) + 1);
         			jQuery('[data-without]').html( parseInt(jQuery('[data-total]').html() - jQuery('[data-with]').html()));
	         	},
	         	error: function (jqXHR, exception) {
			        var msg = cpg_showErrors(jqXHR, exception);
			        jQuery('.cpg__inside--btn').html(msg);
			    },
	        });
		});
	}
});
