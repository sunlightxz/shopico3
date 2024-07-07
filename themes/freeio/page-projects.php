<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Superio
 * @since Superio 1.0
 */
/*
*Template Name: Projects Template
*/

if ( isset( $_REQUEST['load_type'] ) && WP_Freeio_Mixes::is_ajax_request() ) {
	if ( get_query_var( 'paged' ) ) {
	    $paged = get_query_var( 'paged' );
	} elseif ( get_query_var( 'page' ) ) {
	    $paged = get_query_var( 'page' );
	} else {
	    $paged = 1;
	}

	$query_args = array(
		'post_type' => 'project',
	    'post_status' => 'publish',
	    'post_per_page' => wp_freeio_get_option('number_projects_per_page', 10),
	    'paged' => $paged,
	);

	global $wp_query;
	$atts = array();
	if ( !empty($wp_query->post->post_content) ) {
		$shortcode_atts = freeio_get_shortcode_atts($wp_query->post->post_content, 'wp_freeio_projects');
		if ( !empty($shortcode_atts[0]) ) {
			foreach ($shortcode_atts[0] as $key => $value) {
				$atts[$key] = trim($value, '"');
			}
			
		}
	}

	$params = array();
	if (WP_Freeio_Abstract_Filter::has_filter($atts)) {
		$params = $atts;
	}
	if ( WP_Freeio_Project_Filter::has_filter() ) {
		$params = array_merge($params, $_GET);
	}

	$projects = WP_Freeio_Query::get_posts($query_args, $params);
	
	if ( 'items' !== $_REQUEST['load_type'] ) {
		echo WP_Freeio_Template_Loader::get_template_part('archive-project-ajax-full', array('projects' => $projects));
	} else {
		echo WP_Freeio_Template_Loader::get_template_part('archive-project-ajax-projects', array('projects' => $projects));
	}
} else {
	get_header();

	$layout_type = 'default';

	$sidebar_configs = freeio_get_projects_layout_configs();

	$top_content = freeio_get_projects_show_top_content();

	$bg_color = get_post_meta( $post->ID, 'apus_page_color', true );
	if(!empty($bg_color)){
		$bg_color = 'style = background-color:'.$bg_color;
	}
	$layout_sidebar = freeio_get_projects_layout_sidebar();
	$filter_sidebar = 'projects-filter-sidebar';
	?>
		<section id="main-container" class="page-project-board inner layout-type-<?php echo esc_attr($layout_type); ?> <?php echo esc_attr($top_content ? 'has-filter-top':''); ?>" <?php echo esc_attr($bg_color); ?>>
			

			<?php freeio_render_breadcrumbs_simple(); ?>

			<?php if ( $top_content ) { ?>
				<div class="services-top-content-wrapper">
			   		<?php freeio_display_top_content( $top_content ); ?>
			   	</div>
			<?php } ?>

			<?php if ( $layout_sidebar == 'main' && is_active_sidebar( $filter_sidebar ) && freeio_get_projects_show_offcanvas_filter() ) { ?>
			   	<div class="filter-sidebar offcanvas-filter-sidebar">
			   		<div class="offcanvas-filter-sidebar-header d-flex align-items-center">
				        <div class="title"><?php echo esc_html__('All Filters','freeio'); ?></div>
				        <span class="close-filter-sidebar ms-auto d-flex align-items-center justify-content-center"><i class="ti-close"></i></span>
				    </div>
					<div class="filter-scroll">
			   			<?php dynamic_sidebar( $filter_sidebar ); ?>
			   		</div>
		   		</div>
	   			<div class="over-dark"></div>
			<?php } ?>

			<div class="layout-project-sidebar main-content <?php echo apply_filters('freeio_page_content_class', 'container');?> inner">

				<?php freeio_before_content( $sidebar_configs ); ?>
				<div class="row">
					<?php freeio_display_sidebar_left( $sidebar_configs ); ?>

					<div id="main-content" class="col-12 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
						<main id="main" class="site-main layout-type-<?php echo esc_attr($layout_type); ?>" role="main">

							<?php
							// Start the loop.
							while ( have_posts() ) : the_post();
								
								// Include the page content template.
								the_content();

								// If comments are open or we have at least one comment, load up the comment template.
								if ( comments_open() || get_comments_number() ) :
									comments_template();
								endif;

							// End the loop.
							endwhile;
							?>

						</main><!-- .site-main -->
					</div><!-- .content-area -->
					
					<?php freeio_display_sidebar_right( $sidebar_configs ); ?>
				</div>
			</div>
		</section>
	<?php

	get_footer();
}