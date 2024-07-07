<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

global $post;
?>

<section id="primary" class="content-area inner">
	<div id="main" class="site-main content" role="main">
		<?php if ( have_posts() ) : ?>
			
			<?php
				do_action( 'freeio_service_detail_content', $post );
			?>

		<?php else : ?>
			<div class="container">
				<?php get_template_part( 'content', 'none' ); ?>
			</div>
		<?php endif; ?>
	</div><!-- .site-main -->
</section><!-- .content-area -->
<?php get_footer(); ?>