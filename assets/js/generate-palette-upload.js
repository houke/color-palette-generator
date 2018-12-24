jQuery(document).on('ready', function() {
  //old uploader
  if (typeof uploader != 'undefined') {
    uploader.bind('UploadComplete', function(up, files, info) {
      if (files) {
        setTimeout(function() {
          jQuery(files).each(function() {
            var file = jQuery(this).get(0);
            var id = file.id;
            var img = jQuery('#media-item-' + id)
              .find('img')
              .attr('src');
            var image = img.replace(cpg_upload.thumb, '');
            var edit = jQuery('#media-item-' + id)
              .find('a.edit-attachment')
              .attr('href');
            var edit_split = edit.split('?');
            var id = false;
            if (edit_split.length > 1) {
              var params = cpg_upload_getQueryParams(edit_split[1]);
              if (params.post) {
                id = params.post;
              }
            }
            cpg_generate_on_upload(image, id);
          });
        }, 1000);
      }
    });
  }

  function cpg_upload_getQueryParams(qs) {
    qs = qs.split('+').join(' ');

    var params = {},
      tokens,
      re = /[?&]?([^=]+)=([^&]*)/g;

    while ((tokens = re.exec(qs))) {
      params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }

    return params;
  }

  //new uploader
  if (typeof wp.Uploader !== 'undefined') {
    if (typeof wp.Uploader.queue !== 'undefined') {
      wp.Uploader.queue.on('reset', function(up, files) {
        if (
          typeof files !== 'undefined' &&
          files.previousModels &&
          files.previousModels.length > 0
        ) {
          jQuery(files.previousModels).each(function() {
            var file = $(this).get(0);
            cpg_generate_on_upload(file.changed.url, file.changed.id);
          });
        }
      });
    } else {
      jQuery.extend(wp.Uploader.prototype, {
        init: function() {
          // plupload 'PostInit'
          this.uploader.bind('BeforeUpload', function(file) {});
        },
        success: function(file) {
          cpg_generate_on_upload(file.changed.url, file.changed.id);
        }
      });
    }
  }

  function cpg_generate_on_upload(src, id) {
    var img = new Image();
    img.src = src;
    var nonce =
      typeof cpg_upload != 'undefined' ? cpg_upload._wpnonce : 'undefined';
    var colors =
      typeof cpg_upload != 'undefined' ? parseInt(cpg_upload.colors) : 10;
    img.onload = function() {
      cpg_colorThief = new ColorThief();
      cpg_AddColorsForImageUpload(img, id, nonce, colors, src);
    };
  }

  function cpg_AddColorsForImageUpload(image, id, nonce, colors, file) {
    var cpg_dominant = jQuery.Deferred();
    var cpg_palette = jQuery.Deferred();
    cpg_dominant.resolve(cpg_colorThief.getColor(image));
    cpg_palette.resolve(cpg_colorThief.getPalette(image, colors));
    jQuery
      .when(cpg_dominant, cpg_palette)
      .then(function(cpg_dominant, cpg_palette) {
        jQuery.ajax({
          url: ajaxurl,
          type: 'post',
          dataType: 'JSON',
          timeout: 300000,
          data: {
            action: 'cpg_add_palette',
            dominant: cpg_dominant,
            palette: cpg_palette,
            nonce: nonce,
            id: id,
            file: !id ? file : false
          },
          success: function(response) {},
          error: function(jqXHR, exception) {}
        });
      });
  }
});
