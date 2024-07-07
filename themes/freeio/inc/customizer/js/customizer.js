jQuery( document ).ready(function($) {
	"use strict";

	$('.google-fonts-list').each(function (i, obj) {
		if (!$(obj).hasClass('select2-hidden-accessible')) {
			$(obj).select2({
				allowClear: true,
				placeholder: $(obj).data('placeholder'),
			});
		}
	});

	$('.google-fonts-list').on('change', function() {

		var elementFontWeight = $(this).parent().parent().find('.google-fonts-fontweight-style');
		var elementSubsets = $(this).parent().parent().find('.google-fonts-subsets-style');
		var selectedFont = $(this).val();
		var customizerControlName = $(this).attr('control-name');

		// Clear Weight/Style dropdowns
		elementFontWeight.empty();
		elementSubsets.empty();


		// Get the Google Fonts control object
		var bodyfontcontrol = _wpCustomizeSettings.controls[customizerControlName];

		// For the selected Google font show the available weight/style variants
		if ( bodyfontcontrol.freeiofontslist[selectedFont] ) {
			$.each(bodyfontcontrol.freeiofontslist[selectedFont].variants, function(val, text) {
				elementFontWeight.append(
					$('<option></option>').val(text.id).html(text.name)
				);
				
			});
		}

		if ( bodyfontcontrol.freeiofontslist[selectedFont] ) {
			$.each(bodyfontcontrol.freeiofontslist[selectedFont].subsets, function(val, text) {
				elementSubsets.append(
					$('<option></option>').val(text.id).html(text.name)
				);
			});
		}


		freeioGetAllSelects($(this).parent().parent());
	});

	$('.google_fonts_select_control select').on('change', function() {
		freeioGetAllSelects($(this).parent().parent());
	});

	function freeioGetAllSelects($element) {
		var selectedFont = {
			fontfamily: $element.find('.google-fonts-list').val(),
			fontweight: $element.find('.google-fonts-fontweight-style').val(),
			subsets: $element.find('.google-fonts-subsets-style').val()
		};

		// Important! Make sure to trigger change event so Customizer knows it has to save the field
		$element.find('.customize-control-google-font-selection').val(JSON.stringify(selectedFont)).trigger('change');
	}

	// project
	var template_type = $('#_customize-input-freeio_theme_options_project_archive_elementor_template').val();
	change_projects_template_type(template_type);
	$('#_customize-input-freeio_theme_options_project_archive_elementor_template').on('change', function() {
		var template_type = $(this).val();
		change_projects_template_type(template_type);
	});
	function change_projects_template_type(template_type) {
		if ( template_type !== '' ) {
			$('#customize-control-freeio_theme_options_project_archive_fullwidth').hide();
			$('#customize-control-freeio_settings_project_archive_blog_archive').hide();
			$('#customize-control-freeio_settings_project_archive_layout').hide();
			$('#customize-control-freeio_settings_projects_show_top_content').hide();
			$('#customize-control-freeio_settings_project_display_mode').hide();
			$('#customize-control-freeio_settings_projects_inner_list_style').hide();
			$('#customize-control-freeio_settings_projects_inner_grid_style').hide();
			$('#customize-control-freeio_settings_project_archive_project_columns').hide();
			$('#customize-control-freeio_settings_projects_pagination').hide();

		} else {
			$('#customize-control-freeio_theme_options_project_archive_fullwidth').show();
			$('#customize-control-freeio_settings_project_archive_blog_archive').show();
			$('#customize-control-freeio_settings_project_archive_layout').show();
			$('#customize-control-freeio_settings_projects_show_top_content').show();
			$('#customize-control-freeio_settings_project_display_mode').show();
			$('#customize-control-freeio_settings_projects_inner_list_style').show();
			$('#customize-control-freeio_settings_projects_inner_grid_style').show();
			$('#customize-control-freeio_settings_project_archive_project_columns').show();
			$('#customize-control-freeio_settings_projects_pagination').show();

		}
	}

	var template_type = $('#_customize-input-freeio_theme_options_project_single_elementor_template').val();
	change_project_template_type(template_type);
	$('#_customize-input-freeio_theme_options_project_single_elementor_template').on('change', function() {
		var template_type = $(this).val();
		change_project_template_type(template_type);
	});
	function change_project_template_type(template_type) {
		if ( template_type !== '' ) {
			$('#customize-control-freeio_settings_project_layout_type').hide();
			$('#customize-control-project_header_bg_image').hide();
			$('#customize-control-freeio_theme_options_show_project_detail').hide();
			$('#customize-control-freeio_theme_options_show_project_description').hide();
			$('#customize-control-freeio_theme_options_show_project_attachments').hide();
			$('#customize-control-freeio_theme_options_show_project_skills').hide();
			$('#customize-control-freeio_theme_options_show_project_proposals').hide();
			$('#customize-control-freeio_theme_options_show_project_faq').hide();
			$('#customize-control-freeio_theme_options_show_project_related').hide();
			$('#customize-control-freeio_settings_project_single_number_project_related').hide();
			$('#customize-control-freeio_settings_project_single_related_project_columns').hide();

		} else {
			$('#customize-control-freeio_settings_project_layout_type').show();
			$('#customize-control-project_header_bg_image').show();
			$('#customize-control-freeio_theme_options_show_project_detail').show();
			$('#customize-control-freeio_theme_options_show_project_description').show();
			$('#customize-control-freeio_theme_options_show_project_attachments').show();
			$('#customize-control-freeio_theme_options_show_project_skills').show();
			$('#customize-control-freeio_theme_options_show_project_proposals').show();
			$('#customize-control-freeio_theme_options_show_project_faq').show();
			$('#customize-control-freeio_theme_options_show_project_related').show();
			$('#customize-control-freeio_settings_project_single_number_project_related').show();
			$('#customize-control-freeio_settings_project_single_related_project_columns').show();

		}
	}

	// service
	var template_type = $('#_customize-input-freeio_theme_options_service_archive_elementor_template').val();
	change_services_template_type(template_type);
	$('#_customize-input-freeio_theme_options_service_archive_elementor_template').on('change', function() {
		var template_type = $(this).val();
		change_services_template_type(template_type);
	});
	function change_services_template_type(template_type) {
		if ( template_type !== '' ) {
			$('#customize-control-freeio_theme_options_service_archive_fullwidth').hide();
			$('#customize-control-freeio_settings_service_archive_blog_archive').hide();
			$('#customize-control-freeio_settings_services_layout_sidebar').hide();
			$('#customize-control-freeio_settings_services_show_top_content').hide();
			$('#customize-control-freeio_theme_options_services_show_offcanvas_filter').hide();
			$('#customize-control-freeio_settings_services_display_mode').hide();
			$('#customize-control-freeio_settings_services_inner_list_style').hide();
			$('#customize-control-freeio_settings_services_inner_grid_style').hide();
			$('#customize-control-freeio_settings_service_archive_services_columns').hide();
			$('#customize-control-freeio_settings_services_pagination').hide();

		} else {
			$('#customize-control-freeio_theme_options_service_archive_fullwidth').show();
			$('#customize-control-freeio_settings_service_archive_blog_archive').show();
			$('#customize-control-freeio_settings_services_layout_sidebar').show();
			$('#customize-control-freeio_settings_services_show_top_content').show();
			$('#customize-control-freeio_theme_options_services_show_offcanvas_filter').show();
			$('#customize-control-freeio_settings_services_display_mode').show();
			$('#customize-control-freeio_settings_services_inner_list_style').show();
			$('#customize-control-freeio_settings_services_inner_grid_style').show();
			$('#customize-control-freeio_settings_service_archive_services_columns').show();
			$('#customize-control-freeio_settings_services_pagination').show();

		}
	}

	var template_type = $('#_customize-input-freeio_theme_options_service_single_elementor_template').val();
	change_service_template_type(template_type);
	$('#_customize-input-freeio_theme_options_service_single_elementor_template').on('change', function() {
		var template_type = $(this).val();
		change_service_template_type(template_type);
	});
	function change_service_template_type(template_type) {
		if ( template_type !== '' ) {
			$('#customize-control-freeio_settings_service_layout_type').hide();
			$('#customize-control-service_header_bg_image').hide();
			$('#customize-control-freeio_theme_options_show_service_detail').hide();
			$('#customize-control-freeio_theme_options_show_service_gallery').hide();
			$('#customize-control-freeio_theme_options_show_service_description').hide();
			$('#customize-control-freeio_theme_options_show_service_video').hide();
			$('#customize-control-freeio_theme_options_show_service_faq').hide();
			$('#customize-control-freeio_theme_options_show_service_related').hide();
			$('#customize-control-freeio_settings_service_single_number_service_related').hide();
			$('#customize-control-freeio_settings_service_single_related_services_columns').hide();

		} else {
			$('#customize-control-freeio_settings_service_layout_type').show();
			$('#customize-control-service_header_bg_image').show();
			$('#customize-control-freeio_theme_options_show_service_detail').show();
			$('#customize-control-freeio_theme_options_show_service_gallery').show();
			$('#customize-control-freeio_theme_options_show_service_description').show();
			$('#customize-control-freeio_theme_options_show_service_video').show();
			$('#customize-control-freeio_theme_options_show_service_faq').show();
			$('#customize-control-freeio_theme_options_show_service_related').show();
			$('#customize-control-freeio_settings_service_single_number_service_related').show();
			$('#customize-control-freeio_settings_service_single_related_services_columns').show();

		}
	}

	// job
	var template_type = $('#_customize-input-freeio_theme_options_job_archive_elementor_template').val();
	change_jobs_template_type(template_type);
	$('#_customize-input-freeio_theme_options_job_archive_elementor_template').on('change', function() {
		var template_type = $(this).val();
		change_jobs_template_type(template_type);
	});
	function change_jobs_template_type(template_type) {
		if ( template_type !== '' ) {
			$('#customize-control-freeio_theme_options_job_archive_fullwidth').hide();
			$('#customize-control-freeio_settings_job_archive_blog_archive').hide();
			$('#customize-control-freeio_settings_jobs_layout_sidebar').hide();
			$('#customize-control-freeio_settings_jobs_show_top_content').hide();
			$('#customize-control-freeio_theme_options_jobs_show_offcanvas_filter').hide();
			$('#customize-control-freeio_settings_job_display_mode').hide();
			$('#customize-control-freeio_settings_job_archive_job_columns').hide();
			$('#customize-control-freeio_settings_jobs_pagination').hide();

		} else {
			$('#customize-control-freeio_theme_options_job_archive_fullwidth').show();
			$('#customize-control-freeio_settings_job_archive_blog_archive').show();
			$('#customize-control-freeio_settings_jobs_layout_sidebar').show();
			$('#customize-control-freeio_settings_jobs_show_top_content').show();
			$('#customize-control-freeio_theme_options_jobs_show_offcanvas_filter').show();
			$('#customize-control-freeio_settings_job_display_mode').show();
			$('#customize-control-freeio_settings_job_archive_job_columns').show();
			$('#customize-control-freeio_settings_jobs_pagination').show();

		}
	}

	var template_type = $('#_customize-input-freeio_theme_options_job_single_elementor_template').val();
	change_job_template_type(template_type);
	$('#_customize-input-freeio_theme_options_job_single_elementor_template').on('change', function() {
		var template_type = $(this).val();
		change_job_template_type(template_type);
	});
	function change_job_template_type(template_type) {
		if ( template_type !== '' ) {
			$('#customize-control-job_header_bg_image').hide();
			$('#customize-control-freeio_theme_options_job_fullwidth').hide();
			$('#customize-control-freeio_theme_options_show_job_detail').hide();
			$('#customize-control-freeio_theme_options_show_job_description').hide();
			$('#customize-control-freeio_theme_options_show_job_social_share').hide();
			$('#customize-control-freeio_theme_options_show_job_photos').hide();
			$('#customize-control-freeio_theme_options_show_job_video').hide();
			$('#customize-control-freeio_theme_options_show_job_related').hide();
			$('#customize-control-freeio_settings_job_single_number_job_related').hide();
			$('#customize-control-freeio_settings_job_single_related_job_columns').hide();

		} else {
			$('#customize-control-job_header_bg_image').show();
			$('#customize-control-freeio_theme_options_job_fullwidth').show();
			$('#customize-control-freeio_theme_options_show_job_detail').show();
			$('#customize-control-freeio_theme_options_show_job_description').show();
			$('#customize-control-freeio_theme_options_show_job_social_share').show();
			$('#customize-control-freeio_theme_options_show_job_photos').show();
			$('#customize-control-freeio_theme_options_show_job_video').show();
			$('#customize-control-freeio_theme_options_show_job_related').show();
			$('#customize-control-freeio_settings_job_single_number_job_related').show();
			$('#customize-control-freeio_settings_job_single_related_job_columns').show();

		}
	}

	// employer
	var template_type = $('#_customize-input-freeio_theme_options_employer_archive_elementor_template').val();
	change_employers_template_type(template_type);
	$('#_customize-input-freeio_theme_options_employer_archive_elementor_template').on('change', function() {
		var template_type = $(this).val();
		change_employers_template_type(template_type);
	});
	function change_employers_template_type(template_type) {
		if ( template_type !== '' ) {
			$('#customize-control-freeio_theme_options_employer_archive_fullwidth').hide();
			$('#customize-control-freeio_settings_employer_archive_layout').hide();
			$('#customize-control-freeio_settings_employers_show_top_content').hide();
			$('#customize-control-freeio_theme_options_employers_show_offcanvas_filter').hide();
			$('#customize-control-freeio_settings_employer_display_mode').hide();
			$('#customize-control-freeio_settings_employer_archive_employer_columns').hide();
			$('#customize-control-freeio_settings_employers_pagination').hide();

		} else {
			$('#customize-control-freeio_theme_options_employer_archive_fullwidth').show();
			$('#customize-control-freeio_settings_employer_archive_layout').show();
			$('#customize-control-freeio_settings_employers_show_top_content').show();
			$('#customize-control-freeio_theme_options_employers_show_offcanvas_filter').show();
			$('#customize-control-freeio_settings_employer_display_mode').show();
			$('#customize-control-freeio_settings_employer_archive_employer_columns').show();
			$('#customize-control-freeio_settings_employers_pagination').show();

		}
	}

	var template_type = $('#_customize-input-freeio_theme_options_employer_single_elementor_template').val();
	change_employer_template_type(template_type);
	$('#_customize-input-freeio_theme_options_employer_single_elementor_template').on('change', function() {
		var template_type = $(this).val();
		change_employer_template_type(template_type);
	});
	function change_employer_template_type(template_type) {
		if ( template_type !== '' ) {
			$('#customize-control-employer_header_bg_image').hide();
			$('#customize-control-freeio_theme_options_show_employer_description').hide();
			$('#customize-control-freeio_theme_options_show_employer_gallery').hide();
			$('#customize-control-freeio_theme_options_show_employer_video').hide();
			$('#customize-control-freeio_theme_options_show_employer_members').hide();
			$('#customize-control-freeio_theme_options_show_employer_projects').hide();
			$('#customize-control-freeio_theme_options_show_employer_open_jobs').hide();
			$('#customize-control-freeio_theme_options_employer_number_projects').hide();
			$('#customize-control-freeio_theme_options_employer_number_open_jobs').hide();

		} else {
			$('#customize-control-employer_header_bg_image').show();
			$('#customize-control-freeio_theme_options_show_employer_description').show();
			$('#customize-control-freeio_theme_options_show_employer_gallery').show();
			$('#customize-control-freeio_theme_options_show_employer_video').show();
			$('#customize-control-freeio_theme_options_show_employer_members').show();
			$('#customize-control-freeio_theme_options_show_employer_projects').show();
			$('#customize-control-freeio_theme_options_show_employer_open_jobs').show();
			$('#customize-control-freeio_theme_options_employer_number_projects').show();
			$('#customize-control-freeio_theme_options_employer_number_open_jobs').show();

		}
	}

	// freelancer
	var template_type = $('#_customize-input-freeio_theme_options_freelancer_archive_elementor_template').val();
	change_freelancers_template_type(template_type);
	$('#_customize-input-freeio_theme_options_freelancer_archive_elementor_template').on('change', function() {
		var template_type = $(this).val();
		change_freelancers_template_type(template_type);
	});
	function change_freelancers_template_type(template_type) {
		if ( template_type !== '' ) {
			$('#customize-control-freeio_theme_options_freelancer_archive_fullwidth').hide();
			$('#customize-control-freeio_settings_freelancer_archive_blog_archive').hide();
			$('#customize-control-freeio_settings_freelancer_archive_layout').hide();
			$('#customize-control-freeio_settings_freelancers_show_top_content').hide();
			$('#customize-control-freeio_theme_options_freelancers_show_offcanvas_filter').hide();
			$('#customize-control-freeio_settings_freelancer_display_mode').hide();
			$('#customize-control-freeio_settings_freelancers_inner_grid_style').hide();
			$('#customize-control-freeio_settings_freelancers_inner_list_style').hide();
			$('#customize-control-freeio_settings_freelancer_archive_freelancer_columns').hide();
			$('#customize-control-freeio_settings_freelancers_pagination').hide();

		} else {
			$('#customize-control-freeio_theme_options_freelancer_archive_fullwidth').show();
			$('#customize-control-freeio_settings_freelancer_archive_blog_archive').show();
			$('#customize-control-freeio_settings_freelancer_archive_layout').show();
			$('#customize-control-freeio_settings_freelancers_show_top_content').show();
			$('#customize-control-freeio_theme_options_freelancers_show_offcanvas_filter').show();
			$('#customize-control-freeio_settings_freelancer_display_mode').show();
			$('#customize-control-freeio_settings_freelancers_inner_grid_style').show();
			$('#customize-control-freeio_settings_freelancers_inner_list_style').show();
			$('#customize-control-freeio_settings_freelancer_archive_freelancer_columns').show();
			$('#customize-control-freeio_settings_freelancers_pagination').show();

		}
	}

	var template_type = $('#_customize-input-freeio_theme_options_freelancer_single_elementor_template').val();
	change_freelancer_template_type(template_type);
	$('#_customize-input-freeio_theme_options_freelancer_single_elementor_template').on('change', function() {
		var template_type = $(this).val();
		change_freelancer_template_type(template_type);
	});
	function change_freelancer_template_type(template_type) {
		if ( template_type !== '' ) {
			$('#customize-control-freeio_settings_freelancer_layout_type').hide();
			$('#customize-control-freelancer_header_bg_image').hide();
			$('#customize-control-freeio_theme_options_show_freelancer_detail').hide();
			$('#customize-control-freeio_theme_options_show_freelancer_description').hide();
			$('#customize-control-freeio_theme_options_show_freelancer_gallery').hide();
			$('#customize-control-freeio_theme_options_show_freelancer_video').hide();
			$('#customize-control-freeio_theme_options_show_freelancer_education').hide();
			$('#customize-control-freeio_theme_options_show_freelancer_experience').hide();
			$('#customize-control-freeio_theme_options_show_freelancer_skill').hide();
			$('#customize-control-freeio_theme_options_show_freelancer_award').hide();
			$('#customize-control-freeio_theme_options_show_freelancer_services').hide();
			$('#customize-control-freeio_theme_options_show_freelancer_related').hide();
			$('#customize-control-freeio_settings_freelancer_single_number_freelancer_related').hide();
			$('#customize-control-freeio_settings_freelancer_single_related_freelancer_columns').hide();

		} else {
			$('#customize-control-freeio_settings_freelancer_layout_type').show();
			$('#customize-control-freelancer_header_bg_image').show();
			$('#customize-control-freeio_theme_options_show_freelancer_detail').show();
			$('#customize-control-freeio_theme_options_show_freelancer_description').show();
			$('#customize-control-freeio_theme_options_show_freelancer_gallery').show();
			$('#customize-control-freeio_theme_options_show_freelancer_video').show();
			$('#customize-control-freeio_theme_options_show_freelancer_education').show();
			$('#customize-control-freeio_theme_options_show_freelancer_experience').show();
			$('#customize-control-freeio_theme_options_show_freelancer_skill').show();
			$('#customize-control-freeio_theme_options_show_freelancer_award').show();
			$('#customize-control-freeio_theme_options_show_freelancer_services').show();
			$('#customize-control-freeio_theme_options_show_freelancer_related').show();
			$('#customize-control-freeio_settings_freelancer_single_number_freelancer_related').show();
			$('#customize-control-freeio_settings_freelancer_single_related_freelancer_columns').show();

		}
	}
});