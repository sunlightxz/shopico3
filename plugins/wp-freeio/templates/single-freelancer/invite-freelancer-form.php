<?php

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}


?>

<div id="invite-freelancer-form-wrapper-<?php echo esc_attr($freelancer_id); ?>" class="invite-freelancer-form-wrapper mfp-hide">
	<div class="inner">
		<div class="title-wrapper flex-middle">
			<h2 class="widget-title">
				<span><?php echo __('Invite to apply project', 'wp-freeio'); ?></span>
			</h2>
			<a href="javascript:void(0);" class="close-magnific-popup ali-right"><i class="ti-close"></i></a>
		</div>
		<div class="widget-content">
			<div class="des">
				<?php esc_html_e('Select project to invite this user', 'wp-freeio'); ?>
			</div>
			<div class="jobs">
				<form id="invite-freelancer-form-<?php echo esc_attr($freelancer_id); ?>" class="invite-freelancer-form" method="post" action="post">
					<?php
						$user_id = WP_Freeio_User::get_user_id();
						$query_vars = array(
							'post_type'     => 'project',
							'post_status'   => 'publish',
							'posts_per_page' => -1,
							'author'        => $user_id,
							'orderby'		=> 'date',
							'order'			=> 'DESC',
							'fields'		=> 'ids',
						);

						$projects = new WP_Query($query_vars);
						if ( !empty($projects->posts) ) {
							?>
							<div class="form-group">
								<ul class="checklist">
								<?php
								foreach ($projects->posts as $project_id) { ?>
									<li>
										<label>
											<input type="checkbox" name="project_ids[]" value="<?php echo esc_attr($project_id); ?>">
											<?php echo get_the_title($project_id); ?>
										</label>
									</li>
									<?php
								}
								?>
								</ul>
							</div>
							<?php
						}
					?>

					<input type="hidden" name="freelancer_id" value="<?php echo esc_attr($freelancer_id); ?>">

					<button class="btn btn-theme" name="invite-freelancer"><?php echo esc_html__( 'Invite', 'wp-freeio' ); ?></button>
				</form>
			</div>
		</div>
	</div>
</div>
