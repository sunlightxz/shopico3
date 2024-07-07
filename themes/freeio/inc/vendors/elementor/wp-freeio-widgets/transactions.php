<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Transactions extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_transactions';
    }

	public function get_title() {
        return esc_html__( 'Apus Transactions', 'freeio' );
    }
    
	public function get_categories() {
        return [ 'freeio-elements' ];
    }

	protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

   		$this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'freeio' ),
                'type'          => Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'freeio' ),
            ]
        );

        $this->end_controls_section();

    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );
        ?>
        <h1 class="title-profile"><?php esc_html_e( 'Transactions', 'freeio' ) ; ?></h1>
        <div class="box-white-dashboard">
            <div class="inner-list">
                <?php if ($title!=''): ?>
                    <h2 class="title">
                        <?php echo esc_attr( $title ); ?>
                    </h2>
                <?php endif; ?>

                <?php if ( ! is_user_logged_in() ) {
                    ?>
                        <div class="text-warning"><?php  esc_html_e( 'Please login to see this page.', 'freeio' ); ?></div>
                    <?php
                } else {
                    if ( get_query_var( 'paged' ) ) {
                        $paged = get_query_var( 'paged' );
                    } elseif ( get_query_var( 'page' ) ) {
                        $paged = get_query_var( 'page' );
                    } else {
                        $paged = 1;
                    }
                    $args = array(
                        'post_type' => 'shop_order',
                        'posts_per_page' => get_option('posts_per_page'),
                        'paged' => $paged,
                        'post_status' => array('wc-pending', 'wc-on-hold', 'wc-cancelled', 'wc-failed', 'wc-processing', 'wc-refunded', 'wc-completed'),
                        'order' => 'DESC',
                        'orderby' => 'ID',
                        'meta_query' => array(
                            array(
                                'meta_key' => '_customer_user',
                                'value' => get_current_user_id(),
                            )
                        )
                    );
                    $trans_loop = new WP_Query($args);
                    $total_trans = $trans_loop->found_posts;

                    if ( $trans_loop->have_posts() ) {
                    ?>
                        <div class="widget m-0 <?php echo esc_attr($el_class); ?>">
                            <div class="table-responsive">
                                <table class="user-transactions">
                                    <thead>
                                        <tr>
                                            <td><?php esc_html_e('Order ID', 'freeio'); ?></td>
                                            <td><?php esc_html_e('Package', 'freeio'); ?></td>
                                            <td><?php esc_html_e('Amount', 'freeio'); ?></td>
                                            <td><?php esc_html_e('Date', 'freeio'); ?></td>
                                            <td><?php esc_html_e('Payment Mode', 'freeio'); ?></td>
                                            <td><?php esc_html_e('Status', 'freeio'); ?></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($trans_loop->have_posts()) : $trans_loop->the_post();
                                            global $post;

                                            $trans_order_name = '';
                                            $trans_order_obj = wc_get_order($post->ID);
                                            // $continue = false;
                                            // foreach ( $trans_order_obj->get_items() as $item ) {
                                            //     $oproduct = wc_get_product( $item['product_id'] );
                                            //     if ( is_object($oproduct) && $oproduct->is_type( array( 'job_package', 'service_package', 'project_package', 'cv_package', 'contact_package', 'resume_package', 'freelancer_package' ) ) ) {
                                            //         $trans_order_name = get_the_title($oproduct->get_ID());
                                            //         $continue = true;
                                            //     }
                                            // }
                                            // if ( !$continue ) {
                                            //     continue;
                                            // }
                                            $trans_order_price = $trans_order_obj->get_total();

                                            $order_price = wc_price($trans_order_price);

                                            $trans_status = $trans_order_obj->get_status();
                                            if ($trans_status == 'completed') {
                                                $status_txt = esc_html__('Successfull', 'freeio');
                                                $status_class = 'success';
                                            } else if ($trans_status == 'processing') {
                                                $status_txt = esc_html__('Processing', 'freeio');
                                                $status_class = 'pending';
                                            } else if ($trans_status == 'refunded') {
                                                $status_txt = esc_html__('Refunded', 'freeio');
                                                $status_class = 'pending';
                                            } else {
                                                $status_txt = esc_html__('Pending', 'freeio');
                                                $status_class = 'pending';
                                            }

                                            $order_date_obj = $trans_order_obj->get_date_created();
                                            $order_date_array = json_decode(json_encode($order_date_obj), true);
                                            $order_date = isset($order_date_array['date']) ? $order_date_array['date'] : '';

                                            $payment_mode = $trans_order_obj->get_payment_method();
                                            $payment_mode = $payment_mode != '' ? $payment_mode : '-';
                                            if ($payment_mode == 'cod') {
                                                $payment_mode = esc_html__('Cash on Delivery', 'freeio');
                                            }
                                        ?>
                                            <tr>
                                                <td class="id_listing"><?php the_ID(); ?></td>
                                                <td class="title"><?php the_title(); ?></td>
                                                <td><?php echo trim($order_price); ?></td>
                                                <td class="date"><?php echo trim($order_date != '' ? date_i18n(get_option('date_format'), strtotime($order_date)) : '-') ?></td>
                                                <td><?php echo trim($payment_mode) ?></td>
                                                <td><span class="action <?php echo esc_attr($status_class) ?>"><?php echo trim($status_txt); ?></span></td>
                                            </tr>
                                        <?php endwhile;
                                            wp_reset_postdata();
                                        ?>
                                    </tbody>
                                </table>

                                <?php
                                WP_Freeio_Mixes::custom_pagination( array(
                                    'max_num_pages' => $trans_loop->max_num_pages,
                                    'prev_text'     => '<i class=" ti-angle-left"></i>',
                                    'next_text'     => '<i class=" ti-angle-right"></i>',
                                    'wp_query' => $trans_loop
                                ));
                                ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="not-found"><?php esc_html_e('Don\'t have any items', 'freeio'); ?></div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    <?php }

}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Transactions );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Transactions );
}