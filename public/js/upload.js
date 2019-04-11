$('#image-input').on('change', function()
{
	var selectImage = $('#select-image');
	var cancelImage = $('#cancel-image');

	$('#upload-form').submit();
	selectImage.text('Your image is uploading, please wait');
});

$('#select-image, #cancel-image').on('click', function()
{
	$('#image-input').click();
});

$('.delete').on('click', function() {
	return confirm('Are you sure? This image WILL BE DELETED');
});

$('#ban').on('click', function() {
	return confirm('Are you sure? This user will be BANNED and ALL OF THEIR IMAGES WILL BE DELETED');
});

$('#links li input').on('click', function()
{
	$(this).select();
});

// Upload handler
var uploadButton = $('<button/>')
	.addClass('btn btn-primary')
	.prop('disabled', true)
	.text('Processing...')
	.on('click', function () {
		var $this = $(this),
			data = $this.data();
		$this
			.off('click')
			.text('Abort')
			.on('click', function () {
				$this.remove();
				data.abort();
			});
		data.submit().always(function () {
			$this.remove();
		});
	});


$('#fileupload').fileupload({
	url: $('#fileupload').attr('action'),
	dataType: 'json',
	autoUpload: true,
	acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
	maxFileSize: max_upload,
	// Do not resize.
	disableImageResize: true,
	previewMaxWidth: 130,
	previewMaxHeight: 130,
	previewCrop: true
}).on('fileuploadadd', function (e, data) {
	data.context = $('<div class="upload-part"/>').appendTo('#files');
	$.each(data.files, function (index, file) {
		var previewPanel = $('<div class="preview">').append($('<span class="name"/>').attr('title', file.name).text(file.name))
		data.context.append(previewPanel);
		if (!index) {
			data.context.append('<div class="actions">');
		}
	});
}).on('fileuploadprocessalways', function (e, data) {
	var index = data.index,
		file = data.files[index],
		node = $(data.context);
	window.xx = data
	if (file.preview) {
		node.find('.preview')
			.prepend('<br>')
			.prepend(file.preview);
	}
	if (file.error) {
		node.append($('<span class="upload-error"/>').text(file.error));
	}
}).on('fileuploadprogressall', function (e, data) {
	var progress = parseInt(data.loaded / data.total * 100, 10);
	$('progress').val(progress);
}).on('fileuploaddone', function (e, data) {
	$.each(data.result.files, function (index, file) {
		if (file.url) {
			var link = $('<a>')
				.attr('target', '_blank')
				.prop('href', file.url);
			$(data.context).find('.preview').wrap(link);
			
			var delUrl = file.deleteUrl;
			if (delUrl) {
				var delBtn = $('<a class="delete-btn">')
					.attr('target', '_blank')
					.text('DELETE')
					.prop('href', file.deleteUrl)
					.click(function (e) {
						e.preventDefault();

						$.getJSON(delUrl + '&ajax=1').then(function (json) {
							if (json.success) {
								data.context.remove();
							}
						});
					});
				data.context.find('.actions').append(delBtn);
			}
		} else if (file.error) {
			var error = $('<span class="upload-error"/>').text(file.error);
			$(data.context).append('<br>').append(error);
		}
	});
}).on('fileuploadfail', function (e, data) {
	$.each(data.files, function (index) {
		var error = $('<span class="upload-error"/>').text('File upload failed.');
		$(data.context).append('<br>').append(error);
	});
}).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
// });

