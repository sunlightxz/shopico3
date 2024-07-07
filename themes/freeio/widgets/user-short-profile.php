<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
extract( $args );

if ( !is_user_logged_in() || !class_exists('WP_Freeio_User') ) {
    return;
}

extract( $args );
extract( $instance );

echo trim($before_widget);
$title = apply_filters('widget_title', $instance['title']);
if ( $title ) {
    echo trim($before_title)  . trim( $title ) . $after_title;
}
$user_id = get_current_user_id();
if ( WP_Freeio_User::is_employer($user_id) ) {
    $employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
    
    $title = get_the_title($employer_id);
    $post = get_post($employer_id);
    ob_start();
    freeio_employer_display_logo($post);
    $logo = ob_get_clean();

    $location = freeio_employer_display_short_location($post, 'no-title', false);

    if ($nav_menu_employer) {
        $term = get_term_by( 'slug', $nav_menu_employer, 'nav_menu' );
        if ( !empty($term) ) {
            $nav_menu_id = $term->term_id;
        }
    }
} elseif ( method_exists('WP_Freeio_User', 'is_employee') && WP_Freeio_User::is_employee($user_id) ) {
    $user_id = WP_Freeio_User::get_user_id();
    if ( empty($user_id) ) {
        return;
    }
    $employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
    $title = get_the_title($employer_id);
    $post = get_post($employer_id);
    ob_start();
    freeio_employer_display_logo($post);
    $logo = ob_get_clean();

    $location = freeio_employer_display_short_location($post, 'no-title', false);

    if ($nav_menu_employee) {
        $term = get_term_by( 'slug', $nav_menu_employee, 'nav_menu' );
        if ( !empty($term) ) {
            $nav_menu_id = $term->term_id;
        }
    }
} elseif ( WP_Freeio_User::is_freelancer($user_id) ) {
    $freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
    $title = get_the_title($freelancer_id);
    $post = get_post($freelancer_id);
    ob_start();
    freeio_freelancer_display_logo($post);
    $logo = ob_get_clean();

    $location = freeio_freelancer_display_short_location($post, 'no-title', false);

    if ($nav_menu_freelancer) {
        $term = get_term_by( 'slug', $nav_menu_freelancer, 'nav_menu' );
        if ( !empty($term) ) {
            $nav_menu_id = $term->term_id;
        }
    }
} else {
    return;
}
?>

<div class="user-short-profile-top <?php echo esc_attr( (WP_Freeio_User::is_freelancer($user_id))? 'is_freelancer': ''); ?>">
    <div class="d-flex align-items-center">
        <?php
            if ( !empty($logo) ) {
                ?>
                <div class="user-logo flex-shrink-0"><?php echo trim($logo); ?></div>
                <?php
            }
        ?>
        <div class="inner flex-grow-1">
            <?php if ( $title ) { ?>
                <h3 class="title">
                    <a href="<?php echo esc_url(get_permalink($post)); ?>">
                        <?php echo trim($title); ?>
                    </a>
                </h3>
            <?php } ?>
            <?php if ( WP_Freeio_User::is_freelancer($user_id) ) {
                $total_balance = WP_Freeio_Post_Type_Withdraw::get_freelancer_balance($user_id);
                $current_balance = isset($total_balance['current_balance']) ? $total_balance['current_balance'] : 0;
                ?>
                <div class="balance-available text-success">
                    <?php echo WP_Freeio_Price::format_price($current_balance, true);?>
                </div>
                <?php
            }
            ?>
            <?php if ( $post->post_status == 'publish' ) { ?>
                <div class="clearfix">
                    <a class="view-profile" href="<?php echo esc_url(get_permalink($post)); ?>"><?php esc_html_e('View Profile', 'freeio'); ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php if ( !empty($nav_menu_id) ) { ?>
    <div class="user_short_profile">
        <?php
            $args = array(
                'menu'        => $nav_menu_id,
                'container_class' => 'navbar-collapse no-padding',
                'menu_class' => 'menu_short_profile',
                'fallback_cb' => '',
                'walker' => new Freeio_Nav_Menu()
            );
            wp_nav_menu($args);
        ?>
    </div>
<?php } ?>

<?php echo trim($after_widget);