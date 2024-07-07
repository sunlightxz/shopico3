<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
?>

<?php do_action( 'wp_freeio_before_service_detail', $post->ID ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('service-single'); ?>>

	<!-- heading -->
	<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-service/header' ); ?>

	<div class="service-content-area container">
		<!-- Main content -->
		<div class="row content-service-detail">
			<div class="list-content-service col-12 col-lg-<?php echo esc_attr( is_active_sidebar( 'service-single-sidebar' ) ? 8 : 12); ?>">
				<div class="content-main-service">
					<?php do_action( 'wp_freeio_before_service_content', $post->ID ); ?>
					
					
					<?php
					if ( freeio_get_config('show_service_detail', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-service/detail' );
					}
					?>
					
					<?php
					if ( freeio_get_config('show_service_gallery', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-service/gallery' );
					}
					?>

					<!-- service description -->
					<?php
					if ( freeio_get_config('show_service_description', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-service/description' );
					}

					echo WP_Freeio_Template_Loader::get_template_part( 'single-service/attachments' );
					
					?>

					<?php
					if ( freeio_get_config('show_service_video', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-service/video' );
					}
					?>

					<?php
					if ( freeio_get_config('show_service_faq', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-service/faq' );
					}
					?>

					<?php if ( is_active_sidebar( 'service-single-sidebar' ) ): ?>
						<div class="sidebar-wrapper sidebar-service col-lg-4 col-12 d-block d-lg-none">
							<aside class="sidebar sidebar-listing-detail sidebar-right">
						   		<?php dynamic_sidebar( 'service-single-sidebar' ); ?>
						   	</aside>
					   	</div>
				   	<?php endif; ?>

					<?php if ( freeio_check_post_review($post) ) : ?>
						<!-- Review -->
						<?php comments_template(); ?>
					<?php endif; ?>
					
					<?php do_action( 'wp_freeio_after_service_content', $post->ID ); ?>
				</div>
			</div>
			
			<?php if ( is_active_sidebar( 'service-single-sidebar' ) ): ?>
				<div class="sidebar-wrapper sidebar-service col-lg-4 col-12 d-none d-lg-block">
					<aside class="sidebar-listing-detail sidebar sidebar-right sticky-top">
				   		<?php dynamic_sidebar( 'service-single-sidebar' ); ?>
				   	</aside>
			   	</div>
		   	<?php endif; ?>
		   	
		</div>

		<!-- service related -->
        <?php
            if ( freeio_get_config('show_service_related', true) ) {
            	echo WP_Freeio_Template_Loader::get_template_part( 'single-service/related' );
            }
        ?>
	</div>

</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_service_detail', $post->ID ); ?>