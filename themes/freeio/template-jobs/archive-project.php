<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wp_query;

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

$params = array();
$taxs = ['category', 'location', 'tag', 'experience', 'freelancer_type'];
foreach ($taxs as $tax) {
	if ( is_tax('project_'.$tax) ) {
		$term = $wp_query->queried_object;
		if ( isset( $term->term_id) ) {
			$params['filter-'.$tax] = $term->term_id;
		}
	}
}
// no prefix
$taxs = ['project_skill', 'project_level', 'project_language', 'project_duration', 'project_experience'];
foreach ($taxs as $tax) {
	if ( is_tax($tax) ) {
		$term = $wp_query->queried_object;
		if ( isset( $term->term_id) ) {
			$params['filter-'.$tax] = $term->term_id;
		}
	}
}

if ( WP_Freeio_Project_Filter::has_filter() ) {
	$params = array_merge($params, $_GET);
}

$projects = WP_Freeio_Query::get_posts($query_args, $params);

if ( isset( $_REQUEST['load_type'] ) && WP_Freeio_Mixes::is_ajax_request() ) {
	if ( 'items' !== $_REQUEST['load_type'] ) {
        echo WP_Freeio_Template_Loader::get_template_part('archive-project-ajax-full', array('projects' => $projects));
	} else {
		echo WP_Freeio_Template_Loader::get_template_part('archive-project-ajax-projects', array('projects' => $projects));
	}

} else {
	get_header();

	$layout_type = 'default';
	$projects_display_mode = freeio_get_projects_display_mode();
	$project_inner_style = freeio_get_projects_inner_style();

	$args = array(
		'projects' => $projects,
		'project_inner_style' => $project_inner_style,
		'projects_display_mode' => $projects_display_mode,
	);

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

			<div class="layout-project-sidebar main-content <?php echo apply_filters('freeio_project_content_class', 'container');?> inner">
				<?php freeio_before_content( $sidebar_configs ); ?>
				<div class="row">
					<?php freeio_display_sidebar_left( $sidebar_configs ); ?>

					<div id="main-content" class="col-12 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
						<main id="main" class="site-main layout-type-<?php echo esc_attr($layout_type); ?>" role="main">
							<?php
								echo WP_Freeio_Template_Loader::get_template_part('loop/project/archive-inner', $args);

								echo WP_Freeio_Template_Loader::get_template_part('loop/project/pagination', array('projects' => $projects));
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