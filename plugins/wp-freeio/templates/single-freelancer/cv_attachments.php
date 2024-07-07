<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$cv_attachments = WP_Freeio_Freelancer::get_post_meta($post->ID, 'cv_attachment', true );

if ( !empty($cv_attachments) ) {
?>
    <div class="freelancer-detail-cv-attachments">
        
        <a href="<?php echo esc_url($url); ?>"><?php esc_html_e('Download CV', 'wp-freeio'); ?></a>
        
    </div>
<?php }