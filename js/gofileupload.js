(function( $ ) {
	$.fn.goFileUpload = function(callback) {
		var upload_counter = 0;
		var jqXHR = new Array();
		var target = $(this);
		var upload_list = target.find('.l-upload-list');
		target.fileupload( {
			dropZone: target,
			pasteZone: target,
			acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
			add: function(e, data) {
				$.each(data.files, function(index, value) {
					data.context = $('<div class="l-upload-status fresh">\
						<div class="l-upload-bar" style="width: 0%;"></div>\
						<div class="l-upload-progress">0%</div>\
						<div class="l-upload-name"><span>'+value.name+'</span></div>\
					</div>').appendTo(upload_list);
				});
				data.submit();
				upload_counter = upload_counter + 1;
				jqXHR.push(data.jqXHR);
			},
			start: function() {
				target.find('#l_upload_notification').waDialog({
					onSubmit: function () {
						return false;
					}
				});
				target.find('.l-upload-status').removeClass('l-fresh-error');
				target.find('.l-upload-errors').hide();
				target.find('.l-cancel').text('Прервать').addClass('l-cancel-upload');
			},
			done: function(e, data) {
				if(data.context) {
					data.context.each(function(index) {
						if(typeof(data.result.data[index]) == 'undefined') {
							$(this).addClass('l-progress-error');
							$(this).addClass('l-fresh-error');
							$(this).find('.l-upload-progress').remove();
							$(this).find('.l-upload-name span').css('font-weight', 'bold');
							$(this).find('.l-upload-name span').css('margin-right', '5px');
							$(this).find('.l-upload-name').append('Ошибка загрузки на стороне сервера');
						}
						else {
							var fData = data.result.data[index];
							if(fData.result == '1') {
								callback(fData);
							}
							else {
								$(this).addClass('l-progress-error');
								$(this).addClass('l-fresh-error');
								$(this).find('.l-upload-progress').remove();
								$(this).find('.l-upload-name span').css('font-weight', 'bold');
								$(this).find('.l-upload-name span').css('margin-right', '5px');
								$(this).find('.l-upload-name').append(fData.message);
							}
						}
					});
				}
				upload_counter = upload_counter - 1;
				if(upload_counter == 0) {
					if(target.find('.l-upload-list').find('.l-fresh-error').length) {
						target.find('.l-upload-errors').show();
					}
					else {
						target.find('#l_upload_notification').find('.cancel').trigger('click');
					}
					target.find('.l-cancel').text('Закрыть').removeClass('l-cancel-upload');
					target.find('.l-upload-status').removeClass('fresh');
					jqXHR = new Array();
				}
			},
			progress: function (e, data) {
				var progress_percentage = parseInt(data.loaded / data.total * 100, 10);
				data.context.find('.l-upload-bar').css('width', progress_percentage+'%');
				data.context.find('.l-upload-progress').text(progress_percentage+'%');
			},
			fail: function (e, data) {
			}
		});
		target.find('.l-cancel').click(function() {
			if($(this).hasClass('l-cancel-upload')) {
				$.each(jqXHR, function(index, value) {
					value.abort();
				});
				jqXHR = new Array();
				target.find('.l-upload-status.fresh').remove();
				$(this).text('Закрыть').removeClass('l-cancel-upload');
				upload_counter = 0;
			}
		});
	};
})(jQuery);