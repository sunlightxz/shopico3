<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$meta_obj = WP_Freeio_Project_Meta::get_instance($post->ID);

if ( $meta_obj->check_post_meta_exist('faq') && ($faq = $meta_obj->get_post_meta('faq')) ) {
    ?>
    <div id="project-detail-faq" class="project-detail-faq">
        <h3 class="title"><?php echo trim($meta_obj->get_post_meta_title('faq')); ?></h3>
        <div class="inner">
            <div class="panel-group" id="project-faq-accordion" role="tablist" aria-multiselectable="true">
                <?php $i = 0; foreach ($faq as $key => $item) {
                    if ( !empty($item['question']) && !empty($item['answer']) ) {
                    ?>
                        <div class="accordion-item accordion-item-faq">
                            <h4 class="accordion-header">
                                <a class="accordion-button <?php echo esc_attr($i !== 0 ? 'collapsed' : ''); ?>" role="button" data-bs-toggle="collapse" data-bs-parent="#project-faq-accordion" href="#project-faq-collapse-<?php echo esc_attr( $key ); ?>" aria-expanded="true" aria-controls="project-faq-collapse-<?php echo esc_attr( $key ); ?>">
                                    <?php echo esc_html($item['question']); ?>
                                </a>
                            </h4>
                            <div id="project-faq-collapse-<?php echo esc_attr( $key ); ?>" class="accordion-collapse collapse <?php echo esc_attr($i == 0 ? 'show' : ''); ?>" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $key ); ?>">
                                <div class="accordion-body">
                                    <div class="description"><?php echo trim($item['answer']); ?></div>
                                </div>
                            </div>

                        </div>
                    <?php $i++; } ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
}