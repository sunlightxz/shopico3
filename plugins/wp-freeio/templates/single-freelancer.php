<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main content" role="main">
			<?php if ( have_posts() ) : ?>
				<?php
				while ( have_posts() ) : the_post();
					global $post;
					if ( !WP_Freeio_Freelancer::check_view_freelancer_detail() ) {
						?>
						<div class="restrict-wrapper">
							<!-- list cv package -->
							<?php
								$restrict_detail = wp_freeio_get_option('freelancer_restrict_detail', 'all');
								switch ($restrict_detail) {
									case 'register_user':
										?>
										<h2 class="restrict-title"><?php echo __( 'The page is restricted only for register user.', 'wp-freeio' ); ?></h2>
										<div class="restrict-content"><?php echo __( 'You need login to view this page', 'wp-freeio' ); ?></div>
										<?php
										break;
									case 'only_applicants':
										?>
										<h2 class="restrict-title"><?php echo __( 'The page is restricted only for employers view his applicants.', 'wp-freeio' ); ?></h2>
										<?php
										break;
									case 'register_employer':
										?>
										<h2 class="restrict-title"><?php echo __( 'Please login as employer to view freelancer.', 'wp-freeio' ); ?></h2>
										<?php
										break;
									default:
										$content = apply_filters('wp-freeio-restrict-freelancer-detail-information', '', $post);
										echo trim($content);
										break;
								}
							?>
						</div><!-- /.alert -->

						<?php
					} else {
						echo WP_Freeio_Template_Loader::get_template_part( 'content-single-freelancer' );
					}
				endwhile;
				?>

				<?php the_posts_pagination( array(
					'prev_text'          => __( 'Previous page', 'wp-freeio' ),
					'next_text'          => __( 'Next page', 'wp-freeio' ),
					'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'wp-freeio' ) . ' </span>',
				) ); ?>
			<?php else : ?>
				<?php get_template_part( 'content', 'none' ); ?>
			<?php endif; ?>
		</main><!-- .site-main -->
	</section><!-- .content-area -->

<?php get_footer(); ?>
