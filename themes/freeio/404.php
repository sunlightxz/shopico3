<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage Freeio
 * @since Freeio 1.0
 */
/*
*Template Name: 404 Page
*/
get_header();

$icon_url = freeio_get_config('404_icon_img');
$bg_img = freeio_get_config('404_bg_img');

?>
<section class="page-404 justify-content-center flex-middle">
	<div id="main-container" class="inner">
		<div id="main-content" class="main-page">
			<section class="error-404 not-found clearfix">
				<div class="container">
					<div class="content-inner d-md-flex align-items-center">
						<div class="top-image col-12 col-md-7">
							<?php if( !empty($bg_img) ) { ?>
								<img src="<?php echo esc_url( $bg_img); ?>" alt="<?php bloginfo( 'name' ); ?>">
							<?php }else{ ?>
								<img src="<?php echo esc_url( get_template_directory_uri().'/images/error.jpg'); ?>" alt="<?php bloginfo( 'name' ); ?>">
							<?php } ?>
						</div>
						<div class="col-12 col-md-5 inner-right">
							<div class="img-icon">
								<?php if( !empty($icon_url) ) { ?>
									<img src="<?php echo esc_url( $icon_url); ?>" alt="<?php bloginfo( 'name' ); ?>">
								<?php }else{ ?>
									<img src="<?php echo esc_url( get_template_directory_uri().'/images/icon-error.png'); ?>" alt="<?php bloginfo( 'name' ); ?>">
								<?php } ?>
							</div>
							<h4 class="title-big">
								<?php
								$title = freeio_get_config('404_title');
								if ( !empty($title) ) {
									echo esc_html($title);
								} else {
									esc_html_e('Oh! Page Not Found', 'freeio');
								}
								?>
							</h4>
							<div class="description">
								<?php
								$description = freeio_get_config('404_description');
								if ( !empty($description) ) {
									echo esc_html($description);
								} else {
									esc_html_e('We can’t seem to find the page you’re looking for', 'freeio');
								}
								?>
							</div>
							<div class="return">
								<a class="btn btn-theme btn-inverse" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e('Back to Home','freeio') ?><i class="flaticon-right-up next"></i></a>
							</div>
						</div>
					</div>
				</div>
			</section><!-- .error-404 -->
		</div><!-- .content-area -->
	</div>
</section>
<?php get_footer(); ?>