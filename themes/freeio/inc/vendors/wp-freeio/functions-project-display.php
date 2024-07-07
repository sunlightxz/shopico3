<?php

function freeio_project_display_image($post, $thumbsize = '630x500', $link = true) {
	?>
    <div class="project-image">
    	<?php if ( $link ) { ?>
        	<a href="<?php echo esc_url( get_permalink($post) ); ?>">
        <?php } ?>
	        	<?php if ( has_post_thumbnail($post->ID) ) {
	        		$image_id = get_post_thumbnail_id($post->ID);
        		?>
	                <?php echo freeio_get_attachment_thumbnail( $image_id, $thumbsize ); ?>
	            <?php } else { ?>
	            	<img src="<?php echo esc_url(freeio_project_placeholder_img_src()); ?>" alt="<?php echo esc_attr(get_the_title($post->ID)); ?>">
	            <?php } ?>
        <?php if ( $link ) { ?>
        	</a>
        <?php } ?>
    </div>
    <?php
}

function freeio_project_display_employer_logo($post, $display_type = 'logo') {
	$author_id = freeio_get_project_post_author($post->ID);
	$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
	if ( $employer_id ) {
		$employer_obj = get_post($employer_id);
		?>
            <a href="<?php echo esc_url( get_permalink($employer_id) ); ?>">
            	<?php if ($display_type == 'logo') {
            		freeio_employer_display_logo($employer_obj, false);
            	} ?>
            </a>
	    <?php
	}
}

function freeio_project_display_employer_title($post, $display_type = 'no-icon') {
	$author_id = freeio_get_project_post_author($post->ID);
	$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
	if ( $employer_id ) {
		$employer_obj = get_post($employer_id);
		?>
	        <h3 class="employer-title">
	            <a href="<?php echo esc_url( get_permalink($employer_id) ); ?>">
	            	<?php if ($display_type == 'icon') { ?>
							<i class="ti-home"></i>
					<?php } ?>
	                <?php echo freeio_employer_name($employer_obj); ?>
	            </a>
	        </h3>
	    <?php
	}
}

function freeio_project_display_author($post, $display_type = 'logo') {
	$author_id = freeio_get_project_post_author($post->ID);
	$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
	if ( $employer_id ) {
		$employer_obj = get_post($employer_id);
		?>
	        <div class="project-author">
	            <a href="<?php echo esc_url( get_permalink($employer_id) ); ?>">
	            	<?php if ($display_type == 'logo') {
	            		freeio_employer_display_logo($employer_obj, false);
	            	} ?>
	            	<span class="text"><?php echo freeio_employer_name($employer_obj); ?></span>
	            </a>
	        </div>
	    <?php
	}
}

function freeio_project_display_category($post, $display_category = 'no-title') {
	$categories = get_the_terms( $post->ID, 'project_category' );
	if ( $categories ) { $i = 1;
		?>
			<?php
			if ( $display_category == 'title' ) {
				?>
				<div class="project-category with-title">
					<strong><?php esc_html_e('Job Category:', 'freeio'); ?></strong>
				<?php
			} elseif ($display_category == 'icon') {
				?>
				<div class="project-category with-icon">
					<i class="flaticon-category"></i>
			<?php
			} else {
				?>
				<div class="project-category">
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
    	<?php
    }
}

function freeio_project_display_first_category($post, $display_category = 'no-title') {
	$categories = get_the_terms( $post->ID, 'project_category' );
	if ( $categories ) {
		?>
			<?php
			if ( $display_category == 'title' ) {
				?>
				<div class="project-category with-title">
					<strong><?php esc_html_e('Job Category:', 'freeio'); ?></strong>
				<?php
			} elseif ($display_category == 'icon') {
				?>
				<div class="project-category with-icon">
					<i class="flaticon-category"></i>
			<?php
			} else {
				?>
				<div class="project-category">
				<?php
			}
				foreach ($categories as $term) {
					$color = get_term_meta( $term->term_id, '_color', true );
					$style = '';
					if ( $color ) {
						$style = 'color: '.$color;
					}
					?>
						<a href="<?php echo get_term_link($term); ?>" style="<?php echo esc_attr($style); ?>"><?php echo esc_html($term->name); ?></a>	
		        	<?php break;
		    	}
	    	?>
	    	</div>
    	<?php
    }
}

function freeio_project_display_skills($post, $display_type = 'no-title', $echo = true) {
	$skills = get_the_terms( $post->ID, 'project_skill' );
	ob_start();
	if ( $skills ) {
		?>
			<?php
			if ( $display_type == 'title' ) {
				?>
				<div class="project-skills">
				<strong><?php esc_html_e('Skills:', 'freeio'); ?></strong>
				<?php
			} else {
				?>
				<div class="project-skills project-tags">
				<?php
			}
				foreach ($skills as $term) {
					?>
		            	<a class="skill-project tag-project" href="<?php echo get_term_link($term); ?>"><?php echo esc_html($term->name); ?></a>
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

function freeio_project_display_skills_version2($post, $display_type = 'no-title', $echo = true, $limit = 3) {
	$skills = get_the_terms( $post->ID, 'project_skill' );
	ob_start();
	$i = 1;
	if ( $skills ) {
		?>
			<?php
			if ( $display_type == 'title' ) {
				?>
				<div class="project-skills">
				<strong><?php esc_html_e('Tagged as:', 'freeio'); ?></strong>
				<?php
			} else {
				?>
				<div class="project-skills project-tags">
				<?php
			}
				foreach ($skills as $term) {
					if ( $limit > 0 && $i > $limit ) {
	                    break;
	                }
					?>
		            	<a class="skill-project tag-project" href="<?php echo get_term_link($term); ?>"><?php echo esc_html($term->name); ?></a>
		        	<?php $i++;
		    	} 
	    	?>

	    	<?php if ( $limit > 0 && count($skills) > $limit ) { ?>
	    		<span class="count-more-skills"><?php echo sprintf(esc_html__('+%d', 'freeio'), (count($skills) - $limit) ); ?></span>
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

function freeio_project_display_short_location($post, $display_type = 'no-icon', $echo = true) {
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
		<div class="project-location">
			<?php if ($display_type == 'icon') { ?>
	        <i class="flaticon-place"></i><?php } ?>
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

function freeio_project_display_full_location($post, $display_type = 'no-icon-title') {
	$obj_meta = WP_Freeio_Project_Meta::get_instance($post->ID);

	$location = $obj_meta->get_post_meta( 'address' );
	if ( empty($location) ) {
		$location = $obj_meta->get_post_meta( 'map_location_address' );
	}
	if ( $location ) {
		if ( $display_type == 'icon' ) {
			?>
			<div class="project-location with-icon"><i class="flaticon-place"></i> <?php echo trim($location); ?></div>
			<?php
		} elseif ( $display_type == 'title' ) {
			?>
			<div class="project-location with-title">
				<strong><?php esc_html_e('Location:', 'freeio'); ?></strong> <?php echo trim($location); ?>
			</div>
			<?php
		} else {
			?>
			<div class="project-location"><?php echo trim($location); ?></div>
			<?php
		}
    }
}

function freeio_project_display_tax($post, $tax = 'duration', $echo = true) {
	$terms = get_the_terms( $post->ID, 'project_'.$tax );
	ob_start();
	if ( $terms ) {
		?>
		<div class="project-tax project-<?php echo esc_attr($tax); ?>">
            <?php $i=1; foreach ($terms as $term) { ?>
                <a href="<?php echo get_term_link($term); ?>"><?php echo trim($term->name); ?></a><?php echo esc_html( $i < count($terms) ? ', ' : '' ); ?>
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

// Freelancer Archive hooks
function freeio_project_display_filter_btn() {
    $filter_sidebar = 'projects-filter-sidebar';
    $layout_sidebar = freeio_get_projects_layout_sidebar();
    if ( $layout_sidebar == 'main' && freeio_get_projects_show_offcanvas_filter() && is_active_sidebar( $filter_sidebar ) ) {
        ?>
        <div class="filter-in-sidebar-wrapper">
            <span class="filter-in-sidebar"><svg width="14" height="12" viewBox="0 0 14 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
<path d="M3.06254 0.9375L3.06248 9.7035L3.78979 8.97725L4.58529 9.77275L2.50004 11.858L0.414795 9.77275L1.21029 8.97725L1.93748 9.7035L1.93754 0.9375H3.06254ZM8.12504 9.375V10.5H5.31254V9.375H8.12504ZM9.81254 6.5625V7.6875H5.31254V6.5625H9.81254ZM11.5 3.75V4.875H5.31254V3.75H11.5ZM13.1875 0.9375V2.0625H5.31254V0.9375H13.1875Z" fill="currentColor"/>
</svg><span class="text"><?php esc_html_e( 'Filter', 'freeio' ); ?></span></span>
        </div>
        <?php
    }
}

function freeio_project_display_price($post, $display_type = 'no-icon-title', $echo = true) {
	$price = WP_Freeio_Project::get_price_html($post->ID);
	ob_start();
	if ( $price ) {
		if ( $display_type == 'icon' ) {
			?>
			<div class="project-price with-icon"><i class="flaticon-money"></i> <?php echo trim($price); ?></div>
			<?php
		} elseif ( $display_type == 'text' ) {
			?>
			<div class="project-price with-title">
				<span class="text"><?php esc_html_e('Price:', 'freeio'); ?></span> <span><?php echo trim($price); ?></span>
			</div>
			<?php
		} else {
			?>
			<div class="project-price"><?php echo trim($price); ?></div>
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

function freeio_project_display_project_type($post, $display_type = 'no-icon', $echo = true) {
	$obj_meta = WP_Freeio_Project_Meta::get_instance($post->ID);
	if ( $obj_meta->check_post_meta_exist('project_type') && ($value = $obj_meta->get_post_meta( 'project_type' )) ) {
		ob_start();

		if ( $display_type == 'icon' ) {
			?>
			<div class="project-type with-icon"><i class="flaticon-money"></i> <?php echo trim($value); ?></div>
			<?php
		} else {
			?>
			<div class="project-type"><?php echo trim($value); ?></div>
			<?php
		}

	    $output = ob_get_clean();
	    if ( $echo ) {
	    	echo trim($output);
	    } else {
	    	return $output;
	    }
    }
}

function freeio_project_display_postdate($post, $display_type = 'no-icon-title', $format = 'ago', $echo = true) {

	ob_start();
	if ( $format == 'ago' ) {
		$post_date = sprintf(esc_html__(' Posted %s ago', 'freeio'), human_time_diff(date( 'U', strtotime( $post->post_date ) ), current_time('timestamp')) );
	} else {
		$post_date = date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) );
	}

	if ( $display_type == 'icon' ) {
		?>
		<div class="project-deadline with-icon"><i class="flaticon-30-days"></i> <?php echo trim($post_date); ?></div>
		<?php
	} elseif ( $display_type == 'title' ) {
		?>
		<div class="project-deadline with-title">
			<strong><?php esc_html_e('Posted date:', 'freeio'); ?></strong> <?php echo trim($post_date); ?>
		</div>
		<?php
	} else {
		?>
		<div class="project-deadline"><?php echo trim($post_date); ?></div>
		<?php
	}
	$output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function freeio_project_display_proposals_count($post, $display_type = 'no-icon', $echo = true) {
	ob_start();
	$args = array(
        'post_type' => 'project_proposal',
        'post_status'   => array('publish', 'hired', 'completed', 'cancelled'),
        'posts_per_page'   => 1,
        'meta_query' => array(
            array(
                'key'     => WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id',
                'value'   => intval( $post->ID ),
                'compare' => '=',
            ),
        ),
		'fields' => 'ids'
    );

    $query = new WP_Query( $args );
    $proposals_count = $query->found_posts;
	if ( $display_type == 'icon' ) {
		?>
		<div class="project-proposals-count with-icon"><i class="flaticon-rocket-1"></i> <?php echo sprintf(_n(esc_html__('%d Proposal', 'freeio'), esc_html__('%d Proposals', 'freeio'), $proposals_count), $proposals_count); ?></div>
		<?php
	} else {
		?>
		<div class="project-proposals-count"><?php echo sprintf(_n(esc_html__('%d Proposal', 'freeio'), esc_html__('%d Proposals', 'freeio'), $proposals_count), $proposals_count); ?></div>
		<?php
	}
	$output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function freeio_project_display_list_proposals($return, $project_id) {
	ob_start();
	$args = array(
        'post_type' => 'project_proposal',
        'post_status'   => 'publish',
        'meta_query' => array(
            array(
                'key'     => WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id',
                'value'   => intval( $project_id ),
                'compare' => '=',
            ),
        ),
        'order' => 'DESC',
        'orderby' => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
			'ID'         => 'DESC',
		),
    );

    $query = new WP_Query( $args );
    
    if ( !empty($query) && !empty($query->posts) ) {
    	?>
    	<h3 class="title"><?php esc_html_e('Your Proposals', 'freeio'); ?> (<?php echo trim($query->found_posts); ?>)</h3>
    	<div class="proposals-content">
        	<?php
        	while ( $query->have_posts() ) : $query->the_post();
    		?>
				<div class="item-proposal">
					<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-project/proposal-inner-list' ); ?>
				</div>
			<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<?php
    }
    $return = ob_get_clean();
    return $return;
}
add_filter('wp-freeio-project-list-proposals', 'freeio_project_display_list_proposals', 10, 2);

function freeio_project_display_list_proposal_messages($return, $proposal_id) {
	$messages = get_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'messages', true);;
    $messages = !empty($messages) ? $messages : array();
    if ( empty($messages) ) {
    	return '';
    }
    return freeio_display_list_messages($messages, $proposal_id, 'project');
}
add_filter('wp-freeio-get-list-proposal-messages', 'freeio_project_display_list_proposal_messages', 10, 2);

function freeio_project_display_views($post) {
	$views = get_post_meta($post->ID, '_viewed_count', true);
	$views_display = $views ? WP_Freeio_Mixes::format_number($views) : 0;
	?>
	<div class="views">
		<i class="flaticon-website"></i>
		<?php echo sprintf(_n('<span class="text">%d</span> View', '<span class="text">%d</span> Views', intval($views), 'freeio'), $views_display); ?>
	</div>
    <?php
}

function freeio_project_display_featured_icon($post, $display = 'icon') {
	$obj_meta = WP_Freeio_Project_Meta::get_instance($post->ID);

	$featured = $obj_meta->get_post_meta( 'featured' );
	if ( $featured ) { ?>
		<?php if($display == 'icon') {?>
        	<span class="featured" data-toggle="tooltip" title="<?php esc_attr_e('featured', 'freeio'); ?>"><i class="flaticon-tick"></i></span>
        <?php } else { ?>
        	<span class="featured-text"><?php esc_html_e('Featured', 'freeio'); ?></span>
        <?php } ?>
    <?php }
}


function freeio_project_item_map_meta($post) {
	$obj_meta = WP_Freeio_Project_Meta::get_instance($post->ID);

	$latitude = $obj_meta->get_post_meta( 'map_location_latitude' );
	$longitude = $obj_meta->get_post_meta( 'map_location_longitude' );

	$author_id = freeio_get_project_post_author($post->ID);
	$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($author_id);

	$url = '';
	if ( has_post_thumbnail($post->ID) ) {
		$url = get_the_post_thumbnail_url($post->ID, 'thumbnail');
	}

	echo 'data-latitude="'.esc_attr($latitude).'" data-longitude="'.esc_attr($longitude).'" data-img="'.esc_url($url).'"';
}

function freeio_project_display_meta($post, $meta_key, $icon = '', $show_title = false, $suffix = '', $echo = true) {
	$obj_meta = WP_Freeio_Project_Meta::get_instance($post->ID);

	ob_start();
	if ( $obj_meta->check_post_meta_exist($meta_key) && ($value = $obj_meta->get_post_meta( $meta_key )) ) {
		?>
		<div class="project-meta with-<?php echo esc_attr($show_title ? 'icon-title' : 'icon'); ?>">

			<div class="project-meta">

				<?php if ( !empty($show_title) ) {
					$title = $obj_meta->get_post_meta_title($meta_key);
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

function freeio_project_display_custom_field_meta($post, $meta_key, $icon = '', $show_title = false, $suffix = '', $echo = true) {
	$obj_meta = WP_Freeio_Project_Meta::get_instance($post->ID);

	ob_start();
	if ( $obj_meta->check_custom_post_meta_exist($meta_key) && ($value = $obj_meta->get_custom_post_meta( $meta_key )) ) {
		?>
		<div class="project-meta with-<?php echo esc_attr($show_title ? 'icon-title' : 'icon'); ?>">

			<div class="project-meta">

				<?php if ( !empty($show_title) ) {
					$title = $obj_meta->get_custom_post_meta_title($meta_key);
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

function freeio_project_display_favorite_btn($post_id) {
	
	$return = WP_Freeio_Favorite::display_project_favorite_btn($post_id, array(
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

function freeio_project_display_report_icon($post) {
	?>
	<a data-toggle="tooltip" href="#project-report-wrapper-<?php echo esc_attr($post->ID); ?>" class="btn-show-popup btn-view-project-report" title="<?php echo esc_attr_e('Report this project', 'freeio'); ?>"><i class="fas fa-exclamation-triangle fa-angle-right"></i></a>

	<div id="project-report-wrapper-<?php echo esc_attr($post->ID); ?>" class="project-report-wrapper mfp-hide">
		<div class="inner">
			<a href="javascript:void(0);" class="close-magnific-popup ali-right"><i class="ti-close"></i></a>

			<h2 class="widget-title"><span><?php esc_html_e('Report this project', 'freeio'); ?></span></h2>
			<div class="content">
				<form method="post" action="?" class="report-form-wrapper">
                    <div class="form-group">
                        <input type="text" class="form-control" name="subject" placeholder="<?php esc_attr_e( 'Subject', 'freeio' ); ?>" required="required">
                    </div><!-- /.form-group -->

		            <div class="form-group">
		                <textarea class="form-control" name="message" placeholder="<?php esc_attr_e( 'Message', 'freeio' ); ?>" required="required"></textarea>
		            </div><!-- /.form-group -->

		            <?php if ( WP_Freeio_Recaptcha::is_recaptcha_enabled() ) { ?>
		                <div id="recaptcha-report-project-form" class="ga-recaptcha" data-sitekey="<?php echo esc_attr(wp_freeio_get_option( 'recaptcha_site_key' )); ?>"></div>
		            <?php } ?>

		            <input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>">
		            <button class="button btn btn-theme btn-outline w-100" name="submit-report"><?php echo esc_html__( 'Send Report', 'freeio' ); ?><i class="flaticon-right-up next"></i></button>
		        </form>
			</div>
		</div>
	</div>
	<?php
}