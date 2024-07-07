(function ($) {
    "use strict";

    if (!$.wpfiAdminExtensions)
        $.wpfiAdminExtensions = {};
    
    function WJBPAdminMainCore() {
        var self = this;
        self.init();
    };

    WJBPAdminMainCore.prototype = {
        /**
         *  Initialize
         */
        init: function() {
            var self = this;

            self.taxInit();

            self.emailSettings();

            self.mixes();
        },
        taxInit: function() {
            $('.tax_color_input').wpColorPicker();
        },
        emailSettings: function() {
            var show_hiden_action = function(key, checked) {
                if ( checked ) {
                    $('.cmb2-id-' + key + '-subject').show();
                    $('.cmb2-id-' + key + '-content').show();
                } else {
                    $('.cmb2-id-' + key + '-subject').hide();
                    $('.cmb2-id-' + key + '-content').hide();
                }
            }
            $('#admin_notice_add_new_job').on('change', function(){
                var key = 'admin-notice-add-new-job';
                var checked = $(this).is(":checked");
                show_hiden_action(key, checked);
            });
            var checked = $('#admin_notice_add_new_job').is(":checked");
            var key = 'admin-notice-add-new-job';
            show_hiden_action(key, checked);

            // updated
            $('#admin_notice_updated_job').on('change', function(){
                var key = 'admin-notice-updated-job';
                var checked = $(this).is(":checked");
                show_hiden_action(key, checked);
            });
            var checked = $('#admin_notice_updated_job').is(":checked");
            var key = 'admin-notice-updated-job';
            show_hiden_action(key, checked);

            // admin expiring
            $('#admin_notice_expiring_job').on('change', function(){
                var key = 'admin-notice-expiring-job';
                var checked = $(this).is(":checked");
                show_hiden_action(key, checked);
                if ( checked ) {
                    $('.cmb2-id-admin-notice-expiring-job-days').show();
                } else {
                    $('.cmb2-id-admin-notice-expiring-job-days').hide();
                }
            });
            var checked = $('#admin_notice_expiring_job').is(":checked");
            var key = 'admin-notice-expiring-job';
            show_hiden_action(key, checked);
            if ( checked ) {
                $('.cmb2-id-admin-notice-expiring-job-days').show();
            } else {
                $('.cmb2-id-admin-notice-expiring-job-days').hide();
            }

            // employer expiring
            $('#employer_notice_expiring_job').on('change', function(){
                var key = 'employer-notice-expiring-job';
                var checked = $(this).is(":checked");
                show_hiden_action(key, checked);

                if ( checked ) {
                    $('.cmb2-id-employer-notice-expiring-job-days').show();
                } else {
                    $('.cmb2-id-employer-notice-expiring-job-days').hide();
                }
            });
            var checked = $('#employer_notice_expiring_job').is(":checked");
            var key = 'employer-notice-expiring-job';
            show_hiden_action(key, checked);
            if ( checked ) {
                $('.cmb2-id-employer-notice-expiring-job-days').show();
            } else {
                $('.cmb2-id-employer-notice-expiring-job-days').hide();
            }

            $('#employer_notice_add_new_internal_apply').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('internal-apply-job-notice', checked);
            });
            var checked = $('#employer_notice_add_new_internal_apply').is(":checked");
            show_hiden_action('internal-apply-job-notice', checked);

            $('#freelancer_notice_add_thanks_apply').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('applied-job-thanks-notice', checked);
            });
            var checked = $('#freelancer_notice_add_thanks_apply').is(":checked");
            show_hiden_action('applied-job-thanks-notice', checked);

            $('#user_notice_add_new_meeting').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('meeting-create', checked);
            });
            var checked = $('#user_notice_add_new_meeting').is(":checked");
            show_hiden_action('meeting-create', checked);

            $('#user_notice_add_reschedule_meeting').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('meeting-reschedule', checked);
            });
            var checked = $('#user_notice_add_reschedule_meeting').is(":checked");
            show_hiden_action('meeting-reschedule', checked);

            $('#user_notice_add_reject_interview').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('reject-interview-notice', checked);
            });
            var checked = $('#user_notice_add_reject_interview').is(":checked");
            show_hiden_action('reject-interview-notice', checked);

            $('#user_notice_add_undo_reject_interview').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('undo-reject-interview-notice', checked);
            });
            var checked = $('#user_notice_add_undo_reject_interview').is(":checked");
            show_hiden_action('undo-reject-interview-notice', checked);

            $('#user_notice_add_approve_interview').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('approve-interview-notice', checked);
            });
            var checked = $('#user_notice_add_approve_interview').is(":checked");
            show_hiden_action('approve-interview-notice', checked);

            $('#user_notice_add_undo_approve_interview').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('undo-approve-interview-notice', checked);
            });
            var checked = $('#user_notice_add_undo_approve_interview').is(":checked");
            show_hiden_action('undo-approve-interview-notice', checked);


            // user
            $('#user_notice_add_new_user_register').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('user-register-auto-approve', checked);
            });
            var checked = $('#user_notice_add_new_user_register').is(":checked");
            show_hiden_action('user-register-auto-approve', checked);

            $('#user_notice_add_approved_user').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('user-register-approved', checked);
            });
            var checked = $('#user_notice_add_approved_user').is(":checked");
            show_hiden_action('user-register-approved', checked);

            $('#user_notice_add_denied_user').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('user-register-denied', checked);
            });
            var checked = $('#user_notice_add_denied_user').is(":checked");
            show_hiden_action('user-register-denied', checked);




            // project
            $('#admin_notice_add_new_project').on('change', function(){
                var key = 'admin-notice-add-new-project';
                var checked = $(this).is(":checked");
                show_hiden_action(key, checked);
            });
            var checked = $('#admin_notice_add_new_project').is(":checked");
            var key = 'admin-notice-add-new-project';
            show_hiden_action(key, checked);

            // updated
            $('#admin_notice_updated_project').on('change', function(){
                var key = 'admin-notice-updated-project';
                var checked = $(this).is(":checked");
                show_hiden_action(key, checked);
            });
            var checked = $('#admin_notice_updated_project').is(":checked");
            var key = 'admin-notice-updated-project';
            show_hiden_action(key, checked);

            // admin expiring
            $('#admin_notice_expiring_project').on('change', function(){
                var key = 'admin-notice-expiring-project';
                var checked = $(this).is(":checked");
                show_hiden_action(key, checked);
                if ( checked ) {
                    $('.cmb2-id-admin-notice-expiring-project-days').show();
                } else {
                    $('.cmb2-id-admin-notice-expiring-project-days').hide();
                }
            });
            var checked = $('#admin_notice_expiring_project').is(":checked");
            var key = 'admin-notice-expiring-project';
            show_hiden_action(key, checked);
            if ( checked ) {
                $('.cmb2-id-admin-notice-expiring-project-days').show();
            } else {
                $('.cmb2-id-admin-notice-expiring-project-days').hide();
            }

            // employer expiring
            $('#employer_notice_expiring_project').on('change', function(){
                var key = 'employer-notice-expiring-project';
                var checked = $(this).is(":checked");
                show_hiden_action(key, checked);

                if ( checked ) {
                    $('.cmb2-id-employer-notice-expiring-project-days').show();
                } else {
                    $('.cmb2-id-employer-notice-expiring-project-days').hide();
                }
            });
            var checked = $('#employer_notice_expiring_project').is(":checked");
            var key = 'employer-notice-expiring-project';
            show_hiden_action(key, checked);
            if ( checked ) {
                $('.cmb2-id-employer-notice-expiring-project-days').show();
            } else {
                $('.cmb2-id-employer-notice-expiring-project-days').hide();
            }


            // proposal
            $('#employer_notice_add_new_proposal').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('send-proposal-notice', checked);
            });
            var checked = $('#employer_notice_add_new_proposal').is(":checked");
            show_hiden_action('send-proposal-notice', checked);

            $('#freelancer_notice_add_hired_proposal').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('hired-proposal-notice', checked);
            });
            var checked = $('#freelancer_notice_add_hired_proposal').is(":checked");
            show_hiden_action('hired-proposal-notice', checked);

            $('#employer_notice_add_hired_proposal').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('hired-proposal-employer-notice', checked);
            });
            var checked = $('#employer_notice_add_hired_proposal').is(":checked");
            show_hiden_action('hired-proposal-employer-notice', checked);

            $('#freelancer_notice_add_completed_project').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('completed-project-notice', checked);
            });
            var checked = $('#freelancer_notice_add_completed_project').is(":checked");
            show_hiden_action('completed-project-notice', checked);

            $('#employer_notice_add_completed_project').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('completed-project-employer-notice', checked);
            });
            var checked = $('#employer_notice_add_completed_project').is(":checked");
            show_hiden_action('completed-project-employer-notice', checked);

            $('#freelancer_notice_add_cancelled_project').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('cancelled-project-notice', checked);
            });
            var checked = $('#freelancer_notice_add_cancelled_project').is(":checked");
            show_hiden_action('cancelled-project-notice', checked);

            $('#employer_notice_add_cancelled_project').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('cancelled-project-employer-notice', checked);
            });
            var checked = $('#employer_notice_add_cancelled_project').is(":checked");
            show_hiden_action('cancelled-project-employer-notice', checked);

            $('#user_notice_hired_project_message').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('hired-project-message-notice', checked);
            });
            var checked = $('#user_notice_hired_project_message').is(":checked");
            show_hiden_action('hired-project-message-notice', checked);


            $('#user_notice_add_invite_freelancer').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('invite-freelancer-notice', checked);
            });
            var checked = $('#user_notice_add_invite_freelancer').is(":checked");
            show_hiden_action('invite-freelancer-notice', checked);


            // service
            $('#admin_notice_add_new_service').on('change', function(){
                var key = 'admin-notice-add-new-service';
                var checked = $(this).is(":checked");
                show_hiden_action(key, checked);
            });
            var checked = $('#admin_notice_add_new_service').is(":checked");
            var key = 'admin-notice-add-new-service';
            show_hiden_action(key, checked);

            // updated
            $('#admin_notice_updated_service').on('change', function(){
                var key = 'admin-notice-updated-service';
                var checked = $(this).is(":checked");
                show_hiden_action(key, checked);
            });
            var checked = $('#admin_notice_updated_service').is(":checked");
            var key = 'admin-notice-updated-service';
            show_hiden_action(key, checked);

            // admin expiring
            $('#admin_notice_expiring_service').on('change', function(){
                var key = 'admin-notice-expiring-service';
                var checked = $(this).is(":checked");
                show_hiden_action(key, checked);
                if ( checked ) {
                    $('.cmb2-id-admin-notice-expiring-service-days').show();
                } else {
                    $('.cmb2-id-admin-notice-expiring-service-days').hide();
                }
            });
            var checked = $('#admin_notice_expiring_service').is(":checked");
            var key = 'admin-notice-expiring-service';
            show_hiden_action(key, checked);
            if ( checked ) {
                $('.cmb2-id-admin-notice-expiring-service-days').show();
            } else {
                $('.cmb2-id-admin-notice-expiring-service-days').hide();
            }

            // freelancer expiring
            $('#freelancer_notice_expiring_service').on('change', function(){
                var key = 'freelancer-notice-expiring-service';
                var checked = $(this).is(":checked");
                show_hiden_action(key, checked);

                if ( checked ) {
                    $('.cmb2-id-freelancer-notice-expiring-service-days').show();
                } else {
                    $('.cmb2-id-freelancer-notice-expiring-service-days').hide();
                }
            });
            var checked = $('#freelancer_notice_expiring_service').is(":checked");
            var key = 'freelancer-notice-expiring-service';
            show_hiden_action(key, checked);
            if ( checked ) {
                $('.cmb2-id-freelancer-notice-expiring-service-days').show();
            } else {
                $('.cmb2-id-freelancer-notice-expiring-service-days').hide();
            }

            $('#freelancer_notice_add_hired_service').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('hired-service-notice', checked);
            });
            var checked = $('#freelancer_notice_add_hired_service').is(":checked");
            show_hiden_action('hired-service-notice', checked);

            $('#employer_notice_add_hired_service').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('hired-service-employer-notice', checked);
            });
            var checked = $('#employer_notice_add_hired_service').is(":checked");
            show_hiden_action('hired-service-employer-notice', checked);

            $('#freelancer_notice_add_completed_service').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('completed-service-notice', checked);
            });
            var checked = $('#freelancer_notice_add_completed_service').is(":checked");
            show_hiden_action('completed-service-notice', checked);

            $('#employer_notice_add_completed_service').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('completed-service-employer-notice', checked);
            });
            var checked = $('#employer_notice_add_completed_service').is(":checked");
            show_hiden_action('completed-service-employer-notice', checked);

            $('#freelancer_notice_add_cancelled_service').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('cancelled-service-notice', checked);
            });
            var checked = $('#freelancer_notice_add_cancelled_service').is(":checked");
            show_hiden_action('cancelled-service-notice', checked);

            $('#employer_notice_add_cancelled_service').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('cancelled-service-employer-notice', checked);
            });
            var checked = $('#employer_notice_add_cancelled_service').is(":checked");
            show_hiden_action('cancelled-service-employer-notice', checked);

            $('#user_notice_hired_service_message').on('change', function(){
                var checked = $(this).is(":checked");
                show_hiden_action('hired-service-message-notice', checked);
            });
            var checked = $('#user_notice_hired_service_message').is(":checked");
            show_hiden_action('hired-service-message-notice', checked);






            $('.before-group-row-inner .cmb-type-wp-freeio-title').on('click', function(){
                var parent = $(this).closest('.before-group-row');
                parent.find('.before-group-row-inner-content').slideToggle();
                if ( $(this).hasClass('show-content') ) {
                    $(this).removeClass('show-content');
                } else {
                    $(this).addClass('show-content');
                }
            });

            // emloyer project
            var commission_fee = $('#emloyers_project_commission_fee').val();
            if ( commission_fee == 'comissions_tiers' ) {
                $('.cmb2-id-emloyers-project-comissions-tiers').show();
            } else {
                $('.cmb2-id-emloyers-project-comissions-tiers').hide();
            }
            $('#emloyers_project_commission_fee').on('change', function() {
                var commission_fee = $(this).val();
                if ( commission_fee == 'comissions_tiers' ) {
                    $('.cmb2-id-emloyers-project-comissions-tiers').show();
                } else {
                    $('.cmb2-id-emloyers-project-comissions-tiers').hide();
                }
            });
            // emloyer service
            var commission_fee = $('#emloyers_service_commission_fee').val();
            if ( commission_fee == 'comissions_tiers' ) {
                $('.cmb2-id-emloyers-service-comissions-tiers').show();
            } else {
                $('.cmb2-id-emloyers-service-comissions-tiers').hide();
            }
            $('#emloyers_service_commission_fee').on('change', function() {
                var commission_fee = $(this).val();
                if ( commission_fee == 'comissions_tiers' ) {
                    $('.cmb2-id-emloyers-service-comissions-tiers').show();
                } else {
                    $('.cmb2-id-emloyers-service-comissions-tiers').hide();
                }
            });

            // freelancer project
            var commission_fee = $('#freelancers_project_commission_fee').val();
            if ( commission_fee == 'comissions_tiers' ) {
                $('.cmb2-id-freelancers-project-comissions-tiers').show();
            } else {
                $('.cmb2-id-freelancers-project-comissions-tiers').hide();
            }
            $('#freelancers_project_commission_fee').on('change', function() {
                var commission_fee = $(this).val();
                if ( commission_fee == 'comissions_tiers' ) {
                    $('.cmb2-id-freelancers-project-comissions-tiers').show();
                } else {
                    $('.cmb2-id-freelancers-project-comissions-tiers').hide();
                }
            });
            // freelancer service
            var commission_fee = $('#freelancers_service_commission_fee').val();
            if ( commission_fee == 'comissions_tiers' ) {
                $('.cmb2-id-freelancers-service-comissions-tiers').show();
            } else {
                $('.cmb2-id-freelancers-service-comissions-tiers').hide();
            }
            $('#freelancers_service_commission_fee').on('change', function() {
                var commission_fee = $(this).val();
                if ( commission_fee == 'comissions_tiers' ) {
                    $('.cmb2-id-freelancers-service-comissions-tiers').show();
                } else {
                    $('.cmb2-id-freelancers-service-comissions-tiers').hide();
                }
            });
        },
        mixes: function() {
            

            //
            var apply_type = $('#_job_apply_type').val();
            if ( apply_type == 'internal' ) {
                $('.cmb2-id--job-apply-url').hide();
                $('.cmb2-id--job-apply-email').hide();
                $('.cmb2-id--job-phone').hide();
            } else if ( apply_type == 'external' ) {
                $('.cmb2-id--job-apply-url').show();
                $('.cmb2-id--job-apply-email').hide();
                $('.cmb2-id--job-phone').hide();
            } else if ( apply_type == 'with_email' ) {
                $('.cmb2-id--job-apply-url').hide();
                $('.cmb2-id--job-phone').hide();
                $('.cmb2-id--job-apply-email').show();
            } else if ( apply_type == 'call' ) {
                $('.cmb2-id--job-apply-url').hide();
                $('.cmb2-id--job-phone').show();
                $('.cmb2-id--job-apply-email').hide();
            }
            $('#_job_apply_type').change(function(){
                var apply_type = $('#_job_apply_type').val();
                if ( apply_type == 'internal' ) {
                    $('.cmb2-id--job-apply-url').hide();
                    $('.cmb2-id--job-apply-email').hide();
                    $('.cmb2-id--job-phone').hide();
                } else if ( apply_type == 'external' ) {
                    $('.cmb2-id--job-apply-url').show();
                    $('.cmb2-id--job-apply-email').hide();
                    $('.cmb2-id--job-phone').hide();
                } else if ( apply_type == 'with_email' ) {
                    $('.cmb2-id--job-apply-url').hide();
                    $('.cmb2-id--job-apply-email').show();
                    $('.cmb2-id--job-phone').hide();
                } else if ( apply_type == 'call' ) {
                    $('.cmb2-id--job-apply-url').hide();
                    $('.cmb2-id--job-phone').show();
                    $('.cmb2-id--job-apply-email').hide();
                }
            });

            // price type
            var price_type = $('#_service_price_type').val();
            if ( price_type == 'package' ) {
                $('.cmb-row.cmb2-id--service-price').hide();
                $('.cmb-row.cmb2-id--service-price-packages').show();
            } else {
                $('.cmb-row.cmb2-id--service-price').show();
                $('.cmb-row.cmb2-id--service-price-packages').hide();
            }
            $('#_service_price_type').change(function(){
                var price_type = $(this).val();
                if ( price_type == 'package' ) {
                    $('.cmb-row.cmb2-id--service-price').hide();
                    $('.cmb-row.cmb2-id--service-price-packages').show();
                } else {
                    $('.cmb-row.cmb2-id--service-price').show();
                    $('.cmb-row.cmb2-id--service-price-packages').hide();
                }
            });

            // currency
            var enable_mutil_currencies = $('#enable_multi_currencies').val();
            if ( enable_mutil_currencies == 'yes' ) {
                $('.cmb2-id-multi-currencies').show();
                $('.cmb2-id-exchangerate-api-key').show();
            } else {
                $('.cmb2-id-multi-currencies').hide();
                $('.cmb2-id-exchangerate-api-key').hide();
            }

            $('#enable_multi_currencies').on('change', function() {
                var enable_mutil_currencies = $(this).val();
                if ( enable_mutil_currencies == 'yes' ) {
                    $('.cmb2-id-multi-currencies').show();
                    $('.cmb2-id-exchangerate-api-key').show();
                } else {
                    $('.cmb2-id-multi-currencies').hide();
                    $('.cmb2-id-exchangerate-api-key').hide();
                }
            });
        }
    }

    $.wpfiAdminMainCore = WJBPAdminMainCore.prototype;
    
    $(document).ready(function() {
        // Initialize script
        new WJBPAdminMainCore();
    });
    
})(jQuery);

