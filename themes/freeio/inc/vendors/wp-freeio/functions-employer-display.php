<?php

function freeio_employer_display_logo($post, $link = true, $thumbnail = 'thumbnail', $attr = '') {
	?>
    <div class="employer-logo">
    	<?php if ( $link ) { ?>
        	<a href="<?php echo esc_url( get_permalink($post) ); ?>">
        <?php } ?>
	        	<?php if ( has_post_thumbnail($post->ID) ) { ?>
	                <?php
	                    $image_id = get_post_thumbnail_id($post);
	                    echo freeio_get_attachment_thumbnail($image_id, $thumbnail, false, $attr);
                    ?>
	            <?php } else { ?>
	            	<img src="<?php echo esc_url(freeio_placeholder_img_src()); ?>" alt="<?php echo esc_attr(get_the_title($post->ID)); ?>">
	            <?php } ?>
        <?php if ( $link ) { ?>
        	</a>
        <?php } ?>

        <?php
        $meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);
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

function freeio_employer_name($post) {
    $title = get_the_title($post);
    if ( !WP_Freeio_Employer::check_restrict_view_contact_info($post) && wp_freeio_get_option('restrict_contact_employer_name', 'on') == 'on' ) {
        $title = freeio_hide_string($title);
    }

    return $title;
}

function freeio_employer_display_short_location($post, $display_type = 'no-icon-title', $echo = true) {
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
            ?>
	        <?php if ( $display_type == 'title' ) {
	        	$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);
	        	$title = $meta_obj->get_post_meta_title('location');
	            ?>
	            <div class="employer-location"><h3 class="title"><?php echo trim($title); ?></h3>

			<?php } else { ?>
	            <div class="employer-location">
	        <?php } ?> 
	        	<div class="value">
		        	<?php if ( $display_type == 'icon' ) { ?>
	            		<i class="flaticon-place"></i>
	            	<?php } ?>

		        	<?php $i=1; foreach ($terms as $term) { ?>
		                <?php echo trim($term->name); ?><?php echo esc_html( $i < count($terms) ? ', ' : '' ); ?>
		            <?php $i++; } ?>
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

function freeio_employer_display_full_location($post, $display_type = 'no-icon-title', $echo = true) {
	$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);

	$location = $meta_obj->get_post_meta( 'address' );
	if ( empty($location) ) {
		$location = $meta_obj->get_post_meta( 'map_location_address' );
	}
	ob_start();
	if ( $location ) {
		
		if ( $display_type == 'icon' ) {
			?>
			<div class="employer-location with-icon"><i class="flaticon-place"></i> <?php echo trim($location); ?></div>
			<?php
		} elseif ( $display_type == 'title' ) {
			$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);
        	$title = $meta_obj->get_post_meta_title('location');
			?>
			<div class="employer-location with-title">
				<strong><?php echo trim($title); ?></strong> <span><?php echo trim($location); ?></span>
			</div>
			<?php
		} else {
			?>
			<div class="employer-location"><?php echo trim($location); ?></div>
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

function freeio_employer_display_open_position($post, $show_url = false) {
	$user_id = WP_Freeio_User::get_user_by_employer_id($post->ID);
	$args = array(
	        'post_type' => 'project',
	        'post_per_page' => 1,
	        'post_status' => array('publish', 'hired'),
	        'fields' => 'ids',
	        'author' => $user_id
	    );
	$jobs = WP_Freeio_Query::get_posts($args);
	$count_jobs = $jobs->found_posts;
	
	?>
	<div class="wrapper-open-job">
		<?php if ($show_url) { ?>
			<a href="<?php echo esc_url(get_permalink($post)); ?>" class="open-job">
		<?php } ?>
        <?php echo sprintf(_n('Open Project - <span>%s</span>', 'Open Projects - <span>%s</span>', intval($count_jobs), 'freeio'), intval($count_jobs)); ?>
        <?php if ($show_url) { ?>
			</a>
		<?php } ?>
    </div>
    <?php
}

function freeio_employer_display_nb_jobs($post, $show_url = false) {
	$user_id = WP_Freeio_User::get_user_by_employer_id($post->ID);
	$args = array(
	        'post_type' => 'job_listing',
	        'post_per_page' => 1,
	        'post_status' => 'publish',
	        'fields' => 'ids',
	        'author' => $user_id
	    );
	$jobs = WP_Freeio_Query::get_posts($args);
	$count_jobs = $jobs->found_posts;
	
	?>
	<div class="nb-job">
		<?php if ($show_url) { ?>
			<a href="<?php echo esc_url(get_permalink($post)); ?>">
		<?php } ?>
        <?php echo sprintf(_n('<span class="text">Open Job </span> <span class="space">-</span> <span>%d</span>', '<span class="text">Open Jobs </span> <span class="space">-</span> <span>%d</span>', intval($count_jobs), 'freeio'), intval($count_jobs)); ?>
        <?php if ($show_url) { ?>
			</a>
		<?php } ?>
    </div>
    <?php
}

function freeio_employer_display_nb_projects($post, $show_url = false) {
	$user_id = WP_Freeio_User::get_user_by_employer_id($post->ID);
	$args = array(
	        'post_type' => 'project',
	        'post_per_page' => 1,
	        'post_status' => array('publish', 'hired'),
	        'fields' => 'ids',
	        'author' => $user_id
	    );
	$projects = WP_Freeio_Query::get_posts($args);
	$count_projects = $projects->found_posts;
	
	?>
	<div class="nb-job">
		<?php if ($show_url) { ?>
			<a href="<?php echo esc_url(get_permalink($post)); ?>">
		<?php } ?>
        <?php echo sprintf(_n('<span class="text">Open Project </span> <span class="space">-</span> <span>%d</span>', '<span class="text">Open Projects </span> <span class="space">-</span> <span>%d</span>', intval($count_projects), 'freeio'), intval($count_projects)); ?>
        <?php if ($show_url) { ?>
			</a>
		<?php } ?>
    </div>
    <?php
}

function freeio_employer_display_nb_reviews($post) {
	if ( freeio_check_employer_freelancer_review($post) ) {
		$employer_id = $post->ID;
		$total_reviews = WP_Freeio_Review::get_total_reviews($employer_id);
		$total_reviews_display = $total_reviews ? WP_Freeio_Mixes::format_number($total_reviews) : 0;
		?>
		<div class="nb_reviews">
	        <?php echo sprintf(_n('<span class="text-green">%d</span> <span class="text">Review</span>', '<span class="text-green">%d</span> <span class="text">Reviews</span>', intval($total_reviews), 'freeio'), $total_reviews_display); ?>
	    </div>
	    <?php
	}
}

function freeio_employer_display_rating_reviews($post) {
	if ( freeio_check_post_review($post) ) {
		$employer_id = $post->ID;
		$rating = WP_Freeio_Review::get_ratings_average($post->ID);
		$total_reviews = WP_Freeio_Review::get_total_reviews($employer_id);
		$total_reviews_display = $total_reviews ? WP_Freeio_Mixes::format_number($total_reviews) : 0;
		?>
		<div class="rating-reviews">
			<i class="fa fa-star"></i>
			<span class="rating"><?php echo number_format((float)$rating, 1, '.', ''); ?></span>
	        <?php echo sprintf(_n('<span class="text">%d</span> <span class="text">Review</span>', '<span class="text-green">%d</span> <span class="text">Reviews</span>', intval($total_reviews), 'freeio'), $total_reviews_display); ?>
	    </div>
	    <?php
	}
}


function freeio_employer_display_nb_views($post) {
	$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);

	$views = $meta_obj->get_post_meta( 'views_count' );
	$views_display = $views ? WP_Freeio_Mixes::format_number($views) : 0;
	?>
	<div class="nb_views">
        <?php echo sprintf(_n('<span class="text-blue">%d</span> <span class="text">View</span>', '<span class="text-blue">%d</span> <span class="text">Views</span>', intval($views), 'freeio'), $views_display); ?>
    </div>
    <?php
}

function freeio_employer_display_featured_icon($post, $display_type = 'icon') {
	$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);

	$featured = $meta_obj->get_post_meta( 'featured' );
	if ( $featured ) {
		if ( $display_type == 'icon' ) {
			?>
	        <span class="featured" data-toggle="tooltip" title="<?php esc_attr_e('featured', 'freeio'); ?>"><i class="flaticon-tick"></i></span>
		    <?php
    	} else {
    		?>
    		<span class="featured-text"><?php esc_html_e('Featured', 'freeio'); ?></span>
    		<?php
    	}
	}
}

function freeio_employer_display_phone($post, $icon = 'fa fa-phone', $title = false, $echo = true) {
	$phone = WP_Freeio_Employer::get_display_phone( $post );
	ob_start();
	if ( $phone ) {
		?>
		<div class="job-phone">
			<?php if ( $title ) {
				$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);
				$title = $meta_obj->get_post_meta_title('phone');
			?>
				<h3 class="title"><?php echo trim($title); ?>:</h3>
			<?php } ?>
			<div class="value">
				<?php freeio_display_phone($phone, $icon); ?>
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


function freeio_employer_display_tagline($post) {
	$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);
	$tagline = $meta_obj->get_post_meta( 'tagline' );
	if ( $tagline ) { ?>
		<div class="tagline"><?php echo trim($tagline); ?></div>
	<?php	
	}
}

function freeio_employer_display_email($post, $display_type = 'icon', $echo = true) {
	$email = WP_Freeio_Employer::get_display_email( $post );
	ob_start();
	if ( $email ) {
		?>
		<div class="job-email">
			<?php if ( $display_type == 'title' ) {
				$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);
				$title = $meta_obj->get_post_meta_title('email');
			?>
				<h3 class="title"><?php echo trim($title); ?>:</h3>
			<?php } ?>
			<div class="value">
				<?php if ( $display_type == 'icon' ) { ?>
					<i class="flaticon-mail"></i>
				<?php } ?>
				<?php echo trim($email); ?>
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

function freeio_employer_display_category($post, $display_type = 'no-icon', $echo = true) {
	ob_start();
	$categories = get_the_terms( $post->ID, 'employer_category' );
	if ( $categories ) {
		?>
		<?php if($display_type == "title"){
			$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);
			$title = $meta_obj->get_post_meta_title('category');
		?> 
			<div class="job-category"><h3 class="title"><?php echo trim($title); ?>:</h3>
		<?php } else { ?>
			<div class="job-category">
    	<?php } ?>
    		<div class="value">
    			<?php if($display_type == "icon"){ ?> 
					<i class="flaticon-category"></i>
				<?php } ?> 
	    		<?php
	    		$i=1;
				foreach ($categories as $term) {
					?>
		            	<a class="category-employer" href="<?php echo get_term_link($term); ?>"><?php echo esc_html($term->name); ?></a><?php echo esc_html( $i < count($categories) ? ', ' : '' ); ?>
		        	<?php
		        	$i++;
		    	} ?>
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

function freeio_employer_display_category_first($post, $display_type = 'no-icon') {
	$categories = get_the_terms( $post->ID, 'employer_category' );
	if ( $categories ) {
		?>
		<?php if($display_type == "title"){
			$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);
			$title = $meta_obj->get_post_meta_title('category');
		?> 
			<div class="job-category"><h3 class="title"><?php echo trim($title); ?></h3>
		<?php } else { ?>
			<div class="job-category">
    	<?php } ?>
    		<div class="value">
    			<?php if($display_type == "icon"){ ?> 
					<i class="flaticon-category"></i>
				<?php } ?> 
	    		<?php
				foreach ($categories as $term) {
					?>
		            	<a class="category-employer" href="<?php echo get_term_link($term); ?>"><?php echo esc_html($term->name); ?></a>
		        	<?php
		        	break;
		    	} ?>
	    	</div>
    	</div>
    	<?php
    }
}

function freeio_employer_display_company_size($post, $display_type = 'no-icon', $echo = true) {
	$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);
	ob_start();
	
	$company_size = $meta_obj->get_post_meta( 'company_size' );
	if ( $company_size ) {
		?>
		<?php if($display_type == "title"){
			$title = $meta_obj->get_post_meta_title('company_size');
		?> 
			<div class="company_size"><h3 class="title"><?php echo trim($title); ?></h3>
		<?php } else { ?>
			<div class="company_size">
    	<?php } ?>
    		<div class="value"><?php echo trim($company_size); ?></div>
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

function freeio_employer_display_favorite_btn($post_id) {

	$return = WP_Freeio_Favorite::display_employer_favorite_btn($post_id, array(
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


function freeio_employer_item_map_meta($post) {
	$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);

	$latitude = $meta_obj->get_post_meta( 'map_location_latitude' );
	$longitude = $meta_obj->get_post_meta( 'map_location_longitude' );

	$url = '';
    if ( has_post_thumbnail($post->ID) ) {
        $url = get_the_post_thumbnail_url($post->ID, 'thumbnail');
    }

	echo 'data-latitude="'.esc_attr($latitude).'" data-longitude="'.esc_attr($longitude).'" data-img="'.esc_url($url).'"';
}

function freeio_employer_display_meta($post, $meta_key, $icon = '', $show_title = false, $suffix = '', $echo = false) {
	$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);

	ob_start();
	if ( $meta_obj->check_post_meta_exist($meta_key) && ($value = $meta_obj->get_post_meta( $meta_key )) ) {
		?>
		<div class="employer-meta with-<?php echo esc_attr($show_title ? 'icon-title' : 'icon'); ?>">
			<?php if ( !empty($show_title) ) {
				$title = $meta_obj->get_post_meta_title($meta_key);
			?>
				<h3 class="title">
					<?php echo esc_html($title); ?>:
				</h3>
			<?php } ?>
			<div class="value">
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

function freeio_employer_display_custom_field_meta($post, $meta_key, $icon = '', $show_title = false, $suffix = '', $echo = false) {
	$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);

	ob_start();
	if ( $meta_obj->check_custom_post_meta_exist($meta_key) && ($value = $meta_obj->get_custom_post_meta( $meta_key )) ) {
		?>
		<div class="employer-meta with-<?php echo esc_attr($show_title ? 'icon-title' : 'icon'); ?>">

			<div class="employer-meta">
				<?php if ( !empty($show_title) ) {
					$title = $meta_obj->get_custom_post_meta_title($meta_key);
				?>
					<h3 class="title">
						<?php echo esc_html($title); ?>:
					</h3>
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


// Employer Archive hooks

function freeio_employer_display_filter_btn() {
	$filter_sidebar = 'employers-filter-sidebar';
	$layout_sidebar = freeio_get_employers_layout_sidebar();
    if ( $layout_sidebar == 'main' && freeio_get_employers_show_offcanvas_filter() && is_active_sidebar( $filter_sidebar ) ) {
        ?>
        <div class="filter-in-sidebar-wrapper">
            <span class="filter-in-sidebar"><svg width="14" height="12" viewBox="0 0 14 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
<path d="M3.06254 0.9375L3.06248 9.7035L3.78979 8.97725L4.58529 9.77275L2.50004 11.858L0.414795 9.77275L1.21029 8.97725L1.93748 9.7035L1.93754 0.9375H3.06254ZM8.12504 9.375V10.5H5.31254V9.375H8.12504ZM9.81254 6.5625V7.6875H5.31254V6.5625H9.81254ZM11.5 3.75V4.875H5.31254V3.75H11.5ZM13.1875 0.9375V2.0625H5.31254V0.9375H13.1875Z" fill="currentColor"/>
</svg><span class="text"><?php esc_html_e( 'Filter', 'freeio' ); ?></span></span>
        </div>
        <?php
    }
}

function freeio_employer_display_report_icon($post) {
	?>
	<a data-toggle="tooltip" href="#employer-report-wrapper-<?php echo esc_attr($post->ID); ?>" class="btn-show-popup btn-view-employer-report" title="<?php echo esc_attr_e('Report this employer', 'freeio'); ?>"><i class="fas fa-exclamation-triangle fa-angle-right"></i></a>

	<div id="employer-report-wrapper-<?php echo esc_attr($post->ID); ?>" class="employer-report-wrapper mfp-hide">
		<div class="inner">
			<a href="javascript:void(0);" class="close-magnific-popup ali-right"><i class="ti-close"></i></a>

			<h2 class="widget-title"><span><?php esc_html_e('Report this employer', 'freeio'); ?></span></h2>
			<div class="content">
				<form method="post" action="?" class="report-form-wrapper">
                    <div class="form-group">
                        <input type="text" class="form-control" name="subject" placeholder="<?php esc_attr_e( 'Subject', 'freeio' ); ?>" required="required">
                    </div><!-- /.form-group -->

		            <div class="form-group">
		                <textarea class="form-control" name="message" placeholder="<?php esc_attr_e( 'Message', 'freeio' ); ?>" required="required"></textarea>
		            </div><!-- /.form-group -->

		            <?php if ( WP_Freeio_Recaptcha::is_recaptcha_enabled() ) { ?>
		                <div id="recaptcha-report-employer-form" class="ga-recaptcha" data-sitekey="<?php echo esc_attr(wp_freeio_get_option( 'recaptcha_site_key' )); ?>"></div>
		            <?php } ?>

		            <input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>">
		            <button class="button btn btn-theme btn-outline w-100" name="submit-report"><?php echo esc_html__( 'Send Report', 'freeio' ); ?><i class="flaticon-right-up next"></i></button>
		        </form>
			</div>
		</div>
	</div>
	<?php
}