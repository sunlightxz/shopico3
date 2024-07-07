jQuery(document).ready(function($){
    "use strict";
    
    if ( typeof wp.data !== 'undefined' ) {
        const { select, subscribe } = wp.data;

        class PageTemplateSwitcher {

            constructor() {
                this.template = null;
            }

            init() {

                subscribe( () => {

                    const newTemplate = select( 'core/editor' ).getEditedPostAttribute( 'template' );

                    if ( newTemplate && newTemplate !== this.template ) {
                        this.template = newTemplate;
                        this.changeTemplate();
                    }

                });
            }

            changeTemplate() {
                // do your stuff here
                // console.log(`template changed to ${this.template}`);

                show_hide_page_setting(this.template);
            }
        }

        new PageTemplateSwitcher().init();
    }
    
    // Jobs|Employers|Freelancer Template
    $(document).on('change', '#page_template', function(){
        var val = $(this).val();
        show_hide_page_setting(val);
    });
    setTimeout(function() {
        if ( $('#page_template').length > 0 ) {
            var val = $('#page_template').val();
            show_hide_page_setting(val);
        }
    }, 100);
    
    function show_hide_page_setting(val) {
        
        if ( val == 'page-jobs.php' ) {
            $('#apus_page_jobs_setting').show();

            $('#apus_page_employers_setting').hide();
            $('#apus_page_freelancers_setting').hide();
            $('#apus_page_services_setting').hide();
            $('#apus_page_projects_setting').hide();

            $('.cmb2-id-apus-page-left-sidebar').hide();
            $('.cmb2-id-apus-page-right-sidebar').hide();

        } 
        // Employers template
        else if ( val == 'page-employers.php' ) {
            $('#apus_page_employers_setting').show();

            $('#apus_page_jobs_setting').hide();
            $('#apus_page_freelancers_setting').hide();
            $('#apus_page_services_setting').hide();
            $('#apus_page_projects_setting').hide();

            $('.cmb2-id-apus-page-left-sidebar').hide();
            $('.cmb2-id-apus-page-right-sidebar').hide();

        } 
        // Freelancers template
        else if ( val == 'page-freelancers.php' ) {
            $('#apus_page_freelancers_setting').show();

            $('#apus_page_jobs_setting').hide();
            $('#apus_page_employers_setting').hide();
            $('#apus_page_services_setting').hide();
            $('#apus_page_projects_setting').hide();

            $('.cmb2-id-apus-page-left-sidebar').hide();
            $('.cmb2-id-apus-page-right-sidebar').hide();
        }
        // Services template
        else if ( val == 'page-services.php' ) {
            $('#apus_page_services_setting').show();

            $('#apus_page_jobs_setting').hide();
            $('#apus_page_employers_setting').hide();
            $('#apus_page_freelancers_setting').hide();
            $('#apus_page_projects_setting').hide();

            $('.cmb2-id-apus-page-left-sidebar').hide();
            $('.cmb2-id-apus-page-right-sidebar').hide();
        }
        // Projects template
        else if ( val == 'page-projects.php' ) {
            $('#apus_page_projects_setting').show();

            $('#apus_page_jobs_setting').hide();
            $('#apus_page_employers_setting').hide();
            $('#apus_page_freelancers_setting').hide();
            $('#apus_page_services_setting').hide();

            $('.cmb2-id-apus-page-left-sidebar').hide();
            $('.cmb2-id-apus-page-right-sidebar').hide();
        } else {
            $('#apus_page_freelancers_setting').hide();
            $('#apus_page_jobs_setting').hide();
            $('#apus_page_employers_setting').hide();
            $('#apus_page_services_setting').hide();
            $('#apus_page_projects_setting').hide();

            $('.cmb2-id-apus-page-left-sidebar').show();
            $('.cmb2-id-apus-page-right-sidebar').show();
        }
    }
    
    // service
    $(document).on('change', '#apus_page_layout_type', function(){
        var val = $(this).val();
        if ( val == 'default' ) {
            $('.cmb2-id-apus-page-services-show-filter-top').show();
        } else {
            $('.cmb2-id-apus-page-services-show-filter-top').hide();
        }
    });
    setTimeout(function() {
        if ( $('#apus_page_layout_type').length > 0 ) {
            var val = $('#apus_page_layout_type').val();
            if ( val == 'default' ) {
                $('.cmb2-id-apus-page-services-show-filter-top').show();
            } else {
                $('.cmb2-id-apus-page-services-show-filter-top').hide();
            }
        }
    }, 100);

    // employer
    $(document).on('change', '#apus_page_services_display_mode', function(){
        var val = $(this).val();
        show_hide_service_setting(val);
    });
    setTimeout(function() {
        if ( $('#apus_page_services_display_mode').length > 0 ) {
            var val = $('#apus_page_services_display_mode').val();
            show_hide_service_setting(val);
        }
    }, 100);
    function show_hide_service_setting(val) {
        if ( val == 'grid' ) {
            $('.cmb2-id-apus-page-services-inner-grid-style').show();
            $('.cmb2-id-apus-page-services-inner-list-style').hide();
        } else if( val == 'list' ) {
            $('.cmb2-id-apus-page-services-inner-grid-style').hide();
            $('.cmb2-id-apus-page-services-inner-list-style').show();
        }
    }

    

    

});


