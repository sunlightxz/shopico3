<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Freeio
 * @since Freeio 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
	<link rel="profile" href="//gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php if ( freeio_get_config('preload', true) ) {
	$preload_icon = freeio_get_config('media-preload-icon');
	$styles = array();
	if ( !empty($preload_icon) ) {
		$preload_icon_id = attachment_url_to_postid($preload_icon);
		if ( !empty($preload_icon_id) ) {
			$img = wp_get_attachment_image_src($preload_icon_id, 'full');
			if ( !empty($img[0]) ) {
				$styles[] = 'background-image: url(\''.$img[0].'\');';
			}
			if ( !empty($img[1]) ) {
				$styles[] = 'width: '.$img[1].'px;';
			}
			if ( !empty($img[1]) ) {
				$styles[] = 'height: '.$img[2].'px;';
			}
	    } else {
	    	$styles[] = 'background-image: url(\''.$preload_icon.'\');';
	    }
    }
    $style_attr = '';
    if ( !empty($styles) ) {
    	$style_attr = 'style="'.implode(' ', $styles).'"';
    }
?>
	<div class="apus-page-loading">
        <div class="apus-loader-inner" <?php echo trim($style_attr); ?>></div>
    </div>
<?php } ?>

<?php
if ( function_exists( 'wp_body_open' ) ) {
    wp_body_open();
}
?>
<div id="wrapper-container" class="wrapper-container">
    
    <?php
    if ( freeio_get_config('separate_header_mobile', true) ) {
		get_template_part( 'headers/mobile/offcanvas-menu' );
		get_template_part( 'headers/mobile/header-mobile' );
	}
	?>

	<?php
		$header = apply_filters( 'freeio_get_header_layout', freeio_get_config('header_type') );
		if ( !empty($header) ) {
			freeio_display_header_builder($header);
		} else {
			get_template_part( 'headers/default' );
		}
	?>
	<div id="apus-main-content">