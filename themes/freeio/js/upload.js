jQuery(document).ready(function($){
	"use strict";
	var freeio_upload;
	var freeio_selector;

	function freeio_add_file(event, selector) {

		var upload = $(".uploaded-file"), frame;
		var $el = $(this);
		freeio_selector = selector;

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( freeio_upload ) {
			freeio_upload.open();
			return;
		} else {
			// Create the media frame.
			freeio_upload = wp.media.frames.freeio_upload =  wp.media({
				// Set the title of the modal.
				title: "Select Image",

				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: "Selected",
					// Tell the button not to close the modal, since we're
					// going to refresh the page when the image is selected.
					close: false
				}
			});

			// When an image is selected, run a callback.
			freeio_upload.on( 'select', function() {
				// Grab the selected attachment.
				var attachment = freeio_upload.state().get('selection').first();

				freeio_upload.close();
				freeio_selector.find('.upload_image').val(attachment.attributes.url).change();
				if ( attachment.attributes.type == 'image' ) {
					freeio_selector.find('.freeio_screenshot').empty().hide().prepend('<img src="' + attachment.attributes.url + '">').slideDown('fast');
				}
			});

		}
		// Finally, open the modal.
		freeio_upload.open();
	}

	function freeio_remove_file(selector) {
		selector.find('.freeio_screenshot').slideUp('fast').next().val('').trigger('change');
	}
	
	$('body').on('click', '.freeio_upload_image_action .remove-image', function(event) {
		freeio_remove_file( $(this).parent().parent() );
	});

	$('body').on('click', '.freeio_upload_image_action .add-image', function(event) {
		freeio_add_file(event, $(this).parent().parent());
	});

});