(function ($) {
    "use strict";

    if (!$.wpfiWcAdminExtensions)
        $.wpfiWcAdminExtensions = {};
    
    function WJBPWCAdminMainCore() {
        var self = this;
        self.init();
    };

    WJBPWCAdminMainCore.prototype = {
        /**
         *  Initialize
         */
        init: function() {
            var self = this;

            self.mixes();
        },
        mixes: function() {
            var self = this;

            var val_package_type = $('#_job_package_package_type').val();
            self.changePackageTypeFn(val_package_type);
            $('#_job_package_package_type').on('change', function() {
                var val_package_type = $(this).val();
                self.changePackageTypeFn(val_package_type);
            });

            self.productPackageTypeFn();


            var val_detail = $('input[name=freelancer_restrict_contact_info]:checked').val();
            var restrict_type = $('#freelancer_restrict_type').val();
            self.changeRestrictFreelancerFn(val_detail, restrict_type);
            $('input[name=freelancer_restrict_contact_info]').on('change', function() {
                var val_detail = $('input[name=freelancer_restrict_contact_info]:checked').val();
                var restrict_type = $('#freelancer_restrict_type').val();
                self.changeRestrictFreelancerFn(val_detail, restrict_type);
            });
            
            $('#freelancer_restrict_type').on('change', function() {
                var restrict_type = $(this).val();
                var val_detail = $('input[name=freelancer_restrict_contact_info]:checked').val();
                self.changeRestrictFreelancerFn(val_detail, restrict_type);
            });
        },
        changePackageTypeFn: function(val_package_type) {
            if ( val_package_type == 'job_package' ) {
                $('#_job_package_job_package').css({'display': 'block'});
                //
                $('#_job_package_service_package').css({'display': 'none'});
                $('#_job_package_project_package').css({'display': 'none'});
                $('#_job_package_cv_package').css({'display': 'none'});
                $('#_job_package_contact_package').css({'display': 'none'});
                $('#_job_package_freelancer_package').css({'display': 'none'});
                $('#_job_package_resume_package').css({'display': 'none'});
            } else if ( val_package_type == 'service_package' ) {
                $('#_job_package_service_package').css({'display': 'block'});

                $('#_job_package_job_package').css({'display': 'none'});
                $('#_job_package_project_package').css({'display': 'none'});
                $('#_job_package_cv_package').css({'display': 'none'});
                $('#_job_package_contact_package').css({'display': 'none'});
                $('#_job_package_freelancer_package').css({'display': 'none'});
                $('#_job_package_resume_package').css({'display': 'none'});
            } else if ( val_package_type == 'project_package' ) {
                $('#_job_package_project_package').css({'display': 'block'});

                $('#_job_package_job_package').css({'display': 'none'});
                $('#_job_package_service_package').css({'display': 'none'});
                $('#_job_package_cv_package').css({'display': 'none'});
                $('#_job_package_contact_package').css({'display': 'none'});
                $('#_job_package_freelancer_package').css({'display': 'none'});
                $('#_job_package_resume_package').css({'display': 'none'});
            } else if ( val_package_type == 'cv_package' ) {
                $('#_job_package_cv_package').css({'display': 'block'});
                //
                $('#_job_package_service_package').css({'display': 'none'});
                $('#_job_package_project_package').css({'display': 'none'});
                $('#_job_package_job_package').css({'display': 'none'});
                $('#_job_package_contact_package').css({'display': 'none'});
                $('#_job_package_freelancer_package').css({'display': 'none'});
                $('#_job_package_resume_package').css({'display': 'none'});
            } else if ( val_package_type == 'contact_package' ) {
                $('#_job_package_contact_package').css({'display': 'block'});
                //
                $('#_job_package_service_package').css({'display': 'none'});
                $('#_job_package_project_package').css({'display': 'none'});
                $('#_job_package_job_package').css({'display': 'none'});
                $('#_job_package_cv_package').css({'display': 'none'});
                $('#_job_package_freelancer_package').css({'display': 'none'});
                $('#_job_package_resume_package').css({'display': 'none'});
            } else if ( val_package_type == 'freelancer_package' ) {
                $('#_job_package_freelancer_package').css({'display': 'block'});
                //
                $('#_job_package_service_package').css({'display': 'none'});
                $('#_job_package_project_package').css({'display': 'none'});
                $('#_job_package_job_package').css({'display': 'none'});
                $('#_job_package_cv_package').css({'display': 'none'});
                $('#_job_package_contact_package').css({'display': 'none'});
                $('#_job_package_resume_package').css({'display': 'none'});
            } else if ( val_package_type == 'resume_package' ) {
                $('#_job_package_resume_package').css({'display': 'block'});
                //
                $('#_job_package_service_package').css({'display': 'none'});
                $('#_job_package_project_package').css({'display': 'none'});
                $('#_job_package_job_package').css({'display': 'none'});
                $('#_job_package_cv_package').css({'display': 'none'});
                $('#_job_package_contact_package').css({'display': 'none'});
                $('#_job_package_freelancer_package').css({'display': 'none'});
            } else {
                $('#_job_package_service_package').css({'display': 'none'});
                $('#_job_package_project_package').css({'display': 'none'});
                $('#_job_package_resume_package').css({'display': 'none'});
                $('#_job_package_job_package').css({'display': 'none'});
                $('#_job_package_cv_package').css({'display': 'none'});
                $('#_job_package_contact_package').css({'display': 'none'});
                $('#_job_package_freelancer_package').css({'display': 'none'});
            }
        },
        productPackageTypeFn: function() {

            jQuery('#product-type').change( function() {
                jQuery('#woocommerce-product-data').removeClass(function(i, classNames) {
                    var classNames = classNames.match(/is\_[a-zA-Z\_]+/g);
                    if ( ! classNames ) {
                        return '';
                    }
                    return classNames.join(' ');
                });
                jQuery('#woocommerce-product-data').addClass( 'is_' + jQuery(this).val() );
            } );
            
            $('.pricing').addClass( 'show_if_job_package show_if_cv_package show_if_contact_package show_if_freelancer_package show_if_resume_package' );
            $('._tax_status_field').closest('div').addClass( 'show_if_job_package show_if_job_package_subscription show_if_service_package show_if_service_package_subscription show_if_project_package show_if_project_package_subscription show_if_cv_package show_if_cv_package_subscription show_if_contact_package show_if_contact_package_subscription' );
            $('._tax_status_field').closest('div').addClass( 'show_if_freelancer_package show_if_freelancer_package_subscription show_if_resume_package show_if_resume_package_subscription' );
            $('.show_if_subscription, .grouping.pricing').addClass( 'show_if_job_package_subscription show_if_service_package_subscription show_if_project_package_subscription show_if_cv_package_subscription show_if_contact_package_subscription show_if_freelancer_package_subscription show_if_resume_package_subscription' );
            jQuery('.options_group.pricing ._regular_price_field').addClass( 'hide_if_job_package_subscription hide_if_service_package_subscription hide_if_project_package_subscription hide_if_cv_package_subscription hide_if_contact_package_subscription hide_if_freelancer_package_subscription hide_if_resume_package_subscription' );
            $('#product-type').change();

            $('#_service_package_subscription_type').change(function(){
                if ( $(this).val() === 'listing' ) {
                    $('#_services_duration').closest('.form-field').hide().val('');
                } else {
                    $('#_services_duration').closest('.form-field').show();
                }
            }).change();

            $('#_project_package_subscription_type').change(function(){
                if ( $(this).val() === 'listing' ) {
                    $('#_projects_duration').closest('.form-field').hide().val('');
                } else {
                    $('#_projects_duration').closest('.form-field').show();
                }
            }).change();

            $('#_job_package_subscription_type').change(function(){
                if ( $(this).val() === 'listing' ) {
                    $('#_jobs_duration').closest('.form-field').hide().val('');
                } else {
                    $('#_jobs_duration').closest('.form-field').show();
                }
            }).change();

            $('#_resume_package_subscription_type').change(function(){
                if ( $(this).val() === 'listing' ) {
                    $('#_resumes_duration').closest('.form-field').hide().val('');
                } else {
                    $('#_resumes_duration').closest('.form-field').show();
                }
            }).change();

            $('#_cv_package_subscription_type').change(function(){
                if ( $(this).val() === 'listing' ) {
                    $('#_cv_package_expiry_time').closest('.form-field').hide().val('');
                } else {
                    $('#_cv_package_expiry_time').closest('.form-field').show();
                }
            }).change();

            $('#_contact_package_subscription_type').change(function(){
                if ( $(this).val() === 'listing' ) {
                    $('#_contact_package_expiry_time').closest('.form-field').hide().val('');
                } else {
                    $('#_contact_package_expiry_time').closest('.form-field').show();
                }
            }).change();

            $('#_freelancer_package_subscription_type').change(function(){
                if ( $(this).val() === 'listing' ) {
                    $('#_freelancer_package_expiry_time').closest('.form-field').hide().val('');
                } else {
                    $('#_freelancer_package_expiry_time').closest('.form-field').show();
                }
            }).change();
        },
        changeRestrictFreelancerFn: function(val_detail, restrict_type) {
            if ( restrict_type == 'view_contact_info' && val_detail == 'register_employer_contact_with_package' ) {
                $('.cmb2-id-contact-package-page-id').css({'display': 'block'});
            } else {
                $('.cmb2-id-contact-package-page-id').css({'display': 'none'});
            }
        }
    }

    $.wpfiWcAdminMainCore = WJBPWCAdminMainCore.prototype;
    
    $(document).ready(function() {
        // Initialize script
        new WJBPWCAdminMainCore();
    });
    
})(jQuery);

