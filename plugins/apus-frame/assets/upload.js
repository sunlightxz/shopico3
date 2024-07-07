jQuery(document).ready(function($){

	var optionsframe_upload;
	var optionsframe_selector;

	function optionsframe_add_file(event, selector) {

		var upload = $(".uploaded-file"), frame;
		var $el = $(this);
		optionsframe_selector = selector;

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( optionsframe_upload ) {
			optionsframe_upload.open();
		} else {
			// Create the media frame.
			optionsframe_upload = wp.media.frames.optionsframe_upload =  wp.media({
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
			optionsframe_upload.on( 'select', function() {
				// Grab the selected attachment.
				var attachment = optionsframe_upload.state().get('selection').first();
				optionsframe_upload.close();
				optionsframe_selector.find('.upload_image').val(attachment.attributes.url).change();
				if ( attachment.attributes.type == 'image' ) {
					optionsframe_selector.find('.screenshot').empty().hide().prepend('<img src="' + attachment.attributes.url + '">').slideDown('fast');
				}
			});

		}
		// Finally, open the modal.
		optionsframe_upload.open();
	}

	function optionsframe_remove_file(selector) {
		selector.find('.screenshot').slideUp('fast').next().val('').trigger('change');
	}
	
	$('body').delegate('.upload_image_action .remove-image', 'click', function(event) {
		optionsframe_remove_file( $(this).parent().parent() );
	});

	$('body').delegate('.upload_image_action .add-image', 'click', function(event) {
		optionsframe_add_file(event, $(this).parent().parent());
	});

});