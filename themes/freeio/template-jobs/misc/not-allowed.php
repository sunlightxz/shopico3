<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="box-dashboard-wrapper">
	<div class="alert alert-warning not-allow-wrapper">
		<?php
		if ( empty($need_role) ) {
			echo esc_html__( 'You are not allowed to access this page.', 'freeio' );
		} else {
			switch ($need_role) {
				case 'employer':
					$need_role = esc_html__( 'employer', 'freeio' );
					break;
				default:
					$need_role = esc_html__( 'freelancer', 'freeio' );
					break;
			}
			echo sprintf(esc_html__( 'You need to login with %s account to access this page.', 'freeio' ), $need_role);
		}

		?>
	</div><!-- /.alert -->
</div>