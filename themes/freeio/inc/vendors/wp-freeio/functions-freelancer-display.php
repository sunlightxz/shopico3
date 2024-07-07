<?php

function freeio_freelancer_display_logo($post, $link = true, $thumbnail = 'thumbnail', $attr = '') {
	?>
    <div class="freelancer-logo d-flex align-items-center">
        <?php if ( $link ) { ?>
            <a href="<?php echo esc_url( get_permalink($post) ); ?>">
        <?php } ?>
                <?php if ( has_post_thumbnail($post->ID) ) { ?>
                    <?php
                        $image_id = get_post_thumbnail_id($post);
                        echo freeio_get_attachment_thumbnail($image_id, $thumbnail, false, $attr);
                    ?>
                <?php } else { ?>
                    <img src="<?php echo esc_url(freeio_freelancer_placeholder_img_src()); ?>" alt="<?php echo esc_attr(get_the_title($post->ID)); ?>">
                <?php } ?>
        <?php if ( $link ) { ?>
            </a>
        <?php } ?>

        <?php
        $meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);
        $verified = $meta_obj->get_post_meta( 'verified' );
        if ( $verified ) {
            ?>
            <span class="verified"><i class="flaticon-tick"></i></span>
            <?php
        }
        ?>
    </div>
    <?php
}

function freeio_freelancer_name($post) {
    $title = get_the_title($post);
    if ( !WP_Freeio_Freelancer::check_restrict_view_contact_info($post) && wp_freeio_get_option('restrict_contact_freelancer_name', 'on') == 'on' ) {
        $title = freeio_hide_string($title);
    }

    return $title;
}

function freeio_freelancer_display_categories($post, $display_type = 'no-icon') {
	$categories = get_the_terms( $post->ID, 'freelancer_category' );
	if ( $categories ) {
		?>
		<div class="freelancer-category">
			<?php if ($display_type == 'icon') { ?>
				<i class="flaticon-category"></i>
			<?php } ?>
            <?php $i=1; foreach ($categories as $term) { ?>
                <a href="<?php echo get_term_link($term); ?>"><?php echo trim($term->name); ?></a><?php echo esc_html( $i < count($categories) ? ', ' : '' ); ?>
            <?php $i++; } ?>
        </div>
		<?php
    }
}

function freeio_freelancer_display_short_location($post, $display_type = 'no-icon-title', $echo = true) {
    $locations = get_the_terms( $post->ID, 'location' );
    ob_start();
    if ( $locations ) {
        $terms = array();
        freeio_locations_walk($locations, 0, $terms);
        if ( empty($terms) ) {
            $terms = $locations;
        } else {
            $terms = array_reverse($terms, true);
        }
        if ( $display_type == 'icon' ) {
            ?>
            <div class="freelancer-location with-icon"><i class="flaticon-place"></i>
            <?php
        } elseif ( $display_type == 'title' ) {
            ?>
            <div class="freelancer-location with-title"><strong><?php esc_html_e('Location:', 'freeio'); ?></strong>
            <?php
        } else {
            ?>
            <div class="freelancer-location">
            <?php
        }

            $i=1; foreach ($terms as $term) { ?>
                <?php echo trim($term->name); ?><?php echo esc_html( $i < count($terms) ? ', ' : '' ); ?>
            <?php $i++; } ?>

        </div>
        <?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
        echo trim($output);
    } else {
        return $output;
    }
}

function freeio_freelancer_display_full_location($post, $display_type = 'no-icon-title', $echo = true) {
	$meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);

    $location = $meta_obj->get_post_meta( 'address' );
    if ( empty($location) ) {
        $location = $meta_obj->get_post_meta( 'map_location_address' );
    }
	ob_start();
	if ( $location ) {
		
		if ( $display_type == 'icon' ) {
			?>
			<div class="freelancer-location with-icon"><i class="flaticon-place"></i> <?php echo trim($location); ?></div>
			<?php
		} elseif ( $display_type == 'title' ) {
			?>
			<div class="freelancer-location with-title">
				<strong><?php esc_html_e('Location:', 'freeio'); ?></strong> <span><?php echo trim($location); ?></span>
			</div>
			<?php
		} else {
			?>
			<div class="freelancer-location"><?php echo trim($location); ?></div>
			<?php
		}
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function freeio_freelancer_display_job_title($post) {
    $meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);

    $job_title = $meta_obj->get_post_meta( 'job_title' );

	if ( $job_title ) { ?>
        <div class="freelancer-job">
            <?php echo trim($job_title); ?>
        </div>
    <?php }
}

function freeio_freelancer_display_featured_icon($post,$display_type = 'text') {
    $meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);

    $featured = $meta_obj->get_post_meta( 'featured' );
	if ( $featured ) { ?>
        
        <?php if($display_type == 'icon') { ?>
            <span class="featured" data-toggle="tooltip" title="<?php esc_attr_e('featured', 'freeio'); ?>"><i class="flaticon-tick"></i></span>
        <?php }else{ ?>
            <span class="featured-text"><?php esc_html_e('Featured', 'freeio'); ?></span>
        <?php } ?>

    <?php }
}

function freeio_freelancer_display_urgent_icon($post) {
    $meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);

    $urgent = $meta_obj->get_post_meta( 'urgent' );
	if ( $urgent ) { ?>
        <span class="urgent"><?php esc_html_e('Urgent', 'freeio'); ?></span>
    <?php }
}

function freeio_freelancer_display_phone($post, $echo = true, $force_show_phone = false) {
	$phone = WP_Freeio_Freelancer::get_display_phone( $post->ID );
	ob_start();
	if ( $phone ) { ?>
        <div class="freelancer-phone">
            <?php freeio_display_phone($phone, '', $force_show_phone); ?>
        </div>
    <?php }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function freeio_freelancer_display_email($post, $show_icon = true, $echo = true) {
	$email = WP_Freeio_Freelancer::get_display_email( $post->ID );
	ob_start();
	if ( $email ) { ?>
        <div class="freelancer-email">
            <?php if ( $show_icon ) { ?>
                <i class="flaticon-envelope"></i>
            <?php } ?>
            <?php echo trim($email); ?>
        </div>
    <?php }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function freeio_freelancer_display_salary($post, $display_type = 'no-icon-title', $echo = true) {
	$salary = WP_Freeio_Freelancer::get_salary_html($post->ID);
	ob_start();
	if ( $salary ) {
		if ( $display_type == 'icon' ) {
			?>
			<div class="freelancer-salary with-icon"><i class="flaticon-income"></i> <?php echo trim($salary); ?></div>
			<?php
		} elseif ( $display_type == 'title' ) {
			?>
			<div class="freelancer-salary with-title">
				<strong><?php esc_html_e('Rate:', 'freeio'); ?></strong> <span><?php echo trim($salary); ?></span>
			</div>
			<?php
		} else {
			?>
			<div class="freelancer-salary"><?php echo trim($salary); ?></div>
			<?php
		}
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function freeio_freelancer_display_birthday($post, $display_type = 'no-icon-title', $echo = true) {
    $meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);

    $birthday = $meta_obj->get_post_meta( 'founded_date' );
	ob_start();
	if ( $birthday ) {
		$birthday = strtotime($birthday);
		$birthday = date_i18n(get_option('date_format'), $birthday);
		if ( $display_type == 'icon' ) {
			?>
			<div class="freelancer-birthday with-icon"><i class="flaticon-30-days"></i> <?php echo trim($birthday); ?></div>
			<?php
		} elseif ( $display_type == 'title' ) {
			?>
			<div class="freelancer-birthday with-title">
				<strong><?php esc_html_e('Birthday:', 'freeio'); ?></strong> <span><?php echo trim($birthday); ?></span>
			</div>
			<?php
		} else {
			?>
			<div class="freelancer-birthday"><?php echo trim($birthday); ?></div>
			<?php
		}
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function freeio_freelancer_display_tags($post, $display_type = 'no-title', $echo = true) {
    $tags = get_the_terms( $post->ID, 'freelancer_tag' );
    ob_start();
    if ( $tags ) {
        ?>
            <?php
            if ( $display_type == 'title' ) {
                ?>
                <div class="freelancer-tags">
                <strong><?php esc_html_e('Tagged as:', 'freeio'); ?></strong>
                <?php
            } else {
                ?>
                <div class="freelancer-tags">
                <?php
            }
                foreach ($tags as $term) {
                    ?>
                        <a class="tag-freelancer" href="<?php echo get_term_link($term); ?>"><?php echo esc_html($term->name); ?></a>
                    <?php
                }
            ?>
            </div>
        <?php
    }

    $output = ob_get_clean();
    if ( $echo ) {
        echo trim($output);
    } else {
        return $output;
    }
}

function freeio_freelancer_display_tags_version2($post, $display_type = 'no-title', $echo = true, $limit = 3) {
    $tags = get_the_terms( $post->ID, 'freelancer_tag' );
    ob_start();
    $i = 1;
    if ( $tags ) {
        ?>
            <?php
            if ( $display_type == 'title' ) {
                ?>
                <div class="freelancer-tags">
                <strong><?php esc_html_e('Tagged as:', 'freeio'); ?></strong>
                <?php
            } else {
                ?>
                <div class="freelancer-tags">
                <?php
            }
                foreach ($tags as $term) {
                    if ( $limit > 0 && $i > $limit ) {
                        break;
                    }
                    ?>
                        <a class="tag-freelancer" href="<?php echo get_term_link($term); ?>"><?php echo esc_html($term->name); ?></a>
                    <?php $i++;
                }
            ?>

            <?php if ( $limit > 0 && count($tags) > $limit ) { ?>
                <span class="count-more-tags"><?php echo sprintf(esc_html__('+%d', 'freeio'), (count($tags) - $limit) ); ?></span>
            <?php } ?>
            
            </div>
        <?php
    }

    $output = ob_get_clean();
    if ( $echo ) {
        echo trim($output);
    } else {
        return $output;
    }
}

function freeio_freelancer_display_rating_reviews($post) {
    if ( freeio_check_post_review($post) ) {
        $freelancer_id = $post->ID;
        $rating = WP_Freeio_Review::get_ratings_average($post->ID);
        $total_reviews = WP_Freeio_Review::get_total_reviews($freelancer_id);
        $total_reviews_display = $total_reviews ? WP_Freeio_Mixes::format_number($total_reviews) : 0;
        ?>
        <div class="rating-reviews">
            <i class="fa fa-star"></i>
            <span class="rating text-link"><?php echo number_format((float)$rating, 1, '.', ''); ?></span> 
            <?php echo sprintf(_n('<span class="text">(%d Review)</span>', '<span class="text">(%d Reviews)</span>', intval($total_reviews), 'freeio'), $total_reviews_display); ?>
        </div>
        <?php
    }
}

function freeio_freelancer_display_favorite_btn($post_id) {

    $return = WP_Freeio_Favorite::display_freelancer_favorite_btn($post_id, array(
        'show_text' => true,
        'tooltip' => true,
        'added_text' => esc_html__('Saved', 'freeio'),
        'add_text' => esc_html__('Save', 'freeio'),
        'added_tooltip_title' => esc_html__('Remove Favorite', 'freeio'),
        'add_tooltip_title' => esc_html__('Add Favorite', 'freeio'),
        'echo' => false,
    ));
    
    return $return;
}

function freeio_freelancer_item_map_meta($post) {
    $meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);

    $latitude = $meta_obj->get_post_meta( 'map_location_latitude' );
    $longitude = $meta_obj->get_post_meta( 'map_location_longitude' );

    $url = '';
    if ( has_post_thumbnail($post->ID) ) {
        $url = get_the_post_thumbnail_url($post->ID, 'thumbnail');
    }
    
    echo 'data-latitude="'.esc_attr($latitude).'" data-longitude="'.esc_attr($longitude).'" data-img="'.esc_url($url).'" data-logo="'.esc_url($url).'"';
}


function freeio_freelancer_display_meta($post, $meta_key, $icon = '', $show_title = false, $suffix = '', $echo = false) {
    $obj_job_meta = WP_Freeio_Freelancer_Meta::get_instance($post->ID);

    $value = $obj_job_meta->get_post_meta( $meta_key );
    
    ob_start();
    if ( $obj_job_meta->check_post_meta_exist($meta_key) && ($value = $obj_job_meta->get_post_meta( $meta_key )) ) {
        ?>
        <div class="freelancer-meta with-<?php echo esc_attr($show_title ? 'icon-title' : 'icon'); ?>">

            <div class="freelancer-meta">
                <?php if ( !empty($show_title) ) {
                    $title = $obj_job_meta->get_post_meta_title($meta_key);
                ?>
                    <span class="title-meta">
                        <?php echo esc_html($title); ?>
                    </span>
                <?php } ?>

                <?php if ( !empty($icon) ) { ?>
                    <i class="<?php echo esc_attr($icon); ?>"></i>
                <?php } ?>
                <?php
                    if ( is_array($value) ) {
                        echo implode(', ', $value);
                    } else {
                        echo esc_html($value);
                    }
                ?>
                <?php echo trim($suffix); ?>
            </div>

        </div>
        <?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
        echo trim($output);
    } else {
        return $output;
    }
}

function freeio_freelancer_display_custom_field_meta($post, $meta_key, $icon = '', $show_title = false, $suffix = '', $echo = false) {
    $obj_job_meta = WP_Freeio_Freelancer_Meta::get_instance($post->ID);

    ob_start();
    if ( $obj_job_meta->check_custom_post_meta_exist($meta_key) && ($value = $obj_job_meta->get_custom_post_meta( $meta_key )) ) {
        ?>
        <div class="freelancer-meta with-<?php echo esc_attr($show_title ? 'icon-title' : 'icon'); ?>">

            <div class="freelancer-meta">
                <?php if ( !empty($show_title) ) {
                    $title = $obj_job_meta->get_custom_post_meta_title($meta_key);
                ?>
                    <span class="title-meta">
                        <?php echo esc_html($title); ?>
                    </span>
                <?php } ?>

                <?php if ( !empty($icon) ) { ?>
                    <i class="<?php echo esc_attr($icon); ?>"></i>
                <?php } ?>
                <?php
                    if ( is_array($value) ) {
                        echo implode(', ', $value);
                    } else {
                        echo esc_html($value);
                    }
                ?>
                <?php echo trim($suffix); ?>
            </div>

        </div>
        <?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
        echo trim($output);
    } else {
        return $output;
    }
}


// Freelancer Archive hooks
function freeio_freelancer_display_filter_btn() {
    $filter_sidebar = 'freelancers-filter-sidebar';
    $layout_sidebar = freeio_get_freelancers_layout_sidebar();
    if ( $layout_sidebar == 'main' && freeio_get_freelancers_show_offcanvas_filter() && is_active_sidebar( $filter_sidebar ) ) {
        ?>
        <div class="filter-in-sidebar-wrapper">
            <span class="filter-in-sidebar"><svg width="14" height="12" viewBox="0 0 14 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
<path d="M3.06254 0.9375L3.06248 9.7035L3.78979 8.97725L4.58529 9.77275L2.50004 11.858L0.414795 9.77275L1.21029 8.97725L1.93748 9.7035L1.93754 0.9375H3.06254ZM8.12504 9.375V10.5H5.31254V9.375H8.12504ZM9.81254 6.5625V7.6875H5.31254V6.5625H9.81254ZM11.5 3.75V4.875H5.31254V3.75H11.5ZM13.1875 0.9375V2.0625H5.31254V0.9375H13.1875Z" fill="currentColor"/>
</svg><span class="text"><?php esc_html_e( 'Filter', 'freeio' ); ?></span></span>
        </div>
        <?php
    }
}

function freeio_freelancer_show_invite($post_id) {

    $show = freeio_get_config('show_freelancer_invite_apply_job', 'always');

    if ( $show == 'always' || ($show == 'show_loggedin' && is_user_logged_in() && WP_Freeio_User::is_employer(get_current_user_id()) ) || ($show == 'none-register-employer' && (!is_user_logged_in() || WP_Freeio_User::is_employer(get_current_user_id())) ) ) {
        WP_Freeio_Freelancer::display_invite_btn($post_id);
    }
        
}

function freeio_freelancer_display_report_icon($post) {
    ?>
    <a data-toggle="tooltip" href="#freelancer-report-wrapper-<?php echo esc_attr($post->ID); ?>" class="btn-show-popup btn-view-freelancer-report" title="<?php echo esc_attr_e('Report this freelancer', 'freeio'); ?>"><i class="fas fa-exclamation-triangle fa-angle-right"></i></a>

    <div id="freelancer-report-wrapper-<?php echo esc_attr($post->ID); ?>" class="freelancer-report-wrapper mfp-hide">
        <div class="inner">
            <a href="javascript:void(0);" class="close-magnific-popup ali-right"><i class="ti-close"></i></a>

            <h2 class="widget-title"><span><?php esc_html_e('Report this freelancer', 'freeio'); ?></span></h2>
            <div class="content">
                <form method="post" action="?" class="report-form-wrapper">
                    <div class="form-group">
                        <input type="text" class="form-control" name="subject" placeholder="<?php esc_attr_e( 'Subject', 'freeio' ); ?>" required="required">
                    </div><!-- /.form-group -->

                    <div class="form-group">
                        <textarea class="form-control" name="message" placeholder="<?php esc_attr_e( 'Message', 'freeio' ); ?>" required="required"></textarea>
                    </div><!-- /.form-group -->

                    <?php if ( WP_Freeio_Recaptcha::is_recaptcha_enabled() ) { ?>
                        <div id="recaptcha-report-freelancer-form" class="ga-recaptcha" data-sitekey="<?php echo esc_attr(wp_freeio_get_option( 'recaptcha_site_key' )); ?>"></div>
                    <?php } ?>

                    <input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>">
                    <button class="button btn btn-theme btn-outline w-100" name="submit-report"><?php echo esc_html__( 'Send Report', 'freeio' ); ?><i class="flaticon-right-up next"></i></button>
                </form>
            </div>
        </div>
    </div>
    <?php
}

function freeio_freelancer_get_project_success($post) {
    $user_id = WP_Freeio_User::get_user_by_freelancer_id($post->ID);
    $query_vars = array(
        'post_type'     => 'project_proposal',
        'post_status'   => 'completed',
        'author'        => $user_id,
        'fields'       => 'ids',
        'posts_per_page' => 1
    );
    $proposals = new WP_Query($query_vars);
    return $proposals->found_posts;
}

function freeio_freelancer_get_total_services($post) {
    $user_id = WP_Freeio_User::get_user_by_freelancer_id($post->ID);
    $query_vars = array(
        'post_type'     => 'service',
        'post_status'   => 'publish',
        'author'        => $user_id,
        'fields'       => 'ids',
        'posts_per_page' => 1
    );
    $services = new WP_Query($query_vars);
    return $services->found_posts;
}

function freeio_freelancer_get_completed_service($post) {
    $user_id = WP_Freeio_User::get_user_by_freelancer_id($post->ID);
    
    $query_vars = array(
        'post_type'     => 'service_order',
        'post_status'   => array( 'completed' ),
        'fields'       => 'ids',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key'     => WP_FREEIO_SERVICE_ORDER_PREFIX.'service_author',
                'value'   => intval( $user_id ),
                'compare' => '=',
            ),
        ),
    );
    $service_orders = new WP_Query($query_vars);
    return $service_orders->found_posts;
}

function freeio_freelancer_get_inqueue_service($post) {
    $user_id = WP_Freeio_User::get_user_by_freelancer_id($post->ID);
    
    $query_vars = array(
        'post_type'     => 'service_order',
        'post_status'   => array( 'hired' ),
        'fields'       => 'ids',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key'     => WP_FREEIO_SERVICE_ORDER_PREFIX.'service_author',
                'value'   => intval( $user_id ),
                'compare' => '=',
            ),
        ),
    );
    $service_orders = new WP_Query($query_vars);
    return $service_orders->found_posts;
}