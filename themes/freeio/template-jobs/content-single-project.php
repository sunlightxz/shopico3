<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
?>

<?php do_action( 'wp_freeio_before_project_detail', $post->ID ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('project-single'); ?>>

	<!-- heading -->
	<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-project/header' ); ?>

	<div class="project-content-area container">
		<!-- Main content -->
		<div class="row content-project-detail content-service-detail">
			<div class="list-content-project list-content-service col-12 col-lg-<?php echo esc_attr( is_active_sidebar( 'project-single-sidebar' ) ? 8 : 12); ?>">
				<div class="content-main-service content-main-project">
					<?php do_action( 'wp_freeio_before_project_content', $post->ID ); ?>
					
					<?php
					if ( freeio_get_config('show_project_detail', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-project/detail' );
					}
					?>

					<!-- project description -->
					<?php
					if ( freeio_get_config('show_project_description', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-project/description' );
					}
					?>

					<?php
					if ( freeio_get_config('show_project_attachments', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-project/attachments' );
					}
					?>

					<?php
					if ( freeio_get_config('show_project_skills', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-project/skills' );
					}
					?>

					<?php if ( is_active_sidebar( 'project-single-sidebar' ) ): ?>
						<div class="sidebar-wrapper sidebar-service col-lg-4 col-12 d-block d-lg-none">
							<aside class="sidebar sidebar-listing-detail sidebar-right">
						   		<?php dynamic_sidebar( 'project-single-sidebar' ); ?>
						   	</aside>
					   	</div>
				   	<?php endif; ?>

					<?php
					if ( freeio_get_config('show_project_proposals', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-project/proposals' );
					}
					?>
					
					<?php
					if ( freeio_get_config('show_project_faq', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-project/faq' );
					}
					?>

					<!-- project related -->
			        <?php
			            if ( freeio_get_config('project_related_show', true) ) {
			            	echo WP_Freeio_Template_Loader::get_template_part( 'single-project/related' );
			            }
			        ?>

					<?php do_action( 'wp_freeio_after_project_content', $post->ID ); ?>
				</div>
			</div>
			
			<?php if ( is_active_sidebar( 'project-single-sidebar' ) ): ?>
				<div class="sidebar-wrapper sidebar-service col-lg-4 col-12 d-none d-lg-block">
					<aside class="sidebar sidebar-listing-detail sidebar-right sticky-top">
				   		<?php dynamic_sidebar( 'project-single-sidebar' ); ?>
				   	</aside>
			   	</div>
		   	<?php endif; ?>
		   	
		</div>
	</div>

</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_project_detail', $post->ID ); ?>