<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);

$query_args = array(
	'post_type' => 'freelancer',
    'post_status' => 'publish',
    'post_per_page' => -1,
    'fields' => 'ids'
);
$params = true;
if ( WP_Freeio_Freelancer_Filter::has_filter() ) {
	$params = $_GET;
}
$loop = WP_Freeio_Query::get_posts($query_args, $params);

echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';
?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
     <?php do_action('rss2_ns'); ?>>
    <channel>
        <title><?php bloginfo_rss('name'); ?> - Feed</title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <language><?php echo get_option('rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters('rss_update_period', 'hourly'); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters('rss_update_frequency', '1'); ?></sy:updateFrequency>
        <?php do_action('rss2_head'); ?>
        <?php
        if ( !empty($loop->posts) ) {
        	foreach ($loop->posts as $freelancer_id) {
        		
                $expiry_date = get_post_meta($freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'expiry_date', true);
                if ($expiry_date != '') {
                    $expiry_date = mysql2date('D, d M Y H:i:s +0000', $expiry_date, false);
                }
                $title = apply_filters( 'the_title_rss', get_the_title($freelancer_id) );
                $link = esc_url( apply_filters( 'the_permalink_rss', get_permalink($freelancer_id) ) );

                $post_content = get_post_field('post_content', $freelancer_id);
                $post_content = apply_filters('the_content', $post_content);
                
                $post_thumbnail_id = get_post_thumbnail_id($freelancer_id);
                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
	                

                $address = get_post_meta($freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'address', true);
                
                
                $salary = WP_Freeio_Freelancer::get_salary_html($freelancer_id, false);
                
                $categories = wp_get_post_terms($freelancer_id, 'freelancer_category');
                $category = isset($categories[0]->name) ? $categories[0]->name : '';
                ?>
                <item>
                    <title><?php echo $title; ?></title>
                    <link><?php echo $link; ?></link>
                    <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                        
                    <expiryDate><?php echo $expiry_date; ?></expiryDate>
                    <salary><![CDATA[<?php echo $salary; ?>]]></salary>
                        
                    <logo><![CDATA[<?php echo $post_thumbnail_src; ?>]]></logo>
                    
                    <location><![CDATA[<?php echo $address; ?>]]></location>
                    <category><![CDATA[<?php echo $category; ?>]]></category>
                        
                    <excerpt><![CDATA[<?php the_excerpt_rss(); ?>]]></excerpt>
                    <description><![CDATA[<?php echo $post_content; ?>]]></description>
                    <?php rss_enclosure(); ?>
                    <?php do_action('rss2_item'); ?>
                </item>
                <?php
            }
        }
        ?>
    </channel>
</rss>