<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( $user_packages ) : ?>
	<div class="widget widget-your-packages">
		<h2 class="widget-title"><?php esc_html_e( 'Your Packages', 'wp-freeio-wc-paid-listings' ); ?></h2>
		<ul class="user-job-packaged">
			<?php
				$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
				$checked = 1;
			foreach ( $user_packages as $key => $package ) :
				$package_count = get_post_meta($package->ID, $prefix.'package_count', true);
				$job_limit = get_post_meta($package->ID, $prefix.'job_limit', true);
				$job_duration = get_post_meta($package->ID, $prefix.'job_duration', true);
				$urgent_jobs = get_post_meta($package->ID, $prefix.'urgent_jobs', true);
				$feature_jobs = get_post_meta($package->ID, $prefix.'feature_jobs', true);
			?>
				<li>
					<input type="radio" <?php checked( $checked, 1 ); ?> name="wpfiwpl_listing_user_package" value="<?php echo esc_attr($package->ID); ?>" id="user-package-<?php echo esc_attr($package->ID); ?>" />
					<label for="user-package-<?php echo esc_attr($package->ID); ?>"><?php echo trim($package->post_title); ?></label><br/>

					<?php
						if ( $job_limit ) {
							printf( _n( '%s job posted out of %d', '%s jobs posted out of %d', $package_count, 'wp-freeio-wc-paid-listings' ), $package_count, $job_limit );
						} else {
							printf( _n( '%s job posted', '%s jobs posted', $package_count, 'wp-freeio-wc-paid-listings' ), $package_count );
						}

						if ( $job_duration ) {
							printf(  ', ' . _n( 'listed for %s day', 'listed for %s days', $job_duration, 'wp-freeio-wc-paid-listings' ), $job_duration );
						}
						echo sprintf(__( ', urgent job: %s', 'wp-freeio-wc-paid-listings' ), $urgent_jobs ? __( 'Yes', 'wp-freeio-wc-paid-listings' ) : __( 'No', 'wp-freeio-wc-paid-listings' )  );
						echo sprintf(__( ', featured job: %s', 'wp-freeio-wc-paid-listings' ), $feature_jobs ? __( 'Yes', 'wp-freeio-wc-paid-listings' ) : __( 'No', 'wp-freeio-wc-paid-listings' )  );
						$checked = 0;
					?>
				</li>
			<?php endforeach; ?>
		</ul>
		<button class="btn btn-theme" type="submit">
			<?php esc_html_e('Continue using quota', 'superio') ?>
		</button>
	</div>
<?php endif; ?>