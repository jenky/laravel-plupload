function createUploader(uploader)
{
	var $uploader = $('#uploader-' + uploader);

	if (!$uploader || !$uploader.length) {
		alert('Cannot find uploader');
	}

	var	$filelist = $uploader.find('.filelist'),
		$uploaded = $uploader.find('.uploaded'),
		$uploadAction = $uploader.find('.upload-actions'),
		$uploadBtn = $('#' + $uploader.data('uploadbtn')) || false,
		options = $uploader.data('options') || {},
		autoStart = $uploader.data('autostart') || false;

	defaultOptions = {
		init: {
			PostInit: function(up) {   
				if (!autoStart && $uploadBtn) {
					$uploadBtn.click(function() {
						$uploadAction.hide();
						up.start();
						return false;
					});                         
				}
			},
	 
			FilesAdded: function(up, files) {
				// $filelist.find('.alert-file button.close').trigger('click'); //limit uploading to 1
				// $uploaded.html('');
				// $uploadAction.hide();
				$.each(files, function(i, file){
					$filelist.append(
						'<div id="' + file.id + '" class="alert alert-file">' +
						'<span class="filename hide">' + file.name + ' (' + plupload.formatSize(file.size) + ') </span> <button type="button" class="close cancelUpload">&times;</button>' +
						'<div class="progress progress-striped"><div class="progress-bar" style="width: 1%;"></div></div></div>');

					$filelist.on('click', '#' + file.id + ' button.cancelUpload', function(){
						$uploadAction.show();                       
					});
				});
				up.refresh(); // Reposition Flash/Silverlight
				if (autoStart) {
					$uploadAction.hide();
					up.start();
				}
			},
	 
			UploadProgress: function(up, file) {                
				//if(!$('#' + file.id + ' .progress').hasClass('progress-striped')){
					$('#' + file.id + ' .progress').addClass('active');
					$('#' + file.id + ' button.cancelUpload').hide();
				//}
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
				$('#' + file.id + ' button.cancelUpload').attr('data-id', response.id).show();                
			}
		}
	};

	$.extend(options, defaultOptions);

	var uploader = new plupload.Uploader(options);
	uploader.init();
}