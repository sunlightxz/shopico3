<?php
/**
 * 
 * Theme Name: Freeio
*Theme URI: https://themeforest.net/item/freeio-freelance-marketplace-wordpress-theme/42045416
*Author: ApusTheme
*Author URI: https://themeforest.net/user/apustheme
*Description: Freeio is a Freelance Marketplace WordPress theme with some exciting features and excellent code quality.
*Version: 1.3.3
*License: GNU General Public License v2 or later
*License URI: http://www.gnu.org/licenses/gpl-2.0.html
*Tags: custom-background, custom-colors, custom-header, custom-menu, editor-style, featured-images, microformats, post-formats, rtl-language-support, sticky-post, threaded-comments, translation-ready
*Text Domain: freeio
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package WordPress
 * @subpackage Freeio
 * @since Freeio 1.0
 */

get_header();
?>
	<div id="primary" class="content-area content-index">
		<main id="main" class="site-main" role="main">
			<div class="container">
			<div class="container-inner main-content">
				<div class="row"> 
	                <!-- MAIN CONTENT -->
	                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
	                        <?php  if ( have_posts() ) : 
	                        	while ( have_posts() ) : the_post();
									?>
										<div class="layout-blog">
											<?php get_template_part( 'template-posts/loop/inner-grid' ); ?>
										</div>
									<?php
								// End the loop.
								endwhile;
								freeio_paging_nav();
								?>
	                        <?php else : ?>
	                            <?php get_template_part( 'template-posts/content', 'none' ); ?>
	                        <?php endif; ?>
	                </div>
	                <div class="col-sm-3 col-xs-12 sidebar">
	                	<?php if ( is_active_sidebar( 'sidebar-default' ) ): ?>
				   			<?php dynamic_sidebar('sidebar-default'); ?>
				   		<?php endif; ?>
	                   	
	                </div>
	            </div>
            </div>
            </div>
		</main><!-- .site-main -->
	</div><!-- .content-area -->
<?php get_footer(); ?>