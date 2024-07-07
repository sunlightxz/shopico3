<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


global $post, $preview_post;
if ( $preview_post ) {
    $post = $preview_post;
}
if ( empty($post->post_type) || $post->post_type !== 'project' ) {
    return;
}
extract( $args );

extract( $args );
extract( $instance );


echo trim($before_widget);
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo trim($before_title)  . trim( $title ) . $after_title;
}

?>
    <div class="project-widget-price">
        
        <div class="project-price">
            <?php echo WP_Freeio_Project::get_price_html($post->ID); ?>
        </div>
        
        <button type="submit" class="btn btn-theme btn-inverse w-100 submit-a-proposal-btn"><?php esc_html_e('Submit a Proposal', 'freeio'); ?> <i class="flaticon-right-up next"></i></button>
        
    </div>
<?php

echo trim($after_widget);