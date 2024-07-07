<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$categories = get_the_terms( $post->ID, 'freelancer_category' );
$address = get_post_meta( $post->ID, WP_FREEIO_FREELANCER_PREFIX . 'address', true );
$salary = WP_Freeio_Freelancer::get_salary_html($post->ID);

?>

<?php do_action( 'wp_freeio_before_freelancer_content', $post->ID ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() ) { ?>
        <div class="freelancer-thumbnail">
            <?php echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); ?>
        </div>
    <?php } ?>
    <div class="freelancer-information">
    	
		<?php the_title( sprintf( '<h2 class="entry-title freelancer-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

        <?php if ( $categories ) { ?>
            <?php foreach ($categories as $term) { ?>
                <a href="<?php echo get_term_link($term); ?>"><?php echo $term->name; ?></a>
            <?php } ?>
        <?php } ?>
        <!-- rating -->

        <?php if ( $address ) { ?>
            <div class="freelancer-location">
                <?php esc_html_e('Location', 'wp-freeio'); ?>
                <?php echo $address; ?>
            </div>
        <?php } ?>

        <?php if ( $salary ) { ?>
            <div class="freelancer-salary">
                <?php esc_html_e('Salary', 'wp-freeio'); ?>
                <?php echo $salary; ?>
            </div>
        <?php } ?>
	</div>

    <a href="<?php the_permalink(); ?>" class="btn button"><?php esc_html_e('View Profile', 'wp-freeio'); ?></a>
</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_freelancer_content', $post->ID ); ?>