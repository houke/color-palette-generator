jQuery(document).on('ready', function() {
	var cpg_colorThief;

	jQuery(document).on('click', '.cpg-button-bulk', function(e) {
		e.preventDefault();
		var elem = jQuery(this);
		if (!elem.hasClass('disabled')) {
			elem
				.text(cpg.generating + '...')
				.attr('disabled', 'disabled')
				.addClass('disabled')
				.parent()
				.append('<br/><br/><small>' + cpg.keep_open + '</small>');
		}
		var src = elem.data('src');
		var params = cpg_parseParams(elem.attr('href').split('?')[1]);
		cpg_CreateImg(
			src,
			params.post_id,
			params._wpnonce,
			params.colors,
			params.regenerate
		);
	});

	jQuery(document).on('click', '.cpg-button-bulk--reset', function(e) {
		e.preventDefault();
		var elem = jQuery(this);
		var params = cpg_parseParams(elem.attr('href').split('?')[1]);
		jQuery('[data-with]').html('&bull;&bull;&bull;');
		jQuery('.cpg-postbox--skipped').remove();
		jQuery('.cpg__inside--generate').html('<p>' + cpg.deleting + '...</p>');
		jQuery.ajax({
			url: ajaxurl,
			type: 'post',
			dataType: 'JSON',
			timeout: 0,
			data: {
				action: 'cpg_bulk_add_palette',
				nonce: params._wpnonce,
				regenerate: params.regenerate
			},
			success: function(response) {
				jQuery('[data-with]').html(0);
				jQuery('.cpg__inside--generate').html(
					'<p>' + cpg.removed + '</p>'
				);
			},
			error: function(jqXHR, exception, errorThrown) {
				var msg = cpg_showErrors(jqXHR, exception);
				jQuery('.cpg__inside--generate').html(msg);
			}
		});
	});

	jQuery(document).on('click', '.cpg-button-bulk--regenerate', function(e) {
		e.preventDefault();
		var elem = jQuery(this);
		var params = cpg_parseParams(elem.attr('href').split('?')[1]);
		jQuery('[data-with]').html('&bull;&bull;&bull;');
		jQuery('.cpg-postbox--skipped').remove();
		jQuery('.cpg__inside--generate').html(
			'<p>' +
				cpg.deleting +
				'... <br/><small>' +
				cpg.keep_open +
				'</small></p>'
		);
		jQuery.ajax({
			url: ajaxurl,
			type: 'post',
			dataType: 'JSON',
			timeout: 0,
			data: {
				action: 'cpg_bulk_add_palette',
				nonce: params._wpnonce,
				regenerate: params.regenerate
			},
			success: function(response) {
				jQuery('.cpg__inside--generate').html(
					'<p>' +
						cpg.regenerating +
						'... <br/><small>' +
						cpg.keep_open +
						'</small></p>'
				);
				jQuery('[data-with]').html(0);
				if (response.more) {
					cpg_CreateImg(response.src, response.id, response.nonce);
				} else {
					jQuery('.cpg__inside--generate').html(cpg.done);
					jQuery('.cpg-hndle small').remove();
				}
			},
			error: function(jqXHR, exception, errorThrown) {
				var msg = cpg_showErrors(jqXHR, exception);
				jQuery('.cpg__inside--generate').html(msg);
			}
		});
	});

	jQuery(document).on('click', '[data-add-color]', function(e) {
		e.preventDefault();
		var name_in_array = jQuery(this)
			.parents('tr')
			.find('td:nth-child(2) input')
			.val();
		var name_in_array = name_in_array.replace(' ', '-').toLowerCase();
		jQuery(this)
			.parents('td')
			.find('.cpg-color-table__colors')
			.append(
				'<div class="cpg-color-table__div cpg-color-table__div--added">\
				<input type="text" value="" class="cpg-color-picker" name="cpg_options[color_table][' +
					name_in_array +
					'][tints][]" />\
				<button class="cpg-delete-color">&times;</button>\
			</div>'
			);
	});

	jQuery(document).on('click', '.cpg-delete-color', function(e) {
		e.preventDefault();
		jQuery(this)
			.parents('.cpg-color-table__div')
			.remove();
	});

	jQuery(document).on('click', '.cpg-color-table .trash', function(e) {
		e.preventDefault();
		jQuery(this)
			.parents('tr')
			.remove();
	});

	jQuery(document).on('keyup', '.cpg-color-name', function() {
		var input = jQuery(this).val();
		var inputs = jQuery(this)
			.parents('tr')
			.find('input');
		inputs.each(function() {
			var name = jQuery(this).attr('name');
			name = name.replace(
				/_table\]\[.*?\]\s?/g,
				'_table][' + input + ']'
			);
			jQuery(this).attr('name', name.toLowerCase());
		});
	});

	jQuery(document).on('click', function(e) {
		if (
			jQuery('.cpg-color-picker-iris').length > 0 &&
			!jQuery(e.target)
				.parent()
				.hasClass('cpg-color-picker-iris')
		) {
			jQuery('.cpg-color-picker-iris .cpg-color-picker').iris('hide');
			jQuery('.cpg-color-picker-iris').removeClass(
				'cpg-color-picker-iris'
			);
		}
	});

	jQuery(document).on('focus', '.cpg-color-picker', function(e) {
		e.preventDefault();
		jQuery('.cpg-color-picker-iris .cpg-color-picker').iris('hide');
		jQuery(this)
			.parent('.cpg-color__main-color, .cpg-color-table__div')
			.addClass('cpg-color-picker-iris');
		if (jQuery(this).hasClass('iris-active')) {
			jQuery(this).iris('show');
		} else {
			var color =
				jQuery(this).val() == ''
					? jQuery(this)
							.parents('tr')
							.find('.cpg-color__main-color input')
							.val()
					: jQuery(this).val();
			jQuery(this)
				.addClass('iris-active')
				.iris({
					color: color,
					mode: 'rgb',
					width: 158,
					change: function(event, ui) {
						jQuery(event.target).css(
							'background-color',
							ui.color.toString()
						);
					}
				});
		}
	});

	jQuery(document).on('click', '.cpg-color-table__add-row', function(e) {
		e.preventDefault();
		var new_color = prompt(cpg.enter_value, cpg.enter_value_placeholder);
		var new_color_lower = new_color.replace(' ', '-').toLowerCase();
		if (new_color != null) {
			jQuery('.cpg-color-table tbody').append(
				'<tr>\
				<td class="cpg-color-table__div--added">\
					<div class="cpg-color__main-color">\
						<input type="text" class="cpg-color-picker" maxlength="7" name="cpg_options[color_table][' +
					new_color_lower +
					'][code]"/><br/>\
					</div>\
					<div class="row-actions">\
						<span class="trash"><a href="#">Trash</a></span>\
					</div>\
				</td>\
				<td>\
					<input type="text" class="cpg-color-name" readonly="readonly" value="' +
					new_color +
					'" placeholder="Color name" name="cpg_options[color_table][' +
					new_color_lower +
					'][name]"/>\
				</td>\
				<td>\
					<div class="cpg-color-table__colors">\
					</div>\
					<div class="cpg-color-table__div"><button class="button tiny" data-add-color>Add color tint</button></div>\
				</td>\
			</tr>'
			);

			jQuery('.cpg-color-table__div--added .cpg-color-picker').iris({
				mode: 'rgb',
				width: 158,
				change: function(event, ui) {
					jQuery(event.target).css(
						'background-color',
						ui.color.toString()
					);
				}
			});
		}
	});

	function cpg_CreateImg(src, id, nonce, colors, regenerate) {
		var img = new Image();
		img.src = src;
		img.onload = function() {
			cpg_colorThief = new ColorThief();
			cpg_AddColorsForImage(img, id, nonce, colors, regenerate);
		};
		img.onerror = function() {
			if (
				jQuery('.cpg__inside--generate .cpg__inside--scroller').length <
				1
			) {
				jQuery('.cpg__inside--generate').append(
					'<div class="cpg__inside--scroller"/>'
				);
			}
			cpg_ExcludeFromBulk(id, nonce);
		};
	}

	function cpg_ExcludeFromBulk(id, nonce) {
		jQuery.ajax({
			url: ajaxurl,
			type: 'post',
			dataType: 'JSON',
			timeout: 0,
			data: {
				action: 'cpg_exclude_bulk',
				id: id,
				nonce: nonce
			},
			success: function(response) {
				if (response.more) {
					jQuery(
						'.cpg__inside--generate .cpg__inside--scroller'
					).prepend('<p>' + cpg.loading_failed + ': ' + id + '</p>');
					cpg_CreateImg(response.src, response.id, response.nonce);
				} else {
					jQuery('.cpg__inside--generate').html(cpg.done);
					jQuery('.cpg-hndle small').remove();
				}
				jQuery('.cpg__stats--hidden').removeClass('cpg__stats--hidden');
				jQuery('[data-with]').html(
					parseInt(jQuery('[data-with]').html()) + 1
				);
			},
			error: function(jqXHR, exception) {
				var msg = cpg_showErrors(jqXHR, exception);
				jQuery('.cpg__inside--generate').html(msg);
			}
		});
	}

	function cpg_AddColorsForImage(image, id, nonce, colors, regenerate) {
		var cpg_dominant = jQuery.Deferred();
		var cpg_palette = jQuery.Deferred();
		cpg_dominant.resolve(cpg_colorThief.getColor(image));
		cpg_palette.resolve(cpg_colorThief.getPalette(image, colors));

		jQuery
			.when(cpg_dominant, cpg_palette)
			.done(function(cpg_dominant, cpg_palette) {
				jQuery.ajax({
					url: ajaxurl,
					type: 'post',
					dataType: 'JSON',
					timeout: 0,
					data: {
						action: 'cpg_bulk_add_palette',
						dominant: cpg_dominant,
						palette: cpg_palette,
						id: id,
						nonce: nonce,
						regenerate: regenerate
					},
					success: function(response) {
						if (response.more) {
							cpg_CreateImg(
								response.src,
								response.id,
								response.nonce
							);
						} else {
							jQuery('.cpg__inside--generate').html(cpg.done);
							jQuery('.cpg-hndle small').remove();
						}
						if (response.regenerate) {
							jQuery('[data-with]').html(0);
						} else {
							jQuery('[data-with]').html(
								parseInt(jQuery('[data-with]').html()) + 1
							);
						}
					},
					error: function(jqXHR, exception) {
						var msg = cpg_showErrors(jqXHR, exception);
						jQuery('.cpg__inside--generate').html(msg);
					}
				});
			});
	}
});
