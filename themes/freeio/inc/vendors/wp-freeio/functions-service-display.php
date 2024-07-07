<?php

function freeio_service_display_image($post, $thumbsize = 'freeio-listing-grid', $link = true) {
	?>
    <div class="service-image">
    	<?php if ( $link ) { ?>
        	<a href="<?php echo esc_url( get_permalink($post) ); ?>">
        <?php } ?>
	        	<?php if ( has_post_thumbnail($post->ID) ) {
	        		$image_id = get_post_thumbnail_id($post->ID);
        		?>
	                <?php echo freeio_get_attachment_thumbnail( $image_id, $thumbsize ); ?>
	            <?php } else { ?>
	            	<img src="<?php echo esc_url(freeio_service_placeholder_img_src()); ?>" alt="<?php echo esc_attr(get_the_title($post->ID)); ?>">
	            <?php } ?>
        <?php if ( $link ) { ?>
        	</a>
        <?php } ?>
    </div>
    <?php
}

function freeio_service_display_author($post, $display_type = 'logo') {
	$author_id = freeio_get_service_post_author($post->ID);
	$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($author_id);
	if ( $freelancer_id ) {
		$freelancer_obj = get_post($freelancer_id);
		?>
	        <div class="service-author">
	            <a href="<?php echo esc_url( get_permalink($freelancer_id) ); ?>">
	            	<?php if ($display_type == 'logo') {
	            		freeio_freelancer_display_logo($freelancer_obj, false);
	            	} ?>
	            	<span class="text">
	                	<?php echo freeio_freelancer_name($freelancer_obj); ?>
	                </span>
	            </a>
	        </div>
	    <?php
	}
}

function freeio_service_display_category($post, $display_category = 'no-title') {
	$categories = get_the_terms( $post->ID, 'service_category' );
	if ( $categories ) { $i = 1;
		?>
		<div class="category-service">
			<?php
			if ( $display_category == 'title' ) {
				?>
				<div class="service-category with-title">
					<strong><?php esc_html_e('Job Category:', 'freeio'); ?></strong>
				<?php
			} elseif ($display_category == 'icon') {
				?>
				<div class="service-category with-icon">
					<i class="flaticon-category"></i>
			<?php
			} else {
				?>
				<div class="service-category">
				<?php
			}
				foreach ($categories as $term) {
					$color = get_term_meta( $term->term_id, '_color', true );
					$style = '';
					if ( $color ) {
						$style = 'color: '.$color;
					}
					?>
						<?php if( $i < count($categories) ) { ?>
							<a href="<?php echo get_term_link($term); ?>" style="<?php echo esc_attr($style); ?>"><?php echo esc_html($term->name); ?></a>, 
						<?php }else{ ?>
							<a href="<?php echo get_term_link($term); ?>" style="<?php echo esc_attr($style); ?>"><?php echo esc_html($term->name); ?></a>
						<?php } ?>	
		        	<?php $i++;
		    	}
	    	?>
	    	</div>
	    </div>
    	<?php
    }
}

function freeio_service_display_first_category($post) {
	$categories = get_the_terms( $post->ID, 'service_category' );
	if ( $categories ) { $i = 1; 
		?>
		<div class="category-service">
			<?php foreach ($categories as $term) {
				$color = get_term_meta( $term->term_id, '_color', true );
				$style = '';
				if ( $color ) {
					$style = 'color: '.$color;
				}
				?>
					<?php if($i ==1 ) { ?>
					<a href="<?php echo get_term_link($term); ?>" style="<?php echo esc_attr($style); ?>"><?php echo esc_html($term->name); ?></a>
					<?php } ?>
	        	<?php $i++;
		    } ?>
	    </div>
    	<?php
    }
}


function freeio_service_display_tags($post, $display_type = 'no-title', $echo = true) {
	$tags = get_the_terms( $post->ID, 'service_tag' );
	ob_start();
	if ( $tags ) {
		?>
			<?php
			if ( $display_type == 'title' ) {
				?>
				<div class="service-tags">
				<strong><?php esc_html_e('Tagged as:', 'freeio'); ?></strong>
				<?php
			} else {
				?>
				<div class="service-tags">
				<?php
			}
				foreach ($tags as $term) {
					?>
		            	<a class="tag-service" href="<?php echo get_term_link($term); ?>"><?php echo esc_html($term->name); ?></a>
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

function freeio_service_display_tags_version2($post, $display_type = 'no-title',$echo = true, $limit = 3) {
	$tags = get_the_terms( $post->ID, 'service_tag' );
	ob_start();
	$i = 1;
	if ( $tags ) {
		?>
			<?php
			if ( $display_type == 'title' ) {
				?>
				<div class="service-tags">
				<strong><?php esc_html_e('Tagged as:', 'freeio'); ?></strong>
				<?php
			} else {
				?>
				<div class="service-tags">
				<?php
			}
				foreach ($tags as $term) {
					if ( $limit > 0 && $i > $limit ) {
	                    break;
	                }
					?>
		            	<a class="tag-service" href="<?php echo get_term_link($term); ?>"><?php echo esc_html($term->name); ?></a>
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

function freeio_service_display_short_location($post, $display_type = 'no-icon', $echo = true) {
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
		<div class="service-location">
			<?php if ($display_type == 'icon') { ?>
	            <i class="flaticon-place"></i>
	        <?php } ?>
            <?php $i=1; foreach ($terms as $term) { ?>
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

function freeio_service_display_full_location($post, $display_type = 'no-icon-title') {
	$obj_service_meta = WP_Freeio_Service_Meta::get_instance($post->ID);

	$location = $obj_service_meta->get_post_meta( 'address' );
	if ( empty($location) ) {
		$location = $obj_service_meta->get_post_meta( 'map_location_address' );
	}
	if ( $location ) {
		if ( $display_type == 'icon' ) {
			?>
			<div class="service-location with-icon"><i class="flaticon-place"></i> <?php echo trim($location); ?></div>
			<?php
		} elseif ( $display_type == 'title' ) {
			?>
			<div class="service-location with-title">
				<strong><?php esc_html_e('Location:', 'freeio'); ?></strong> <?php echo trim($location); ?>
			</div>
			<?php
		} else {
			?>
			<div class="service-location"><?php echo trim($location); ?></div>
			<?php
		}
    }
}

function freeio_service_display_price($post, $display_type = 'no-icon-title', $echo = true) {
	$salary = WP_Freeio_Service::get_price_html($post->ID);
	ob_start();
	if ( $salary ) {
		if ( $display_type == 'icon' ) {
			?>
			<div class="service-salary with-icon"><i class="flaticon-money-1"></i> <?php echo trim($salary); ?></div>
			<?php
		} elseif ( $display_type == 'text' ) {
			?>
			<div class="service-salary with-title">
				<span class="text"><?php esc_html_e('Starting at:', 'freeio'); ?></span> <span><?php echo trim($salary); ?></span>
			</div>
			<?php
		} else {
			?>
			<div class="service-salary"><?php echo trim($salary); ?></div>
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


function freeio_service_display_rating_reviews($post) {
	if ( freeio_check_post_review($post) ) {
		$service_id = $post->ID;
		$rating = WP_Freeio_Review::get_ratings_average($post->ID);
		$total_reviews = WP_Freeio_Review::get_total_reviews($service_id);
		$total_reviews_display = $total_reviews ? WP_Freeio_Mixes::format_number($total_reviews) : 0;
		?>
		<div class="rating-reviews">
			<i class="fa fa-star"></i>
			<span class="rating"><?php echo number_format((float)$rating, 1, '.', ''); ?></span>
	        <?php echo sprintf(_n('<span class="text">(%d Review)</span>', '<span class="text">(%d Reviews)</span>', intval($total_reviews), 'freeio'), $total_reviews_display); ?>
	    </div>
	    <?php
	}
}

function freeio_service_display_views($post) {
	$views = get_post_meta($post->ID, '_viewed_count', true);
	$views_display = $views ? WP_Freeio_Mixes::format_number($views) : 0;
	?>
	<div class="views">
		<i class="flaticon-website"></i>
		<?php echo sprintf(_n('<span class="text">%d</span> View', '<span class="text">%d</span> Views', intval($views), 'freeio'), $views_display); ?>
	</div>
    <?php
}

function freeio_service_display_postdate($post, $display_type = 'no-icon-title', $echo = true) {

	ob_start();
	if ( $display_type == 'icon' ) {
		?>
		<div class="service-deadline with-icon"><i class="flaticon-30-days"></i> <?php the_time(get_option('date_format')); ?></div>
		<?php
	} elseif ( $display_type == 'title' ) {
		?>
		<div class="service-deadline with-title">
			<strong><?php esc_html_e('Posted date:', 'freeio'); ?></strong> <?php the_time(get_option('date_format')); ?>
		</div>
		<?php
	} else {
		?>
		<div class="service-deadline"><?php the_time(get_option('date_format')); ?></div>
		<?php
	}
	$output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function freeio_service_display_featured_icon($post, $display = 'icon') {
	$obj_service_meta = WP_Freeio_Service_Meta::get_instance($post->ID);

	$featured = $obj_service_meta->get_post_meta( 'featured' );
	if ( $featured ) { ?>
		<?php if($display == 'icon') {?>
        	<span class="featured" data-toggle="tooltip" title="<?php esc_attr_e('featured', 'freeio'); ?>"><i class="flaticon-tick"></i></span>
        <?php } else { ?>
        	<span class="featured-text"><?php esc_html_e('Featured', 'freeio'); ?></span>
        <?php } ?>
    <?php }
}


function freeio_service_item_map_meta($post) {
	$obj_service_meta = WP_Freeio_Service_Meta::get_instance($post->ID);

	$latitude = $obj_service_meta->get_post_meta( 'map_location_latitude' );
	$longitude = $obj_service_meta->get_post_meta( 'map_location_longitude' );

	$thumbnail_url = '';
	if ( has_post_thumbnail($post->ID) ) {
		$thumbnail_url = get_the_post_thumbnail_url( $post, 'freeio-listing-grid' );
	}

	$author_id = freeio_get_service_post_author($post->ID);
	$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($author_id);

	$logo_url = '';
	if ( has_post_thumbnail($post->ID) ) {
		$logo_url = get_the_post_thumbnail_url($post->ID, 'thumbnail');
	}

	echo 'data-latitude="'.esc_attr($latitude).'" data-longitude="'.esc_attr($longitude).'" data-img="'.esc_url($thumbnail_url).'" data-logo="'.esc_url($logo_url).'"';
}

function freeio_service_display_meta($post, $meta_key, $icon = '', $show_title = false, $suffix = '', $echo = false) {
	$obj_service_meta = WP_Freeio_Service_Meta::get_instance($post->ID);

	ob_start();
	if ( $obj_service_meta->check_post_meta_exist($meta_key) && ($value = $obj_service_meta->get_post_meta( $meta_key )) ) {
		?>
		<div class="service-meta with-<?php echo esc_attr($show_title ? 'icon-title' : 'icon'); ?>">

			<div class="service-meta">

				<?php if ( !empty($show_title) ) {
					$title = $obj_service_meta->get_post_meta_title($meta_key);
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

function freeio_service_display_custom_field_meta($post, $meta_key, $icon = '', $show_title = false, $suffix = '', $echo = false) {
	$obj_service_meta = WP_Freeio_Service_Meta::get_instance($post->ID);

	ob_start();
	if ( $obj_service_meta->check_custom_post_meta_exist($meta_key) && ($value = $obj_service_meta->get_custom_post_meta( $meta_key )) ) {
		?>
		<div class="service-meta with-<?php echo esc_attr($show_title ? 'icon-title' : 'icon'); ?>">

			<div class="service-meta">

				<?php if ( !empty($show_title) ) {
					$title = $obj_service_meta->get_custom_post_meta_title($meta_key);
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

function freeio_service_display_favorite_btn($post_id) {
	
	$return = WP_Freeio_Favorite::display_service_favorite_btn($post_id, array(
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


// Service Archive hooks
function freeio_service_display_filter_btn() {
	$layout_type = freeio_get_services_layout_type();
	$layout_sidebar = freeio_get_services_layout_sidebar();
	$filter_sidebar = 'services-filter-sidebar';
	if ( ($layout_type == 'half-map' || ($layout_type == 'default' && $layout_sidebar == 'main' && freeio_get_services_show_offcanvas_filter())) && is_active_sidebar( $filter_sidebar ) ) {
		?>
		<div class="filter-in-sidebar-wrapper">
			<span class="filter-in-sidebar"><svg width="14" height="12" viewBox="0 0 14 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
<path d="M3.06254 0.9375L3.06248 9.7035L3.78979 8.97725L4.58529 9.77275L2.50004 11.858L0.414795 9.77275L1.21029 8.97725L1.93748 9.7035L1.93754 0.9375H3.06254ZM8.12504 9.375V10.5H5.31254V9.375H8.12504ZM9.81254 6.5625V7.6875H5.31254V6.5625H9.81254ZM11.5 3.75V4.875H5.31254V3.75H11.5ZM13.1875 0.9375V2.0625H5.31254V0.9375H13.1875Z" fill="currentColor"/>
</svg><span class="text"><?php esc_html_e('Filter', 'freeio'); ?></span></span>
		</div>
		<?php
	}
}

function freeio_service_display_list_service_order_messages($return, $service_order_id) {
	$messages = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX . 'messages', true);;
    $messages = !empty($messages) ? $messages : array();
    if ( empty($messages) ) {
    	return '';
    }
    return freeio_display_list_messages($messages, $service_order_id, 'service');
}
add_filter('wp-freeio-get-list-service-order-messages', 'freeio_service_display_list_service_order_messages', 10, 2);


function freeio_service_display_report_icon($post) {
	?>
	<a data-toggle="tooltip" href="#service-report-wrapper-<?php echo esc_attr($post->ID); ?>" class="btn-show-popup btn-view-service-report" title="<?php echo esc_attr_e('Report this service', 'freeio'); ?>"><i class="fas fa-exclamation-triangle fa-angle-right"></i></a>

	<div id="service-report-wrapper-<?php echo esc_attr($post->ID); ?>" class="service-report-wrapper mfp-hide">
		<div class="inner">
			<a href="javascript:void(0);" class="close-magnific-popup ali-right"><i class="ti-close"></i></a>

			<h2 class="widget-title"><span><?php esc_html_e('Report this service', 'freeio'); ?></span></h2>
			<div class="content">
				<form method="post" action="?" class="report-form-wrapper">
                    <div class="form-group">
                        <input type="text" class="form-control" name="subject" placeholder="<?php esc_attr_e( 'Subject', 'freeio' ); ?>" required="required">
                    </div><!-- /.form-group -->

		            <div class="form-group">
		                <textarea class="form-control" name="message" placeholder="<?php esc_attr_e( 'Message', 'freeio' ); ?>" required="required"></textarea>
		            </div><!-- /.form-group -->

		            <?php if ( WP_Freeio_Recaptcha::is_recaptcha_enabled() ) { ?>
		                <div id="recaptcha-report-service-form" class="ga-recaptcha" data-sitekey="<?php echo esc_attr(wp_freeio_get_option( 'recaptcha_site_key' )); ?>"></div>
		            <?php } ?>

		            <input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>">
		            <button class="button btn btn-theme btn-outline w-100" name="submit-report"><?php echo esc_html__( 'Send Report', 'freeio' ); ?><i class="flaticon-right-up next"></i></button>
		        </form>
			</div>
		</div>
	</div>
	<?php
}