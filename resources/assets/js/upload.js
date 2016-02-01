function createUploader(uploaderId)
{
	var $uploader = $('#uploader-' + uploaderId);

	if (!$uploader || !$uploader.length) {
		alert('Cannot find uploader');
	}

	var $filelist = $uploader.find('.filelist'),
		$uploaded = $uploader.find('.uploaded'),
		$uploadAction = $uploader.find('.upload-actions'),
		$uploadBtn = $('#' + $uploader.data('uploadbtn')) || false,
		options = $uploader.data('options') || {},
		autoStart = $uploader.data('autostart') || false,
		deleteUrl = $uploader.data('deleteurl') || false,
		deleteMethod = $uploader.data('deletemethod') || 'DELETE';

	defaultOptions = {
		init: {
			PostInit: function(up) {   
				if (!autoStart && $uploadBtn) {
					$uploadBtn.click(function() {
						up.start();
						return false;
					});                         
				}
			},
	 
			FilesAdded: function(up, files) {
				$.each(files, function(i, file){
					$filelist.append(
						'<div id="' + file.id + '" class="alert alert-file">' +
						'<div class="filename hide">' + file.name + ' (' + plupload.formatSize(file.size) + ')  <button type="button" class="close cancelUpload">&times;</button></div>' +
						'<div class="progress progress-striped"><div class="progress-bar" style="width: 0;"></div></div></div>');

					$filelist.on('click', '#' + file.id + ' button.cancelUpload', function() {
						var $this = $(this),
							$file = $('#' + file.id),
							deleteUrl = $this.data('deleteurl') || false,
							id = $this.data('id') || false;
						
						if (deleteUrl) {
							$.ajax({
								dataType: 'json',
								type: deleteMethod,
								url: deleteUrl,
								data: options.multipart_params,
								success: function(result) {                                  
									if (result.success) {
										up.removeFile(file);
										$('#' + file.id).remove();
										$('#' + file.id + '-hidden').remove();
										$uploadAction.show();
									}
									else {
										$('#' + file.id).append('<span class="text-danger">' + result.message + '</span>');
									}
								}
							});
						}
						else {
							$uploadAction.show();     
							$file.hide(); 
							up.removeFile(file);                 
						}
					});
				}); 
				up.refresh(); // Reposition Flash/Silverlight
				if (autoStart) {
					$uploadAction.hide();
					up.start();
				}
			},
	 
			UploadProgress: function(up, file) {   
				$uploadAction.hide();             
				$('#' + file.id + ' .progress').addClass('active');
				$('#' + file.id + ' button.cancelUpload').hide();
				$('#' + file.id + ' .progress .progress-bar').animate({width: file.percent + '%'}, 100, 'linear');
			},
	 
			Error: function(up, err) {
				$filelist.append('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>' +
					'Error: ' + err.code + ', Message: ' + err.message +
						(err.file ? ', File: ' + err.file.name : '') +
						"</div>"
				);
				up.refresh(); // Reposition Flash/Silverlight
			},

			FileUploaded: function(up, file, info) {
				var response = JSON.parse(info.response);

				$('#' + file.id + ' .progress .progress-bar').animate({width: '100%'}, 100,  'linear');
				$('#' + file.id + ' .progress').removeClass('progress-striped').removeClass('active').fadeOut();
				$('#' + file.id + ' .filename').removeClass('hide').show();                
				$('#' + file.id + ' button.cancelUpload').show();                
				
				if (response.result.id) {
					$('#' + file.id + ' button.cancelUpload').attr('data-id', response.result.id);
					$('<input type="hidden" name="' + uploaderId + '_files[]" value="' + response.result.id + '" id="' + file.id + '-hidden">').appendTo($uploader);
				}

				if (response.result.deleteUrl) {
					$('#' + file.id + ' button.cancelUpload').attr('data-deleteurl', response.result.deleteUrl);
				}

				if (response.result.url) {
					$('#' + file.id).append('<img src="' + response.result.url + '" class="img-responsive img-thumbnail" />');
				}

			}
		}
	};

	$.extend(options, defaultOptions);

	var uploader = new plupload.Uploader(options);
	uploader.init();
}