<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( $user_packages ) : ?>
	<div class="widget widget-your-packages">
		<h2 class="widget-title"><?php esc_html_e( 'Your Packages', 'wp-freeio-wc-paid-listings' ); ?></h2>
		<ul class="user-project-packaged">
			<?php
				$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
				$checked = 1;
			foreach ( $user_packages as $key => $package ) :
				$package_count = get_post_meta($package->ID, $prefix.'package_count', true);
				$project_limit = get_post_meta($package->ID, $prefix.'project_limit', true);
				$project_duration = get_post_meta($package->ID, $prefix.'project_duration', true);
				$feature_projects = get_post_meta($package->ID, $prefix.'feature_projects', true);
			?>

				<li>
						<input type="radio" <?php checked( $checked, 1 ); ?> name="wpfiwpl_project_user_package" value="<?php echo esc_attr($package->ID); ?>" id="user-package-<?php echo esc_attr($package->ID); ?>" />
						<label for="user-package-<?php echo esc_attr($package->ID); ?>"><?php echo trim($package->post_title); ?></label><br/>

						<?php
							if ( $project_limit ) {
								printf( _n( '%s project posted out of %d', '%s projects posted out of %d', $package_count, 'wp-freeio-wc-paid-listings' ), $package_count, $project_limit );
							} else {
								printf( _n( '%s project posted', '%s projects posted', $package_count, 'wp-freeio-wc-paid-listings' ), $package_count );
							}

							if ( $project_duration ) {
								printf(  ', ' . _n( 'listed for %s day', 'listed for %s days', $project_duration, 'wp-freeio-wc-paid-listings' ), $project_duration );
							}
							echo sprintf(__( ', featured project: %s', 'wp-freeio-wc-paid-listings' ), $feature_projects ? __( 'Yes', 'wp-freeio-wc-paid-listings' ) : __( 'No', 'wp-freeio-wc-paid-listings' )  );
							$checked = 0;
						?>
					</li>

			<?php endforeach; ?>
		</ul>

		<button class="btn btn-theme" type="submit">
			<?php esc_html_e('Continue using quota', 'wp-freeio-wc-paid-listings') ?>
		</button>
	</div>
<?php endif; ?>