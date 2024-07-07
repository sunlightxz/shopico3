<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Team extends Widget_Base {

    public function get_name() {
        return 'apus_element_team';
    }

    public function get_title() {
        return esc_html__( 'Apus Teams', 'freeio' );
    }

    public function get_icon() {
        return 'fa fa-users';
    }

    public function get_categories() {
        return [ 'freeio-elements' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Team', 'freeio' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'icon',
            [
                'label' => esc_html__( 'Social Icon', 'freeio' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'social',
                'label_block' => true,
                'default' => [
                    'value' => 'fab fa-facebook-f',
                    'library' => 'fa-brands',
                ],
                'recommended' => [
                    'fa-brands' => [
                        'android',
                        'apple',
                        'behance',
                        'bitbucket',
                        'codepen',
                        'delicious',
                        'deviantart',
                        'digg',
                        'dribbble',
                        'elementor',
                        'facebook',
                        'flickr',
                        'foursquare',
                        'free-code-camp',
                        'github',
                        'gitlab',
                        'globe',
                        'google-plus',
                        'houzz',
                        'instagram',
                        'jsfiddle',
                        'linkedin',
                        'medium',
                        'meetup',
                        'mixcloud',
                        'odnoklassniki',
                        'pinterest',
                        'product-hunt',
                        'reddit',
                        'shopping-cart',
                        'skype',
                        'slideshare',
                        'snapchat',
                        'soundcloud',
                        'spotify',
                        'stack-overflow',
                        'steam',
                        'stumbleupon',
                        'telegram',
                        'thumb-tack',
                        'tripadvisor',
                        'tumblr',
                        'twitch',
                        'twitter',
                        'viber',
                        'vimeo',
                        'vk',
                        'weibo',
                        'weixin',
                        'whatsapp',
                        'wordpress',
                        'xing',
                        'yelp',
                        'youtube',
                        '500px',
                    ],
                    'fa-solid' => [
                        'envelope',
                        'link',
                        'rss',
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => esc_html__( 'Link Social', 'freeio' ),
                'type' => Controls_Manager::URL,
                'label_block' => true,
                'default' => [
                    'is_external' => 'true',
                ],
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => esc_html__( 'https://your-link.com', 'freeio' ),
            ]
        );

        $this->add_control(
            'name', [
                'label' => esc_html__( 'Member Name', 'freeio' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Member Name' , 'freeio' ),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'listing', [
                'label' => esc_html__( 'Member Listing', 'freeio' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Member Listing' , 'freeio' ),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'img_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Image', 'freeio' ),
                'type' => Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Image Here', 'freeio' ),
            ]
        );

        $this->add_control(
            'socials',
            [
                'label' => esc_html__( 'Socials', 'freeio' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );
        

        $this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'freeio' ),
                'type'          => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'freeio' ),
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Title', 'freeio' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Background Hover Color', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Core\Schemes\Color::get_type(),
                    'value' => Core\Schemes\Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .social a:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings();

        extract( $settings );
        $migration_allowed = Icons_Manager::is_migration_allowed();

        ?>
        <div class="widget-team <?php echo esc_attr($el_class); ?>">
            <div class="team-item">
                <div class="top-image">
                    <?php
                    if ( !empty($settings['img_src']['id']) ) {
                    ?>
                        <div class="team-image">
                            <?php echo freeio_get_attachment_thumbnail($settings['img_src']['id'], 'full'); ?>
                        </div>
                    <?php } ?>
                </div>
                <ul class="social">
                    <?php
                    foreach ( $settings['socials'] as $index => $item ) {
                        $migrated = isset( $item['__fa4_migrated']['icon'] );
                        $is_new = empty( $item['social'] ) && $migration_allowed;
                        $social = '';

                        // add old default
                        if ( empty( $item['social'] ) && ! $migration_allowed ) {
                            $item['social'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-wordpress';
                        }

                        if ( ! empty( $item['social'] ) ) {
                            $social = str_replace( 'fa fa-', '', $item['social'] );
                        }

                        if ( ( $is_new || $migrated ) && 'svg' !== $item['icon']['library'] ) {
                            $social = explode( ' ', $item['icon']['value'], 2 );
                            if ( empty( $social[1] ) ) {
                                $social = '';
                            } else {
                                $social = str_replace( 'fa-', '', $social[1] );
                            }
                        }
                        if ( 'svg' === $item['icon']['library'] ) {
                            $social = '';
                        }

                        $link_key = 'link_' . $index;

                        $this->add_render_attribute( $link_key, 'href', $item['link']['url'] );

                        if ( $item['link']['is_external'] ) {
                            $this->add_render_attribute( $link_key, 'target', '_blank' );
                        }

                        if ( $item['link']['nofollow'] ) {
                            $this->add_render_attribute( $link_key, 'rel', 'nofollow' );
                        }
                        ?>
                        <li>
                            <a <?php echo trim($this->get_render_attribute_string( $link_key )); ?>>
                                <?php
                                if ( $is_new || $migrated ) {
                                    Icons_Manager::render_icon( $item['icon'] );
                                } else { ?>
                                    <i class="<?php echo esc_attr( $item['social'] ); ?>"></i>
                                <?php } ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="content">
                <?php if ( !empty($name) ) { ?>
                    <h3 class="name-team"><?php echo esc_html($name); ?></h3>
                <?php } ?>
                <?php if ( !empty($listing) ) { ?>
                    <div class="listing"><?php echo esc_html($listing); ?></div>
                <?php } ?>
            </div>
        </div>
        <?php
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Team );
} else {
    Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Team );
}